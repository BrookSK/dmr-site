<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Upload das imagens do "Conteúdo do site" (public/uploads/content/).
 */
final class ContentUploadService
{
    private const MAX_BYTES = 8_388_608; // 8 MB
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    /**
     * @param array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     * @return string caminho relativo a uploads/ (ex.: "content/home-hero-image-1a2b3c4d.jpg")
     */
    public static function store(array $file, string $hint): string
    {
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(match ((int) $file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'A imagem é maior que o limite do servidor.',
                UPLOAD_ERR_PARTIAL => 'O envio da imagem foi interrompido. Tente novamente.',
                default => 'Não foi possível enviar a imagem.',
            });
        }
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            throw new RuntimeException('Cada imagem pode ter no máximo 8 MB.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Arquivo de upload inválido.');
        }

        $ext = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new RuntimeException('Formato não aceito. Use JPG, PNG, WEBP, GIF ou SVG.');
        }
        self::assertSafe($file['tmp_name'], $ext);

        $dir = PUBLIC_PATH . '/uploads/content';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Não foi possível criar a pasta de uploads.');
        }

        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($hint)), '-');
        $name = substr($slug !== '' ? $slug : 'imagem', 0, 50) . '-' . bin2hex(random_bytes(4)) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            throw new RuntimeException('Falha ao salvar a imagem.');
        }
        @chmod($dir . '/' . $name, 0644);

        return 'content/' . $name;
    }

    /** Apaga um upload do conteúdo. Ignora imagens padrão (assets) e caminhos suspeitos. */
    public static function delete(?string $path): void
    {
        $path = ltrim(str_replace('\\', '/', trim((string) $path)), '/');
        if (!str_starts_with($path, 'content/') || str_contains($path, '..')) {
            return;
        }
        $root = realpath(PUBLIC_PATH . '/uploads/content');
        $file = realpath(PUBLIC_PATH . '/uploads/' . $path);
        if ($root !== false && $file !== false && str_starts_with($file, $root) && is_file($file)) {
            @unlink($file);
        }
    }

    private static function assertSafe(string $tmp, string $ext): void
    {
        if ($ext === 'svg') {
            $svg = (string) file_get_contents($tmp);
            if (!preg_match('/<svg[\s>]/i', $svg)) {
                throw new RuntimeException('O SVG enviado não parece válido.');
            }
            // SVG pode carregar script: recusa qualquer conteúdo ativo.
            if (preg_match('/<(script|foreignObject|iframe|embed|object)\b|\son[a-z]+\s*=|javascript:|<!ENTITY/i', $svg)) {
                throw new RuntimeException('O SVG contém conteúdo não permitido (scripts ou eventos).');
            }
            return;
        }
        if (@getimagesize($tmp) === false) {
            throw new RuntimeException('O arquivo enviado não é uma imagem válida.');
        }
    }
}
