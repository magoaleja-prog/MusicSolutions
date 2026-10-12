<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'MusicSolutions'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'version' => '1.0.0',
    'upload_max_size' => (int) env('UPLOAD_MAX_SIZE', 10 * 1024 * 1024),
    'music_search_external' => (bool) env('MUSIC_SEARCH_EXTERNAL', true),
];
