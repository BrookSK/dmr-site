<?php
/** @var string $title */
/** @var string|null $error */
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Entrar') ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin admin--auth">
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <span class="login-brand__mark">DMR</span>
            <span class="login-brand__sub">Assessoria Imobiliária</span>
        </div>
        <h1 class="login-title">Acesso ao painel</h1>
        <p class="login-desc">Entre com suas credenciais para continuar.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert--error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="<?= e(base_url('admin/login')) ?>" method="post" class="form" novalidate>
            <?= Csrf::field() ?>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required autocomplete="username" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn--primary btn--block">Entrar</button>
        </form>

        <a class="login-back" href="<?= e(base_url('')) ?>">← Voltar ao site</a>
    </div>
</div>
</body>
</html>
