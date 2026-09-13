<?php
/**
 * Acesso à tabela usuarios.
 */
class Usuario extends Model
{
    protected string $tabela = 'usuarios';

    public const PERFIS = ['admin', 'funcionario', 'cliente'];

    /** @return array<string, mixed>|null */
    public function buscarPorEmail(string $email): ?array
    {
        return $this->consultarUm("SELECT * FROM {$this->tabela} WHERE email = ?", [$email]);
    }

    /**
     * Verifica se o e-mail já pertence a outro usuário.
     * $ignorarId evita conflito do usuário com ele mesmo na edição.
     */
    public function emailExiste(string $email, int $ignorarId = 0): bool
    {
        $linha = $this->consultarUm(
            "SELECT COUNT(*) AS total FROM {$this->tabela} WHERE email = ? AND id <> ?",
            [$email, $ignorarId]
        );
        return (int) $linha['total'] > 0;
    }

    /**
     * Cria um usuário gerando o hash da senha. Retorna o id.
     */
    public function criar(string $nome, string $email, string $senha, string $perfil = 'cliente'): int
    {
        return $this->inserir([
            'nome'       => $nome,
            'email'      => $email,
            'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'perfil'     => $perfil,
        ]);
    }

    /**
     * Confere e-mail e senha. Retorna o usuário se as credenciais forem
     * válidas e a conta estiver ativa; caso contrário, null.
     * @return array<string, mixed>|null
     */
    public function autenticar(string $email, string $senha): ?array
    {
        $usuario = $this->buscarPorEmail($email);

        if ($usuario === null || (int) $usuario['ativo'] !== 1) {
            return null;
        }
        if (!password_verify($senha, $usuario['senha_hash'])) {
            return null;
        }
        return $usuario;
    }
}
