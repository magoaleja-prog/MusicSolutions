<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Pattern based HTTP router with middleware support.
 */
final class Router
{
    /** @var array<int, array{method:string, uri:string, action:mixed, middleware:array<int, class-string>}> */
    private array $routes = [];

    public function get(string $uri, mixed $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, mixed $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, mixed $action, array $middleware = []): void
    {
        $this->add('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, mixed $action, array $middleware = []): void
    {
        $this->add('DELETE', $uri, $action, $middleware);
    }

    public function add(string $method, string $uri, mixed $action, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $this->normalize($uri),
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->compile($route['uri']);

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            foreach ($route['middleware'] as $middleware) {
                $result = (new $middleware())->handle($request);

                if ($result instanceof Response) {
                    return $result;
                }
            }

            $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

            return $this->invoke($route['action'], $params);
        }

        return Response::abort(404);
    }

    private function invoke(mixed $action, array $params): Response
    {
        if (is_callable($action)) {
            $result = $action(...$params);
        } else {
            [$class, $method] = $action;
            $result = (new $class())->{$method}(...$params);
        }

        if ($result instanceof Response) {
            return $result;
        }

        return Response::make((string) $result);
    }

    private function compile(string $uri): string
    {
        $pattern = preg_replace(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            '(?P<$1>[^/]+)',
            $uri
        );

        return '#^' . $pattern . '$#';
    }

    private function normalize(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');

        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }
}
