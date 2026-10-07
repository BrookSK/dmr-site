<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Model base com acesso ao banco via PDO (prepared statements).
 */
abstract class Model
{
    protected Database $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::instance();
    }

    /** @return array<string,mixed>|null */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    public function count(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS total FROM {$this->table}");
        return (int) ($row['total'] ?? 0);
    }

    public function delete(int $id): void
    {
        $this->db->run("DELETE FROM {$this->table} WHERE id = :id", [':id' => $id]);
    }
}
