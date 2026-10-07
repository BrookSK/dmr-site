<?php
/** @var int $totalUsers */
/** @var int $activeUsers */
/** @var int $totalContacts */
/** @var array $recentContacts */
/** @var bool $smtpConfigured */
/** @var string $siteName */
/** @var bool $canManageUsers */
/** @var bool $canManageSettings */
?>
<div class="cards">
    <div class="card card--stat">
        <span class="card__label">Usuários</span>
        <span class="card__value"><?= e($totalUsers) ?></span>
        <span class="card__hint"><?= e($activeUsers) ?> ativo(s)</span>
    </div>
    <div class="card card--stat">
        <span class="card__label">Contatos recebidos</span>
        <span class="card__value"><?= e($totalContacts) ?></span>
        <span class="card__hint">leads do site</span>
    </div>
    <div class="card card--stat">
        <span class="card__label">Status do SMTP</span>
        <span class="card__value card__value--sm">
            <?php if ($smtpConfigured): ?>
                <span class="pill pill--ok">Configurado</span>
            <?php else: ?>
                <span class="pill pill--warn">Pendente</span>
            <?php endif; ?>
        </span>
        <span class="card__hint">envio de e-mails</span>
    </div>
    <div class="card card--stat">
        <span class="card__label">Site</span>
        <span class="card__value card__value--sm"><?= e($siteName) ?></span>
        <span class="card__hint">identidade atual</span>
    </div>
</div>

<div class="grid-2">
    <section class="panel">
        <div class="panel__head">
            <h2>Contatos recentes</h2>
        </div>
        <?php if (empty($recentContacts)): ?>
            <p class="muted">Nenhum contato recebido ainda.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                <tr><th>Nome</th><th>Tipo</th><th>Contato</th><th>Data</th></tr>
                </thead>
                <tbody>
                <?php foreach ($recentContacts as $c): ?>
                    <tr>
                        <td><?= e($c['name']) ?></td>
                        <td><span class="tag"><?= e(ucfirst($c['audience'])) ?></span></td>
                        <td>
                            <div><?= e($c['email']) ?></div>
                            <div class="muted"><?= e($c['phone']) ?></div>
                        </td>
                        <td class="muted"><?= e(date('d/m/Y H:i', strtotime($c['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2>Atalhos</h2>
        </div>
        <div class="shortcuts">
            <?php if ($canManageUsers): ?>
                <a class="shortcut" href="<?= e(base_url('admin/usuarios')) ?>">
                    <span class="shortcut__ico">◍</span>
                    <span>Gerenciar usuários</span>
                </a>
            <?php endif; ?>
            <?php if ($canManageSettings): ?>
                <a class="shortcut" href="<?= e(base_url('admin/configuracoes')) ?>">
                    <span class="shortcut__ico">⚙</span>
                    <span>Configurações e SMTP</span>
                </a>
            <?php endif; ?>
            <a class="shortcut" href="<?= e(base_url('admin/perfil')) ?>">
                <span class="shortcut__ico">◉</span>
                <span>Alterar minha senha</span>
            </a>
            <a class="shortcut" href="<?= e(base_url('')) ?>" target="_blank" rel="noopener">
                <span class="shortcut__ico">↗</span>
                <span>Visualizar o site</span>
            </a>
        </div>
        <?php if (!$smtpConfigured && $canManageSettings): ?>
            <div class="alert alert--warn" style="margin-top:1rem;">
                O SMTP ainda não está configurado. O formulário de contato não conseguirá enviar e-mails até a configuração ser concluída.
                <a href="<?= e(base_url('admin/configuracoes')) ?>">Configurar agora</a>.
            </div>
        <?php endif; ?>
    </section>
</div>
