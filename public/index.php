<?php
/**
 * Front Controller: ponto de entrada único da aplicação.
 *
 * Toda requisição chega aqui na forma index.php?page=X&acao=Y.
 * "page" escolhe o Controller e "acao" o método a ser executado.
 */
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ErroController;
use App\Controllers\FilmeController;
use App\Controllers\IngressoController;
use App\Controllers\InicioController;
use App\Controllers\SalaController;
use App\Controllers\SessaoController;
use App\Controllers\UsuarioController;
use App\Core\Auth;
use App\Core\Session;
use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface as DotenvException;

require __DIR__ . '/../vendor/autoload.php';

// Sem configuração válida não há banco nem página de erro: avisa e encerra
try {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS']);
} catch (DotenvException $e) {
    error_log((string) $e);
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Configuração inválida: copie .env.example para .env e preencha todas as variáveis.');
}

$app = require __DIR__ . '/../config/app.php';
date_default_timezone_set($app['timezone']);

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
