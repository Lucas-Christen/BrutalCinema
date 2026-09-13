<?php
/**
 * Venda (balcão) e compra (cliente) de ingressos, com mapa de assentos.
 */
class IngressoController extends Controller
{
    private Ingresso $ingressos;
    private Sessao $sessoes;

    public function __construct()
    {
        $this->ingressos = new Ingresso();
        $this->sessoes   = new Sessao();
    }

    /**
     * Lista de ingressos vendidos (admin e funcionário).
     * Cliente que cair aqui é levado para "meus ingressos".
     */
    public function index(): void
    {
        Auth::exigirLogin();

        if (Auth::temPerfil('cliente')) {
            $this->redirecionar('ingressos', 'meus');
        }
        Auth::exigirPerfil('admin', 'funcionario');

        $this->renderizar('ingressos/lista', [
            'titulo'    => 'Ingressos vendidos',
            'ingressos' => $this->ingressos->todosComDetalhes(),
        ]);
    }

    /**
     * Ingressos do cliente logado.
     */
    public function meus(): void
    {
        Auth::exigirPerfil('cliente');

        $this->renderizar('ingressos/meus', [
            'titulo'    => 'Meus ingressos',
            'ingressos' => $this->ingressos->doUsuario(Auth::id()),
        ]);
    }

    /**
     * Venda no balcão: funcionário informa nome e CPF do cliente.
     */
    public function vender(): void
    {
        Auth::exigirPerfil('admin', 'funcionario');
        $this->processar(balcao: true);
    }

    /**
     * Compra pelo próprio cliente: nome vem da conta, CPF é informado.
     */
    public function comprar(): void
    {
        Auth::exigirPerfil('cliente');
        $this->processar(balcao: false);
    }

    /**
     * Cancela um ingresso excluindo-o, o que libera o assento (admin e funcionário).
     */
    public function cancelar(): void
    {
        Auth::exigirPerfil('admin', 'funcionario');

        if ($this->ehPost()) {
            $id       = (int) ($_POST['id'] ?? 0);
            $ingresso = $id > 0 ? $this->ingressos->buscar($id) : null;

            if ($ingresso === null) {
                Session::flash('erro', 'Ingresso não encontrado ou já cancelado.');
            } else {
                $this->ingressos->excluir($id);
                Session::flash('sucesso', "Ingresso {$ingresso['fileira']}{$ingresso['numero']} cancelado. Assento liberado.");
            }
        }
        $this->redirecionar('ingressos');
    }

    // ------------------------------------------------------------ internos

    /** Limite de assentos por venda/compra */
    private const MAX_ASSENTOS = 10;

