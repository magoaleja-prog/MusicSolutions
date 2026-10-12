<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

/**
 * Revisa la sintaxis de fragmentos de codigo usando binarios locales
 * (php, node, python). El codigo se escribe en un fichero temporal y solo se
 * parsea/compila (php -l, node --check, python -m py_compile): NUNCA se
 * ejecuta, por lo que es seguro aunque se pegue codigo arbitrario.
 */
final class Linter
{
    private const MAX_CODE_BYTES = 64 * 1024; // 64 KB

    /** @var array<string, array{command:string, args:list<string>, ext:string}> */
    private const LANGUAGES = [
        'php' => ['command' => 'php', 'args' => ['-l'], 'ext' => '.php'],
        'javascript' => ['command' => 'node', 'args' => ['--check'], 'ext' => '.js'],
        'python' => ['command' => 'python', 'args' => ['-m', 'py_compile'], 'ext' => '.py'],
        'html' => ['command' => '', 'args' => [], 'ext' => '.html'],
    ];

    /** @var array<string, bool> */
    private static array $availableCache = [];

    public static function supportedLanguages(): array
    {
        return array_keys(self::LANGUAGES);
    }

    /**
     * @param  string $language (php, javascript, python, html)
     * @return array{available:bool, ok:bool, errors:list<array{line:int, message:string}>}
     */
    public static function lint(string $language, string $code): array
    {
        $language = strtolower(trim($language));
        $definition = self::LANGUAGES[$language] ?? null;

        $result = [
            'language' => $language,
            'available' => false,
            'ok' => false,
            'errors' => [],
        ];

        if ($definition === null) {
            $result['errors'] = [['line' => 0, 'message' => 'Lenguaje no soportado.']];
            return $result;
        }

        // HTML/CSS no se lintan en el servidor: se previsualizan en el navegador.
        if ($definition['command'] === '') {
            $result['available'] = true;
            $result['ok'] = true;
            return $result;
        }

        $binary = self::resolveBinary($definition['command']);

        if ($binary === null) {
            $result['errors'] = [['line' => 0, 'message' => 'El ejecutable ' . $definition['command'] . ' no esta disponible en este equipo.']];
            return $result;
        }

        $tmpFile = self::writeTemporaryFile($code, $definition['ext']);

        try {
            [$exitCode, $output] = self::run($binary, $definition['args'], $tmpFile);

            $result['available'] = true;
            $result['ok'] = $exitCode === 0;

            if ($exitCode !== 0 && $output !== '') {
                $result['errors'] = self::parseErrors($language, $output);
                if ($result['errors'] === []) {
                    $result['errors'] = [['line' => 0, 'message' => trim(self::stripPaths($output))]];
                }
            }
        } finally {
            @unlink($tmpFile);
        }

        return $result;
    }

    private static function writeTemporaryFile(string $code, string $extension): string
    {
        $directory = base_path('storage/tmp');

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $bytes = strlen($code);
        if ($bytes > self::MAX_CODE_BYTES) {
            throw new RuntimeException('El codigo supera el limite de ' . (self::MAX_CODE_BYTES / 1024) . ' KB.');
        }

        $tmpFile = $directory . DIRECTORY_SEPARATOR . 'lint_' . bin2hex(random_bytes(8)) . $extension;

        // En Windows (OneDrive incluido) is_writable() puede dar negativo sobre
        // directorios aunque luego la escritura funcione: se valida al escribir.
        if (@file_put_contents($tmpFile, $code) === false) {
            throw new RuntimeException('No se pudo escribir el fichero temporal en storage/tmp.');
        }

        return $tmpFile;
    }

    /**
     * @param  list<string> $args
     * @return array{0:int, 1:string} [codigo de salida, salida combinada]
     */
    private static function run(string $binary, array $args, string $file): array
    {
        $command = array_merge([$binary], $args, [$file]);
        $pipes = [];
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, base_path(), null, ['bypass_shell' => true]);

