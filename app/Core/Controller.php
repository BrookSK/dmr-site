<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controller base com helpers de view, redirecionamento e validação de CSRF.
 */
abstract class Controller
{
    /**
     * Renderiza uma view do site.
     *
     * @param array<string,mixed> $data
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/site'): void
    {
        Response::html(View::render($view, $data, $layout));
    }

    /**
     * Renderiza uma view usando o layout administrativo.
     *
     * @param array<string,mixed> $data
     */
    protected function adminView(string $view, array $data = []): void
    {
        Response::html(View::render($view, $data, 'layouts/admin'));
    }

    protected function redirect(string $to): void
    {
        Response::redirect($to);
    }

    /** @param array<string,mixed>|array<int,mixed> $data */
    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    /**
     * Valida o token CSRF da requisição; aborta com 419 se inválido.
     */
    protected function requireCsrf(Request $request): void
    {
        if (!Csrf::validate($request->csrfToken())) {
            http_response_code(419);
            exit('Token de segurança inválido ou expirado. Recarregue a página e tente novamente.');
        }
    }
}
