<?php
/** @var array<string,array<string,mixed>> $pages */
?>
<p class="muted content-intro">Escolha o que quer editar. Cada seção é salva separadamente e aparece no site na hora. Se algo ficar estranho, use <strong>Restaurar padrão</strong> na seção para voltar ao texto original.</p>

<div class="content-pages">
    <?php foreach ($pages as $slug => $page): ?>
        <a class="content-page" href="<?= e(base_url('admin/conteudo/' . $slug)) ?>">
            <span class="content-page__title"><?= e($page['title']) ?></span>
            <span class="content-page__desc"><?= e($page['description']) ?></span>
            <span class="content-page__meta"><?= count($page['sections']) ?> <?= count($page['sections']) === 1 ? 'seção' : 'seções' ?> →</span>
        </a>
    <?php endforeach; ?>
</div>

<p class="muted content-intro">Telefones, endereço, Instagram, logo e e-mails ficam em <a href="<?= e(base_url('admin/configuracoes')) ?>">Configurações</a>.</p>
