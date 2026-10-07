-- Migration 003 — Tabela de configurações (chave/valor).
-- Armazena configurações gerais e de SMTP, substituindo o uso de arquivo .env.
-- Valores sensíveis (ex.: senha SMTP) são marcados com is_secret = 1 e
-- criptografados pela aplicação antes da persistência.

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
