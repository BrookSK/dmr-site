<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;

/**
 * CRUD de usuários e vínculo de perfis (roles).
 */
final class UserController extends Controller
{
    private User $users;
    private Role $roles;

    public function __construct()
    {
        $this->users = new User();
        $this->roles = new Role();
    }

    public function index(Request $request): void
    {
        $this->adminView('admin/users/index', [
            'title'   => 'Usuários',
            'users'   => $this->users->allWithRoles(),
            'success' => Session::flash('success'),
            'error'   => Session::flash('error'),
        ]);
    }

    public function create(Request $request): void
    {
        $this->adminView('admin/users/form', [
            'title'    => 'Novo usuário',
            'user'     => null,
            'roles'    => $this->roles->all(),
            'userRoles' => [],
            'error'    => Session::flash('error'),
        ]);
    }

    public function store(Request $request): void
    {
        $this->requireCsrf($request);

        $name = $request->string('name');
        $email = $request->string('email');
        $password = (string) $request->input('password', '');
        $active = $request->input('is_active') ? true : false;
        $roleIds = $this->sanitizeRoleIds($request->input('roles', []));

        $error = $this->validate($name, $email, $password, true);
        if ($error !== null) {
            Session::flash('error', $error);
            $this->redirect('/admin/usuarios/novo');
        }

        if ($this->users->findByEmail($email) !== null) {
            Session::flash('error', 'Já existe um usuário com esse e-mail.');
            $this->redirect('/admin/usuarios/novo');
        }

        $id = $this->users->create($name, $email, $password, $active);
        $this->users->syncRoles($id, $roleIds);

        Session::flash('success', 'Usuário criado com sucesso.');
        $this->redirect('/admin/usuarios');
    }

    public function edit(Request $request, array $params): void
    {
        $user = $this->users->find((int) $params['id']);
        if ($user === null) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $this->adminView('admin/users/form', [
            'title'     => 'Editar usuário',
            'user'      => $user,
            'roles'     => $this->roles->all(),
            'userRoles' => $this->users->roleIds((int) $user['id']),
            'error'     => Session::flash('error'),
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $this->requireCsrf($request);

        $id = (int) $params['id'];
        $user = $this->users->find($id);
        if ($user === null) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $name = $request->string('name');
        $email = $request->string('email');
        $active = $request->input('is_active') ? true : false;
        $roleIds = $this->sanitizeRoleIds($request->input('roles', []));

        $error = $this->validate($name, $email, '', false);
        if ($error !== null) {
            Session::flash('error', $error);
            $this->redirect("/admin/usuarios/{$id}/editar");
        }

        $existing = $this->users->findByEmail($email);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'Já existe outro usuário com esse e-mail.');
            $this->redirect("/admin/usuarios/{$id}/editar");
        }

        // Proteção: não permitir que o superadmin se auto-desative ou remova o próprio papel.
        if ($id === AuthService::id() && !$active) {
            Session::flash('error', 'Você não pode desativar a própria conta.');
            $this->redirect("/admin/usuarios/{$id}/editar");
        }

        $this->users->update($id, $name, $email, $active);
        $this->users->syncRoles($id, $roleIds);

        // Se editou a si mesmo, recarrega permissões na sessão.
        if ($id === AuthService::id()) {
            AuthService::cachePermissions($id);
        }

        Session::flash('success', 'Usuário atualizado com sucesso.');
        $this->redirect('/admin/usuarios');
    }

    public function toggleStatus(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = (int) $params['id'];

        if ($id === AuthService::id()) {
            Session::flash('error', 'Você não pode alterar o status da própria conta.');
            $this->redirect('/admin/usuarios');
        }

        $user = $this->users->find($id);
        if ($user === null) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $this->users->setActive($id, (int) $user['is_active'] !== 1);
        Session::flash('success', 'Status do usuário atualizado.');
        $this->redirect('/admin/usuarios');
    }

    public function resetPassword(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = (int) $params['id'];

        $password = (string) $request->input('password', '');
        if (strlen($password) < 8) {
            Session::flash('error', 'A nova senha deve ter ao menos 8 caracteres.');
            $this->redirect("/admin/usuarios/{$id}/editar");
        }

        if ($this->users->find($id) === null) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $this->users->updatePassword($id, $password);
        Session::flash('success', 'Senha redefinida com sucesso.');
        $this->redirect("/admin/usuarios/{$id}/editar");
    }

    public function destroy(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = (int) $params['id'];

        if ($id === AuthService::id()) {
            Session::flash('error', 'Você não pode excluir a própria conta.');
            $this->redirect('/admin/usuarios');
        }

        $user = $this->users->find($id);
        if ($user === null) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        // Impede a exclusão do último superadmin do sistema.
        if ($this->users->isSuperadmin($id) && $this->countSuperadmins() <= 1) {
            Session::flash('error', 'Não é possível excluir o único superadmin do sistema.');
            $this->redirect('/admin/usuarios');
        }

        $this->users->delete($id);
        Session::flash('success', 'Usuário excluído.');
        $this->redirect('/admin/usuarios');
    }

    private function countSuperadmins(): int
    {
        $all = $this->users->allWithRoles();
        $count = 0;
        foreach ($all as $u) {
            if (str_contains((string) ($u['role_slugs'] ?? ''), 'superadmin')) {
                $count++;
            }
        }
        return $count;
    }

    /** @param mixed $raw @return array<int,int> */
    private function sanitizeRoleIds(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        return array_values(array_unique(array_map('intval', $raw)));
    }

    private function validate(string $name, string $email, string $password, bool $requirePassword): ?string
    {
        if ($name === '' || mb_strlen($name) < 2) {
            return 'Informe um nome válido.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Informe um e-mail válido.';
        }
        if ($requirePassword && strlen($password) < 8) {
            return 'A senha deve ter ao menos 8 caracteres.';
        }
        return null;
    }
}
