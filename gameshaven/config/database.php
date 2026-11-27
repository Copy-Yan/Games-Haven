<?php
/**
 * Conexão com o banco de dados GamesHaven.
 * Ajuste DB_HOST / DB_NAME / DB_USER / DB_PASS se necessário.
 * No XAMPP padrão: usuário "root" e senha vazia.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'gameshaven');
define('DB_USER', 'root');
define('DB_PASS', '');

function getConexao() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}
