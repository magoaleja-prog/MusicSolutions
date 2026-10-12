<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class DevResource extends Model
{
    protected string $table = 'dev_resources';

    public function withAuthor(int $limit = 50, ?string $type = null): array
    {
        $sql = 'SELECT r.*, u.name AS author_name, u.username AS author_username
                FROM dev_resources r
                LEFT JOIN users u ON u.id = r.user_id';

        $params = [];

        if ($type !== null && $type !== '') {
            $sql .= ' WHERE r.type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY r.created_at DESC LIMIT ' . max(1, min(200, $limit));

        return $this->query($sql, $params);
    }

    public function findWithAuthor(int $id): ?array
    {
        $rows = $this->query(
            'SELECT r.*, u.name AS author_name, u.username AS author_username
             FROM dev_resources r
             LEFT JOIN users u ON u.id = r.user_id
             WHERE r.id = :id LIMIT 1',
            ['id' => $id]
        );

        return $rows[0] ?? null;
    }

    public function search(string $term, int $limit = 12): array
    {
        $limit = max(1, min(50, $limit));
        $like = '%' . $term . '%';

        return $this->query(
            'SELECT * FROM dev_resources
             WHERE title LIKE :t1 OR description LIKE :t2 OR language LIKE :t3
             ORDER BY created_at DESC LIMIT ' . $limit,
            ['t1' => $like, 't2' => $like, 't3' => $like]
        );
    }

    public function countByUser(int $userId): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM dev_resources WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }
}
