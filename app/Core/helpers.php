<?php
/**
 * Funções auxiliares curtas, usadas principalmente nas Views.
 */

/**
 * Escapa texto para exibição segura em HTML (evita XSS).
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/**
 * Monta uma URL interna da aplicação.
 * Ex.: url('filmes', 'editar', ['id' => 3]) => index.php?page=filmes&acao=editar&id=3
 */
function url(string $page, string $acao = '', array $params = []): string
{
    $query = ['page' => $page];
    if ($acao !== '') {
        $query['acao'] = $acao;
    }
    return 'index.php?' . http_build_query($query + $params);
}

/**
 * Lê um valor de config/app.php. Ex.: config('nome')
 */
function config(string $chave, mixed $padrao = null): mixed
{
    static $app = null;
    if ($app === null) {
        $app = require __DIR__ . '/../../config/app.php';
    }
    return $app[$chave] ?? $padrao;
}
