// -------------------- Autocomplete Google Places (Destino) --------------------
function initPlacesAutocompleteStrict() {
    try {
        console.log('Iniciando configuração de autocomplete...');
        
        // Verificar se a API do Google Maps está disponível
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.warn('Google Maps API não está disponível ainda');
            return;
        }

        const fields = [
            { id: 'tripDestination' },
            { id: 'origem' }
        ];

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                console.log(`Configurando autocomplete para: ${field.id}`);
                
                // Verificar se já existe nosso novo sistema de autocomplete (step1)
                if (input.classList.contains('origem-input') || 
                    input.classList.contains('destino-input') ||
                    input.hasAttribute('data-new-autocomplete')) {
                    return;
                }
                
                if (!input._autocompleteInitialized) {
                    try {
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
                    } catch (error) {
                        console.error(`Erro ao configurar autocomplete para ${field.id}:`, error);
                    }
                }
            }
        });
    } catch (error) {
        console.error('Erro geral na configuração do autocomplete:', error);
    }
}

// Callback global do Google Maps - definido imediatamente
window.initTripFormMap = function() {
    try {
        console.log('Google Maps API callback iniciado');
        initPlacesAutocompleteStrict();
    } catch (error) {
        console.error('Erro no callback do Google Maps:', error);
    }
};

// Garantir que a função está disponível globalmente
if (typeof window.initTripFormMap !== 'function') {
    window.initTripFormMap = function() {
        try {
            if (typeof initPlacesAutocompleteStrict === 'function') {
                initPlacesAutocompleteStrict();
            }
        } catch (error) {
            console.error('Erro no fallback do Google Maps:', error);
        }
    };
}

