<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Role;
use App\Models\User;

/**
 * Serviço de autenticação e autorização.
 *
 * As permissões são sempre verificadas aqui (backend). Esconder botões na UI
 * NÃO substitui essa checagem.
 */
final class AuthService
{
    /**
     * Tenta autenticar com e-mail e senha. Retorna true em caso de sucesso.
     */
    public static function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user === null || (int) $user['is_active'] !== 1) {
            // Executa um hash fictício para mitigar timing attacks.
            password_verify($password, '$2y$10$usesomesillystringforsalt1234567890abcdefghi');
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        // Rehash caso o algoritmo/custo tenha mudado.
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $userModel->updatePassword((int) $user['id'], $password);
        }

        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        self::cachePermissions((int) $user['id']);

        $userModel->touchLogin((int) $user['id']);

        return true;
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    /** @return array<string,mixed>|null */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return (new User())->find((int) Session::get('user_id'));
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id !== null ? (int) $id : null;
    }

    public static function isSuperadmin(): bool
    {
        $slugs = self::roleSlugs();
        return in_array('superadmin', $slugs, true);
    }

    /**
     * Verifica se o usuário atual possui uma permissão.
     * Superadmin possui todas as permissões implicitamente.
     */
    public static function can(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }
        if (self::isSuperadmin()) {
            return true;
        }
        $perms = Session::get('permissions', []);
        return is_array($perms) && in_array($permission, $perms, true);
    }

    /** @return array<int,string> */
    public static function roleSlugs(): array
    {
        $slugs = Session::get('role_slugs', []);
        return is_array($slugs) ? $slugs : [];
    }

    /**
     * Recarrega e armazena na sessão as permissões/papéis do usuário.
     */
    public static function cachePermissions(int $userId): void
    {
        $userModel = new User();
        $roleIds = $userModel->roleIds($userId);

        $roleModel = new Role();
        $permissions = $roleModel->permissionsForRoles($roleIds);

        // Resolve slugs dos papéis.
        $slugs = [];
        foreach ($roleModel->all() as $role) {
            if (in_array((int) $role['id'], $roleIds, true)) {
                $slugs[] = $role['slug'];
            }
        }

        Session::set('permissions', $permissions);
        Session::set('role_slugs', $slugs);
    }
}
