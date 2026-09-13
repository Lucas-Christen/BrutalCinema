<?php
/**
 * Acesso à tabela filmes.
 */
class Filme extends Model
{
    protected string $tabela = 'filmes';

    /** Espelha o ENUM classificacao do banco */
    public const CLASSIFICACOES = ['L', '10', '12', '14', '16', '18'];

    /** @return array<int, array<string, mixed>> */
    public function todos(string $ordem = 'titulo'): array
    {
        return parent::todos($ordem);
    }

    /**
     * Apenas filmes ativos (usado ao montar sessões).
     * @return array<int, array<string, mixed>>
     */
    public function ativos(): array
    {
        return $this->consultar("SELECT * FROM {$this->tabela} WHERE ativo = 1 ORDER BY titulo");
    }

    /**
     * Verifica se o filme possui sessão agendada no futuro.
     * Usado para impedir a desativação de filmes em exibição.
     */
    public function temSessaoFutura(int $id): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM sessoes
             WHERE filme_id = ? AND status = 'agendada' AND inicio > NOW()",
            [$id]
        );
        return (int) $linha['total'] > 0;
    }

    public function alterarAtivo(int $id, bool $ativo): bool
    {
        return $this->atualizar($id, ['ativo' => $ativo ? 1 : 0]);
    }
}
