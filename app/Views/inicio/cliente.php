<?php
/**
 * Página inicial do cliente: sessões em cartaz e próximos ingressos.
 * Variáveis: $sessoes (próximas, com vendidos e capacidade), $ingressos (próximos do cliente)
 */
?>
<div class="mb-4">
    <h1 class="h3 mb-0">Olá, <?= e(Auth::usuario()['nome']) ?>!</h1>
    <span class="text-secondary">Escolha uma sessão e garanta seu lugar.</span>
</div>

<?php if ($ingressos !== []): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-ticket-perforated text-brutal"></i> Seus próximos ingressos</span>
        <a href="<?= url('ingressos', 'meus') ?>" class="small link-light">Ver todos</a>
    </div>
    <div class="list-group list-group-flush">
        <?php foreach ($ingressos as $i): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($i['filme_titulo']) ?></strong>
                    <small class="text-secondary d-block"><?= dataHoraBr($i['inicio']) ?> &middot; <?= e($i['sala_nome']) ?></small>
                </div>
                <span class="badge text-bg-light fs-6"><?= e($i['fileira'] . $i['numero']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<h2 class="h5 mb-3"><i class="bi bi-film text-brutal"></i> Em cartaz</h2>

<?php if ($sessoes === []): ?>
    <p class="text-secondary">Nenhuma sessão disponível no momento. Volte em breve.</p>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($sessoes as $s): ?>
        <?php $livres = (int) $s['capacidade'] - (int) $s['vendidos']; ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 mb-0"><?= e($s['filme_titulo']) ?></h3>
                        <span class="badge text-bg-light"><?= e($s['classificacao']) ?></span>
                    </div>
                    <ul class="list-unstyled small text-secondary mb-3">
                        <li><i class="bi bi-calendar3"></i> <?= dataHoraBr($s['inicio']) ?></li>
                        <li><i class="bi bi-door-open"></i> <?= e($s['sala_nome']) ?> (<?= e($s['sala_tipo']) ?>) &middot; <?= e(ucfirst($s['idioma'])) ?></li>
                        <li><i class="bi bi-people"></i> <?= $livres ?> lugares livres</li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="fw-bold"><?= moeda($s['preco']) ?></span>
                        <?php if ($livres > 0): ?>
                            <a href="<?= url('ingressos', 'comprar', ['sessao_id' => $s['id']]) ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-ticket-perforated"></i> Comprar
                            </a>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Esgotado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
