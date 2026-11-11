<?php
/** @var array<string,mixed> $lead */
/** @var array{details:array<string,string>,message:string} $parsed */
/** @var int|null $newerId */
/** @var int|null $olderId */
/** @var string|null $success */

use App\Core\Csrf;
use App\Models\Contact;

$digits = (string) preg_replace('/\D+/', '', (string) $lead['phone']);
if (strlen($digits) === 10 || strlen($digits) === 11) {
    $digits = '55' . $digits;
}
$firstName = trim(explode(' ', trim((string) $lead['name']))[0] ?? '');
$waUrl = 'https://wa.me/' . $digits . '?text=' . rawurlencode('Olá, ' . $firstName . '! Aqui é da DMR Assessoria Imobiliária. Recebemos seu contato pelo site.');
$mailUrl = 'mailto:' . $lead['email'] . '?subject=' . rawurlencode('DMR Assessoria Imobiliária — seu contato pelo site');
$isPartner = in_array($lead['audience'], ['corretor', 'imobiliaria', 'construtora', 'incorporadora'], true);
?>
<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>

<div class="lead-nav">
    <a class="btn btn--ghost btn--sm" href="<?= e(base_url('admin/contatos')) ?>">← Todos os contatos</a>
    <span class="lead-nav__arrows">
        <?php if ($newerId): ?><a class="btn btn--ghost btn--sm" href="<?= e(base_url('admin/contatos/' . $newerId)) ?>">← Mais recente</a><?php endif; ?>
        <?php if ($olderId): ?><a class="btn btn--ghost btn--sm" href="<?= e(base_url('admin/contatos/' . $olderId)) ?>">Mais antigo →</a><?php endif; ?>
    </span>
</div>

<div class="grid-2 lead-grid">
    <section class="panel">
        <div class="panel__head">
            <h2><?= e($lead['name']) ?></h2>
            <span class="tag"><?= $isPartner ? 'Sou parceiro' : 'Quero financiar' ?></span>
        </div>
        <dl class="lead-data">
            <div><dt>Recebido em</dt><dd><?= e(date('d/m/Y \à\s H:i', (int) strtotime((string) $lead['created_at']))) ?></dd></div>
            <div><dt>WhatsApp</dt><dd><a href="<?= e($waUrl) ?>" target="_blank" rel="noopener"><?= e($lead['phone']) ?></a></dd></div>
            <div><dt>E-mail</dt><dd><a href="<?= e($mailUrl) ?>"><?= e($lead['email']) ?></a></dd></div>
            <div><dt>Perfil</dt><dd><?= e(Contact::audienceLabel((string) $lead['audience'])) ?></dd></div>
            <?php foreach ($parsed['details'] as $label => $value): ?>
                <div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
            <?php endforeach; ?>
        </dl>

        <h3 class="lead-subtitle">Mensagem</h3>
        <?php if ($parsed['message'] !== ''): ?>
            <div class="lead-message"><?= nl2br(e($parsed['message']), false) ?></div>
        <?php else: ?>
            <p class="muted"><em>A pessoa não escreveu mensagem.</em></p>
        <?php endif; ?>

        <div class="lead-actions">
            <a class="btn btn--primary" href="<?= e($waUrl) ?>" target="_blank" rel="noopener">Responder no WhatsApp</a>
            <a class="btn btn--ghost" href="<?= e($mailUrl) ?>">Responder por e-mail</a>
        </div>
    </section>

    <aside>
        <section class="panel">
            <div class="panel__head"><h2>Situação do atendimento</h2></div>
            <form action="<?= e(base_url('admin/contatos/' . $lead['id'] . '/status')) ?>" method="post" class="lead-status">
                <?= Csrf::field() ?>
                <?php foreach (Contact::STATUSES as $v => $l): ?>
                    <button type="submit" name="status" value="<?= e($v) ?>" class="btn <?= $lead['status'] === $v ? 'btn--primary' : 'btn--ghost' ?>"<?= $lead['status'] === $v ? ' aria-pressed="true"' : '' ?>><?= e($l) ?></button>
                <?php endforeach; ?>
            </form>
            <p class="muted lead-hint">Marque como "Em atendimento" quando alguém assumir e "Concluído" ao finalizar. Ajuda a equipe a não esquecer ninguém.</p>
        </section>

        <section class="panel">
            <div class="panel__head"><h2>Envio</h2></div>
            <dl class="lead-data lead-data--sm">
                <div><dt>E-mail de aviso</dt><dd>
                    <?php if ((int) $lead['email_sent'] === 1): ?>
                        <span class="pill pill--ok">Enviado</span>
                    <?php else: ?>
                        <span class="pill pill--warn">Não enviado</span>
                    <?php endif; ?>
                </dd></div>
                <div><dt>Código</dt><dd>#<?= (int) $lead['id'] ?></dd></div>
                <?php if (!empty($lead['ip_address'])): ?><div><dt>IP</dt><dd><?= e($lead['ip_address']) ?></dd></div><?php endif; ?>
                <?php if (!empty($lead['user_agent'])): ?><div><dt>Navegador</dt><dd class="lead-ua"><?= e($lead['user_agent']) ?></dd></div><?php endif; ?>
            </dl>
            <?php if ((int) $lead['email_sent'] !== 1): ?>
                <p class="muted lead-hint">O aviso por e-mail falhou ou o SMTP não estava configurado no momento do envio. O contato foi salvo normalmente.</p>
            <?php endif; ?>
        </section>
    </aside>
</div>
