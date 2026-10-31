<?php
/**
 * Configuração de conexão com o banco de dados.
 *
 * Os valores vêm do arquivo .env (modelo em .env.example).
 */
return [
    'host'    => $_ENV['DB_HOST'],
    'port'    => (int) $_ENV['DB_PORT'],
    'dbname'  => $_ENV['DB_NAME'],
    'user'    => $_ENV['DB_USER'],
    'pass'    => $_ENV['DB_PASS'],
    'charset' => 'utf8mb4',
];
