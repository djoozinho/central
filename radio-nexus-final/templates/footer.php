    </main> <!-- Fecha a tag <main> aberta no header.php -->

    <!-- ========================================= -->
    <!--      FOOTER DE ALTO PADRÃO                -->
    <!-- ========================================= -->
    <footer class="site-footer-main">
        <div class="container">
            
            <!-- Seção de Newsletter -->
            <div class="footer-newsletter">
                <div class="newsletter-text">
                    <h3>Fique por Dentro das Novidades</h3>
                    <p>Inscreva-se para receber as últimas notícias, lançamentos e promoções diretamente no seu e-mail.</p>
                </div>
                <form class="newsletter-form">
                    <input type="email" placeholder="Digite seu melhor e-mail" required>
                    <button type="submit">Inscrever <i class="ph ph-paper-plane-tilt"></i></button>
                </form>
            </div>

            <div class="footer-divider"></div>

            <!-- Conteúdo Principal do Rodapé -->
            <div class="footer-content">
                <div class="footer-column about-us">
                    <a href="index.php" class="footer-logo-link">
                        <img src="assets/logo.png" alt="Rádio Central Logo" class="footer-logo">
                        <h3>Rádio Central</h3>
                    </a>
                    <p>A sua conexão definitiva com o universo da música. Milhares de estações, um só lugar.</p>
                </div>
                
                <div class="footer-column quick-links">
                    <h4>Navegação</h4>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="generos.php">Gêneros</a></li>
                        <li><a href="top-musicas.php">Top Músicas</a></li>
                        <li><a href="anuncie.php">Anuncie Conosco</a></li>
                    </ul>
                </div>

                <div class="footer-column legal-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="contato.php">Contato</a></li>
                        <li><a href="#">Termos de Serviço</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div class="footer-column social-media">
                    <h4>Conecte-se</h4>
                    <p>Siga-nos nas redes sociais e faça parte da nossa comunidade.</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook" title="Facebook"><i class="ph-fill ph-facebook-logo"></i></a>
                        <a href="#" aria-label="Instagram" title="Instagram"><i class="ph-fill ph-instagram-logo"></i></a>
                        <a href="#" aria-label="Twitter" title="Twitter"><i class="ph-fill ph-twitter-logo"></i></a>
                        <a href="#" aria-label="Youtube" title="Youtube"><i class="ph-fill ph-youtube-logo"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>

            <!-- Rodapé Inferior -->
            <div class="footer-bottom">
                <p>© <?php echo date("Y"); ?> Rádio Central. Todos os direitos reservados. Projetado com <i class="ph-fill ph-heart" style="color:var(--accent-red); vertical-align: middle;"></i>.</p>
            </div>

        </div>
    </footer>

    <!-- Player de Música Estiloso (Persistente) -->
    <div id="player" class="player-bar-container hidden">
        <div class="player-content">
            <img id="player-logo" src="" alt="Logo da rádio">
            <div class="station-details">
                <p id="player-radio-name" class="radio-name">Nome da Rádio</p>
                <p id="player-status" class="now-playing">Ao Vivo</p>
            </div>
            <div class="player-controls">
                <button id="play-pause-btn" class="control-button">
                    <i id="play-pause-icon" class="ph-fill ph-play-circle"></i>
                </button>
            </div>
            <div class="player-volume">
                <i id="volume-icon" class="ph ph-speaker-high"></i>
                <input type="range" min="0" max="1" step="0.01" value="0.8" id="volume-slider">
            </div>
        </div>
    </div>
    
    <div id="menu-overlay" class="menu-overlay-hidden"></div>
    
    <!-- ========================================= -->
    <!--      SCRIPTS CARREGADOS NO FINAL          -->
    <!-- ========================================= -->
    
    <!-- NOVA BIBLIOTECA PARA LEITURA DE ÁUDIO (essencial para a página 'Anuncie') -->
    <script src="https://cdn.jsdelivr.net/npm/music-metadata-browser@2.5.10/dist/bundles/music-metadata.min.js"></script>
    
    <!-- Seu script principal, carregado DEPOIS da biblioteca -->
    <script src="script.js"></script>

</body>
</html>