<?php
require 'conexao.php';

$nome = "NOME";
$email = "EMAIL";
$senha = "SENHA"; // Troque pela senha que desejar

// Criptografa a senha com segurança máxima
$hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $hash]);
    echo "Usuário criado com sucesso! Você já pode apagar este arquivo e fazer login.";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
