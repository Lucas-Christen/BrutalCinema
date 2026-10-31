<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Filme;
use App\Models\Ingresso;
use App\Models\Sala;
use App\Models\Sessao;

/**
 * Página inicial (painel). Conteúdo varia conforme o perfil:
 * - admin e funcionário: indicadores, próximas sessões com ocupação e atalhos;
 * - cliente: sessões em cartaz para compra e seus próximos ingressos.
 */
class InicioController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $sessoes = new Sessao();

        if (Auth::temPerfil('cliente')) {
            $this->renderizar('inicio/cliente', [
                'titulo'    => 'Início',
                'sessoes'   => $sessoes->proximas(12),
                'ingressos' => (new Ingresso())->proximosDoUsuario(Auth::id()),
            ]);
            return;
        }

        $this->renderizar('inicio/painel', [
            'titulo'        => 'Painel',
            'filmesAtivos'  => (new Filme())->contarAtivos(),
            'salasAtivas'   => (new Sala())->contarAtivas(),
            'sessoesHoje'   => $sessoes->contarHoje(),
            'vendasHoje'    => (new Ingresso())->resumoHoje(),
            'proximas'      => $sessoes->proximas(8),
        ]);
    }
}
