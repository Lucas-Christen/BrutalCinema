<?php
/**
 * Painel de admin e funcionário.
 * Variáveis: $filmesAtivos, $salasAtivas, $sessoesHoje, $vendasHoje (quantidade, receita), $proximas
 */
$ehAdmin = Auth::temPerfil('admin');

$indicadores = [
    ['Filmes ativos',   $filmesAtivos,               'bi-film',              url('filmes')],
    ['Salas ativas',    $salasAtivas,                'bi-door-open',         url('salas')],
    ['Sessões hoje',    $sessoesHoje,                'bi-calendar3',         url('sessoes')],
    ['Ingressos hoje',  $vendasHoje['quantidade'],   'bi-ticket-perforated', url('ingressos'), moeda($vendasHoje['receita'])],
];

// [rótulo, ícone, url, só admin?]
$atalhos = [
    ['Vender ingresso', 'bi-ticket-perforated', url('sessoes'),           false],
    ['Nova sessão',     'bi-calendar-plus',     url('sessoes', 'novo'),   true],
    ['Novo filme',      'bi-film',              url('filmes', 'novo'),    true],
    ['Nova sala',       'bi-door-open',         url('salas', 'novo'),     true],
    ['Usuários',        'bi-people',            url('usuarios'),          true],
];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Painel</h1>
        <span class="text-secondary">Olá, <?= e(Auth::usuario()['nome']) ?>. Hoje é <?= date('d/m/Y') ?>.</span>
    </div>
</div>

<!-- Indicadores -->
<div class="row g-3 mb-4">
    <?php foreach ($indicadores as [$rotulo, $valor, $icone, $link]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= $link ?>" class="card indicador text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi <?= $icone ?> fs-1 text-brutal"></i>
                    <div>
                        <div class="fs-2 fw-bold lh-1"><?= (int) $valor ?></div>
                        <div class="text-secondary small"><?= $rotulo ?></div>
                        <?php if ($rotulo === 'Ingressos hoje'): ?>
                            <div class="text-success small"><?= moeda($vendasHoje['receita']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Próximas sessões -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock text-brutal"></i> Próximas sessões</span>
                <a href="<?= url('sessoes') ?>" class="small link-light">Ver todas</a>
            </div>
            <?php if ($proximas === []): ?>
                <div class="card-body text-secondary">
                    Nenhuma sessão futura agendada.
                    <?php if ($ehAdmin): ?><a href="<?= url('sessoes', 'novo') ?>" class="link-light">Criar sessão</a>.<?php endif; ?>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Horário</th>
                            <th>Filme</th>
                            <th>Sala</th>
                            <th style="min-width: 140px">Ocupação</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($proximas as $s): ?>
                        <?php
                        $pct  = $s['capacidade'] > 0 ? (int) round($s['vendidos'] / $s['capacidade'] * 100) : 0;
                        $cor  = $pct >= 90 ? 'bg-danger' : ($pct >= 50 ? 'bg-warning' : 'bg-success');
                        $hoje = date('Y-m-d', strtotime($s['inicio'])) === date('Y-m-d');
                        ?>
                        <tr>
                            <td class="text-nowrap">
                                <strong><?= date('H:i', strtotime($s['inicio'])) ?></strong>
                                <small class="text-secondary d-block"><?= $hoje ? 'Hoje' : date('d/m', strtotime($s['inicio'])) ?></small>
                            </td>
                            <td>
                                <?= e($s['filme_titulo']) ?>
                                <small class="text-secondary d-block"><?= e(ucfirst($s['idioma'])) ?> &middot; <?= moeda($s['preco']) ?></small>
                            </td>
                            <td><?= e($s['sala_nome']) ?></td>
                            <td>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><?= (int) $s['vendidos'] ?>/<?= (int) $s['capacidade'] ?></span>
                                    <span class="text-secondary"><?= $pct ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px">
                                    <div class="progress-bar <?= $cor ?>" style="width: <?= $pct ?>%"></div>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('ingressos', 'vender', ['sessao_id' => $s['id']]) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-ticket-perforated"></i> Vender
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Atalhos -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-lightning text-brutal"></i> Atalhos</div>
            <div class="list-group list-group-flush">
                <?php foreach ($atalhos as [$rotulo, $icone, $link, $soAdmin]): ?>
                    <?php if ($soAdmin && !$ehAdmin) continue; ?>
                    <a href="<?= $link ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <i class="bi <?= $icone ?> fs-5 text-brutal"></i>
                        <span><?= $rotulo ?></span>
                        <i class="bi bi-chevron-right ms-auto text-secondary"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
