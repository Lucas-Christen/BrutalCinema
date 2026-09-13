<?php
/**
 * Ingressos vendidos (admin e funcionário).
 * Variáveis: $ingressos
 */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-ticket-perforated text-brutal"></i> Ingressos vendidos</h1>
    <a href="<?= url('sessoes') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Vender ingresso
    </a>
</div>

<?php if ($ingressos === []): ?>
    <p class="text-secondary">Nenhum ingresso vendido. Escolha uma sessão para iniciar a venda.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Filme</th>
                <th>Sessão</th>
                <th>Sala</th>
                <th>Assento</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Vendido por</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ingressos as $i): ?>
            <tr>
                <td><?= e($i['filme_titulo']) ?></td>
                <td><?= dataHoraBr($i['inicio']) ?></td>
                <td><?= e($i['sala_nome']) ?></td>
                <td><span class="badge text-bg-light"><?= e($i['fileira'] . $i['numero']) ?></span></td>
                <td>
                    <?= e($i['nome_cliente']) ?>
                    <small class="text-secondary d-block"><?= cpfFormatado($i['cpf_cliente']) ?></small>
                </td>
                <td><?= e(ucfirst($i['tipo'])) ?></td>
                <td><?= moeda($i['valor_pago']) ?></td>
                <td>
                    <?= e($i['vendedor_nome']) ?>
                    <small class="text-secondary d-block"><?= dataHoraBr($i['vendido_em']) ?></small>
                </td>
                <td class="text-end">
                    <?php if ($i['inicio'] > date('Y-m-d H:i:s')): ?>
                        <form method="post" action="<?= url('ingressos', 'cancelar') ?>" class="d-inline"
                              onsubmit="return confirm('Cancelar este ingresso e liberar o assento?');">
                            <input type="hidden" name="id" value="<?= (int) $i['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
