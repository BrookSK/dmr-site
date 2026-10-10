-- Migration 009 — Logos da marca (fundo escuro e fundo claro).
INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'site_logo', '', 0),
    ('general', 'site_logo_on_light', '', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);
