<?php
/**
 * Cria (ou garante) o usuário SUPERADMIN inicial.
 *
 * A senha inicial pode ser informada por variável de ambiente DMR_SUPERADMIN_PASSWORD.
 * Se não informada, uma senha aleatória forte é gerada e exibida UMA ÚNICA VEZ
 * no terminal — anote-a. Em ambos os casos, recomenda-se trocá-la no primeiro acesso.
 *
 * E-mail inicial: definível por DMR_SUPERADMIN_EMAIL (padrão: admin@dmrassessoria.com.br).
 */

declare(strict_types=1);

function seedSuperadmin(PDO $pdo): void
{
    $email = getenv('DMR_SUPERADMIN_EMAIL') ?: 'admin@dmrassessoria.com.br';
    $email = strtolower(trim($email));

    $stmt = $pdo->prepare("SELECT id FROM `users` WHERE email = :e LIMIT 1");
    $stmt->execute([':e' => $email]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        echo "Superadmin já existe ({$email}). Nenhuma ação necessária.\n";
        ensureSuperadminRole($pdo, (int) $existing);
        return;
    }

    $envPassword = getenv('DMR_SUPERADMIN_PASSWORD');
    if ($envPassword && strlen($envPassword) >= 8) {
        $password = $envPassword;
        $generated = false;
    } else {
        $password = generateStrongPassword();
        $generated = true;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->prepare("
        INSERT INTO `users` (`name`, `email`, `password_hash`, `is_active`)
        VALUES (:n, :e, :p, 1)
    ")->execute([
        ':n' => 'Superadmin',
        ':e' => $email,
        ':p' => $hash,
    ]);

    $userId = (int) $pdo->lastInsertId();
    ensureSuperadminRole($pdo, $userId);

    echo "\n==================================================\n";
    echo "  SUPERADMIN criado com sucesso.\n";
    echo "  E-mail: {$email}\n";
    if ($generated) {
        echo "  Senha : {$password}\n";
        echo "  (senha gerada automaticamente — anote e troque após o login)\n";
    } else {
        echo "  Senha : (definida via DMR_SUPERADMIN_PASSWORD)\n";
    }
    echo "==================================================\n\n";
}

function ensureSuperadminRole(PDO $pdo, int $userId): void
{
    $roleId = $pdo->query("SELECT id FROM `roles` WHERE slug = 'superadmin' LIMIT 1")->fetchColumn();
    if (!$roleId) {
        return;
    }
    $pdo->prepare("
        INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`) VALUES (:u, :r)
    ")->execute([':u' => $userId, ':r' => (int) $roleId]);
}

function generateStrongPassword(int $length = 14): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789@#$%&*';
    $max = strlen($alphabet) - 1;
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $alphabet[random_int(0, $max)];
    }
    return $out;
}
