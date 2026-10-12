<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

/**
 * Safe file upload handling with extension whitelisting and random names.
 */
final class Uploader
{
    /**
     * @param array<int, string> $allowedExtensions
     * @return array{path:string, original_name:string, extension:string, mime:string, size:int}
     */
    public static function store(array $file, string $directory, array $allowedExtensions): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('La subida del archivo fallo. Intenta de nuevo.');
        }

        $maxSize = (int) config('app.upload_max_size', 10 * 1024 * 1024);

        if ((int) $file['size'] > $maxSize) {
            throw new RuntimeException('El archivo supera el tamano maximo permitido (' . self::humanSize($maxSize) . ').');
        }

        $originalName = (string) ($file['name'] ?? 'archivo');
        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException('Tipo de archivo no permitido. Formatos validos: ' . implode(', ', $allowedExtensions) . '.');
        }

        $relativePath = trim($directory, '/') . '/' . date('Y/m');

        $finalDirectory = base_path('public/uploads/' . $relativePath);

        if (!is_dir($finalDirectory) && !mkdir($finalDirectory, 0775, true) && !is_dir($finalDirectory)) {
            throw new RuntimeException('No se pudo crear el directorio de subidas.');
        }

        $filename = bin2hex(random_bytes(10)) . '.' . $extension;
        $destination = $finalDirectory . '/' . $filename;

        if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('No se pudo guardar el archivo en el servidor.');
        }

        $mime = self::mime($destination, (string) ($file['type'] ?? 'application/octet-stream'));

        return [
            'path' => $relativePath . '/' . $filename,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime' => $mime,
            'size' => (int) $file['size'],
        ];
    }

    private static function mime(string $path, string $fallback): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detected = finfo_file($finfo, $path);
                finfo_close($finfo);
                if (is_string($detected) && $detected !== '') {
                    return $detected;
                }
            }
        }

        return $fallback;
    }

    private static function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, 1) . ' ' . $units[$index];
    }
}
