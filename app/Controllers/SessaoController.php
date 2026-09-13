<?php
/**
 * CRUD de sessões. Listagem aberta a todos os logados; alterações só para admin.
 */
class SessaoController extends Controller
{
    private const FORMATO_INPUT = 'Y-m-d\TH:i';   // enviado por <input type="datetime-local">
    private const FORMATO_BANCO = 'Y-m-d H:i:s';

    private Sessao $sessoes;

    public function __construct()
    {
        $this->sessoes = new Sessao();
    }

    public function index(): void
    {
        Auth::exigirLogin();

        $this->renderizar('sessoes/lista', [
            'titulo'  => 'Sessões',
            'sessoes' => $this->sessoes->todasComDetalhes(),
        ]);
    }

    public function novo(): void
    {
        Auth::exigirPerfil('admin');

        $this->renderizarForm('Nova sessão', [], []);
    }

    public function editar(): void
    {
        Auth::exigirPerfil('admin');

        $sessao = $this->buscarOuRedirecionar();

        // Converte para os formatos usados no formulário (datetime-local e vírgula decimal)
        $sessao['inicio'] = date(self::FORMATO_INPUT, strtotime($sessao['inicio']));
        $sessao['preco']  = number_format((float) $sessao['preco'], 2, ',', '');

        $this->renderizarForm('Editar sessão', $sessao, []);
    }

    public function salvar(): void
    {
        Auth::exigirPerfil('admin');

        if (!$this->ehPost()) {
            $this->redirecionar('sessoes');
        }

        $dados = $this->dadosPost();
        $id    = (int) ($dados['id'] ?? 0);

        [$erros, $registro] = $this->validar($dados, $id);

        if ($erros !== []) {
            $this->renderizarForm($id > 0 ? 'Editar sessão' : 'Nova sessão', $dados, $erros);
            return;
        }

        if ($id > 0) {
            $this->sessoes->atualizar($id, $registro);
            Session::flash('sucesso', 'Sessão atualizada com sucesso.');
        } else {
            $this->sessoes->inserir($registro);
            Session::flash('sucesso', 'Sessão cadastrada com sucesso.');
        }

        $this->redirecionar('sessoes');
    }

    /**
     * Cancela a sessão. Bloqueado se já houver ingressos vendidos.
     */
    public function cancelar(): void
    {
        Auth::exigirPerfil('admin');

        if ($this->ehPost()) {
            $sessao = $this->buscarOuRedirecionar();

            if ($this->sessoes->temIngressoVendido((int) $sessao['id'])) {
                Session::flash('erro', 'Não é possível cancelar: a sessão possui ingressos vendidos.');
            } else {
                $this->sessoes->cancelar((int) $sessao['id']);
                Session::flash('sucesso', 'Sessão cancelada.');
            }
        }
        $this->redirecionar('sessoes');
    }

    // ------------------------------------------------------------ internos

    /**
     * Valida o formulário e, se válido, monta o registro pronto para gravar
     * (com fim calculado e preço normalizado).
     *
     * @return array{0: array<string, string>, 1: array<string, mixed>} [erros, registro]
     */
    private function validar(array $dados, int $id): array
    {
        $v = new Validator($dados);
        $v->obrigatorio('filme_id', 'Filme')->inteiro('filme_id', 'Filme', 1)
          ->obrigatorio('sala_id', 'Sala')->inteiro('sala_id', 'Sala', 1)
          ->obrigatorio('inicio', 'Início')->dataHora('inicio', 'Início', self::FORMATO_INPUT)
          ->obrigatorio('preco', 'Preço')->decimal('preco', 'Preço', 0.01)
          ->obrigatorio('idioma', 'Idioma')->opcoes('idioma', 'Idioma', Sessao::IDIOMAS);
        $erros = $v->erros();

        // Filme e sala precisam existir e estar ativos
        $filme = !isset($erros['filme_id']) ? (new Filme())->buscar((int) $dados['filme_id']) : null;
        if (!isset($erros['filme_id']) && ($filme === null || (int) $filme['ativo'] !== 1)) {
            $erros['filme_id'] = 'Filme inexistente ou inativo.';
        }

        $sala = !isset($erros['sala_id']) ? (new Sala())->buscar((int) $dados['sala_id']) : null;
        if (!isset($erros['sala_id']) && ($sala === null || (int) $sala['ativo'] !== 1)) {
            $erros['sala_id'] = 'Sala inexistente ou inativa.';
        }

        // Início deve ser no futuro
        $inicio = !isset($erros['inicio']) ? DateTime::createFromFormat(self::FORMATO_INPUT, $dados['inicio']) : false;
        if ($inicio !== false && $inicio <= new DateTime()) {
            $erros['inicio'] = 'Início deve ser uma data futura.';
        }

        // Com ingressos vendidos, a sala não pode mudar (os assentos pertencem a ela)
        if ($id > 0 && !isset($erros['sala_id']) && $this->sessoes->temIngressoVendido($id)) {
            $atual = $this->sessoes->buscar($id);
            if ((int) $atual['sala_id'] !== (int) $dados['sala_id']) {
                $erros['sala_id'] = 'Sessão com ingressos vendidos não pode trocar de sala.';
            }
        }

        if ($erros !== []) {
            return [$erros, []];
        }

        // fim = início + duração do filme + intervalo de limpeza
        $fim = (clone $inicio)->modify('+' . ((int) $filme['duracao_min'] + (int) config('intervalo_limpeza_min')) . ' minutes');

        $inicioBanco = $inicio->format(self::FORMATO_BANCO);
        $fimBanco    = $fim->format(self::FORMATO_BANCO);

        // Sobreposição de horário na mesma sala
        $conflito = $this->sessoes->buscarConflito((int) $sala['id'], $inicioBanco, $fimBanco, $id);
        if ($conflito !== null) {
            $erros['inicio'] = sprintf(
                'Conflito na %s: "%s" ocupa das %s às %s (inclui %d min de limpeza).',
                $sala['nome'],
                $conflito['filme_titulo'],
                date('H:i', strtotime($conflito['inicio'])),
                date('H:i', strtotime($conflito['fim'])),
                (int) config('intervalo_limpeza_min')
            );
            return [$erros, []];
        }

        $registro = [
            'filme_id' => (int) $filme['id'],
            'sala_id'  => (int) $sala['id'],
            'inicio'   => $inicioBanco,
            'fim'      => $fimBanco,
            'preco'    => round((float) str_replace(',', '.', $dados['preco']), 2),
            'idioma'   => $dados['idioma'],
        ];

        return [[], $registro];
    }

    private function renderizarForm(string $titulo, array $sessao, array $erros): void
    {
        $this->renderizar('sessoes/form', [
            'titulo' => $titulo,
            'sessao' => $sessao,
            'erros'  => $erros,
            'filmes' => (new Filme())->ativos(),
            'salas'  => (new Sala())->ativas(),
        ]);
    }

    /** @return array<string, mixed> */
    private function buscarOuRedirecionar(): array
    {
        $id     = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $sessao = $id > 0 ? $this->sessoes->buscar($id) : null;

        if ($sessao === null) {
            Session::flash('erro', 'Sessão não encontrada.');
            $this->redirecionar('sessoes');
        }
        return $sessao;
    }
}
