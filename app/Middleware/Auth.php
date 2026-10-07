<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Garante que o usuário esteja autenticado. Caso contrário, redireciona ao login.
 */
final class Auth
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Faça login para acessar essa área.');
            Session::set('intended_url', $request->path());
            Response::redirect('/admin/login');
        }
    }
}
