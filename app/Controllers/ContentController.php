<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Content;
use App\Services\ContentRegistry;
use App\Services\ContentRenderer;
use App\Services\ContentUploadService;
use RuntimeException;

/**
 * Painel → Conteúdo do site: edição de todos os textos e imagens.
 *
 * Os campos são definidos em App\Services\ContentRegistry. Cada seção é salva
 * separadamente. "Restaurar padrão" apaga a personalização e o site volta ao
 * texto/imagem original.
 */
final class ContentController extends Controller
{
    /** Limites de tamanho por tipo (caracteres). */
    private const MAX_LEN = [
        'text' => 500, 'rich' => 500, 'textarea' => 3000,
        'paragraphs' => 15000, 'markup' => 60000,
        'icon' => 40, 'select' => 40, 'bool' => 1,
    ];
    private const MAX_LIST_ITEMS = 50;

    public function index(Request $request): void
    {
        $this->adminView('admin/content/index', [
            'title' => 'Conteúdo do site',
            'pages' => ContentRegistry::pages(),
        ]);
    }

    /** @param array<string,string> $params */
    public function edit(Request $request, array $params): void
    {
        $slug = $params['page'] ?? '';
        $pages = ContentRegistry::pages();
        if (!isset($pages[$slug])) {
            $this->redirect('/admin/conteudo');
        }

        $this->adminView('admin/content/edit', [
            'title'    => 'Conteúdo — ' . $pages[$slug]['title'],
            'slug'     => $slug,
            'page'     => $pages[$slug],
            'pages'    => $pages,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error'),
            'openSection' => Session::flash('open_section'),
        ]);
    }

    /** @param array<string,string> $params */
    public function save(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        [$slug, $sectionSlug, $section] = $this->resolve($params);

        $input = $request->input('f', []);
        $input = is_array($input) ? $input : [];
        $remove = $request->input('remove', []);
        $remove = is_array($remove) ? $remove : [];

        try {
            foreach ($section['fields'] as $field) {
                $key = $field['key'];
                switch ($field['type']) {
                    case 'image':
                        $this->saveImage($key, $this->uploadedFile(['files', $key]), !empty($remove[$key]));
                        break;
                    case 'list':
                        $this->saveList($field, is_array($input[$key] ?? null) ? $input[$key] : []);
                        break;
                    default:
                        $value = $input[$key] ?? null;
                        if (is_string($value)) {
                            Content::set($key, $this->clean($value, $field['type']));
                        }
                }
            }
        } catch (RuntimeException $e) {
            $this->back($slug, $sectionSlug, null, $e->getMessage());
        }

        $this->back($slug, $sectionSlug, '"' . $section['title'] . '" salvo. As alterações já estão no site.');
    }

    /** @param array<string,string> $params */
    public function restore(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        [$slug, $sectionSlug, $section] = $this->resolve($params);

        foreach ($section['fields'] as $field) {
            $key = $field['key'];
            if ($field['type'] === 'image') {
                ContentUploadService::delete(Content::get($key));
            } elseif ($field['type'] === 'list') {
                foreach ($this->imagesOf($field, ContentRenderer::value($key)) as $img) {
                    ContentUploadService::delete($img);
                }
            }
            Content::forget($key);
        }

        $this->back($slug, $sectionSlug, '"' . $section['title'] . '" voltou aos textos e imagens originais.');
    }

    // --- Internos ----------------------------------------------------------

    /**
     * @param array<string,string> $params
     * @return array{0:string,1:string,2:array<string,mixed>}
     */
    private function resolve(array $params): array
    {
        $slug = $params['page'] ?? '';
        $sectionSlug = $params['section'] ?? '';
        $pages = ContentRegistry::pages();
        $section = $pages[$slug]['sections'][$sectionSlug] ?? null;
        if ($section === null) {
            $this->redirect('/admin/conteudo');
        }
        return [$slug, $sectionSlug, $section];
    }

    private function back(string $slug, string $section, ?string $success, ?string $error = null): void
    {
        if ($success !== null) {
            Session::flash('success', $success);
        }
        if ($error !== null) {
            Session::flash('error', $error);
        }
        Session::flash('open_section', $section);
        $this->redirect('/admin/conteudo/' . $slug . '#sec-' . $section);
    }

