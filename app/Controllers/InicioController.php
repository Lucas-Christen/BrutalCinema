<?php
/**
 * Página inicial. Será transformada em dashboard em passo posterior.
 */
class InicioController extends Controller
{
    public function index(): void
    {
        $this->renderizar('inicio/index', ['titulo' => 'Início']);
    }
}
