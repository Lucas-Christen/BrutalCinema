<?php
/**
 * Configurações gerais da aplicação.
 */
return [
    'nome'     => 'BrutalCinema',
    'timezone' => 'America/Sao_Paulo',

    // Em desenvolvimento, exibe detalhes do erro na página 500. Desligar em produção.
    'debug'    => true,

    // Minutos reservados para limpeza da sala entre uma sessão e outra
    'intervalo_limpeza_min' => 15,
];
