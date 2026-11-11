-- Migration 014 — Conteúdo editável do site (textos e imagens pelo painel).
-- Cada linha guarda o valor de um campo definido em App\Services\ContentRegistry.
-- Campo sem linha aqui = o site usa o texto/imagem padrão do código.
-- Listas (depoimentos, perguntas, bancos...) são guardadas como JSON.
--
-- Também cria "notify_email": e-mail(s) que recebem os contatos do formulário
-- (vazio = usa o e-mail de contato).

CREATE TABLE IF NOT EXISTS `site_contents` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key_name`   VARCHAR(120) NOT NULL,
    `value`      MEDIUMTEXT NULL DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_site_contents_key` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('smtp', 'notify_email', '', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);
