<?php
session_start();

// Se o usuário já estiver logado, redireciona para o painel de administração
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: admin.php");
    exit;
}

require 'db_connect.php';

$username = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validação básica
    if (empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
        $error_message = "Por favor, preencha o nome de usuário e a senha.";
    } else {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        // Prepara a consulta para buscar o usuário
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Verifica se o usuário existe
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $db_username, $hashed_password);
                    if ($stmt->fetch()) {
                        // Verifica se a senha está correta
                        if (password_verify($password, $hashed_password)) {
                            // Senha correta, inicia a sessão
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $db_username;                            
                            
                            // Redireciona para o painel de administração
                            header("location: admin.php");
                            exit;
                        } else {
                            $error_message = "A senha que você digitou não é válida.";
                        }
                    }
                } else {
                    $error_message = "Nenhum usuário encontrado com esse nome.";
                }
            } else {
                $error_message = "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rádio Central</title>
    <link rel="stylesheet" href="login-style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="assets/logo.png" alt="Rádio Central Logo" class="logo">
            <h2>Login do Painel</h2>
        </div>

        <?php 
        if (!empty($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>    
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn"><i class="ph ph-sign-in"></i>Entrar</button>
            </div>
        </form>
        <div class="back-link">
            <a href="index.html"><i class="ph ph-arrow-left"></i> Voltar ao site</a>
        </div>
    </div>
</body>
</html>