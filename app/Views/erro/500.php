<h1>Erro interno</h1>
<p>Ocorreu um erro inesperado. Tente novamente mais tarde.</p>
<?php if ($detalhe !== ''): ?>
    <pre><?= e($detalhe) ?></pre>
<?php endif; ?>
<p><a href="<?= url('inicio') ?>">Voltar ao início</a></p>
