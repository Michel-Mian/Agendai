<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/30 rounded-lg p-2">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-yellow-500">Cotação de Moeda</h3>
                    <p class="text-yellow-400 text-sm font-medium" id="destination-currency-text">Selecione um destino</p>
                </div>
            </div>
            @if(isset($viagem->destinos) && $viagem->destinos->count() > 0)
            <div class="w-1/3">
                <select id="currency-destination-select" data-trip-id="{{ $viagem->pk_id_viagem }}" class="block w-full bg-white/20 text-white border-white/30 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-300">
                    @foreach($viagem->destinos as $destino)
                        <option value="{{ $destino->pk_id_destino }}" data-destination-name="{{ $destino->nome_destino }}">{{ $destino->nome_destino }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" id="currency-cards-container">
            {{-- Skeleton para os cards --}}
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>
        </div>

        <div class="bg-gray-50 rounded-lg p-8 border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-area text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">Gráfico de Cotação: <span id="chart-currency-title"></span></h4>
                <p class="text-gray-500" id="chart-message">Selecione um destino para ver os dados.</p>
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
    // Início da Lógica de Cotação de Moeda Adaptada
    let userLocalCurrency = 'BRL';
    let destinationCurrencyCode = 'USD';
    let myChart;
    const GOOGLE_API_KEY = "{{ config('services.google_maps_api_key') }}"; // Use a chave do seu .env

    async function translateText(text, targetLanguage) {
        if (!GOOGLE_API_KEY) return null;
        try {
            const response = await fetch(`https://translation.googleapis.com/language/translate/v2?key=${GOOGLE_API_KEY}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ q: text, target: targetLanguage, format: 'text' }),
            });
            if (!response.ok) return null;
            const data = await response.json();
            return data?.data?.translations?.[0]?.translatedText || null;
        } catch (error) {
            console.error('Erro na API de Tradução:', error);
            return null;
        }
    }

    async function getDestinationCurrency(destinationName) {
        const translatedCountryName = await translateText(destinationName.split(',').pop().trim(), 'en');
        const countryToSearch = translatedCountryName || destinationName;
        try {
            const response = await fetch(`https://restcountries.com/v3.1/name/${countryToSearch}?fullText=true`);
            if (!response.ok) { // Tenta busca parcial se a busca exata falhar
                const partialResponse = await fetch(`https://restcountries.com/v3.1/name/${countryToSearch}`);
                if (!partialResponse.ok) return null;
                const data = await partialResponse.json();
                return data[0] ? Object.keys(data[0].currencies)[0] : null;
            }
            const data = await response.json();
            return data[0] ? Object.keys(data[0].currencies)[0] : null;
        } catch (error) {
            console.warn(`Erro ao buscar moeda para '${countryToSearch}'`);
            return null;
        }
    }

    // ADAPTADO: A função agora aceita `destinationName` e se torna global
    window.getCurrencyRates = async function(destinationName) {
        const currencyCardsContainer = document.getElementById('currency-cards-container');
        currencyCardsContainer.innerHTML = `
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>
            <div class="animate-pulse bg-gray-200 rounded-lg p-4 h-28"></div>`;

        try {
            userLocalCurrency = 'BRL'; // Simplificado, pode ser expandido com IP API se desejar
            const fetchedDestinationCurrency = await getDestinationCurrency(destinationName);
            destinationCurrencyCode = fetchedDestinationCurrency || 'USD'; // Fallback para USD

            const currenciesToCompare = [...new Set(['USD', 'EUR', destinationCurrencyCode])];

            const fetchPromises = currenciesToCompare.map(async (fromCurrency) => {
                if (fromCurrency === userLocalCurrency) return null;
                try {
                    const response = await fetch(`https://api.frankfurter.app/latest?from=${fromCurrency}&to=${userLocalCurrency}`);
                    if (!response.ok) return { from: fromCurrency, rate: 'N/A' };
                    const data = await response.json();
                    return { from: fromCurrency, rate: data.rates?.[userLocalCurrency] ?? 'N/A' };
                } catch {
                    return { from: fromCurrency, rate: 'Erro' };
                }
            });

            const results = (await Promise.all(fetchPromises)).filter(Boolean);
            currencyCardsContainer.innerHTML = ''; // Limpa os skeletons

            results.forEach(result => {
                let rateDisplay = 'N/A';
                if (typeof result.rate === 'number') {
                    rateDisplay = `${userLocalCurrency} ${result.rate.toFixed(2).replace('.', ',')}`;
                } else {
                    rateDisplay = result.rate;
                }

                let cardSubtitle = '';
                if (result.from === 'USD') cardSubtitle = 'Dólar Americano';
                else if (result.from === 'EUR') cardSubtitle = 'Euro';
                else if (result.from === destinationCurrencyCode) cardSubtitle = 'Moeda do Destino';
                else cardSubtitle = `${result.from} Cotação`;

                currencyCardsContainer.innerHTML += `
                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-blue-900">${result.from}/${userLocalCurrency}</span>
                        </div>
                        <div class="text-2xl font-extrabold text-gray-900">${rateDisplay}</div>
                        <div class="text-sm text-gray-800">${cardSubtitle}</div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Erro ao buscar cotações:', error);
            currencyCardsContainer.innerHTML = `<p class="text-red-600 col-span-3">Erro ao carregar dados de moeda.</p>`;
        }
    }

    async function getHistoricalData(rangeDays) {
        if (destinationCurrencyCode === userLocalCurrency) {
            return { message: `Moeda de destino e local são as mesmas (${userLocalCurrency}).` };
        }
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - rangeDays);
        const startFormatted = startDate.toISOString().split('T')[0];

        try {
            const response = await fetch(`https://api.frankfurter.app/${startFormatted}..${new Date().toISOString().split('T')[0]}?from=${destinationCurrencyCode}&to=${userLocalCurrency}`);
            if (!response.ok) return { message: `Dados históricos para ${destinationCurrencyCode} indisponíveis.`};
            const data = await response.json();
            const rates = data.rates;
            if (!rates || Object.keys(rates).length === 0) return { message: 'Nenhum dado histórico encontrado.' };
            
            const labels = Object.keys(rates).sort();
            const dataValues = labels.map(date => rates[date][userLocalCurrency]);
            return { labels, dataValues };
        } catch (error) {
            return { message: 'Erro ao buscar dados do gráfico.' };
        }
    }
    
    // ADAPTADO: A função agora se torna global
    window.renderChart = async function(rangeDays = 7) {
        const chartMessage = document.getElementById('chart-message');
        const chartCanvas = document.getElementById('currencyChart');
        document.getElementById('chart-currency-title').textContent = `(${destinationCurrencyCode}/${userLocalCurrency})`;
        chartMessage.textContent = 'Carregando gráfico...';
        chartCanvas.classList.add('hidden');
        if (myChart) myChart.destroy();

        const { labels, dataValues, message } = await getHistoricalData(rangeDays);

        if (message) {
            chartMessage.textContent = message;
        } else {
            chartMessage.classList.add('hidden');
            chartCanvas.classList.remove('hidden');
            myChart = new Chart(chartCanvas.getContext('2d'), {
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
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    document.querySelectorAll('[data-range]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-range]').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-700');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.add('bg-blue-100', 'text-blue-700');
            renderChart(parseInt(this.dataset.range));
        });
    });
</script>