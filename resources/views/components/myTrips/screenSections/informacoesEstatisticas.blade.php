@php
    // Arquivo refatorado: refactor_myTrips_stats.blade.php
    // Objetivo: organizar markup + scripts (Weather, News, Events, Currency, UI State)
@endphp

<div class="space-y-8">
    @include('components/myTrips/screenSections/themes/statisticSection', ['viagem' => $viagem])

    @include('components/myTrips/screenSections/themes/wetherSection', [
        'viagem' => $viagem,
        'usuario' => $usuario ?? null
    ])

</div>

<!-- Bloco de Notícias -->
@include('components/myTrips/screenSections/themes/newsSection', [
    'viagem' => $viagem,
    'eventos' => $eventos ?? collect()
])

<!-- Bloco de Eventos -->
<div class="mt-8">
    <label for="events-destination-select" class="block text-sm font-medium text-gray-700 mb-2">Eventos para o destino:</label>
    <select id="events-destination-select" class="mb-4 px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
        @foreach($viagem->destinos as $destino)
            <option value="{{ $destino->pk_id_destino }}">{{ $destino->nome_destino }}</option>
        @endforeach
    </select>

    <div id="events-skeleton-stats" class="py-8 text-center hidden">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-400"></i>
        <p class="text-gray-500 mt-2">Carregando eventos...</p>
    </div>

    <div id="events-error-stats" class="py-8 text-center hidden">
        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
        <p class="text-gray-500 mt-2">Não foi possível carregar eventos para este destino.</p>
    </div>

    <div id="events-content-stats" class="space-y-6"></div>
</div>

<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
</style>

