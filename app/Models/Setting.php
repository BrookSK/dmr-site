<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

/**
 * Configurações do sistema (chave/valor) armazenadas no banco.
 *
 * Valores marcados como secretos (ex.: senha SMTP) são criptografados com
 * AES-256-GCM usando a app_key antes de persistir, e descriptografados na leitura.
 */
final class Setting
{
    /** @var array<string,string|null>|null cache em memória por requisição */
    private static ?array $cache = null;

    private static function loadCache(): void
    {
        if (self::$cache !== null) {
            return;
        }
        self::$cache = [];
        $rows = Database::instance()->fetchAll("SELECT key_name, value, is_secret FROM settings");
        foreach ($rows as $row) {
            $value = $row['value'];
            if ((int) $row['is_secret'] === 1 && $value !== null && $value !== '') {
                $value = self::decrypt((string) $value);
            }
            self::$cache[$row['key_name']] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::loadCache();
        $value = self::$cache[$key] ?? null;
        return ($value === null || $value === '') ? $default : $value;
    }

    /**
     * Retorna todas as configurações de um grupo (já descriptografadas).
     *
     * @return array<string,string|null>
     */
    public static function group(string $group): array
    {
        $rows = Database::instance()->fetchAll(
            "SELECT key_name, value, is_secret FROM settings WHERE group_name = :g ORDER BY id",
            [':g' => $group]
        );
        $out = [];
        foreach ($rows as $row) {
            $value = $row['value'];
            if ((int) $row['is_secret'] === 1 && $value !== null && $value !== '') {
                $value = self::decrypt((string) $value);
            }
            $out[$row['key_name']] = $value;
        }
        return $out;
    }

    public static function set(string $key, ?string $value, bool $isSecret = false, string $group = 'general'): void
    {
        $stored = $value;
        if ($isSecret && $value !== null && $value !== '') {
            $stored = self::encrypt($value);
        }

        $existing = Database::instance()->fetch(
            "SELECT id FROM settings WHERE key_name = :k",
            [':k' => $key]
        );

        if ($existing) {
            Database::instance()->run(
                "UPDATE settings SET value = :v, is_secret = :s WHERE key_name = :k",
                [':v' => $stored, ':s' => $isSecret ? 1 : 0, ':k' => $key]
            );
        } else {
            Database::instance()->run(
                "INSERT INTO settings (group_name, key_name, value, is_secret) VALUES (:g, :k, :v, :s)",
                [':g' => $group, ':k' => $key, ':v' => $stored, ':s' => $isSecret ? 1 : 0]
            );
        }

        self::$cache = null; // invalida cache
    }

    /**
     * Atualiza em lote um conjunto de chaves.
     *
     * @param array<string,string|null> $values
     * @param array<int,string> $secretKeys chaves que devem ser criptografadas
     */
    public static function updateMany(array $values, array $secretKeys = []): void
    {
        foreach ($values as $key => $value) {
            // Para campos secretos vazios, mantém o valor atual (não sobrescreve).
            if (in_array($key, $secretKeys, true) && ($value === null || $value === '')) {
                continue;
            }
            self::set($key, $value, in_array($key, $secretKeys, true));
        }
    }

    // --- Criptografia de valores sensíveis ---------------------------------

    private static function key(): string
    {
        $appKey = (string) Config::get('app_key', 'dmr-default-key');
        return hash('sha256', $appKey, true); // 32 bytes para AES-256
    }

    private static function encrypt(string $plain): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            return $plain;
        }
        return 'enc:' . base64_encode($iv . $tag . $cipher);
    }

    private static function decrypt(string $stored): string
    {
        if (strncmp($stored, 'enc:', 4) !== 0) {
            return $stored; // valor legado em claro
        }
        $raw = base64_decode(substr($stored, 4), true);
        if ($raw === false || strlen($raw) < 28) {
            return '';
        }
        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $cipher = substr($raw, 28);
        $plain = openssl_decrypt($cipher, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        return $plain === false ? '' : $plain;
    }
}
