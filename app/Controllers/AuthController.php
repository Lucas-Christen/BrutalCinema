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
     * Encerra a sessão. Aceita apenas POST (botão "Sair" do menu).
     */
    public function sair(): void
    {
        if ($this->ehPost()) {
            Auth::logout();
        }
        $this->redirecionar('login');
    }
}
