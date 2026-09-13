<?php
/**
 * Página inicial. Será transformada em dashboard em passo posterior.
 */
class InicioController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $this->renderizar('inicio/index', ['titulo' => 'Início']);
    }
}
