-- Migration 007 — Atualiza o handle padrão do Instagram.
-- O perfil correto da DMR é @dmrassessoria (o valor anterior era um placeholder).
-- Atualiza apenas se a configuração ainda estiver com o valor antigo, para não
-- sobrescrever uma personalização feita manualmente pelo painel.

UPDATE `settings`
SET `value` = 'dmrassessoria'
WHERE `key_name` = 'instagram'
  AND (`value` = 'dmrassessoriaoficial' OR `value` = '' OR `value` IS NULL);
