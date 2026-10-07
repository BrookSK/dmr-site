<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Services\AuthService;

/**
 * Login, logout e troca de senha do painel administrativo.
 */
final class AuthController extends Controller
{
    public function showLogin(Request $request): void
    {
        if (AuthService::check()) {
            $this->redirect('/admin');
        }

        $this->view('admin/auth/login', [
            'title' => 'Entrar — Painel DMR',
            'error' => Session::flash('error'),
        ], null);
    }

    public function login(Request $request): void
    {
        $this->requireCsrf($request);

        $email = $request->string('email');
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            Session::flash('error', 'Informe e-mail e senha.');
            $this->redirect('/admin/login');
        }

        if (!AuthService::attempt($email, $password)) {
            Session::flash('error', 'Credenciais inválidas ou usuário inativo.');
            $this->redirect('/admin/login');
        }

        $intended = Session::get('intended_url');
        Session::remove('intended_url');

        $this->redirect(is_string($intended) && str_starts_with($intended, '/admin') ? $intended : '/admin');
    }

    public function logout(Request $request): void
    {
        $this->requireCsrf($request);
        AuthService::logout();
        $this->redirect('/admin/login');
    }

    public function showProfile(Request $request): void
    {
        $this->adminView('admin/profile/edit', [
            'title'   => 'Minha conta',
            'user'    => AuthService::user(),
            'success' => Session::flash('success'),
            'error'   => Session::flash('error'),
        ]);
    }

    public function updatePassword(Request $request): void
    {
        $this->requireCsrf($request);

        $current = (string) $request->input('current_password', '');
        $new = (string) $request->input('new_password', '');
        $confirm = (string) $request->input('confirm_password', '');

        $user = AuthService::user();
        if ($user === null) {
            $this->redirect('/admin/login');
        }

        if (!password_verify($current, $user['password_hash'])) {
            Session::flash('error', 'A senha atual está incorreta.');
            $this->redirect('/admin/perfil');
        }

        if (strlen($new) < 8) {
            Session::flash('error', 'A nova senha deve ter ao menos 8 caracteres.');
            $this->redirect('/admin/perfil');
        }

        if ($new !== $confirm) {
            Session::flash('error', 'A confirmação não corresponde à nova senha.');
            $this->redirect('/admin/perfil');
        }

        (new User())->updatePassword((int) $user['id'], $new);
        Session::flash('success', 'Senha atualizada com sucesso.');
        $this->redirect('/admin/perfil');
    }
}
