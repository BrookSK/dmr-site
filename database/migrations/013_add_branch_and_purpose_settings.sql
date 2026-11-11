-- Migration 013 — Filial São Paulo e texto de propósito (reunião de 10/11/2026).
-- Matriz: São José do Rio Preto (contact_phone / whatsapp_number / contact_address).
-- Filial: São Paulo/SP, sem endereço publicado, com o telefone antigo (11) 98223-1363.
-- purpose_text: quando preenchido no painel, substitui Missão, Visão e Valores
-- na home. Fica vazio até a equipe da DMR enviar o texto.

INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'branch_city',     'São Paulo/SP', 0),
    ('general', 'branch_phone',    '(11) 98223-1363', 0),
    ('general', 'branch_whatsapp', '5511982231363', 0),
    ('general', 'purpose_text',    '', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);
