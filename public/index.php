<?php
/**
 * Front Controller — ponto de entrada da aplicação.
 *
 * Todas as requisições (que não sejam arquivos físicos) são roteadas para cá
 * pelo .htaccess da raiz, que repassa a URL via parâmetro ?url=.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', __DIR__);

// --- Carrega configuração base ---------------------------------------------
$configFile = CONFIG_PATH . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Arquivo de configuração ausente. Copie config/config.example.php para config/config.php.');
}
$config = require $configFile;

// --- Tratamento de erros conforme ambiente ---------------------------------
if (($config['env'] ?? 'production') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// --- Autoloader PSR-4 do app ------------------------------------------------
require APP_PATH . '/Core/Autoloader.php';
App\Core\Autoloader::register([
    'App\\' => APP_PATH,
]);

// Composer (opcional — usado pelo PHPMailer se instalado)
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

// --- Inicializa a aplicação -------------------------------------------------
use App\Core\App;

$app = new App($config);
$app->run();
