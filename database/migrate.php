<?php
/**
 * Runner de migrations.
 *
 * Uso (a partir da raiz do projeto):
 *   php database/migrate.php            Aplica as migrations pendentes.
 *   php database/migrate.php --fresh    (CUIDADO) Dropa e recria o schema.
 *
 * Após aplicar as migrations, cria o usuário SUPERADMIN caso ainda não exista.
 * As migrations são aplicadas em ordem alfabética do nome do arquivo e nunca
 * são reaplicadas (controle via tabela `migrations`).
 *
 * REGRA: nunca edite uma migration já aplicada. Crie uma nova com número maior.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Este script deve ser executado via linha de comando (CLI).');
}

define('BASE_PATH', dirname(__DIR__));

$configFile = BASE_PATH . '/config/config.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "Arquivo config/config.php não encontrado. Copie de config.example.php.\n");
    exit(1);
}
$config = require $configFile;
$dbc = $config['db'];

$dsn = sprintf(
    '%s:host=%s;port=%d;charset=%s',
    $dbc['driver'] ?? 'mysql',
    $dbc['host'] ?? '127.0.0.1',
    (int) ($dbc['port'] ?? 3306),
    $dbc['charset'] ?? 'utf8mb4'
);

try {
    $pdo = new PDO($dsn, $dbc['username'] ?? '', $dbc['password'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, 'Falha ao conectar ao servidor de banco: ' . $e->getMessage() . "\n");
    exit(1);
}

$database = $dbc['database'];
$fresh = in_array('--fresh', $argv, true);

if ($fresh) {
    echo "Modo --fresh: recriando o banco '{$database}'...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `{$database}`");
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$database}`");

// Tabela de controle de migrations.
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `filename`   VARCHAR(255) NOT NULL,
        `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_migrations_filename` (`filename`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$applied = $pdo->query("SELECT filename FROM `migrations`")->fetchAll(PDO::FETCH_COLUMN);
$applied = array_flip($applied);

$files = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];
sort($files, SORT_STRING);

$ran = 0;
foreach ($files as $file) {
    $name = basename($file);
    if (isset($applied[$name])) {
        continue;
    }

    echo "Aplicando {$name}... ";
    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        echo "vazio, ignorado.\n";
        continue;
    }

    try {
        $pdo->exec($sql);
        $stmt = $pdo->prepare("INSERT INTO `migrations` (`filename`) VALUES (:f)");
        $stmt->execute([':f' => $name]);
        echo "ok\n";
        $ran++;
    } catch (PDOException $e) {
        echo "FALHOU\n";
        fwrite(STDERR, "Erro em {$name}: " . $e->getMessage() . "\n");
        exit(1);
    }
}

echo $ran === 0 ? "Nenhuma migration pendente.\n" : "{$ran} migration(s) aplicada(s).\n";

// --- Criação do SUPERADMIN --------------------------------------------------
require BASE_PATH . '/database/seed_superadmin.php';
seedSuperadmin($pdo);

echo "Concluído.\n";
