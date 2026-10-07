<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Roteador simples com suporte a parâmetros nomeados ({id}) e middlewares.
 */
final class Router
{
    /** @var array<int,array{method:string,pattern:string,handler:mixed,middlewares:array<int,string>}> */
    private array $routes = [];

    public function get(string $pattern, mixed $handler, array $middlewares = []): void
    {
        $this->add('GET', $pattern, $handler, $middlewares);
    }

    public function post(string $pattern, mixed $handler, array $middlewares = []): void
    {
        $this->add('POST', $pattern, $handler, $middlewares);
    }

    public function put(string $pattern, mixed $handler, array $middlewares = []): void
    {
        $this->add('PUT', $pattern, $handler, $middlewares);
    }

    public function delete(string $pattern, mixed $handler, array $middlewares = []): void
    {
        $this->add('DELETE', $pattern, $handler, $middlewares);
    }

    private function add(string $method, string $pattern, mixed $handler, array $middlewares): void
    {
        $this->routes[] = [
            'method'      => $method,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Resolve e despacha a requisição.
     */
    public function dispatch(Request $request): void
    {
        $path = $request->path();
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $path);
            if ($params === null) {
                continue;
            }

            // Executa middlewares (ex.: auth, permissão).
            foreach ($route['middlewares'] as $middleware) {
                $this->runMiddleware($middleware, $request);
            }

            $this->invoke($route['handler'], $request, $params);
            return;
        }

        $this->notFound();
    }

    /**
     * @return array<string,string>|null parâmetros capturados ou null se não casar
     */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    private function runMiddleware(string $middleware, Request $request): void
    {
        $class = 'App\\Middleware\\' . $middleware;
        if (!class_exists($class)) {
            throw new RuntimeException("Middleware não encontrado: {$middleware}");
        }
        (new $class())->handle($request);
    }

    /**
     * @param mixed $handler array [Controller::class, 'metodo'] ou callable
     * @param array<string,string> $params
     */
    private function invoke(mixed $handler, Request $request, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->$action($request, $params);
            return;
        }

        if (is_callable($handler)) {
            $handler($request, $params);
            return;
        }

        throw new RuntimeException('Handler de rota inválido.');
    }

    private function notFound(): void
    {
        http_response_code(404);
        if (is_file(APP_PATH . '/Views/errors/404.php')) {
            echo View::render('errors/404', ['solidHeader' => true], 'layouts/site');
        } else {
            echo '404 — Página não encontrada.';
        }
    }
}
