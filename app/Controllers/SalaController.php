<?php
/**
 * CRUD de salas. Listagem aberta a todos os logados; alterações só para admin.
 */
class SalaController extends Controller
{
    private Sala $salas;

    public function __construct()
    {
        $this->salas = new Sala();
    }

    public function index(): void
    {
        Auth::exigirLogin();

        $this->renderizar('salas/lista', [
            'titulo' => 'Salas',
            'salas'  => $this->salas->todos(),
        ]);
    }

    public function novo(): void
    {
        Auth::exigirPerfil('admin');

        $this->renderizar('salas/form', [
            'titulo' => 'Nova sala',
            'sala'   => [],
            'erros'  => [],
        ]);
    }

    public function editar(): void
    {
        Auth::exigirPerfil('admin');

        $sala = $this->buscarOuRedirecionar();

        $this->renderizar('salas/form', [
            'titulo' => 'Editar sala',
            'sala'   => $sala,
            'erros'  => [],
        ]);
    }

    public function salvar(): void
    {
        Auth::exigirPerfil('admin');

        if (!$this->ehPost()) {
            $this->redirecionar('salas');
        }

        $dados = $this->dadosPost();
        $id    = (int) ($dados['id'] ?? 0);
        $erros = $this->validar($dados, $id);

        if ($erros !== []) {
            $this->renderizar('salas/form', [
                'titulo' => $id > 0 ? 'Editar sala' : 'Nova sala',
                'sala'   => $dados,
                'erros'  => $erros,
            ]);
            return;
        }

        $registro = [
            'nome'                 => $dados['nome'],
            'fileiras'             => (int) $dados['fileiras'],
            'assentos_por_fileira' => (int) $dados['assentos_por_fileira'],
            'tipo'                 => $dados['tipo'],
        ];

        if ($id > 0) {
            $this->salas->atualizar($id, $registro);
            Session::flash('sucesso', 'Sala atualizada com sucesso.');
        } else {
            $this->salas->inserir($registro);
            Session::flash('sucesso', 'Sala cadastrada com sucesso.');
        }

        $this->redirecionar('salas');
    }

    public function desativar(): void
    {
        Auth::exigirPerfil('admin');

        if ($this->ehPost()) {
            $sala = $this->buscarOuRedirecionar();

            if ($this->salas->temSessaoFutura((int) $sala['id'])) {
                Session::flash('erro', 'Não é possível desativar: a sala possui sessões futuras agendadas.');
            } else {
                $this->salas->alterarAtivo((int) $sala['id'], false);
                Session::flash('sucesso', 'Sala desativada.');
            }
        }
        $this->redirecionar('salas');
    }

    public function ativar(): void
    {
        Auth::exigirPerfil('admin');

        if ($this->ehPost()) {
            $sala = $this->buscarOuRedirecionar();
            $this->salas->alterarAtivo((int) $sala['id'], true);
            Session::flash('sucesso', 'Sala reativada.');
        }
        $this->redirecionar('salas');
    }

    // ------------------------------------------------------------ internos

    /**
     * Regras de validação. Além das regras de formato, verifica no banco:
     * nome único e, na edição, se a redução do tamanho deixaria algum
     * assento já vendido fora da sala.
     *
     * @return array<string, string>
     */
    private function validar(array $dados, int $id): array
    {
        $v = new Validator($dados);
        $v->obrigatorio('nome', 'Nome')->tamanho('nome', 'Nome', 2, 50)
          ->obrigatorio('fileiras', 'Fileiras')->inteiro('fileiras', 'Fileiras', 1, 26)
          ->obrigatorio('assentos_por_fileira', 'Assentos por fileira')->inteiro('assentos_por_fileira', 'Assentos por fileira', 1, 50)
          ->obrigatorio('tipo', 'Tipo')->opcoes('tipo', 'Tipo', Sala::TIPOS);
        $erros = $v->erros();

        if (!isset($erros['nome']) && $this->salas->nomeExiste($dados['nome'], $id)) {
            $erros['nome'] = 'Já existe uma sala com este nome.';
        }

        if ($id > 0 && !isset($erros['fileiras']) && !isset($erros['assentos_por_fileira'])) {
            $maior = $this->salas->maiorAssentoVendido($id);

            if ((int) $dados['fileiras'] < $maior['fileira']) {
                $erros['fileiras'] = "Há ingresso vendido na fileira " . chr(64 + $maior['fileira']) . ". Mínimo: {$maior['fileira']} fileiras.";
            }
            if ((int) $dados['assentos_por_fileira'] < $maior['numero']) {
                $erros['assentos_por_fileira'] = "Há ingresso vendido no assento {$maior['numero']}. Mínimo: {$maior['numero']} assentos por fileira.";
            }
        }

        return $erros;
    }

    /** @return array<string, mixed> */
    private function buscarOuRedirecionar(): array
    {
        $id   = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $sala = $id > 0 ? $this->salas->buscar($id) : null;

        if ($sala === null) {
            Session::flash('erro', 'Sala não encontrada.');
            $this->redirecionar('salas');
        }
        return $sala;
    }
}
