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
