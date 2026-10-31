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
require __DIR__ . '/../app/Models/Filme.php';
require __DIR__ . '/../app/Models/Sala.php';
require __DIR__ . '/../app/Models/Sessao.php';
require __DIR__ . '/../app/Models/Ingresso.php';

// Controllers
require __DIR__ . '/../app/Controllers/ErroController.php';
require __DIR__ . '/../app/Controllers/InicioController.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/FilmeController.php';
require __DIR__ . '/../app/Controllers/SalaController.php';
require __DIR__ . '/../app/Controllers/SessaoController.php';
require __DIR__ . '/../app/Controllers/IngressoController.php';
require __DIR__ . '/../app/Controllers/UsuarioController.php';

Session::iniciar();

// Mapa de páginas: valor de ?page= => classe do Controller
$rotas = [
    'inicio'    => InicioController::class,
    'login'     => AuthController::class,
    'filmes'    => FilmeController::class,
    'salas'     => SalaController::class,
    'sessoes'   => SessaoController::class,
    'ingressos' => IngressoController::class,
    'usuarios'  => UsuarioController::class,
];

$page = $_GET['page'] ?? 'inicio';
$acao = $_GET['acao'] ?? 'index';

try {
    Auth::sincronizar();

    // Parâmetro que não seja texto, página desconhecida ou ação inválida => 404.
    // Só métodos públicos são ações; __construct e outros métodos mágicos ficam de fora.
    $rotaValida = is_string($page) && is_string($acao)
        && isset($rotas[$page])
        && !str_starts_with($acao, '__')
        && in_array($acao, get_class_methods($rotas[$page]), true);

    if (!$rotaValida) {
        (new ErroController())->naoEncontrado();
        exit;
    }

    $controller = new $rotas[$page]();
    $controller->$acao();

} catch (Throwable $e) {
    error_log((string) $e);
    $detalhe = $app['debug'] ? $e->getMessage() : '';
    (new ErroController())->interno($detalhe);
}
