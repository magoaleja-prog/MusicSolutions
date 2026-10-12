<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Env;
use App\Core\Session;

if (!function_exists('base_path')) {
    /**
     * Absolute path from the project root.
     */
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * Read configuration using dot notation, e.g. config('database.host').
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $items = null;

        if ($items === null) {
            $items = [];
            foreach (glob(base_path('config/*.php')) ?: [] as $file) {
                $items[basename($file, '.php')] = require $file;
            }
        }

        if ($key === null) {
            return $items;
        }

        $value = $items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('e')) {
    /**
     * Escape output for HTML context.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/')) . '?v=' . config('app.version', '1.0.0');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');

        if ($base === '') {
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        }

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path): string
    {
        return url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('request')) {
    function request(): \App\Core\Request
    {
        return new \App\Core\Request();
    }
}

if (!function_exists('user')) {
    function user(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed
    {
        if (func_num_args() === 1) {
            return Session::pull($key);
        }

        Session::flash($key, $value);

        return null;
    }
}

if (!function_exists('old')) {
    /**
     * Previous form input, remembered once per request.
     */
    function old(string $key, mixed $default = ''): mixed
    {
        static $old = null;

        if ($old === null) {
            $old = Session::pull('_old', []);
            $old = is_array($old) ? $old : [];
        }

        return array_key_exists($key, $old) ? $old[$key] : $default;
    }
}

if (!function_exists('flash_errors')) {
    function flash_errors(array $errors): void
    {
        Session::flash('errors', $errors);
    }
}

if (!function_exists('errors')) {
    /**
     * Validation errors remembered once per request.
     */
    function errors(): array
    {
        static $errors = null;

        if ($errors === null) {
            $errors = Session::pull('errors', []);
            $errors = is_array($errors) ? $errors : [];
        }

        return $errors;
    }
}

if (!function_exists('active')) {
    /**
     * Returns "is-active" when the current path matches, for nav highlighting.
     */
    function active(string $path): string
    {
        $current = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');

        return $current === $path ? 'is-active' : '';
    }
}
