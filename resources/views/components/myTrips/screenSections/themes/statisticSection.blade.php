<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/30 rounded-lg p-2">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-yellow-500">Cotação de Moeda</h3>
                    <p class="text-yellow-400 text-sm font-medium" id="destination-currency-text">{{ $viagem->destino_viagem }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-yellow-500 text-sm">Última atualização</div>
                <div class="text-yellow-400 font-semibold" id="last-updated">Carregando...</div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" id="currency-cards-container">
            {{-- Os cards serão preenchidos dinamicamente via JavaScript --}}
        </div>

        <div class="bg-gray-50 rounded-lg p-8 border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-area text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">Gráfico de Cotação: <span id="chart-currency-title"></span></h4>
                <p class="text-gray-500" id="chart-message">Carregando gráfico...</p>
                <div class="mt-4 flex justify-center space-x-2">
                    <button class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium" data-range="7">7D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="30">30D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="90">90D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="365">1A</button>
                </div>
                <div style="max-height: 300px; margin-top: 1rem;">
                    <canvas id="currencyChart" class="hidden"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let userLocalCurrency = 'BRL'; // Valor padrão, será atualizado pela API de geolocalização
    let destinationCurrencyCode = 'USD'; // Valor padrão inicial, será atualizado pela API REST Countries

    let myChart; // Variável para a instância do Chart.js

    // A chave da API do Google, injetada pelo Blade/Laravel
    // IMPORTANTE: Certifique-se de que esta chave da API tenha as permissões para a Google Cloud Translation API.
    const GOOGLE_API_KEY = "AIzaSyDWLH0DB_w7iFWxaPJHOl69rSP6YT3sp80";

    function formatDateTime(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    async function getUserIP() {
        try {
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            return data.ip;
        } catch (error) {
            console.error('Erro ao obter o IP do usuário:', error);
            return null;
        }
    }

    async function getUserLocalCurrency(ip) {
        if (!ip) return 'BRL';

        try {
            const response = await fetch(`https://ipapi.co/${ip}/json/`);
            const data = await response.json();
            if (data && data.currency) {
                return data.currency;
            }
        } catch (error) {
            console.error('Erro ao obter a moeda local do usuário:', error);
        }
        return 'BRL';
    }

    /**
     * Função para traduzir um texto usando a Google Cloud Translation API.
     * @param {string} text O texto a ser traduzido.
     * @param {string} targetLanguage O idioma de destino (ex: 'en' para inglês).
     * @returns {Promise<string|null>} O texto traduzido ou null em caso de erro.
     */
    async function translateText(text, targetLanguage) {
        if (!GOOGLE_API_KEY) {
            console.warn('GOOGLE_API_KEY não configurada. A tradução do país não será realizada.');
            return null;
        }

        try {
            const response = await fetch(`https://translation.googleapis.com/language/translate/v2?key=${GOOGLE_API_KEY}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    q: text,
                    target: targetLanguage,
                    format: 'text' // Garante que o formato é texto simples
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error(`Erro na API de Tradução do Google (status: ${response.status}):`, errorData);
                // Retorna o texto original em caso de erro da API para que a busca REST Countries possa ser tentada com o nome original.
                return null;
            }

            const data = await response.json();
            if (data && data.data && data.data.translations && data.data.translations.length > 0) {
                return data.data.translations[0].translatedText;
            } else {
                console.warn('Resposta da API de Tradução do Google sem tradução esperada:', data);
                return null;
            }
        } catch (error) {
            console.error('Erro ao chamar a API de Tradução do Google:', error);
            return null;
        }
    }

    /**
     * Função para descobrir a moeda do destino usando a API REST Countries.
     * Agora usa a API de Tradução do Google para converter o nome do país para inglês.
     * @param {string} destination Viagem->destino_viagem (ex: "Roma, Itália" ou "Turquia")
     * @returns {string} O código da moeda (ex: "EUR") ou null se não encontrada.
     */
    async function getDestinationCurrency(destination) {
        const parts = destination.split(',').map(part => part.trim());
        let countryNameOriginal = parts[parts.length - 1]; // Pega a última parte como o país

        if (!countryNameOriginal) {
            console.warn('Não foi possível extrair o nome do país do destino:', destination);
            return null;
        }

        let possibleCountryNames = [];

        // 1. Tenta traduzir o nome do país para o inglês
        const translatedCountryName = await translateText(countryNameOriginal, 'en');

        if (translatedCountryName) {
            possibleCountryNames.push(translatedCountryName);
            console.log(`País '${countryNameOriginal}' traduzido para '${translatedCountryName}'.`);

            // Se a tradução for 'Türkiye', adiciona 'Turkey' como fallback
            if (translatedCountryName.toLowerCase() === 'türkiye') {
                possibleCountryNames.push('Turkey');
                console.log(`Adicionando 'Turkey' como fallback para 'Türkiye'.`);
            }
        } else {
            console.warn(`Não foi possível traduzir '${countryNameOriginal}'. Tentando buscar com o nome original e fallbacks conhecidos.`);
        }

        // 2. Adiciona o nome original como fallback, caso a tradução falhe
        if (!possibleCountryNames.includes(countryNameOriginal)) {
             possibleCountryNames.push(countryNameOriginal);
        }
        // 3. Adiciona alguns fallbacks manuais para casos específicos se a tradução não for exata ou a API REST Countries tiver problemas
        if (countryNameOriginal.toLowerCase() === 'estados unidos' && !possibleCountryNames.includes('United States')) {
            possibleCountryNames.push('United States');
        }
        if (countryNameOriginal.toLowerCase() === 'reino unido' && !possibleCountryNames.includes('United Kingdom')) {
            possibleCountryNames.push('United Kingdom');
        }
        if (countryNameOriginal.toLowerCase() === 'irlanda' && !possibleCountryNames.includes('Ireland')) {
            possibleCountryNames.push('Ireland');
        }


        for (const countryNameToTry of possibleCountryNames) {
            try {
                // Tenta busca fullText primeiro
                let response = await fetch(`https://restcountries.com/v3.1/name/${countryNameToTry}?fullText=true`);
                let data;

                if (response.ok) {
                    data = await response.json();
                } else {
                    // Se fullText falhar, tenta uma busca parcial
                    const partialResponse = await fetch(`https://restcountries.com/v3.1/name/${countryNameToTry}`);
                    if (partialResponse.ok) {
                        data = await partialResponse.json();
                    } else {
                        console.warn(`Falha na busca REST Countries para '${countryNameToTry}' (status: ${partialResponse.status}).`);
                        continue; // Tenta o próximo nome na lista
                    }
                }

                if (data && data.length > 0) {
                    // Tenta encontrar o país correto na lista de resultados
                    const country = data.find(c =>
                        c.name.common.toLowerCase() === countryNameToTry.toLowerCase() ||
                        c.altSpellings?.some(s => s.toLowerCase() === countryNameToTry.toLowerCase()) ||
                        c.translations?.por?.common?.toLowerCase() === countryNameOriginal.toLowerCase()
                    ) || data[0]; // Se não encontrar correspondência exata, pega o primeiro resultado

                    if (country && country.currencies) {
                        console.log(`Moeda encontrada para '${countryNameToTry}':`, Object.keys(country.currencies)[0]);
                        return Object.keys(country.currencies)[0]; // Retorna o primeiro código de moeda
                    }
                }
            } catch (error) {
                console.error(`Erro ao buscar moeda do destino para '${countryNameToTry}' na API REST Countries:`, error);
            }
        }

        console.warn('Moeda não encontrada para o destino após todas as tentativas:', destination);
        return null;
    }


    async function getCurrencyRates() {
        document.getElementById('last-updated').textContent = 'Atualizando...';
        const currencyCardsContainer = document.getElementById('currency-cards-container');
        currencyCardsContainer.innerHTML = ''; // Limpa cards existentes

        try {
            const userIp = await getUserIP();
            userLocalCurrency = await getUserLocalCurrency(userIp);

            const destinoViagem = "{{ $viagem->destino_viagem }}";
            const fetchedDestinationCurrency = await getDestinationCurrency(destinoViagem);
            if (fetchedDestinationCurrency) {
                destinationCurrencyCode = fetchedDestinationCurrency;
                document.getElementById('destination-currency-text').textContent = `${destinoViagem} (${destinationCurrencyCode})`;
            } else {
                document.getElementById('destination-currency-text').textContent = `${destinoViagem} (Moeda N/A)`;
                console.warn('Não foi possível determinar a moeda do destino. Usando padrão USD.');
                destinationCurrencyCode = 'USD'; // Fallback se não encontrar
            }

            // Moedas fixas para os cards: Dólar, Euro e a Moeda do Destino
            // Garante que não haja duplicatas caso a moeda de destino seja USD ou EUR
            const currenciesToCompare = ['USD', 'EUR'];
            if (!currenciesToCompare.includes(destinationCurrencyCode)) {
                currenciesToCompare.push(destinationCurrencyCode);
            }


            const fetchPromises = currenciesToCompare.map(async (fromCurrency) => {
                if (fromCurrency === userLocalCurrency) {
                    return { from: fromCurrency, to: userLocalCurrency, rate: 'N/A', pctChange: 'N/A', isSame: true };
                }

                try {
                    const response = await fetch(`https://api.frankfurter.app/latest?from=${fromCurrency}&to=${userLocalCurrency}`);
                    const data = await response.json();
                    let bid = 'N/A';
                    let pctChange = 'N/A';
                    let errorStatus = null; // Para guardar o status de erro

                    if (response.status === 404) { // Captura especificamente o 404 da Frankfurter
                        errorStatus = 404;
                        console.warn(`Dados para ${fromCurrency}/${userLocalCurrency} não encontrados (404) na Frankfurter.`);
                    } else if (!response.ok) {
                        errorStatus = response.status;
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }


                    if (data && data.rates && data.rates[userLocalCurrency]) {
                        bid = data.rates[userLocalCurrency];

                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        const yesterdayFormatted = yesterday.toISOString().split('T')[0];

                        const prevResponse = await fetch(`https://api.frankfurter.app/${yesterdayFormatted}?from=${fromCurrency}&to=${userLocalCurrency}`);
                        const prevData = await prevResponse.json();

                        if (prevResponse.status === 404) {
                            console.warn(`Dados históricos para ${fromCurrency}/${userLocalCurrency} não encontrados (404) na Frankfurter.`);
                        } else if (!prevResponse.ok) {
                             throw new Error(`HTTP error! status: ${prevResponse.status} for historical data.`);
                        }


                        if (prevData && prevData.rates && prevData.rates[userLocalCurrency]) {
                            const prevBid = prevData.rates[userLocalCurrency];
                            if (prevBid !== 0) {
                                pctChange = ((bid - prevBid) / prevBid) * 100;
                            }
                        }
                    }
                    return { from: fromCurrency, to: userLocalCurrency, rate: bid, pctChange: pctChange, isSame: false, errorStatus: errorStatus };
                } catch (error) {
                    console.error(`Erro ao buscar ${fromCurrency}/${userLocalCurrency}:`, error);
                    return { from: fromCurrency, to: userLocalCurrency, rate: 'Erro', pctChange: 'Erro', isSame: false, errorStatus: 500 }; // Captura outros erros
                }
            });

            const results = await Promise.all(fetchPromises);

            results.forEach(result => {
                let cardHtml = '';
                if (result.isSame) {
                    cardHtml = `
                        <div class="bg-white rounded-lg p-4 border border-gray-400 shadow-sm currency-card">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">--</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">${result.from}/${result.to}</span>
                                </div>
                                <span class="text-gray-700 text-sm font-semibold">N/A</span>
                            </div>
                            <div class="text-2xl font-extrabold text-gray-900">N/A</div>
                            <div class="text-sm text-gray-800">Moeda Local</div>
                        </div>
                    `;
                } else {
                    let cardClass = 'border-gray-400';
                    let changeClass = 'text-gray-700';
                    let displayPctChange = 'N/A';
                    let symbol = '';
                    let rateDisplay = 'N/A';
                    let cardSubtitle = '';

                    if (result.errorStatus === 404 && result.from === destinationCurrencyCode) {
                         cardClass = 'border-orange-400';
                         changeClass = 'text-orange-700';
                         rateDisplay = 'N/A';
                         displayPctChange = 'N/A';
                         cardSubtitle = 'Moeda com dados instáveis/indisponíveis';
                    } else if (typeof result.pctChange === 'number') {
                        if (result.pctChange >= 0) {
                            cardClass = 'border-green-400';
                            changeClass = 'text-green-700';
                        } else {
                            cardClass = 'border-red-400';
                            changeClass = 'text-red-700';
                        }
                        displayPctChange = `${result.pctChange > 0 ? '+' : ''}${result.pctChange.toFixed(2)}%`;
                        rateDisplay = `${result.to} ${typeof result.rate === 'number' ? result.rate.toFixed(2).replace('.', ',') : result.rate}`;
                    } else if (result.pctChange === 'Erro' || result.rate === 'Erro') {
                        changeClass = 'text-red-700';
                        cardClass = 'border-red-400';
                        displayPctChange = 'Erro';
                        rateDisplay = 'Erro';
                        cardSubtitle = 'Erro ao carregar';
                    } else {
                        rateDisplay = `${result.to} ${typeof result.rate === 'number' ? result.rate.toFixed(2).replace('.', ',') : result.rate}`;
                    }


                    try {
                        const formatter = new Intl.NumberFormat(undefined, { style: 'currency', currency: result.from });
                        symbol = formatter.format(0).replace(/0|\s/g, '').trim();
                        if (!symbol) symbol = result.from;
                    } catch (e) {
                        symbol = result.from;
                    }

                    if (!cardSubtitle) { // Se não foi definida por um erro específico
                        if (result.from === 'USD') {
                            cardSubtitle = 'Dólar Americano';
                        } else if (result.from === 'EUR') {
                            cardSubtitle = 'Euro';
                        } else if (result.from === destinationCurrencyCode) {
                            cardSubtitle = 'Moeda do Destino';
                        } else {
                            cardSubtitle = `${result.from} Cotação`;
                        }
                    }


                    cardHtml = `
                        <div class="bg-white rounded-lg p-4 ${cardClass} shadow-sm currency-card">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">${symbol}</span>
                                    </div>
                                    <span class="font-semibold text-blue-900">${result.from}/<span class="local-currency-code">${result.to}</span></span>
                                </div>
                                <span class="${changeClass} text-sm font-semibold">${displayPctChange}</span>
                            </div>
                            <div class="text-2xl font-extrabold text-gray-900">${rateDisplay}</div>
                            <div class="text-sm text-gray-800">${cardSubtitle}</div>
                        </div>
                    `;
                }
                currencyCardsContainer.innerHTML += cardHtml;
            });

            document.getElementById('last-updated').textContent = formatDateTime(new Date());

        } catch (error) {
            console.error('Erro geral ao buscar cotações de moeda:', error);
            document.getElementById('last-updated').textContent = 'Erro ao carregar';
            currencyCardsContainer.innerHTML = `
                <div class="bg-white rounded-lg p-4 border-red-400 shadow-sm currency-card col-span-full text-center">
                    <p class="text-red-700 font-semibold">Erro ao carregar dados de moeda. Por favor, tente novamente mais tarde.</p>
                </div>
            `;
        }
    }

    async function getHistoricalData(rangeDays) {
        // O gráfico sempre compara a moeda do destino com a moeda local do usuário
        const fromCurrency = destinationCurrencyCode;
        const toCurrency = userLocalCurrency;

        if (fromCurrency === toCurrency) {
            return {
                labels: [],
                dataValues: [],
                message: `Comparação não aplicável: A moeda de destino (${fromCurrency}) e sua moeda local (${toCurrency}) são as mesmas.`,
            };
        }

        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - rangeDays);

        const startFormatted = startDate.toISOString().split('T')[0];
        const endFormatted = endDate.toISOString().split('T')[0];

        try {
            const response = await fetch(`https://api.frankfurter.app/${startFormatted}..${endFormatted}?from=${fromCurrency}&to=${toCurrency}`);
            if (!response.ok) {
                if (response.status === 404) {
                    return {
                        labels: [],
                        dataValues: [],
                        message: `Dados históricos para ${fromCurrency}/${toCurrency} indisponíveis ou com alta instabilidade.`,
                    };
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.rates || Object.keys(data.rates).length === 0) {
                return {
                    labels: [],
                    dataValues: [],
                    message: `Nenhum dado histórico disponível para ${fromCurrency}/${toCurrency}.`,
                };
            }

            const labels = Object.keys(data.rates).sort();
            const dataValues = labels.map(date => data.rates[date][toCurrency]);

            return { labels, dataValues, message: null };

        } catch (error) {
            console.error('Erro ao buscar dados históricos:', error);
            return { labels: [], dataValues: [], message: `Erro ao buscar dados históricos para ${fromCurrency}/${toCurrency}.` };
        }
    }

    async function renderChart(rangeDays = 7) {
        const chartMessageElement = document.getElementById('chart-message');
        const chartCanvas = document.getElementById('currencyChart');
        const chartTitleElement = document.getElementById('chart-currency-title');

        chartMessageElement.textContent = 'Carregando gráfico...';
        chartCanvas.classList.add('hidden');
        chartTitleElement.textContent = `(${destinationCurrencyCode}/${userLocalCurrency})`; // Atualiza o título do gráfico

        const { labels, dataValues, message } = await getHistoricalData(rangeDays);

        if (myChart) {
            myChart.destroy();
        }

        if (message) {
            chartMessageElement.textContent = message;
            chartCanvas.classList.add('hidden');
            chartMessageElement.classList.remove('hidden');
        } else if (labels.length > 0 && dataValues.length > 0) {
            chartMessageElement.classList.add('hidden');
            chartCanvas.classList.remove('hidden');

            const ctx = chartCanvas.getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `${destinationCurrencyCode}/${userLocalCurrency}`,
                        data: dataValues,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: 'rgb(107, 114, 128)'
                            }
                        },
                        title: {
                            display: true,
                            text: `Custo de ${destinationCurrencyCode} em ${userLocalCurrency} (últimos ${rangeDays} dias)`,
                            color: 'rgb(55, 65, 81)',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: 'rgb(107, 114, 128)'
                            },
                            grid: {
                                color: 'rgba(209, 213, 219, 0.3)'
                            }
                        },
                        y: {
                            ticks: {
                                color: 'rgb(107, 114, 128)'
                            },
                            grid: {
                                color: 'rgba(209, 213, 219, 0.3)'
                            }
                        }
                    }
                }
            });
        } else {
            chartMessageElement.textContent = `Dados históricos para ${destinationCurrencyCode}/${userLocalCurrency} não disponíveis ou erro ao carregar o gráfico.`;
            chartCanvas.classList.add('hidden');
            chartMessageElement.classList.remove('hidden');
        }
    }

    document.querySelectorAll('[data-range]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-range]').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-700', 'font-medium');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.remove('bg-gray-100', 'text-gray-600');
            this.classList.add('bg-blue-100', 'text-blue-700', 'font-medium');

            const range = parseInt(this.dataset.range);
            renderChart(range);
        });
    });

    document.addEventListener('DOMContentLoaded', async () => {
        // Garante que a moeda local e a moeda do destino sejam determinadas primeiro.
        // getCurrencyRates agora faz a chamada para getDestinationCurrency
        await getCurrencyRates();
        // Renderiza o gráfico APÓS as moedas serem definidas
        await renderChart(7);
    });
</script>