<?php
/**
 * Login e logout.
 */
class AuthController extends Controller
{
    /**
     * GET: exibe o formulário. POST: valida e autentica.
     */
    public function index(): void
    {
        if (Auth::logado()) {
            $this->redirecionar('inicio');
        }

        $dados = [];
        $erros = [];
        $erroGeral = '';

        if ($this->ehPost()) {
            $dados = $this->dadosPost();

            $v = new Validator($dados);
            $v->obrigatorio('email', 'E-mail')->email('email', 'E-mail')
              ->obrigatorio('senha', 'Senha');
            $erros = $v->erros();

            if ($erros === []) {
                $usuario = (new Usuario())->autenticar($dados['email'], $dados['senha']);

                if ($usuario !== null) {
                    Auth::login($usuario);
                    Session::flash('sucesso', 'Bem-vindo, ' . $usuario['nome'] . '!');
                    $this->redirecionar('inicio');
                }
                // Mensagem genérica: não revela se o e-mail existe
                $erroGeral = 'E-mail ou senha inválidos.';
            }
        }

        $this->renderizar('auth/login', [
            'titulo'    => 'Login',
            'dados'     => $dados,
            'erros'     => $erros,
            'erroGeral' => $erroGeral,
        ]);
    }

    /**
     * Cadastro público. Todo usuário criado por aqui recebe o perfil "cliente".
     * GET: exibe o formulário. POST: valida, grava e já autentica.
     */
    public function registro(): void
    {
        if (Auth::logado()) {
            $this->redirecionar('inicio');
        }

        $dados = [];
        $erros = [];

        if ($this->ehPost()) {
            $dados = $this->dadosPost();
            $erros = $this->validarRegistro($dados);

            if ($erros === []) {
                $usuarios = new Usuario();
                $id = $usuarios->criar($dados['nome'], $dados['email'], $dados['senha']);

                Auth::login($usuarios->buscar($id));
                Session::flash('sucesso', 'Cadastro realizado. Bem-vindo, ' . $dados['nome'] . '!');
                $this->redirecionar('inicio');
            }
        }

        $this->renderizar('auth/registro', [
            'titulo' => 'Criar conta',
            'dados'  => $dados,
            'erros'  => $erros,
        ]);
    }

    /**
     * Encerra a sessão. Aceita apenas POST (botão "Sair" do menu).
     */
    public function sair(): void
    {
        if ($this->ehPost()) {
            Auth::logout();
        }
        $this->redirecionar('login');
    }

    // ------------------------------------------------------------ internos

    /**
     * Regras do formulário de cadastro.
     * @return array<string, string>
     */
    private function validarRegistro(array $dados): array
    {
        $v = new Validator($dados);
        $v->obrigatorio('nome', 'Nome')->tamanho('nome', 'Nome', 2, 100)
          ->obrigatorio('email', 'E-mail')->email('email', 'E-mail')->tamanho('email', 'E-mail', 5, 150)
          ->obrigatorio('senha', 'Senha')->tamanho('senha', 'Senha', 6, 72)
          ->obrigatorio('senha_confirmacao', 'Confirmação de senha')->igual('senha_confirmacao', 'Confirmação de senha', 'senha');
        $erros = $v->erros();

        if (!isset($erros['email']) && (new Usuario())->emailExiste($dados['email'])) {
            $erros['email'] = 'Este e-mail já está cadastrado.';
        }

        return $erros;
    }
}
