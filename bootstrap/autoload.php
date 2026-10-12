<?php

declare(strict_types=1);

/**
 * Registers the PSR-4 autoloader.
 *
 * Prefers Composer's autoloader when dependencies are installed, and falls
 * back to a tiny built-in loader so the app also runs with a plain PHP install.
 */
$composer = dirname(__DIR__) . '/vendor/autoload.php';

if (is_file($composer)) {
    require $composer;

    return;
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require dirname(__DIR__) . '/app/Support/helpers.php';
