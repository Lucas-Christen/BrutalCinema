<?php
/**
 * Formulário de cadastro público (cria usuário com perfil cliente).
 * Variáveis: $dados (valores digitados), $erros (campo => mensagem)
 */
?>
<div class="card card-auth shadow">
    <div class="card-body p-4">
        <h1 class="h3 text-center mb-1">
            <i class="bi bi-film text-brutal"></i> <?= e(config('nome')) ?>
        </h1>
        <p class="text-center text-secondary mb-4">Crie sua conta para comprar ingressos</p>

        <form method="post" action="<?= url('login', 'registro') ?>" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" id="nome" name="nome" class="form-control <?= classeInvalida($erros, 'nome') ?>"
                       value="<?= e($dados['nome'] ?? '') ?>" autofocus>
                <?= feedback($erros, 'nome') ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control <?= classeInvalida($erros, 'email') ?>"
                       value="<?= e($dados['email'] ?? '') ?>">
                <?= feedback($erros, 'email') ?>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha *</label>
                <input type="password" id="senha" name="senha" class="form-control <?= classeInvalida($erros, 'senha') ?>">
                <div class="form-text">Mínimo de 6 caracteres.</div>
                <?= feedback($erros, 'senha') ?>
            </div>

            <div class="mb-4">
                <label for="senha_confirmacao" class="form-label">Confirmar senha *</label>
                <input type="password" id="senha_confirmacao" name="senha_confirmacao"
                       class="form-control <?= classeInvalida($erros, 'senha_confirmacao') ?>">
                <?= feedback($erros, 'senha_confirmacao') ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-person-plus"></i> Criar conta
            </button>
        </form>

        <p class="text-center text-secondary small mt-3 mb-0">
            Já tem conta? <a href="<?= url('login') ?>" class="link-light">Entrar</a>
        </p>
    </div>
</div>
