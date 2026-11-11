<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Exige a permissão de visualizar os contatos recebidos pelo site.
 */
final class CanViewContacts extends Permission
{
    protected function permission(): string
    {
        return 'contacts.view';
    }
}
