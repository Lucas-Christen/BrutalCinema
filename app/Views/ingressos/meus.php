<?php
/**
 * Ingressos do cliente logado, em cards.
 * Variáveis: $ingressos
 */
$agora = date('Y-m-d H:i:s');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-ticket-perforated text-brutal"></i> Meus ingressos</h1>
    <a href="<?= url('sessoes') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Comprar ingresso
    </a>
</div>

<?php if ($ingressos === []): ?>
    <p class="text-secondary">Você ainda não comprou ingressos. Veja as <a href="<?= url('sessoes') ?>" class="link-light">sessões disponíveis</a>.</p>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($ingressos as $i): ?>
        <?php $passado = $i['inicio'] < $agora || $i['sessao_status'] !== 'agendada'; ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 <?= $passado ? 'opacity-50' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h2 class="h5 mb-0"><?= e($i['filme_titulo']) ?></h2>
                        <span class="badge text-bg-light fs-6"><?= e($i['fileira'] . $i['numero']) ?></span>
                    </div>
                    <ul class="list-unstyled small mb-2">
                        <li><i class="bi bi-calendar3"></i> <?= dataHoraBr($i['inicio']) ?></li>
                        <li><i class="bi bi-door-open"></i> <?= e($i['sala_nome']) ?> (<?= e($i['sala_tipo']) ?>) &middot; <?= e(ucfirst($i['idioma'])) ?></li>
                        <li><i class="bi bi-person"></i> <?= e($i['nome_cliente']) ?> &middot; <?= cpfFormatado($i['cpf_cliente']) ?></li>
                        <li><i class="bi bi-cash"></i> <?= e(ucfirst($i['tipo'])) ?> &middot; <?= moeda($i['valor_pago']) ?></li>
                    </ul>
                    <?php if ($i['sessao_status'] === 'cancelada'): ?>
                        <span class="badge text-bg-danger">Sessão cancelada</span>
                    <?php elseif ($i['inicio'] < $agora): ?>
                        <span class="badge text-bg-secondary">Sessão encerrada</span>
                    <?php else: ?>
                        <span class="badge text-bg-success">Válido</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
