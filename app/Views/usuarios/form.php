<?php
/**
 * Formulário de usuário (admin), usado para criar e editar.
 * Variáveis: $titulo, $usuario, $erros
 */
$id = (int) ($usuario['id'] ?? 0);
$rotulos = ['admin' => 'Administrador', 'funcionario' => 'Funcionário', 'cliente' => 'Cliente'];
?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <h1 class="h3 mb-3"><i class="bi bi-people text-brutal"></i> <?= e($titulo) ?></h1>

    <div class="card">
    <div class="card-body">
        <form method="post" action="<?= url('usuarios', 'salvar') ?>" novalidate>
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control <?= classeInvalida($erros, 'nome') ?>"
                           value="<?= e($usuario['nome'] ?? '') ?>" autofocus>
                    <?= feedback($erros, 'nome') ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="perfil" class="form-label">Perfil *</label>
                    <select id="perfil" name="perfil" class="form-select <?= classeInvalida($erros, 'perfil') ?>">
                        <option value="">Selecione</option>
                        <?php foreach (Usuario::PERFIS as $p): ?>
                            <option value="<?= $p ?>" <?= ($usuario['perfil'] ?? '') === $p ? 'selected' : '' ?>><?= $rotulos[$p] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= feedback($erros, 'perfil') ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control <?= classeInvalida($erros, 'email') ?>"
                       value="<?= e($usuario['email'] ?? '') ?>">
                <?= feedback($erros, 'email') ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="senha" class="form-label">Senha <?= $id > 0 ? '' : '*' ?></label>
                    <input type="password" id="senha" name="senha" class="form-control <?= classeInvalida($erros, 'senha') ?>">
                    <div class="form-text">
                        <?= $id > 0 ? 'Deixe em branco para manter a senha atual.' : 'Mínimo de 6 caracteres.' ?>
                    </div>
                    <?= feedback($erros, 'senha') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="senha_confirmacao" class="form-label">Confirmar senha</label>
                    <input type="password" id="senha_confirmacao" name="senha_confirmacao"
                           class="form-control <?= classeInvalida($erros, 'senha_confirmacao') ?>">
                    <?= feedback($erros, 'senha_confirmacao') ?>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    </div>
</div>
</div>
