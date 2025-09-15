<div class="space-y-8">
    @include('components/myTrips/screenSections/themes/statisticSection')
    
    <!-- Seção de Clima -->
    @include('components/myTrips/screenSections/themes/wetherSection', [
        'viagem' => $viagem,
        'usuario' => $usuario ?? null
    ])
    
    <!-- Seção de Notícias -->
    @include('components/myTrips/screenSections/themes/newsSection', [
        'viagem' => $viagem,
        'eventos' => $eventos ?? collect()
    ])
</div>

<script>
    let isWeatherLoading = false;
    let isNewsLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    // Carregar dados apenas quando a aba de estatísticas estiver ativa
    const statsTab = document.getElementById('tab-informacoes-estatisticas');
    if (statsTab) {
        statsTab.addEventListener('click', function() {
            setTimeout(() => {
                const tripId = window.currentTripId;
                if (tripId) {
                    loadWeatherDataStats(tripId);
                    loadNewsDataStats(tripId);
                }
            }, 100);
        });
    }
});

// Função para carregar dados do clima 
async function loadWeatherDataStats(tripId) {
    if (isWeatherLoading) return;
    isWeatherLoading = true;
    setUIState('weather', 'loading');

    try {
        const response = await fetch(`/viagens/${tripId}/weather`); //
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

// Função para carregar notícias 
async function loadNewsDataStats(tripId) {
    if (isNewsLoading) return;
    isNewsLoading = true;
    setUIState('news', 'loading'); 

    try {
        const response = await fetch(`/viagens/${tripId}/news`); //
        const data = await response.json();

        if (data.success && data.data) {
            displayNewsDataStatsEnhanced(data.data);
            setUIState('news', 'content'); 
        } else {
            setUIState('news', 'error'); 
        }
    } catch (error) {
        console.error('Erro ao carregar notícias:', error); //
        setUIState('news', 'error'); 
    } finally {
        isNewsLoading = false;
    }
}

// Função APRIMORADA para exibir dados do clima
function displayWeatherDataStatsEnhanced(weatherData) {
    const skeleton = document.getElementById('weather-skeleton-stats');
    const content = document.getElementById('weather-content-stats');
    
    if (!skeleton || !content) return;
    
    let weatherHtml = '';
    
    // Clima atual em destaque
    if (weatherData.daily && weatherData.daily.time && weatherData.daily.time.length > 0) {
        const today = weatherData.daily;
        const todayTemp = Math.round(today.temperature_2m_max[0] || 25);
        const todayMin = Math.round(today.temperature_2m_min[0] || 18);
        const humidity = today.relative_humidity_2m ? Math.round(today.relative_humidity_2m[0]) : 65;
        const windSpeed = today.wind_speed_10m_max ? Math.round(today.wind_speed_10m_max[0]) : 10;
        const precipitation = today.precipitation_sum ? Math.round(today.precipitation_sum[0]) : 0;
        const uvIndex = today.uv_index_max ? Math.round(today.uv_index_max[0]) : 5;
        
        weatherHtml += `
            <div class="bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-2xl p-6 mb-6 border border-blue-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-900 mb-1">Hoje</h3>
                        <p class="text-blue-700">${new Date(today.time[0]).toLocaleDateString('pt-BR', { 
                            weekday: 'long', 
                            day: 'numeric', 
                            month: 'long' 
                        })}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center space-x-3">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-sun text-blue-800 text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-4xl font-bold text-blue-900">${todayTemp}°</div>
                                <div class="text-sm text-blue-600">Mín: ${todayMin}°</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detalhes expandidos -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white/60 rounded-xl p-3 text-center backdrop-blur-sm">
                        <i class="fas fa-tint text-blue-800 text-lg mb-2"></i>
                        <div class="text-xs text-blue-700 mb-1">Umidade</div>
                        <div class="font-bold text-blue-900">${humidity}%</div>
                    </div>
                    <div class="bg-white/60 rounded-xl p-3 text-center backdrop-blur-sm">
                        <i class="fas fa-wind text-blue-800 text-lg mb-2"></i>
                        <div class="text-xs text-blue-700 mb-1">Vento</div>
                        <div class="font-bold text-blue-900">${windSpeed} km/h</div>
                    </div>
                    <div class="bg-white/60 rounded-xl p-3 text-center backdrop-blur-sm">
                        <i class="fas fa-cloud-rain text-blue-800 text-lg mb-2"></i>
                        <div class="text-xs text-blue-700 mb-1">Chuva</div>
                        <div class="font-bold text-blue-900">${precipitation} mm</div>
                    </div>
                    <div class="bg-white/60 rounded-xl p-3 text-center backdrop-blur-sm">
                        <i class="fas fa-sun text-yellow-800 text-lg mb-2"></i>
                        <div class="text-xs text-blue-700 mb-1">UV</div>
                        <div class="font-bold text-blue-900">${uvIndex}</div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Previsão para os próximos dias
    weatherHtml += '<div class="mb-6"><h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center"><i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Próximos 7 dias</h4></div>';
    weatherHtml += '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">';
    
    if (weatherData.daily && weatherData.daily.time) {
        for (let i = 0; i < Math.min(7, weatherData.daily.time.length); i++) {
            const date = new Date(weatherData.daily.time[i]);
            const dayName = date.toLocaleDateString('pt-BR', { weekday: 'short' });
            const dayNum = date.getDate();
            const maxTemp = Math.round(weatherData.daily.temperature_2m_max[i] || 25);
            const minTemp = Math.round(weatherData.daily.temperature_2m_min[i] || 18);
            const precipitation = weatherData.daily.precipitation_probability_max ? 
                Math.round(weatherData.daily.precipitation_probability_max[i]) : 0;
            const windSpeed = weatherData.daily.wind_speed_10m_max ? 
                Math.round(weatherData.daily.wind_speed_10m_max[i]) : 10;
            
            // Determinar ícone baseado nas condições
            let weatherIcon = 'fa-sun';
            let iconColor = 'text-yellow-500';
            
            if (precipitation > 60) {
                weatherIcon = 'fa-cloud-rain';
                iconColor = 'text-blue-500';
            } else if (precipitation > 30) {
                weatherIcon = 'fa-cloud-sun';
                iconColor = 'text-gray-500';
            }
            
            weatherHtml += `
                <div class="bg-gradient-to-b from-white to-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="text-center">
                        <!-- Data -->
                        <div class="text-sm font-medium text-gray-700 mb-1">${dayName}</div>
                        <div class="text-xs text-gray-500 mb-3">${dayNum}</div>
                        
                        <!-- Ícone do clima -->
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                            <i class="fas ${weatherIcon} ${iconColor} text-lg"></i>
                        </div>
                        
                        <!-- Temperaturas -->
                        <div class="font-bold text-gray-800 mb-1">${maxTemp}°</div>
                        <div class="text-sm text-gray-500 mb-3">${minTemp}°</div>
                        
                        <!-- Detalhes extras -->
                        <div class="space-y-1 text-xs text-gray-600">
                            <div class="flex items-center justify-center space-x-1">
                                <i class="fas fa-tint text-blue-400"></i>
                                <span>${precipitation}%</span>
                            </div>
                            <div class="flex items-center justify-center space-x-1">
                                <i class="fas fa-wind text-gray-400"></i>
                                <span>${windSpeed}km/h</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    weatherHtml += '</div>';
    
    // Dicas baseadas no clima
    weatherHtml += `
        <div class="mt-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
            <h5 class="font-semibold text-green-800 mb-2 flex items-center">
                <i class="fas fa-lightbulb mr-2 text-green-600"></i>
                Dicas para sua viagem
            </h5>
            <div class="text-sm text-green-700 space-y-1">
                <p>• Leve roupas leves para os dias mais quentes</p>
                <p>• Não esqueça do protetor solar e chapéu</p>
                <p>• Mantenha-se hidratado durante os passeios</p>
                ${weatherData.daily && weatherData.daily.precipitation_sum && 
                  weatherData.daily.precipitation_sum.some(p => p > 5) ? 
                  '<p>• Leve um guarda-chuva, há previsão de chuva</p>' : ''}
            </div>
        </div>
    `;
    
    content.innerHTML = weatherHtml;
    skeleton.classList.add('hidden');
    content.classList.remove('hidden');
}

// Função APRIMORADA para exibir notícias
function displayNewsDataStatsEnhanced(newsData) {
    const skeleton = document.getElementById('news-skeleton-stats');
    const content = document.getElementById('news-content-stats');
    
    if (!skeleton || !content) return;
    
    let newsHtml = '';
    
    if (newsData && newsData.length > 0) {
        // Notícia em destaque (primeira)
        const featuredNews = newsData[0];
        newsHtml += `
            <div class="bg-gradient-to-r from-red-50 via-orange-50 to-red-50 rounded-2xl p-6 mb-6 border border-red-200">
                <div class="flex items-start space-x-6">
                    ${featuredNews.thumbnail ? `
                        <div class="flex-shrink-0">
                            <img src="${featuredNews.thumbnail}" alt="Imagem da notícia" 
                                 class="w-32 h-24 object-cover rounded-lg shadow-md">
                        </div>
                    ` : `
                        <div class="w-32 h-24 bg-gradient-to-br from-red-200 to-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-newspaper text-red-600 text-2xl"></i>
                        </div>
                    `}
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">EM DESTAQUE</span>
                            ${featuredNews.category ? `
                                <span class="bg-red-100 text-red-700 text-xs font-medium px-2 py-1 rounded-full">${featuredNews.category}</span>
                            ` : ''}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">${featuredNews.title || 'Sem título'}</h3>
                        <p class="text-gray-700 mb-4 line-clamp-3">${featuredNews.snippet || 'Sem descrição'}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-1 text-red-600"></i>
                                    ${featuredNews.date || 'Data não informada'}
                                </span>
                                ${featuredNews.source ? `
                                    <span class="flex items-center">
                                        <i class="fas fa-globe mr-1 text-red-600"></i>
                                        ${featuredNews.source}
                                    </span>
                                ` : ''}
                            </div>
                            <a href="${featuredNews.link || '#'}" target="_blank" 
                               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                Ler mais <i class="fas fa-external-link-alt ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Outras notícias em grid
        if (newsData.length > 1) {
            newsHtml += `
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list-ul mr-2 text-red-600"></i>
                        Outras Notícias
                    </h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            `;
            
            newsData.slice(1, 7).forEach((article, index) => {
                // Cores alternadas para variedade visual
                const colors = [
                    'from-blue-500 to-blue-600 border-blue-200',
                    'from-green-500 to-green-600 border-green-200',
                    'from-purple-500 to-purple-600 border-purple-200',
                    'from-yellow-500 to-yellow-600 border-yellow-200',
                    'from-pink-500 to-pink-600 border-pink-200',
                    'from-indigo-500 to-indigo-600 border-indigo-200'
                ];
                const colorClass = colors[index % colors.length];
                
                newsHtml += `
                    <div class="bg-gradient-to-br ${colorClass} border rounded-xl p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            ${article.thumbnail ? `
                                <img src="${article.thumbnail}" alt="Imagem da notícia" 
                                     class="w-20 h-16 object-cover rounded-lg flex-shrink-0 shadow-sm">
                            ` : `
                                <div class="w-20 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            `}
                            <div class="flex-1 min-w-0">
                                ${article.category ? `
                                    <span class="bg-white/70 text-gray-700 text-xs font-medium px-2 py-1 rounded-full mb-2 inline-block">${article.category}</span>
                                ` : ''}
                                <h5 class="font-semibold text-gray-900 mb-2 line-clamp-2 leading-tight">${article.title || 'Sem título'}</h5>
                                <p class="text-gray-700 text-sm mb-3 line-clamp-2">${article.snippet || 'Sem descrição'}</p>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-600 flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        ${article.date || 'Hoje'}
                                    </div>
                                    <a href="${article.link || '#'}" target="_blank" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                                        Ver <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            newsHtml += '</div>';
        }
        
        // Link para ver mais notícias
        newsHtml += `
            <div class="mt-6 text-center">
                <a href="https://news.google.com/search?q=notícias+turismo" 
                   target="_blank" 
                   class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Ver mais notícias no Google News
                </a>
            </div>
        `;
        
    } else {
        // Estado vazio melhorado
        newsHtml = `
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gradient-to-br from-red-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-newspaper text-red-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Nenhuma notícia disponível</h3>
                <p class="text-gray-600 mb-6">Não encontramos notícias recentes para este destino no momento.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="loadNewsDataStats(window.currentTripId)" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>Tentar novamente
                    </button>
                    <a href="https://news.google.com" target="_blank" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-external-link-alt mr-2"></i>Ver Google News
                    </a>
                </div>
            </div>
        `;
    }
    
    content.innerHTML = newsHtml;
    skeleton.classList.add('hidden');
    content.classList.remove('hidden');
}

function setUIState(section, state) { // 'section' pode ser 'weather' ou 'news'. 'state' pode ser 'loading', 'content' ou 'error'
    const skeleton = document.getElementById(`${section}-skeleton-stats`);
    const content = document.getElementById(`${section}-content-stats`);
    const error = document.getElementById(`${section}-error-stats`);

    if (!skeleton || !content || !error) return;

    skeleton.classList.toggle('hidden', state !== 'loading');
    content.classList.toggle('hidden', state !== 'content');
    error.classList.toggle('hidden', state !== 'error');
}
</script>

<style>
    /* Animações suaves para hover */
    .hover\:shadow-md:hover {
        transition: box-shadow 0.3s ease-in-out;
    }
    
    /* Gradientes personalizados */
    .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }
    
    /* Efeitos de transição */
    .transition-shadow {
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .transition-colors {
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
</style>
