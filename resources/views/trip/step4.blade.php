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

        // NOVO: Variável global para rastrear quantos seguros estão sendo exibidos por viajante
        // Armazenará: { viaganteIndex: currentVisibleCount }
        window.visibleInsuranceCount = {};
        const INSURANCES_PER_PAGE = 6; // Constante para o número de seguros a serem exibidos

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
            try {
                // Buscar todas as datas dos destinos
                const dataInicioInputs = document.querySelectorAll('input[name="destino_data_inicio[]"]');
                const dataFimInputs = document.querySelectorAll('input[name="destino_data_fim[]"]');
                const destinoSelect = document.getElementById('MainContent_Cotador_selContinente');
                const idadesInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');

                if (!destinoSelect) {
                    console.error('Campo de destino não encontrado no formulário.');
                    return null;
                }

                // Coletar idades dos inputs
                const idades = Array.from(idadesInputs).map(input => {
                    const valor = parseInt(input.value);
                    return isNaN(valor) ? 25 : valor; // Fallback para 25 anos se inválido
                });

                // Se não houver idades, usar array com idade padrão
                if (idades.length === 0) {
                    idades.push(25);
                }

                // Para o seguro, usar a data do primeiro destino como data de início 
                // e a data do último destino como data de fim
                let dataIda = '';
                let dataVolta = '';

                if (dataInicioInputs.length > 0 && dataInicioInputs[0].value) {
                    dataIda = dataInicioInputs[0].value;
                }

                if (dataFimInputs.length > 0) {
                    for (let i = dataFimInputs.length - 1; i >= 0; i--) {
                        if (dataFimInputs[i].value) {
                            dataVolta = dataFimInputs[i].value;
                            break;
                        }
                    }
                }

                if (!dataIda || !dataVolta) {
                    console.error('Datas de início ou fim dos destinos não encontradas.');
                    return null;
                }

                return {
                    destino: destinoSelect.value,
                    data_ida: dataIda,
                    data_volta: dataVolta,
                    idades: idades
                };
            } catch (error) {
                console.error('Erro ao coletar dados do formulário:', error);
                return null;
            }
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

                // O container interno é onde os cards de seguro (e o botão Ver Mais) serão inseridos
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
                    <div class="flex flex-col gap-6">
                        </div>
                `;
                tabsContent.appendChild(tabContent);
            }
        }

        // Função para atualizar título da tab com nome do viajante
        window.updateTabTitle = function(tabIndex, value) {
            const tabButton = document.querySelectorAll('.tab-button')[tabIndex];
            if (tabButton) {
                const idade = idades[tabIndex] || 25;
                const nomeExibir = value.trim() || `Viajante ${tabIndex + 1}`;
                tabButton.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user text-sm"></i>
                        <span>${nomeExibir}</span>
                        <span class="text-xs text-gray-500">(${idade} anos)</span>
                    </div>
                `;
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

            return false; // Previne qualquer navegação
        }

        function searchInsuranceAttempt() {
            if (attemptsMade === 0) showLoading();

            attemptsMade++;
            updateProgress(attemptsMade);

            const formData = getFormData();
            if (!formData) {
                hideLoading();
                showErrorMessage('Dados do formulário inválidos ou incompletos');
                return;
            }

            fetch('{{ route('run.Scraping.ajax') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw new Error(err.message || `HTTP ${res.status}: ${res.statusText}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error + (data.message ? ': ' + data.message : ''));

                if (data.frases && Object.keys(data.frases).length > 0) {
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
                showErrorMessage(err.message || 'Erro ao buscar seguros');
            });
        }

        // --- ADIÇÃO: função global para reiniciar a busca (polling) ---
        window.restartSearch = function () {
            console.info('[Seguros] restartSearch acionado');
            attemptsMade = 0;
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
            // Fazer a primeira tentativa imediatamente
            try {
                searchInsuranceAttempt();
            } catch (e) {
                console.error('[Seguros] erro ao executar primeira tentativa:', e);
            }

            // Agendar tentativas subsequentes
            intervalId = setInterval(() => {
                if (attemptsMade < maxAttempts) {
                    try {
                        searchInsuranceAttempt();
                    } catch (e) {
                        console.error('[Seguros] erro em polling:', e);
                        clearInterval(intervalId);
                        intervalId = null;
                    }
                } else {
                    clearInterval(intervalId);
                    intervalId = null;
                }
            }, 4000);
        };

        function showTravelerTabsOnly() {
            // Ocultar loading e container de seguros
            document.getElementById('loading-seguros').style.display = 'none';
            document.getElementById('seguros-container').style.display = 'none';

            // Mostrar container de tabs
            document.getElementById('tabs-seguros-container').style.display = 'block';

            // Obter informações dos viajantes e criar apenas as tabs (sem seguros)
            const viajantesInfo = getViajantesInfo();
            createTabs(viajantesInfo);
        }

        // Tornar as funções globais para que possam ser chamadas de outros scripts
        window.showTravelerTabsOnly = showTravelerTabsOnly;

        function showNoInsuranceNeeded() {
            document.getElementById('loading-seguros').style.display = 'none';
            document.getElementById('tabs-seguros-container').style.display = 'none';
            document.getElementById('seguros-container').style.display = 'block';
            document.getElementById('seguros-container').innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <div class="text-blue-800 font-semibold mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Seguro não selecionado
                    </div>
                    <div class="text-blue-700 text-sm">
                        Você optou por não contratar seguro para esta viagem. Você pode continuar para o próximo passo.
                    </div>
                </div>
            `;
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

        // =========================================================================
        // Funções novas / modificadas: filtrar por idade, renderizar por viajante
        // =========================================================================

        // Parseia strings de faixa etária e retorna [min, max] ou null se indisponível
        function parseAgeRange(text) {
            if (!text) return null;
            const txt = String(text).toLowerCase();

            // Busca explicitamente por "faixa etária" e pega o primeiro intervalo
            let faixaMatch = txt.match(/faixa et[aá]ria[:\s]*([^\|]+)/i);
            let faixaStr = faixaMatch ? faixaMatch[1] : txt;

            // Remove tokens de preço e outros irrelevantes
            faixaStr = faixaStr.replace(/r\$\s*[\d\.,]+/g, '')
                               .replace(/us\$?\s*[\d\.,]+/g, '')
                               .replace(/usd\s*[\d\.,]+/g, '')
                               .replace(/maiores de\s*\d{1,3}/g, '')
                               .replace(/menores de\s*\d{1,3}/g, '')
                               .replace(/até\s*\d{1,3}/g, '');

            // Padrão "0 a 75", "0 até 75", "0-75"
            let m = faixaStr.match(/(\d{1,3})\s*(?:a|até|-)\s*(\d{1,3})/i);
            if (m) {
                return [parseInt(m[1], 10), parseInt(m[2], 10)];
            }

            // "maiores de 64 anos" -> [65, 120]
            m = txt.match(/maiores?\s*de\s*(\d{1,3})/i);
            if (m) {
                return [parseInt(m[1], 10) + 1, 120];
            }

            // "até 75 anos" -> [0,75]
            m = txt.match(/até\s*(\d{1,3})/i);
            if (m) {
                return [0, parseInt(m[1], 10)];
            }

            // Se não encontrou nada, retorna null
            return null;
        }

        function isValidForAge(insurance, age) {
            const detalhes = insurance.detalhes_etarios || insurance.details || '';
            const range = parseAgeRange(detalhes);
            // Se não houver faixa definida, só mostra para idades até 75
            if (!range) return age <= 75;
            return age >= range[0] && age <= range[1];
        }

        // Mantém map de filtros por viajante para paginação/Ver mais
        window.filteredInsurancesByViajante = window.filteredInsurancesByViajante || {};

        function displayInsurancesForTab(viaganteIndex, insurances, tabContentContainer) {
            // Obtem idades atuais (fallback para 25)
            const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
            const idade = (idadeInputs[viaganteIndex] && parseInt(idadeInputs[viaganteIndex].value)) ? parseInt(idadeInputs[viaganteIndex].value) : 25;

            // Cria array com mapeamento para índice original
            const mapped = insurances.map((ins, idx) => ({ insurance: ins, originalIndex: idx }));

            // Filtra por idade do viajante
            const filtered = mapped.filter(item => isValidForAge(item.insurance, idade));

            // Salva para uso posterior (show more)
            window.filteredInsurancesByViajante[viaganteIndex] = filtered;

            // Inicializa contagem visível se necessário
            if (!window.visibleInsuranceCount[viaganteIndex]) {
                window.visibleInsuranceCount[viaganteIndex] = INSURANCES_PER_PAGE;
            }

            const currentVisibleCount = window.visibleInsuranceCount[viaganteIndex];
            const totalInsurances = filtered.length;

            // Limpa o container
            tabContentContainer.innerHTML = '';
            let html = '';

            // Renderiza apenas os seguros visíveis (usando originalIndex para referência)
            filtered.forEach((item, idx) => {
                if (idx < currentVisibleCount) {
                    const insurance = item.insurance;
                    const originalIndex = item.originalIndex;

                    const seguradora = insurance.seguradora || 'Seguradora N/A';
                    const plano = insurance.plano || 'Plano N/A';
                    const coberturaMedica = insurance.coberturas?.medica || 'N/A';
                    const coberturaBagagem = insurance.coberturas?.bagagem || 'N/A';
                    const precoPix = insurance.precos?.pix || 'N/A';
                    const precoCartao = insurance.precos?.cartao || 'N/A';
                    const parcelas = insurance.precos?.parcelas || '';
                    const detalhesEtarios = insurance.detalhes_etarios || '';
                    const link = insurance.link || '#';

                    html += `
                        <div class="seguro-card" data-insurance-index="${idx}" data-original-index="${originalIndex}" data-viajante-index="${viaganteIndex}" onclick="selectInsurance(this, ${originalIndex}, ${viaganteIndex})">
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
                }
            });

            // Adiciona os cards ao container
            tabContentContainer.innerHTML = html;

            // Adiciona o botão "Ver Mais" se houver seguros não exibidos
            if (currentVisibleCount < totalInsurances) {
                const showMoreButton = document.createElement('button');
                showMoreButton.type = 'button';
                showMoreButton.className = 'mx-auto w-1/6 mt-6 py-3 px-4 bg-gray-100 text-blue-600 font-semibold rounded-lg hover:bg-gray-200 transition-colors border border-gray-300';
                showMoreButton.innerHTML = `Ver mais +`;
                showMoreButton.onclick = (e) => {
                    e.preventDefault();
                    window.showMoreInsurances(viaganteIndex);
                };
                
                const buttonContainer = document.createElement('div');
                buttonContainer.className = 'flex justify-center w-full';
                buttonContainer.appendChild(showMoreButton);
                
                tabContentContainer.appendChild(buttonContainer);
            }
            
            // Re-aplicar a seleção se houver uma (após recarregar a lista)
            const selectedData = window.selectedInsurancesByViajante ? window.selectedInsurancesByViajante[viaganteIndex] : null;
            if (selectedData) {
                const selectedCard = tabContentContainer.querySelector(`.seguro-card[data-original-index="${selectedData.insuranceIndex}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        }

        // =========================================================================
        // Função para o botão "Ver Mais" (ajustada para trabalhar com filtered list)
        // =========================================================================
        window.showMoreInsurances = function(viaganteIndex) {
            const viajantesInfo = getViajantesInfo();
            const idade = viajantesInfo.idades[viaganteIndex];
            const segurosParaIdade = window.currentInsurances[idade] || [];
            const filtered = window.filteredInsurancesByViajante[viaganteIndex] || [];
            const currentVisible = window.visibleInsuranceCount[viaganteIndex] || INSURANCES_PER_PAGE;
            const newVisible = currentVisible + INSURANCES_PER_PAGE;
            window.visibleInsuranceCount[viaganteIndex] = Math.min(newVisible, filtered.length);

            const tabContentContainer = document.querySelector(`#tab-content-${viaganteIndex} .flex.flex-col.gap-6`);
            if (tabContentContainer) {
                // Agora passa apenas os seguros da idade correta
                displayInsurancesForTab(viaganteIndex, segurosParaIdade, tabContentContainer);
            }
        };

        // =========================================================================
        // MODIFICADO: renderInsurances para usar a nova lógica de paginação / filtro
        // =========================================================================
        function renderInsurances(insurances) {
            window.currentInsurances = insurances;
            window.visibleInsuranceCount = {};
            window.filteredInsurancesByViajante = {};

            const viajantesInfo = getViajantesInfo();
            createTabs(viajantesInfo);

            const { numPessoas, idades } = viajantesInfo;
            
            for (let viaganteIndex = 0; viaganteIndex < numPessoas; viaganteIndex++) {
                const tabContentContainer = document.querySelector(`#tab-content-${viaganteIndex} .flex.flex-col.gap-6`);
                if (!tabContentContainer) continue;
                
                // Agora cada tab mostra apenas seguros compatíveis com a idade do viajante
                displayInsurancesForTab(viaganteIndex, insurances, tabContentContainer);
            }
        }

        // REMOVA a versão antiga de renderInsurances que espera array!
        // MANTENHA apenas esta versão, que espera um objeto frases { idade: [seguros...] }
        function renderInsurances(frasesObj) {
            window.currentInsurances = frasesObj;
            window.visibleInsuranceCount = {};
            window.filteredInsurancesByViajante = {};

            const viajantesInfo = getViajantesInfo();
            createTabs(viajantesInfo);

            const { numPessoas, idades } = viajantesInfo;

            for (let viaganteIndex = 0; viaganteIndex < numPessoas; viaganteIndex++) {
                const idade = idades[viaganteIndex];
                const tabContentContainer = document.querySelector(`#tab-content-${viaganteIndex} .flex.flex-col.gap-6`);
                if (!tabContentContainer) continue;

                // Pega os seguros para a idade do viajante
                const segurosParaIdade = frasesObj[idade] || [];
                displayInsurancesForTab(viaganteIndex, segurosParaIdade, tabContentContainer);
            }
        }

        // =========================================================================
        // Seleção de seguro: agora recebe originalIndex (índice no array completo)
        // =========================================================================
        window.selectInsurance = function (cardElement, originalIndex, viaganteIndex) {
            const currentTabContent = document.querySelector(`#tab-content-${viaganteIndex}`);
            if (currentTabContent) {
                currentTabContent.querySelectorAll('.seguro-card').forEach(card => card.classList.remove('selected'));
            }
            cardElement.classList.add('selected');

            const insuranceData = window.currentInsurances[originalIndex];
            if (!insuranceData) {
                console.error('Dados do seguro não encontrados para o índice original:', originalIndex);
                return;
            }

            if (!window.selectedInsurancesByViajante) {
                window.selectedInsurancesByViajante = {};
            }
            window.selectedInsurancesByViajante[viaganteIndex] = {
                insuranceData: insuranceData,
                insuranceIndex: originalIndex,
                nomeViajante: getViajanteNome(viaganteIndex)
            };

            const fullInsuranceName = `${insuranceData.seguradora} - ${insuranceData.plano}`;
            sessionStorage.setItem(`selectedSeguroName_viajante_${viaganteIndex}`, fullInsuranceName);
            sessionStorage.setItem(`nomeViajante_${viaganteIndex}`, getViajanteNome(viaganteIndex));

            if (viaganteIndex === 0) {
                const hiddenInput = document.getElementById('seguroSelecionadoData');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(insuranceData);
                }
                sessionStorage.setItem('selectedSeguroName', fullInsuranceName);
            }
        };

        const step4Observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                // Verificar se seguro foi selecionado como "Sim" ou "Não" no step 2
                const seguroSelect = document.getElementById('seguroViagem');
                const desejaSeguro = seguroSelect ? seguroSelect.value === 'Sim' : false;

                if (desejaSeguro) {
                    // Se escolheu "Sim": fazer busca normal de seguros
                    const hasOldFormat = document.querySelector('.seguro-card');
                    const hasNewFormat = document.querySelector('#tabs-seguros-container .seguro-card');

                    if (!hasOldFormat && !hasNewFormat) {
                        restartSearch();
                    }
                } else {
                    // Se escolheu "Não": mostrar tabs dos viajantes para preenchimento de nomes
                    showTravelerTabsOnly();
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Supondo que você tem as idades dos viajantes disponíveis
    const idades = window.idadesViajantes || [18, 20, 90]; // Exemplo, substitua pelo real
    const tabs = document.querySelectorAll('.tab-button');
    const segurosWrapper = document.getElementById('seguros-wrapper');

    function renderInsurances(seguroArray, container) {
        container.innerHTML = '';
        if (!seguroArray || seguroArray.length === 0) {
            container.innerHTML = '<div class="text-red-600">Nenhum seguro disponível para esta idade.</div>';
            return;
        }
        seguroArray.forEach(seguro => {
            container.innerHTML += `<div class="insurance-card">${seguro.seguradora} - ${seguro.plano} (${seguro.detalhes_etarios || 'todas idades'})</div>`;
        });
    }

    function carregarSeguros() {
        // Verificar se os elementos necessários existem
        const continenteSelect = document.getElementById('MainContent_Cotador_selContinente');
        const dataIdaInput = document.getElementById('data_ida');
        const dataVoltaInput = document.getElementById('data_volta');

        if (!continenteSelect || !dataIdaInput || !dataVoltaInput) {
            console.error('Elementos necessários para carregar seguros não encontrados');
            return;
        }

        if (!continenteSelect.value || !dataIdaInput.value || !dataVoltaInput.value) {
            console.warn('Dados incompletos para buscar seguros');
            return;
        }

        // Monta o payload para o backend
        const data = {
            destino: continenteSelect.value,
            data_ida: dataIdaInput.value,
            data_volta: dataVoltaInput.value,
            idades: idades
        };

        fetch('/trip/search-insurance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'concluido' && data.frases) {
                // Para cada tab/idade, renderiza os seguros corretos
                tabs.forEach((tab, idx) => {
                    const idade = idades[idx];
                    let container = document.getElementById(`seguros-container-${idx}`);
                    if (!container) {
                        container = document.createElement('div');
                        container.id = `seguros-container-${idx}`;
                        container.classList.add('seguros-container');
                        if (idx !== 0) container.classList.add('hidden');
                        
                        if (segurosWrapper) {
                            segurosWrapper.appendChild(container);
                        }
                    }
                    renderInsurances(data.frases[idade], container);

                    tab.addEventListener('click', () => {
                        tabs.forEach(t => t.classList.remove('active'));
                        tab.classList.add('active');
                        document.querySelectorAll('.seguros-container').forEach(c => c.classList.add('hidden'));
                        container.classList.remove('hidden');
                    });
                });
            } else {
                // Mostra mensagem de carregando ou erro
                if (segurosWrapper) {
                    segurosWrapper.innerHTML = '<div class="text-gray-600">Carregando seguros...</div>';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao carregar seguros:', error);
            if (segurosWrapper) {
                segurosWrapper.innerHTML = '<div class="text-red-600">Erro ao carregar seguros. Tente novamente.</div>';
            }
        });
    }

    carregarSeguros();
});
</script>