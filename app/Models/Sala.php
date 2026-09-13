<?php
/**
 * Acesso à tabela salas.
 * Capacidade não é armazenada: é fileiras * assentos_por_fileira.
 */
class Sala extends Model
{
    protected string $tabela = 'salas';

    /** Espelha o ENUM tipo do banco */
    public const TIPOS = ['2D', '3D', 'IMAX'];

    /** @return array<int, array<string, mixed>> */
    public function todos(string $ordem = 'nome'): array
    {
        return parent::todos($ordem);
    }

    /** @return array<int, array<string, mixed>> */
    public function ativas(): array
    {
        return $this->consultar("SELECT * FROM {$this->tabela} WHERE ativo = 1 ORDER BY nome");
    }

    /**
     * Verifica se já existe outra sala com o mesmo nome.
     * $ignorarId evita que a sala em edição conflite com ela mesma.
     */
    public function nomeExiste(string $nome, int $ignorarId = 0): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM {$this->tabela} WHERE nome = ? AND id <> ?",
            [$nome, $ignorarId]
        );
        return (int) $linha['total'] > 0;
    }

    public function temSessaoFutura(int $id): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM sessoes
             WHERE sala_id = ? AND status = 'agendada' AND inicio > NOW()",
            [$id]
        );
        return (int) $linha['total'] > 0;
    }

    /**
     * Maior posição de assento já vendida em sessões futuras da sala.
     * Fileira é convertida de letra para número (A = 1).
     * Usado para impedir que a sala seja reduzida abaixo de um assento vendido.
     *
     * @return array{fileira: int, numero: int}
     */
    public function maiorAssentoVendido(int $id): array
    {
        $linha = $this->consultarUm(
            "SELECT COALESCE(MAX(ORD(i.fileira) - 64), 0) AS fileira,
                    COALESCE(MAX(i.numero), 0)            AS numero
             FROM ingressos i
             JOIN sessoes s ON s.id = i.sessao_id
             WHERE s.sala_id = ? AND s.status = 'agendada' AND s.inicio > NOW()",
            [$id]
        );
        return ['fileira' => (int) $linha['fileira'], 'numero' => (int) $linha['numero']];
    }

    public function alterarAtivo(int $id, bool $ativo): bool
    {
        return $this->atualizar($id, ['ativo' => $ativo ? 1 : 0]);
    }
}
