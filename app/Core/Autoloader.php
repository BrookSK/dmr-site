<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Autoloader PSR-4 simples, sem dependência do Composer.
 */
final class Autoloader
{
    /** @var array<string,string> prefixo de namespace => diretório base */
    private static array $prefixes = [];

    /**
     * @param array<string,string> $map Ex.: ['App\\' => '/caminho/app']
     */
    public static function register(array $map): void
    {
        foreach ($map as $prefix => $baseDir) {
            self::$prefixes[$prefix] = rtrim($baseDir, '/\\');
        }

        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $file = $baseDir . '/' . str_replace('\\', '/', $relative) . '.php';

            if (is_file($file)) {
                require $file;
                return;
            }
        }
    }
}
