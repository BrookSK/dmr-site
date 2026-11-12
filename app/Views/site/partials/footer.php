<?php
$offices = offices();
$instagram = ltrim((string) setting('instagram', 'dmrassessoriaoficial'), '@');
$email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
$hasPurpose = trim((string) content('home.purpose.text')) !== '';
$year = date('Y');
$siteName = (string) setting('site_name', 'DMR Assessoria Imobiliária');
$footerLogo = brand_logo_url('dark');
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <?php if ($footerLogo): ?>
                <img class="brand__logo brand__logo--footer" src="<?= e($footerLogo) ?>" alt="<?= e($siteName) ?>">
            <?php else: ?>
                <span class="brand__mark brand__mark--light">DMR</span>
                <p class="site-footer__tagline"><?= ct('layout.footer.tagline') ?></p>
            <?php endif; ?>
            <p class="site-footer__desc"><?= ct('layout.footer.desc') ?></p>
        </div>

        <nav class="site-footer__col" aria-label="Links do site">
            <h3><?= ct('layout.footer.nav_title') ?></h3>
            <a href="<?= e(base_url('#para-voce')) ?>"><?= ct('layout.menu.b2c') ?></a>
            <a href="<?= e(base_url('#para-parceiros')) ?>"><?= ct('layout.menu.b2b') ?></a>
            <a href="<?= e(base_url('#como-funciona')) ?>"><?= ct('layout.menu.steps') ?></a>
            <a href="<?= e(base_url('#sobre')) ?>"><?= ct('layout.menu.about') ?></a>
            <a href="<?= e(base_url('#o-que-nos-orienta')) ?>"><?= $hasPurpose ? ct('home.purpose.eyebrow') : ct('layout.footer.nav_purpose') ?></a>
            <a href="<?= e(base_url('#contato')) ?>"><?= ct('layout.footer.nav_contact') ?></a>
        </nav>

        <div class="site-footer__col">
            <h3><?= ct('layout.footer.contact_title') ?></h3>
            <?php foreach ($offices as $office): ?>
                <div class="site-footer__office">
                    <span class="site-footer__office-label"><?= e($office['label']) ?> · <?= e($office['city']) ?></span>
                    <a href="https://wa.me/<?= e($office['whatsapp']) ?>" target="_blank" rel="noopener"><?= e($office['phone']) ?></a>
                    <?php if ($office['address'] !== ''): ?>
                        <address class="site-footer__address"><?= e($office['address']) ?></address>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <a class="site-footer__email" href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            <?php if ($instagram !== ''): ?>
                <a href="https://instagram.com/<?= e($instagram) ?>" target="_blank" rel="noopener">@<?= e($instagram) ?></a>
            <?php endif; ?>
        </div>

        <div class="site-footer__col">
            <h3><?= ct('layout.footer.legal_title') ?></h3>
            <a href="<?= e(base_url('politica-de-privacidade')) ?>"><?= ct('privacy.title') ?></a>
            <a href="<?= e(base_url('termos-de-uso')) ?>"><?= ct('terms.title') ?></a>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>&copy; <?= e($year) ?> <?= e($siteName) ?>. <?= ct('layout.footer.copyright') ?></p>
        <div class="site-footer__meta">
            <a class="site-footer__credit" href="https://lrvweb.com.br" target="_blank" rel="noopener">Desenvolvido por LRV Web</a>
            <a class="site-footer__restricted" href="<?= e(base_url('admin/login')) ?>">Área Restrita</a>
        </div>
    </div>
</footer>
