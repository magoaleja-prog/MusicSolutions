<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Application kernel: loads environment, routes and dispatches requests.
 */
final class App
{
    private Router $router;

    public function __construct(private string $basePath)
    {
    }

    public function boot(): void
    {
        Env::load($this->basePath . '/.env');

        date_default_timezone_set((string) config('app.timezone', 'UTC'));

        if ((bool) config('app.debug', false)) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        }

        Session::start();

        $this->router = new Router();
        $router = $this->router;

        require $this->basePath . '/routes/web.php';
    }

    public function run(): void
    {
        $request = new Request();

        try {
            $response = $this->router->dispatch($request);
        } catch (Throwable $exception) {
            $response = $this->renderException($exception);
        }

        $response->send();
    }

    private function renderException(Throwable $exception): Response
    {
        $this->log($exception);

        if ((bool) config('app.debug', false)) {
            $details = sprintf(
                '<details open class="debug-trace"><summary>%s</summary><pre>%s</pre></details>',
                e($exception->getMessage()),
                e($exception->getTraceAsString())
            );

            return Response::make(View::render('errors/500', [
                'status' => 500,
                'message' => $exception->getMessage(),
                'trace' => $details,
            ]), 500);
        }

        return Response::abort(500);
    }

    private function log(Throwable $exception): void
    {
        $directory = $this->basePath . '/storage/logs';

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $line = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        @file_put_contents($directory . '/app.log', $line, FILE_APPEND);
    }
}
