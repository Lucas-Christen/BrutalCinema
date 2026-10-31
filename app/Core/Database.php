<?php
namespace App\Core;

use PDO;

/**
 * Gerencia a conexão com o banco de dados via PDO.
 *
 * A conexão é aberta uma única vez por requisição e reaproveitada
 * por todos os Models (padrão Singleton).
 */
class Database
{
    private static ?PDO $conexao = null;

    /**
     * Retorna a conexão ativa, criando-a na primeira chamada.
     */
    public static function conectar(): PDO
    {
        if (self::$conexao === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = "mysql:host={$config['host']};port={$config['port']};"
                 . "dbname={$config['dbname']};charset={$config['charset']}";

            self::$conexao = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Prepared statements reais, executados pelo próprio banco
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // NOW() e CURDATE() seguem o mesmo fuso configurado no PHP
            self::$conexao->exec("SET time_zone = '" . date('P') . "'");
        }

        return self::$conexao;
    }
}
