// -------------------- Autocomplete Google Places (Destino) --------------------
function initPlacesAutocompleteStrict() {
    const fields = [
        { id: 'tripDestination' },
        { id: 'origem' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input && typeof google !== 'undefined' && google.maps && google.maps.places) {
            // Verificar se já existe nosso novo sistema de autocomplete (step1)
            if (input.classList.contains('origem-input') || 
                input.classList.contains('destino-input') ||
                input.hasAttribute('data-new-autocomplete')) {
                console.log(`Pulando inicialização do formTrip.js para ${field.id} - novo sistema já ativo`);
                return;
            }
            
            if (!input._autocompleteInitialized) {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['(regions)'],
                });
                input._autocompleteInitialized = true;

                // Armazena se o usuário selecionou uma sugestão válida
                input._placeSelected = false;

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place && place.place_id) {
                        input._placeSelected = true;
                        input.classList.remove('border-red-500');
                    } else {
                        input._placeSelected = false;
                        input.classList.add('border-red-500');
                    }
                });

                // Ao digitar, reseta o status de seleção
                input.addEventListener('input', function() {
                    input._placeSelected = false;
                    input.classList.remove('border-red-500');
                });

                // Ao sair do campo, verifica se selecionou uma sugestão
                input.addEventListener('blur', function(e) {
                    setTimeout(() => {
                        if (!input._placeSelected) {
                            input.classList.add('border-red-500');
                            input.focus();
                        }
                    }, 200);
                });
            }
        }
    });
}

// Callback global do Google Maps - definido imediatamente
window.initTripFormMap = function() {
    console.log('Google Maps API carregada, inicializando autocomplete...');
    initPlacesAutocompleteStrict();
};

// Garantir que a função está disponível globalmente
if (typeof window.initTripFormMap !== 'function') {
    window.initTripFormMap = function() {
        console.log('Fallback: Google Maps API carregada');
        if (typeof initPlacesAutocompleteStrict === 'function') {
            initPlacesAutocompleteStrict();
        }
    };
}