        if (!is_resource($process)) {
            throw new RuntimeException('No se pudo lanzar el proceso de revision.');
        }

        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [$exitCode, trim($stdout . "\n" . $stderr)];
    }

    /**
     * @return list<array{line:int, message:string}>
     */
    private static function parseErrors(string $language, string $output): array
    {
        $clean = self::stripPaths($output);
        $lines = preg_split('/\r?\n/', $clean) ?: [];
        $errors = [];

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);

            // Ruido de los linters: caretas, stack traces y lineas vacias
            if ($line === '') {
                continue;
            }
            if ($line === '^' || str_starts_with($line, 'at ') || str_starts_with($line, 'Node.js v')
                || $line === 'Traceback (most recent call last):') {
                continue;
            }

            // Trozos del codigo copiados en la salida (empiezan con espacios)
            if (preg_match('/^\s/', $rawLine)) {
                continue;
            }

            $lineNumber = self::extractLineNumber($language, $line);

            $errors[] = ['line' => $lineNumber, 'message' => $line];
        }

        // Reduce cada lenguaje a su error mas descriptivo cuando hay duplicados
        if ($language === 'javascript') {
            $syntax = null;
            foreach ($errors as $error) {
                if (str_contains($error['message'], 'SyntaxError')) {
                    $syntax = $error;
                    break;
                }
            }
            if ($syntax !== null) {
                $line = 0;
                foreach ($errors as $error) {
                    if ($error['line'] > 0) {
                        $line = $error['line'];
                        break;
                    }
                }
                return [['line' => $line, 'message' => $syntax['message']]];
            }
        }

        if ($language === 'python') {
            foreach ($errors as $error) {
                if (preg_match('/(SyntaxError|IndentationError|TabError|NameError):/', $error['message'])) {
                    $line = 0;
                    foreach ($errors as $e) {
                        if ($e['line'] > 0) {
                            $line = $e['line'];
                            break;
                        }
                    }
                    return [['line' => $line, 'message' => $error['message']]];
                }
            }
        }

        if ($language === 'php') {
            foreach ($errors as $error) {
                if (str_contains($error['message'], 'Parse error') || str_contains($error['message'], 'Fatal error')) {
                    $line = 0;
                    foreach ($errors as $e) {
                        if ($e['line'] > 0) {
                            $line = $e['line'];
                            break;
                        }
                    }
                    return [['line' => $line, 'message' => $error['message']]];
                }
            }
        }

        return array_slice(array_values($errors), 0, 8);
    }

    private static function extractLineNumber(string $language, string $line): int
    {
        switch ($language) {
            case 'php':
                if (preg_match('/on line (\d+)/i', $line, $match)) {
                    return (int) $match[1];
                }
                break;

            case 'javascript':
                if (preg_match('/:(\d+)$/', $line, $match)) {
                    return (int) $match[1];
                }
                break;

            case 'python':
                if (preg_match('/line (\d+)/i', $line, $match)) {
                    return (int) $match[1];
                }
                break;
        }

        return 0;
    }

    private static function stripPaths(string $output): string
    {
        $dir = base_path('storage/tmp');
        $output = str_ireplace([str_replace('/', '\\', $dir), str_replace('\\', '/', $dir)], '<archivo>', $output);

        return (string) preg_replace(
            '~[A-Za-z]:[\\\\/][^:\r\n]*?storage[\\\\/]tmp[\\\\/][\w.\-]+~i',
            '<archivo>',
            $output
        );
    }

    private static function resolveBinary(string $command): ?string
    {
        if (isset(self::$availableCache[$command])) {
            return self::$availableCache[$command];
        }

        $candidates = [];

        if ($command === 'php') {
            $candidates[] = 'C:\\xampp\\php\\php.exe';
        } elseif ($command === 'node') {
            $candidates[] = 'C:\\Program Files\\nodejs\\node.exe';
        } elseif ($command === 'python') {
            foreach (glob('C:\\Program Files\\Python*\\python.exe') ?: [] as $path) {
                $candidates[] = $path;
            }
            $local = (string) getenv('LOCALAPPDATA');
            if ($local !== '') {
                foreach (glob($local . '\\Programs\\Python\\Python*\\python.exe') ?: [] as $path) {
                    $candidates[] = $path;
                }
            }
        }

        $candidates[] = $command; // busca en el PATH del sistema

        foreach ($candidates as $candidate) {
            if ($candidate === '') {
                continue;
            }
            if (strpbrk($candidate, '/\\') !== false) {
                if (is_file($candidate)) {
                    self::$availableCache[$command] = $candidate;
                    return $candidate;
                }
                continue;
            }
            $resolved = self::findInPath($candidate);
            if ($resolved !== null) {
                self::$availableCache[$command] = $resolved;
                return $resolved;
            }
        }

        self::$availableCache[$command] = null;

        return null;
    }

    private static function findInPath(string $command): ?string
    {
        $paths = explode(PATH_SEPARATOR, (string) getenv('PATH'));

        foreach ($paths as $path) {
            $path = trim($path);

            if ($path === '') {
                continue;
            }

            $candidates = [
                rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $command,
                rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $command . '.exe',
            ];

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        return null;
    }
}