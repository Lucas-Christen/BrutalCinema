<?php
/**
 * Formulário de login.
 * Variáveis: $dados (valores digitados), $erros (campo => mensagem), $erroGeral
 */
?>
<div class="card card-auth shadow">
    <div class="card-body p-4">
        <h1 class="h3 text-center mb-4">
            <i class="bi bi-film text-brutal"></i> <?= e(config('nome')) ?>
        </h1>

        <?php if ($erroGeral !== ''): ?>
            <div class="alert alert-danger py-2"><?= e($erroGeral) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('login') ?>" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email"
                       class="form-control <?= isset($erros['email']) ? 'is-invalid' : '' ?>"
                       value="<?= e($dados['email'] ?? '') ?>" autofocus>
                <?php if (isset($erros['email'])): ?>
                    <div class="invalid-feedback"><?= e($erros['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" id="senha" name="senha"
                       class="form-control <?= isset($erros['senha']) ? 'is-invalid' : '' ?>">
                <?php if (isset($erros['senha'])): ?>
                    <div class="invalid-feedback"><?= e($erros['senha']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>

        <p class="text-center text-secondary small mt-3 mb-0">
            Não tem conta? <a href="<?= url('login', 'registro') ?>" class="link-light">Criar conta</a>
        </p>
    </div>
</div>