// Fallback caso a API já esteja carregada antes do DOM
document.addEventListener('DOMContentLoaded', function() {

    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        initPlacesAutocompleteStrict();
    }

    // -------------------- Variáveis Globais e Steps --------------------
    let currentStep = 0;
    const steps = document.querySelectorAll('.form-step');
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    let meioLocomocao = document.querySelectorAll('.form-step')[1].querySelector('select').value;
    let voosCarregados = []; // Armazena voos carregados
    let flightSearchInitiated = false; // Controla se a busca de voos já foi iniciada

    // -------------------- Função para mostrar o passo atual --------------------
    function showStep(idx) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === idx);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            el.classList.toggle('active', i === idx);
        });

        // Se chegou no step de voos (step 4, index 4) e meio de locomoção é avião
        if (idx === 4 && meioLocomocao === 'Avião' && !flightSearchInitiated) {
            flightSearchInitiated = true;
            searchFlights();
        }

        // Se for o último passo, preenche a revisão
        if (idx === steps.length - 1) {
            preencherRevisao();
        }
    }

    // -------------------- Função para buscar voos --------------------
    async function searchFlights() {
        if (meioLocomocao === 'Avião') {
            document.querySelectorAll('.form-step')[4].classList.remove('hidden');
            
            // Contar quantos destinos foram preenchidos
            const destinosInputs = document.querySelectorAll('.destino-input');
            let destinosPreenchidos = 0;
            
            for (let input of destinosInputs) {
                if (input.value.trim() && input.getAttribute('data-valid') === 'true') {
                    destinosPreenchidos++;
                }
            }
            
            // Buscar datas do primeiro destino
            const primeiraDataInicio = document.getElementById('destino_data_inicio_0')?.value || '';
            const primeiraDataFim = document.getElementById('destino_data_fim_0')?.value || '';
            
            // Para qualquer quantidade de destinos, sempre buscar ida e volta usando as datas do primeiro destino
            const data = {
                dep_iata: document.getElementById('dep_iata')?.value || '',
                arr_iata: document.getElementById('arr_iata')?.value || '',
                date_departure: primeiraDataInicio,
                date_return: primeiraDataFim,
            };
            
            const container = document.getElementById('flights-container');
            container.innerHTML = '<div class="flex flex-col items-center justify-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div><div class="text-gray-600">Carregando voos em background...</div><div class="text-sm text-gray-500 mt-2">Você pode continuar preenchendo o formulário</div></div>';
            
            // Executar busca de voos de forma assíncrona sem bloquear
            searchFlightsAsync(data, container);
            
        } else {
            document.querySelectorAll('.form-step')[4].classList.add('hidden');
        }
    }

    // Função assíncrona separada para buscar voos
    async function searchFlightsAsync(data, container) {
        let timeout = false;
        const timer = setTimeout(() => {
            timeout = true;
            container.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><div class="text-red-800 font-semibold mb-2">Tempo esgotado</div><div class="text-red-600 text-sm mb-4">A busca por voos demorou mais que o esperado</div><button onclick="retryFlightSearch()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Tentar Novamente</button></div>';
        }, 15000); // Aumentei o timeout para 15 segundos
        
        try {
            const resVoos = await fetch('/formTrip/flights?' + new URLSearchParams(data));
            if (timeout) return;
            clearTimeout(timer);
            
            const result = await resVoos.json();
            if (result.flights && result.flights.length) {
                voosCarregados = result.flights;
                container.innerHTML = '';
                
                for (let i = 0; i < result.flights.length; i++) {
                    const flight = result.flights[i];
                    const resCard = await fetch('/formTrip/card-flight?' + new URLSearchParams({
                        flight: JSON.stringify(flight),
                        index: i
                    }));
                    const cardData = await resCard.json();
                    container.innerHTML += cardData.html;
                }
                
                // Adiciona listeners nos checkboxes dos voos
                document.querySelectorAll('.select-flight-checkbox').forEach((checkbox, idx) => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                            document.getElementById('selected_flight_index').value = idx;
                        }
                    });
                });
                
                // Mostrar notificação de sucesso
                showFlightNotification('Voos carregados com sucesso!', 'success');
                
            } else {
                container.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center"><div class="text-yellow-800 font-semibold mb-2">Nenhum voo encontrado</div><div class="text-yellow-600 text-sm mb-4">Não foram encontrados voos de ida e volta para os critérios informados</div><button onclick="retryFlightSearch()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Buscar Novamente</button></div>';
            }
        } catch (e) {
            if (!timeout) {
                console.error('Erro na busca de voos:', e);
                container.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><div class="text-red-800 font-semibold mb-2">Erro ao buscar voos</div><div class="text-red-600 text-sm mb-4">Ocorreu um erro técnico na busca</div><button onclick="retryFlightSearch()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Tentar Novamente</button></div>';
            }
        }
    }
    
    // Função global para retry
    window.retryFlightSearch = function() {
        flightSearchInitiated = true;
        searchFlights();
    };
    
    // Função para mostrar notificações sobre voos
    function showFlightNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-0 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas fa-plane"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // Função para formatar data yyyy-mm-dd para dd/mm/aaaa
    function formatarDataBR(data) {
        if (!data) return '';
        const [ano, mes, dia] = data.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    function preencherRevisao() {
        const reviewList = document.getElementById('reviewList');
        if (!reviewList) return;

        console.log('🔍 Iniciando coleta de dados para revisão...');

        // Pegar nome da viagem
        const nomeViagemInput = document.getElementById('nome_viagem');
        const nomeViagem = nomeViagemInput ? nomeViagemInput.value.trim() : '';
        console.log('📝 Nome da viagem:', nomeViagem);

        // Pegar origem
        const origemInput = document.getElementById('origem');
        const origem = origemInput ? origemInput.value.trim() : '';
        console.log('📍 Origem:', origem);

        // Pegar destinos preenchidos
        const destinosInputs = document.querySelectorAll('.destino-input');
        const destinosValidos = [];
        
        destinosInputs.forEach((input, index) => {
            if (input.value.trim()) {
                destinosValidos.push(input.value.trim());
                console.log(`🎯 Destino ${index + 1}:`, input.value.trim());
            }
        });
        
        const destino = destinosValidos.length > 0 ? destinosValidos.join(', ') : '';
        
        // Pegar dados básicos do step 1
        const numPessoasSelect = document.getElementById('num_pessoas');
        const numPessoas = numPessoasSelect ? numPessoasSelect.value : '';
        console.log('👥 Número de pessoas:', numPessoas);
        
        // Pegar idades dos viajantes
        const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
        const idades = Array.from(idadeInputs).map(input => input.value).filter(value => value !== '');
        console.log('👶 Idades:', idades);
        
        // Pegar datas dos destinos
        const dataInicioInputs = document.querySelectorAll('input[name="destino_data_inicio[]"]');
        const dataFimInputs = document.querySelectorAll('input[name="destino_data_fim[]"]');
        
        let datasInfo = [];
        for (let i = 0; i < Math.min(dataInicioInputs.length, dataFimInputs.length); i++) {
            const dataInicio = dataInicioInputs[i].value;
            const dataFim = dataFimInputs[i].value;
            const destinoCorrespondente = destinosValidos[i] || `Destino ${i + 1}`;
            
            if (dataInicio && dataFim) {
                datasInfo.push(`<span class="text-blue-100">${destinoCorrespondente}:</span> ${formatarDataBR(dataInicio)} a ${formatarDataBR(dataFim)}`);
                console.log(`📅 Datas ${destinoCorrespondente}:`, dataInicio, 'a', dataFim);
            }
        }
        
        // Dados da primeira e última data para compatibilidade
        let primeiraDataInicio = '';
        let ultimaDataFim = '';
        
        if (dataInicioInputs.length > 0 && dataInicioInputs[0].value) {
            primeiraDataInicio = dataInicioInputs[0].value;
        }
        
        for (let i = dataFimInputs.length - 1; i >= 0; i--) {
            if (dataFimInputs[i].value) {
                ultimaDataFim = dataFimInputs[i].value;
                break;
            }
        }

        // Pegar dados do step 2 (detalhes da viagem)
        const orcamentoInput = document.getElementById('orcamento');
        const orcamento = orcamentoInput ? orcamentoInput.value : '';
        console.log('💰 Orçamento:', orcamento);
        
        const meioSelect = document.querySelectorAll('.form-step')[1]?.querySelector('select');
        const meio = meioSelect ? meioSelect.value : '';
        console.log('🚗 Meio de locomoção:', meio);
        
        // Dados de aeroportos (se aplicável)
        let aeroportosInfo = '';
        if (meio === 'Avião') {
            const depIataInput = document.getElementById('dep_iata');
            const arrIataInput = document.getElementById('arr_iata');
            const depIata = depIataInput ? depIataInput.value.trim() : '';
            const arrIata = arrIataInput ? arrIataInput.value.trim() : '';
            console.log('✈️ Aeroportos:', depIata, '→', arrIata);
            
            if (depIata || arrIata) {
                aeroportosInfo = `<li><b>Aeroportos:</b> ${depIata || 'Não informado'} → ${arrIata || 'Não informado'}</li>`;
            }
        }
        
        // Dados de seguro
        const seguroSelect = document.getElementById('seguroViagem');
        const seguro = seguroSelect ? seguroSelect.value : '';
        let seguroInfo = '';
        if (seguro === 'Sim') {
            const nomeSeguro = sessionStorage.getItem('selectedSeguroName') || '';
            const destinoSeguroSelect = document.getElementById('MainContent_Cotador_selContinente');
            const destinoSeguro = destinoSeguroSelect ? destinoSeguroSelect.options[destinoSeguroSelect.selectedIndex]?.text || '' : '';
            console.log('🛡️ Seguro:', nomeSeguro, 'para', destinoSeguro);
            
            seguroInfo = `<li><b>Seguro de viagem:</b> ${seguro}`;
            if (nomeSeguro) seguroInfo += ` - ${nomeSeguro}`;
            if (destinoSeguro) seguroInfo += ` (${destinoSeguro})`;
            seguroInfo += `</li>`;
        } else {
            console.log('🛡️ Seguro:', seguro);
            seguroInfo = `<li><b>Seguro de viagem:</b> ${seguro}</li>`;
        }

        // Recuperar preferências do step 3
        const preferencesInput = document.getElementById('preferences');
        let preferences = [];
        if (preferencesInput && preferencesInput.value) {
            preferences = preferencesInput.value.split(',').map(p => p.trim()).filter(p => p.length > 0);
        }
        console.log('❤️ Preferências:', preferences);

        // Dados de voo do step 5
        let vooInfoHtml = '';
        const selectedFlightDataInput = document.getElementById('selected_flight_data');
        if (meio === 'Avião' && selectedFlightDataInput && selectedFlightDataInput.value) {
            try {
                const flightData = JSON.parse(selectedFlightDataInput.value);
                if (flightData.flights && flightData.flights.length > 0) {
                    const flight = flightData.flights[0];
                    const airline = flight.airline || 'Não selecionada';
                    const price = flightData.price || 'Não informado';
                    console.log('✈️ Voo selecionado:', airline, '-', price);
                    vooInfoHtml = `<li><b>Voo selecionado:</b> ${airline} - R$ ${price}</li>`;
                }
            } catch (e) {
                console.error("❌ Erro ao ler dados do voo:", e);
                vooInfoHtml = `<li><b>Voo:</b> Dados não disponíveis</li>`;
            }
        } else if (meio === 'Avião') {
            console.log('✈️ Nenhum voo selecionado para meio de locomoção: Avião');
            vooInfoHtml = `<li><b>Voo:</b> Nenhum voo selecionado</li>`;
        }

        // Montar HTML da revisão
        let reviewHtml = `
            ${nomeViagem ? `<li><b>✨ Nome da viagem:</b> ${nomeViagem}</li>` : ''}
            ${origem ? `<li><b>🏠 Origem:</b> ${origem}</li>` : ''}
            <li><b>🎯 Destinos:</b> ${destino || 'Nenhum destino informado'}</li>
            <li><b>👥 Número de pessoas:</b> ${numPessoas}</li>
            ${idades.length > 0 ? `<li><b>👶 Idades dos viajantes:</b> ${idades.join(', ')} anos</li>` : ''}
            ${primeiraDataInicio && ultimaDataFim ? `<li><b>📅 Período da viagem:</b> ${formatarDataBR(primeiraDataInicio)} a ${formatarDataBR(ultimaDataFim)}</li>` : ''}
            ${datasInfo.length > 0 ? `<li><b>📅 Datas por destino:</b><br>${datasInfo.join('<br>')}</li>` : ''}
            <li><b>🚗 Meio de locomoção:</b> ${meio}</li>
            ${orcamento ? `<li><b>💰 Orçamento:</b> R$ ${orcamento}</li>` : ''}
            ${aeroportosInfo}
            ${vooInfoHtml}
            ${seguroInfo}
            <li><b>❤️ Preferências:</b> ${preferences.length > 0 ? preferences.join(', ') : 'Nenhuma'}</li>
        `;

        console.log('✅ Dados coletados com sucesso!');
        reviewList.innerHTML = reviewHtml;
    }

    // -------------------- Eventos dos botões de navegação --------------------
    nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', async function() {
            console.log('validarStep', currentStep);
            if (!validarStep(currentStep)) {
                console.log('validarStep retornou false no step', currentStep);
                return;
            }
            console.log('Avançando step', currentStep);

            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value; // Atualiza sempre

            // Lógica de navegação entre os passos
            if (currentStep === 2) {
                if (seguro && seguro.value === 'Não' && meioLocomocao !== 'Avião') {
                    currentStep += 3;
                } else if (seguro && seguro.value === 'Não' && meioLocomocao === 'Avião') {
                    currentStep += 2;
                    flightSearchInitiated = true;
                    searchFlights(); // Remover await para não bloquear
                } else if (seguro && seguro.value === 'Sim') {
                    currentStep++;
                    // Usuário pode clicar manualmente no botão "Buscar Seguros" no step 4
                }
            } else if (currentStep === 3) {
                if (meioLocomocao !== 'Avião') {
                    currentStep += 2;
                } else {
                    currentStep++;
                    flightSearchInitiated = true;
                    searchFlights(); // Remover await para não bloquear
                }
            } else {
                currentStep++;
            }

            if (currentStep >= steps.length) currentStep = steps.length - 1;
            showStep(currentStep);
        });
    });

    prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value;

            // Se está no passo 5 ou 6 e seguro é "Não", pule a etapa de seguros ao voltar
            if (
                (currentStep === 5 && seguro && seguro.value === 'Não' && meioLocomocao !== 'Avião') ||
                (currentStep === 6 && seguro && seguro.value === 'Não' && meioLocomocao === 'Avião')
            ) {
                currentStep -= 3;
            } else if (currentStep === 4 && meioLocomocao === 'Avião' && seguro && seguro.value === 'Não') {
                currentStep -= 2;
            } else {
                currentStep--;
            }
            if (currentStep < 0) currentStep = 0;
            showStep(currentStep);
        });
    });

    // -------------------- Eventos de seleção de seguro e preferências --------------------
    document.querySelectorAll('.insurance-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.insurance-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
        });
    });

    document.querySelectorAll('.pref-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.classList.toggle('selected');
        });
    });

    // -------------------- Autocomplete de aeroportos --------------------
    function setupAirportAutocomplete(inputId, suggestionsId) {
        const input = document.getElementById(inputId);
        const suggestions = document.getElementById(suggestionsId);

        input.addEventListener('input', function () {
            const query = this.value;
            if (query.length < 2) {
                suggestions.innerHTML = '';
                return;
            }
            fetch(`/autocomplete-airports?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(item => {
                        html += `<div class="px-2 py-1 hover:bg-gray-100 cursor-pointer" data-iata="${item.iata_code}">${item.name} (${item.iata_code}) - ${item.city}</div>`;
                    });
                    suggestions.innerHTML = html;
                });
        });

        suggestions.addEventListener('click', function (e) {
            if (e.target && e.target.dataset.iata) {
                input.value = e.target.dataset.iata;
                suggestions.innerHTML = '';
            }
        });

        // Fecha sugestões ao clicar fora
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.innerHTML = '';
            }
        });
    }

    // Inicializa autocomplete para os campos de IATA
    setupAirportAutocomplete('dep_iata', 'dep_iata_suggestions');
    setupAirportAutocomplete('arr_iata', 'arr_iata_suggestions');

    // -------------------- Exibição dinâmica de campos conforme seleção --------------------
    const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
    const depIataContainer = document.getElementById('dep_iata_container');
    const seguro = document.getElementById('seguroViagem');
    const insuranceOptions = document.getElementById('insurance-options');

    meioSelect.addEventListener('change', function() {
        meioLocomocao = this.value;
        if (this.value === 'Avião') {
            depIataContainer.classList.remove('hidden');
        } else {
            depIataContainer.classList.add('hidden');
        }
    });

    seguro.addEventListener('change', function() {
        if (this.value === 'Sim') {
            insuranceOptions.classList.remove('hidden');
        } else {
            insuranceOptions.classList.add('hidden');
        }
    });

    // Exibe campo de IATA se "Avião" vier selecionado por padrão
    if (meioSelect.value === 'Avião') {
        depIataContainer.classList.remove('hidden');
    } else {
        depIataContainer.classList.add('hidden');
    }

    // -------------------- Inicialização --------------------
    showStep(currentStep);
});

// -------------------- Eventos de seleção de voo --------------------
document.addEventListener('DOMContentLoaded', function() {
    const flightsContainer = document.getElementById('flights-container');
    if (flightsContainer) {
        flightsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('select-flight-checkbox')) {
                // Desmarca todos os outros checkboxes
                document.querySelectorAll('.select-flight-checkbox').forEach(cb => {
                    if (cb !== e.target) cb.checked = false;
                });
                // Remove destaque de todos os cards
                document.querySelectorAll('.flight-card').forEach(card => card.classList.remove('border-4', 'border-blue-600'));
                // Se marcado, destaca o card e salva o índice
                if (e.target.checked) {
                    const idx = parseInt(e.target.dataset.index);
                    document.getElementById('selected_flight_index').value = idx;
                    document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                } else {
                    document.getElementById('selected_flight_index').value = '';
                    document.getElementById('selected_flight_data').value = '';
                }
            }
        });
    }

    // Listener individual para cada checkbox de voo
    document.querySelectorAll('.select-flight-checkbox').forEach((checkbox, idx) => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                document.getElementById('selected_flight_index').value = idx;
            }
        });
    });
});

// -------------------- Evento de submit do formulário --------------------
document.getElementById('multiStepForm').addEventListener('submit', function (e) {
    console.log('Formulário sendo enviado!');
    // Permitir o envio normal do formulário para o servidor
    // O formulário será enviado via POST para a rota definida
});

// -------------------- Tratamento de erros e mensagens de feedback --------------------
function showNotification(message, type = 'warning') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    `;
    notification.style.backgroundColor = type === 'error' ? '#EF4444' : (type === 'success' ? '#10B981' : '#F59E0B');
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

function validarStep(idx) {
    if (idx === 0) {
        // Validar nome da viagem
        const nomeViagem = document.getElementById('nome_viagem');
        if (!nomeViagem || !nomeViagem.value.trim()) {
            showNotification('Informe o nome da viagem.', 'error');
            if (nomeViagem) nomeViagem.focus();
            return false;
        }
        
        // Usar as funções de validação que já existem no step1.blade.php
        if (typeof window.validateTripForm === 'function') {
            return window.validateTripForm();
        }
        
        // Fallback para validação manual se a função não estiver disponível
        const origem = document.getElementById('origem');
        const adultos = document.querySelectorAll('.form-step')[0]?.querySelectorAll('select')[0];
        
        // Validar origem
        if (!origem || !origem.value.trim()) {
            showNotification('Informe a origem.', 'error');
            if (origem) origem.focus();
            return false;
        }
        
        // Validar número de adultos
        if (!adultos || !adultos.value) {
            showNotification('Informe o número de pessoas.', 'error');
            if (adultos) adultos.focus();
            return false;
        }
        
        // Validar destinos
        const allDestinos = document.querySelectorAll('.destino-input');
        let temDestinoValido = false;
        
        for (let destino of allDestinos) {
            if (destino.value.trim() && destino.getAttribute('data-valid') === 'true') {
                temDestinoValido = true;
                break;
            }
        }
        
        if (!temDestinoValido) {
            showNotification('Informe pelo menos um destino válido.', 'error');
            if (allDestinos[0]) allDestinos[0].focus();
            return false;
        }
        
        // Validar datas
        const allDataInicio = document.querySelectorAll('.destino-data-inicio');
        const allDataFim = document.querySelectorAll('.destino-data-fim');
        
        for (let i = 0; i < allDataInicio.length; i++) {
            const dataInicio = allDataInicio[i];
            const dataFim = allDataFim[i];
            
            // Verificar se os campos têm valores quando há destino correspondente
            const destinoCorrespondente = document.getElementById(`tripDestination_${i}`);
            if (destinoCorrespondente && destinoCorrespondente.value.trim()) {
                if (!dataInicio.value) {
                    showNotification(`Informe a data de início para o destino ${i + 1}.`, 'error');
                    dataInicio.focus();
                    return false;
                }
                if (!dataFim.value) {
                    showNotification(`Informe a data de fim para o destino ${i + 1}.`, 'error');
                    dataFim.focus();
                    return false;
                }
                
                // Validar se data de fim não é anterior à data de início
                if (dataFim.value < dataInicio.value) {
                    showNotification(`A data de fim não pode ser anterior à data de início no destino ${i + 1}.`, 'error');
                    dataFim.focus();
                    return false;
                }
                
                // Validar se data de início não é no passado (apenas para o primeiro destino)
                if (i === 0 && new Date(dataInicio.value) < new Date()) {
                    showNotification('A data de início do primeiro destino não pode ser no passado.', 'error');
                    dataInicio.focus();
                    return false;
                }
            }
        }
        
        // Validar idades
        const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
        let algumVazio = false;
        let temAdulto = false;
        idadeInputs.forEach(input => {
            if (input.value >= 18) {
                temAdulto = true;
            }
            if (!input.value.trim()) {
                algumVazio = true;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });
        if (!temAdulto) {
            showNotification('Pelo menos um adulto deve participar da viagem.', 'error');
            if (idadeInputs.length > 0) idadeInputs[0].focus();
            return false;
        }
        if (idadeInputs.length === 0 || algumVazio) {
            showNotification('Preencha todas as idades dos viajantes.', 'error');
            if (idadeInputs.length > 0) idadeInputs[0].focus();
            return false;
        }
        
        return true;
    }
    

    if (idx === 1) {
        const orcamento = document.querySelectorAll('.form-step')[1]?.querySelector('input[type="number"]');
        const meioLocomocao = document.querySelectorAll('.form-step')[1]?.querySelectorAll('select')[0];
        const seguro = document.getElementById('seguroViagem');
        if (!orcamento.value || Number(orcamento.value) <= 0) {
            showNotification('Informe um orçamento válido.', 'error');
            orcamento.focus();
            return false;
        }
        if (meioLocomocao && meioLocomocao.value === 'Avião') {
            const depIata = document.getElementById('dep_iata');
            const arrIata = document.getElementById('arr_iata');
            if (!depIata.value.trim()) {
                showNotification('Informe o aeroporto de partida.', 'error');
                depIata.focus();
                return false;
            }
            if (!arrIata.value.trim()) {
                showNotification('Informe o aeroporto de chegada.', 'error');
                arrIata.focus();
                return false;
            }
        }
        if (seguro && seguro.value === 'Sim') {
            const destinoSeguro = document.getElementById('MainContent_Cotador_selContinente');
            
            if (!destinoSeguro || !destinoSeguro.value || destinoSeguro.value === '') {
                alert('Selecione um destino para a viagem.');
                if (destinoSeguro) destinoSeguro.focus();
                return false;
            }
        }

    }
    return true;
}

// -------------------- Formatação data picker --------------------
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateDeparture = document.getElementById('date_departure');
    const dateReturn = document.getElementById('date_return');

    dateDeparture.min = today;
    dateReturn.min = dateDeparture.value || today;

    dateDeparture.addEventListener('change', function() {
        dateReturn.min = this.value;
        if (dateReturn.value < this.value){}
    });
});

// -------------------- Mostrar/Ocultar detalhes do voo --------------------
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('ver-detalhes-btn') || e.target.closest('.ver-detalhes-btn')) {
            const btn = e.target.closest('.ver-detalhes-btn');
            const targetId = btn.getAttribute('data-target');
            const detalhes = document.getElementById(targetId);
            if (detalhes) {
                detalhes.classList.toggle('hidden');
            }
        }
    });
});

// -------------------- Seleção de preferências (step 3) --------------------
document.addEventListener('DOMContentLoaded', function() {
    const prefBtns = document.querySelectorAll('.pref-btn');
    const preferencesInput = document.getElementById('preferences');

    let selectedPrefs = [];

    prefBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const pref = btn.querySelector('span.text-gray-700').innerText;
            btn.classList.toggle('bg-blue-100');
            if (selectedPrefs.includes(pref)) {
                selectedPrefs = selectedPrefs.filter(p => p !== pref);
            } else {
                selectedPrefs.push(pref);
            }
            // Atualiza o input hidden com todas as preferências selecionadas
            preferencesInput.value = selectedPrefs.join(',');
        });
    });
});