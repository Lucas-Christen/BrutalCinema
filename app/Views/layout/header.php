<?php
/**
 * Abertura do HTML, CSS e menu.
 * Variáveis disponíveis: $titulo (string), $flash (array tipo => mensagem)
 */
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? config('nome')) ?> - <?= e(config('nome')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/estilo.css" rel="stylesheet">
</head>
<body>
<?php if (Auth::logado()) require __DIR__ . '/nav.php'; ?>
<main class="container py-4">
<?php require __DIR__ . '/flash.php'; ?>
