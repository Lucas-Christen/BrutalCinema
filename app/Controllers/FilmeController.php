<?php
/**
 * CRUD de filmes. Listagem aberta a todos os logados; alterações só para admin.
 */
class FilmeController extends Controller
{
    private Filme $filmes;

    public function __construct()
    {
        $this->filmes = new Filme();
    }

    public function index(): void
    {
        Auth::exigirLogin();

        $this->renderizar('filmes/lista', [
            'titulo' => 'Filmes',
            'filmes' => $this->filmes->todos(),
        ]);
    }

    /**
     * Formulário de novo filme.
     */
    public function novo(): void
    {
        Auth::exigirPerfil('admin');

        $this->renderizar('filmes/form', [
            'titulo' => 'Novo filme',
            'filme'  => [],
            'erros'  => [],
        ]);
    }

    /**
     * Formulário de edição, preenchido com os dados atuais.
     */
    public function editar(): void
    {
        Auth::exigirPerfil('admin');

        $filme = $this->buscarOuRedirecionar();

        $this->renderizar('filmes/form', [
            'titulo' => 'Editar filme',
            'filme'  => $filme,
            'erros'  => [],
        ]);
    }

    /**
     * Recebe o POST do formulário (novo ou edição). Valida e grava.
     */
    public function salvar(): void
    {
        Auth::exigirPerfil('admin');

        if (!$this->ehPost()) {
            $this->redirecionar('filmes');
        }

        $dados = $this->dadosPost();
        $id    = (int) ($dados['id'] ?? 0);
        $erros = $this->validar($dados);

        if ($erros !== []) {
            // Reexibe o formulário com os valores digitados e os erros
            $this->renderizar('filmes/form', [
                'titulo' => $id > 0 ? 'Editar filme' : 'Novo filme',
                'filme'  => $dados,
                'erros'  => $erros,
            ]);
            return;
        }

        $registro = [
            'titulo'         => $dados['titulo'],
            'sinopse'        => $dados['sinopse'] !== '' ? $dados['sinopse'] : null,
            'duracao_min'    => (int) $dados['duracao_min'],
            'classificacao'  => $dados['classificacao'],
            'genero'         => $dados['genero'],
            'ano_lancamento' => $dados['ano_lancamento'] !== '' ? (int) $dados['ano_lancamento'] : null,
        ];

        if ($id > 0) {
            $this->filmes->atualizar($id, $registro);
            Session::flash('sucesso', 'Filme atualizado com sucesso.');
        } else {
            $this->filmes->inserir($registro);
            Session::flash('sucesso', 'Filme cadastrado com sucesso.');
        }

        $this->redirecionar('filmes');
    }

    /**
     * Desativa o filme (soft delete). Bloqueado se houver sessão futura.
     */
    public function desativar(): void
    {
        Auth::exigirPerfil('admin');

        if ($this->ehPost()) {
            $filme = $this->buscarOuRedirecionar();

            if ($this->filmes->temSessaoFutura((int) $filme['id'])) {
                Session::flash('erro', 'Não é possível desativar: o filme possui sessões futuras agendadas.');
            } else {
                $this->filmes->alterarAtivo((int) $filme['id'], false);
                Session::flash('sucesso', 'Filme desativado.');
            }
        }
        $this->redirecionar('filmes');
    }

    public function ativar(): void
    {
        Auth::exigirPerfil('admin');

        if ($this->ehPost()) {
            $filme = $this->buscarOuRedirecionar();
            $this->filmes->alterarAtivo((int) $filme['id'], true);
            Session::flash('sucesso', 'Filme reativado.');
        }
        $this->redirecionar('filmes');
    }

    // ------------------------------------------------------------ internos

    /**
     * Regras de validação do formulário.
     * @return array<string, string> campo => mensagem
     */
    private function validar(array $dados): array
    {
        $anoMaximo = (int) date('Y') + 2;

        $v = new Validator($dados);
        $v->obrigatorio('titulo', 'Título')->tamanho('titulo', 'Título', 2, 150)
          ->tamanho('sinopse', 'Sinopse', 0, 2000)
          ->obrigatorio('duracao_min', 'Duração')->inteiro('duracao_min', 'Duração', 1, 600)
          ->obrigatorio('classificacao', 'Classificação')->opcoes('classificacao', 'Classificação', Filme::CLASSIFICACOES)
          ->obrigatorio('genero', 'Gênero')->tamanho('genero', 'Gênero', 2, 50)
          ->inteiro('ano_lancamento', 'Ano de lançamento', 1888, $anoMaximo);

        return $v->erros();
    }

    /**
     * Busca o filme pelo ?id=. Se não existir, avisa e volta para a lista.
     * @return array<string, mixed>
     */
    private function buscarOuRedirecionar(): array
    {
        $id    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $filme = $id > 0 ? $this->filmes->buscar($id) : null;

        if ($filme === null) {
            Session::flash('erro', 'Filme não encontrado.');
            $this->redirecionar('filmes');
        }
        return $filme;
    }
}