// Fallback caso a API já esteja carregada antes do DOM
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página do formulário
    const isFormPage = document.getElementById('multiStepForm') !== null;
    
    if (!isFormPage) {
        return;
    }

    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        console.log('Google Maps API disponível, iniciando autocomplete...');
        initPlacesAutocompleteStrict();
    } else {
        console.log('Google Maps API ainda não disponível, aguardando callback...');
        // A API será inicializada pelo callback initTripFormMap quando carregada
    }

    // -------------------- Variáveis Globais e Steps --------------------
    let currentStep = 0;
    const stepsNodeList = document.querySelectorAll('.form-step');
    const steps = Array.from(stepsNodeList); // usar array para facilitar manipulação
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    let meioLocomocao = '';
    const formStepElement = document.querySelectorAll('.form-step')[1];
    if (formStepElement && formStepElement.querySelector('select')) {
        meioLocomocao = formStepElement.querySelector('select').value;
    }
    let voosCarregados = []; // Armazena voos carregados
    let flightSearchInitiated = false; // Controla se a busca de voos já foi iniciada
    // Descobrir dinamicamente o índice do step de aluguel de carros (se existir)
    const carStepIndex = steps.findIndex(s => s.querySelector('h2') && s.querySelector('h2').innerText.trim().toLowerCase().includes('aluguel de carros'));
    const insuranceStepIndex = steps.findIndex(s => s.querySelector('#seguros-container') || s.querySelector('#tabs-seguros-container') || (s.querySelector('h2') && s.querySelector('h2').innerText.trim().toLowerCase().includes('seguro')));
    let carRentalNeeded = false; // flag para controlar se exibir/pular o step de aluguel

    // -------------------- Função para mostrar o passo atual --------------------
    function showStep(idx) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === idx);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            el.classList.toggle('active', i === idx);
        });

        // Se o step atual for o de aluguel e ele não for necessário, pular para o próximo
        if (idx === carStepIndex && carStepIndex !== -1 && !carRentalNeeded) {
            idx = idx + 1;
            currentStep = idx;
            steps.forEach((step, i) => step.classList.toggle('active', i === idx));
            document.querySelectorAll('.step-indicator').forEach((el, i) => el.classList.toggle('active', i === idx));
        }

        // Se chegou no step de voos (index dinâmico) e meio de locomoção é avião
        const flightsIndex = steps.findIndex(s => s.querySelector('h2') && s.querySelector('h2').innerText.trim().toLowerCase().includes('voos'));
        if (idx === flightsIndex && meioLocomocao === 'Avião' && !flightSearchInitiated) {
            flightSearchInitiated = true;
            searchFlights();
        }

        // Se chegou no step de aluguel e o aluguel for necessário, iniciar busca de carros
        if (idx === carStepIndex && carStepIndex !== -1 && carRentalNeeded) {
            // Inicia busca de veículos em background (não bloqueará o usuário)
            if (typeof initiateCarSearch === 'function') {
                initiateCarSearch();
            }
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

        // Preparar dados dos viajantes para garantir que estejam atualizados
        prepararDadosViajantes();



        // Pegar nome da viagem
        const nomeViagemInput = document.getElementById('nome_viagem');
        const nomeViagem = nomeViagemInput ? nomeViagemInput.value.trim() : '';


        // Pegar origem
        const origemInput = document.getElementById('origem');
        const origem = origemInput ? origemInput.value.trim() : '';


        // Pegar destinos preenchidos
        const destinosInputs = document.querySelectorAll('.destino-input');
        const destinosValidos = [];
        
        destinosInputs.forEach((input, index) => {
            if (input.value.trim()) {
                destinosValidos.push(input.value.trim());

            }
        });
        
        const destino = destinosValidos.length > 0 ? destinosValidos.join(', ') : '';
        
        // Pegar dados básicos do step 1
        const numPessoasSelect = document.getElementById('num_pessoas');
        const numPessoas = numPessoasSelect ? numPessoasSelect.value : '';

        
        // Pegar idades dos viajantes
        const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
        const idades = Array.from(idadeInputs).map(input => input.value).filter(value => value !== '');

        // Coletar nomes dos viajantes
        const nomesViajantes = [];
        const numPessoasInt = parseInt(numPessoas) || 1;
        for (let i = 0; i < numPessoasInt; i++) {
            const nomeInput = document.getElementById(`viajante-nome-${i}`);
            const nomePersonalizado = nomeInput ? nomeInput.value.trim() : '';
            const nomeViajante = nomePersonalizado || `Viajante ${i + 1}`;
            const idade = idades[i] || 'Não informada';
            nomesViajantes.push(`${nomeViajante} (${idade} anos)`);
        }

        
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

        
        const meioSelect = document.querySelectorAll('.form-step')[1]?.querySelector('select');
        const meio = meioSelect ? meioSelect.value : '';

        
        // Dados de aeroportos (se aplicável)
        let aeroportosInfo = '';
        if (meio === 'Avião') {
            const depIataInput = document.getElementById('dep_iata');
            const arrIataInput = document.getElementById('arr_iata');
            const depIata = depIataInput ? depIataInput.value.trim() : '';
            const arrIata = arrIataInput ? arrIataInput.value.trim() : '';

            
            if (depIata || arrIata) {
                aeroportosInfo = `<li><b>Aeroportos:</b> ${depIata || 'Não informado'} → ${arrIata || 'Não informado'}</li>`;
            }
        }
        
        // Dados de seguro - atualizado para múltiplos viajantes
        const seguroSelect = document.getElementById('seguroViagem');
        const seguro = seguroSelect ? seguroSelect.value : '';
        let seguroInfo = '';
        
        if (seguro === 'Sim') {
            // Verificar se há seguros selecionados por viajante (novo sistema)
            if (window.selectedInsurancesByViajante && Object.keys(window.selectedInsurancesByViajante).length > 0) {
                seguroInfo = `<li><b>🛡️ Seguros de viagem selecionados:</b><br>`;
                
                // Obter informações dos viajantes para mostrar nomes e idades
                const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
                const idades = Array.from(idadeInputs).map(input => parseInt(input.value) || 25);
                
                const segurosViajantes = [];
                Object.keys(window.selectedInsurancesByViajante).forEach(viaganteIndex => {
                    const index = parseInt(viaganteIndex);
                    const seguroData = window.selectedInsurancesByViajante[viaganteIndex];
                    const idade = idades[index] || 25;
                    
                    if (seguroData && seguroData.insuranceData) {
                        const { seguradora, plano } = seguroData.insuranceData;
                        // Usar nome personalizado se disponível, senão fallback para sessionStorage ou padrão
                        const nomeViajante = seguroData.nomeViajante || 
                                           sessionStorage.getItem(`nomeViajante_${index}`) || 
                                           `Viajante ${index + 1}`;
                        
                        segurosViajantes.push(
                            `<span class="text-blue-100 ml-4">• ${nomeViajante} (${idade} anos):</span> ${seguradora} - ${plano}`
                        );
                    }
                });
                
                if (segurosViajantes.length > 0) {
                    seguroInfo += segurosViajantes.join('<br>');
                } else {
                    seguroInfo += `<span class="text-blue-100 ml-4">Nenhum seguro selecionado ainda</span>`;
                }
                
                seguroInfo += `</li>`;
            } else {
                // Fallback para sistema antigo (compatibilidade)
                const nomeSeguro = sessionStorage.getItem('selectedSeguroName') || '';
                const destinoSeguroSelect = document.getElementById('MainContent_Cotador_selContinente');
                const destinoSeguro = destinoSeguroSelect ? destinoSeguroSelect.options[destinoSeguroSelect.selectedIndex]?.text || '' : '';

                seguroInfo = `<li><b>🛡️ Seguro de viagem:</b> ${seguro}`;
                if (nomeSeguro) seguroInfo += ` - ${nomeSeguro}`;
                if (destinoSeguro) seguroInfo += ` (${destinoSeguro})`;
                seguroInfo += `</li>`;
            }
        } else {
            seguroInfo = `<li><b>🛡️ Seguro de viagem:</b> ${seguro}</li>`;
        }

        // Recuperar preferências do step 3
        const preferencesInput = document.getElementById('preferences');
        let preferences = [];
        if (preferencesInput && preferencesInput.value) {
            preferences = preferencesInput.value.split(',').map(p => p.trim()).filter(p => p.length > 0);
        }


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

                    vooInfoHtml = `<li><b>Voo selecionado:</b> ${airline} - R$ ${price}</li>`;
                }
            } catch (e) {
                console.error("❌ Erro ao ler dados do voo:", e);
                vooInfoHtml = `<li><b>Voo:</b> Dados não disponíveis</li>`;
            }
        } else if (meio === 'Avião') {

            vooInfoHtml = `<li><b>Voo:</b> Nenhum voo selecionado</li>`;
        }

        // Dados do veículo selecionado (step 6)
        let veiculoInfoHtml = '';
        const selectedCarDataInput = document.getElementById('selected_car_data');
        if (selectedCarDataInput && selectedCarDataInput.value) {
            try {
                const carData = JSON.parse(selectedCarDataInput.value);
                const carNome = carData.nome || carData.nome_veiculo || carData.title || 'Veículo selecionado';
                const carPreco = (carData.preco && carData.preco.total) ? carData.preco.total : (carData.preco_total || null);
                const localRet = (carData.local_retirada && (carData.local_retirada.endereco || carData.local_retirada.nome)) ? (carData.local_retirada.endereco || carData.local_retirada.nome) : (carData.nome_local || carData.endereco_retirada || 'Local não informado');
                veiculoInfoHtml = `<li><b>Veículo selecionado:</b> ${carNome}${carPreco ? ' — R$ ' + carPreco : ''}<br><span class="text-sm text-gray-400">Local de retirada: ${localRet}</span></li>`;

                // Também popular o bloco específico em Step 7
                const selCarDiv = document.getElementById('selectedCarReview');
                if (selCarDiv) {
                    let html = `<div class="flex items-start gap-4">
                        <div class="w-32 flex-shrink-0"><img src="${carData.imagem || carData.imagem_url || '/imgs/default-car.png'}" class="w-full h-20 object-contain" alt="${carNome}"></div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${carNome}</div>
                            ${carPreco ? `<div class="text-blue-600 font-bold">R$ ${String(carPreco).replace('.',',')}</div>` : ''}
                            <div class="text-sm text-gray-600 mt-2">${localRet}</div>
                        </div>
                    </div>`;
                    selCarDiv.innerHTML = html;
                }

            } catch (e) {
                console.error('Erro ao parsear selected_car_data:', e);
                veiculoInfoHtml = `<li><b>Veículo:</b> Dados não disponíveis</li>`;
            }
        } else {
            veiculoInfoHtml = `<li><b>Veículo:</b> Nenhum veículo selecionado</li>`;
            const selCarDiv = document.getElementById('selectedCarReview');
            if (selCarDiv) selCarDiv.innerHTML = '<p class="italic text-gray-500">Nenhum veículo selecionado.</p>';
        }

        // Montar HTML da revisão
        let reviewHtml = `
            ${nomeViagem ? `<li><b>✨ Nome da viagem:</b> ${nomeViagem}</li>` : ''}
            ${origem ? `<li><b>🏠 Origem:</b> ${origem}</li>` : ''}
            <li><b>🎯 Destinos:</b> ${destino || 'Nenhum destino informado'}</li>
            <li><b>👥 Número de pessoas:</b> ${numPessoas}</li>
            ${nomesViajantes.length > 0 ? `<li><b>👤 Viajantes:</b> ${nomesViajantes.join(', ')}</li>` : ''}
            ${idades.length > 0 ? `<li><b>👶 Idades dos viajantes:</b> ${idades.join(', ')} anos</li>` : ''}
            ${primeiraDataInicio && ultimaDataFim ? `<li><b>📅 Período da viagem:</b> ${formatarDataBR(primeiraDataInicio)} a ${formatarDataBR(ultimaDataFim)}</li>` : ''}
            ${datasInfo.length > 0 ? `<li><b>📅 Datas por destino:</b><br>${datasInfo.join('<br>')}</li>` : ''}
            <li><b>🚗 Meio de locomoção:</b> ${meio}</li>
            ${orcamento ? `<li><b>💰 Orçamento:</b> R$ ${orcamento}</li>` : ''}
            ${aeroportosInfo}
            ${vooInfoHtml}
            ${veiculoInfoHtml}
            ${seguroInfo}
            <li><b>❤️ Preferências:</b> ${preferences.length > 0 ? preferences.join(', ') : 'Nenhuma'}</li>
        `;


        reviewList.innerHTML = reviewHtml;
    }

    // -------------------- Eventos dos botões de navegação --------------------
    nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', async function() {

            if (!validarStep(currentStep)) {

                return;
            }

            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value; // Atualiza sempre

            // Atualiza flag de necessidade do step de aluguel
            carRentalNeeded = (meioLocomocao === 'Carro (alugado)');
            // Mostrar/ocultar o elemento do step de aluguel se existir
            if (carStepIndex !== -1) {
                const carStepEl = steps[carStepIndex];
                if (carRentalNeeded) {
                    carStepEl.classList.remove('hidden');
                } else {
                    carStepEl.classList.add('hidden');
                }
            }

            // Lógica de navegação entre os passos com pulo condicional (robusta)
            let nextStep = currentStep + 1;

            // recalcular índices e valores no momento do clique
            const flightsIndex2 = steps.findIndex(s => s.querySelector('h2') && s.querySelector('h2').innerText.trim().toLowerCase().includes('voos'));
            const carIndexNow = carStepIndex;
            const seguroVal = document.getElementById('seguroViagem') ? document.getElementById('seguroViagem').value : '';
            const meioAtual = (meioLocomocao || '').toString().trim();

            function shouldSkipStep(index) {
                if (index < 0 || index >= steps.length) return false;
                // pular aluguel se não necessário
                if (index === carIndexNow && !carRentalNeeded) return true;
                // pular seguros se usuário escolheu 'Não'
                if (index === insuranceStepIndex && seguroVal === 'Não') return true;
                // pular voos se meio não for Avião
                if (index === flightsIndex2 && meioAtual !== 'Avião') return true;
                return false;
            }

            // avançar até encontrar um passo válido
            while (shouldSkipStep(nextStep) && nextStep < steps.length) {
                nextStep++;
            }

            // Se vamos entrar no passo de voos e for avião, iniciar busca
            if (nextStep === flightsIndex2 && meioAtual === 'Avião' && !flightSearchInitiated) {
                flightSearchInitiated = true;
                searchFlights();
            }

            currentStep = Math.min(nextStep, steps.length - 1);
            showStep(currentStep);
        });
    });

    prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value;

            // Atualiza flag
            carRentalNeeded = (meioLocomocao === 'Carro (alugado)');

            // Navegação para trás com pulo condicional (robusta)
            let prevStep = currentStep - 1;

            const flightsIndex3 = steps.findIndex(s => s.querySelector('h2') && s.querySelector('h2').innerText.trim().toLowerCase().includes('voos'));
            const seguroValuePrev = document.getElementById('seguroViagem') ? document.getElementById('seguroViagem').value : '';
            const meioAtualPrev = (meioLocomocao || '').toString().trim();

            function shouldSkipPrev(index) {
                if (index < 0 || index >= steps.length) return false;
                if (index === carStepIndex && !carRentalNeeded) return true;
                if (index === insuranceStepIndex && seguroValuePrev === 'Não') return true;
                if (index === flightsIndex3 && meioAtualPrev !== 'Avião') return true;
                return false;
            }

            while (shouldSkipPrev(prevStep) && prevStep > 0) {
                prevStep--;
            }

            // Segurança: garantir limites
            if (prevStep < 0) prevStep = 0;

            currentStep = prevStep;
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
    const carsRentContainer = document.getElementById('cars-rent');
    const seguro = document.getElementById('seguroViagem');
    const insuranceOptions = document.getElementById('insurance-options');

    meioSelect.addEventListener('change', function() {
        meioLocomocao = this.value;
        if (this.value === 'Avião') {
            depIataContainer.classList.remove('hidden');
        } else {
            depIataContainer.classList.add('hidden');
        }

        // Mostrar opções de aluguel no step 2 se escolher 'Carro (alugado)'
        if (carsRentContainer) {
            if (this.value === 'Carro (alugado)') {
                carsRentContainer.classList.remove('hidden');
            } else {
                carsRentContainer.classList.add('hidden');
            }
        }
    });

    seguro.addEventListener('change', function() {
        if (this.value === 'Sim') {
            insuranceOptions.classList.remove('hidden');
        } else {
            insuranceOptions.classList.add('hidden');
        }
        
        // Atualizar a visualização do step 4 de seguros se estivermos nele
        const currentStepElement = document.querySelector('.form-step.active');
        if (currentStepElement && currentStepElement.querySelector('#seguros-container')) {
            // Resetar a visualização do step 4
            document.getElementById('loading-seguros').style.display = 'none';
            document.getElementById('tabs-seguros-container').style.display = 'none';
            document.getElementById('seguros-container').style.display = 'none';
            
            // Trigger a re-evaluation of insurance display based on new selection
            setTimeout(() => {
                if (this.value === 'Sim') {
                    // Se mudou para "Sim", fazer busca de seguros
                    if (typeof window.restartSearch === 'function') {
                        window.restartSearch();
                    }
                } else {
                    // Se mudou para "Não", mostrar apenas as tabs dos viajantes
                    if (typeof window.showTravelerTabsOnly === 'function') {
                        window.showTravelerTabsOnly();
                    }
                }
            }, 100);
        }
    });

    // Exibe campo de IATA se "Avião" vier selecionado por padrão
    if (meioSelect.value === 'Avião') {
        depIataContainer.classList.remove('hidden');
    } else {
        depIataContainer.classList.add('hidden');
    }
    if (carsRentContainer) {
        if (meioSelect.value === 'Carro (alugado)') {
            carsRentContainer.classList.remove('hidden');
        } else {
            carsRentContainer.classList.add('hidden');
        }
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
const multiStepForm = document.getElementById('multiStepForm');
if (multiStepForm) {
    multiStepForm.addEventListener('submit', async function (e) {
        // Interceptar para garantir que o veículo selecionado seja salvo via AJAX antes do envio final
        e.preventDefault();

        // Coletar dados dos viajantes e seguros antes do envio
        prepararDadosViajantes();

        // Proteção contra envios duplicados do formulário
        if (window.isSubmitting) {
            console.log('Submit já em progresso, ignorando submissão duplicada');
            return;
        }
        window.isSubmitting = true;

        // Se já salvamos o veículo via AJAX anteriormente, prosseguir
        if (window.vehicleSavedViaAjax) {
            // Submeter o formulário normalmente (usando submit() para não re-disparar este listener)
            return multiStepForm.submit();
        }

        // Se há um veículo selecionado localmente, tentar salvar via AJAX apenas se a viagem já existir
        const selectedCarDataInput = document.getElementById('selected_car_data');
        const selectedCarIndexInput = document.getElementById('selected_car_index');

        if (selectedCarDataInput && selectedCarDataInput.value) {
            // Determinar se a viagem já tem ID (edição) ou é criação nova
            const existingViagemId = window.VIAGEM_DATA && window.VIAGEM_DATA.pk_id_viagem ? window.VIAGEM_DATA.pk_id_viagem : (document.querySelector('input[name="pk_id_viagem"]') ? document.querySelector('input[name="pk_id_viagem"]').value : null);

            // Se não existir viagemId, não tentamos salvar via AJAX (pois o endpoint exige fk_id_viagem existente).
            // Neste caso, deixamos o formulário submeter normalmente. O backend que receber a criação da viagem
            // deve ler o campo `selected_car_data` e persistir o veículo após criar a viagem.
            if (!existingViagemId) {
                showNotification('Veículo será salvo automaticamente após a criação da viagem.', 'info');
                return multiStepForm.submit();
            }

            // Caso exista viagemId, salvamos via AJAX antes do submit final
            let carObj = null;
            try {
                carObj = JSON.parse(selectedCarDataInput.value);
            } catch (err) {
                showNotification('Dados do veículo corrompidos. Não foi possível salvar.', 'error');
                return;
            }

            const idx = selectedCarIndexInput && selectedCarIndexInput.value ? parseInt(selectedCarIndexInput.value) : null;

            const saved = await saveSelectedCar(idx, carObj);
            if (!saved) {
                showNotification('Não foi possível salvar o veículo. Tente novamente antes de finalizar.', 'error');
                window.isSubmitting = false;
                return;
            }
            // marca flag para não salvar novamente
            window.vehicleSavedViaAjax = true;
        }
        // finalmente submeter o formulário
        return multiStepForm.submit();
    });
}

// -------------------- Função para preparar dados dos viajantes --------------------
function prepararDadosViajantes() {
    const viajantesData = [];
    const segurosViajantesData = [];
    
    // Obter informações básicas dos viajantes
    const numPessoasSelect = document.getElementById('num_pessoas');
    const numPessoas = numPessoasSelect ? parseInt(numPessoasSelect.value) || 1 : 1;
    const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
    
    for (let i = 0; i < numPessoas; i++) {
        // Coletar dados básicos do viajante
        const idade = idadeInputs[i] ? parseInt(idadeInputs[i].value) || 25 : 25;
        const nomeInput = document.getElementById(`viajante-nome-${i}`);
        const nomePersonalizado = nomeInput ? nomeInput.value.trim() : '';
        const nomeViajante = nomePersonalizado || `Viajante ${i + 1}`;
        
        // Dados do viajante
        const viaganteData = {
            index: i,
            nome: nomeViajante,
            idade: idade,
            nome_personalizado: nomePersonalizado
        };
        viajantesData.push(viaganteData);
        
        // Verificar se há seguro selecionado para este viajante
        if (window.selectedInsurancesByViajante && window.selectedInsurancesByViajante[i]) {
            const seguroData = window.selectedInsurancesByViajante[i];
            const seguroViajante = {
                viajante_index: i,
                viajante_nome: nomeViajante,
                insurance_data: seguroData.insuranceData,
                insurance_index: seguroData.insuranceIndex
            };
            segurosViajantesData.push(seguroViajante);
        }
    }
    
    // Salvar nos inputs hidden
    document.getElementById('viajantesData').value = JSON.stringify(viajantesData);
    document.getElementById('segurosViajantesData').value = JSON.stringify(segurosViajantesData);
}

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

        // Se o meio for Carro (alugado), exigir campos de especificação de aluguel
        const meioLoc = (typeof meioLocomocao === 'string' && meioLocomocao) ? meioLocomocao : (document.querySelectorAll('.form-step')[1]?.querySelector('select')?.value || '');
        if (meioLoc === 'Carro (alugado)') {
            const pickup = document.querySelector('input[name="car_pickup_datetime"]');
            const ret = document.querySelector('input[name="car_return_datetime"]');
            if (!pickup || !pickup.value) {
                showNotification('Informe a data/hora de retirada do carro.', 'error');
                if (pickup) pickup.focus();
                return false;
            }
            if (!ret || !ret.value) {
                showNotification('Informe a data/hora de devolução do carro.', 'error');
                if (ret) ret.focus();
                return false;
            }
            // Validação adicional: devolução não pode ser antes da retirada
            if (ret.value < pickup.value) {
                showNotification('A data/hora de devolução não pode ser anterior à retirada.', 'error');
                ret.focus();
                return false;
            }
        }

    }
    return true;
}

