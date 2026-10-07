<?php
/** @var array $users */
/** @var string|null $success */
/** @var string|null $error */
use App\Core\Csrf;
use App\Services\AuthService;
?>
<div class="page-actions">
    <a class="btn btn--primary" href="<?= e(base_url('admin/usuarios/novo')) ?>">+ Novo usuário</a>
</div>

<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<section class="panel">
    <table class="table">
        <thead>
        <tr>
            <th>Nome</th><th>E-mail</th><th>Perfis</th><th>Status</th><th>Último acesso</th><th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['name']) ?><?= (int)$u['id'] === AuthService::id() ? ' <span class="muted">(você)</span>' : '' ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['roles'] ?? '—') ?></td>
                <td>
                    <?php if ((int) $u['is_active'] === 1): ?>
                        <span class="pill pill--ok">Ativo</span>
                    <?php else: ?>
                        <span class="pill pill--warn">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="muted">
                    <?= $u['last_login_at'] ? e(date('d/m/Y H:i', strtotime($u['last_login_at']))) : 'nunca' ?>
                </td>
                <td class="table-actions">
                    <a class="btn btn--ghost btn--sm" href="<?= e(base_url('admin/usuarios/' . $u['id'] . '/editar')) ?>">Editar</a>
                    <?php if ((int) $u['id'] !== AuthService::id()): ?>
                        <form action="<?= e(base_url('admin/usuarios/' . $u['id'] . '/status')) ?>" method="post" class="inline">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn--ghost btn--sm">
                                <?= (int) $u['is_active'] === 1 ? 'Desativar' : 'Ativar' ?>
                            </button>
                        </form>
                        <form action="<?= e(base_url('admin/usuarios/' . $u['id'] . '/excluir')) ?>" method="post" class="inline" onsubmit="return confirm('Excluir este usuário? Esta ação não pode ser desfeita.');">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn--danger btn--sm">Excluir</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
