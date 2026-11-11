<?php
/** @var array{q:string,audience:string,status:string} $filters */
/** @var array{items:array,total:int,pages:int,page:int} $result */
/** @var int $newCount */
/** @var string|null $success */

use App\Models\Contact;

$query = static function (array $override) use ($filters): string {
    $params = array_filter([
        'q' => $filters['q'], 'tipo' => $filters['audience'], 'status' => $filters['status'],
    ] + $override, static fn ($v) => $v !== '' && $v !== null && $v !== 1);
    return base_url('admin/contatos') . ($params ? '?' . http_build_query($params) : '');
};
$hasFilter = $filters['q'] !== '' || $filters['audience'] !== '' || $filters['status'] !== '';
$pillClass = ['novo' => 'pill--warn', 'em_andamento' => 'pill--info', 'concluido' => 'pill--ok'];
?>
<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>

<form class="panel leads-filter" method="get" action="<?= e(base_url('admin/contatos')) ?>">
    <div class="form-group leads-filter__q">
        <label for="q">Buscar</label>
        <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Nome, e-mail, telefone ou texto da mensagem">
    </div>
    <div class="form-group">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo">
            <option value="">Todos</option>
            <option value="cliente"<?= $filters['audience'] === 'cliente' ? ' selected' : '' ?>>Compradores</option>
            <option value="parceiro"<?= $filters['audience'] === 'parceiro' ? ' selected' : '' ?>>Parceiros (todos)</option>
            <?php foreach (['corretor', 'imobiliaria', 'construtora'] as $a): ?>
                <option value="<?= e($a) ?>"<?= $filters['audience'] === $a ? ' selected' : '' ?>>— <?= e(Contact::audienceLabel($a)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="status">Situação</label>
        <select id="status" name="status">
            <option value="">Todas</option>
            <?php foreach (Contact::STATUSES as $v => $l): ?>
                <option value="<?= e($v) ?>"<?= $filters['status'] === $v ? ' selected' : '' ?>><?= e($l) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="leads-filter__actions">
        <button type="submit" class="btn btn--primary">Filtrar</button>
        <?php if ($hasFilter): ?><a class="btn btn--ghost" href="<?= e(base_url('admin/contatos')) ?>">Limpar</a><?php endif; ?>
    </div>
</form>

<p class="muted leads-summary">
    <?= $result['total'] ?> <?= $result['total'] === 1 ? 'contato encontrado' : 'contatos encontrados' ?>
    <?php if ($newCount > 0): ?> · <a href="<?= e(base_url('admin/contatos?status=novo')) ?>"><?= $newCount ?> <?= $newCount === 1 ? 'novo' : 'novos' ?> aguardando atendimento</a><?php endif; ?>
</p>

<section class="panel">
    <?php if ($result['items'] === []): ?>
        <p class="muted"><?= $hasFilter ? 'Nenhum contato com esses filtros.' : 'Nenhum contato recebido ainda. Os envios do formulário do site aparecem aqui.' ?></p>
    <?php else: ?>
        <table class="table leads-table">
            <thead>
            <tr><th>Data</th><th>Nome</th><th>Tipo / interesse</th><th>Contato</th><th>Situação</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($result['items'] as $c):
                $parsed = Contact::parseMessage($c['message'] ?? '');
                $url = base_url('admin/contatos/' . $c['id']);
            ?>
                <tr class="<?= $c['status'] === 'novo' ? 'is-new' : '' ?>">
                    <td class="muted" data-label="Data"><?= e(date('d/m/Y H:i', (int) strtotime((string) $c['created_at']))) ?></td>
                    <td data-label="Nome"><a class="leads-table__name" href="<?= e($url) ?>"><?= e($c['name']) ?></a></td>
                    <td data-label="Tipo">
                        <span class="tag"><?= e(Contact::audienceLabel((string) $c['audience'])) ?></span>
                        <?php if (!empty($parsed['details']['Interesse'])): ?><div class="muted"><?= e($parsed['details']['Interesse']) ?></div><?php endif; ?>
                    </td>
                    <td data-label="Contato">
                        <div><?= e($c['phone']) ?></div>
                        <div class="muted leads-table__email"><?= e($c['email']) ?></div>
                    </td>
                    <td data-label="Situação"><span class="pill <?= e($pillClass[$c['status']] ?? '') ?>"><?= e(Contact::statusLabel((string) $c['status'])) ?></span></td>
                    <td class="table-actions"><a class="btn btn--ghost btn--sm" href="<?= e($url) ?>">Ver detalhes</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($result['pages'] > 1): ?>
        <nav class="pager" aria-label="Páginas">
            <?php if ($result['page'] > 1): ?><a class="btn btn--ghost btn--sm" href="<?= e($query(['pagina' => $result['page'] - 1])) ?>">← Anteriores</a><?php endif; ?>
            <span class="muted">Página <?= $result['page'] ?> de <?= $result['pages'] ?></span>
            <?php if ($result['page'] < $result['pages']): ?><a class="btn btn--ghost btn--sm" href="<?= e($query(['pagina' => $result['page'] + 1])) ?>">Mais antigos →</a><?php endif; ?>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