// -------------------- Formatação data picker --------------------
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de criação de viagem
    if (!window.location.pathname.includes('formTrip') && !document.getElementById('date_departure')) {
        return;
    }

    const today = new Date().toISOString().split('T')[0];
    const dateDeparture = document.getElementById('date_departure');
    const dateReturn = document.getElementById('date_return');

    if (dateDeparture) {
        dateDeparture.min = today;
    }
    
    if (dateReturn && dateDeparture) {
        dateReturn.min = dateDeparture.value || today;
        
        dateDeparture.addEventListener('change', function() {
            dateReturn.min = this.value;
            if (dateReturn.value < this.value) {
                dateReturn.value = '';
            }
        });
    }
});

// -------------------- Snap de 30 minutos para inputs de aluguel --------------------
document.addEventListener('DOMContentLoaded', function() {
    const pickup = document.getElementById('car_pickup_datetime');
    const ret = document.getElementById('car_return_datetime');

    function parseLocalDateTime(val) {
        if (!val) return null;
        const parts = val.split('T');
        if (parts.length < 2) return null;
        const [datePart, timePart] = parts;
        const [y, m, d] = datePart.split('-').map(n => parseInt(n, 10));
        const [hh, mm] = timePart.split(':').map(n => parseInt(n, 10));
        return new Date(y, m - 1, d, hh || 0, mm || 0, 0, 0);
    }

    function formatLocalDateTime(dt) {
        if (!dt) return '';
        const y = dt.getFullYear();
        const mo = String(dt.getMonth() + 1).padStart(2, '0');
        const d = String(dt.getDate()).padStart(2, '0');
        const hh = String(dt.getHours()).padStart(2, '0');
        const mm = String(dt.getMinutes()).padStart(2, '0');
        return `${y}-${mo}-${d}T${hh}:${mm}`;
    }

    function snapTo30(dt) {
        const mins = dt.getMinutes();
        const snapped = Math.round(mins / 30) * 30;
        dt.setMinutes(snapped);
        dt.setSeconds(0);
        dt.setMilliseconds(0);
        return dt;
    }

    function snapAndSet(el) {
        if (!el) return;
        const val = el.value;
        const dt = parseLocalDateTime(val);
        if (!dt) return;
        const snapped = snapTo30(new Date(dt.getTime()));
        el.value = formatLocalDateTime(snapped);
    }

    function ensureReturnAfterPickup() {
        if (!pickup || !ret) return;
        const p = parseLocalDateTime(pickup.value);
        const r = parseLocalDateTime(ret.value);
        if (!p || !r) return;
        if (r < p) {
            const adjusted = new Date(p.getTime() + 30 * 60 * 1000);
            snapTo30(adjusted);
            ret.value = formatLocalDateTime(adjusted);
            showNotification('A data/hora de devolução foi ajustada para ser pelo menos 30 minutos após a retirada.', 'warning');
        }
    }

    ['change', 'blur', 'input'].forEach(evt => {
        if (pickup) pickup.addEventListener(evt, function() {
            snapAndSet(pickup);
            ensureReturnAfterPickup();
        });
        if (ret) ret.addEventListener(evt, function() {
            snapAndSet(ret);
            ensureReturnAfterPickup();
        });
    });
});

