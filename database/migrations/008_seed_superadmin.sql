-- Migration 008 — Cria o usuário SUPERADMIN inicial (100% via SQL).
--
-- Esta migration permite instalar o sistema importando apenas arquivos .sql
-- (phpMyAdmin, MySQL Workbench, Adminer, HeidiSQL), sem rodar scripts no terminal.
--
-- Credenciais iniciais:
--   E-mail: admin@dmrassessoria.com.br
--   Senha : DmrAdmin@2026
--
-- IMPORTANTE (segurança):
--   1. O hash abaixo foi gerado com password_hash() do PHP (bcrypt, custo 12).
--   2. TROQUE a senha no primeiro acesso em /admin/perfil.
--   3. Em produção, recomenda-se também alterar o e-mail do superadmin.

-- Cria o usuário apenas se ainda não existir (idempotente).
INSERT INTO `users` (`name`, `email`, `password_hash`, `is_active`)
SELECT 'Superadmin', 'admin@dmrassessoria.com.br',
       '$2y$12$W49fdLv1reGUHU1h7O5DOeqPb/CZ0hsq709GeZ4x8UaYZDmR.VQHq', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `email` = 'admin@dmrassessoria.com.br'
);

-- Vincula o perfil "superadmin" ao usuário (idempotente).
INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id
FROM `users` u
JOIN `roles` r ON r.slug = 'superadmin'
WHERE u.email = 'admin@dmrassessoria.com.br'
  AND NOT EXISTS (
      SELECT 1 FROM `user_roles` ur
      WHERE ur.user_id = u.id AND ur.role_id = r.id
  );
