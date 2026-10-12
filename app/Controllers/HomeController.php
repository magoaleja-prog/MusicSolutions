<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Models\DevResource;
use App\Models\Score;
use App\Models\Song;
use Throwable;

final class HomeController extends Controller
{
    public function index(): Response
    {
        $stats = ['scores' => 0, 'resources' => 0, 'songs' => 0];
        $latestSongs = [];

        try {
            $stats['scores'] = (new Score())->count();
            $stats['resources'] = (new DevResource())->count();
            $stats['songs'] = (new Song())->count();
            $latestSongs = (new Song())->latest(6);
        } catch (Throwable) {
            // La base de datos puede no estar migrada todavia; mostramos la landing igualmente.
        }

        return $this->view('home/index', ['stats' => $stats, 'latestSongs' => $latestSongs]);
    }

    public function dashboard(): Response
    {
        $userId = (int) Auth::id();

        $scores = (new Score())->forUser($userId);
        $resources = (new DevResource())->withAuthor(6, null);

        return $this->view('home/dashboard', [
            'scores' => array_slice($scores, 0, 5),
            'scoreCount' => count($scores),
            'resourceCount' => (new DevResource())->countByUser($userId),
            'latestResources' => $resources,
            'latestSongs' => (new Song())->latest(6),
        ]);
    }
}
