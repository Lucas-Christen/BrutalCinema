<div class="text-center py-5">
    <h1 class="display-4 text-brutal">Erro interno</h1>
    <p class="lead">Ocorreu um erro inesperado. Tente novamente mais tarde.</p>
    <?php if ($detalhe !== ''): ?>
        <pre class="text-start bg-body-tertiary p-3 rounded small"><?= e($detalhe) ?></pre>
    <?php endif; ?>
    <a href="<?= url('inicio') ?>" class="btn btn-primary">Voltar ao início</a>
</div>
