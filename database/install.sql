-- =============================================================================
-- DMR Assessoria Imobiliária — Instalação completa do banco de dados
-- =============================================================================
-- Arquivo único para importar via phpMyAdmin, MySQL Workbench, Adminer ou
-- HeidiSQL. NÃO exige rodar nada no terminal.
--
-- O que este arquivo faz:
--   1. Cria o banco de dados `dmr_site` (ajuste o nome se desejar).
--   2. Cria todas as tabelas.
--   3. Insere perfis, permissões, configurações padrão.
--   4. Cria o usuário SUPERADMIN inicial.
--
-- Credenciais iniciais do painel (/admin/login):
--   E-mail: admin@dmrassessoria.com.br
--   Senha : DmrAdmin@2026
--   >> TROQUE a senha no primeiro acesso em /admin/perfil <<
--
-- Observação: este arquivo é equivalente a aplicar todas as migrations de
-- database/migrations/ em ordem. Para evoluções futuras do schema, continue
-- criando novas migrations numeradas (nunca edite as já aplicadas).
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `dmr_site`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dmr_site`;

-- -----------------------------------------------------------------------------
-- Tabela de controle de migrations (para compatibilidade com o runner opcional)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `filename`   VARCHAR(255) NOT NULL,
    `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_migrations_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 001 — Usuários
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(150) NOT NULL,
    `email`           VARCHAR(190) NOT NULL,
    `password_hash`   VARCHAR(255) NOT NULL,
    `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at`   DATETIME NULL DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 002 — Perfis (roles), permissões e relacionamentos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(50) NOT NULL,
    `name`        VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL DEFAULT NULL,
    `is_system`   TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(80) NOT NULL,
    `name`        VARCHAR(120) NOT NULL,
    `description` VARCHAR(255) NULL DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permissions_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id`       INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `idx_rp_permission` (`permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`)
        REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`)
        REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_roles` (
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`),
    KEY `idx_ur_role` (`role_id`),
    CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`)
        REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 003 — Configurações (chave/valor)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_name` VARCHAR(50) NOT NULL DEFAULT 'general',
    `key_name`   VARCHAR(100) NOT NULL,
    `value`      TEXT NULL DEFAULT NULL,
    `is_secret`  TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`key_name`),
    KEY `idx_settings_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 004 — Contatos (leads do formulário)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(150) NOT NULL,
    `email`        VARCHAR(190) NOT NULL,
    `phone`        VARCHAR(40) NOT NULL,
    `audience`     VARCHAR(50) NOT NULL DEFAULT 'outro',
    `message`      TEXT NULL DEFAULT NULL,
    `status`       VARCHAR(20) NOT NULL DEFAULT 'novo',
    `ip_address`   VARCHAR(45) NULL DEFAULT NULL,
    `user_agent`   VARCHAR(255) NULL DEFAULT NULL,
    `email_sent`   TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_contacts_status` (`status`),
    KEY `idx_contacts_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 005 — Tokens de redefinição de senha
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at`    DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pr_user` (`user_id`),
    CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 006 — Seed de perfis, permissões e configurações
-- -----------------------------------------------------------------------------
INSERT INTO `roles` (`slug`, `name`, `description`, `is_system`) VALUES
    ('superadmin', 'Superadmin',    'Acesso total ao sistema.', 1),
    ('admin',      'Administrador', 'Gerencia usuários e configurações.', 1),
    ('editor',     'Editor',        'Acesso de edição de conteúdo.', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `permissions` (`slug`, `name`, `description`) VALUES
    ('dashboard.view',  'Ver dashboard',           'Acessar o painel administrativo.'),
    ('users.manage',    'Gerenciar usuários',      'Criar, editar, ativar e excluir usuários.'),
    ('settings.manage', 'Gerenciar configurações', 'Editar configurações gerais e de SMTP.'),
    ('contacts.view',   'Ver contatos',            'Visualizar os leads recebidos pelo site.')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r CROSS JOIN `permissions` p
WHERE r.slug = 'superadmin'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r
JOIN `permissions` p ON p.slug IN ('dashboard.view', 'users.manage', 'settings.manage', 'contacts.view')
WHERE r.slug = 'admin'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r
JOIN `permissions` p ON p.slug IN ('dashboard.view', 'contacts.view')
WHERE r.slug = 'editor'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'site_name',       'DMR Assessoria Imobiliária', 0),
    ('general', 'site_url',        '', 0),
    ('general', 'contact_email',   'contato@dmrassessoria.com.br', 0),
    ('general', 'contact_phone',   '(11) 98223-1363', 0),
    ('general', 'whatsapp_number', '5511982231363', 0),
    ('general', 'instagram',       'dmrassessoria', 0),
    ('smtp',    'smtp_host',       '', 0),
    ('smtp',    'smtp_port',       '587', 0),
    ('smtp',    'smtp_username',   '', 0),
    ('smtp',    'smtp_password',   '', 1),
    ('smtp',    'smtp_encryption', 'tls', 0),
    ('smtp',    'smtp_from_name',  'DMR Assessoria Imobiliária', 0),
    ('smtp',    'smtp_from_email', '', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);

-- -----------------------------------------------------------------------------
-- 008 — Usuário SUPERADMIN (senha: DmrAdmin@2026 — TROQUE no primeiro acesso)
-- -----------------------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password_hash`, `is_active`)
SELECT 'Superadmin', 'admin@dmrassessoria.com.br',
       '$2y$12$W49fdLv1reGUHU1h7O5DOeqPb/CZ0hsq709GeZ4x8UaYZDmR.VQHq', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `email` = 'admin@dmrassessoria.com.br'
);

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id
FROM `users` u
JOIN `roles` r ON r.slug = 'superadmin'
WHERE u.email = 'admin@dmrassessoria.com.br'
  AND NOT EXISTS (
      SELECT 1 FROM `user_roles` ur WHERE ur.user_id = u.id AND ur.role_id = r.id
  );

-- -----------------------------------------------------------------------------
-- Registra as migrations como aplicadas (mantém o runner opcional em sincronia)
-- -----------------------------------------------------------------------------
INSERT INTO `migrations` (`filename`) VALUES
    ('001_create_users_table.sql'),
    ('002_create_roles_and_permissions.sql'),
    ('003_create_settings_table.sql'),
    ('004_create_contacts_table.sql'),
    ('005_create_password_resets_table.sql'),
    ('006_seed_roles_permissions_settings.sql'),
    ('007_update_instagram_handle.sql'),
    ('008_seed_superadmin.sql')
ON DUPLICATE KEY UPDATE `filename` = VALUES(`filename`);
