<?php
/** @var array|null $user */
/** @var array $roles */
/** @var array $userRoles */
/** @var string|null $error */
use App\Core\Csrf;

$isEdit = $user !== null;
$action = $isEdit ? base_url('admin/usuarios/' . $user['id']) : base_url('admin/usuarios');
?>
<div class="page-actions">
    <a class="btn btn--ghost" href="<?= e(base_url('admin/usuarios')) ?>">← Voltar</a>
</div>

<?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<section class="panel panel--form">
    <form action="<?= e($action) ?>" method="post" class="form">
        <?= Csrf::field() ?>

        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" required value="<?= e($user['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required value="<?= e($user['email'] ?? '') ?>">
        </div>

        <?php if (!$isEdit): ?>
        <div class="form-group">
            <label for="password">Senha inicial</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <small class="muted">Mínimo de 8 caracteres.</small>
        </div>
        <?php endif; ?>

        <fieldset class="form-group">
            <legend>Perfis de acesso</legend>
            <div class="checkbox-grid">
                <?php foreach ($roles as $role): ?>
                    <label class="checkbox">
                        <input type="checkbox" name="roles[]" value="<?= e($role['id']) ?>"
                            <?= in_array((int) $role['id'], $userRoles, true) ? 'checked' : '' ?>>
                        <span>
                            <strong><?= e($role['name']) ?></strong>
                            <?php if (!empty($role['description'])): ?>
                                <small class="muted"><?= e($role['description']) ?></small>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <small class="muted">As permissões são aplicadas conforme os perfis selecionados.</small>
        </fieldset>

        <div class="form-group">
            <label class="checkbox checkbox--inline">
                <input type="checkbox" name="is_active" value="1" <?= ($user === null || (int) $user['is_active'] === 1) ? 'checked' : '' ?>>
                <span>Usuário ativo</span>
            </label>
        </div>

        <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Salvar alterações' : 'Criar usuário' ?></button>
    </form>
</section>

<?php if ($isEdit): ?>
<section class="panel panel--form">
    <h2 class="panel-sub">Redefinir senha</h2>
    <form action="<?= e(base_url('admin/usuarios/' . $user['id'] . '/senha')) ?>" method="post" class="form">
        <?= Csrf::field() ?>
        <div class="form-group">
            <label for="reset_password">Nova senha</label>
            <input type="password" id="reset_password" name="password" required minlength="8" autocomplete="new-password">
            <small class="muted">Mínimo de 8 caracteres.</small>
        </div>
        <button type="submit" class="btn btn--ghost">Redefinir senha</button>
    </form>
</section>
<?php endif; ?>
