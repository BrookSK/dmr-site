-- Migration 004 — Tabela de contatos (leads do formulário da landing page).

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
