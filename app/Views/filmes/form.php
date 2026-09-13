<?php
/**
 * Formulário de filme, usado tanto para criar quanto para editar.
 * Variáveis: $titulo, $filme (valores atuais ou digitados), $erros (campo => mensagem)
 */
$id = (int) ($filme['id'] ?? 0);
?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <h1 class="h3 mb-3"><i class="bi bi-film text-brutal"></i> <?= e($titulo) ?></h1>

    <div class="card">
    <div class="card-body">
        <form method="post" action="<?= url('filmes', 'salvar') ?>" novalidate>
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="titulo" class="form-label">Título *</label>
                <input type="text" id="titulo" name="titulo" class="form-control <?= classeInvalida($erros, 'titulo') ?>"
                       value="<?= e($filme['titulo'] ?? '') ?>" autofocus>
                <?= feedback($erros, 'titulo') ?>
            </div>

            <div class="mb-3">
                <label for="sinopse" class="form-label">Sinopse</label>
                <textarea id="sinopse" name="sinopse" rows="3"
                          class="form-control <?= classeInvalida($erros, 'sinopse') ?>"><?= e($filme['sinopse'] ?? '') ?></textarea>
                <?= feedback($erros, 'sinopse') ?>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="duracao_min" class="form-label">Duração (min) *</label>
                    <input type="number" id="duracao_min" name="duracao_min"
                           class="form-control <?= classeInvalida($erros, 'duracao_min') ?>"
                           value="<?= e($filme['duracao_min'] ?? '') ?>">
                    <?= feedback($erros, 'duracao_min') ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="classificacao" class="form-label">Classificação *</label>
                    <select id="classificacao" name="classificacao" class="form-select <?= classeInvalida($erros, 'classificacao') ?>">
                        <option value="">Selecione</option>
                        <?php foreach (Filme::CLASSIFICACOES as $c): ?>
                            <option value="<?= $c ?>" <?= ($filme['classificacao'] ?? '') === $c ? 'selected' : '' ?>>
                                <?= $c === 'L' ? 'Livre' : $c . ' anos' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= feedback($erros, 'classificacao') ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="ano_lancamento" class="form-label">Ano de lançamento</label>
                    <input type="number" id="ano_lancamento" name="ano_lancamento"
                           class="form-control <?= classeInvalida($erros, 'ano_lancamento') ?>"
                           value="<?= e($filme['ano_lancamento'] ?? '') ?>">
                    <?= feedback($erros, 'ano_lancamento') ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="genero" class="form-label">Gênero *</label>
                <input type="text" id="genero" name="genero" class="form-control <?= classeInvalida($erros, 'genero') ?>"
                       value="<?= e($filme['genero'] ?? '') ?>" placeholder="Ex.: Ação, Drama, Animação">
                <?= feedback($erros, 'genero') ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                <a href="<?= url('filmes') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    </div>
</div>
</div>
