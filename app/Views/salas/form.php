<?php
/**
 * Formulário de sala, usado para criar e editar.
 * Variáveis: $titulo, $sala (valores atuais ou digitados), $erros
 */
$id = (int) ($sala['id'] ?? 0);
?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <h1 class="h3 mb-3"><i class="bi bi-door-open text-brutal"></i> <?= e($titulo) ?></h1>

    <div class="card">
    <div class="card-body">
        <form method="post" action="<?= url('salas', 'salvar') ?>" novalidate>
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control <?= classeInvalida($erros, 'nome') ?>"
                           value="<?= e($sala['nome'] ?? '') ?>" placeholder="Ex.: Sala 1" autofocus>
                    <?= feedback($erros, 'nome') ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tipo" class="form-label">Tipo *</label>
                    <select id="tipo" name="tipo" class="form-select <?= classeInvalida($erros, 'tipo') ?>">
                        <option value="">Selecione</option>
                        <?php foreach (Sala::TIPOS as $t): ?>
                            <option value="<?= $t ?>" <?= ($sala['tipo'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= feedback($erros, 'tipo') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fileiras" class="form-label">Fileiras *</label>
                    <input type="number" id="fileiras" name="fileiras"
                           class="form-control <?= classeInvalida($erros, 'fileiras') ?>"
                           value="<?= e($sala['fileiras'] ?? '') ?>">
                    <div class="form-text">De 1 a 26. Identificadas por letras (A, B, C...).</div>
                    <?= feedback($erros, 'fileiras') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="assentos_por_fileira" class="form-label">Assentos por fileira *</label>
                    <input type="number" id="assentos_por_fileira" name="assentos_por_fileira"
                           class="form-control <?= classeInvalida($erros, 'assentos_por_fileira') ?>"
                           value="<?= e($sala['assentos_por_fileira'] ?? '') ?>">
                    <div class="form-text">De 1 a 50.</div>
                    <?= feedback($erros, 'assentos_por_fileira') ?>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                <a href="<?= url('salas') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    </div>
</div>
</div>
