<?php
// conexao.php
$host = 'localhost';
$dbname = 'afterlife_db';
$username = 'root'; // Padrão do XAMPP
$password = '';     // Padrão do XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>