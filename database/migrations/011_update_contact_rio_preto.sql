-- Migration 011 — Contato oficial de São José do Rio Preto (pedido da cliente, 29/10/2026).
-- Atualiza telefone/WhatsApp apenas se ainda estiverem com o valor antigo ou vazios,
-- preservando um valor diferente que tenha sido definido pelo painel.
-- Cria a configuração de endereço comercial exibida no site.

UPDATE `settings`
SET `value` = '(17) 99639-4104'
WHERE `key_name` = 'contact_phone'
  AND (`value` = '(11) 98223-1363' OR `value` = '' OR `value` IS NULL);

UPDATE `settings`
SET `value` = '5517996394104'
WHERE `key_name` = 'whatsapp_number'
  AND (`value` = '5511982231363' OR `value` = '' OR `value` IS NULL);

INSERT INTO `settings` (`group_name`, `key_name`, `value`, `is_secret`) VALUES
    ('general', 'contact_address', 'Rua Romeu Strazzi, 325 – Sala 415, Vila Sinibaldi, São José do Rio Preto/SP – CEP 15084-010', 0)
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`);
