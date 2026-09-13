<?php
/**
 * Encapsula o uso de $_SESSION.
 *
 * Centraliza início da sessão, leitura/escrita de valores e
 * mensagens "flash" (exibidas uma única vez, na próxima requisição).
 */
class Session
{
    /**
     * Inicia a sessão, se ainda não estiver ativa.
     */
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $chave, mixed $valor): void
    {
        $_SESSION[$chave] = $valor;
    }

    public static function get(string $chave, mixed $padrao = null): mixed
    {
        return $_SESSION[$chave] ?? $padrao;
    }

    public static function tem(string $chave): bool
    {
        return isset($_SESSION[$chave]);
    }

    public static function remover(string $chave): void
    {
        unset($_SESSION[$chave]);
    }

    /**
     * Grava uma mensagem flash. Tipos usados: 'sucesso' e 'erro'.
     */
    public static function flash(string $tipo, string $mensagem): void
    {
        $_SESSION['_flash'][$tipo] = $mensagem;
    }

    /**
     * Devolve todas as mensagens flash e as apaga da sessão.
     * @return array<string, string>
     */
    public static function obterFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }

    /**
     * Encerra a sessão por completo (usado no logout).
     */
    public static function destruir(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
