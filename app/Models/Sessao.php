<?php
/**
 * Acesso à tabela sessoes.
 * Uma sessão é um filme exibido em uma sala em um horário.
 */
class Sessao extends Model
{
    protected string $tabela = 'sessoes';

    public const IDIOMAS = ['dublado', 'legendado'];
    public const STATUS  = ['agendada', 'cancelada', 'encerrada'];

    /**
     * Todas as sessões com título do filme e nome da sala, mais recentes primeiro.
     * @return array<int, array<string, mixed>>
     */
    public function todasComDetalhes(): array
    {
        return $this->consultar(
            "SELECT s.*, f.titulo AS filme_titulo, f.duracao_min, sa.nome AS sala_nome
             FROM {$this->tabela} s
             JOIN filmes f  ON f.id  = s.filme_id
             JOIN salas  sa ON sa.id = s.sala_id
             ORDER BY s.inicio DESC"
        );
    }

    /** @return array<string, mixed>|null */
    public function buscarComDetalhes(int $id): ?array
    {
        return $this->consultarUm(
            "SELECT s.*, f.titulo AS filme_titulo, f.duracao_min, f.classificacao,
                    sa.nome AS sala_nome, sa.fileiras, sa.assentos_por_fileira, sa.tipo AS sala_tipo
             FROM {$this->tabela} s
             JOIN filmes f  ON f.id  = s.filme_id
             JOIN salas  sa ON sa.id = s.sala_id
             WHERE s.id = ?",
            [$id]
        );
    }

    /**
     * Procura outra sessão agendada na mesma sala cujo período se sobreponha
     * ao intervalo [inicio, fim). Retorna a sessão conflitante ou null.
     *
     * Dois períodos se sobrepõem quando um começa antes do outro terminar
     * e termina depois do outro começar.
     *
     * @return array<string, mixed>|null
     */
    public function buscarConflito(int $salaId, string $inicio, string $fim, int $ignorarId = 0): ?array
    {
        return $this->consultarUm(
            "SELECT s.inicio, s.fim, f.titulo AS filme_titulo
             FROM {$this->tabela} s
             JOIN filmes f ON f.id = s.filme_id
             WHERE s.sala_id = ? AND s.status = 'agendada' AND s.id <> ?
               AND s.inicio < ? AND s.fim > ?
             LIMIT 1",
            [$salaId, $ignorarId, $fim, $inicio]
        );
    }

    public function temIngressoVendido(int $id): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM ingressos WHERE sessao_id = ?",
            [$id]
        );
        return (int) $linha['total'] > 0;
    }

    public function cancelar(int $id): bool
    {
        return $this->atualizar($id, ['status' => 'cancelada']);
    }
}