// -------------------- Mostrar/Ocultar detalhes do voo --------------------
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.ver-detalhes-btn');
        if (btn) {
            e.preventDefault(); // <-- Adicione isto!
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
    // Esperar um pouco para garantir que o DOM esteja totalmente carregado
    setTimeout(() => {
        const prefBtns = document.querySelectorAll('.pref-btn');
        const preferencesInput = document.getElementById('preferences');

        if (!prefBtns.length) {
            console.warn('⚠️ Nenhum botão de preferência encontrado');
            return;
        }

        if (!preferencesInput) {
            console.warn('⚠️ Input de preferências não encontrado');
            return;
        }

        let selectedPrefs = [];

        prefBtns.forEach((btn, index) => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const prefText = btn.getAttribute('data-preference') || btn.querySelector('span').innerText;
                
                // Toggle da classe selected com efeito visual imediato
                const wasSelected = btn.classList.contains('selected');
                btn.classList.toggle('selected');
                
                // Feedback visual imediato
                if (btn.classList.contains('selected')) {
                    btn.style.borderColor = '#22c55e';
                    btn.style.background = 'linear-gradient(135deg, #dcfce7, #bbf7d0)';
                    btn.style.transform = 'scale(1.05)';
                } else {
                    btn.style.borderColor = '#e5e7eb';
                    btn.style.background = '#f9fafb';
                    btn.style.transform = 'scale(1)';
                }
                
                // Atualizar array
                if (wasSelected) {
                    selectedPrefs = selectedPrefs.filter(p => p !== prefText);
                } else {
                    selectedPrefs.push(prefText);
                }
                
                // Atualizar input hidden
                preferencesInput.value = selectedPrefs.join(',');
                
                // Mostrar feedback visual temporário
                const span = btn.querySelector('span');
                const originalText = span.innerText;
                if (!wasSelected) {
                    span.innerText = '✓ ' + originalText;
                    setTimeout(() => {
                        if (btn.classList.contains('selected')) {
                            span.innerText = originalText;
                        }
                    }, 1000);
                }
            });
        });
    }, 100);
});

