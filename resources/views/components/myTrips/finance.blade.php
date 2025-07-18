<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/30 rounded-lg p-2">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-yellow-500">Cotação de Moeda</h3>
                    {{-- O destino da viagem ainda virá do PHP --}}
                    <p class="text-yellow-400 text-sm font-medium" id="destination-currency-text">{{ $viagem->destino_viagem }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-yellow-500 text-sm">Última atualização</div>
                {{-- A data de atualização será preenchida via JS --}}
                <div class="text-yellow-400 font-semibold" id="last-updated">Carregando...</div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" id="currency-cards-container">
            {{-- Os cards serão preenchidos dinamicamente via JavaScript --}}
            <div class="bg-white rounded-lg p-4 border border-green-400 shadow-sm currency-card" id="usd-card">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">$</span>
                        </div>
                        <span class="font-semibold text-green-900">USD/<span class="local-currency-code">BRL</span></span>
                    </div>
                    <span class="text-green-700 text-sm font-semibold" id="usd-change"></span>
                </div>
                <div class="text-2xl font-extrabold text-gray-900" id="usd-value">Carregando...</div>
                <div class="text-sm text-gray-800">Dólar Americano</div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-blue-400 shadow-sm currency-card" id="eur-card">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">€</span>
                        </div>
                        <span class="font-semibold text-blue-900">EUR/<span class="local-currency-code">BRL</span></span>
                    </div>
                    <span class="text-red-700 text-sm font-semibold" id="eur-change"></span>
                </div>
                <div class="text-2xl font-extrabold text-gray-900" id="eur-value">Carregando...</div>
                <div class="text-sm text-gray-800">Euro</div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-purple-400 shadow-sm currency-card" id="gbp-card">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">£</span>
                        </div>
                        <span class="font-semibold text-purple-900">GBP/<span class="local-currency-code">BRL</span></span>
                    </div>
                    <span class="text-green-700 text-sm font-semibold" id="gbp-change"></span>
                </div>
                <div class="text-2xl font-extrabold text-gray-900" id="gbp-value">Carregando...</div>
                <div class="text-sm text-gray-800">Libra Esterlina</div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-8 border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-area text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">Gráfico de Cotação</h4>
                <p class="text-gray-500" id="chart-message">Carregando gráfico...</p>
                <div class="mt-4 flex justify-center space-x-2">
                    <button class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium" data-range="7">7D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="30">30D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="90">90D</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm" data-range="365">1A</button>
                </div>
                {{-- Canvas para o gráfico. Adicionada uma altura máxima para controlar o tamanho. --}}
                <div style="max-height: 300px; margin-top: 1rem;">
                    <canvas id="currencyChart" class="hidden"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inclua o Font Awesome no seu layout principal ou aqui se for o único local --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
{{-- Inclua o Chart.js para o gráfico --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Variáveis globais para as moedas a serem monitoradas e a moeda local do usuário
    const targetCurrencies = ['USD', 'EUR', 'GBP'];
    let userLocalCurrency = 'BRL'; // Valor padrão, será atualizado pela API de geolocalização
    const chartBaseCurrency = 'USD'; // Moeda base para o gráfico, sempre USD
    let myChart; // Variável para a instância do Chart.js

    // Função para formatar a data e hora
    function formatDateTime(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    // Função para obter o IP do usuário
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

    // Função para obter a moeda local do usuário baseada no IP
    async function getUserLocalCurrency(ip) {
        if (!ip) return 'BRL'; // Retorna BRL como fallback se o IP não for encontrado

        try {
            const response = await fetch(`https://ipapi.co/${ip}/json/`);
            const data = await response.json();
            if (data && data.currency) {
                return data.currency;
            }
        } catch (error) {
            console.error('Erro ao obter a moeda local do usuário:', error);
        }
        return 'BRL'; // Retorna BRL como fallback
    }

    // Função para buscar e exibir as cotações de moeda para os cards
    async function getCurrencyRates() {
        document.getElementById('last-updated').textContent = 'Atualizando...';
        try {
            // 1. Obter IP do usuário e descobrir a moeda local
            const userIp = await getUserIP();
            userLocalCurrency = await getUserLocalCurrency(userIp);

            // Atualiza os códigos da moeda local nos cards
            document.querySelectorAll('.local-currency-code').forEach(element => {
                element.textContent = userLocalCurrency;
            });

            const currencyPromises = targetCurrencies.map(async (currency) => {
                const valueElement = document.getElementById(`${currency.toLowerCase()}-value`);
                const changeElement = document.getElementById(`${currency.toLowerCase()}-change`);

                if (currency === userLocalCurrency) {
                    if (valueElement) valueElement.textContent = 'N/A';
                    if (changeElement) {
                        changeElement.textContent = 'N/A';
                        changeElement.className = `text-sm font-semibold text-gray-700`;
                    }
                    return; // Pula a chamada da API se as moedas forem as mesmas
                }

                try {
                    // Usando Frankfurter API para taxas atuais
                    const response = await fetch(`https://api.frankfurter.app/latest?from=${currency}&to=${userLocalCurrency}`);
                    const data = await response.json();

                    if (data && data.rates && data.rates[userLocalCurrency]) {
                        const bid = data.rates[userLocalCurrency];

                        // Para a variação percentual, vamos buscar o valor de ontem para uma estimativa
                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        const yesterdayFormatted = yesterday.toISOString().split('T')[0];

                        const prevResponse = await fetch(`https://api.frankfurter.app/${yesterdayFormatted}?from=${currency}&to=${userLocalCurrency}`);
                        const prevData = await prevResponse.json();
                        let pctChange = 0;

                        if (prevData && prevData.rates && prevData.rates[userLocalCurrency]) {
                            const prevBid = prevData.rates[userLocalCurrency];
                            if (prevBid !== 0) {
                                pctChange = ((bid - prevBid) / prevBid) * 100;
                            }
                        }

                        if (valueElement) valueElement.textContent = `${userLocalCurrency} ${bid.toFixed(2).replace('.', ',')}`;
                        if (changeElement) {
                            changeElement.textContent = `${pctChange > 0 ? '+' : ''}${pctChange.toFixed(2)}%`;
                            changeElement.className = `text-sm font-semibold ${pctChange >= 0 ? 'text-green-700' : 'text-red-700'}`;
                        }
                    } else {
                        if (valueElement) valueElement.textContent = 'N/A';
                        if (changeElement) {
                            changeElement.textContent = 'N/A';
                            changeElement.className = `text-sm font-semibold text-gray-700`;
                        }
                    }
                } catch (error) {
                    console.error(`Erro ao buscar ${currency}/${userLocalCurrency}:`, error);
                    if (valueElement) valueElement.textContent = 'Erro';
                    if (changeElement) {
                        changeElement.textContent = 'Erro';
                        changeElement.className = `text-sm font-semibold text-gray-700`;
                    }
                }
            });

            document.getElementById('last-updated').textContent = formatDateTime(new Date());

        } catch (error) {
            console.error('Erro geral ao buscar cotações de moeda:', error);
            document.getElementById('last-updated').textContent = 'Erro ao carregar';
            targetCurrencies.forEach(currencyCode => {
                document.getElementById(`${currencyCode.toLowerCase()}-value`).textContent = 'Erro';
                document.getElementById(`${currencyCode.toLowerCase()}-change`).textContent = 'Erro';
            });
        }
    }

    // Função para buscar dados históricos para o gráfico (sempre USD vs userLocalCurrency)
    async function getHistoricalData(rangeDays) {
        if (chartBaseCurrency === userLocalCurrency) {
            return {
                labels: [],
                dataValues: [],
                message: "Comparação não aplicável: Dólar Americano e sua moeda local são as mesmas.",
            };
        }

        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - rangeDays);

        const startFormatted = startDate.toISOString().split('T')[0];
        const endFormatted = endDate.toISOString().split('T')[0];

        try {
            const response = await fetch(`https://api.frankfurter.app/${startFormatted}..${endFormatted}?from=${chartBaseCurrency}&to=${userLocalCurrency}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.rates || Object.keys(data.rates).length === 0) {
                return {
                    labels: [],
                    dataValues: [],
                    message: `Nenhum dado histórico disponível para ${chartBaseCurrency}/${userLocalCurrency}.`,
                };
            }

            const labels = Object.keys(data.rates).sort(); // Dates are keys, sort them
            const dataValues = labels.map(date => data.rates[date][userLocalCurrency]);

            return { labels, dataValues, message: null };

        } catch (error) {
            console.error('Erro ao buscar dados históricos:', error);
            return { labels: [], dataValues: [], message: `Erro ao buscar dados históricos para ${chartBaseCurrency}/${userLocalCurrency}.` };
        }
    }

    // Função para renderizar o gráfico
    async function renderChart(rangeDays = 7) {
        const chartMessageElement = document.getElementById('chart-message');
        const chartCanvas = document.getElementById('currencyChart');

        chartMessageElement.textContent = 'Carregando gráfico...';
        chartCanvas.classList.add('hidden'); // Esconde o canvas enquanto carrega

        const { labels, dataValues, message } = await getHistoricalData(rangeDays);

        if (myChart) {
            myChart.destroy(); // Destroi a instância anterior do gráfico se existir
        }

        if (message) {
            chartMessageElement.textContent = message;
            chartCanvas.classList.add('hidden');
            chartMessageElement.classList.remove('hidden');
        } else if (labels.length > 0 && dataValues.length > 0) {
            chartMessageElement.classList.add('hidden'); // Esconde a mensagem
            chartCanvas.classList.remove('hidden'); // Mostra o canvas

            const ctx = chartCanvas.getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `${chartBaseCurrency}/${userLocalCurrency} (últimos ${rangeDays} dias)`,
                        data: dataValues,
                        borderColor: 'rgb(59, 130, 246)', // Tailwind blue-500
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Importante para controlar o aspecto com CSS
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: 'rgb(107, 114, 128)' // Tailwind gray-500
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: 'rgb(107, 114, 128)'
                            },
                            grid: {
                                color: 'rgba(209, 213, 219, 0.3)' // Tailwind gray-300 com transparência
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
            chartMessageElement.textContent = `Dados históricos para ${chartBaseCurrency}/${userLocalCurrency} não disponíveis ou erro ao carregar o gráfico.`;
            chartCanvas.classList.add('hidden');
            chartMessageElement.classList.remove('hidden');
        }
    }

    // Event Listeners para os botões de range do gráfico
    document.querySelectorAll('[data-range]').forEach(button => {
        button.addEventListener('click', function() {
            // Remove a classe 'active' de todos os botões e adiciona ao clicado
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

    // Inicialização ao carregar a página
    document.addEventListener('DOMContentLoaded', async () => {
        // 1. Obter IP do usuário e descobrir a moeda local (para os cards e para o gráfico)
        await getCurrencyRates(); // Esta função já define userLocalCurrency

        // 2. Renderiza o gráfico com USD e a moeda local do usuário
        await renderChart(7);
    });

    // Opcional: Atualizar as cotações a cada 5 minutos (300000 ms)
    // setInterval(getCurrencyRates, 300000);
</script>