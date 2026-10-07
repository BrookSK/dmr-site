<?php
$phone = (string) setting('contact_phone', '(11) 98223-1363');
$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5511982231363'));
$instagram = ltrim((string) setting('instagram', 'dmrassessoriaoficial'), '@');
$email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
$year = date('Y');
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <span class="brand__mark brand__mark--light">DMR</span>
            <p class="site-footer__tagline">Assessoria Imobiliária</p>
            <p class="site-footer__desc">Crédito imobiliário com acompanhamento próximo, do primeiro passo até a entrega das chaves.</p>
        </div>

        <nav class="site-footer__col" aria-label="Links do site">
            <h3>Navegação</h3>
            <a href="<?= e(base_url('#sobre')) ?>">A DMR</a>
            <a href="<?= e(base_url('#pilares')) ?>">Diferenciais</a>
            <a href="<?= e(base_url('#jornada')) ?>">A jornada</a>
            <a href="<?= e(base_url('#solucoes')) ?>">Soluções</a>
            <a href="<?= e(base_url('#home-equity')) ?>">Home Equity</a>
            <a href="<?= e(base_url('#contato')) ?>">Contato</a>
        </nav>

        <div class="site-footer__col">
            <h3>Contato</h3>
            <a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><?= e($phone) ?></a>
            <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            <a href="https://instagram.com/<?= e($instagram) ?>" target="_blank" rel="noopener">@<?= e($instagram) ?></a>
        </div>

        <div class="site-footer__col">
            <h3>Institucional</h3>
            <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>
            <a href="<?= e(base_url('termos-de-uso')) ?>">Termos de Uso</a>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>&copy; <?= e($year) ?> DMR Assessoria Imobiliária. Todos os direitos reservados.</p>
        <a class="site-footer__restricted" href="<?= e(base_url('admin/login')) ?>">Área Restrita</a>
    </div>
</footer>
