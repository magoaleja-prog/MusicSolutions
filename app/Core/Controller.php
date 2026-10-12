<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller with rendering and response helpers.
 */
abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/app'): Response
    {
        return Response::make(View::render($view, $data, $layout));
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect($path);
    }
}
