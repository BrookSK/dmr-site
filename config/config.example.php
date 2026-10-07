<?php
/**
 * Configuração base da aplicação.
 *
 * Copie este arquivo para config/config.php e ajuste os valores de conexão
 * com o banco de dados do seu ambiente.
 *
 * IMPORTANTE: Conforme a arquitetura do projeto, NÃO utilizamos arquivo .env.
 * Apenas as credenciais de CONEXÃO com o banco ficam aqui (o mínimo necessário
 * para a aplicação subir). Todas as demais configurações (SMTP, nome do site,
 * contatos, etc.) são administráveis pelo painel e ficam armazenadas no banco
 * de dados, na tabela `settings`.
 */

return [
    // Ambiente: 'production' desativa a exibição de erros detalhados.
    'env' => 'development',

    // Conexão com o banco de dados (MySQL / MariaDB).
    'db' => [
        'driver'   => 'mysql',
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 'dmr_site',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // Segredo usado para hashing/HMAC auxiliares (ex.: tokens).
    // Gere um valor aleatório longo em produção.
    'app_key' => 'troque-este-valor-por-uma-string-aleatoria-longa',
];
