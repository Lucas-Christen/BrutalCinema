<?php
/**
 * Classe base de todos os Controllers.
 *
 * Fornece renderização de views dentro do layout e redirecionamento.
 * Os controllers concretos apenas decidem o que fazer e passam dados às views.
 */
abstract class Controller
{
    /**
     * Renderiza uma view dentro do layout (header + view + footer).
     *
     * @param string $view  Caminho relativo a app/Views, sem extensão. Ex.: 'filmes/lista'
     * @param array  $dados Variáveis disponíveis na view (extraídas com extract)
     */
    protected function renderizar(string $view, array $dados = []): void
    {
        $arquivo = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($arquivo)) {
            throw new RuntimeException("View não encontrada: {$view}");
        }

        // Disponibiliza as mensagens flash para o layout
        $flash = Session::obterFlash();

        extract($dados);

        require __DIR__ . '/../Views/layout/header.php';
        require $arquivo;
        require __DIR__ . '/../Views/layout/footer.php';
    }

    /**
     * Redireciona para outra página da aplicação e encerra a execução.
     */
    protected function redirecionar(string $page, string $acao = '', array $params = []): never
    {
        header('Location: ' . url($page, $acao, $params));
        exit;
    }

    /**
     * Retorna os dados enviados por POST, com espaços das extremidades removidos.
     * @return array<string, mixed>
     */
    protected function dadosPost(): array
    {
        $dados = [];
        foreach ($_POST as $chave => $valor) {
            $dados[$chave] = is_string($valor) ? trim($valor) : $valor;
        }
        return $dados;
    }

    protected function ehPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
