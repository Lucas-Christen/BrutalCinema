<?php
/**
 * Exibe as mensagens flash como alertas do Bootstrap.
 */
$classes = ['sucesso' => 'alert-success', 'erro' => 'alert-danger', 'aviso' => 'alert-warning'];
?>
<?php foreach ($flash as $tipo => $mensagem): ?>
    <div class="alert <?= $classes[$tipo] ?? 'alert-info' ?> alert-dismissible fade show" role="alert">
        <?= e($mensagem) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endforeach; ?>
<?php unset($classes, $tipo, $mensagem); ?>
