<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Session based authentication against the `solutions` database.
 */
final class Auth
{
    private static ?array $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = (new User())->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        self::login($user);

        return true;
    }

    public static function login(array $user): void
    {
        Session::set('auth_user_id', (int) $user['id']);
        Session::regenerate();
        self::$user = $user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $id = Session::get('auth_user_id');

        if ($id === null) {
            return null;
        }

        self::$user = (new User())->find((int) $id);

        return self::$user;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user === null ? null : (int) $user['id'];
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::forget('auth_user_id');
        Session::destroy();
    }
}
