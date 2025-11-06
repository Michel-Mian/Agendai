// Sistema de Busca de Veículos - vehicles-search.js
(function() {
    'use strict';

    let pollingInterval = null;
    let pollingAttempts = 0;
    let pollingStartTime = null;
    const MAX_POLLING_ATTEMPTS = 180; // 15 minutos (5s * 180 = 900s)
    const MAX_POLLING_TIME_MS = 900000; // 15 minutos de timeout máximo (900 segundos)

    // Elementos DOM
    const form = document.getElementById('vehicle-search-form');
    const loadingDiv = document.getElementById('vehicle-loading');
    const errorDiv = document.getElementById('vehicle-error');
    const resultsDiv = document.getElementById('vehicle-results');
    const alertDiv = document.getElementById('vehicle-location-alert');
    const vehiclesGrid = document.getElementById('vehicles-grid');
    const totalVehiclesSpan = document.getElementById('total-vehicles');
    const noVehiclesMsg = document.getElementById('no-vehicles-message');

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }

        // Preencher datas padrão
        setDefaultDates();

        // Adicionar validação dinâmica de datas
        setupDateValidation();
    });

    /**
     * Define datas padrão (hoje + 1 dia)
     */
    function setDefaultDates() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        const dataRetirada = document.getElementById('data_retirada');
        const dataDevolucao = document.getElementById('data_devolucao');

        if (dataRetirada && !dataRetirada.value) {
            dataRetirada.value = formatDateForInput(tomorrow);
        }

        if (dataDevolucao && !dataDevolucao.value) {
            const afterTomorrow = new Date(tomorrow);
            afterTomorrow.setDate(afterTomorrow.getDate() + 2);
            dataDevolucao.value = formatDateForInput(afterTomorrow);
        }

        // Definir hora padrão 10:00
        const horaRetirada = document.getElementById('hora_retirada');
        const horaDevolucao = document.getElementById('hora_devolucao');
        
        if (horaRetirada) horaRetirada.value = '10:00';
        if (horaDevolucao) horaDevolucao.value = '10:00';
    }

    /**
     * Formata data para input type="date"
     */
    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * Configura validação dinâmica de datas
     */
    function setupDateValidation() {
        const dataRetirada = document.getElementById('data_retirada');
        const dataDevolucao = document.getElementById('data_devolucao');

        if (!dataRetirada || !dataDevolucao) return;

        // Quando data de retirada mudar, ajustar mínimo da devolução
        dataRetirada.addEventListener('change', function() {
            const minDevolucao = this.value;
            dataDevolucao.setAttribute('min', minDevolucao);

            // Se data de devolução for anterior, ajustar automaticamente
            if (dataDevolucao.value && dataDevolucao.value < minDevolucao) {
                dataDevolucao.value = minDevolucao;
            }
        });

        // Validar ao mudar data de devolução
        dataDevolucao.addEventListener('change', function() {
            if (dataRetirada.value && this.value < dataRetirada.value) {
                alert('A data de devolução não pode ser anterior à data de retirada');
                this.value = dataRetirada.value;
            }
        });
    }

    /**
     * Handle do submit do formulário
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        // Coletar dados do formulário
        const formData = {
            local_retirada: document.getElementById('local_retirada').value,
            data_retirada: document.getElementById('data_retirada').value,
            hora_retirada: document.getElementById('hora_retirada').value,
            data_devolucao: document.getElementById('data_devolucao').value,
            hora_devolucao: document.getElementById('hora_devolucao').value,
            _token: document.querySelector('input[name="_token"]').value
        };

        // Validação básica
        if (!formData.local_retirada) {
            showError('Por favor, informe o local de retirada');
            return;
        }

        // Validar datas
        if (!formData.data_retirada || !formData.data_devolucao) {
            showError('Por favor, informe as datas de retirada e devolução');
            return;
        }

        const dataRetirada = new Date(formData.data_retirada);
        const dataDevolucao = new Date(formData.data_devolucao);

        if (dataDevolucao < dataRetirada) {
            showError('A data de devolução deve ser igual ou posterior à data de retirada');
            return;
        }

        // Validar se as datas não são no passado
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        
        if (dataRetirada < hoje) {
            showError('A data de retirada não pode ser no passado');
            return;
        }

        // Iniciar busca
        startSearch(formData);
    }

    /**
     * Inicia a busca de veículos
     */
    function startSearch(formData) {
        console.log('🚀 [INIT] Iniciando busca de veículos...', formData);

        // Resetar polling
        stopPolling();
        pollingAttempts = 0;
        pollingStartTime = null;

        // Mostrar loading
        showLoading();

        // Fazer requisição inicial
        fetch(window.APP_ROUTES.searchVehicles, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': formData._token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            handleSearchResponse(data, formData);
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            hideLoading();
            showError('Erro ao iniciar busca. Por favor, tente novamente.');
        });
    }

    /**
     * Processa resposta da busca
     */
    function handleSearchResponse(data, formData) {
        console.log('🔍 [BUSCA] Resposta recebida:', {
            status: data.status,
            hasVeiculos: !!data.veiculos,
            veiculosCount: data.veiculos?.length,
            hasError: !!data.error,
            fullData: data
        });

        if (data.status === 'carregando') {
            console.log('⏳ [BUSCA] Status: carregando - iniciando polling');
            // Iniciar polling (loading continua visível)
            startPolling(formData);
        } else if (data.status === 'concluido') {
            console.log('✅ [BUSCA] Status: concluido - exibindo resultados');
            // Ocultar loading e exibir resultados
            hideLoading();
            displayResults(data.veiculos, data.alerta);
        } else if (data.error) {
            console.error('❌ [BUSCA] Erro detectado:', data.error);
            hideLoading();
            showError(data.message || data.error);
        } else {
            console.warn('⚠️ [BUSCA] Status desconhecido:', data.status);
            hideLoading();
        }
    }

    /**
     * Inicia polling para verificar status
     */
    function startPolling(formData) {
        pollingStartTime = Date.now();
        
        console.log('🔄 [POLL] Iniciando polling...', {
            maxAttempts: MAX_POLLING_ATTEMPTS,
            intervalMs: 5000,
            maxTimeoutMs: MAX_POLLING_TIME_MS,
            startTime: new Date(pollingStartTime).toISOString()
        });

        pollingInterval = setInterval(() => {
            pollingAttempts++;
            const elapsedMs = Date.now() - pollingStartTime;
            const elapsedSeconds = Math.floor(elapsedMs / 1000);

            console.log(`🔄 [POLL] Tentativa ${pollingAttempts}/${MAX_POLLING_ATTEMPTS} (${elapsedSeconds}s/${Math.floor(MAX_POLLING_TIME_MS/1000)}s)`);

            // Verificar timeout de tempo
            if (elapsedMs > MAX_POLLING_TIME_MS) {
                console.error('⏰ [POLL] Timeout: Tempo máximo de espera excedido', {
                    elapsedMs: elapsedMs,
                    maxTimeMs: MAX_POLLING_TIME_MS,
                    elapsedSeconds: elapsedSeconds
                });
                stopPolling();
                hideLoading();
                showError(`Tempo de busca excedido (${elapsedSeconds}s). A busca pode estar demorando mais que o esperado. Por favor, tente novamente.`);
                return;
            }

            // Verificar timeout de tentativas
            if (pollingAttempts > MAX_POLLING_ATTEMPTS) {
                console.error('⏰ [POLL] Timeout: Máximo de tentativas atingido');
                stopPolling();
                hideLoading();
                showError('Tempo de busca excedido. Por favor, tente novamente.');
                return;
            }

            // Fazer requisição de polling
            fetch(window.APP_ROUTES.searchVehicles, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                console.log('📡 [POLL] Resposta HTTP recebida:', {
                    status: response.status,
                    ok: response.ok,
                    statusText: response.statusText
                });
                return response.json();
            })
            .then(data => {
                console.log('📦 [POLL] Dados processados:', {
                    status: data.status,
                    hasVeiculos: !!data.veiculos,
                    veiculosCount: data.veiculos?.length,
                    hasError: !!data.error
                });

                if (data.status === 'concluido') {
                    console.log('✅ [POLL] Completado! Parando polling e exibindo resultados');
                    stopPolling();
                    hideLoading();
                    displayResults(data.veiculos, data.alerta);
                } else if (data.status === 'failed' || data.error) {
                    console.error('❌ [POLL] Falha detectada:', data.error || data.message);
                    stopPolling();
                    hideLoading();
                    showError(data.message || data.error);
                } else if (data.status === 'carregando') {
                    console.log('⏳ [POLL] Ainda processando... Continuando polling');
                } else {
                    console.warn('⚠️ [POLL] Status inesperado:', data.status);
                }
                // Se ainda estiver carregando, continua polling
            })
            .catch(error => {
                console.error('❌ [POLL] Erro na requisição:', error);
                pollingAttempts--; // Não contar tentativa com erro
            });

        }, 5000); // Poll a cada 5 segundos
    }

    /**
     * Para o polling
     */
    function stopPolling() {
        if (pollingInterval) {
            console.log('🛑 [POLL] Parando polling', {
                totalAttempts: pollingAttempts,
                totalTimeSeconds: pollingAttempts * 5
            });
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    /**
     * Exibe resultados
     */
    function displayResults(veiculos, alerta) {
        console.log('🎉 [DISPLAY] Exibindo resultados:', {
            veiculosCount: veiculos?.length || 0,
            hasAlerta: !!alerta,
            veiculos: veiculos
        });

        hideLoading();

        // Exibir alerta se houver
        if (alerta) {
            showAlert(alerta);
        } else {
            hideAlert();
        }

        // Verificar se há veículos
        if (!veiculos || veiculos.length === 0) {
            showNoResults();
            return;
        }

        // Atualizar contagem
        totalVehiclesSpan.textContent = veiculos.length;

        // Limpar grid
        vehiclesGrid.innerHTML = '';

        // Renderizar cada veículo
        veiculos.forEach((veiculo, index) => {
            const card = createVehicleCard(veiculo, index);
            vehiclesGrid.appendChild(card);
        });

        // Mostrar resultados
        resultsDiv.classList.remove('hidden');
        noVehiclesMsg.classList.add('hidden');
    }

    /**
     * Cria card de veículo
     */
    function createVehicleCard(veiculo, index) {
        const card = document.createElement('div');
        card.className = 'vehicle-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-200';

        // Tags
        let tagsHtml = '';
        if (veiculo.tags && veiculo.tags.length > 0) {
            tagsHtml = `
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2">
                    ${veiculo.tags.map(tag => `
                        <span class="inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full mr-2">
                            ${escapeHtml(tag)}
                        </span>
                    `).join('')}
                </div>
            `;
        }

        // Configurações
        const config = veiculo.configuracoes || {};
        const configHtml = `
            <div class="flex flex-wrap gap-3 mb-4">
                ${config.passageiros ? `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-user mr-1"></i>
                        ${config.passageiros} passageiros
                    </div>
                ` : ''}
                ${config.malas ? `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-suitcase mr-1"></i>
                        ${escapeHtml(config.malas)} malas
                    </div>
                ` : ''}
                ${config.ar_condicionado ? `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-snowflake mr-1"></i>
                        Ar condicionado
                    </div>
                ` : ''}
                ${config.cambio ? `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-cog mr-1"></i>
                        ${escapeHtml(config.cambio)}
                    </div>
                ` : ''}
                ${config.quilometragem ? `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-road mr-1"></i>
                        ${escapeHtml(config.quilometragem)}
                    </div>
                ` : ''}
            </div>
        `;

        // Diferenciais
        let diferenciaisHtml = '';
        if (veiculo.diferenciais && veiculo.diferenciais.length > 0) {
            diferenciaisHtml = `
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Proteções incluídas:</h4>
                    <ul class="text-xs text-gray-600 space-y-1">
                        ${veiculo.diferenciais.map(dif => `
                            <li><i class="fas fa-check-circle text-green-500 mr-1"></i> ${escapeHtml(dif)}</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }

        // Local de retirada
        const localRetirada = veiculo.local_retirada || {};
        let localRetiradaHtml = '';
        if (localRetirada.endereco) {
            localRetiradaHtml = `
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Local de retirada:</h4>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-1"></i>
                        ${escapeHtml(localRetirada.endereco)}
                    </div>
                </div>
            `;
        }

        // Locadora
        const locadora = veiculo.locadora || {};
        let locadoraHtml = '';
        if (locadora.nome) {
            locadoraHtml = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        ${locadora.logo ? `
                            <img src="${escapeHtml(locadora.logo)}" alt="${escapeHtml(locadora.nome)}" class="h-6 mr-2">
                        ` : `
                            <span class="text-sm font-medium text-gray-700">${escapeHtml(locadora.nome)}</span>
                        `}
                        ${locadora.avaliacao ? `
                            <span class="ml-2 text-sm text-gray-600">
                                <i class="fas fa-star text-yellow-500"></i>
                                ${locadora.avaliacao}/10
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Preço
        const preco = veiculo.preco || {};
        let precoHtml = '';
        if (preco.total) {
            precoHtml = `
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-1">Total</div>
                    <div class="text-3xl font-bold text-blue-600">
                        R$ ${formatPrice(preco.total)}
                    </div>
                    ${preco.diaria ? `
                        <div class="text-xs text-gray-500">
                            R$ ${formatPrice(preco.diaria)}/dia
                        </div>
                    ` : ''}
                </div>
            `;
        }

        // Montar card completo
        card.innerHTML = `
            ${tagsHtml}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Imagem -->
                    <div class="md:col-span-1 flex items-center justify-center">
                        <img 
                            src="${veiculo.imagem || '/images/default-car.png'}" 
                            alt="${escapeHtml(veiculo.nome)}"
                            class="w-full h-auto object-contain"
                            onerror="this.src='/images/default-car.png'"
                        >
                    </div>
                    
                    <!-- Informações -->
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 uppercase">
                                    ${escapeHtml(veiculo.nome)}
                                </h3>
                                ${veiculo.categoria ? `
                                    <p class="text-sm text-gray-600">
                                        ${escapeHtml(veiculo.categoria)}
                                    </p>
                                ` : ''}
                            </div>
                            ${precoHtml}
                        </div>
                        
                        ${configHtml}
                        ${diferenciaisHtml}
                        ${localRetiradaHtml}
                        ${locadoraHtml}
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    ${veiculo.link_continuar ? `
                        <a 
                            href="${buildRentcarsUrl(veiculo.link_continuar)}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-all inline-flex items-center"
                        >
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Ir para Site
                        </a>
                    ` : ''}
                    <button 
                        onclick="saveVehicle(${index})"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-all"
                    >
                        <i class="fas fa-check mr-2"></i>
                        Selecionar Veículo
                    </button>
                </div>
            </div>
        `;

        // Armazenar dados do veículo no card
        card.dataset.vehicleData = JSON.stringify(veiculo);

        return card;
    }

    /**
     * Constrói URL completa do Rentcars a partir da URL relativa
     */
    function buildRentcarsUrl(relativeUrl) {
        if (!relativeUrl) return '#';
        
        // Remove barras invertidas
        let cleanUrl = relativeUrl.replace(/\\/g, '');
        
        // Se já for uma URL completa, retorna
        if (cleanUrl.startsWith('http://') || cleanUrl.startsWith('https://')) {
            return cleanUrl;
        }
        
        // Remove barra inicial se houver
        if (cleanUrl.startsWith('/')) {
            cleanUrl = cleanUrl.substring(1);
        }
        
        // Constrói URL completa com o step=1
        const baseUrl = 'https://www.rentcars.com';
        const fullUrl = `${baseUrl}/${cleanUrl}?step=1`;
        
        return fullUrl;
    }

    /**
     * Salva veículo selecionado
     */
    window.saveVehicle = function(index) {
        const cards = document.querySelectorAll('.vehicle-card');
        if (!cards[index]) {
            console.error('Card não encontrado:', index);
            return;
        }

        const veiculoData = JSON.parse(cards[index].dataset.vehicleData);
        
        // Armazenar dados do veículo e índice para uso posterior
        window.selectedVehicleData = veiculoData;
        window.selectedVehicleIndex = index;
        
        // Buscar viagens do usuário
        fetchUserTrips();
    };

    /**
     * Busca viagens do usuário
     */
    function fetchUserTrips() {
        fetch(window.APP_ROUTES.getUserTrips, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.viagens && data.viagens.length > 0) {
                populateTripSelect(data.viagens);
                openVehicleTripModal();
            } else {
                alert('Uma viagem deve estar criada para se selecionar um carro');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar viagens:', error);
            alert('Erro ao buscar viagens. Por favor, tente novamente.');
        });
    }

    /**
     * Popula o dropdown de viagens
     */
    function populateTripSelect(viagens) {
        const select = document.getElementById('trip-select');
        select.innerHTML = '<option value="">Selecione uma viagem</option>';
        
        viagens.forEach(viagem => {
            const option = document.createElement('option');
            option.value = viagem.pk_id_viagem;
            
            const dataInicio = formatDate(viagem.data_inicio_viagem);
            const dataFim = formatDate(viagem.data_final_viagem);
            
            option.textContent = `${viagem.nome_viagem} (${dataInicio} - ${dataFim})`;
            select.appendChild(option);
        });
    }

    /**
     * Formata data para exibição
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    /**
     * Abre o modal de seleção de viagem
     */
    window.openVehicleTripModal = function() {
        const modal = document.getElementById('vehicle-trip-modal');
        modal.classList.remove('hidden');
    };

    /**
     * Fecha o modal de seleção de viagem
     */
    window.closeVehicleTripModal = function() {
        const modal = document.getElementById('vehicle-trip-modal');
        modal.classList.add('hidden');
    };

    /**
     * Confirma a seleção do veículo
     */
    window.confirmVehicleSelection = function() {
        const select = document.getElementById('trip-select');
        const tripId = select.value;
        
        if (!tripId) {
            alert('Por favor, selecione uma viagem');
            return;
        }

        const veiculoData = window.selectedVehicleData;
        const index = window.selectedVehicleIndex;

        fetch(window.APP_ROUTES.saveVehicle, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                fk_id_viagem: tripId,
                veiculo_data: veiculoData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Veículo salvo com sucesso!');
                closeVehicleTripModal();
                
                // Marcar como selecionado visualmente
                const cards = document.querySelectorAll('.vehicle-card');
                if (cards[index]) {
                    cards[index].classList.add('ring-4', 'ring-green-500');
                }
            } else {
                alert('Erro ao salvar veículo: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao salvar:', error);
            alert('Erro ao salvar veículo');
        });
    };



    /**
     * Mostra erro
     */
    function showError(message) {
        hideAll();
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
        errorDiv.classList.remove('hidden');
    }

    /**
     * Mostra loading
     */
    function showLoading() {
        hideAll();
        if (loadingDiv) {
            loadingDiv.classList.remove('hidden');
        }
    }

    /**
     * Oculta loading
     */
    function hideLoading() {
        if (loadingDiv) {
            loadingDiv.classList.add('hidden');
        }
    }

    /**
     * Mostra alerta de localização alternativa
     */
    function showAlert(alerta) {
        const localOriginal = document.getElementById('alert-local-original');
        const localAlternativo = document.getElementById('alert-local-alternativo');
        const distancia = document.getElementById('alert-distancia');

        if (localOriginal) localOriginal.textContent = alerta.local_original || '';
        if (localAlternativo) localAlternativo.textContent = alerta.local_alternativo || '';
        if (distancia) distancia.textContent = alerta.distancia || '';

        alertDiv.classList.remove('hidden');
    }

    /**
     * Oculta alerta
     */
    function hideAlert() {
        alertDiv.classList.add('hidden');
    }

    /**
     * Mostra mensagem de sem resultados
     */
    function showNoResults() {
        hideAll();
        resultsDiv.classList.remove('hidden');
        noVehiclesMsg.classList.remove('hidden');
        totalVehiclesSpan.textContent = '0';
    }

    /**
     * Oculta todos os containers
     */
    function hideAll() {
        if (loadingDiv) loadingDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');
        resultsDiv.classList.add('hidden');
        alertDiv.classList.add('hidden');
    }

    /**
     * Escapa HTML para prevenir XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Formata preço
     */
    function formatPrice(price) {
        return parseFloat(price).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

})();
