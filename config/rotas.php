<?php
/**
 * Tabela de rotas da aplicação.
 *
 * "paginas" associa cada página ao seu Controller. "rotas" lista
 * [métodos HTTP, caminho, página, ação]; a ação é um método do Controller.
 * A função url() usa esta mesma tabela para montar os links.
 */

use App\Controllers\AuthController;
use App\Controllers\FilmeController;
use App\Controllers\IngressoController;
use App\Controllers\InicioController;
use App\Controllers\SalaController;
use App\Controllers\SessaoController;
use App\Controllers\UsuarioController;

return [
    'paginas' => [
        'inicio'    => InicioController::class,
        'login'     => AuthController::class,
        'filmes'    => FilmeController::class,
        'salas'     => SalaController::class,
        'sessoes'   => SessaoController::class,
        'ingressos' => IngressoController::class,
        'usuarios'  => UsuarioController::class,
    ],

    'rotas' => [
        ['GET',           '/',         'inicio', 'index'],

        // Autenticação
        [['GET', 'POST'], '/login',    'login', 'index'],
        [['GET', 'POST'], '/registro', 'login', 'registro'],
        ['POST',          '/sair',     'login', 'sair'],

        // Filmes
        ['GET',  '/filmes',                'filmes', 'index'],
        ['GET',  '/filmes/novo',           'filmes', 'novo'],
        ['GET',  '/filmes/{id:\d+}/editar', 'filmes', 'editar'],
        ['POST', '/filmes/salvar',         'filmes', 'salvar'],
        ['POST', '/filmes/desativar',      'filmes', 'desativar'],
        ['POST', '/filmes/ativar',         'filmes', 'ativar'],

        // Salas
        ['GET',  '/salas',                 'salas', 'index'],
        ['GET',  '/salas/novo',            'salas', 'novo'],
        ['GET',  '/salas/{id:\d+}/editar', 'salas', 'editar'],
        ['POST', '/salas/salvar',          'salas', 'salvar'],
        ['POST', '/salas/desativar',       'salas', 'desativar'],
        ['POST', '/salas/ativar',          'salas', 'ativar'],

        // Sessões
        ['GET',  '/sessoes',                 'sessoes', 'index'],
        ['GET',  '/sessoes/novo',            'sessoes', 'novo'],
        ['GET',  '/sessoes/{id:\d+}/editar', 'sessoes', 'editar'],
        ['POST', '/sessoes/salvar',          'sessoes', 'salvar'],
        ['POST', '/sessoes/cancelar',        'sessoes', 'cancelar'],

        // Ingressos
        ['GET',           '/ingressos',                       'ingressos', 'index'],
        ['GET',           '/ingressos/meus',                  'ingressos', 'meus'],
        ['POST',          '/ingressos/cancelar',              'ingressos', 'cancelar'],
        [['GET', 'POST'], '/sessoes/{sessao_id:\d+}/vender',  'ingressos', 'vender'],
        [['GET', 'POST'], '/sessoes/{sessao_id:\d+}/comprar', 'ingressos', 'comprar'],

        // Usuários
        ['GET',  '/usuarios',                 'usuarios', 'index'],
        ['GET',  '/usuarios/novo',            'usuarios', 'novo'],
        ['GET',  '/usuarios/{id:\d+}/editar', 'usuarios', 'editar'],
        ['POST', '/usuarios/salvar',          'usuarios', 'salvar'],
        ['POST', '/usuarios/desativar',       'usuarios', 'desativar'],
        ['POST', '/usuarios/ativar',          'usuarios', 'ativar'],
    ],
];
