<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renderizador de views PHP com suporte a layouts e escaping.
 */
final class View
{
    private static string $viewPath = APP_PATH . '/Views';

    /**
     * Renderiza uma view dentro de um layout.
     *
     * @param array<string,mixed> $data
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/site'): string
    {
        $content = self::renderPartial($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::renderPartial($layout, array_merge($data, ['content' => $content]));
    }

    /**
     * Renderiza uma view sem layout (ex.: partials).
     *
     * @param array<string,mixed> $data
     */
    public static function renderPartial(string $view, array $data = []): string
    {
        $file = self::$viewPath . '/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("View não encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    /**
     * Escaping de saída (anti-XSS). Helper global `e()` também disponível.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
