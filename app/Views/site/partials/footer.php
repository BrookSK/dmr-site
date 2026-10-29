<?php
$phone = (string) setting('contact_phone', '(17) 99639-4104');
$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5517996394104'));
$instagram = ltrim((string) setting('instagram', 'dmrassessoriaoficial'), '@');
$email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
$address = (string) setting('contact_address', 'Rua Romeu Strazzi, 325 – Sala 415, Vila Sinibaldi, São José do Rio Preto/SP – CEP 15084-010');
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
                <p class="site-footer__tagline">Assessoria Imobiliária</p>
            <?php endif; ?>
            <p class="site-footer__desc">Experiência que conecta pessoas, negócios e conquistas.</p>
        </div>

        <nav class="site-footer__col" aria-label="Links do site">
            <h3>Navegação</h3>
            <a href="<?= e(base_url('#para-voce')) ?>">Para você</a>
            <a href="<?= e(base_url('#para-parceiros')) ?>">Para parceiros</a>
            <a href="<?= e(base_url('#como-funciona')) ?>">Como funciona</a>
            <a href="<?= e(base_url('#sobre')) ?>">Sobre a DMR</a>
            <a href="<?= e(base_url('#o-que-nos-orienta')) ?>">O que nos orienta</a>
            <a href="<?= e(base_url('#contato')) ?>">Contato</a>
        </nav>

        <div class="site-footer__col">
            <h3>Contato</h3>
            <a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><?= e($phone) ?></a>
            <a class="site-footer__email" href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            <a href="https://instagram.com/<?= e($instagram) ?>" target="_blank" rel="noopener">@<?= e($instagram) ?></a>
            <?php if ($address !== ''): ?>
                <address class="site-footer__address"><?= e($address) ?></address>
            <?php endif; ?>
        </div>

        <div class="site-footer__col">
            <h3>Institucional</h3>
            <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>
            <a href="<?= e(base_url('termos-de-uso')) ?>">Termos de Uso</a>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>&copy; <?= e($year) ?> <?= e($siteName) ?>. Todos os direitos reservados.</p>
        <div class="site-footer__meta">
            <a class="site-footer__credit" href="https://lrvweb.com.br" target="_blank" rel="noopener">Desenvolvido por LRV Web</a>
            <a class="site-footer__restricted" href="<?= e(base_url('admin/login')) ?>">Área Restrita</a>
        </div>
    </div>
</footer>
