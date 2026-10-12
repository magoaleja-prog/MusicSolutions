<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\DevResource;
use App\Models\Score;
use App\Models\Song;
use App\Support\MusicSearch;

final class SearchController extends Controller
{
    public function index(): Response
    {
        $term = (string) request()->input('q', '');

        return $this->view('search/index', $this->results($term) + ['term' => $term]);
    }

    /**
     * JSON endpoint used by the live search on the front-end.
     */
    public function api(): Response
    {
        $term = (string) request()->input('q', '');

        return $this->json($this->results($term));
    }

    /**
     * @return array{local:array, external:array, scores:array, resources:array}
     */
    private function results(string $term): array
    {
        $term = trim($term);

        if ($term === '') {
            return [
                'local' => (new Song())->latest(8),
                'external' => [],
                'scores' => [],
                'resources' => [],
            ];
        }

        return [
            'local' => (new Song())->search($term),
            'external' => MusicSearch::itunes($term),
            'scores' => (new Score())->search($term, 8),
            'resources' => (new DevResource())->search($term, 8),
        ];
    }
}
