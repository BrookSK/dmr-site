<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Abstração da requisição HTTP atual.
 */
final class Request
{
    private string $method;
    private string $path;
    /** @var array<string,mixed> */
    private array $query;
    /** @var array<string,mixed> */
    private array $post;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $url = $_GET['url'] ?? '';
        $url = is_string($url) ? trim($url, '/') : '';
        $this->path = $url === '' ? '/' : '/' . $url;

        $this->query = $_GET;
        $this->post  = $_POST;
    }

    public function method(): string
    {
        // Suporta _method para PUT/DELETE/PATCH via formulário.
        if ($this->method === 'POST' && isset($this->post['_method'])) {
            $override = strtoupper((string) $this->post['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Retorna uma string sanitizada (trim). Sanitização de saída é feita no escaping.
     */
    public function string(string $key, string $default = ''): string
    {
        $value = $this->input($key, $default);
        return is_string($value) ? trim($value) : $default;
    }

    public function csrfToken(): ?string
    {
        $token = $this->post['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        return is_string($token) ? $token : null;
    }

    /** @return array<string,mixed> */
    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    public function ip(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function userAgent(): string
    {
        return (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    }

    /**
     * Arquivo enviado via multipart. Null quando o campo não veio ou está vazio.
     *
     * @return array{name:string,type:string,tmp_name:string,error:int,size:int}|null
     */
    public function file(string $key): ?array
    {
        $file = $_FILES[$key] ?? null;
        if (!is_array($file) || !isset($file['error'], $file['tmp_name'])) {
            return null;
        }
        if ((int) $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return [
            'name'     => (string) ($file['name'] ?? ''),
            'type'     => (string) ($file['type'] ?? ''),
            'tmp_name' => (string) $file['tmp_name'],
            'error'    => (int) $file['error'],
            'size'     => (int) ($file['size'] ?? 0),
        ];
    }
}
