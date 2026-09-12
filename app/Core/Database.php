<?php
/**
 * Gerencia a conexão com o banco de dados via mysqli.
 *
 * A conexão é aberta uma única vez por requisição e reaproveitada
 * por todos os Models (padrão Singleton).
 */
class Database
{
    private static ?mysqli $conexao = null;

    /**
     * Retorna a conexão ativa, criando-a na primeira chamada.
     */
    public static function conectar(): mysqli
    {
        if (self::$conexao === null) {
            $config = require __DIR__ . '/../../config/database.php';

            // Faz o mysqli lançar exceções em vez de retornar false silenciosamente
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            self::$conexao = new mysqli(
                $config['host'],
                $config['user'],
                $config['pass'],
                $config['dbname'],
                $config['port']
            );
            self::$conexao->set_charset($config['charset']);
        }

        return self::$conexao;
    }
}
