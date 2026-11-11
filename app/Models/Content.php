<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

/**
 * Conteúdo editável do site (tabela site_contents).
 *
 * Só guarda o que foi alterado no painel. Quando uma chave não existe no banco,
 * quem chama usa o padrão definido em App\Services\ContentRegistry.
 */
final class Content
{
    /** @var array<string,array{value:?string,updated_at:?string}>|null */
    private static ?array $cache = null;

    private static function load(): void
    {
        if (self::$cache !== null) {
            return;
        }
        self::$cache = [];
        try {
            $rows = Database::instance()->fetchAll('SELECT key_name, value, updated_at FROM site_contents');
        } catch (Throwable $e) {
            // Tabela ainda não criada (migration 014 pendente): site segue com os padrões.
            error_log('[DMR][conteudo] ' . $e->getMessage());
            return;
        }
        foreach ($rows as $row) {
            self::$cache[(string) $row['key_name']] = [
                'value'      => $row['value'] === null ? null : (string) $row['value'],
                'updated_at' => $row['updated_at'] === null ? null : (string) $row['updated_at'],
            ];
        }
    }

    public static function has(string $key): bool
    {
        self::load();
        return array_key_exists($key, self::$cache ?? []);
    }

    public static function get(string $key): ?string
    {
        self::load();
        return self::$cache[$key]['value'] ?? null;
    }

    public static function updatedAt(string $key): ?string
    {
        self::load();
        return self::$cache[$key]['updated_at'] ?? null;
    }

    public static function set(string $key, string $value): void
    {
        // REPLACE também renova o updated_at (usado como "última atualização").
        Database::instance()->run(
            'REPLACE INTO site_contents (key_name, value) VALUES (:k, :v)',
            [':k' => $key, ':v' => $value]
        );
        self::$cache = null;
    }

    /** Remove a personalização: o campo volta ao padrão do código. */
    public static function forget(string $key): void
    {
        Database::instance()->run('DELETE FROM site_contents WHERE key_name = :k', [':k' => $key]);
        self::$cache = null;
    }

    /** Usado em testes/preview para injetar valores sem banco. @param array<string,string> $values */
    public static function fake(array $values): void
    {
        self::$cache = [];
        foreach ($values as $k => $v) {
            self::$cache[$k] = ['value' => $v, 'updated_at' => null];
        }
    }
}
