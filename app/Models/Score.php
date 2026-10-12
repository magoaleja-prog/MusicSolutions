<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Score extends Model
{
    protected string $table = 'scores';

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId, 'created_at DESC');
    }

    /**
     * Public board of sheet music with the author name attached.
     */
    public function withAuthor(int $limit = 50, ?int $userId = null): array
    {
        $sql = 'SELECT s.*, u.name AS author_name, u.username AS author_username
                FROM scores s
                LEFT JOIN users u ON u.id = s.user_id';

        $params = [];

        if ($userId !== null) {
            $sql .= ' WHERE s.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $sql .= ' ORDER BY s.created_at DESC LIMIT ' . max(1, min(200, $limit));

        return $this->query($sql, $params);
    }

    public function findWithAuthor(int $id): ?array
    {
        $rows = $this->query(
            'SELECT s.*, u.name AS author_name, u.username AS author_username
             FROM scores s
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.id = :id LIMIT 1',
            ['id' => $id]
        );

        return $rows[0] ?? null;
    }

    public function search(string $term, int $limit = 12): array
    {
        $limit = max(1, min(50, $limit));
        $like = '%' . $term . '%';

        return $this->query(
            'SELECT * FROM scores
             WHERE title LIKE :t1 OR composer LIKE :t2 OR instrument LIKE :t3
             ORDER BY created_at DESC LIMIT ' . $limit,
            ['t1' => $like, 't2' => $like, 't3' => $like]
        );
    }

    public function countByUser(int $userId): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM scores WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }
}