    /**
     * Fluxo comum de venda e compra.
     * GET: exibe mapa de assentos e formulário. POST: valida e grava.
     * Vários assentos podem ser escolhidos de uma vez; todos ficam com o
     * mesmo cliente e o mesmo tipo (inteira/meia).
     */
    private function processar(bool $balcao): void
    {
        $sessaoId = (int) ($_GET['sessao_id'] ?? $_POST['sessao_id'] ?? 0);
        $sessao   = $sessaoId > 0 ? $this->sessoes->buscarComDetalhes($sessaoId) : null;

        // Só é possível vender para sessão agendada e ainda não iniciada
        if ($sessao === null || $sessao['status'] !== 'agendada' || $sessao['inicio'] <= date('Y-m-d H:i:s')) {
            Session::flash('erro', 'Sessão indisponível para venda de ingressos.');
            $this->redirecionar('sessoes');
        }

        $dados = [];
        $erros = [];

        if ($this->ehPost()) {
            $dados = $this->dadosPost();
            $dados['assentos'] = is_array($dados['assentos'] ?? null) ? $dados['assentos'] : [];

            $erros = $this->validar($dados, $sessao, $balcao);

            if ($erros === []) {
                $preco = (float) $sessao['preco'];
                $base  = [
                    'sessao_id'    => $sessaoId,
                    'usuario_id'   => Auth::id(),
                    'nome_cliente' => $balcao ? $dados['nome_cliente'] : Auth::usuario()['nome'],
                    'cpf_cliente'  => preg_replace('/\D/', '', $dados['cpf_cliente']),
                    'tipo'         => $dados['tipo'],
                    // Valor calculado no servidor: meia-entrada paga 50%
                    'valor_pago'   => $dados['tipo'] === 'meia' ? round($preco / 2, 2) : $preco,
                ];

                $registros = [];
                foreach ($dados['assentos'] as $codigo) {
                    [$fileira, $numero] = explode('-', $codigo);
                    $registros[] = $base + ['fileira' => $fileira, 'numero' => (int) $numero];
                }

                try {
                    // Tudo ou nada: se um assento falhar, nenhum é gravado
                    $this->ingressos->venderVarios($registros);
                } catch (mysqli_sql_exception $e) {
                    // 1062 = violação de UNIQUE: outro usuário comprou um dos assentos neste instante
                    if ($e->getCode() === 1062) {
                        $erros['assentos'] = 'Um dos assentos acabou de ser vendido. Escolha novamente.';
                    } else {
                        throw $e;
                    }
                }

                if ($erros === []) {
                    $lista = implode(', ', array_map(fn($c) => str_replace('-', '', $c), $dados['assentos']));
                    $plural = count($registros) > 1;
                    Session::flash('sucesso', sprintf(
                        '%s %s %s com sucesso.',
                        $plural ? 'Ingressos' : 'Ingresso',
                        $lista,
                        $balcao ? ($plural ? 'vendidos' : 'vendido') : ($plural ? 'comprados' : 'comprado')
                    ));
                    $this->redirecionar('ingressos', $balcao ? '' : 'meus');
                }
            }
        }

        $this->renderizar('ingressos/mapa', [
            'titulo'      => $balcao ? 'Vender ingressos' : 'Comprar ingressos',
            'sessao'      => $sessao,
            'ocupados'    => $this->ingressos->assentosOcupados($sessaoId),
            'dados'       => $dados,
            'erros'       => $erros,
            'balcao'      => $balcao,
            'maxAssentos' => self::MAX_ASSENTOS,
        ]);
    }

    /**
     * Regras do formulário. Cada assento chega como "A-5" (fileira-número).
     * @return array<string, string>
     */
    private function validar(array $dados, array $sessao, bool $balcao): array
    {
        $v = new Validator($dados);
        $v->obrigatorio('tipo', 'Tipo')->opcoes('tipo', 'Tipo', Ingresso::TIPOS)
          ->obrigatorio('cpf_cliente', 'CPF')->digitos('cpf_cliente', 'CPF', 11);

        if ($balcao) {
            $v->obrigatorio('nome_cliente', 'Nome do cliente')->tamanho('nome_cliente', 'Nome do cliente', 2, 100);
        }
        $erros = $v->erros();

        $assentos = $dados['assentos'];

        if ($assentos === []) {
            $erros['assentos'] = 'Selecione pelo menos um assento.';
            return $erros;
        }
        if (count($assentos) > self::MAX_ASSENTOS) {
            $erros['assentos'] = 'Máximo de ' . self::MAX_ASSENTOS . ' assentos por venda.';
            return $erros;
        }
        if (count($assentos) !== count(array_unique($assentos))) {
            $erros['assentos'] = 'Assento repetido na seleção.';
            return $erros;
        }

        foreach ($assentos as $codigo) {
            if (!preg_match('/^([A-Z])-(\d{1,2})$/', (string) $codigo, $m)) {
                $erros['assentos'] = 'Assento com formato inválido.';
                return $erros;
            }
            $fileira = $m[1];
            $numero  = (int) $m[2];
            $indiceFileira = ord($fileira) - 64;   // A = 1

            if ($indiceFileira > (int) $sessao['fileiras'] || $numero < 1 || $numero > (int) $sessao['assentos_por_fileira']) {
                $erros['assentos'] = "Assento {$fileira}{$numero} fora dos limites da sala.";
                return $erros;
            }
            if ($this->ingressos->assentoOcupado((int) $sessao['id'], $fileira, $numero)) {
                $erros['assentos'] = "Assento {$fileira}{$numero} já está ocupado.";
                return $erros;
            }
        }

        return $erros;
    }
}
