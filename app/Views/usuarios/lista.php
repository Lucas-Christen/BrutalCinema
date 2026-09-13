<?php
/**
 * Listagem de usuários (admin).
 * Variáveis: $usuarios
 */
$cores = ['admin' => 'text-bg-danger', 'funcionario' => 'text-bg-warning', 'cliente' => 'text-bg-info'];
$meuId = Auth::id();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-people text-brutal"></i> Usuários</h1>
    <a href="<?= url('usuarios', 'novo') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Novo usuário
    </a>
</div>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Cadastro</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <?php $souEu = (int) $u['id'] === $meuId; ?>
            <tr class="<?= $u['ativo'] ? '' : 'text-secondary' ?>">
                <td>
                    <?= e($u['nome']) ?>
                    <?php if ($souEu): ?><span class="badge text-bg-light ms-1">você</span><?php endif; ?>
                </td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge <?= $cores[$u['perfil']] ?? 'text-bg-secondary' ?>"><?= e($u['perfil']) ?></span></td>
                <td><?= dataHoraBr($u['criado_em']) ?></td>
                <td>
                    <?php if ($u['ativo']): ?>
                        <span class="badge text-bg-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge text-bg-secondary">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="text-end text-nowrap">
                    <a href="<?= url('usuarios', 'editar', ['id' => $u['id']]) ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <?php if ($souEu): ?>
                        <?php /* Admin não desativa a própria conta */ ?>
                    <?php elseif ($u['ativo']): ?>
                        <form method="post" action="<?= url('usuarios', 'desativar') ?>" class="d-inline"
                              onsubmit="return confirm('Desativar este usuário? Ele não conseguirá mais fazer login.');">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Desativar
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= url('usuarios', 'ativar') ?>" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check-circle"></i> Ativar
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