<script>
// Refatoração completa: módulo único com submódulos para Weather, News, Events e Currency
(function () {
    'use strict';

    // ---------- CONFIG / HELPERS ----------
    const $ = (sel) => document.querySelector(sel);
    const $all = (sel) => Array.from(document.querySelectorAll(sel));

    function safeText(text) {
        return text == null ? '' : String(text);
    }

    function fetchJson(url, options = {}) {
        // wrapper simples para fetch com erro padronizado
        return fetch(url, options).then(async res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        });
    }

    function createElementFromHTML(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstChild;
    }

    function dateToLongPtBr(dateStr) {
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' });
        } catch (e) {
            return safeText(dateStr);
        }
    }

    // ---------- UI STATE MANAGER ----------
    const UIState = {
        set(section, state) {
            const skeleton = document.getElementById(`${section}-skeleton-stats`);
            const content  = document.getElementById(`${section}-content-stats`);
            const error    = document.getElementById(`${section}-error-stats`);
            if (!skeleton || !content || !error) return;

            skeleton.classList.toggle('hidden', state !== 'loading');
            content.classList.toggle('hidden', state !== 'content');
            error.classList.toggle('hidden', state !== 'error');
        }
    };

    // ---------- ICON MAP (weather wmoCode) ----------
    const WEATHER_ICONS = {
        default: { icon: 'fa-question-circle', color: 'text-gray-400' },
        0: { icon: 'fa-sun', color: 'text-yellow-500' },
        1: { icon: 'fa-cloud-sun', color: 'text-gray-500' },
        2: { icon: 'fa-cloud-sun', color: 'text-gray-500' },
        3: { icon: 'fa-cloud', color: 'text-gray-400' },
        45: { icon: 'fa-smog', color: 'text-gray-400' },
        48: { icon: 'fa-smog', color: 'text-gray-400' },
        51: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        53: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        55: { icon: 'fa-cloud-rain', color: 'text-blue-400' },
        61: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        63: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        65: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-500' },
        71: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        73: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        75: { icon: 'fa-snowflake', color: 'text-cyan-400' },
        80: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        81: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        82: { icon: 'fa-cloud-showers-heavy', color: 'text-blue-600' },
        85: { icon: 'fa-snowflake', color: 'text-cyan-300' },
        86: { icon: 'fa-snowflake', color: 'text-cyan-300' },
        95: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
        96: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
        99: { icon: 'fa-cloud-bolt', color: 'text-purple-500' },
    };

    function getWeatherIcon(wmoCode) {
        return WEATHER_ICONS[wmoCode] || WEATHER_ICONS.default;
    }

    // ---------- MODULE: Weather ----------
    const WeatherModule = (function () {
        let isLoading = false;

        function render(weatherData) {
            const content = document.getElementById('weather-content-stats');
            if (!content) return;

            let html = '';
            const daily = weatherData?.daily;
            if (daily && Array.isArray(daily.time) && daily.time.length > 0) {
                // find today index safely
                const today = new Date();
                let todayIndex = daily.time.findIndex(dt => {
                    const d = new Date(dt);
                    return d.getDate() === today.getDate() && d.getMonth() === today.getMonth() && d.getFullYear() === today.getFullYear();
                });

                if (todayIndex === -1) todayIndex = 0; // fallback

                const todayTemp = Math.round(daily.temperature_2m_max?.[todayIndex] ?? 0);
                const todayMin  = Math.round(daily.temperature_2m_min?.[todayIndex] ?? 0);
                const todayWmo  = daily.weather_code?.[todayIndex] ?? 0;
                const iconToday  = getWeatherIcon(todayWmo);

                html += `
                    <div class="bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-2xl p-6 mb-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-blue-900 mb-1">Hoje</h3>
                                <p class="text-blue-700">${dateToLongPtBr(daily.time[todayIndex])}</p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center space-x-3">
                                    <div class="w-16 h-16 bg-white/50 rounded-full flex items-center justify-center shadow-lg">
                                        <i class="fas ${iconToday.icon} ${iconToday.color} text-3xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-4xl font-bold text-blue-900">${todayTemp}°</div>
                                        <div class="text-sm text-blue-600">Mín: ${todayMin}°</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                // grid with days
                html += '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">';
                for (let i = 0; i < daily.time.length; i++) {
                    const d = new Date(daily.time[i]);
                    const dayName = d.toLocaleDateString('pt-BR', { weekday: 'short' });
                    const dayNum = d.getDate();
                    const max = Math.round(daily.temperature_2m_max?.[i] ?? 0);
                    const min = Math.round(daily.temperature_2m_min?.[i] ?? 0);
                    const icon = getWeatherIcon(daily.weather_code?.[i] ?? 0);
                    const isToday = i === todayIndex;

                    html += `
                        <div class="${isToday ? 'ring-2 ring-blue-400' : ''} bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                            <div class="text-sm font-medium text-gray-700 mb-1">${dayName}</div>
                            <div class="text-xs text-gray-500 mb-3">${dayNum}</div>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas ${icon.icon} ${icon.color} text-xl"></i>
                            </div>
                            <div class="font-bold text-gray-800">${max}°</div>
                            <div class="text-sm text-gray-500">${min}°</div>
                        </div>`;
                }
                html += '</div>';
            } else {
                html = `<div class="text-center py-12"><p class="text-gray-600">Dados de clima indisponíveis para este destino.</p></div>`;
            }

            content.innerHTML = html;
        }

        async function load(tripId, destinoId) {
            if (isLoading) return;
            isLoading = true;
            UIState.set('weather', 'loading');
            try {
                const data = await fetchJson(`/viagens/${tripId}/weather/${destinoId}`);
                if (data?.success && data.data) {
                    render(data.data);
                    UIState.set('weather', 'content');
                } else {
                    UIState.set('weather', 'error');
                }
            } catch (e) {
                console.error('Erro ao carregar clima:', e);
                UIState.set('weather', 'error');
            } finally {
                isLoading = false;
            }
        }

        return { load };
    })();

    // ---------- MODULE: News ----------
    const NewsModule = (function () {
        let isLoading = false;

        function render(newsData) {
            const content = document.getElementById('news-content-stats');
            if (!content) return;
            let html = '';

            if (Array.isArray(newsData) && newsData.length > 0) {
                const featured = newsData[0];
                const featuredTitle = safeText(featured.title) || 'Título indisponível';
                const featuredSnippet = safeText(featured.snippet) || 'Descrição não fornecida.';
                const featuredLink = featured.link || '#';

                html += `
                    <div class="bg-red-50 rounded-2xl p-6 mb-6 border border-red-200">
                        <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 md:space-x-6">
                            ${featured.thumbnail ? `
                                <img src="${featured.thumbnail}" alt="Imagem da notícia" class="w-full md:w-48 h-32 object-cover rounded-lg shadow-md">` : `
                                <div class="w-full md:w-48 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 flex-shrink-0"><i class="fas fa-image text-2xl"></i></div>`}
                            <div class="flex-1">
                                <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">DESTAQUE</span>
                                <h3 class="text-xl font-bold text-gray-900 mt-2 mb-2 line-clamp-2">${featuredTitle}</h3>
                                <p class="text-gray-700 mb-4 line-clamp-2">${featuredSnippet}</p>
                                <a href="${featuredLink}" target="_blank" rel="noopener noreferrer" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Ler mais</a>
                            </div>
                        </div>
                    </div>`;

                if (newsData.length > 1) {
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                    newsData.slice(1, 7).forEach(article => {
                        const title = safeText(article.title) || 'Título indisponível';
                        const link = article.link || '#';
                        html += `
                            <div class="bg-white border border-gray-100 rounded-xl p-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-start space-x-4">
                                    ${article.thumbnail ? `
                                        <img src="${article.thumbnail}" alt="Imagem da notícia" class="w-24 h-16 object-cover rounded-lg flex-shrink-0">` : `
                                        <div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 flex-shrink-0"><i class="fas fa-image"></i></div>`}
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-semibold text-gray-800 mb-1 line-clamp-2">${title}</h5>
                                        <a href="${link}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-red-600 hover:text-red-800">Ver <i class="fas fa-chevron-right ml-1 text-xs"></i></a>
                                    </div>
                                </div>
                            </div>`;
                    });
                    html += '</div>';
                }
            } else {
                html = `<div class="text-center py-12"><p class="text-gray-600">Nenhuma notícia encontrada para este destino.</p></div>`;
            }

            content.innerHTML = html;
        }

        async function load(tripId, destinoId) {
            if (isLoading) return;
            isLoading = true;
            UIState.set('news', 'loading');
            try {
                const data = await fetchJson(`/viagens/${tripId}/news/${destinoId}`);
                if (data?.success && data.data) {
                    render(data.data);
                    UIState.set('news', 'content');
                } else {
                    UIState.set('news', 'error');
                }
            } catch (e) {
                console.error('Erro ao carregar notícias:', e);
                UIState.set('news', 'error');
            } finally {
                isLoading = false;
            }
        }

        return { load };
    })();

    // ---------- MODULE: Events ----------
    const EventsModule = (function () {
        let isLoading = false;

        function render(eventsData) {
            const content = document.getElementById('events-content-stats');
            if (!content) return;


            let html = '';

            if (Array.isArray(eventsData) && eventsData.length > 0) {
                const featured = eventsData[0];
                const featuredTitle = safeText(featured.title) || 'Evento sem título';
                const featuredDate  = safeText(featured.date) || 'Data não informada';
                const featuredAddress = safeText(featured.address) || '';
                const featuredLink = featured.link || '#';

                // Imagem de maior qualidade possível
                const featuredImage = featured.image_highres || featured.thumbnail || featured.image || null;
                const featuredDescription = featured.description || featured.snippet || '';
                const featuredTime = featured.time || featured.start_time || '';
                const featuredPrice = featured.price || featured.ticket_info || '';
                const featuredCategory = featured.category || featured.type || '';
                const featuredOrganizer = featured.organizer || featured.source || '';
                const featuredVenueUrl = featured.map_url || featured.venue_link || '';

                html += `
                    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-2xl p-6 mb-6 border border-green-200 shadow-lg">
                        <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 md:space-x-8">
                            <div class="w-full md:w-56 h-40 flex items-center justify-center bg-white rounded-xl overflow-hidden shadow-md border border-green-100">
                                ${featuredImage ?
                                    `<img src="${featuredImage}" alt="Imagem do evento" class="object-cover w-full h-full" loading="lazy">` :
                                    `<div class="w-full h-full flex items-center justify-center text-green-300"><i class="fas fa-calendar-alt text-4xl"></i></div>`
                                }
                            </div>
                            <div class="flex-1">
                                <span class="bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">PRINCIPAL</span>
                                <h3 class="text-2xl font-bold text-gray-900 mt-2 mb-2 line-clamp-2">${featuredTitle}</h3>
                                <p class="text-gray-700 mb-1"><i class="fas fa-calendar mr-1"></i> ${featuredDate}${featuredTime ? ' - ' + featuredTime : ''}</p>
                                ${featuredAddress ? `<p class="text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-1"></i> ${featuredAddress}</p>` : ''}
                                ${featuredVenueUrl ? `<a href="${featuredVenueUrl}" target="_blank" rel="noopener noreferrer" class="text-green-700 underline text-xs mb-1 inline-block">Ver local no mapa</a>` : ''}
                                ${featuredCategory ? `<span class="inline-block bg-green-200 text-green-800 text-xs px-2 py-1 rounded mr-2 mb-1">${featuredCategory}</span>` : ''}
                                ${featuredPrice ? `<span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mb-1">${featuredPrice}</span>` : ''}
                                ${featuredOrganizer ? `<div class="text-xs text-gray-500 mb-1">Organizador: ${featuredOrganizer}</div>` : ''}
                                ${featuredDescription ? `<div class="text-gray-600 text-sm mt-2 line-clamp-2">${featuredDescription}</div>` : ''}
                                <a href="${featuredLink}" target="_blank" rel="noopener noreferrer" class="inline-block mt-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow transition-colors">Ver detalhes</a>
                            </div>
                        </div>
                    </div>`;

                if (eventsData.length > 1) {
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
                    eventsData.slice(1, 7).forEach(ev => {
                        const title = safeText(ev.title) || 'Evento sem título';
                        const date  = safeText(ev.date) || '';
                        const link  = ev.link || '#';
                        const image = ev.image_highres || ev.thumbnail || ev.image || null;
                        const description = ev.description || ev.snippet || '';
                        const time = ev.time || ev.start_time || '';
                        const price = ev.price || ev.ticket_info || '';
                        const category = ev.category || ev.type || '';
                        const organizer = ev.organizer || ev.source || '';
                        const venueUrl = ev.map_url || ev.venue_link || '';
                        html += `
                            <div class="bg-white border border-green-100 rounded-xl p-4 hover:shadow-xl transition-shadow flex flex-col h-full">
                                <div class="w-full h-28 mb-3 flex items-center justify-center bg-gray-50 rounded-lg overflow-hidden border border-green-50">
                                    ${image ?
                                        `<img src="${image}" alt="Imagem do evento" class="object-cover w-full h-full" loading="lazy">` :
                                        `<div class="w-full h-full flex items-center justify-center text-green-200"><i class="fas fa-calendar-alt text-2xl"></i></div>`
                                    }
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col">
                                    <h5 class="font-semibold text-gray-800 mb-1 line-clamp-2">${title}</h5>
                                    <p class="text-gray-600 text-xs mb-1"><i class="fas fa-calendar mr-1"></i> ${date}${time ? ' - ' + time : ''}</p>
                                    ${venueUrl ? `<a href="${venueUrl}" target="_blank" rel="noopener noreferrer" class="text-green-700 underline text-xs mb-1">Ver local</a>` : ''}
                                    ${category ? `<span class="inline-block bg-green-200 text-green-800 text-xs px-2 py-1 rounded mr-2 mb-1">${category}</span>` : ''}
                                    ${price ? `<span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mb-1">${price}</span>` : ''}
                                    ${organizer ? `<div class="text-xs text-gray-500 mb-1">Organizador: ${organizer}</div>` : ''}
                                    ${description ? `<div class="text-gray-600 text-xs mt-1 line-clamp-2">${description}</div>` : ''}
                                    <a href="${link}" target="_blank" rel="noopener noreferrer" class="mt-auto text-sm font-medium text-green-600 hover:text-green-800">Ver <i class="fas fa-chevron-right ml-1 text-xs"></i></a>
                                </div>
                            </div>`;
                    });
                    html += '</div>';
                }
            } else {
                html = `<div class="text-center py-12"><p class="text-gray-600">Nenhum evento encontrado para este destino.</p></div>`;
            }

            content.innerHTML = html;
        }

        async function load(tripId, destinoId) {
            if (isLoading) return;
            isLoading = true;
            UIState.set('events', 'loading');
            try {
                const data = await fetchJson(`/viagens/${tripId}/events/${destinoId}`);
                if (data?.success && data.data) {
                    render(data.data);
                    UIState.set('events', 'content');
                } else {
                    UIState.set('events', 'error');
                }
            } catch (e) {
                console.error('Erro ao carregar eventos:', e);
                UIState.set('events', 'error');
            } finally {
                isLoading = false;
            }
        }

        return { load };
    })();

    // ---------- MODULE: Currency (thin adapter) ----------
    const CurrencyModule = (function () {
        let isLoading = false;

        async function update(destinationName) {
            if (isLoading || !destinationName) return;
            isLoading = true;
            const el = document.getElementById('destination-currency-text');
            if (el) el.textContent = `Moeda para ${destinationName}`;

            try {
                if (typeof window.getCurrencyRates === 'function') {
                    await window.getCurrencyRates(destinationName);
                } else {
                    console.warn('getCurrencyRates não disponível');
                }

                if (typeof window.renderChart === 'function') {
                    await window.renderChart(7);
                }
            } catch (e) {
                console.error('Erro ao atualizar moeda:', e);
            } finally {
                isLoading = false;
            }
        }

        return { update };
    })();

    // ---------- INITIALIZATION ----------
    function initializeStatsForCurrentTab() {
        // garante execução única
        if (initializeStatsForCurrentTab._ran) return;
        initializeStatsForCurrentTab._ran = true;

        const tripId = window.currentTripId;
        if (!tripId) return;

        // Weather initial
        const weatherSelect = document.getElementById('weather-destination-select');
        if (weatherSelect && weatherSelect.value) WeatherModule.load(tripId, weatherSelect.value);

        // News initial
        const newsSelect = document.getElementById('news-destination-select');
        if (newsSelect && newsSelect.value) NewsModule.load(tripId, newsSelect.value);

        // Currency initial
        const currencySelect = document.getElementById('currency-destination-select');
        if (currencySelect && currencySelect.options.length > 0) {
            const selected = currencySelect.options[currencySelect.selectedIndex];
            const destinationName = selected?.dataset?.destinationName;
            if (destinationName) CurrencyModule.update(destinationName);
        }

        // Events initial + listeners
        const eventsSelect = document.getElementById('events-destination-select');
        if (eventsSelect) {
            EventsModule.load(tripId, eventsSelect.value);
            eventsSelect.addEventListener('change', function () { EventsModule.load(tripId, this.value); });
        }

        // attach listeners for other selects
        if (weatherSelect) weatherSelect.addEventListener('change', function () { WeatherModule.load(tripId, this.value); });
        if (newsSelect) newsSelect.addEventListener('change', function () { NewsModule.load(tripId, this.value); });
        if (currencySelect) currencySelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const name = opt?.dataset?.destinationName;
            if (name) CurrencyModule.update(name);
        });
    }

    // Expondo init no window para ser chamado pelo tab (ou botão) externo
    window.initStatsTab = initializeStatsForCurrentTab;

    // Se o TAB é clicado, inicializa apenas uma vez (compatível com o comportamento anterior)
    document.addEventListener('DOMContentLoaded', () => {
        const statsTab = document.getElementById('tab-informacoes-estatisticas');
        if (statsTab) statsTab.addEventListener('click', initializeStatsForCurrentTab, { once: true });

        // Caso o tab já esteja ativo, inicializa automaticamente
        if (statsTab && statsTab.classList.contains('active')) initializeStatsForCurrentTab();

        // Expondo possíveis funções de moeda/gráfico se já existirem
        if (typeof window.getCurrencyRates === 'function') window.getCurrencyRates = window.getCurrencyRates;
        if (typeof window.renderChart === 'function') window.renderChart = window.renderChart;
    });

})();
</script>
