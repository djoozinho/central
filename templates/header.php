<?php
    // Define um título padrão que pode ser sobrescrito pela página que o inclui.
    $page_title = isset($page_title) ? $page_title : 'Rádio Central - Sua Conexão Musical';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Links para CSS e Fontes -->
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Ícones -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <img src="assets/logo.png" alt="Rádio Central Logo">
                <h1>Rádio Central</h1>
            </a>
            
            <nav class="main-nav">
                 <button id="close-menu-btn" class="close-menu-button"><i class="ph ph-x"></i></button>
                <ul class="nav-list">
                    <li><a href="index.php"><i class="ph ph-house"></i> <span>Início</span></a></li>
                    <li><a href="generos.php"><i class="ph ph-radio"></i> <span>Gêneros</span></a></li>
                    <li><a href="top-musicas.php"><i class="ph ph-music-notes"></i> <span>Top Músicas</span></a></li>
                    <li><a href="contato.php"><i class="ph ph-megaphone"></i> <span>Contato</span></a></li>
                    <!-- NOVO LINK PARA A PÁGINA "ANUNCIE" -->
                    <li><a href="anuncie.php"><i class="ph ph-currency-circle-dollar"></i> <span>Anuncie</span></a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <a href="login.php" class="login-button"><i class="ph ph-user-circle"></i> <span>Login</span></a>
                <button id="mobile-menu-toggle" class="mobile-menu-button"><i class="ph ph-list"></i></button>
            </div>
        </div>
    </header>
    
    <!-- O container principal onde o conteúdo de cada página será inserido -->
    <main id="pjax-container" class="main-content">