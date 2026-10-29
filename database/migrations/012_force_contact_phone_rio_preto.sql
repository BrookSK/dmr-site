-- Migration 012 — Força o telefone/WhatsApp oficial (17) 99639-4104.
-- A 011 só trocava se o valor ainda fosse o padrão antigo; aqui o número é
-- definido em qualquer caso, inclusive sobrescrevendo um valor do painel.

INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'contact_phone',   '(17) 99639-4104', 0),
    ('general', 'whatsapp_number', '5517996394104', 0)
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
