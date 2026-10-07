<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Services\AuthService;

/**
 * Base para middlewares que exigem uma permissão específica.
 *
 * A verificação SEMPRE ocorre no backend. Esconder botões na interface não
 * substitui essa checagem.
 */
abstract class Permission
{
    abstract protected function permission(): string;

    public function handle(Request $request): void
    {
        if (!AuthService::can($this->permission())) {
            http_response_code(403);
            echo \App\Core\View::render('errors/403', [], 'layouts/admin');
            exit;
        }
    }
}
