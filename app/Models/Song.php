<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Song extends Model
{
    protected string $table = 'songs';

    public function search(string $term, int $limit = 12): array
    {
        $limit = max(1, min(50, $limit));
        $like = '%' . $term . '%';

        return $this->query(
            'SELECT * FROM songs
             WHERE title LIKE :t1 OR artist LIKE :t2 OR album LIKE :t3 OR genre LIKE :t4
             ORDER BY title ASC LIMIT ' . $limit,
            ['t1' => $like, 't2' => $like, 't3' => $like, 't4' => $like]
        );
    }

    public function latest(int $limit = 8): array
    {
        return $this->query('SELECT * FROM songs ORDER BY created_at DESC LIMIT ' . max(1, min(50, $limit)));
    }
}
