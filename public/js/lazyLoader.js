/**
 * Gerenciador de Carregamento Lazy para Detalhes da Viagem
 */
class LazyLoader {
    constructor(tripId) {
        this.tripId = tripId;
        this.loadingStates = {
            weather: false,
            news: false
        };
        this.init();
    }

    init() {
        // Carregar dados após DOM estar pronto e com delay para priorizar conteúdo crítico
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                this.loadWeatherData();
                this.loadNewsData();
            }, 800); // Delay de 800ms para priorizar conteúdo principal
        });
    }

    async loadWeatherData() {
        if (this.loadingStates.weather) return;
        this.loadingStates.weather = true;

        try {
            const response = await fetch(`/viagens/${this.tripId}/weather`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();

            if (data.success && data.data) {
                this.renderWeatherData(data.data);
                this.showContent('weather');
            } else {
                throw new Error('Invalid weather data');
            }

        } catch (error) {
            console.error('Erro ao carregar clima:', error);
            this.showError('weather');
        } finally {
            this.loadingStates.weather = false;
        }
    }

    async loadNewsData() {
        if (this.loadingStates.news) return;
        this.loadingStates.news = true;

        try {
            const response = await fetch(`/viagens/${this.tripId}/news`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();

            if (data.success && Array.isArray(data.data)) {
                this.renderNewsData(data.data);
                this.showContent('news');
            } else {
                throw new Error('Invalid news data');
            }

        } catch (error) {
            console.error('Erro ao carregar notícias:', error);
            this.showError('news');
        } finally {
            this.loadingStates.news = false;
        }
    }

    renderWeatherData(weatherData) {
        const container = document.getElementById('weather-content');
        if (!container || !weatherData.daily) return;

        const weatherIcons = {
            0: 'fas fa-sun',
            1: 'fas fa-cloud-sun',
            2: 'fas fa-cloud',
            3: 'fas fa-cloud',
            45: 'fas fa-smog',
            48: 'fas fa-smog',
            51: 'fas fa-cloud-drizzle',
            53: 'fas fa-cloud-drizzle',
            55: 'fas fa-cloud-rain',
            61: 'fas fa-cloud-rain',
            63: 'fas fa-cloud-rain',
            65: 'fas fa-cloud-showers-heavy'
        };

        let html = '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">';

        for (let i = 0; i < Math.min(7, weatherData.daily.time.length); i++) {
            const date = new Date(weatherData.daily.time[i]);
            const dayName = date.toLocaleDateString('pt-BR', { weekday: 'short' });
            const maxTemp = Math.round(weatherData.daily.temperature_2m_max[i]);
            const minTemp = Math.round(weatherData.daily.temperature_2m_min[i]);
            const weatherCode = weatherData.daily.weathercode[i];
            const icon = weatherIcons[weatherCode] || 'fas fa-sun';

            html += `
                <div class="text-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                    <div class="font-semibold text-gray-800 text-sm mb-2">${dayName}</div>
                    <div class="text-blue-600 text-2xl mb-2">
                        <i class="${icon}"></i>
                    </div>
                    <div class="font-bold text-lg text-gray-800">${maxTemp}°</div>
                    <div class="text-gray-600 text-sm">${minTemp}°</div>
                </div>
            `;
        }

        html += '</div>';
        html += '<div class="text-center mt-4 text-gray-600 text-sm">Previsão dos próximos 7 dias</div>';

        container.innerHTML = html;
    }

    renderNewsData(newsData) {
        const container = document.getElementById('news-content');
        if (!container || !Array.isArray(newsData)) return;

        if (newsData.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500">Nenhuma notícia encontrada</div>';
            return;
        }

        let html = '<div class="space-y-4">';

        newsData.slice(0, 6).forEach((article, index) => {
            const title = article.title || 'Título não disponível';
            const snippet = article.snippet || article.summary || 'Descrição não disponível';
            const link = article.link || '#';
            const source = article.source?.name || 'Fonte não identificada';
            
            html += `
                <div class="border-b border-gray-100 pb-4 hover:bg-gray-50 p-3 rounded-lg transition-colors">
                    <a href="${link}" target="_blank" class="block group">
                        <h3 class="font-semibold text-gray-800 group-hover:text-red-600 transition-colors mb-2 line-clamp-2">
                            ${title}
                        </h3>
                        <p class="text-gray-600 text-sm mb-2 line-clamp-3">
                            ${snippet}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">${source}</span>
                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-red-500"></i>
                        </div>
                    </a>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    showContent(section) {
        const skeleton = document.getElementById(`${section}-skeleton`);
        const content = document.getElementById(`${section}-content`);
        const error = document.getElementById(`${section}-error`);

        if (skeleton) skeleton.classList.add('hidden');
        if (error) error.classList.add('hidden');
        if (content) {
            content.classList.remove('hidden');
            content.style.opacity = '0';
            setTimeout(() => {
                content.style.transition = 'opacity 0.3s ease-in';
                content.style.opacity = '1';
            }, 50);
        }
    }

    showError(section) {
        const skeleton = document.getElementById(`${section}-skeleton`);
        const content = document.getElementById(`${section}-content`);
        const error = document.getElementById(`${section}-error`);

        if (skeleton) skeleton.classList.add('hidden');
        if (content) content.classList.add('hidden');
        if (error) {
            error.classList.remove('hidden');
            error.style.opacity = '0';
            setTimeout(() => {
                error.style.transition = 'opacity 0.3s ease-in';
                error.style.opacity = '1';
            }, 50);
        }
    }
}

// CSS para line-clamp (adicionar ao final do arquivo se não existir)
const style = document.createElement('style');
style.textContent = `
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
