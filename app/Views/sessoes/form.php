<?php
/**
 * Formulário de sessão, usado para criar e editar.
 * Variáveis: $titulo, $sessao, $erros, $filmes (ativos), $salas (ativas)
 */
$id = (int) ($sessao['id'] ?? 0);
?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <h1 class="h3 mb-3"><i class="bi bi-calendar3 text-brutal"></i> <?= e($titulo) ?></h1>

    <div class="card">
    <div class="card-body">
        <form method="post" action="<?= url('sessoes', 'salvar') ?>" novalidate>
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="filme_id" class="form-label">Filme *</label>
                <select id="filme_id" name="filme_id" class="form-select <?= classeInvalida($erros, 'filme_id') ?>" autofocus>
                    <option value="">Selecione</option>
                    <?php foreach ($filmes as $f): ?>
                        <option value="<?= (int) $f['id'] ?>" <?= (int) ($sessao['filme_id'] ?? 0) === (int) $f['id'] ? 'selected' : '' ?>>
                            <?= e($f['titulo']) ?> (<?= (int) $f['duracao_min'] ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= feedback($erros, 'filme_id') ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sala_id" class="form-label">Sala *</label>
                    <select id="sala_id" name="sala_id" class="form-select <?= classeInvalida($erros, 'sala_id') ?>">
                        <option value="">Selecione</option>
                        <?php foreach ($salas as $s): ?>
                            <option value="<?= (int) $s['id'] ?>" <?= (int) ($sessao['sala_id'] ?? 0) === (int) $s['id'] ? 'selected' : '' ?>>
                                <?= e($s['nome']) ?> (<?= e($s['tipo']) ?>, <?= (int) $s['fileiras'] * (int) $s['assentos_por_fileira'] ?> lugares)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= feedback($erros, 'sala_id') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma *</label>
                    <select id="idioma" name="idioma" class="form-select <?= classeInvalida($erros, 'idioma') ?>">
                        <option value="">Selecione</option>
                        <?php foreach (Sessao::IDIOMAS as $i): ?>
                            <option value="<?= $i ?>" <?= ($sessao['idioma'] ?? '') === $i ? 'selected' : '' ?>><?= ucfirst($i) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= feedback($erros, 'idioma') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="inicio" class="form-label">Início *</label>
                    <input type="datetime-local" id="inicio" name="inicio"
                           class="form-control <?= classeInvalida($erros, 'inicio') ?>"
                           value="<?= e($sessao['inicio'] ?? '') ?>">
                    <div class="form-text">
                        Término calculado automaticamente: duração do filme + <?= (int) config('intervalo_limpeza_min') ?> min de limpeza.
                    </div>
                    <?= feedback($erros, 'inicio') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="preco" class="form-label">Preço (R$) *</label>
                    <input type="text" id="preco" name="preco" inputmode="decimal"
                           class="form-control <?= classeInvalida($erros, 'preco') ?>"
                           value="<?= e($sessao['preco'] ?? '') ?>" placeholder="Ex.: 30,00">
                    <div class="form-text">Valor da inteira. Meia-entrada é calculada como 50%.</div>
                    <?= feedback($erros, 'preco') ?>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                <a href="<?= url('sessoes') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    </div>
</div>
</div>
