<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->firstBy('email', $email);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->firstBy('username', $username);
    }

    public function register(array $data): int
    {
        return $this->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'member',
        ]);
    }
}
