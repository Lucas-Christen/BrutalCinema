<?php
/**
 * Front Controller: ponto de entrada único da aplicação.
 *
 * Toda requisição chega aqui na forma index.php?page=X&acao=Y.
 * "page" escolhe o Controller e "acao" o método a ser executado.
 */
declare(strict_types=1);

$app = require __DIR__ . '/../config/app.php';
date_default_timezone_set($app['timezone']);

// Núcleo
require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Session.php';
require __DIR__ . '/../app/Core/Controller.php';
require __DIR__ . '/../app/Core/Model.php';
require __DIR__ . '/../app/Core/Validator.php';
require __DIR__ . '/../app/Core/Auth.php';

// Models
require __DIR__ . '/../app/Models/Usuario.php';

// Controllers
require __DIR__ . '/../app/Controllers/ErroController.php';
require __DIR__ . '/../app/Controllers/InicioController.php';
require __DIR__ . '/../app/Controllers/AuthController.php';

Session::iniciar();

// Mapa de páginas: valor de ?page= => classe do Controller
$rotas = [
    'inicio' => InicioController::class,
    'login'  => AuthController::class,
];

$page = $_GET['page'] ?? 'inicio';
$acao = $_GET['acao'] ?? 'index';

try {
    // Página desconhecida ou método inexistente => 404
    if (!isset($rotas[$page]) || !method_exists($rotas[$page], $acao)) {
        (new ErroController())->naoEncontrado();
        exit;
    }

    $controller = new $rotas[$page]();
    $controller->$acao();

} catch (Throwable $e) {
    error_log($e);
    $detalhe = $app['debug'] ? $e->getMessage() : '';
    (new ErroController())->interno($detalhe);
}
