<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Usuários do painel administrativo.
 */
final class User extends Model
{
    protected string $table = 'users';

    /** @return array<string,mixed>|null */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = :e LIMIT 1",
            [':e' => strtolower(trim($email))]
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function allWithRoles(): array
    {
        return $this->db->fetchAll("
            SELECT u.*,
                   GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') AS roles,
                   GROUP_CONCAT(r.slug ORDER BY r.slug SEPARATOR ',')   AS role_slugs
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            GROUP BY u.id
            ORDER BY u.name
        ");
    }

    public function create(string $name, string $email, string $password, bool $active = true): int
    {
        $this->db->run(
            "INSERT INTO users (name, email, password_hash, is_active)
             VALUES (:n, :e, :p, :a)",
            [
                ':n' => $name,
                ':e' => strtolower(trim($email)),
                ':p' => password_hash($password, PASSWORD_DEFAULT),
                ':a' => $active ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, bool $active): void
    {
        $this->db->run(
            "UPDATE users SET name = :n, email = :e, is_active = :a WHERE id = :id",
            [':n' => $name, ':e' => strtolower(trim($email)), ':a' => $active ? 1 : 0, ':id' => $id]
        );
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->db->run(
            "UPDATE users SET password_hash = :p WHERE id = :id",
            [':p' => password_hash($password, PASSWORD_DEFAULT), ':id' => $id]
        );
    }

    public function setActive(int $id, bool $active): void
    {
        $this->db->run(
            "UPDATE users SET is_active = :a WHERE id = :id",
            [':a' => $active ? 1 : 0, ':id' => $id]
        );
    }

    public function touchLogin(int $id): void
    {
        $this->db->run("UPDATE users SET last_login_at = NOW() WHERE id = :id", [':id' => $id]);
    }

    /** @return array<int,int> ids dos papéis do usuário */
    public function roleIds(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT role_id FROM user_roles WHERE user_id = :u",
            [':u' => $userId]
        );
        return array_map(static fn ($r) => (int) $r['role_id'], $rows);
    }

    /** @param array<int,int> $roleIds */
    public function syncRoles(int $userId, array $roleIds): void
    {
        $this->db->run("DELETE FROM user_roles WHERE user_id = :u", [':u' => $userId]);
        foreach ($roleIds as $roleId) {
            $this->db->run(
                "INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:u, :r)",
                [':u' => $userId, ':r' => (int) $roleId]
            );
        }
    }

    public function isSuperadmin(int $userId): bool
    {
        $row = $this->db->fetch("
            SELECT 1 FROM user_roles ur
            JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = :u AND r.slug = 'superadmin'
            LIMIT 1
        ", [':u' => $userId]);
        return $row !== null;
    }

    public function countActive(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS t FROM users WHERE is_active = 1");
        return (int) ($row['t'] ?? 0);
    }
}
