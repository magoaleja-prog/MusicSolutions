<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DeveloperController;
use App\Controllers\HomeController;
use App\Controllers\ScoreController;
use App\Controllers\SearchController;
use App\Controllers\TunerController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/** @var App\Core\Router $router */

// --- Public routes ---------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'loginForm'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/register', [AuthController::class, 'registerForm'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);

// --- Authenticated routes --------------------------------------------------
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->get('/dashboard', [HomeController::class, 'dashboard'], [AuthMiddleware::class]);

// Afinador
$router->get('/afinador', [TunerController::class, 'index'], [AuthMiddleware::class]);

// Partituras
$router->get('/partituras', [ScoreController::class, 'index'], [AuthMiddleware::class]);
$router->get('/partituras/nueva', [ScoreController::class, 'create'], [AuthMiddleware::class]);
$router->post('/partituras', [ScoreController::class, 'store'], [AuthMiddleware::class]);
$router->get('/partituras/{id}', [ScoreController::class, 'show'], [AuthMiddleware::class]);
$router->get('/partituras/{id}/descargar', [ScoreController::class, 'download'], [AuthMiddleware::class]);
$router->post('/partituras/{id}/eliminar', [ScoreController::class, 'destroy'], [AuthMiddleware::class]);

// Buscar musica
$router->get('/buscar', [SearchController::class, 'index'], [AuthMiddleware::class]);
$router->get('/api/buscar', [SearchController::class, 'api'], [AuthMiddleware::class]);

// Zona de programadores
$router->get('/programadores', [DeveloperController::class, 'index'], [AuthMiddleware::class]);
$router->get('/programadores/nuevo', [DeveloperController::class, 'create'], [AuthMiddleware::class]);
$router->post('/programadores', [DeveloperController::class, 'store'], [AuthMiddleware::class]);
$router->get('/programadores/{id}', [DeveloperController::class, 'show'], [AuthMiddleware::class]);
$router->get('/programadores/{id}/descargar', [DeveloperController::class, 'download'], [AuthMiddleware::class]);
$router->post('/programadores/{id}/eliminar', [DeveloperController::class, 'destroy'], [AuthMiddleware::class]);
