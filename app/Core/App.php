<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Núcleo da aplicação: bootstrap, headers de segurança e dispatch de rotas.
 */
final class App
{
    private Router $router;

    /** @param array<string,mixed> $config */
    public function __construct(array $config)
    {
        Config::load($config);
        $this->sendSecurityHeaders();
        Session::start();

        $this->loadHelpers();

        $this->router = new Router();
        $this->loadRoutes();
    }

    public function run(): void
    {
        $request = new Request();

        try {
            $this->router->dispatch($request);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    private function sendSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-XSS-Protection: 1; mode=block');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header_remove('X-Powered-By');
    }

    private function loadHelpers(): void
    {
        require_once APP_PATH . '/Core/helpers.php';
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require APP_PATH . '/../routes/web.php';
    }

    private function handleException(Throwable $e): void
    {
        if (Config::get('env') !== 'production') {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo "Erro: {$e->getMessage()}\n\n";
            echo $e->getFile() . ':' . $e->getLine() . "\n\n";
            echo $e->getTraceAsString();
            return;
        }

        error_log('[DMR] ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
        http_response_code(500);
        if (is_file(APP_PATH . '/Views/errors/500.php')) {
            echo View::render('errors/500', ['solidHeader' => true], 'layouts/site');
        } else {
            echo 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
        }
    }
}
