<?php
/**
 * Classe base de todos os Models.
 *
 * Concentra o acesso ao banco via mysqli com prepared statements.
 * Cada Model concreto define a tabela e adiciona consultas específicas.
 */
abstract class Model
{
    /** Nome da tabela no banco. Definido em cada Model concreto. */
    protected string $tabela;

    protected mysqli $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Retorna todos os registros da tabela.
     * @return array<int, array<string, mixed>>
     */
    public function todos(string $ordem = 'id'): array
    {
        return $this->consultar("SELECT * FROM {$this->tabela} ORDER BY {$ordem}");
    }

    /**
     * Busca um registro pela chave primária.
     * @return array<string, mixed>|null
     */
    public function buscar(int $id): ?array
    {
        return $this->consultarUm("SELECT * FROM {$this->tabela} WHERE id = ?", [$id]);
    }

    /**
     * Insere um registro e retorna o id gerado.
     * @param array<string, mixed> $dados coluna => valor
     */
    public function inserir(array $dados): int
    {
        $colunas = implode(', ', array_keys($dados));
        $marcadores = implode(', ', array_fill(0, count($dados), '?'));

        $sql = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ({$marcadores})";
        $this->executar($sql, array_values($dados));

        return (int) $this->db->insert_id;
    }

    /**
     * Atualiza um registro pela chave primária.
     * @param array<string, mixed> $dados coluna => valor
     */
    public function atualizar(int $id, array $dados): bool
    {
        $atribuicoes = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($dados)));

        $sql = "UPDATE {$this->tabela} SET {$atribuicoes} WHERE id = ?";
        $stmt = $this->executar($sql, [...array_values($dados), $id]);

        return $stmt->affected_rows >= 0;
    }

    /**
     * Exclui um registro pela chave primária.
     */
    public function excluir(int $id): bool
    {
        $stmt = $this->executar("DELETE FROM {$this->tabela} WHERE id = ?", [$id]);
        return $stmt->affected_rows > 0;
    }

    /**
     * Executa um SELECT e retorna todas as linhas.
     * @return array<int, array<string, mixed>>
     */
    protected function consultar(string $sql, array $params = []): array
    {
        $stmt = $this->executar($sql, $params);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Executa um SELECT e retorna apenas a primeira linha (ou null).
     * @return array<string, mixed>|null
     */
    protected function consultarUm(string $sql, array $params = []): ?array
    {
        $linhas = $this->consultar($sql, $params);
        return $linhas[0] ?? null;
    }

    /**
     * Prepara e executa uma query com os parâmetros informados.
     * Os tipos dos parâmetros (i, d, s) são deduzidos automaticamente.
     */
    protected function executar(string $sql, array $params = []): mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);

        if ($params !== []) {
            $tipos = '';
            foreach ($params as $valor) {
                $tipos .= match (true) {
                    is_int($valor)   => 'i',
                    is_float($valor) => 'd',
                    default          => 's',
                };
            }
            $stmt->bind_param($tipos, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }
}
