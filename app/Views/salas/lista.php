<?php
/**
 * Listagem de salas.
 * Variáveis: $salas (array de linhas)
 */
$ehAdmin = Auth::temPerfil('admin');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-door-open text-brutal"></i> Salas</h1>
    <?php if ($ehAdmin): ?>
        <a href="<?= url('salas', 'novo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nova sala
        </a>
    <?php endif; ?>
</div>

<?php if ($salas === []): ?>
    <p class="text-secondary">Nenhuma sala cadastrada.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Fileiras</th>
                <th>Assentos por fileira</th>
                <th>Capacidade</th>
                <th>Status</th>
                <?php if ($ehAdmin): ?><th class="text-end">Ações</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($salas as $s): ?>
            <tr class="<?= $s['ativo'] ? '' : 'text-secondary' ?>">
                <td><?= e($s['nome']) ?></td>
                <td><span class="badge text-bg-light"><?= e($s['tipo']) ?></span></td>
                <td><?= (int) $s['fileiras'] ?> (A a <?= chr(64 + (int) $s['fileiras']) ?>)</td>
                <td><?= (int) $s['assentos_por_fileira'] ?></td>
                <td><?= (int) $s['fileiras'] * (int) $s['assentos_por_fileira'] ?> lugares</td>
                <td>
                    <?php if ($s['ativo']): ?>
                        <span class="badge text-bg-success">Ativa</span>
                    <?php else: ?>
                        <span class="badge text-bg-secondary">Inativa</span>
                    <?php endif; ?>
                </td>
                <?php if ($ehAdmin): ?>
                <td class="text-end text-nowrap">
                    <a href="<?= url('salas', 'editar', ['id' => $s['id']]) ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <?php if ($s['ativo']): ?>
                        <form method="post" action="<?= url('salas', 'desativar') ?>" class="d-inline"
                              onsubmit="return confirm('Desativar esta sala?');">
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Desativar
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= url('salas', 'ativar') ?>" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check-circle"></i> Ativar
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
