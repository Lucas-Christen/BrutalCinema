<?php
namespace App\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use RuntimeException;

use function FastRoute\simpleDispatcher;

/**
 * Resolve URLs transparentes usando a tabela de config/rotas.php.
 *
 * Traduz a URL recebida em Controller + ação (com o pacote nikic/fast-route)
 * e faz o caminho inverso, gerando links a partir de página + ação.
 */
class Router
{
    /** @var array{paginas: array<string, class-string>, rotas: array<int, array>}|null */
    private static ?array $tabela = null;

    private static string $paginaAtual = '';

    /**
     * Procura a rota da requisição. Os parâmetros da URL são copiados para $_GET.
     * @return array{controller: class-string, acao: string}|null null = rota inexistente
     */
    public static function resolver(string $metodo, string $uri): ?array
    {
        $tabela = self::tabela();

        $dispatcher = simpleDispatcher(function (RouteCollector $r) use ($tabela) {
            foreach ($tabela['rotas'] as [$metodos, $caminho, $page, $acao]) {
                $r->addRoute($metodos, $caminho, [$page, $acao]);
            }
        });

        $resultado = $dispatcher->dispatch($metodo, self::caminho($uri));

        if ($resultado[0] !== Dispatcher::FOUND) {
            return null;
        }

        [, [$page, $acao], $parametros] = $resultado;

        $_GET = $parametros + $_GET;
        self::$paginaAtual = $page;

        return ['controller' => $tabela['paginas'][$page], 'acao' => $acao];
    }

    /**
     * Monta a URL de uma página + ação. Parâmetros que não fazem parte
     * do caminho vão para a query string.
     */
    public static function url(string $page, string $acao = '', array $params = []): string
    {
        foreach (self::tabela()['rotas'] as [, $caminho, $paginaRota, $acaoRota]) {
            if ($paginaRota !== $page || $acaoRota !== ($acao !== '' ? $acao : 'index')) {
                continue;
            }

            $url = preg_replace_callback('/\{(\w+)(?::[^}]*)?\}/', function ($m) use (&$params) {
                $valor = $params[$m[1]];
                unset($params[$m[1]]);
                return rawurlencode((string) $valor);
            }, $caminho);

            $query = http_build_query($params);
            return self::base() . $url . ($query !== '' ? '?' . $query : '');
        }

        throw new RuntimeException("Rota não encontrada: {$page}/{$acao}");
    }

    /**
     * Prefixo da aplicação na URL. Vazio em php -S; no XAMPP, por exemplo,
     * "/BrutalCinema/public".
     */
    public static function base(): string
    {
        return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
    }

    /**
     * Página da rota atual, usada para destacar o item do menu.
     */
    public static function paginaAtual(): string
    {
        return self::$paginaAtual;
    }

    /**
     * Isola o caminho da URL, sem query string, prefixo ou barra final.
     */
    private static function caminho(string $uri): string
    {
        $caminho = rawurldecode((string) parse_url($uri, PHP_URL_PATH));
        $base = self::base();

        if ($base !== '' && str_starts_with($caminho, $base)) {
            $caminho = substr($caminho, strlen($base));
        }

        return '/' . trim($caminho, '/');
    }

    /** @return array{paginas: array<string, class-string>, rotas: array<int, array>} */
    private static function tabela(): array
    {
        return self::$tabela ??= require __DIR__ . '/../../config/rotas.php';
    }
}
