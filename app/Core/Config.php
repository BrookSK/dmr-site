<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Acesso centralizado à configuração base da aplicação (config/config.php).
 */
final class Config
{
    /** @var array<string,mixed> */
    private static array $items = [];

    /** @param array<string,mixed> $items */
    public static function load(array $items): void
    {
        self::$items = $items;
    }

    /**
     * Suporta notação por ponto: Config::get('db.host').
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$items;

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
