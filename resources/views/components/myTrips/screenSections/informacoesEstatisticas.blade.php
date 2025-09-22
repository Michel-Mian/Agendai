<div class="space-y-8">
    @include('components/myTrips/screenSections/themes/statisticSection', ['viagem' => $viagem])
    
    @include('components/myTrips/screenSections/themes/wetherSection', [
        'viagem' => $viagem,
        'usuario' => $usuario ?? null
    ])
    
    @include('components/myTrips/screenSections/themes/newsSection', [
        'viagem' => $viagem,
        'eventos' => $eventos ?? collect()
    ])
</div>

<script>
// Variáveis de estado para evitar chamadas duplas
let isWeatherLoading = false;
let isNewsLoading = false;
let isCurrencyLoading = false;
let areStatsInitialized = false;

// Função para buscar e exibir os dados para o destino selecionado
function initializeStatsForCurrentTab() {
    if (areStatsInitialized) return; // Executa apenas uma vez por carregamento de página
    
    const tripId = window.currentTripId;
    if (!tripId) return;

    // Pega o valor inicial de cada dropdown
    const initialWeatherDestId = document.getElementById('weather-destination-select')?.value;
    if (initialWeatherDestId) loadWeatherDataStats(tripId, initialWeatherDestId);

    const initialNewsDestId = document.getElementById('news-destination-select')?.value;
    if (initialNewsDestId) loadNewsDataStats(tripId, initialNewsDestId);
    
    const currencySelect = document.getElementById('currency-destination-select');
    if (currencySelect && currencySelect.options.length > 0) {
        const selectedOption = currencySelect.options[currencySelect.selectedIndex];
        const destinationName = selectedOption?.dataset.destinationName;
        if(destinationName) updateCurrencyDataForDestination(destinationName);
    }
    
    areStatsInitialized = true; // Marca como inicializado
}

document.addEventListener('DOMContentLoaded', function() {
    const statsTab = document.getElementById('tab-informacoes-estatisticas');
    if (statsTab) {
        statsTab.addEventListener('click', initializeStatsForCurrentTab, { once: true });
    }

    // Adiciona event listeners para os dropdowns
    document.getElementById('weather-destination-select')?.addEventListener('change', function() {
        loadWeatherDataStats(window.currentTripId, this.value);
    });

    document.getElementById('news-destination-select')?.addEventListener('change', function() {
        loadNewsDataStats(window.currentTripId, this.value);
    });

    document.getElementById('currency-destination-select')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const destinationName = selectedOption.dataset.destinationName;
        updateCurrencyDataForDestination(destinationName);
    });
});

// =============================================================
// FUNÇÕES DE LÓGICA E RENDERIZAÇÃO APRIMORADAS
// =============================================================

