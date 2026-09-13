<?php
/**
 * Listagem de sessões.
 * Variáveis: $sessoes (linhas com filme_titulo, sala_nome, duracao_min)
 */
$ehAdmin   = Auth::temPerfil('admin');
$ehCliente = Auth::temPerfil('cliente');
$agora     = date('Y-m-d H:i:s');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-calendar3 text-brutal"></i> Sessões</h1>
    <?php if ($ehAdmin): ?>
        <a href="<?= url('sessoes', 'novo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nova sessão
        </a>
    <?php endif; ?>
</div>

<?php if ($sessoes === []): ?>
    <p class="text-secondary">Nenhuma sessão cadastrada.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Filme</th>
                <th>Sala</th>
                <th>Início</th>
                <th>Término</th>
                <th>Idioma</th>
                <th>Preço</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sessoes as $s): ?>
            <?php
            // Sessão agendada cujo horário já passou é exibida como encerrada
            $encerrada = $s['status'] === 'agendada' && $s['fim'] < $agora;
            $editavel  = $s['status'] === 'agendada' && !$encerrada;
            ?>
            <tr class="<?= $editavel ? '' : 'text-secondary' ?>">
                <td>
                    <?= e($s['filme_titulo']) ?>
                    <small class="text-secondary d-block"><?= (int) $s['duracao_min'] ?> min</small>
                </td>
                <td><?= e($s['sala_nome']) ?></td>
                <td><?= dataHoraBr($s['inicio']) ?></td>
                <td><?= date('H:i', strtotime($s['fim'])) ?></td>
                <td><?= e(ucfirst($s['idioma'])) ?></td>
                <td><?= moeda($s['preco']) ?></td>
                <td>
                    <?php if ($s['status'] === 'cancelada'): ?>
                        <span class="badge text-bg-danger">Cancelada</span>
                    <?php elseif ($encerrada || $s['status'] === 'encerrada'): ?>
                        <span class="badge text-bg-secondary">Encerrada</span>
                    <?php else: ?>
                        <span class="badge text-bg-success">Agendada</span>
                    <?php endif; ?>
                </td>
                <td class="text-end text-nowrap">
                    <?php if ($editavel): ?>
                        <a href="<?= url('ingressos', $ehCliente ? 'comprar' : 'vender', ['sessao_id' => $s['id']]) ?>"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-ticket-perforated"></i> <?= $ehCliente ? 'Comprar' : 'Vender' ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($ehAdmin && $editavel): ?>
                        <a href="<?= url('sessoes', 'editar', ['id' => $s['id']]) ?>" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form method="post" action="<?= url('sessoes', 'cancelar') ?>" class="d-inline"
                              onsubmit="return confirm('Cancelar esta sessão?');">
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
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
