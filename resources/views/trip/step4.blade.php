<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Seguro Viagem</h2>

    <div id="loading-seguros" class="flex flex-col items-center justify-center py-12" style="display: none;">
        <style>
            /* ===== Loader ===== */
            /* Loader base */

            .loading-container {
                position: relative;
                width: 220px;
                height: 220px;
                margin: 0 auto;
            }

            .circle {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 200px;
                height: 200px;
                background: #0088FF;
                border-radius: 50%;
                transform: translate(-50%, -50%);
                box-shadow: 0 0 40px 0 #0088FF33;
            }

            .night-mode .circle {
                background: #2d4a7a;
                box-shadow: 0 0 40px 0 #2d4a7a55;
            }

            .cloud {
                position: absolute;
                background: url('/imgs/loading/clouds.png') no-repeat center/cover;
                opacity: 0.9;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
            }

            .night-mode .cloud {
                filter: brightness(0.7) drop-shadow(0 2px 8px #222);
                opacity: 0.7;
            }

            /* Nuvens acima e abaixo do avião/círculo */
            .cloud1 {
                width: 60px;
                height: 60px;
                top: 35px;
                left: 100%;
                animation: cloudMove 7s linear infinite;
            }

            .cloud2 {
                width: 50px;
                height: 50px;
                top: 60px;
                left: 100%;
                animation: cloudMove 9s linear infinite;
                animation-delay: 2s;
            }

            .cloud3 {
                width: 45px;
                height: 45px;
                top: 120px;
                left: 100%;
                animation: cloudMove 8s linear infinite;
                animation-delay: 4s;
            }

            .cloud4 {
                width: 55px;
                height: 55px;
                top: 170px;
                left: 100%;
                animation: cloudMove 10s linear infinite;
                animation-delay: 1s;
            }

            .cloud5 {
                width: 40px;
                height: 40px;
                top: 80px;
                left: 100%;
                animation: cloudMove 6s linear infinite;
                animation-delay: 3s;
            }

            .cloud6 {
                width: 50px;
                height: 50px;
                top: 150px;
                left: 100%;
                animation: cloudMove 11s linear infinite;
                animation-delay: 5s;
            }

            @keyframes cloudMove {
                0% {
                    left: 100%;
                }

                100% {
                    left: -60px;
                }
            }

            .airplane {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 120px;
                height: 120px;
                background: url('/imgs/loading/plane.png') no-repeat center/cover;
                transform: translate(-50%, -50%) rotate(10deg);
                animation: fly 4s ease-in-out infinite;
                z-index: 10;
            }

            @keyframes fly {
                0% {
                    transform: translate(-50%, -50%) rotate(10deg) translateY(0);
                }

                50% {
                    transform: translate(-50%, -50%) rotate(12deg) translateY(-8px);
                }

                100% {
                    transform: translate(-50%, -50%) rotate(10deg) translateY(0);
                }
            }

        </style>
        <div class="loading-container">
            <div class="circle"></div>
            <div class="cloud cloud1"></div>
            <div class="cloud cloud2"></div>
            <div class="cloud cloud3"></div>
            <div class="cloud cloud4"></div>
            <div class="cloud cloud5"></div>
            <div class="cloud cloud6"></div>
            <div class="airplane"></div>
        </div>

        <div class="text-blue-700 font-semibold text-lg mb-2" id="loading-message">Buscando os melhores seguros...</div>
        <div class="text-gray-600 text-sm text-center max-w-md mb-4" id="loading-description">
            Estamos consultando múltiplas seguradoras para encontrar as melhores opções para sua viagem.
            Isso pode levar alguns segundos.
        </div>
        <div class="mt-4 w-full max-w-md">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                    style="width: 0%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-1 text-center" id="progress-text">Iniciando busca...</div>
        </div>
    </div>

    <!-- Container das tabs e seguros - aparece após o loading -->
    <div id="tabs-seguros-container" class="mb-8" style="display: none;">
        <!-- Navegação das tabs -->
        <div id="tabs-navigation" class="flex border-b border-gray-200 mb-6 overflow-x-auto">
            <!-- Tabs serão criadas dinamicamente aqui -->
        </div>
        
        <!-- Conteúdo das tabs -->
        <div id="tabs-content">
            <!-- Conteúdo das tabs será criado dinamicamente aqui -->
        </div>
    </div>

    <div id="seguros-container" class="mb-8"></div>

    <div class="flex justify-between mt-8">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>

{{-- ESTILOS DOS CARDS DE SEGURO --}}
<style>
    /* Define uma variável de cor para fácil customização */
    :root {
        --primary-color: #2563eb;
        /* Azul padrão do Tailwind */
    }

    .seguro-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        /* Borda cinza clara */
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }

    .seguro-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .seguro-card.selected {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        /* Sombra de anel azul */
    }

    /* Estilo para o link "Ver detalhes" */
    .details-link {
        font-weight: 500;
        color: #4b5563;
        /* Cinza escuro */
        transition: color 0.2s;
    }

    .seguro-card:hover .details-link {
        color: var(--primary-color);
    }

    /* Estilos das tabs */
    .tab-button {
        padding: 12px 24px;
        border: none;
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        font-size: 14px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
        min-width: 120px;
    }

    .tab-button:hover {
        color: var(--primary-color);
        background-color: #f8fafc;
    }

    .tab-button.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        background-color: #f0f9ff;
        font-weight: 600;
    }

    .tab-content {
        display: none;
        animation: fadeInTab 0.3s ease-in-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeInTab {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsividade das tabs */
    @media (max-width: 768px) {
        .tab-button {
            padding: 10px 16px;
            font-size: 13px;
            min-width: 100px;
        }
    }
</style>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.insuranceScriptLoaded) return;
        window.insuranceScriptLoaded = true;

        // ... (todo o código de loading, polling, etc., permanece o mesmo) ...
        let attemptsMade = 0;
        const maxAttempts = 15;
        let intervalId = null;
        let startTime = null;

        function showLoading() {
            document.getElementById('loading-seguros').style.display = 'flex';
            document.getElementById('seguros-container').style.display = 'none';
            startTime = Date.now();
            updateProgress(0);
        }

        function hideLoading() {
            document.getElementById('loading-seguros').style.display = 'none';
            document.getElementById('tabs-seguros-container').style.display = 'block';
            document.getElementById('seguros-container').style.display = 'none';
        }

        function updateProgress(attempt) {
            const progressBar = document.getElementById('progress-bar');
            if (!progressBar) return;

            const progress = Math.min((attempt / maxAttempts) * 100, 95);
            progressBar.style.width = progress + '%';

            const elapsed = startTime ? Math.floor((Date.now() - startTime) / 1000) : 0;
            const messages = {
                progress: `Processando... (${elapsed}s)`,
                loading: 'Coletando as ofertas disponíveis...',
                description: 'Acessando os principais portais de seguradoras do mercado.'
            };
            document.getElementById('progress-text').textContent = messages.progress;
            document.getElementById('loading-message').textContent = messages.loading;
            document.getElementById('loading-description').textContent = messages.description;
        }

    function getFormData() {
        // Buscar todas as datas dos destinos
        const dataInicioInputs = document.querySelectorAll('input[name="destino_data_inicio[]"]');
        const dataFimInputs = document.querySelectorAll('input[name="destino_data_fim[]"]');
        const destinoSelect = document.getElementById('MainContent_Cotador_selContinente');

        if (!destinoSelect) {
            console.error('Campo de destino não encontrado no formulário.');
            return { error: 'Campo de destino ausente.' };
        }
        
        // Para o seguro, usar a data do primeiro destino como data de início e a data do último destino como data de fim
        let dataIda = '';
        let dataVolta = '';
        
        if (dataInicioInputs.length > 0 && dataInicioInputs[0].value) {
            dataIda = dataInicioInputs[0].value; // Data de início do primeiro destino
        }
        
        if (dataFimInputs.length > 0) {
            // Pegar a data de fim do último destino que tenha valor
            for (let i = dataFimInputs.length - 1; i >= 0; i--) {
                if (dataFimInputs[i].value) {
                    dataVolta = dataFimInputs[i].value;
                    break;
                }
            }
        }

        if (!dataIda || !dataVolta) {
            console.error('Datas de início ou fim dos destinos não encontradas.');
            return { error: 'Datas dos destinos não encontradas.' };
        }
        
        const formData = {
            destino: destinoSelect.value,
            data_ida: dataIda,
            data_volta: dataVolta
        };

            return formData;
        }

        // Função para obter o número de viajantes e suas idades
        function getViajantesInfo() {
            const numPessoasSelect = document.getElementById('num_pessoas');
            const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
            
            const numPessoas = numPessoasSelect ? parseInt(numPessoasSelect.value) || 1 : 1;
            const idades = Array.from(idadeInputs).map(input => parseInt(input.value) || 0).filter(idade => idade > 0);
            
            // Se não há idades preenchidas, usar idades padrão baseado no número de pessoas
            if (idades.length === 0) {
                for (let i = 0; i < numPessoas; i++) {
                    idades.push(25); // Idade padrão
                }
            }
            
            return { numPessoas, idades };
        }

        // Função para criar as tabs dos viajantes
        function createTabs(viajantesInfo) {
            const tabsNavigation = document.getElementById('tabs-navigation');
            const tabsContent = document.getElementById('tabs-content');
            
            if (!tabsNavigation || !tabsContent) return;
            
            tabsNavigation.innerHTML = '';
            tabsContent.innerHTML = '';
            
            const { numPessoas, idades } = viajantesInfo;
            
            for (let i = 0; i < numPessoas; i++) {
                const idade = idades[i] || 25;
                const isActive = i === 0;
                
                // Criar botão da tab
                const tabButton = document.createElement('button');
                tabButton.type = 'button'; // Importante: evita submit do formulário
                tabButton.className = `tab-button ${isActive ? 'active' : ''}`;
                tabButton.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user text-sm"></i>
                        <span>Viajante ${i + 1}</span>
                        <span class="text-xs text-gray-500">(${idade} anos)</span>
                    </div>
                `;
                
                // Event listener que previne comportamento padrão
                tabButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.switchTab(i);
                    return false;
                });
                
                tabsNavigation.appendChild(tabButton);
                
                // Criar conteúdo da tab
                const tabContent = document.createElement('div');
                tabContent.className = `tab-content ${isActive ? 'active' : ''}`;
                tabContent.id = `tab-content-${i}`;
                tabContent.innerHTML = `
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label for="viajante-nome-${i}" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-500 mr-2"></i>
                            Nome do viajante (opcional)
                        </label>
                        <input 
                            type="text" 
                            id="viajante-nome-${i}" 
                            name="viajante_nome_${i}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Digite o nome do viajante..."
                            maxlength="100"
                            oninput="updateTabTitle(${i}, this.value)"
                        >
                        <p class="text-xs text-gray-500 mt-1">Deixe em branco para usar "Viajante ${i + 1}"</p>
                    </div>
                    <div class="flex flex-col gap-6"></div>
                `;
                tabsContent.appendChild(tabContent);
            }
        }

        // Função para alternar entre tabs
        window.switchTab = function(tabIndex) {
            // Atualizar botões das tabs
            document.querySelectorAll('.tab-button').forEach((btn, index) => {
                btn.classList.toggle('active', index === tabIndex);
            });
            
            // Atualizar conteúdo das tabs
            document.querySelectorAll('.tab-content').forEach((content, index) => {
                content.classList.toggle('active', index === tabIndex);
            });
            
            console.log(`Switched to tab ${tabIndex + 1}`);
            return false; // Previne qualquer navegação
        }

        function searchInsuranceAttempt() {
            if (attemptsMade === 0) showLoading();

            attemptsMade++;
            updateProgress(attemptsMade);

            const formData = getFormData();

            fetch('{{ route('run.Scraping.ajax') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(formData)
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                    return res.json();
                })
                .then(data => {
                    if (data.error) throw new Error(data.error + (data.message ? ': ' + data.message : ''));

                    if (data.frases && data.frases.length > 0) {
                        clearInterval(intervalId);
                        intervalId = null;
                        hideLoading();
                        renderInsurances(data.frases);
                    } else if (data.status === 'carregando' && attemptsMade < maxAttempts) {
                        // Polling continua
                    } else {
                        clearInterval(intervalId);
                        intervalId = null;
                        hideLoading();
                        showNoInsuranceMessage();
                    }
                })
                .catch((err) => {
                    clearInterval(intervalId);
                    intervalId = null;
                    hideLoading();
                    console.error('[Seguros] Erro na requisição:', err);
                    showErrorMessage(err.message);
                });
        }

        function showNoInsuranceMessage() {
            document.getElementById('tabs-seguros-container').style.display = 'none';
            document.getElementById('seguros-container').style.display = 'block';
            document.getElementById('seguros-container').innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <div class="text-yellow-800 font-semibold mb-2">Nenhum seguro encontrado no momento</div>
                <div class="text-yellow-700 text-sm mb-4">Não foi possível encontrar seguros para os critérios selecionados.</div>
                <button onclick="restartSearch()" class="btn-primary">Tentar Novamente</button>
            </div>
        `;
        }

        function showErrorMessage(errorMessage) {
            document.getElementById('tabs-seguros-container').style.display = 'none';
            document.getElementById('seguros-container').style.display = 'block';
            document.getElementById('seguros-container').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <div class="text-red-800 font-semibold mb-2">Erro ao buscar seguros</div>
                <div class="text-red-700 text-sm mb-4">Ocorreu um erro técnico: <br><code>${errorMessage}</code></div>
                <button onclick="restartSearch()" class="btn-primary">Tentar Novamente</button>
            </div>
        `;
        }

        function renderInsurances(insurances) {
            window.currentInsurances = insurances;
            
            // Obter informações dos viajantes
            const viajantesInfo = getViajantesInfo();
            
            // Criar as tabs
            createTabs(viajantesInfo);
            
            // Distribuir seguros para cada tab (cada viajante terá todos os seguros)
            const { numPessoas } = viajantesInfo;
            
            for (let viaganteIndex = 0; viaganteIndex < numPessoas; viaganteIndex++) {
                const tabContentContainer = document.querySelector(`#tab-content-${viaganteIndex} .flex.flex-col.gap-6`);
                if (!tabContentContainer) continue;
                
                let html = '';
                
                insurances.forEach((insurance, index) => {
                    // Acessa os dados pelas chaves, de forma segura e independente da ordem
                    const seguradora = insurance.seguradora || 'Seguradora N/A';
                    const plano = insurance.plano || 'Plano N/A';
                    const coberturaMedica = insurance.coberturas?.medica || 'N/A';
                    const coberturaBagagem = insurance.coberturas?.bagagem || 'N/A';
                    const precoPix = insurance.precos?.pix || 'N/A';
                    const precoCartao = insurance.precos?.cartao || 'N/A';
                    const parcelas = insurance.precos?.parcelas || '';
                    const detalhesEtarios = insurance.detalhes_etarios || '';
                    const link = insurance.link || '#';

                    // Estrutura HTML final com ícones e dados nos lugares corretos
                    html += `
                    <div class="seguro-card" data-insurance-index="${index}" data-viajante-index="${viaganteIndex}" onclick="selectInsurance(this, ${index}, ${viaganteIndex})">
                        <div class="p-5 border-b border-gray-200">
                            <p class="text-sm text-gray-500">${seguradora}</p>
                            <h3 class="font-bold text-gray-800 text-lg">${plano}</h3>
                            ${detalhesEtarios ? `<p class="text-xs text-gray-400 mt-1">${detalhesEtarios}</p>` : ''}
                        </div>

                        <div class="p-5 flex-grow grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-start">
                                <i class="fa-solid fa-user-doctor text-blue-500 mr-3 mt-1 text-xl w-6 text-center"></i>
                                <div>
                                    <p class="text-sm text-gray-700 font-semibold">Despesa Médica</p>
                                    <p class="text-sm text-gray-500">${coberturaMedica}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fa-solid fa-suitcase-rolling text-blue-500 mr-3 mt-1 text-xl w-6 text-center"></i>
                                <div>
                                    <p class="text-sm text-gray-700 font-semibold">Seguro Bagagem</p>
                                    <p class="text-sm text-gray-500">${coberturaBagagem}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 bg-gray-50 rounded-b-lg mt-auto">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <p class="text-xs text-gray-500">No cartão</p>
                                    <p class="text-lg font-bold text-gray-800">${precoCartao}</p>
                                    <p class="text-xs text-gray-500">${parcelas}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-green-600 font-semibold">No PIX</p>
                                    <p class="text-2xl font-extrabold text-green-600">${precoPix}</p>
                                </div>
                            </div>
                            <a href="${link}" target="_blank" class="details-link text-sm w-full text-center block" onclick="event.stopPropagation()">
                                Ver detalhes completos
                            </a>
                        </div>
                    </div>
                `;
                });
                
                tabContentContainer.innerHTML = html;
            }
        }

        window.restartSearch = function () {
            attemptsMade = 0;
            if (intervalId) clearInterval(intervalId);

            searchInsuranceAttempt();
            intervalId = setInterval(() => {
                if (attemptsMade < maxAttempts) searchInsuranceAttempt();
                else clearInterval(intervalId);
            }, 4000);
        };

        // Função para obter o nome do viajante (com fallback)
        function getViajanteNome(viaganteIndex) {
            const nomeInput = document.getElementById(`viajante-nome-${viaganteIndex}`);
            const nomePersonalizado = nomeInput ? nomeInput.value.trim() : '';
            return nomePersonalizado || `Viajante ${viaganteIndex + 1}`;
        }

        // Função para atualizar o título da tab quando o nome é digitado
        window.updateTabTitle = function(viaganteIndex, nomeValue) {
            const tabButton = document.querySelectorAll('.tab-button')[viaganteIndex];
            if (tabButton) {
                const nomeExibir = nomeValue.trim() || `Viajante ${viaganteIndex + 1}`;
                const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
                const idade = idadeInputs[viaganteIndex] ? parseInt(idadeInputs[viaganteIndex].value) || 25 : 25;
                
                tabButton.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user text-sm"></i>
                        <span>${nomeExibir}</span>
                        <span class="text-xs text-gray-500">(${idade} anos)</span>
                    </div>
                `;
            }
        }

        window.selectInsurance = function (cardElement, index, viaganteIndex) {
            // 1. Atualiza a interface (UI) - remove seleção de todos os cards da tab atual
            const currentTabContent = document.querySelector(`#tab-content-${viaganteIndex}`);
            if (currentTabContent) {
                currentTabContent.querySelectorAll('.seguro-card').forEach(card => card.classList.remove('selected'));
            }
            cardElement.classList.add('selected');

            // 2. Pega os dados do seguro selecionado do array global
            const insuranceData = window.currentInsurances[index];

            if (!insuranceData) {
                console.error('Dados do seguro não encontrados para o índice:', index);
                return;
            }

            // 3. Obtém o nome do viajante
            const nomeViajante = getViajanteNome(viaganteIndex);

            // 4. Armazena a seleção por viajante (incluindo nome)
            if (!window.selectedInsurancesByViajante) {
                window.selectedInsurancesByViajante = {};
            }
            window.selectedInsurancesByViajante[viaganteIndex] = {
                insuranceData: insuranceData,
                insuranceIndex: index,
                nomeViajante: nomeViajante
            };

            const fullInsuranceName = `${insuranceData.seguradora} - ${insuranceData.plano}`;
            sessionStorage.setItem(`selectedSeguroName_viajante_${viaganteIndex}`, fullInsuranceName);
            sessionStorage.setItem(`nomeViajante_${viaganteIndex}`, nomeViajante);

            // 5. Para compatibilidade, armazenar também no formato antigo (primeiro viajante)
            if (viaganteIndex === 0) {
                const hiddenInput = document.getElementById('seguroSelecionadoData');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(insuranceData);
                } else {
                    console.error('O campo hidden #seguroSelecionadoData não foi encontrado no formulário.');
                }
                sessionStorage.setItem('selectedSeguroName', fullInsuranceName);
            }

            console.log(`Seguro selecionado para ${nomeViajante}:`, fullInsuranceName);
        };

        const step4Observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                // Verificar se já existem seguros carregados (tanto no formato antigo quanto no novo)
                const hasOldFormat = document.querySelector('.seguro-card');
                const hasNewFormat = document.querySelector('#tabs-seguros-container .seguro-card');
                
                if (!hasOldFormat && !hasNewFormat) {
                    restartSearch();
                }
            }
        }, { threshold: 0.1 });

        const step4Element = document.querySelector('#seguros-container').closest('.form-step') || 
                            document.querySelector('#tabs-seguros-container').closest('.form-step');
        if (step4Element) {
            step4Observer.observe(step4Element);
        }
    });
</script>