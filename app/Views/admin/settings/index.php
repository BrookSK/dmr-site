<?php
/** @var array $general */
/** @var array $smtp */
/** @var string|null $success */
/** @var string|null $error */
/** @var string $activeTab */
use App\Core\Csrf;
use App\Services\BrandLogoService;

$g = static fn (string $k): string => e($general[$k] ?? '');
$s = static fn (string $k): string => e($smtp[$k] ?? '');
$enc = $smtp['smtp_encryption'] ?? 'tls';
$hasPassword = !empty($smtp['smtp_password']);
?>
<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<div class="tabs" data-tabs>
    <div class="tabs__nav" role="tablist">
        <button class="tabs__btn<?= $activeTab === 'general' ? ' is-active' : '' ?>" data-tab="general" role="tab" type="button">Geral</button>
        <button class="tabs__btn<?= $activeTab === 'smtp' ? ' is-active' : '' ?>" data-tab="smtp" role="tab" type="button">SMTP / E-mail</button>
    </div>

    <!-- Aba Geral -->
    <div class="tabs__panel<?= $activeTab === 'general' ? ' is-active' : '' ?>" data-panel="general">
        <section class="panel panel--form">
            <form action="<?= e(base_url('admin/configuracoes/geral')) ?>" method="post" class="form" enctype="multipart/form-data">
                <?= Csrf::field() ?>
                <div class="form-group">
                    <label for="site_name">Nome do site</label>
                    <input type="text" id="site_name" name="site_name" value="<?= $g('site_name') ?>">
                </div>
                <div class="form-group">
                    <span class="form-label">Logos da marca</span>
                    <small class="muted">Quando cadastradas, substituem o texto “DMR Assessoria Imobiliária” no header, no footer e na área restrita.</small>
                </div>
                <div class="logo-fields">
                    <?php
                    $logoDark = BrandLogoService::url($general['site_logo'] ?? null);
                    $logoLight = BrandLogoService::url($general['site_logo_on_light'] ?? null);
                    ?>
                    <div class="logo-field">
                        <label for="site_logo">Logo para fundo escuro</label>
                        <small class="muted">Header, footer do site e menu do painel.</small>
                        <?php if ($logoDark): ?>
                            <div class="logo-preview logo-preview--dark">
                                <img src="<?= e($logoDark) ?>" alt="Logo atual (fundo escuro)">
                            </div>
                            <label class="checkbox checkbox--inline">
                                <input type="checkbox" name="remove_site_logo" value="1">
                                <span>Remover esta logo</span>
                            </label>
                        <?php endif; ?>
                        <input type="file" id="site_logo" name="site_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    </div>
                    <div class="logo-field">
                        <label for="site_logo_on_light">Logo para fundo claro</label>
                        <small class="muted">Tela de login. Se vazia, usa a logo de fundo escuro.</small>
                        <?php if ($logoLight): ?>
                            <div class="logo-preview logo-preview--light">
                                <img src="<?= e($logoLight) ?>" alt="Logo atual (fundo claro)">
                            </div>
                            <label class="checkbox checkbox--inline">
                                <input type="checkbox" name="remove_site_logo_on_light" value="1">
                                <span>Remover esta logo</span>
                            </label>
                        <?php endif; ?>
                        <input type="file" id="site_logo_on_light" name="site_logo_on_light" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    </div>
                </div>
                <div class="form-group">
                    <label for="site_url">URL base</label>
                    <input type="url" id="site_url" name="site_url" value="<?= $g('site_url') ?>" placeholder="https://www.dmrassessoria.com.br">
                    <small class="muted">Usada em links absolutos, sitemap e e-mails.</small>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_email">E-mail de contato</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?= $g('contact_email') ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_phone">Telefone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?= $g('contact_phone') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="whatsapp_number">WhatsApp (somente números)</label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= $g('whatsapp_number') ?>" placeholder="5511982231363">
                    </div>
                    <div class="form-group">
                        <label for="instagram">Instagram (sem @)</label>
                        <input type="text" id="instagram" name="instagram" value="<?= $g('instagram') ?>" placeholder="dmrassessoria">
                    </div>
                </div>
                <button type="submit" class="btn btn--primary">Salvar configurações gerais</button>
            </form>
        </section>
    </div>

    <!-- Aba SMTP -->
    <div class="tabs__panel<?= $activeTab === 'smtp' ? ' is-active' : '' ?>" data-panel="smtp">
        <section class="panel panel--form">
            <form action="<?= e(base_url('admin/configuracoes/smtp')) ?>" method="post" class="form">
                <?= Csrf::field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_host">Servidor (Host)</label>
                        <input type="text" id="smtp_host" name="smtp_host" value="<?= $s('smtp_host') ?>" placeholder="smtp.seudominio.com.br">
                    </div>
                    <div class="form-group form-group--sm">
                        <label for="smtp_port">Porta</label>
                        <input type="text" id="smtp_port" name="smtp_port" value="<?= $s('smtp_port') ?>" placeholder="587">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_username">Usuário</label>
                        <input type="text" id="smtp_username" name="smtp_username" value="<?= $s('smtp_username') ?>" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="smtp_password">Senha</label>
                        <input type="password" id="smtp_password" name="smtp_password" autocomplete="new-password" placeholder="<?= $hasPassword ? '•••••••• (preencha para alterar)' : '' ?>">
                        <small class="muted">Deixe em branco para manter a senha atual.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group form-group--sm">
                        <label for="smtp_encryption">Criptografia</label>
                        <select id="smtp_encryption" name="smtp_encryption">
                            <option value="tls" <?= $enc === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
                            <option value="ssl" <?= $enc === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= $enc === 'none' ? 'selected' : '' ?>>Nenhuma</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_name">Nome do remetente</label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?= $s('smtp_from_name') ?>">
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_email">E-mail do remetente</label>
                        <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?= $s('smtp_from_email') ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn--primary">Salvar SMTP</button>
            </form>
        </section>

        <section class="panel panel--form">
            <h2 class="panel-sub">Enviar e-mail de teste</h2>
            <p class="muted">Salve as configurações acima antes de testar. O teste usa as credenciais já salvas.</p>
            <form action="<?= e(base_url('admin/configuracoes/smtp/teste')) ?>" method="post" class="form form--inline">
                <?= Csrf::field() ?>
                <div class="form-group">
                    <label for="test_email">E-mail de destino</label>
                    <input type="email" id="test_email" name="test_email" required placeholder="voce@exemplo.com">
                </div>
                <button type="submit" class="btn btn--ghost">Enviar e-mail de teste</button>
            </form>
        </section>
    </div>
</div>
