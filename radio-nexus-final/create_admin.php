<?php
// Inclui a conexão com o banco de dados
require 'db_connect.php';

// --- DEFINA AQUI SEU LOGIN E SENHA ---
$username = 'admin';
$password = 'senhaSuperSegura123'; // Troque por uma senha forte!
// ------------------------------------

// Criptografa a senha com o método mais seguro do PHP
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepara a query para inserir o usuário, evitando SQL injection
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Usuário '{$username}' criado com sucesso!<br>";
    echo "<b>AVISO IMPORTANTE:</b> Delete este arquivo (create_admin.php) do seu servidor agora mesmo por segurança.";
} else {
    echo "Erro ao criar o usuário: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>