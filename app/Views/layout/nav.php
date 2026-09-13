<?php
/**
 * Menu superior. Itens variam conforme o perfil do usuário logado.
 */
$usuarioLogado = Auth::usuario();
$paginaAtual   = $_GET['page'] ?? 'inicio';

// [rótulo, page, ícone, perfis com acesso (vazio = todos os logados)]
$itens = [
    ['Início',         'inicio',    'bi-house',        []],
    ['Filmes',         'filmes',    'bi-film',         []],
    ['Salas',          'salas',     'bi-door-open',    []],
    ['Sessões',        'sessoes',   'bi-calendar3',    []],
    ['Ingressos',      'ingressos', 'bi-ticket-perforated', ['admin', 'funcionario']],
    ['Meus ingressos', 'ingressos', 'bi-ticket-perforated', ['cliente']],
    ['Usuários',       'usuarios',  'bi-people',       ['admin']],
];
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom mb-2">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('inicio') ?>">
            <i class="bi bi-film text-brutal"></i> <?= e(config('nome')) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <?php foreach ($itens as [$rotulo, $page, $icone, $perfis]): ?>
                    <?php if ($perfis !== [] && !Auth::temPerfil(...$perfis)) continue; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaAtual === $page ? 'active' : '' ?>" href="<?= url($page) ?>">
                            <i class="bi <?= $icone ?>"></i> <?= $rotulo ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small">
                    <i class="bi bi-person-circle"></i> <?= e($usuarioLogado['nome']) ?>
                    <span class="badge text-bg-secondary ms-1"><?= e($usuarioLogado['perfil']) ?></span>
                </span>
                <form method="post" action="<?= url('login', 'sair') ?>">
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<?php
// Variáveis do menu não devem vazar para a view que será incluída em seguida
unset($usuarioLogado, $paginaAtual, $itens, $rotulo, $page, $icone, $perfis);
?>
