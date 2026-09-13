<?php
/**
 * Listagem de filmes.
 * Variáveis: $filmes (array de linhas)
 */
$ehAdmin = Auth::temPerfil('admin');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-film text-brutal"></i> Filmes</h1>
    <?php if ($ehAdmin): ?>
        <a href="<?= url('filmes', 'novo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo filme
        </a>
    <?php endif; ?>
</div>

<?php if ($filmes === []): ?>
    <p class="text-secondary">Nenhum filme cadastrado.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Título</th>
                <th>Gênero</th>
                <th>Duração</th>
                <th>Classificação</th>
                <th>Ano</th>
                <th>Status</th>
                <?php if ($ehAdmin): ?><th class="text-end">Ações</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filmes as $f): ?>
            <tr class="<?= $f['ativo'] ? '' : 'text-secondary' ?>">
                <td><?= e($f['titulo']) ?></td>
                <td><?= e($f['genero']) ?></td>
                <td><?= (int) $f['duracao_min'] ?> min</td>
                <td><span class="badge text-bg-light"><?= e($f['classificacao']) ?></span></td>
                <td><?= e($f['ano_lancamento'] ?? '-') ?></td>
                <td>
                    <?php if ($f['ativo']): ?>
                        <span class="badge text-bg-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge text-bg-secondary">Inativo</span>
                    <?php endif; ?>
                </td>
                <?php if ($ehAdmin): ?>
                <td class="text-end text-nowrap">
                    <a href="<?= url('filmes', 'editar', ['id' => $f['id']]) ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <?php if ($f['ativo']): ?>
                        <form method="post" action="<?= url('filmes', 'desativar') ?>" class="d-inline"
                              onsubmit="return confirm('Desativar este filme?');">
                            <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Desativar
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= url('filmes', 'ativar') ?>" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
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
