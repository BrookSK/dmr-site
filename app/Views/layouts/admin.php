<?php
/** @var string $content */
/** @var string $title */
use App\Core\Csrf;
use App\Services\AuthService;

$current = $_GET['url'] ?? '';
$isActive = static fn (string $prefix): string =>
    str_starts_with('/' . $current, $prefix) ? ' is-active' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Painel') ?> — DMR Admin</title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin">
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <span class="admin-brand__mark">DMR</span>
            <span class="admin-brand__text">Painel</span>
        </div>
        <nav class="admin-nav" aria-label="Navegação principal">
            <a class="admin-nav__link<?= $isActive('/admin') === ' is-active' && ($current === 'admin') ? ' is-active' : '' ?>" href="<?= e(base_url('admin')) ?>">
                <span class="admin-nav__ico">▦</span> Dashboard
            </a>
            <?php if (AuthService::can('users.manage')): ?>
            <a class="admin-nav__link<?= $isActive('/admin/usuarios') ?>" href="<?= e(base_url('admin/usuarios')) ?>">
                <span class="admin-nav__ico">◍</span> Usuários
            </a>
            <?php endif; ?>
            <?php if (AuthService::can('settings.manage')): ?>
            <a class="admin-nav__link<?= $isActive('/admin/configuracoes') ?>" href="<?= e(base_url('admin/configuracoes')) ?>">
                <span class="admin-nav__ico">⚙</span> Configurações
            </a>
            <?php endif; ?>
            <a class="admin-nav__link<?= $isActive('/admin/perfil') ?>" href="<?= e(base_url('admin/perfil')) ?>">
                <span class="admin-nav__ico">◉</span> Minha conta
            </a>
        </nav>
        <div class="admin-sidebar__foot">
            <a class="admin-nav__link" href="<?= e(base_url('')) ?>" target="_blank" rel="noopener">↗ Ver o site</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-burger" id="adminBurger" aria-label="Abrir menu" type="button">☰</button>
            <h1 class="admin-topbar__title"><?= e($title ?? 'Painel') ?></h1>
            <div class="admin-user">
                <span class="admin-user__name"><?= e(AuthService::user()['name'] ?? 'Usuário') ?></span>
                <form action="<?= e(base_url('admin/logout')) ?>" method="post" class="admin-logout">
                    <?= Csrf::field() ?>
                    <button type="submit" class="btn btn--ghost btn--sm">Sair</button>
                </form>
            </div>
        </header>

        <main class="admin-content">
            <?= $content ?>
        </main>
    </div>
</div>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
