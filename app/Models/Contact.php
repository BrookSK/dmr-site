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

    /** Situações do atendimento (coluna status). */
    public const STATUSES = [
        'novo'         => 'Novo',
        'em_andamento' => 'Em atendimento',
        'concluido'    => 'Concluído',
    ];

    /** Tipos de contato (coluna audience). */
    public const AUDIENCES = [
        'cliente'       => 'Comprador(a)',
        'corretor'      => 'Corretor',
        'imobiliaria'   => 'Imobiliária',
        'construtora'   => 'Construtora / incorporadora',
        'incorporadora' => 'Incorporadora',
        'home-equity'   => 'Home Equity',
        'outro'         => 'Outro',
    ];

    public static function audienceLabel(string $audience): string
    {
        return self::AUDIENCES[$audience] ?? ucfirst($audience);
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? ucfirst($status);
    }

    /**
     * Lista com busca, filtros e paginação.
     *
     * @param array{q?:string,audience?:string,status?:string} $filters
     * @return array{items:array<int,array<string,mixed>>,total:int,pages:int,page:int}
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(name LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3 OR message LIKE :q4)';
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $params += [':q1' => $like, ':q2' => $like, ':q3' => $like, ':q4' => $like];
        }
        $audience = (string) ($filters['audience'] ?? '');
        if ($audience === 'parceiro') {
            $where[] = "audience IN ('corretor','imobiliaria','construtora','incorporadora')";
        } elseif (array_key_exists($audience, self::AUDIENCES)) {
            $where[] = 'audience = :audience';
            $params[':audience'] = $audience;
        }
        $status = (string) ($filters['status'] ?? '');
        if (array_key_exists($status, self::STATUSES)) {
            $where[] = 'status = :status';
            $params[':status'] = $status;
        }

        $sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = (int) ($this->db->fetch("SELECT COUNT(*) AS t FROM contacts {$sqlWhere}", $params)['t'] ?? 0);
        $perPage = max(1, min(100, $perPage));
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($pages, $page));
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT * FROM contacts {$sqlWhere} ORDER BY created_at DESC, id DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function countByStatus(string $status): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS t FROM contacts WHERE status = :s', [':s' => $status]);
        return (int) ($row['t'] ?? 0);
    }

    public function updateStatus(int $id, string $status): void
    {
        if (!array_key_exists($status, self::STATUSES)) {
            return;
        }
        $this->db->run('UPDATE contacts SET status = :s WHERE id = :id', [':s' => $status, ':id' => $id]);
    }

    /** ID do contato anterior/próximo na ordem da lista (mais recente primeiro). */
    public function neighbor(int $id, string $createdAt, bool $newer): ?int
    {
        $sql = $newer
            ? 'SELECT id FROM contacts WHERE (created_at > :c1 OR (created_at = :c2 AND id > :id)) ORDER BY created_at ASC, id ASC LIMIT 1'
            : 'SELECT id FROM contacts WHERE (created_at < :c1 OR (created_at = :c2 AND id < :id)) ORDER BY created_at DESC, id DESC LIMIT 1';
        $row = $this->db->fetch($sql, [':c1' => $createdAt, ':c2' => $createdAt, ':id' => $id]);
        return $row ? (int) $row['id'] : null;
    }

    /**
     * Separa os detalhes que o formulário grava no início da mensagem
     * ("Interesse: ...", "Empresa: ...", "Cidade/UF: ...") do texto escrito pela pessoa.
     *
     * @return array{details:array<string,string>,message:string}
     */
    public static function parseMessage(?string $message): array
    {
        $labels = ['Interesse' => 'Interesse', 'Empresa' => 'Empresa', 'Cidade/UF' => 'Cidade / Estado'];
        $lines = explode("\n", str_replace("\r\n", "\n", (string) $message));
        $details = [];
        while ($lines !== [] && preg_match('/^(Interesse|Empresa|Cidade\/UF): (.*)$/u', $lines[0], $m)) {
            $details[$labels[$m[1]]] = trim($m[2]);
            array_shift($lines);
        }
        if ($details !== [] && $lines !== [] && trim($lines[0]) === '') {
            array_shift($lines);
        }
        return ['details' => $details, 'message' => trim(implode("\n", $lines))];
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