    private function clean(string $value, string $type): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        // Remove caracteres de controle (exceto quebra de linha e tab).
        $value = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        $value = in_array($type, ['text', 'rich', 'icon', 'select'], true)
            ? trim((string) preg_replace('/\s+/u', ' ', $value))
            : trim($value);
        return mb_substr($value, 0, self::MAX_LEN[$type] ?? 3000);
    }

    /** @param array{name:string,type:string,tmp_name:string,error:int,size:int}|null $file */
    private function saveImage(string $key, ?array $file, bool $remove): void
    {
        $current = Content::get($key);
        if ($file !== null) {
            $stored = ContentUploadService::store($file, $key);
            Content::set($key, $stored);
            ContentUploadService::delete($current);
        } elseif ($remove) {
            ContentUploadService::delete($current);
            Content::forget($key); // volta à imagem padrão
        }
    }

    /**
     * @param array<string,mixed> $field
     * @param array<string,mixed> $rows
     */
    private function saveList(array $field, array $rows): void
    {
        $key = $field['key'];
        $old = ContentRenderer::value($key);
        $max = min((int) ($field['max'] ?? self::MAX_LIST_ITEMS), self::MAX_LIST_ITEMS);
        $items = [];

        foreach ($rows as $uid => $row) {
            if (!is_array($row) || count($items) >= $max) {
                continue;
            }
            $item = [];
            $hasContent = false;
            foreach ($field['item'] as $sub) {
                $sk = $sub['key'];
                if ($sub['type'] === 'image') {
                    $currentImg = is_string($row[$sk] ?? null) ? $this->safeMediaPath($row[$sk]) : '';
                    $file = $this->uploadedFile(['files', $key, (string) $uid, $sk]);
                    if ($file !== null) {
                        $currentImg = ContentUploadService::store($file, $key . '-' . $sk);
                    }
                    $item[$sk] = $currentImg;
                    $hasContent = $hasContent || $currentImg !== '';
                    continue;
                }
                if ($sub['type'] === 'bool') {
                    $item[$sk] = !empty($row[$sk]) ? '1' : '0';
                    continue;
                }
                $val = is_string($row[$sk] ?? null) ? $this->clean($row[$sk], $sub['type']) : '';
                if ($sub['type'] === 'icon' && !array_key_exists($val, ContentRegistry::ICONS)) {
                    $val = array_key_first(ContentRegistry::ICONS);
                }
                if ($sub['type'] === 'select' && !array_key_exists($val, $sub['options'] ?? [])) {
                    $val = (string) array_key_first($sub['options'] ?? ['' => '']);
                }
                $item[$sk] = $val;
                if (!in_array($sub['type'], ['icon', 'select'], true) && $val !== '') {
                    $hasContent = true;
                }
            }
            if ($hasContent) {
                $items[] = $item; // itens totalmente vazios são descartados
            }
        }

        Content::set($key, (string) json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // Apaga uploads que deixaram de ser usados na lista.
        $kept = $this->imagesOf($field, $items);
        foreach ($this->imagesOf($field, $old) as $img) {
            if (!in_array($img, $kept, true)) {
                ContentUploadService::delete($img);
            }
        }
    }

    /** Caminho de imagem vindo do formulário: só aceita upload do conteúdo ou asset padrão. */
    private function safeMediaPath(string $path): string
    {
        $path = ltrim(trim($path), '/');
        if ($path === '' || str_contains($path, '..')) {
            return '';
        }
        return preg_match('#^(content/|img/)[A-Za-z0-9._/-]+$#', $path) ? $path : '';
    }

    /**
     * @param array<string,mixed> $field
     * @return array<int,string>
     */
    private function imagesOf(array $field, mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }
        $imgKeys = array_map(static fn (array $s): string => $s['key'], array_filter($field['item'] ?? [], static fn (array $s): bool => $s['type'] === 'image'));
        $out = [];
        foreach ($items as $item) {
            foreach ($imgKeys as $k) {
                if (is_array($item) && is_string($item[$k] ?? null) && str_starts_with($item[$k], 'content/')) {
                    $out[] = $item[$k];
                }
            }
        }
        return $out;
    }

    /**
     * Lê um arquivo de $_FILES em campo aninhado (ex.: files[chave][uid][image]).
     *
     * @param array<int,string> $path
     * @return array{name:string,type:string,tmp_name:string,error:int,size:int}|null
     */
    private function uploadedFile(array $path): ?array
    {
        $root = array_shift($path);
        $files = $_FILES[$root] ?? null;
        if (!is_array($files)) {
            return null;
        }
        $out = [];
        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $prop) {
            $node = $files[$prop] ?? null;
            foreach ($path as $seg) {
                $node = is_array($node) ? ($node[$seg] ?? null) : null;
            }
            if (is_array($node)) {
                return null;
            }
            $out[$prop] = $node;
        }
        if ($out['tmp_name'] === null || (int) $out['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return [
            'name' => (string) $out['name'], 'type' => (string) $out['type'],
            'tmp_name' => (string) $out['tmp_name'], 'error' => (int) $out['error'], 'size' => (int) $out['size'],
        ];
    }
}
