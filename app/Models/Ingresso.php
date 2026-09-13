<?php
/**
 * Acesso à tabela ingressos.
 * Um ingresso ocupa um assento (fileira + número) de uma sessão.
 * Cancelar um ingresso é excluí-lo, o que libera o assento.
 */
class Ingresso extends Model
{
    protected string $tabela = 'ingressos';

    public const TIPOS = ['inteira', 'meia'];

    /**
     * Assentos já vendidos de uma sessão, no formato "A-5".
     * @return array<int, string>
     */
    public function assentosOcupados(int $sessaoId): array
    {
        $linhas = $this->consultar(
            "SELECT fileira, numero FROM {$this->tabela} WHERE sessao_id = ?",
            [$sessaoId]
        );
        return array_map(fn($l) => $l['fileira'] . '-' . $l['numero'], $linhas);
    }

    /**
     * Grava vários ingressos em uma única transação.
     * Se qualquer inserção falhar (ex.: assento vendido por outro usuário
     * no mesmo instante), todas são desfeitas e a exceção é repassada.
     *
     * @param array<int, array<string, mixed>> $registros
     */
    public function venderVarios(array $registros): void
    {
        $this->db->begin_transaction();
        try {
            foreach ($registros as $registro) {
                $this->inserir($registro);
            }
            $this->db->commit();
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function assentoOcupado(int $sessaoId, string $fileira, int $numero): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM {$this->tabela}
             WHERE sessao_id = ? AND fileira = ? AND numero = ?",
            [$sessaoId, $fileira, $numero]
        );
        return (int) $linha['total'] > 0;
    }

    /**
     * Quantidade e receita dos ingressos vendidos hoje.
     * @return array{quantidade: int, receita: float}
     */
    public function resumoHoje(): array
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor_pago), 0) AS receita
             FROM {$this->tabela} WHERE DATE(vendido_em) = CURDATE()"
        );
        return ['quantidade' => (int) $linha['quantidade'], 'receita' => (float) $linha['receita']];
    }

    /**
     * Próximos ingressos (sessões futuras) de um cliente, para o painel inicial.
     * @return array<int, array<string, mixed>>
     */
    public function proximosDoUsuario(int $usuarioId, int $limite = 3): array
    {
        return $this->consultar(
            "SELECT i.fileira, i.numero, s.inicio, f.titulo AS filme_titulo, sa.nome AS sala_nome
             FROM {$this->tabela} i
             JOIN sessoes s  ON s.id  = i.sessao_id
             JOIN filmes  f  ON f.id  = s.filme_id
             JOIN salas   sa ON sa.id = s.sala_id
             WHERE i.usuario_id = ? AND s.status = 'agendada' AND s.inicio > NOW()
             ORDER BY s.inicio
             LIMIT {$limite}",
            [$usuarioId]
        );
    }

    /**
     * Todos os ingressos com dados da sessão, filme, sala e de quem vendeu.
     * @return array<int, array<string, mixed>>
     */
    public function todosComDetalhes(): array
    {
        return $this->consultar(
            "SELECT i.*, s.inicio, f.titulo AS filme_titulo, sa.nome AS sala_nome, u.nome AS vendedor_nome
             FROM {$this->tabela} i
             JOIN sessoes  s  ON s.id  = i.sessao_id
             JOIN filmes   f  ON f.id  = s.filme_id
             JOIN salas    sa ON sa.id = s.sala_id
             JOIN usuarios u  ON u.id  = i.usuario_id
             ORDER BY i.vendido_em DESC"
        );
    }

    /**
     * Ingressos comprados por um usuário (tela "meus ingressos").
     * @return array<int, array<string, mixed>>
     */
    public function doUsuario(int $usuarioId): array
    {
        return $this->consultar(
            "SELECT i.*, s.inicio, s.fim, s.idioma, s.status AS sessao_status,
                    f.titulo AS filme_titulo, f.classificacao, sa.nome AS sala_nome, sa.tipo AS sala_tipo
             FROM {$this->tabela} i
             JOIN sessoes s  ON s.id  = i.sessao_id
             JOIN filmes  f  ON f.id  = s.filme_id
             JOIN salas   sa ON sa.id = s.sala_id
             WHERE i.usuario_id = ?
             ORDER BY s.inicio DESC",
            [$usuarioId]
        );
    }
}
