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

if (!function_exists('lazy_img')) {
    /**
     * Gera uma tag <img> otimizada (lazy loading + decoding assíncrono).
     * Use para fotos/ilustrações adicionadas futuramente (atendimento, imóveis, equipe).
     *
     * @param string $src   caminho relativo dentro de assets/ (ex.: 'img/equipe.jpg')
     * @param string $alt   texto alternativo (obrigatório para acessibilidade/SEO)
     * @param array<string,string|int> $attrs atributos extras (width, height, class...)
     */
    function lazy_img(string $src, string $alt, array $attrs = []): string
    {
        $url = asset($src);
        $out = '<img src="' . e($url) . '" alt="' . e($alt) . '" loading="lazy" decoding="async"';
        foreach ($attrs as $key => $value) {
            $out .= ' ' . e($key) . '="' . e((string) $value) . '"';
        }
        return $out . '>';
    }
}

if (!function_exists('icon')) {
    /**
     * Retorna um ícone SVG inline (estilo linha, traço fino e profissional).
     * Herda a cor via currentColor e o tamanho via font-size/width do container.
     *
     * Ícones disponíveis: credito, analise, juridico, assessoria, fgts, repasse,
     * acompanhamento, bancos, cuidado, transparencia, agilidade, check.
     */
    function icon(string $name, int $size = 24): string
    {
        $paths = [
            // Crédito imobiliário — casa com cifrão
            'credito'       => '<path d="M3 10.5 12 4l9 6.5"/><path d="M5 9.5V20h14V9.5"/><path d="M12 11v6"/><path d="M13.5 12.3c-.4-.5-1-.6-1.6-.6-.8 0-1.4.4-1.4 1s.5.9 1.5 1.1 1.5.5 1.5 1.1-.6 1-1.4 1c-.6 0-1.2-.2-1.6-.6"/>',
            // Análise de crédito e risco — gráfico/escudo de verificação
            'analise'       => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 3-4 3 2 4-6"/>',
            // Análise jurídica — balança
            'juridico'      => '<path d="M12 3v18"/><path d="M7 21h10"/><path d="M5 7h14"/><path d="M9 7 6 13a3 3 0 0 0 6 0L9 7Z" opacity=".0"/><path d="M6 7l-2.5 5.5a2.8 2.8 0 0 0 5 0L6 7Z"/><path d="M18 7l-2.5 5.5a2.8 2.8 0 0 0 5 0L18 7Z"/>',
            // Assessoria imobiliária — aperto de mãos / pessoas
            'assessoria'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13A4 4 0 0 1 16 11"/>',
            // Saque de FGTS — carteira/dinheiro
            'fgts'          => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="16.5" cy="14.5" r="1.5"/>',
            // Secretaria de vendas e repasse — documentos/transferência
            'repasse'       => '<path d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7"/><path d="M9 3v4h4"/><path d="m16 13 4 0"/><path d="m18 11 2 2-2 2"/><path d="M7 11h4"/><path d="M7 15h3"/>',
            // Acompanhamento ponta a ponta — rota/jornada
            'acompanhamento'=> '<circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="5.5" r="2.5"/><path d="M8 18.5h6a4 4 0 0 0 0-8H9a4 4 0 0 1 0-8h2.5"/>',
            // Parcerias com bancos — colunas (instituição financeira)
            'bancos'        => '<path d="m3 9 9-6 9 6"/><path d="M4 9h16"/><path d="M5 9v9"/><path d="M9.5 9v9"/><path d="M14.5 9v9"/><path d="M19 9v9"/><path d="M3 18h18"/>',
            // Cuidado — coração/mão
            'cuidado'       => '<path d="M19 14c1.5-1.5 3-3.3 3-5.5A4.5 4.5 0 0 0 12 5.5 4.5 4.5 0 0 0 2 8.5c0 2.2 1.5 4 3 5.5l7 7Z"/>',
            // Transparência — olho
            'transparencia' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
            // Agilidade — raio
            'agilidade'     => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>',
            // Check genérico
            'check'         => '<path d="M20 6 9 17l-5-5"/>',

            // --- Painel administrativo ---
            'dashboard'     => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
            'usuarios'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13A4 4 0 0 1 16 11"/>',
            'config'        => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>',
            'perfil'        => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'externo'       => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        ];

        $body = $paths[$name] ?? $paths['check'];

        return sprintf(
            '<svg class="ico" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" '
            . 'stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" '
            . 'aria-hidden="true" focusable="false">%2$s</svg>',
            $size,
            $body
        );
    }
}
