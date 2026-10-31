<?php
/**
 * Configurações gerais da aplicação.
 */
return [
    'nome'     => 'BrutalCinema',
    'timezone' => 'America/Sao_Paulo',

    // Em desenvolvimento, exibe detalhes do erro na página 500. Desligar em produção (APP_DEBUG no .env).
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),

    // Minutos reservados para limpeza da sala entre uma sessão e outra
    'intervalo_limpeza_min' => 15,
];
