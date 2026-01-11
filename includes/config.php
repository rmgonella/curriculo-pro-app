<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u591057133_cv2');
define('DB_USER', 'u591057133_cv2');
define('DB_PASS', '^3Yn*O5W:');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    $pdo->exec("SET SESSION autocommit=1");
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

define('BASE_URL', 'https://cv.techinnovationbr.com.br');
session_start();

// Exibição de erros habilitada para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
