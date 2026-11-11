<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Content;

/**
 * Lê o conteúdo editável (com padrão do ContentRegistry) e converte a
 * formatação simples do painel em HTML seguro.
 *
 * Segurança: todo texto é escapado ANTES de aplicar a formatação. As únicas
 * tags geradas são <strong>, <em>, <br>, <p>, <h2>/<h3>, <ul>/<li> e <a>
 * (com URL restrita a /, #, http(s), mailto e tel).
 */
final class ContentRenderer
{
    /** Valor bruto: string para campos simples, array para listas. */
    public static function value(string $key): mixed
    {
        $field = ContentRegistry::field($key);
        $type = $field['type'] ?? 'text';
        $default = $field['default'] ?? ($type === 'list' ? [] : '');

        if (!Content::has($key)) {
            return $type === 'list' ? self::normalizeList($default, $field) : (string) $default;
        }

        $stored = (string) Content::get($key);
        if ($type === 'list') {
            $decoded = json_decode($stored, true);
            return self::normalizeList(is_array($decoded) ? $decoded : $default, $field);
        }
        return $stored;
    }

    /**
     * Garante que todo item tenha todos os subcampos (como string).
     *
     * @param array<string,mixed>|null $field
     * @return array<int,array<string,string>>
     */
    private static function normalizeList(mixed $items, ?array $field): array
    {
        if (!is_array($items)) {
            return [];
        }
        $subKeys = array_map(static fn (array $f): string => $f['key'], $field['item'] ?? []);
        $out = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $row = [];
            foreach ($subKeys as $k) {
                $row[$k] = is_scalar($item[$k] ?? null) ? (string) $item[$k] : '';
            }
            $out[] = $row;
        }
        return $out;
    }

    /** Linha com *destaque* e **negrito**, sem links. */
    public static function rich(string $text): string
    {
        return nl2br(self::inline($text, false), false);
    }

    /** Parágrafos separados por linha em branco. */
    public static function paragraphs(string $text, string $class = ''): string
    {
        $text = trim(str_replace("\r\n", "\n", $text));
        if ($text === '') {
            return '';
        }
        $attr = $class !== '' ? ' class="' . e($class) . '"' : '';
        $html = '';
        foreach (preg_split('/\n\s*\n/', $text) ?: [] as $block) {
            $block = trim($block);
            if ($block !== '') {
                $html .= '<p' . $attr . '>' . nl2br(self::inline($block), false) . '</p>' . "\n";
            }
        }
        return $html;
    }

    /** Texto longo com títulos (## ), listas (- ) e parágrafos. */
    public static function markup(string $text, string $headingTag = 'h2'): string
    {
        $headingTag = in_array($headingTag, ['h2', 'h3', 'h4'], true) ? $headingTag : 'h2';
        $lines = explode("\n", str_replace("\r\n", "\n", $text));
        $html = '';
        $para = [];
        $list = [];

        $flushPara = static function () use (&$para, &$html): void {
            if ($para !== []) {
                $html .= '<p>' . implode('<br>', array_map(static fn ($l) => self::inline($l), $para)) . "</p>\n";
                $para = [];
            }
        };
        $flushList = static function () use (&$list, &$html): void {
            if ($list !== []) {
                $html .= "<ul>\n" . implode('', array_map(static fn ($l) => '<li>' . self::inline($l) . "</li>\n", $list)) . "</ul>\n";
                $list = [];
            }
        };

        foreach ($lines as $raw) {
            $line = trim($raw);
            if ($line === '') {
                $flushPara();
                $flushList();
                continue;
            }
            if (str_starts_with($line, '## ')) {
                $flushPara();
                $flushList();
                $html .= "<{$headingTag}>" . self::inline(substr($line, 3)) . "</{$headingTag}>\n";
                continue;
            }
            if (str_starts_with($line, '- ')) {
                $flushPara();
                $list[] = substr($line, 2);
                continue;
            }
            $flushList();
            $para[] = $line;
        }
        $flushPara();
        $flushList();
        return $html;
    }

    /** Escapa e aplica a formatação em linha. */
    public static function inline(string $text, bool $links = true): string
    {
        $html = e($text);

        if ($links) {
            $html = (string) preg_replace_callback('/\[([^\]\n]+)\]\(([^)\s]+)\)/u', static function (array $m): string {
                $url = html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');
                if (str_starts_with($url, '/')) {
                    $href = base_url(ltrim($url, '/'));
                    $external = false;
                } elseif (str_starts_with($url, '#') || preg_match('#^(https?://|mailto:|tel:)#i', $url)) {
                    $href = $url;
                    $external = (bool) preg_match('#^https?://#i', $url);
                } else {
                    return $m[1]; // esquema não permitido: mostra só o texto
                }
                return '<a href="' . e($href) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>' . $m[1] . '</a>';
            }, $html);
        }

        $html = (string) preg_replace('/\*\*([^*\n]+)\*\*/u', '<strong>$1</strong>', $html);
        $html = (string) preg_replace('/\*([^*\n]+)\*/u', '<em>$1</em>', $html);

        return strtr($html, self::placeholders());
    }

    /** Remove a marcação (para title, alt, e-mail em texto puro...). */
    public static function plain(string $text): string
    {
        $text = (string) preg_replace('/\[([^\]\n]+)\]\(([^)\s]+)\)/u', '$1', $text);
        $text = str_replace(['**', '*'], '', $text);
        $tokens = array_map(static fn (string $v): string => strip_tags(html_entity_decode($v, ENT_QUOTES, 'UTF-8')), self::placeholders());
        return strtr($text, $tokens);
    }

    /** @return array<string,string> códigos que o site preenche sozinho (já em HTML) */
    private static function placeholders(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
        $offices = offices();
        $phones = implode(' ou ', array_map(
            static fn (array $o): string => $o['phone'] . ' (' . mb_strtolower($o['label']) . ', ' . $o['city'] . ')',
            array_values($offices)
        ));
        $units = 'A DMR tem matriz em ' . $offices['matriz']['city']
            . ($offices['matriz']['address'] !== '' ? ' (' . $offices['matriz']['address'] . ')' : '')
            . (isset($offices['filial']) ? ' e filial em ' . $offices['filial']['city'] : '') . '.';

        return $map = [
            '{empresa}'   => e((string) setting('site_name', 'DMR Assessoria Imobiliária')),
            '{email}'     => '<a href="mailto:' . e($email) . '">' . e($email) . '</a>',
            '{telefones}' => e($phones),
            '{unidades}'  => e($units),
        ];
    }

    /**
     * URL de uma imagem: upload do painel ("content/...") ou asset padrão ("img/...").
     */
    public static function mediaUrl(string $path): string
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return '';
        }
        if (str_starts_with($path, 'content/')) {
            $file = PUBLIC_PATH . '/uploads/' . $path;
            $v = is_file($file) ? '?v=' . filemtime($file) : '';
            return base_url('uploads/' . $path) . $v;
        }
        return asset($path);
    }

    /** @return array{0:int,1:int}|null largura e altura reais da imagem */
    public static function mediaSize(string $path): ?array
    {
        $path = ltrim(trim($path), '/');
        if ($path === '' || str_ends_with(strtolower($path), '.svg')) {
            return null;
        }
        $file = str_starts_with($path, 'content/') ? PUBLIC_PATH . '/uploads/' . $path : PUBLIC_PATH . '/assets/' . $path;
        $info = is_file($file) ? @getimagesize($file) : false;
        return $info ? [(int) $info[0], (int) $info[1]] : null;
    }
}
