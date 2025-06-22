<?php
// Inclui a conexão com o banco de dados
require 'db_connect.php';

// --- DEFINA AQUI O NOVO USUÁRIO ---
$username = 'joao';
$password = '123456'; 
// ----------------------------------

echo "<h1>Criando usuário: {$username}</h1>";

// 1. Criptografa a senha com o método mais seguro do PHP
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "<p>Senha original: {$password}</p>";
echo "<p>Senha criptografada (hash): {$hashed_password}</p>";

// 2. Prepara a query SQL para inserir o usuário de forma segura
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

// 3. Executa a query
if ($stmt->execute()) {
    echo "<h2><span style='color:green;'>SUCESSO:</span> Usuário '{$username}' criado com sucesso!</h2>";
    echo "<p style='color:red; font-weight:bold;'>AVISO: Delete este arquivo (create_user.php) do seu servidor agora mesmo por segurança.</p>";
} else {
    echo "<h2><span style='color:red;'>ERRO:</span> Não foi possível criar o usuário.</h2>";
    echo "<p>Detalhes do erro: " . $stmt->error . "</p>";
    echo "<p>Isso pode acontecer se o usuário '{$username}' já existir no banco de dados.</p>";
}

$stmt->close();
$conn->close();
?>