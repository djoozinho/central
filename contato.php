<?php
    $page_title = 'Contato - Rádio Central';
    $form_message = '';
    $form_status = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = strip_tags(trim($_POST["name"]));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $subject = strip_tags(trim($_POST["subject"]));
        $message = strip_tags(trim($_POST["message"]));

        if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form_message = "Por favor, preencha todos os campos corretamente.";
            $form_status = 'error';
        } else {
            $form_message = "Obrigado por entrar em contato! Sua mensagem foi enviada com sucesso.";
            $form_status = 'success';
        }
    }

    include 'templates/header.php';
?>

<!-- Conteúdo específico da página de Contato -->
<div class="container page-section">

    <div class="page-header">
        <h2 class="section-title">Fale Conosco</h2>
        <p class="page-subtitle">Tem alguma dúvida, sugestão ou quer anunciar? Use os canais abaixo para entrar em contato conosco.</p>
    </div>

    <!-- Cards de Informação de Contato -->
    <div class="contact-info-cards">
        <div class="info-card">
            <div class="info-card-icon-wrapper">
                <i class="ph-fill ph-map-pin"></i>
            </div>
            <h3>Nosso Endereço</h3>
            <p>Av. Paulista, 1234<br>São Paulo, SP, Brasil</p>
        </div>
        <div class="info-card">
            <div class="info-card-icon-wrapper">
                <i class="ph-fill ph-envelope-simple"></i>
            </div>
            <h3>Envie um E-mail</h3>
            <p>contato@radiocentral.com</p>
        </div>
        <div class="info-card">
            <div class="info-card-icon-wrapper">
                <i class="ph-fill ph-phone"></i>
            </div>
            <h3>Ligue para Nós</h3>
            <p>+55 (11) 98765-4321</p>
        </div>
    </div>


    <div class="contact-form-layout">
        
        <!-- Coluna do Formulário -->
        <div class="contact-form-wrapper modern-form">
            <h3>Ou nos envie uma mensagem direta</h3>
            
            <?php if (!empty($form_message)): ?>
                <div class="form-feedback <?php echo $form_status; ?>">
                    <?php echo $form_message; ?>
                </div>
            <?php endif; ?>

            <form action="contato.php" method="post" class="contact-form">
                <!-- NOVA ESTRUTURA PARA LABELS FLUTUANTES -->
                <div class="form-group floating-label">
                    <input type="text" id="name" name="name" required placeholder=" ">
                    <label for="name">Seu Nome</label>
                </div>
                <div class="form-group floating-label">
                    <input type="email" id="email" name="email" required placeholder=" ">
                    <label for="email">Seu E-mail</label>
                </div>
                <div class="form-group floating-label">
                    <input type="text" id="subject" name="subject" required placeholder=" ">
                    <label for="subject">Assunto</label>
                </div>
                <div class="form-group floating-label">
                    <textarea id="message" name="message" rows="6" required placeholder=" "></textarea>
                    <label for="message">Sua Mensagem</label>
                </div>
                <button type="submit" class="btn-submit">Enviar Mensagem <i class="ph ph-paper-plane-tilt"></i></button>
            </form>
        </div>

        <!-- Coluna do Mapa -->
        <div class="map-wrapper modern-map">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.145833475143!2d-46.65889138487739!3d-23.56294428468165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce59c8da0aa315%3A0xd59f9431f2c9776a!2sAv.%20Paulista%2C%20S%C3%A3o%20Paulo%20-%20SP!5e0!3m2!1spt-BR!2sbr!4v1678886543210!5m2!1spt-BR!2sbr" 
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</div>

<?php 
    include 'templates/footer.php'; 
?>