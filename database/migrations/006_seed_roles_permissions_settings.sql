-- Migration 006 — Seed inicial de perfis, permissões, vínculos e configurações.
-- Observação: o usuário SUPERADMIN é criado pelo runner de migrations (PHP),
-- pois sua senha precisa ser gerada com password_hash() de forma segura.

-- Perfis base ---------------------------------------------------------------
INSERT INTO `roles` (`slug`, `name`, `description`, `is_system`) VALUES
    ('superadmin', 'Superadmin',    'Acesso total ao sistema.', 1),
    ('admin',      'Administrador', 'Gerencia usuários e configurações.', 1),
    ('editor',     'Editor',        'Acesso de edição de conteúdo.', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Permissões ----------------------------------------------------------------
INSERT INTO `permissions` (`slug`, `name`, `description`) VALUES
    ('dashboard.view',  'Ver dashboard',           'Acessar o painel administrativo.'),
    ('users.manage',    'Gerenciar usuários',      'Criar, editar, ativar e excluir usuários.'),
    ('settings.manage', 'Gerenciar configurações', 'Editar configurações gerais e de SMTP.'),
    ('contacts.view',   'Ver contatos',            'Visualizar os leads recebidos pelo site.')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Superadmin recebe todas as permissões.
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.slug = 'superadmin'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- Administrador: tudo exceto, por padrão, nada bloqueado (ajustável depois).
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.slug IN ('dashboard.view', 'users.manage', 'settings.manage', 'contacts.view')
WHERE r.slug = 'admin'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- Editor: apenas dashboard e contatos.
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.slug IN ('dashboard.view', 'contacts.view')
WHERE r.slug = 'editor'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- Configurações padrão ------------------------------------------------------
INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'site_name',       'DMR Assessoria Imobiliária', 0),
    ('general', 'site_url',        '', 0),
    ('general', 'contact_email',   'contato@dmrassessoria.com.br', 0),
    ('general', 'contact_phone',   '(11) 98223-1363', 0),
    ('general', 'whatsapp_number', '5511982231363', 0),
    ('general', 'instagram',       'dmrassessoriaoficial', 0),
    ('smtp',    'smtp_host',       '', 0),
    ('smtp',    'smtp_port',       '587', 0),
    ('smtp',    'smtp_username',   '', 0),
    ('smtp',    'smtp_password',   '', 1),
    ('smtp',    'smtp_encryption', 'tls', 0),
    ('smtp',    'smtp_from_name',  'DMR Assessoria Imobiliária', 0),
    ('smtp',    'smtp_from_email', '', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);
