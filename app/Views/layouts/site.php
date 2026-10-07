<?php
/** @var string $content */
/** @var string $title */
/** @var string|null $description */
/** @var string|null $canonical */
/** @var string|null $ogImage */

$title = $title ?? 'DMR Assessoria Imobiliária';
$description = $description ?? 'Crédito imobiliário com acompanhamento do início ao fim.';
$canonical = $canonical ?? base_url('');
$ogImage = $ogImage ?? asset('img/og-image.svg');
$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5511982231363'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#000000">
    <meta name="author" content="<?= e(setting('site_name', 'DMR Assessoria Imobiliária')) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="<?= e(setting('site_name', 'DMR Assessoria Imobiliária')) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:alt" content="DMR Assessoria Imobiliária — crédito imobiliário com acompanhamento do início ao fim">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($description) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= e(asset('img/favicon.svg')) ?>">

    <!-- CSS crítico do site, com preload para renderização mais rápida -->
    <link rel="preload" as="style" href="<?= e(asset('css/site.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">

    <!-- Fontes carregadas de forma não bloqueante (não travam o first paint) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style"
          href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap">
    </noscript>

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FinancialService',
        '@id' => base_url('') . '#organization',
        'name' => setting('site_name', 'DMR Assessoria Imobiliária'),
        'alternateName' => 'DMR',
        'description' => $description,
        'url' => base_url(''),
        'logo' => asset('img/favicon.svg'),
        'image' => $ogImage,
        'telephone' => setting('contact_phone', '(11) 98223-1363'),
        'email' => setting('contact_email', 'contato@dmrassessoria.com.br'),
        'areaServed' => ['@type' => 'Country', 'name' => 'Brasil'],
        'knowsAbout' => [
            'Crédito imobiliário', 'Financiamento imobiliário', 'Repasse de financiamento',
            'Análise de crédito e risco', 'Análise jurídica', 'Saque de FGTS', 'Home Equity',
        ],
        'sameAs' => array_values(array_filter([
            setting('instagram') ? 'https://instagram.com/' . ltrim((string) setting('instagram'), '@') : null,
        ])),
        'makesOffer' => [
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Crédito Imobiliário']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Análise de Crédito e Risco']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Análise Jurídica']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Saque de FGTS']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Home Equity']],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
</head>
<body>
<a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

<?= \App\Core\View::renderPartial('site/partials/header') ?>

<main id="conteudo">
    <?= $content ?>
</main>

<?= \App\Core\View::renderPartial('site/partials/footer') ?>

<!-- Botão flutuante de WhatsApp -->
<a class="wa-float" href="https://wa.me/<?= e($whats) ?>?text=<?= rawurlencode('Olá, gostaria de falar com a DMR sobre crédito imobiliário.') ?>"
   target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
    <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false">
        <path fill="currentColor" d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.3-1.38a9.9 9.9 0 0 0 4.73 1.2h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.64-1.03-5.13-2.9-7A9.82 9.82 0 0 0 12.04 2Zm0 1.8c2.17 0 4.2.84 5.74 2.38a8.06 8.06 0 0 1 2.38 5.72c0 4.48-3.64 8.12-8.12 8.12a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-3.07.8.82-3-.19-.31a8.07 8.07 0 0 1-1.24-4.3c0-4.48 3.64-8.12 8.11-8.12Zm-2.84 4.3c-.14 0-.37.05-.56.26-.19.21-.73.72-.73 1.74 0 1.03.75 2.02.86 2.16.1.14 1.46 2.33 3.6 3.17 1.78.7 2.14.56 2.53.53.39-.04 1.25-.51 1.43-1 .18-.5.18-.92.13-1.01-.05-.09-.19-.14-.4-.25-.21-.1-1.25-.62-1.44-.69-.19-.07-.33-.1-.47.11-.14.21-.54.68-.66.82-.12.14-.24.16-.45.05-.21-.1-.9-.33-1.72-1.06-.63-.57-1.06-1.26-1.18-1.47-.12-.21-.01-.33.09-.43.09-.09.21-.24.31-.36.1-.12.14-.21.21-.35.07-.14.03-.26-.02-.37-.05-.1-.46-1.14-.64-1.56-.17-.4-.34-.35-.47-.36h-.4Z"/>
    </svg>
</a>

<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
