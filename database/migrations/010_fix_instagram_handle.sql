-- Migration 010 — Corrige o Instagram oficial da DMR.
-- O perfil oficial confirmado é @dmrassessoriaoficial
-- (https://www.instagram.com/dmrassessoriaoficial/). A migration 007 havia
-- trocado para "dmrassessoria"; aqui revertemos para o handle correto,
-- sem sobrescrever um valor personalizado diferente pelo painel.

UPDATE `settings`
SET `value` = 'dmrassessoriaoficial'
WHERE `key_name` = 'instagram'
  AND (`value` = 'dmrassessoria' OR `value` = '' OR `value` IS NULL);
