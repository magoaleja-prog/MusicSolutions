<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Small active-record style base model on top of PDO.
 */
abstract class Model
{
    protected string $table = '';

    protected string $primaryKey = 'id';

    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function query(string $sql, array $params = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function all(string $orderBy = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy !== null) {
            $sql .= " ORDER BY {$orderBy}";
        }

        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        return $this->firstBy($this->primaryKey, $id);
    }

    public function firstBy(string $column, mixed $value): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1");
        $statement->execute(['value' => $value]);

        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public function where(string $column, mixed $value, string $orderBy = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        if ($orderBy !== null) {
            $sql .= " ORDER BY {$orderBy}";
        }

        $statement = $this->db->prepare($sql);
        $statement->execute(['value' => $value]);

        return $statement->fetchAll();
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->db->prepare($sql)->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $assignments = array_map(
            static fn (string $column): string => "{$column} = :{$column}",
            array_keys($data)
        );

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__id',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey
        );

        $data['__id'] = $id;

        return $this->db->prepare($sql)->execute($data);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");

        return $statement->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
