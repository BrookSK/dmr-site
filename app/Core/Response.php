<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Respostas HTTP (HTML, JSON, redirecionamento).
 */
final class Response
{
    public static function html(string $body, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=UTF-8');
        echo $body;
    }

    /** @param array<string,mixed>|array<int,mixed> $data */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function redirect(string $to, int $status = 302): void
    {
        header('Location: ' . $to, true, $status);
        exit;
    }
}
