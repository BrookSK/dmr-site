<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Upload e resolução das logos da marca (fundo escuro e fundo claro).
 */
final class BrandLogoService
{
    private const MAX_BYTES = 2_097_152; // 2 MB
    private const ALLOWED_EXT = ['png', 'jpg', 'jpeg', 'webp', 'svg'];

    public static function url(mixed $relativePath): ?string
    {
        $path = trim((string) $relativePath);
        if ($path === '') {
            return null;
        }

        return base_url('uploads/' . ltrim($path, '/'));
    }

    /**
     * @param 'dark'|'light' $surface
     */
    public static function urlFor(string $surface = 'dark'): ?string
    {
        $dark = setting('site_logo');
        $light = setting('site_logo_on_light');
        $chosen = $surface === 'light'
            ? ($light ?: $dark)
            : ($dark ?: $light);

        return self::url(is_string($chosen) ? $chosen : null);
    }

    /**
     * @param array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     * @param 'dark'|'light' $slot
     */
    public static function store(array $file, string $slot): string
    {
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::uploadErrorMessage((int) $file['error']));
        }
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            throw new RuntimeException('A logo deve ter no máximo 2 MB.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Arquivo de upload inválido.');
        }

        $ext = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new RuntimeException('Use PNG, JPG, WEBP ou SVG.');
        }

        self::assertSafeImage($file['tmp_name'], $ext);

        $dir = PUBLIC_PATH . '/uploads/brand';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Não foi possível criar a pasta de uploads.');
        }

        $name = ($slot === 'light' ? 'logo-claro' : 'logo-escuro') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Falha ao salvar a logo.');
        }

        return 'brand/' . $name;
    }

    public static function delete(?string $relativePath): void
    {
        $path = trim((string) $relativePath);
        if ($path === '' || str_contains($path, '..')) {
            return;
        }

        $full = PUBLIC_PATH . '/uploads/' . ltrim(str_replace('\\', '/', $path), '/');
        $root = realpath(PUBLIC_PATH . '/uploads');
        $resolved = realpath($full);
        if ($root === false || $resolved === false || !str_starts_with($resolved, $root)) {
            return;
        }
        if (is_file($resolved)) {
            unlink($resolved);
        }
    }

    private static function assertSafeImage(string $tmp, string $ext): void
    {
        if ($ext === 'svg') {
            $head = (string) file_get_contents($tmp, false, null, 0, 2048);
            if (!preg_match('/<(svg|\\?xml)/i', $head)) {
                throw new RuntimeException('O SVG enviado não parece válido.');
            }
            if (preg_match('/<(script|foreignObject)|javascript:/i', $head)) {
                throw new RuntimeException('O SVG contém conteúdo não permitido.');
            }
            return;
        }

        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new RuntimeException('O arquivo não é uma imagem válida.');
        }
    }

    private static function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'A logo excede o tamanho máximo permitido.',
            UPLOAD_ERR_PARTIAL => 'O envio da logo foi interrompido. Tente novamente.',
            default => 'Não foi possível enviar a logo.',
        };
    }
}
