<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Exige a permissão de gerenciamento de configurações.
 */
final class CanManageSettings extends Permission
{
    protected function permission(): string
    {
        return 'settings.manage';
    }
}
