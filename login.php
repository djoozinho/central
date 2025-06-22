<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: admin.php");
    exit;
}

require 'db_connect.php';

$username = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
        $error_message = "Por favor, preencha o nome de usuário e a senha.";
    } else {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $db_username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $db_username;                            
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
                $error_message = "Oops! Algo deu errado. Tente novamente mais tarde.";
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
    <title>Login - Painel Rádio Central</title>
    <link rel="stylesheet" href="login-style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <a href="index.php">
                    <img src="assets/logo.png" alt="Rádio Central Logo" class="logo">
                </a>
                <h2>Acesso ao Painel</h2>
                <p>Entre com suas credenciais para gerenciar o site.</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="ph-fill ph-warning-circle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
                <!-- NOVA ESTRUTURA COM LABELS FLUTUANTES E ÍCONES -->
                <div class="form-group floating-label with-icon">
                    <i class="ph ph-user"></i>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required placeholder=" ">
                    <label for="username">Usuário</label>
                </div>    
                <div class="form-group floating-label with-icon">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="password" id="password" required placeholder=" ">
                    <label for="password">Senha</label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn"><i class="ph ph-sign-in"></i>Entrar</button>
                </div>
            </form>
            <div class="back-link">
                <a href="index.php"><i class="ph ph-arrow-left"></i> Voltar ao site principal</a>
            </div>
        </div>
    </div>
</body>
</html>