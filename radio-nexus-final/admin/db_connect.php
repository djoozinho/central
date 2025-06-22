<?php
$host = 'localhost';
$user = 'root'; // Usuário padrão do XAMPP
$pass = '';     // Senha padrão do XAMPP
$db = 'radio_nexus_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>