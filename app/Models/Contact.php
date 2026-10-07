<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Leads recebidos pelo formulário de contato da landing page.
 */
final class Contact extends Model
{
    protected string $table = 'contacts';

    /** @param array<string,mixed> $data */
    public function store(array $data): int
    {
        $this->db->run(
            "INSERT INTO contacts (name, email, phone, audience, message, ip_address, user_agent, email_sent)
             VALUES (:name, :email, :phone, :audience, :message, :ip, :ua, :sent)",
            [
                ':name'     => $data['name'],
                ':email'    => $data['email'],
                ':phone'    => $data['phone'],
                ':audience' => $data['audience'],
                ':message'  => $data['message'] ?? null,
                ':ip'       => $data['ip'] ?? null,
                ':ua'       => $data['user_agent'] ?? null,
                ':sent'     => !empty($data['email_sent']) ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function markEmailSent(int $id): void
    {
        $this->db->run("UPDATE contacts SET email_sent = 1 WHERE id = :id", [':id' => $id]);
    }

    /** @return array<int,array<string,mixed>> */
    public function recent(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));
        return $this->db->fetchAll(
            "SELECT * FROM contacts ORDER BY created_at DESC LIMIT {$limit}"
        );
    }
}
