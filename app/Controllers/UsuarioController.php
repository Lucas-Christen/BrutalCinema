<?php
/**
 * Gestão de usuários. Acesso exclusivo do admin.
 * Permite cadastrar funcionários/admins, alterar perfil e ativar/desativar contas.
 */
class UsuarioController extends Controller
{
    private Usuario $usuarios;

    public function __construct()
    {
        Auth::exigirPerfil('admin');
        $this->usuarios = new Usuario();
    }

    public function index(): void
    {
        $this->renderizar('usuarios/lista', [
            'titulo'   => 'Usuários',
            'usuarios' => $this->usuarios->todos(),
        ]);
    }

    public function novo(): void
    {
        $this->renderizar('usuarios/form', [
            'titulo'  => 'Novo usuário',
            'usuario' => [],
            'erros'   => [],
        ]);
    }

    public function editar(): void
    {
        $usuario = $this->buscarOuRedirecionar();

        $this->renderizar('usuarios/form', [
            'titulo'  => 'Editar usuário',
            'usuario' => $usuario,
            'erros'   => [],
        ]);
    }

    public function salvar(): void
    {
        if (!$this->ehPost()) {
            $this->redirecionar('usuarios');
        }

        $dados = $this->dadosPost();
        $id    = (int) ($dados['id'] ?? 0);
        $erros = $this->validar($dados, $id);

        if ($erros !== []) {
            $this->renderizar('usuarios/form', [
                'titulo'  => $id > 0 ? 'Editar usuário' : 'Novo usuário',
                'usuario' => $dados,
                'erros'   => $erros,
            ]);
            return;
        }

        if ($id > 0) {
            $registro = [
                'nome'   => $dados['nome'],
                'email'  => $dados['email'],
                'perfil' => $dados['perfil'],
            ];
            // Senha em branco na edição mantém a atual
            if ($dados['senha'] !== '') {
                $registro['senha_hash'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            $this->usuarios->atualizar($id, $registro);
            Session::flash('sucesso', 'Usuário atualizado com sucesso.');
        } else {
            $this->usuarios->criar($dados['nome'], $dados['email'], $dados['senha'], $dados['perfil']);
            Session::flash('sucesso', 'Usuário cadastrado com sucesso.');
        }

        $this->redirecionar('usuarios');
    }

    public function desativar(): void
    {
        if ($this->ehPost()) {
            $usuario = $this->buscarOuRedirecionar();

            if ((int) $usuario['id'] === Auth::id()) {
                Session::flash('erro', 'Você não pode desativar a própria conta.');
            } else {
                $this->usuarios->alterarAtivo((int) $usuario['id'], false);
                Session::flash('sucesso', 'Usuário desativado. Ele não conseguirá mais fazer login.');
            }
        }
        $this->redirecionar('usuarios');
    }

    public function ativar(): void
    {
        if ($this->ehPost()) {
            $usuario = $this->buscarOuRedirecionar();
            $this->usuarios->alterarAtivo((int) $usuario['id'], true);
            Session::flash('sucesso', 'Usuário reativado.');
        }
        $this->redirecionar('usuarios');
    }

    // ------------------------------------------------------------ internos

    /**
     * Senha é obrigatória só no cadastro. Na edição, em branco mantém a atual.
     * Admin não pode rebaixar o próprio perfil (evita ficar sem acesso à área).
     *
     * @return array<string, string>
     */
    private function validar(array $dados, int $id): array
    {
        $v = new Validator($dados);
        $v->obrigatorio('nome', 'Nome')->tamanho('nome', 'Nome', 2, 100)
          ->obrigatorio('email', 'E-mail')->email('email', 'E-mail')->tamanho('email', 'E-mail', 5, 150)
          ->obrigatorio('perfil', 'Perfil')->opcoes('perfil', 'Perfil', Usuario::PERFIS)
          ->tamanho('senha', 'Senha', 6, 72)
          ->igual('senha_confirmacao', 'Confirmação de senha', 'senha');

        if ($id === 0) {
            $v->obrigatorio('senha', 'Senha');
        }
        $erros = $v->erros();

        if (!isset($erros['email']) && $this->usuarios->emailExiste($dados['email'], $id)) {
            $erros['email'] = 'Este e-mail já está cadastrado.';
        }

        if ($id === Auth::id() && !isset($erros['perfil']) && $dados['perfil'] !== 'admin') {
            $erros['perfil'] = 'Você não pode alterar o próprio perfil.';
        }

        return $erros;
    }

    /** @return array<string, mixed> */
    private function buscarOuRedirecionar(): array
    {
        $id      = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $usuario = $id > 0 ? $this->usuarios->buscar($id) : null;

        if ($usuario === null) {
            Session::flash('erro', 'Usuário não encontrado.');
            $this->redirecionar('usuarios');
        }
        return $usuario;
    }
}
