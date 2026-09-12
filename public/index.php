<?php
/**
 * Front Controller: ponto de entrada único da aplicação.
 * Toda requisição passa por aqui.
 */
declare(strict_types=1);

$app = require __DIR__ . '/../config/app.php';
date_default_timezone_set($app['timezone']);

require __DIR__ . '/../app/Core/Database.php';

// Teste temporário de conexão. Será substituído pelo roteamento no passo 3.
$db = Database::conectar();
$resultado = $db->query('SELECT COUNT(*) AS total FROM filmes');
$linha = $resultado->fetch_assoc();

echo '<h1>' . htmlspecialchars($app['nome']) . '</h1>';
echo '<p>Conexão OK. Filmes cadastrados: ' . (int) $linha['total'] . '</p>';
