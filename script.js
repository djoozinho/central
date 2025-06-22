document.addEventListener('DOMContentLoaded', () => {

    // ===============================================
    // PARTE 1: LÓGICA PJAX PARA NAVEGAÇÃO CONTÍNUA
    // ===============================================
    const pjaxContainer = document.getElementById('pjax-container');

    const loadPage = async (url, pushState = true) => {
        document.body.style.transition = 'opacity 0.3s ease-out';
        document.body.style.opacity = '0.5';
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`Página não encontrada: ${url}`);
            const text = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newContent = doc.getElementById('pjax-container');
            if (newContent) {
                document.title = doc.title;
                pjaxContainer.innerHTML = newContent.innerHTML;
                if (pushState) {
                    window.history.pushState({ path: url }, doc.title, url);
                }
            }
            initPageScripts(); // Inicializa os scripts da nova página
        } catch (error) {
            console.error('Erro no PJAX:', error);
            pjaxContainer.innerHTML = `<div class="container page-section" style="text-align:center;"><h2>Página não encontrada</h2><p>O conteúdo que você buscou não pôde ser carregado.</p></div>`;
        } finally {
            document.body.style.opacity = '1';
        }
    };

    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.hostname === window.location.hostname && !link.href.includes('login.php') && !link.href.includes('admin.php') && link.target !== '_blank') {
            e.preventDefault();
            if (link.href !== window.location.href) {
                loadPage(link.href);
            }
        }
    });

    window.addEventListener('popstate', (e) => {
        loadPage(e.state ? e.state.path : 'index.php', false);
    });

    // ===============================================
    // PARTE 2: ESTADO GLOBAL E FUNÇÕES UNIVERSAIS
    // ===============================================
    const audio = new Audio();
    let currentRadio = null;
    let allRadios = []; // Cache de rádios para evitar múltiplas chamadas à API

    const playRadio = (radio) => {
        const playerContainer = document.getElementById('player');
        if (!playerContainer || !radio) return;
        currentRadio = radio;
        const streamUrl = radio.streamUrls.split(',')[0].trim();
        document.getElementById('player-logo').src = radio.logoUrl;
        document.getElementById('player-radio-name').textContent = radio.name;
        playerContainer.classList.remove('hidden');
        const playerStatus = document.getElementById('player-status');
        const playPauseIcon = document.getElementById('play-pause-icon');
        playerStatus.textContent = "Carregando...";
        playPauseIcon.className = 'ph-fill ph-play-circle';
        audio.src = streamUrl;
        audio.play().then(() => {
            playerStatus.textContent = "Ao Vivo";
            playPauseIcon.className = 'ph-fill ph-pause-circle';
        }).catch(error => {
            console.error("Erro ao tocar a rádio:", error);
            playerStatus.textContent = "Erro ao conectar";
            playPauseIcon.className = 'ph-fill ph-x-circle';
        }).finally(() => {
            updatePlayingIndicator();
        });
    };

    const updatePlayingIndicator = () => {
        document.querySelectorAll('.radio-card').forEach(card => {
            const isPlaying = currentRadio && parseInt(card.dataset.radioId) === currentRadio.id && !audio.paused;
            card.classList.toggle('playing', isPlaying);
        });
    };

    const createRadioCard = (radio) => {
        const card = document.createElement('div');
        card.className = 'radio-card';
        card.dataset.radioId = radio.id;
        card.innerHTML = `<div class="card-image-container"><img src="${radio.logoUrl}" alt="${radio.name}"><div class="card-play-icon"><i class="ph-fill ph-play"></i></div></div><p class="card-title">${radio.name}</p>`;
        return card;
    };

    const fetchRadiosAPI = async () => {
        if (allRadios.length === 0) {
            try {
                const response = await fetch('api.php');
                if (!response.ok) throw new Error('Falha na API de rádios');
                allRadios = await response.json();
            } catch (e) {
                allRadios = [];
                console.error("Falha ao buscar rádios da API:", e);
            }
        }
        return allRadios;
    };

    // ===============================================
    // PARTE 3: MÓDULOS DE PÁGINA ESPECÍFICOS
    // ===============================================

    // --- Módulo para a Página Inicial ---
    const initHomePage = async () => {
        const radioContentArea = document.getElementById('radio-content-area');
        if (!radioContentArea) return;

        const radios = await fetchRadiosAPI();
        const searchInput = document.getElementById('search-input');
        const featuredGrid = document.getElementById('featured-radio-grid');
        const otherGrid = document.getElementById('other-radio-grid');
        const defaultSections = document.getElementById('default-sections');
        const searchResultsSection = document.getElementById('search-results-section');
        const searchResultsGrid = document.getElementById('search-results-grid');
        const noResultsMessage = document.getElementById('no-results-message');

        const renderHomePageRadios = (radioList) => {
            if (!featuredGrid || !otherGrid) return;
            featuredGrid.innerHTML = '';
            otherGrid.innerHTML = '';
            radioList.forEach(radio => {
                const card = createRadioCard(radio);
                if (radio.is_featured) featuredGrid.appendChild(card);
                else otherGrid.appendChild(card);
            });
            updatePlayingIndicator();
        };

        renderHomePageRadios(radios);

        radioContentArea.addEventListener('click', (e) => {
            const card = e.target.closest('.radio-card');
            if (card) {
                const radioId = parseInt(card.dataset.radioId, 10);
                const radioToPlay = allRadios.find(r => r.id === radioId);
                if (radioToPlay) playRadio(radioToPlay);
            }
        });

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            if (searchTerm.length > 0) {
                const filteredRadios = allRadios.filter(radio => {
                    const nameMatch = radio.name.toLowerCase().includes(searchTerm);
                    const genreMatch = radio.genre ? radio.genre.toLowerCase().includes(searchTerm) : false;
                    const artistsMatch = radio.artists ? radio.artists.toLowerCase().includes(searchTerm) : false;
                    return nameMatch || genreMatch || artistsMatch;
                });
                defaultSections.classList.add('hidden');
                searchResultsSection.classList.remove('hidden');
                searchResultsGrid.innerHTML = '';
                if (filteredRadios.length > 0) {
                    filteredRadios.forEach(radio => searchResultsGrid.appendChild(createRadioCard(radio)));
                    noResultsMessage.classList.add('hidden');
                } else {
                    noResultsMessage.classList.remove('hidden');
                }
            } else {
                defaultSections.classList.remove('hidden');
                searchResultsSection.classList.add('hidden');
            }
            updatePlayingIndicator();
        });
    };

    // --- Módulo para a Página "Anuncie" ---
    const initAnunciePage = async () => {
        const anunciePage = document.getElementById('anuncie-page');
        if (!anunciePage) return;

        const radios = await fetchRadiosAPI();
        
        const priceConfig = {
            audio: { base: 2.0, per_second: 0.3 },
            testimonial: { base: 20.0, per_char: 0.05 },
            music: { base: 50.0, per_second: 0.2 }
        };
        const adSlots = [10, 15, 30, 45, 60, 90, 120];
        let adCart = [];
        let currentAdType = 'audio';
        let selectedRadioForAd = null;

        const adTypeButtons = document.querySelectorAll('.ad-type-button');
        const adPanels = document.querySelectorAll('.ad-panel');
        const radioGrids = document.querySelectorAll('.ad-radio-grid');
        const configWrapper = document.getElementById('config-wrapper');
        const selectedRadioNameEl = document.getElementById('selected-radio-name');
        const adConfigForm = document.getElementById('ad-config-form');
        const dropZoneWrapper = document.getElementById('drop-zone-wrapper');
        const testimonialWrapper = document.getElementById('testimonial-text-wrapper');
        const orderList = document.getElementById('order-list');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const orderTotalWrapper = document.getElementById('order-total');
        const totalPriceEl = document.getElementById('total-price');
        const customerFormWrapper = document.getElementById('customer-form-wrapper');

        radioGrids.forEach(grid => {
            if (grid) {
                grid.innerHTML = '';
                radios.forEach(radio => grid.appendChild(createRadioCard(radio)));
            }
        });

        const resetAdSelection = () => {
            document.querySelectorAll('.ad-radio-grid .radio-card.selected').forEach(c => c.classList.remove('selected'));
            configWrapper.classList.add('hidden');
            dropZoneWrapper.classList.add('hidden');
            testimonialWrapper.classList.add('hidden');
            selectedRadioForAd = null;
            const fileInfo = document.getElementById('file-info');
            if (fileInfo) {
                fileInfo.classList.add('hidden');
                fileInfo.textContent = '';
            }
            if (document.getElementById('file-input')) document.getElementById('file-input').value = '';
            if (document.getElementById('testimonial-text')) document.getElementById('testimonial-text').value = '';
            if (document.getElementById('char-counter')) document.getElementById('char-counter').textContent = '0 / 500';
            if (document.getElementById('duration-display-box')) document.getElementById('duration-display-box').textContent = '-- s';
        };

        adTypeButtons.forEach(button => {
            button.addEventListener('click', () => {
                adTypeButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                currentAdType = button.dataset.type;
                adPanels.forEach(panel => panel.classList.add('hidden'));
                const activePanelId = `panel-${currentAdType === 'music' ? 'audio' : currentAdType}`;
                document.getElementById(activePanelId).classList.add('active');
                resetAdSelection();
            });
        });

        radioGrids.forEach(grid => {
            grid.addEventListener('click', (e) => {
                const card = e.target.closest('.radio-card');
                if (!card) return;
                radioGrids.forEach(g => g.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected')));
                card.classList.add('selected');
                const radioId = parseInt(card.dataset.radioId, 10);
                selectedRadioForAd = allRadios.find(r => r.id === radioId);
                if (selectedRadioForAd) {
                    selectedRadioNameEl.textContent = selectedRadioForAd.name;
                    if (currentAdType === 'audio' || currentAdType === 'music') {
                        dropZoneWrapper.classList.remove('hidden');
                        testimonialWrapper.classList.add('hidden');
                    } else if (currentAdType === 'testimonial') {
                        testimonialWrapper.classList.remove('hidden');
                        dropZoneWrapper.classList.add('hidden');
                        document.getElementById('testimonial-text').dispatchEvent(new Event('input'));
                    }
                }
            });
        });

        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileInfo = document.getElementById('file-info');
        const durationDisplay = document.getElementById('duration-display-box');

        const handleFile = async (file) => {
            if (!file || !file.type.startsWith('audio/')) {
                fileInfo.textContent = 'Erro: Arquivo inválido. Envie MP3 ou WAV.';
                fileInfo.className = 'file-info error';
                fileInfo.classList.remove('hidden');
                return;
            }
            fileInfo.textContent = `Processando "${file.name}"...`;
            fileInfo.className = 'file-info processing';
            fileInfo.classList.remove('hidden');
            configWrapper.classList.add('hidden');

            if (typeof window.musicMetadata === 'undefined') {
                fileInfo.textContent = 'Erro: O leitor de áudio não carregou. Por favor, atualize a página e tente novamente.';
                fileInfo.className = 'file-info error';
                console.error("A biblioteca music-metadata-browser não foi encontrada no objeto window.");
                return;
            }

            try {
                const metadata = await window.musicMetadata.parseBlob(file);
                const duration = metadata.format.duration;
                if (typeof duration !== 'number' || duration <= 0) {
                    throw new Error("Não foi possível determinar a duração do áudio.");
                }
                const selectedSlot = adSlots.find(slot => slot >= duration) || adSlots[adSlots.length - 1];
                fileInfo.textContent = `Arquivo: "${file.name}" | Duração Real: ${duration.toFixed(1)}s`;
                fileInfo.className = 'file-info success';
                durationDisplay.textContent = `${selectedSlot} s`;
                adConfigForm.dataset.duration = duration;
                adConfigForm.dataset.slot = selectedSlot;
                configWrapper.classList.remove('hidden');
            } catch (error) {
                fileInfo.textContent = 'Erro ao processar o áudio. Tente outro arquivo.';
                fileInfo.className = 'file-info error';
                console.error("Erro com music-metadata-browser:", error);
            }
        };

        if (dropZone) dropZone.addEventListener('click', () => fileInput.click());
        if (fileInput) fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('drag-over'); });
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
            dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('drag-over'); handleFile(e.dataTransfer.files[0]); });
        }
        
        const testimonialText = document.getElementById('testimonial-text');
        if (testimonialText) {
            testimonialText.addEventListener('input', () => {
                const charCount = testimonialText.value.length;
                document.getElementById('char-counter').textContent = `${charCount} / 500`;
                if (charCount > 0 && selectedRadioForAd) {
                    const estimatedSeconds = Math.max(10, Math.ceil(charCount / 3.5));
                    const selectedSlot = adSlots.find(slot => slot >= estimatedSeconds) || adSlots[adSlots.length - 1];
                    durationDisplay.textContent = `${selectedSlot} s`;
                    adConfigForm.dataset.charCount = charCount;
                    adConfigForm.dataset.slot = selectedSlot;
                    configWrapper.classList.remove('hidden');
                } else {
                    configWrapper.classList.add('hidden');
                }
            });
        }
        
        if (adConfigForm) {
            adConfigForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (!selectedRadioForAd) { alert("Erro: Nenhuma rádio selecionada."); return; }
                const formData = new FormData(adConfigForm);
                const timeSlot = formData.get('time_slot');
                let price = 0;
                let description = '';
                if (currentAdType === 'testimonial') {
                    const charCount = parseInt(adConfigForm.dataset.charCount, 10);
                    const slot = parseInt(adConfigForm.dataset.slot, 10);
                    if (!charCount || !slot) { alert("Por favor, escreva o texto do anúncio."); return; }
                    price = (priceConfig.testimonial.base + (charCount * priceConfig.testimonial.per_char));
                    description = `Testemunhal de ${slot}s`;
                } else {
                    const duration = parseFloat(adConfigForm.dataset.duration);
                    const slot = parseInt(adConfigForm.dataset.slot, 10);
                    if (!duration || !slot) { alert("Por favor, envie um arquivo de áudio."); return; }
                    const config = priceConfig[currentAdType];
                    price = (config.base + (duration * config.per_second));
                    description = `${currentAdType === 'music' ? 'Divulgação de Música' : 'Comercial'} de ${slot}s`;
                }
                adCart.push({ id: Date.now(), radio: selectedRadioForAd, description, timeSlot, price });
                updateCart();
                resetAdSelection();
            });
        }
        
        const updateCart = () => {
            emptyCartMessage.classList.toggle('hidden', adCart.length > 0);
            orderTotalWrapper.classList.toggle('hidden', adCart.length === 0);
            customerFormWrapper.classList.toggle('hidden', adCart.length === 0);
            orderList.innerHTML = '';
            let total = 0;
            adCart.forEach(item => {
                total += item.price;
                const li = document.createElement('li');
                li.innerHTML = `<img src="${item.radio.logoUrl}" alt="${item.radio.name}"><div class="order-item-details"><strong>${item.radio.name}</strong><span>${item.description} - Período da ${item.timeSlot}</span></div><span class="order-item-price">R$ ${item.price.toFixed(2)}</span><button class="btn-remove-item" data-id="${item.id}" title="Remover"><i class="ph ph-trash"></i></button>`;
                orderList.appendChild(li);
            });
            totalPriceEl.textContent = `R$ ${total.toFixed(2)}`;
        };
        
        if (orderList) {
            orderList.addEventListener('click', (e) => {
                const removeButton = e.target.closest('.btn-remove-item');
                if (removeButton) {
                    adCart = adCart.filter(item => item.id !== parseInt(removeButton.dataset.id, 10));
                    updateCart();
                }
            });
        }
    };

    // ===============================================
    // PARTE 4: ROTEADOR DE SCRIPTS E INICIALIZAÇÃO
    // ===============================================
    const initPageScripts = () => {
        initHomePage();
        initAnunciePage();
    };

    const setupGlobalEventListeners = () => {
        const playPauseBtn = document.getElementById('play-pause-btn');
        if (playPauseBtn) {
            playPauseBtn.addEventListener('click', () => {
                if (!currentRadio) return;
                if (audio.paused) playRadio(currentRadio);
                else {
                    audio.pause();
                    playPauseBtn.querySelector('i').className = 'ph-fill ph-play-circle';
                    document.getElementById('player-status').textContent = "Pausado";
                    updatePlayingIndicator();
                }
            });
        }
        
        const volumeSlider = document.getElementById('volume-slider');
        const volumeIcon = document.getElementById('volume-icon');
        const updateVolumeIconState = () => {
            if (!volumeIcon || !volumeSlider) return;
            if (audio.muted || audio.volume === 0) volumeIcon.className = 'ph ph-speaker-slash';
            else if (audio.volume < 0.5) volumeIcon.className = 'ph ph-speaker-low';
            else volumeIcon.className = 'ph ph-speaker-high';
        };
        if (volumeSlider) {
            volumeSlider.addEventListener('input', (e) => {
                audio.muted = false;
                audio.volume = e.target.value;
                updateVolumeIconState();
            });
        }
        if (volumeIcon) {
            volumeIcon.addEventListener('click', () => {
                audio.muted = !audio.muted;
                volumeSlider.value = audio.muted ? 0 : audio.volume;
                updateVolumeIconState();
            });
        }
        if (volumeSlider) {
            audio.volume = volumeSlider.value;
            updateVolumeIconState();
        }

        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const mainNav = document.querySelector('.main-nav');
        const menuOverlay = document.getElementById('menu-overlay');
        const openMenu = () => { if (mainNav) { mainNav.classList.add('active'); if (menuOverlay) menuOverlay.classList.remove('menu-overlay-hidden'); document.body.style.overflow = 'hidden'; }};
        const closeMenu = () => { if (mainNav) { mainNav.classList.remove('active'); if (menuOverlay) menuOverlay.classList.add('menu-overlay-hidden'); document.body.style.overflow = 'auto'; }};
        if(mobileMenuToggle) mobileMenuToggle.addEventListener('click', openMenu);
        if(closeMenuBtn) closeMenuBtn.addEventListener('click', closeMenu);
        if(menuOverlay) menuOverlay.addEventListener('click', closeMenu);
    };

    // --- INICIALIZAÇÃO ---
    setupGlobalEventListeners();
    initPageScripts();
});