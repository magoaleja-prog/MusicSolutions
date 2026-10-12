<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Tiny HTTP request wrapper.
 */
final class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        return $this->normalize($path);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->isJson()) {
            $json = $this->json();
            $value = $json[$key] ?? $default;

            return is_string($value) ? trim($value) : $value;
        }

        $value = $_POST[$key] ?? $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $value;
    }

    public function all(): array
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return array_merge($_GET, $_POST);
    }

    public function has(string $key): bool
    {
        if ($this->isJson()) {
            return array_key_exists($key, $this->json());
        }

        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Cuerpo de la peticion si viene en JSON (p.ej. para la API de lint).
     */
    public function json(): array
    {
        static $payload = null;

        if ($payload === null) {
            $raw = (string) file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        return $payload;
    }

    private function isJson(): bool
    {
        $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');

        return str_starts_with($contentType, 'application/json');
    }

    public function file(string $key): ?array
    {
        if (!isset($_FILES[$key]) || ($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $_FILES[$key];
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function normalize(string $path): string
    {
        $path = '/' . ltrim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
