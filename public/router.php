<?php

declare(strict_types=1);

/**
 * Router script for the PHP built-in server:
 *   php -S localhost:8000 -t public public/router.php
 *
 * Serves existing static files directly and forwards everything else to the
 * front controller.
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
