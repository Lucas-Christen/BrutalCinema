<?php
/**
 * Mapa de assentos + formulário de venda/compra.
 * Variáveis: $sessao (com filme e sala), $ocupados (["A-1", ...]),
 *            $dados, $erros, $balcao (true = venda no balcão)
 */
$acao     = $balcao ? 'vender' : 'comprar';
$selecao  = $dados['assentos'] ?? [];
$fileiras = (int) $sessao['fileiras'];
$porFila  = (int) $sessao['assentos_por_fileira'];
$livres   = $fileiras * $porFila - count($ocupados);
?>
<h1 class="h3 mb-3"><i class="bi bi-ticket-perforated text-brutal"></i> <?= e($titulo) ?></h1>
<div id="dados-sessao" data-preco="<?= (float) $sessao['preco'] ?>" hidden></div>

<div class="row g-4">
    <!-- Mapa -->
    <div class="col-lg-7">
        <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 mb-1"><?= e($sessao['filme_titulo']) ?></h2>
                    <div class="text-secondary small">
                        <?= e($sessao['sala_nome']) ?> (<?= e($sessao['sala_tipo']) ?>) &middot;
                        <?= dataHoraBr($sessao['inicio']) ?> &middot;
                        <?= e(ucfirst($sessao['idioma'])) ?> &middot;
                        <span class="badge text-bg-light"><?= e($sessao['classificacao']) ?></span>
                    </div>
                </div>
                <span class="badge text-bg-secondary"><?= $livres ?> livres</span>
            </div>

            <form method="post" action="<?= url('ingressos', $acao, ['sessao_id' => $sessao['id']]) ?>" id="form-ingresso" novalidate>
                <input type="hidden" name="sessao_id" value="<?= (int) $sessao['id'] ?>">

                <div class="overflow-auto">
                <div class="mapa">
                    <div class="tela">TELA</div>
                    <?php for ($f = 1; $f <= $fileiras; $f++): ?>
                        <?php $letra = chr(64 + $f); ?>
                        <div class="fileira">
                            <span class="fileira-letra"><?= $letra ?></span>
                            <?php for ($n = 1; $n <= $porFila; $n++): ?>
                                <?php $codigo = "{$letra}-{$n}"; $ocupado = in_array($codigo, $ocupados, true); ?>
                                <label class="assento" title="<?= $letra . $n ?><?= $ocupado ? ' (ocupado)' : '' ?>">
                                    <input type="checkbox" name="assentos[]" value="<?= $codigo ?>"
                                           <?= $ocupado ? 'disabled' : '' ?> <?= in_array($codigo, $selecao, true) ? 'checked' : '' ?>>
                                    <span><?= $n ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                </div>

                <div class="legenda text-secondary small mt-3">
                    <span style="background:#3a3a3a"></span> Livre &nbsp;
                    <span style="background:#f5f5f5"></span> Selecionado &nbsp;
                    <span style="background:var(--brutal-vermelho)"></span> Ocupado
                </div>
                <div class="text-secondary small mt-2">
                    Clique nos assentos para selecionar (até <?= (int) $maxAssentos ?> por vez).
                    <span id="resumo-assentos" class="text-light"></span>
                </div>
                <?php if (isset($erros['assentos'])): ?>
                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle"></i> <?= e($erros['assentos']) ?></div>
                <?php endif; ?>
            </form>
        </div>
        </div>
    </div>

    <!-- Dados da venda: os campos pertencem ao formulário do mapa via atributo form="form-ingresso" -->
    <div class="col-lg-5">
        <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-3">Dados do ingresso</h2>

            <?php if ($balcao): ?>
                <div class="mb-3">
                    <label for="nome_cliente" class="form-label">Nome do cliente *</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" form="form-ingresso"
                           class="form-control <?= classeInvalida($erros, 'nome_cliente') ?>"
                           value="<?= e($dados['nome_cliente'] ?? '') ?>">
                    <?= feedback($erros, 'nome_cliente') ?>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" class="form-control" value="<?= e(Auth::usuario()['nome']) ?>" disabled>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="cpf_cliente" class="form-label">CPF *</label>
                <input type="text" id="cpf_cliente" name="cpf_cliente" form="form-ingresso"
                       inputmode="numeric" maxlength="14" data-mascara="cpf"
                       class="form-control <?= classeInvalida($erros, 'cpf_cliente') ?>"
                       value="<?= e(strlen($dados['cpf_cliente'] ?? '') === 11 ? cpfFormatado($dados['cpf_cliente']) : ($dados['cpf_cliente'] ?? '')) ?>"
                       placeholder="000.000.000-00">
                <?= feedback($erros, 'cpf_cliente') ?>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Tipo *</label>
                <?php $tipoSel = $dados['tipo'] ?? 'inteira'; ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_inteira" value="inteira" form="form-ingresso"
                           <?= $tipoSel === 'inteira' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="tipo_inteira">Inteira &middot; <?= moeda($sessao['preco']) ?></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo" id="tipo_meia" value="meia" form="form-ingresso"
                           <?= $tipoSel === 'meia' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="tipo_meia">Meia &middot; <?= moeda($sessao['preco'] / 2) ?></label>
                </div>
                <?php if (isset($erros['tipo'])): ?>
                    <div class="text-danger small"><?= e($erros['tipo']) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" form="form-ingresso" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $balcao ? 'Confirmar venda' : 'Confirmar compra' ?>
                    <span id="total-ingressos"></span>
                </button>
                <a href="<?= url('sessoes') ?>" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </div>
        </div>
    </div>
</div>
