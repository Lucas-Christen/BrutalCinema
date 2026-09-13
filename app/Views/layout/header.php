<?php
/**
 * Layout provisório. Será substituído pelo layout Bootstrap no passo 4.
 * Variáveis disponíveis: $titulo, $flash
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'BrutalCinema') ?> - BrutalCinema</title>
</head>
<body>
<main>
<?php foreach ($flash as $tipo => $mensagem): ?>
    <p class="flash flash-<?= e($tipo) ?>"><?= e($mensagem) ?></p>
<?php endforeach; ?>
