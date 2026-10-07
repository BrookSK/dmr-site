<?php
/** @var array|null $user */
/** @var string|null $success */
/** @var string|null $error */
use App\Core\Csrf;
?>
<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<section class="panel panel--form">
    <h2 class="panel-sub">Dados da conta</h2>
    <dl class="data-list">
        <div><dt>Nome</dt><dd><?= e($user['name'] ?? '') ?></dd></div>
        <div><dt>E-mail</dt><dd><?= e($user['email'] ?? '') ?></dd></div>
    </dl>
</section>

<section class="panel panel--form">
    <h2 class="panel-sub">Alterar senha</h2>
    <form action="<?= e(base_url('admin/perfil/senha')) ?>" method="post" class="form">
        <?= Csrf::field() ?>
        <div class="form-group">
            <label for="current_password">Senha atual</label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="new_password">Nova senha</label>
            <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
            <small class="muted">Mínimo de 8 caracteres.</small>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmar nova senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn--primary">Atualizar senha</button>
    </form>
</section>
