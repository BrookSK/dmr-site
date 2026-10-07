<?php

declare(strict_types=1);

use App\Core\View;
use App\Core\Config;
use App\Models\Setting;

if (!function_exists('e')) {
    /**
     * Escaping de saída (anti-XSS).
     */
    function e(mixed $value): string
    {
        return View::e($value);
    }
}

if (!function_exists('base_url')) {
    /**
     * Monta uma URL absoluta a partir do caminho informado.
     * Usa a configuração 'site_url' do banco quando disponível.
     */
    function base_url(string $path = ''): string
    {
        $base = Setting::get('site_url');

        if (!$base) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base = $scheme . '://' . $host;
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * URL de um asset público. O .htaccess roteia /assets para /public/assets.
     */
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('old')) {
    /**
     * Recupera valor antigo de formulário (após erro de validação).
     *
     * @param array<string,mixed> $data
     */
    function old(string $key, array $data = [], string $default = ''): string
    {
        return isset($data[$key]) ? e($data[$key]) : $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('setting')) {
    /**
     * Atalho para App\Models\Setting::get().
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
