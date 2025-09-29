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
            document.getElementById('seguros-container').style.display = 'block';
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
            document.getElementById('seguros-container').innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <div class="text-yellow-800 font-semibold mb-2">Nenhum seguro encontrado no momento</div>
                <div class="text-yellow-700 text-sm mb-4">Não foi possível encontrar seguros para os critérios selecionados.</div>
                <button onclick="restartSearch()" class="btn-primary">Tentar Novamente</button>
            </div>
        `;
        }

        function showErrorMessage(errorMessage) {
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
            let html = '<div class="flex flex-col gap-6">';

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
                <div class="seguro-card" data-insurance-index="${index}" onclick="selectInsurance(this, ${index})">
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
            html += '</div>';
            document.getElementById('seguros-container').innerHTML = html;
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

        window.selectInsurance = function (cardElement, index) {
            // 1. Atualiza a interface (UI)
            document.querySelectorAll('.seguro-card').forEach(card => card.classList.remove('selected'));
            cardElement.classList.add('selected');

            // 2. Pega os dados do seguro selecionado do array global
            const insuranceData = window.currentInsurances[index];

            if (!insuranceData) {
                console.error('Dados do seguro não encontrados para o índice:', index);
                return;
            }

            const fullInsuranceName = `${insuranceData.seguradora} - ${insuranceData.plano}`;
            sessionStorage.setItem('selectedSeguroName', fullInsuranceName);

            const hiddenInput = document.getElementById('seguroSelecionadoData');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(insuranceData);
            } else {
                console.error('O campo hidden #seguroSelecionadoData não foi encontrado no formulário.');
            }
        };

        const step4Observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                if (!document.querySelector('.seguro-card')) {
                    restartSearch();
                }
            }
        }, { threshold: 0.1 });

        step4Observer.observe(document.querySelector('#seguros-container').closest('.form-step'));
    });
</script>