<?php
/**
 * Páginas de erro da aplicação.
 */
class ErroController extends Controller
{
    public function naoEncontrado(): void
    {
        http_response_code(404);
        $this->renderizar('erro/404', ['titulo' => 'Página não encontrada']);
    }

    public function interno(string $detalhe = ''): void
    {
        http_response_code(500);
        $this->renderizar('erro/500', ['titulo' => 'Erro interno', 'detalhe' => $detalhe]);
    }
}