// -------------------- Loader global --------------------
document.addEventListener("DOMContentLoaded", function() {
    const loader = document.getElementById("global-loader");
    if (loader) {
        loader.classList.add("hidden");
    }

    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", e => {
            // Não mostra loader se for botão de detalhes do voo
            if (link.classList.contains('ver-detalhes-btn')) return;
            if (link.href && link.href.startsWith(window.location.origin)) {
                loader.classList.remove("hidden");
            }
        });
    });
});

// -------------------- Integração de busca de aluguel de carros --------------------
(function() {
    // Função que monta o payload esperado pelo endpoint de veículos
    function getCarSearchFormData() {
        // local_retirada: usar primeiro destino preenchido, ou origem
        const destinoFirst = document.querySelector('.destino-input');
        const origem = document.getElementById('origem');
        const local_retirada = (destinoFirst && destinoFirst.value.trim()) ? destinoFirst.value.trim() : (origem ? origem.value.trim() : '');

        // datas e horas a partir dos inputs de pick-up/return
        const pickup = document.getElementById('car_pickup_datetime');
        const ret = document.getElementById('car_return_datetime');
        let data_retirada = '', hora_retirada = '', data_devolucao = '', hora_devolucao = '';
        if (pickup && pickup.value) {
            const parts = pickup.value.split('T');
            data_retirada = parts[0];
            hora_retirada = parts[1] ? parts[1].slice(0,5) : '';
        }
        if (ret && ret.value) {
            const parts = ret.value.split('T');
            data_devolucao = parts[0];
            hora_devolucao = parts[1] ? parts[1].slice(0,5) : '';
        }

        return {
            local_retirada,
            data_retirada,
            hora_retirada,
            data_devolucao,
            hora_devolucao
        };
    }

    // Inicia a busca e faz polling até obter resultados
    window.initiateCarSearch = async function() {
        try {
            const payload = getCarSearchFormData();

            // Validação mínima antes de disparar
            if (!payload.local_retirada || !payload.data_retirada || !payload.hora_retirada || !payload.data_devolucao || !payload.hora_devolucao) {
                // Não iniciar se estiver incompleto — usuário ainda pode preencher
                console.log('Busca de carros não iniciada: dados incompletos', payload);
                return;
            }

            const url = window.APP_ROUTES && window.APP_ROUTES.searchVehicles ? window.APP_ROUTES.searchVehicles : '/vehicles/search';

            // Indicador visual simplificado
            const container = document.getElementById('cars-container');
            if (container) {
                container.innerHTML = '<div class="flex flex-col items-center justify-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div><div class="text-gray-600">Buscando veículos...</div></div>';
            }

            // Primeiro POST para iniciar processo (o endpoint retorna 'carregando' e dispatcha job)
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                console.error('Erro ao iniciar busca de carros', err);
                if (container) container.innerHTML = '<div class="text-red-600">Erro ao iniciar busca de veículos.</div>';
                return;
            }

            const data = await res.json();

            // Se já estiver concluído e retornar veículos
            if (data.status === 'concluido' && Array.isArray(data.veiculos)) {
                renderCarResults(data.veiculos, data.alerta || null);
                return;
            }

            // Caso esteja carregando, iniciar polling
            if (data.status === 'carregando' || !data.status) {
                pollCarSearch(payload, 0, container);
            } else if (data.status === 'failed') {
                if (container) container.innerHTML = '<div class="text-red-600">A busca por veículos falhou.</div>';
            }

        } catch (e) {
            console.error('Erro em initiateCarSearch:', e);
        }
    };

    // Polling até obter resultados (timeout em N tentativas)
    async function pollCarSearch(payload, attempt = 0, container) {
        const maxAttempts = 40; // ~40 * 3s = 120s
        const waitMs = 3000;

        try {
            await new Promise(res => setTimeout(res, waitMs));

            const url = window.APP_ROUTES && window.APP_ROUTES.searchVehicles ? window.APP_ROUTES.searchVehicles : '/vehicles/search';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                console.error('Erro no polling de veículos, tentativa', attempt);
                if (attempt < maxAttempts) return pollCarSearch(payload, attempt + 1, container);
                if (container) container.innerHTML = '<div class="text-red-600">Não foi possível obter resultados de veículos.</div>';
                return;
            }

            const data = await res.json();
            if (data.status === 'concluido' && Array.isArray(data.veiculos)) {
                renderCarResults(data.veiculos, data.alerta || null);
                return;
            }

            if (data.status === 'carregando' && attempt < maxAttempts) {
                return pollCarSearch(payload, attempt + 1, container);
            }

            if (data.status === 'failed') {
                if (container) container.innerHTML = '<div class="text-red-600">A busca por veículos falhou.</div>';
                return;
            }

            // fallback
            if (attempt < maxAttempts) return pollCarSearch(payload, attempt + 1, container);
            if (container) container.innerHTML = '<div class="text-yellow-600">Nenhum veículo encontrado no momento.</div>';

        } catch (e) {
            console.error('Erro no pollCarSearch:', e);
            if (attempt < maxAttempts) return pollCarSearch(payload, attempt + 1, container);
        }
    }

    // Renderiza os veículos no container
    function renderCarResults(veiculos, alerta = null) {
        const container = document.getElementById('cars-container');
        if (!container) return;
        if (!Array.isArray(veiculos) || veiculos.length === 0) {
            container.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center"><div class="text-yellow-800 font-semibold mb-2">Nenhum veículo encontrado</div><div class="text-yellow-600 text-sm">Tente alterar horários ou local de retirada.</div></div>';
            return;
        }

        let html = '';

        // Header com contagem
        html += `<div class="mb-4 text-xl font-semibold text-gray-800">${veiculos.length} veículo${veiculos.length > 1 ? 's' : ''} encontrados</div>`;

        veiculos.forEach((v, idx) => {
            const nome = v.nome || v.nome_veiculo || 'Veículo';
            const subtitulo = v.subtitle || v.tipo || (v.locadora && v.locadora.nome) || '';
            const categoria = v.categoria || '';
            const imagem = v.imagem || v.imagem_url || '/imgs/default-car.png';
            const preco = (v.preco && v.preco.total) ? Number(v.preco.total).toFixed(2) : (v.preco_total ? Number(v.preco_total).toFixed(2) : null);
            const precoHtml = preco ? `R$ ${preco.replace('.',',')}` : 'Preço não disponível';
            const protecoes = v.protecoes || v.protections || v.included_protections || [];
            const localRetirada = (v.local_retirada && (v.local_retirada.endereco || v.local_retirada.nome)) ? (v.local_retirada.endereco || v.local_retirada.nome) : (v.nome_local || v.endereco_retirada || 'Local não informado');
            const locadoraLogo = (v.locadora && v.locadora.logo) ? v.locadora.logo : (v.locadora_logo || '');
            const linkReserva = v.link_continuar || v.link_reserva || '#';

            html += `
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6 car-result-card" data-index="${idx}">
                <div class="bg-blue-600 text-white px-6 py-3 font-semibold">${protecoes && protecoes.length ? protecoes[0] : 'Proteção a terceiros incluida'}</div>
                <div class="p-6 flex gap-6">
                    <div class="w-40 flex-shrink-0">
                        <img src="${imagem}" alt="${nome}" class="w-full h-28 object-contain">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-2xl font-bold text-gray-800">${nome}</div>
                                <div class="text-sm text-gray-500">${subtitulo}</div>
                                <div class="flex gap-3 text-sm text-gray-600 mt-3">
                                    <div><i class="fas fa-users"></i> ${(v.configuracoes && v.configuracoes.passageiros) ? v.configuracoes.passageiros : (v.passageiros || '—')} passageiros</div>
                                    <div>• ${(v.configuracoes && v.configuracoes.malas) ? v.configuracoes.malas + ' malas' : (v.malas ? v.malas + ' malas' : '—')}</div>
                                    <div>• ${v.configuracoes && v.configuracoes.ar_condicionado ? 'Ar condicionado' : (v.ar_condicionado ? 'Ar condicionado' : '—')}</div>
                                    <div>• ${v.configuracoes && v.configuracoes.cambio ? v.configuracoes.cambio : (v.cambio || '—')}</div>
                                </div>

                                ${protecoes && protecoes.length ? `<div class="mt-4 text-sm text-gray-700"><b>Proteções incluídas:</b><ul class="ml-4 list-disc">${protecoes.map(p => `<li class="text-sm text-gray-600">${p}</li>`).join('')}</ul></div>` : ''}

                                <div class="mt-4 text-sm text-gray-700"><b>Local de retirada:</b><div class="text-gray-600">${localRetirada}</div></div>
                            </div>

                            <div class="w-48 text-right">
                                <div class="text-sm text-gray-500">Total</div>
                                <div class="text-3xl font-bold text-blue-600">${precoHtml}</div>
                                <div class="mt-6 flex justify-end gap-3">
                                    <a href="${linkReserva}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded shadow" rel="noopener noreferrer">Ir para Site</a>
                                </div>
                                ${locadoraLogo ? `<div class="mt-3"><img src="${locadoraLogo}" alt="locadora" class="h-6 inline-block"></div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });

        container.innerHTML = html;

        // Tornar todo o card clicável: clicar no card seleciona-o (não submete o formulário)
        document.querySelectorAll('.car-result-card').forEach(card => {
            // prevenir que cliques em links internos disparem a seleção
            card.querySelectorAll('a').forEach(a => a.addEventListener('click', function(e){ e.stopPropagation(); }));

            card.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Se o clique veio de um link interno, ignore
                const anchor = e.target.closest('a');
                if (anchor) return;

                const idx = parseInt(this.getAttribute('data-index'));
                const veiculo = veiculos[idx];

                // Marcar seleção localmente (não salvar no servidor ainda)
                const selIdx = document.getElementById('selected_car_index');
                const selData = document.getElementById('selected_car_data');
                if (selIdx) selIdx.value = idx;
                if (selData) selData.value = JSON.stringify(veiculo);

                // Visual feedback de seleção no cliente
                document.querySelectorAll('.car-result-card').forEach(c => c.classList.remove('border-4', 'border-green-600'));
                this.classList.add('border-4', 'border-green-600');

                showNotification('Veículo selecionado localmente. O salvamento será feito ao finalizar o formulário.', 'success');
            });
        });

        // Se houver alerta (local alternativo), mostrar aviso
        if (alerta) {
            const alertaHtml = `<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4 text-sm"><b>Local alternativo sugerido:</b> ${alerta.local_alternativo} (distância ${alerta.distancia} km)</div>`;
            container.insertAdjacentHTML('beforeend', alertaHtml);
        }
    }

    // Salva veículo selecionado via AJAX
    // Expor a função globalmente para que listeners fora do IIFE possam chamá-la
    window.saveSelectedCar = async function(index, veiculoData) {
        try {
            // Proteção: evitar salvar repetidamente para a mesma viagem
            const viagemIdCheck = window.VIAGEM_DATA && window.VIAGEM_DATA.pk_id_viagem ? window.VIAGEM_DATA.pk_id_viagem : (document.querySelector('input[name="pk_id_viagem"]') ? document.querySelector('input[name="pk_id_viagem"]').value : null);
            if (window.vehicleSavedForViagemId && viagemIdCheck && String(window.vehicleSavedForViagemId) === String(viagemIdCheck)) {
                console.log('saveSelectedCar: veículo já salvo anteriormente para esta viagem (ignorado)');
                return true;
            }
            const url = window.APP_ROUTES && window.APP_ROUTES.saveVehicle ? window.APP_ROUTES.saveVehicle : '/vehicles/save';
            const viagemId = window.VIAGEM_DATA && window.VIAGEM_DATA.pk_id_viagem ? window.VIAGEM_DATA.pk_id_viagem : (document.querySelector('input[name="pk_id_viagem"]') ? document.querySelector('input[name="pk_id_viagem"]').value : null);
            if (!viagemId) {
                showNotification('Viagem não encontrada para salvar o veículo.', 'error');
                return false;
            }

            const payload = {
                fk_id_viagem: viagemId,
                veiculo_data: veiculoData
            };

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(payload)
            });

            const data = await res.json().catch(() => ({ success: false }));
            if (res.ok && data.success) {
                // Atualizar hidden inputs
                const selIdx = document.getElementById('selected_car_index');
                const selData = document.getElementById('selected_car_data');
                if (selIdx) selIdx.value = index;
                if (selData) selData.value = JSON.stringify(veiculoData);

                showNotification('Veículo salvo com sucesso!', 'success');

                // Marcar visualmente o selecionado
                document.querySelectorAll('.car-result-card').forEach(card => card.classList.remove('border-4', 'border-green-600'));
                const selectedCard = document.querySelector(`.car-result-card[data-index="${index}"]`);
                if (selectedCard) selectedCard.classList.add('border-4', 'border-green-600');
                // Marcar como salvo via AJAX para evitar reenvios
                window.vehicleSavedViaAjax = true;
                window.vehicleSavedForViagemId = viagemId;

                // Para evitar duplicação do lado do servidor quando o formulário também submeter
                // o campo selected_car_data, limpamos o hidden correspondente pois já persistimos via AJAX
                try {
                    const selData = document.getElementById('selected_car_data');
                    if (selData) selData.value = '';
                } catch (e) {
                    // ignore
                }
                return true;
            } else {
                console.error('Erro ao salvar veículo:', data);
                showNotification('Erro ao salvar veículo.', 'error');
                return false;
            }
        } catch (e) {
            console.error('Erro em saveSelectedCar:', e);
            showNotification('Erro ao salvar veículo.', 'error');
            return false;
        }
    }

})();

