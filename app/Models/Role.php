<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Perfis de acesso (roles) e suas permissões.
 */
final class Role extends Model
{
    protected string $table = 'roles';

    /** @return array<int,array<string,mixed>> */
    public function all(string $orderBy = 'name ASC'): array
    {
        return $this->db->fetchAll("SELECT * FROM roles ORDER BY {$orderBy}");
    }

    /**
     * Retorna os slugs de permissão de um conjunto de papéis.
     *
     * @param array<int,int> $roleIds
     * @return array<int,string>
     */
    public function permissionsForRoles(array $roleIds): array
    {
        if ($roleIds === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($roleIds), '?'));
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT p.slug
             FROM role_permissions rp
             JOIN permissions p ON p.id = rp.permission_id
             WHERE rp.role_id IN ({$placeholders})",
            array_values($roleIds)
        );
        return array_map(static fn ($r) => (string) $r['slug'], $rows);
    }
}
