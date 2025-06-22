<?php 
    $page_title = 'Rádio Central - Início';
    include 'templates/header.php'; 
?>

<!-- Banner Hero Section (sem alterações) -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1>Sua Conexão Musical Definitiva</h1>
        <p>Explore milhares de estações de rádio de todo o mundo.</p>
        <div class="hero-search-bar">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" id="search-input" placeholder="Encontre sua rádio, artista ou gênero favorito...">
        </div>
    </div>
</section>

<!-- Área de Conteúdo das Rádios -->
<div class="container page-section" id="radio-content-area">
    <div id="default-sections">
        <h2 class="section-title">Estações em Destaque</h2>
        <div id="featured-radio-grid" class="radio-grid"></div>
        
        <h2 class="section-title" style="margin-top: 40px;">Todas as Estações</h2>
        <div id="other-radio-grid" class="radio-grid"></div>
    </div>
    <div id="search-results-section" class="hidden">
        <h2 class="section-title">Resultados da Busca</h2>
        <div id="search-results-grid" class="radio-grid"></div>
        <p id="no-results-message" class="hidden">Nenhuma rádio encontrada.</p>
    </div>
</div>

<?php 
    include 'templates/footer.php'; 
?>