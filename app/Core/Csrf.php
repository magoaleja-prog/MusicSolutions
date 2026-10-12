<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Cross Site Request Forgery protection.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::SESSION_KEY)) {
            Session::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
        }

        return (string) Session::get(self::SESSION_KEY);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function validate(?string $token): bool
    {
        $expected = (string) Session::get(self::SESSION_KEY, '');

        return $expected !== '' && is_string($token) && hash_equals($expected, $token);
    }

    /**
     * Aborts the request when the submitted token is invalid.
     */
    public static function verify(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!self::validate(is_string($token) ? $token : null)) {
            Response::abort(419, 'El formulario expiro o el token de seguridad no es valido.')->send();
            exit;
        }
    }
}
