<?php
    // Define o título específico para esta página, que será usado no header.php
    $page_title = 'Anuncie Conosco - Rádio Central';
    
    // Inclui o cabeçalho universal do site
    include 'templates/header.php';
?>

<!-- O ID 'anuncie-page' é essencial para o script.js identificar e inicializar a lógica desta página -->
<div class="container page-section" id="anuncie-page">

    <div class="page-header">
        <h2 class="section-title">Impulsione sua Marca ou Música</h2>
        <p class="page-subtitle">Crie sua campanha de publicidade de forma fácil e intuitiva. Escolha o formato e alcance milhares de ouvintes.</p>
    </div>

    <!-- Seletor de Abas: Permite ao usuário escolher o tipo de anúncio -->
    <div class="ad-type-selector">
        <button class="ad-type-button active" data-type="audio"><i class="ph-fill ph-speaker-high"></i> Comercial de Áudio</button>
        <button class="ad-type-button" data-type="testimonial"><i class="ph-fill ph-microphone-stage"></i> Texto para Locutor</button>
        <button class="ad-type-button" data-type="music"><i class="ph-fill ph-music-notes-simple"></i> Divulgar Música</button>
    </div>

    <div class="anuncie-layout">
        
        <!-- Coluna Esquerda: Configuração da Campanha -->
        <div class="anuncie-main-col">
            
            <!-- Painel para Comercial de Áudio e Divulgação de Música -->
            <div id="panel-audio" class="ad-panel active">
                <div class="step-wrapper">
                    <h3><span class="step-number">1</span> Selecione a Rádio</h3>
                    <!-- Grade de rádios específica para este painel -->
                    <div id="audio-radio-grid" class="radio-grid ad-radio-grid">
                        <!-- Cards de Rádio serão inseridos aqui via JavaScript -->
                    </div>
                </div>

                <div id="drop-zone-wrapper" class="step-wrapper hidden">
                    <h3><span class="step-number">2</span> Envie seu Arquivo de Áudio</h3>
                    <div id="drop-zone" class="drop-zone">
                        <i class="ph-fill ph-upload-simple"></i>
                        <p><strong>Arraste e solte seu arquivo aqui</strong><br>(MP3 ou WAV)</p>
                        <span>ou</span>
                        <label for="file-input" class="btn-upload">Selecione o Arquivo</label>
                        <input type="file" id="file-input" class="hidden" accept=".mp3,.wav">
                    </div>
                    <div id="file-info" class="file-info hidden"></div>
                </div>
            </div>

            <!-- Painel para Texto de Locutor (Testemunhal) -->
            <div id="panel-testimonial" class="ad-panel hidden">
                 <div class="step-wrapper">
                    <h3><span class="step-number">1</span> Selecione a Rádio</h3>
                    <!-- Grade de rádios específica para este painel -->
                    <div id="testimonial-radio-grid" class="radio-grid ad-radio-grid">
                        <!-- Cards de Rádio serão inseridos aqui via JavaScript -->
                    </div>
                </div>
                <div id="testimonial-text-wrapper" class="step-wrapper hidden">
                    <h3><span class="step-number">2</span> Escreva o Texto do Anúncio</h3>
                    <form id="testimonial-form" class="modern-form">
                        <div class="form-group floating-label">
                             <textarea id="testimonial-text" name="testimonial_text" rows="8" placeholder=" " maxlength="500"></textarea>
                             <label for="testimonial-text">Texto para o locutor (máx. 500 caracteres)</label>
                             <div id="char-counter">0 / 500</div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Painel de Configuração Geral (horário, etc.) que aparece para todos os tipos -->
            <div id="config-wrapper" class="step-wrapper hidden">
                 <h3><span class="step-number">3</span> Configure e Adicione ao Pedido</h3>
                 <p>Configurando para: <strong id="selected-radio-name"></strong></p>
                <form id="ad-config-form" class="config-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ad-time-slot">Horário de Veiculação</label>
                            <select id="ad-time-slot" name="time_slot">
                                <option value="manha">Manhã (06h-12h) - Pico</option>
                                <option value="tarde" selected>Tarde (12h-18h) - Normal</option>
                                <option value="noite">Noite (18h-00h) - Nobre</option>
                            </select>
                        </div>
                        <div class="form-group duration-display">
                            <label>Duração do Anúncio</label>
                            <div id="duration-display-box">-- s</div>
                        </div>
                    </div>
                    <button type="submit" class="btn-add-to-cart"><i class="ph ph-plus-circle"></i> Adicionar ao Pedido</button>
                </form>
            </div>
        </div>

        <!-- Coluna Direita: Resumo do Pedido e Formulário Final -->
        <div class="anuncie-sidebar-col">
            
            <div class="step-wrapper">
                <h3>Resumo do Pedido</h3>
                <div id="order-summary" class="order-summary-wrapper">
                    <ul id="order-list" class="order-list">
                        <!-- Itens do pedido serão inseridos aqui via JavaScript -->
                    </ul>
                    <div id="empty-cart-message" class="empty-message">
                        <i class="ph ph-shopping-cart"></i>
                        <p>Selecione um formato e configure sua campanha.</p>
                    </div>
                    <div id="order-total" class="order-total-wrapper hidden">
                        <strong>Total Estimado:</strong>
                        <span id="total-price">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <div id="customer-form-wrapper" class="step-wrapper hidden">
                <h3><span class="step-number">Final</span> Seus Dados</h3>
                <form id="customer-form" class="modern-form">
                    <div class="form-group floating-label">
                        <input type="text" id="customer-name" name="name" required placeholder=" ">
                        <label for="customer-name">Nome ou Empresa</label>
                    </div>
                    <div class="form-group floating-label">
                        <input type="email" id="customer-email" name="email" required placeholder=" ">
                        <label for="customer-email">E-mail de Contato</label>
                    </div>
                    <div class="form-group floating-label">
                        <input type="tel" id="customer-phone" name="phone" required placeholder=" ">
                        <label for="customer-phone">Telefone / WhatsApp</label>
                    </div>
                    <button type="submit" class="btn-submit">Solicitar Cotação Final <i class="ph ph-paper-plane-tilt"></i></button>
                </form>
            </div>

        </div>

    </div>
</div>

<?php 
    // Inclui o rodapé universal do site
    include 'templates/footer.php'; 
?>