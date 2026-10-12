<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Plain PHP view renderer with optional layout.
 */
final class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/app'): string
    {
        $content = self::renderFile($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::renderFile($layout, array_merge($data, ['content' => $content]));
    }

    private static function renderFile(string $view, array $data): string
    {
        $path = base_path('resources/views/' . str_replace('.', '/', $view) . '.php');

        if (!is_file($path)) {
            throw new RuntimeException("Vista no encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }
}
