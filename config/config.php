<?php
/**
 * Configuração local (ambiente de desenvolvimento).
 * Em produção, ajuste as credenciais e defina 'env' => 'production'.
 *
 */

return [
    'env' => 'development',

    'db' => [
        'driver'   => 'mysql',
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 'dmr_site',
        'username' => 'dmr_site',
        'password' => '3RiAywbAt3tf_z0*',
        'charset'  => 'utf8mb4',
    ],

    'app_key' => 'dmr-dev-key-troque-em-producao-'.'a1b2c3d4e5f6',
];
