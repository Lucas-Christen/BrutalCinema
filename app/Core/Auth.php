<?php
/**
 * Controle de autenticação e autorização baseado em sessão.
 *
 * Guarda na sessão apenas id, nome e perfil do usuário logado.
 * Os controllers chamam exigirLogin() / exigirPerfil() no início
 * de cada ação protegida.
 */
class Auth
{
    private const CHAVE = 'usuario';

    /**
     * Registra o usuário na sessão após login bem-sucedido.
     * @param array<string, mixed> $usuario linha da tabela usuarios
     */
    public static function login(array $usuario): void
    {
        // Novo id de sessão evita que um id conhecido antes do login seja reaproveitado
        session_regenerate_id(true);

        Session::set(self::CHAVE, [
            'id'     => (int) $usuario['id'],
            'nome'   => $usuario['nome'],
            'perfil' => $usuario['perfil'],
        ]);
    }

    public static function logout(): void
    {
        Session::destruir();
    }

    public static function logado(): bool
    {
        return Session::tem(self::CHAVE);
    }

    /** @return array{id: int, nome: string, perfil: string}|null */
    public static function usuario(): ?array
    {
        return Session::get(self::CHAVE);
    }

    public static function id(): ?int
    {
        return self::usuario()['id'] ?? null;
    }

    public static function perfil(): ?string
    {
        return self::usuario()['perfil'] ?? null;
    }

    /**
     * Verifica se o usuário logado possui um dos perfis informados.
     */
    public static function temPerfil(string ...$perfis): bool
    {
        return in_array(self::perfil(), $perfis, true);
    }

    /**
     * Bloqueia acesso de visitantes: redireciona para o login.
     */
    public static function exigirLogin(): void
    {
        if (!self::logado()) {
            Session::flash('erro', 'Faça login para acessar esta página.');
            header('Location: ' . url('login'));
            exit;
        }
    }

    /**
     * Bloqueia acesso de usuários sem o perfil necessário.
     */
    public static function exigirPerfil(string ...$perfis): void
    {
        self::exigirLogin();

        if (!self::temPerfil(...$perfis)) {
            Session::flash('erro', 'Você não tem permissão para acessar esta página.');
            header('Location: ' . url('inicio'));
            exit;
        }
    }
}