async function loadWeatherDataStats(tripId, destinoId) {
    if (isWeatherLoading) return;
    isWeatherLoading = true;
    setUIState('weather', 'loading');

    try {
        const response = await fetch(`/viagens/${tripId}/weather/${destinoId}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        if (data.success && data.data) {
            displayWeatherDataStatsEnhanced(data.data);
            setUIState('weather', 'content');
        } else {
            setUIState('weather', 'error'); 
        }
    } catch (error) {
        console.error('Erro ao carregar clima:', error);
        setUIState('weather', 'error');
    } finally {
        isWeatherLoading = false;
    }
}

async function loadNewsDataStats(tripId, destinoId) {
    if (isNewsLoading) return;
    isNewsLoading = true;
    setUIState('news', 'loading'); 

    try {
        const response = await fetch(`/viagens/${tripId}/news/${destinoId}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        if (data.success && data.data) {
            displayNewsDataStatsEnhanced(data.data);
            setUIState('news', 'content'); 
        } else {
            setUIState('news', 'error'); 
        }
    } catch (error) {
        console.error('Erro ao carregar notícias:', error);
        setUIState('news', 'error'); 
    } finally {
        isNewsLoading = false;
    }
}

async function updateCurrencyDataForDestination(destinationName) {
    if (isCurrencyLoading || !destinationName) return;
    isCurrencyLoading = true;

    const currencyTextElement = document.getElementById('destination-currency-text');
    if (currencyTextElement) {
        currencyTextElement.textContent = `Moeda para ${destinationName}`;
    }
    
    if (typeof getCurrencyRates === 'function' && typeof renderChart === 'function') {
        await getCurrencyRates(destinationName);
        await renderChart(7);
    } else {
        console.error("Funções de Cotação de Moeda não encontradas.");
    }
    isCurrencyLoading = false;
}

function setUIState(section, state) {
    const skeleton = document.getElementById(`${section}-skeleton-stats`);
    const content = document.getElementById(`${section}-content-stats`);
    const error = document.getElementById(`${section}-error-stats`);

    if (!skeleton || !content || !error) return;

    skeleton.classList.toggle('hidden', state !== 'loading');
    content.classList.toggle('hidden', state !== 'content');
    error.classList.toggle('hidden', state !== 'error');
}

/**
 * NOVA FUNÇÃO: Traduz o código WMO da API para um ícone e cor do Font Awesome.
 * @param {number} wmoCode - O código do tempo (ex: 0, 3, 61, 80).
 * @returns {object} - Um objeto com as classes do ícone e da cor.
 */
function getWeatherIcon(wmoCode) {
    const icons = {
        // Ícone padrão
        default: { icon: 'fa-question-circle', color: 'text-gray-400' },
        // Céu Limpo
        0: { icon: 'fa-sun', color: 'text-yellow-500' },
        // Principalmente limpo, parcialmente nublado
        1: { icon: 'fa-cloud-sun', color: 'text-gray-500' },
        2: { icon: 'fa-cloud-sun', color: 'text-gray-500' },
        3: { icon: 'fa-cloud', color: 'text-gray-400' },
        // Névoa
        45: { icon: 'fa-smog', color: 'text-gray-400' },
        48: { icon: 'fa-smog', color: 'text-gray-400' },
        // Chuvisco
        51: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        53: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        55: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        // Chuva
        61: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        63: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        65: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        // Neve
        71: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        73: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        75: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        // Pancadas de chuva
        80: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        81: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        82: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        // Neve pesada
        85: { icon: 'fa-snowflake', color: 'text-cyan-300' },
        86: { icon: 'fa-snowflake', color: 'text-cyan-300' },
        // Trovoada
        95: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
        96: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
        99: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
    };
    return icons[wmoCode] || icons.default;
}

// FUNÇÃO DE RENDERIZAÇÃO DO CLIMA (ATUALIZADA)
function displayWeatherDataStatsEnhanced(weatherData) {
    const content = document.getElementById('weather-content-stats');
    if (!content) return;
    
    let weatherHtml = '';
    
    // Acessa os dados de forma segura com optional chaining (?.) e define valores padrão com (??)
    const daily = weatherData?.daily;
    if (daily && daily.time?.length > 0) {
        const todayTemp = Math.round(daily.temperature_2m_max?.[0] ?? 0);
        const todayMin = Math.round(daily.temperature_2m_min?.[0] ?? 0);
        const todayWmoCode = daily.weather_code?.[0] ?? 0;
        const iconInfoToday = getWeatherIcon(todayWmoCode);

        weatherHtml += `
            <div class="bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-2xl p-6 mb-6 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-900 mb-1">Hoje</h3>
                        <p class="text-blue-700">${new Date(daily.time[0]).toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' })}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center space-x-3">
                            <div class="w-16 h-16 bg-white/50 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas ${iconInfoToday.icon} ${iconInfoToday.color} text-3xl"></i>
                            </div>
                            <div>
                                <div class="text-4xl font-bold text-blue-900">${todayTemp}°</div>
                                <div class="text-sm text-blue-600">Mín: ${todayMin}°</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    
        weatherHtml += '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">';
        for (let i = 0; i < Math.min(7, daily.time.length); i++) {
            const date = new Date(daily.time[i]);
            const dayName = date.toLocaleDateString('pt-BR', { weekday: 'short' });
            const dayNum = date.getDate();
            const maxTemp = Math.round(daily.temperature_2m_max?.[i] ?? 0);
            const minTemp = Math.round(daily.temperature_2m_min?.[i] ?? 0);
            const wmoCode = daily.weather_code?.[i] ?? 0;
            const iconInfoDay = getWeatherIcon(wmoCode);

            weatherHtml += `
                <div class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="text-sm font-medium text-gray-700 mb-1">${dayName}</div>
                    <div class="text-xs text-gray-500 mb-3">${dayNum}</div>
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas ${iconInfoDay.icon} ${iconInfoDay.color} text-xl"></i>
                    </div>
                    <div class="font-bold text-gray-800">${maxTemp}°</div>
                    <div class="text-sm text-gray-500">${minTemp}°</div>
                </div>`;
        }
        weatherHtml += '</div>';
    } else {
        // Mensagem de fallback caso não venham dados
        weatherHtml = `<div class="text-center py-12"><p class="text-gray-600">Dados de clima indisponíveis para este destino.</p></div>`;
    }
    content.innerHTML = weatherHtml;
}

// FUNÇÃO DE RENDERIZAÇÃO DE NOTÍCIAS (ATUALIZADA)
function displayNewsDataStatsEnhanced(newsData) {
    const content = document.getElementById('news-content-stats');
    if (!content) return;
    
    let newsHtml = '';
    
    if (newsData && newsData.length > 0) {
        // Notícia em destaque (primeira) com fallbacks
        const featuredNews = newsData[0];
        const featuredTitle = featuredNews.title || 'Título indisponível';
        const featuredSnippet = featuredNews.snippet || 'Descrição não fornecida.';
        const featuredLink = featuredNews.link || '#';
        
        newsHtml += `
            <div class="bg-red-50 rounded-2xl p-6 mb-6 border border-red-200">
                <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 md:space-x-6">
                    ${featuredNews.thumbnail ? 
                        `<img src="${featuredNews.thumbnail}" alt="Imagem da notícia" class="w-full md:w-48 h-32 object-cover rounded-lg shadow-md">` :
                        `<div class="w-full md:w-48 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 flex-shrink-0"><i class="fas fa-image text-2xl"></i></div>`
                    }
                    <div class="flex-1">
                        <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">DESTAQUE</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-2 line-clamp-2">${featuredTitle}</h3>
                        <p class="text-gray-700 mb-4 line-clamp-2">${featuredSnippet}</p>
                        <a href="${featuredLink}" target="_blank" rel="noopener noreferrer" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Ler mais</a>
                    </div>
                </div>
            </div>`;
        
        // Outras notícias em grid
        if (newsData.length > 1) {
            newsHtml += '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
            newsData.slice(1, 7).forEach(article => {
                const articleTitle = article.title || 'Título indisponível';
                const articleLink = article.link || '#';
                
                newsHtml += `
                    <div class="bg-white border border-gray-100 rounded-xl p-4 hover:shadow-lg transition-shadow">
                        <div class="flex items-start space-x-4">
                            ${article.thumbnail ? 
                                `<img src="${article.thumbnail}" alt="Imagem da notícia" class="w-24 h-16 object-cover rounded-lg flex-shrink-0">` :
                                `<div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 flex-shrink-0"><i class="fas fa-image"></i></div>`
                            }
                            <div class="flex-1 min-w-0">
                                <h5 class="font-semibold text-gray-800 mb-1 line-clamp-2">${articleTitle}</h5>
                                <a href="${articleLink}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-red-600 hover:text-red-800">Ver <i class="fas fa-chevron-right ml-1 text-xs"></i></a>
                            </div>
                        </div>
                    </div>`;
            });
            newsHtml += '</div>';
        }
    } else {
        // Mensagem de fallback caso não venham notícias
        newsHtml = `<div class="text-center py-12"><p class="text-gray-600">Nenhuma notícia encontrada para este destino.</p></div>`;
    }
    
    content.innerHTML = newsHtml;
}

// Adaptação para garantir que as funções de moeda estejam disponíveis globalmente
document.addEventListener('DOMContentLoaded', () => {
    if (typeof getCurrencyRates === 'function') window.getCurrencyRates = getCurrencyRates;
    if (typeof renderChart === 'function') window.renderChart = renderChart;
});
</script>

<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
</style>