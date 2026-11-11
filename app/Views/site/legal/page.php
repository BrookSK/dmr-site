<?php
/**
 * Página legal genérica (Política de Privacidade / Termos de Uso).
 * Textos editáveis em Conteúdo do site. $prefix = 'privacy' ou 'terms'.
 *
 * @var string $prefix
 */
$updated = trim(ct_plain($prefix . '.updated'));
if ($updated === '') {
    $stamp = \App\Models\Content::updatedAt($prefix . '.body');
    $updated = $stamp ? date('d/m/Y', (int) strtotime($stamp)) : '10/11/2026';
}
$notice = ct_paragraphs($prefix . '.notice');
?>
<article class="legal">
    <div class="container">
        <header class="legal__header">
            <span class="eyebrow">Institucional</span>
            <h1 class="section__title"><?= ct($prefix . '.title') ?></h1>
            <p class="legal__updated">Última atualização: <?= e($updated) ?></p>
        </header>

        <?php if ($notice !== ''): ?>
            <div class="legal__notice"><?= $notice ?></div>
        <?php endif; ?>

        <div class="legal__content">
            <?= ct_markup($prefix . '.body', 'h2') ?>
        </div>
    </div>
</article>
