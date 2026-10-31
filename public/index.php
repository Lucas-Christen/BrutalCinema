<?php
/**
 * Front Controller: ponto de entrada único da aplicação.
 *
 * Toda requisição chega aqui, e o Router traduz a URL em Controller + ação
 * usando a tabela de config/rotas.php.
 */
declare(strict_types=1);

use App\Controllers\ErroController;
use App\Core\Auth;
use App\Core\Router;
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

try {
    Auth::sincronizar();

    $rota = Router::resolver($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

    // URL inexistente ou método HTTP não permitido => 404
    if ($rota === null) {
        (new ErroController())->naoEncontrado();
        exit;
    }

    $controller = new $rota['controller']();
    $controller->{$rota['acao']}();

} catch (Throwable $e) {
    error_log((string) $e);
    $detalhe = $app['debug'] ? $e->getMessage() : '';
    (new ErroController())->interno($detalhe);
}
