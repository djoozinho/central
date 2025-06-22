<?php
// Defina a senha que você deseja usar
$senha_plana = 'password123'; // Ou qualquer outra senha que você queira

// Criptografa a senha usando o algoritmo padrão do PHP
$hash = password_hash($senha_plana, PASSWORD_DEFAULT);

// Exibe a senha criptografada na tela
echo "Sua nova senha criptografada é: <br><br>";
echo "<strong>" . $hash . "</strong>";
echo "<br><br>";
echo "Copie esta string completa e siga as instruções para atualizar no phpMyAdmin.";
?>