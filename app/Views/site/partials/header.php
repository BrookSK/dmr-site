<?php
$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5511982231363'));
?>
<?php $solidHeader = $solidHeader ?? false; ?>
<header class="site-header<?= $solidHeader ? ' site-header--solid' : '' ?>" id="siteHeader"<?= $solidHeader ? ' data-solid="1"' : '' ?>>
    <div class="container site-header__inner">
        <a class="brand" href="<?= e(base_url('')) ?>" aria-label="DMR Assessoria Imobiliária — página inicial">
            <span class="brand__mark">DMR</span>
            <span class="brand__sub">Assessoria Imobiliária</span>
        </a>

        <nav class="site-nav" id="siteNav" aria-label="Navegação principal">
            <a href="<?= e(base_url('#sobre')) ?>">A DMR</a>
            <a href="<?= e(base_url('#servicos')) ?>">Serviços</a>
            <a href="<?= e(base_url('#jornada')) ?>">A jornada</a>
            <a href="<?= e(base_url('#solucoes')) ?>">Soluções</a>
            <a href="<?= e(base_url('#home-equity')) ?>">Home Equity</a>
            <a href="<?= e(base_url('#contato')) ?>" class="site-nav__cta">Fale com a DMR</a>
        </nav>

        <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false" type="button">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
