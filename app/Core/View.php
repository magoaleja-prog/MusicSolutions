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
        $viewVars = [];
        $content = self::renderFile($view, $data, $viewVars);

        if ($layout === null) {
            return $content;
        }

        // Las variables que la vista declara (p.ej. $pageScripts o $bodyClass)
        // tambien llegan al layout, igual que los datos del controlador.
        return self::renderFile($layout, array_merge($data, $viewVars, ['content' => $content]));
    }

    private static function renderFile(string $view, array $data, ?array &$declared = null): string
    {
        $path = base_path('resources/views/' . str_replace('.', '/', $view) . '.php');

        if (!is_file($path)) {
            throw new RuntimeException("Vista no encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;
        $output = (string) ob_get_clean();

        if ($declared !== null) {
            $internal = ['data' => true, 'path' => true, 'output' => true, 'declared' => true];
            $declared = array_diff_key(get_defined_vars(), $data, $internal);
        }

        return $output;
    }
}
