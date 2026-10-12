<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP response value object.
 */
final class Response
{
    private function __construct(
        private string $content = '',
        private int $status = 200,
        private array $headers = [],
        private ?string $file = null
    ) {
    }

    public static function make(string $content, int $status = 200, array $headers = []): self
    {
        return new self($content, $status, $headers);
    }

    /**
     * Streams a file from disk as the response body.
     */
    public static function download(string $path, string $filename, string $mime = 'application/octet-stream'): self
    {
        return new self('', 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Content-Length' => (string) filesize($path),
        ], $path);
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $status,
            ['Content-Type' => 'application/json; charset=utf-8']
        );
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    public static function abort(int $status, string $message = ''): self
    {
        $view = match ($status) {
            404 => 'errors/404',
            419 => 'errors/419',
            default => 'errors/500',
        };

        $content = View::render($view, [
            'status' => $status,
            'message' => $message,
        ], null);

        return new self($content, $status);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        if ($this->file !== null && is_file($this->file)) {
            readfile($this->file);

            return;
        }

        echo $this->content;
    }
}
