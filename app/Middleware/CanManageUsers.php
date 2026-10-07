<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Exige a permissão de gerenciamento de usuários.
 */
final class CanManageUsers extends Permission
{
    protected function permission(): string
    {
        return 'users.manage';
    }
}