// -------------------- Cálculo de Rota para Carro Próprio --------------------
(function() {
    'use strict';

    // Dados da viagem para cálculo
    let dadosViagem = {
        origem_place_id: null,
        destinos_place_ids: [],
        autonomia: null,
        tipo_combustivel: 'gasolina',
        preco_combustivel: null,
        meio_locomocao: null
    };

    // Função para coletar dados do formulário
    function coletarDadosViagem() {
        // Meio de locomoção
        const meioLocomocao = document.getElementById('meio_locomocao');
        if (meioLocomocao) {
            dadosViagem.meio_locomocao = meioLocomocao.value;
        }

        // Origem
        const origemInput = document.getElementById('origem');
        if (origemInput) {
            dadosViagem.origem_place_id = origemInput.getAttribute('data-place-id');
        }

        // Destinos
        const destinoInputs = document.querySelectorAll('.destino-input');
        dadosViagem.destinos_place_ids = [];
        destinoInputs.forEach(input => {
            const placeId = input.getAttribute('data-place-id');
            if (placeId && input.value.trim()) {
                dadosViagem.destinos_place_ids.push(placeId);
            }
        });

        // Autonomia
        const autonomiaInput = document.getElementById('autonomia_veiculo');
        if (autonomiaInput && autonomiaInput.value) {
            dadosViagem.autonomia = parseFloat(autonomiaInput.value);
        }

        // Tipo de combustível
        const tipoCombustivelSelect = document.getElementById('tipo_combustivel');
        if (tipoCombustivelSelect && tipoCombustivelSelect.value) {
            dadosViagem.tipo_combustivel = tipoCombustivelSelect.value;
        }

        // Preço do combustível
        const precoCombustivelInput = document.getElementById('preco_combustivel');
        if (precoCombustivelInput && precoCombustivelInput.value) {
            dadosViagem.preco_combustivel = parseFloat(precoCombustivelInput.value);
        } else {
            // Usar preço médio
            const precosMedios = {
                'gasolina': 5.89,
                'etanol': 4.29,
                'diesel': 5.99,
                'gnv': 4.50
            };
            dadosViagem.preco_combustivel = precosMedios[dadosViagem.tipo_combustivel] || 5.89;
        }

        return dadosViagem;
    }

    // Função para calcular rota usando endpoint backend (evita CORS)
    async function calcularRota() {
        try {
            const dados = coletarDadosViagem();

            // Verificar se tem todos os dados necessários
            if (!dados.origem_place_id || dados.destinos_place_ids.length === 0) {
                console.warn('Dados incompletos para calcular rota');
                return null;
            }

            if (!dados.autonomia || dados.autonomia <= 0) {
                console.warn('Autonomia não definida');
                return null;
            }

            // Fazer requisição para o backend
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            const response = await fetch('/calcular-rota', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    origem_place_id: dados.origem_place_id,
                    destinos_place_ids: dados.destinos_place_ids
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error('Erro ao calcular rota:', errorData);
                
                // Mostrar mensagem amigável para o usuário
                if (errorData.error && (errorData.error.includes('Routes API') || errorData.error.includes('PERMISSION_DENIED'))) {
                    alert('⚠️ ATENÇÃO: A Google Routes API não está habilitada!\n\n' +
                          'Para habilitar:\n' +
                          '1. Acesse: https://console.cloud.google.com/apis/library/routes-backend.googleapis.com\n' +
                          '2. Clique em "Ativar"\n' +
                          '3. Aguarde 2-3 minutos\n' +
                          '4. Tente novamente\n\n' +
                          'Detalhes: ' + errorData.error);
                }
                
                throw new Error(errorData.error || 'Erro ao calcular rota');
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erro ao calcular rota');
            }

            const distanciaKm = data.distancia_km;

            // Usar pedágio da API se disponível, senão estimar
            let pedagioEstimado = 0;
            let pedagioObservacao = '';
            
            if (data.pedagio) {
                pedagioEstimado = data.pedagio.valor_estimado || 0;
                
                if (data.pedagio.tem_pedagio) {
                    pedagioObservacao = 'Valor oficial da Routes API';
                } else {
                    pedagioObservacao = data.pedagio.observacao || 'Valor estimado';
                }
            } else {
                // Fallback: estimar R$ 0,10 por km
                pedagioEstimado = distanciaKm * 0.10;
                pedagioObservacao = 'Valor estimado (R$ 0,10/km)';
            }

            // Calcular combustível
            const litrosNecessarios = distanciaKm / dados.autonomia;
            const custoCombustivel = litrosNecessarios * dados.preco_combustivel;

            return {
                success: true,
                distancia_km: Math.round(distanciaKm * 100) / 100,
                distancia_metros: data.distancia_metros,
                duracao_segundos: data.duracao_segundos,
                duracao_texto: `${Math.floor(data.duracao_segundos / 3600)}h ${Math.floor((data.duracao_segundos % 3600) / 60)}min`,
                pedagio_estimado: Math.round(pedagioEstimado * 100) / 100,
                pedagio_observacao: pedagioObservacao,
                pedagio_oficial: data.pedagio?.tem_pedagio || false,
                litros_necessarios: Math.round(litrosNecessarios * 100) / 100,
                custo_combustivel: Math.round(custoCombustivel * 100) / 100,
                custo_total: Math.round((custoCombustivel + pedagioEstimado) * 100) / 100,
                preco_combustivel_litro: dados.preco_combustivel,
                polyline: data.polyline || '',
                legs: data.legs || []
            };

        } catch (error) {
            console.error('Erro ao calcular rota:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Função para atualizar a interface do step7 com os dados calculados
    function atualizarStep7ComCalculos(resultado) {
        const carroProprioCalculos = document.getElementById('carroProprioCalculos');
        
        if (!carroProprioCalculos) {
            return;
        }

        if (!resultado || !resultado.success) {
            carroProprioCalculos.classList.add('hidden');
            return;
        }

        // Mostrar a seção
        carroProprioCalculos.classList.remove('hidden');

        // Atualizar displays
        const distanciaDisplay = document.getElementById('distancia_total_display');
        const duracaoDisplay = document.getElementById('duracao_display');
        const combustivelDisplay = document.getElementById('combustivel_litros_display');
        const custoCombustivelDisplay = document.getElementById('custo_combustivel_display');
        const pedagioDisplay = document.getElementById('pedagio_display');
        const custoTotalDisplay = document.getElementById('custo_total_display');

        if (distanciaDisplay) {
            distanciaDisplay.innerHTML = `${resultado.distancia_km.toLocaleString('pt-BR')} km`;
        }

        if (duracaoDisplay) {
            duracaoDisplay.innerHTML = `Tempo estimado: ${resultado.duracao_texto}`;
        }

        if (combustivelDisplay) {
            combustivelDisplay.innerHTML = `${resultado.litros_necessarios.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} L`;
        }

        if (custoCombustivelDisplay) {
            custoCombustivelDisplay.innerHTML = `R$ ${resultado.custo_combustivel.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        if (pedagioDisplay) {
            let pedagioHtml = `R$ ${resultado.pedagio_estimado.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            // Adicionar badge indicando se é oficial ou estimado
            if (resultado.pedagio_oficial) {
                pedagioHtml += ' <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded ml-2">✓ Oficial</span>';
            } else {
                pedagioHtml += ' <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded ml-2">≈ Estimado</span>';
            }
            
            pedagioDisplay.innerHTML = pedagioHtml;
        }

        if (custoTotalDisplay) {
            custoTotalDisplay.innerHTML = `R$ ${resultado.custo_total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        // Renderizar mapa com a rota
        renderizarMapaRota(resultado);

        // Atualizar campos hidden
        const hiddenFields = {
            'distancia_total_km': resultado.distancia_km,
            'combustivel_litros': resultado.litros_necessarios,
            'custo_combustivel': resultado.custo_combustivel,
            'pedagio_estimado': resultado.pedagio_estimado,
            'pedagio_oficial': resultado.pedagio_oficial ? 1 : 0,
            'duracao_segundos': resultado.duracao_segundos,
            'rota_detalhada': JSON.stringify({
                polyline: resultado.polyline,
                legs: resultado.legs,
                duracao: resultado.duracao_segundos
            })
        };

        for (const [fieldId, value] of Object.entries(hiddenFields)) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = value;
            }
        }
    }

    // Variável global para armazenar a instância do mapa
    let routeMapInstance = null;
    let directionsRenderer = null;

    // Função para renderizar o mapa com a rota
    function renderizarMapaRota(resultado) {
        const mapContainer = document.getElementById('routeMap');
        
        if (!mapContainer) {
            console.warn('Container do mapa não encontrado');
            return;
        }

        // Verificar se o Google Maps está disponível
        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API não está carregada');
            return;
        }

        try {
            const dados = coletarDadosViagem();
            
            // Criar mapa se não existir
            if (!routeMapInstance) {
                routeMapInstance = new google.maps.Map(mapContainer, {
                    zoom: 7,
                    center: { lat: -23.5505, lng: -46.6333 }, // São Paulo como centro inicial
                    mapTypeControl: true,
                    streetViewControl: false,
                    fullscreenControl: true
                });
            }

            // Criar DirectionsRenderer se não existir
            if (!directionsRenderer) {
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map: routeMapInstance,
                    suppressMarkers: false,
                    polylineOptions: {
                        strokeColor: '#4F46E5',
                        strokeWeight: 5,
                        strokeOpacity: 0.8
                    }
                });
            }

            // Criar serviço de direções
            const directionsService = new google.maps.DirectionsService();

            // Montar waypoints (destinos intermediários)
            const waypoints = dados.destinos_place_ids.slice(0, -1).map(placeId => ({
                location: { placeId: placeId },
                stopover: true
            }));

            // Última posição é o destino final
            const destinoFinal = dados.destinos_place_ids[dados.destinos_place_ids.length - 1];

            // Fazer requisição de direções
            const request = {
                origin: { placeId: dados.origem_place_id },
                destination: { placeId: destinoFinal },
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING,
                optimizeWaypoints: false
            };

            directionsService.route(request, function(result, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    
                    // Adicionar marcadores customizados
                    const route = result.routes[0];
                    
                    // Marcador de início (origem)
                    new google.maps.Marker({
                        position: route.legs[0].start_location,
                        map: routeMapInstance,
                        label: {
                            text: 'A',
                            color: 'white',
                            fontWeight: 'bold'
                        },
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        },
                        title: 'Origem: ' + route.legs[0].start_address
                    });

                    // Marcadores dos waypoints
                    for (let i = 0; i < route.legs.length - 1; i++) {
                        new google.maps.Marker({
                            position: route.legs[i].end_location,
                            map: routeMapInstance,
                            label: {
                                text: String.fromCharCode(66 + i), // B, C, D...
                                color: 'white',
                                fontWeight: 'bold'
                            },
                            icon: {
                                url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                            },
                            title: 'Parada ' + (i + 1) + ': ' + route.legs[i].end_address
                        });
                    }

                    // Marcador de fim (destino final)
                    const lastLeg = route.legs[route.legs.length - 1];
                    new google.maps.Marker({
                        position: lastLeg.end_location,
                        map: routeMapInstance,
                        label: {
                            text: String.fromCharCode(66 + route.legs.length - 1),
                            color: 'white',
                            fontWeight: 'bold'
                        },
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        },
                        title: 'Destino: ' + lastLeg.end_address
                    });

                    console.log('Mapa da rota renderizado com sucesso');
                } else {
                    console.error('Erro ao renderizar rota no mapa:', status);
                }
            });

        } catch (error) {
            console.error('Erro ao criar mapa:', error);
        }
    }

    // Listener para quando entrar no step7
    function setupStep7Listener() {
        const form = document.getElementById('multiStepForm');
        if (!form) return;

        // Observer para detectar quando step7 fica visível
        const observer = new MutationObserver(async (mutations) => {
            for (const mutation of mutations) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    
                    // Verificar se é o step7 e se ficou ativo
                    if (target.classList.contains('form-step') && 
                        target.classList.contains('active') &&
                        target.querySelector('#carroProprioCalculos')) {
                        
                        // Verificar se é carro próprio
                        const dados = coletarDadosViagem();
                        if (dados.meio_locomocao === 'carro_proprio') {
                            // Calcular rota
                            const resultado = await calcularRota();
                            if (resultado) {
                                atualizarStep7ComCalculos(resultado);
                            }
                        }
                    }
                }
            }
        });

        // Observar todas as form-steps
        document.querySelectorAll('.form-step').forEach(step => {
            observer.observe(step, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    }

    // Inicializar quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        setupStep7Listener();
    });

})();
