<?php
// conexao.php

$host = 'localhost'; // Geralmente 'localhost' se o banco estiver no mesmo servidor
$dbname = 'mais4240_analisador'; // O nome da base de dados que criámos
$user = 'mais4240_analisador'; // O seu utilizador do MySQL (mude no servidor de produção)
$pass = 'Mktrj001@!pu'; // A sua palavra-passe do MySQL (mude no servidor de produção)

try {
    // Cria a ligação usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configura o PDO para lançar exceções em caso de erro (facilita o debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Se a ligação falhar, interrompe o script e mostra o erro
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>