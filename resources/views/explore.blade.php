@extends('index')
@section('content')
<div class="h-screen w-screen overflow-hidden bg-gray-100 flex">
    @include('components/layout/sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('components/layout/header', ['title' => 'Explore'])

        <div class="relative flex-1 overflow-hidden">
            <div id="map" class="absolute inset-0 w-full h-full rounded-xl"></div>

            <!-- Botão para abrir o menu flutuante -->
            <button id="openFloatingMenuBtn"
                class="fixed right-6 top-[120px] z-20 bg-white border border-gray-300 shadow-lg rounded-lg p-3 flex items-center justify-center hover:bg-blue-50 transition-all duration-200"
                type="button">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Menu  flutuante -->
            <div id="floatingMenu"
                class="absolute right-6 top-6 bottom-6 w-96 flex-shrink-0 overflow-hidden z-30 shadow-lg rounded-2xl bg-white border border-gray-200 transition-transform duration-300"
                style="transform: translateX(110%);">
                <div class="flex flex-col h-full w-full bg-white rounded-2xl overflow-hidden">
                    <div class="mb-6 flex-shrink-0 px-4 pt-4">
                        <div class="my-4 flex items-center">
                            <div class="relative flex-1">
                                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input
                                    type="text"
                                    id="searchInput"
                                    placeholder="Pesquisar lugares..."
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm transition-all duration-200"
                                />
                            </div>
                            <button onclick="closeFloatingMenu()" class="flex-shrink-0 bg-white rounded-md p-2 hover:bg-blue-50 focus:outline-none ">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-wrap justify-center items-center">
                            @include('components.explore.filter-modal')
                            @if($hasTrip)
                                <button type="button" onclick="updateItineraryDisplay()"
                                    class="ml-2 px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-500 transition-all duration-150 text-sm font-medium shadow-sm flex items-center gap-1">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M5 9A7 7 0 0112 5a7 7 0 017 7v3a7 7 0 01-7 7 7 7 0 01-7-7V9z" />
                                    </svg>
                                </button>
                                <a href="/viagens/{{ session('trip_id') }}"
                                   class="ml-2 px-3 py-2 rounded-lg border border-blue-500 bg-blue-500 text-white hover:bg-blue-600 hover:border-blue-600 transition-all duration-150 text-sm font-medium shadow-sm flex items-center gap-1">
                                    <i class="fa-solid fa-plane"></i>
                                    <span class="ml-1">Ir para viagem</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @if(!$hasTrip)
                        <div class="flex flex-col justify-center items-center h-[450px] my-8">
                            <a href="/formTrip" id="createTripButton"
                            class="block">
                                <div class="flex flex-col items-center justify-center h-50 rounded-xl border-2 border-dashed border-gray-0 bg-white transition-all duration-200 cursor-pointer py-2 select-none hover:border-blue-500">
                                    <span class="flex items-center justify-center w-10 h-10 mb-2 rounded-full bg-gray-100">
                                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </span>
                                    <span class="text-xl font-extrabold text-gray-800 tracking-wide mb-2">Criar viagem</span>
                                    <span class="text-base text-gray-500 text-center max-w-xs mt-2">Você ainda não criou uma viagem.<br><span class='text-gray-400'>Crie uma viagem para liberar o itinerário e começar a planejar seu roteiro!</span></span>
                                </div>
                            </a>
                        </div>
                    @else
                    <div class="p-6 flex-1 flex flex-col overflow-hidden">
                        <div class="flex items-center gap-3 border-b mb-4 pb-3 flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <label for="datePicker" class="text-sm font-medium text-gray-700">Selecionar Data:</label>
                            <input
                                type="date"
                                id="datePicker"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                value=""
                            />
                        </div>

                        <div class="flex-1 overflow-y-auto min-h-0">
                            <div id="itinerary-content" class="h-full">
                                <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="font-medium mb-1">Nenhuma atividade ainda</span>
                                    <span class="text-sm text-center">Clique nos marcadores do mapa para adicionar atividades ao seu roteiro</span>
                                </div>
                            </div>
                        </div>

                        <div id="suggestions" class="mt-6 pt-6 border-t border-gray-200 flex-shrink-0">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Sugestões para Você</h3>
                            </div>
                            <div id="suggestions-list" class="space-y-3 max-h-48 overflow-y-auto">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros - Movido para fora do menu flutuante -->

@php $hasTrip = session()->has('trip_id'); @endphp
@includeWhen($hasTrip, 'components.explore.detailsmodal')
@unless($hasTrip)
    @include('components.explore.detailsmodal')
@endunless
<script>
// Notificação simples (alert) para fallback
if (typeof showNotification !== 'function') {
    function showNotification(msg, type) {
        // Você pode trocar por um toast mais bonito depois
        let prefix = '';
        if (type === 'success') prefix = '✔️ ';
        else if (type === 'error') prefix = '❌ ';
        else if (type === 'info') prefix = 'ℹ️ ';
        alert(prefix + msg);
    }
}

// --- Floating menu open/close logic ---
function openFloatingMenu() {
    document.getElementById('floatingMenu').style.transform = 'translateX(0)';
    document.getElementById('openFloatingMenuBtn').style.display = 'none';
}

function closeFloatingMenu() {
    document.getElementById('floatingMenu').style.transform = 'translateX(110%)';
    document.getElementById('openFloatingMenuBtn').style.display = 'flex';
}

// Global variables
let map;
let markers = [];
let places = [];
let infoWindow;
let currentDate = null;
let itinerary = {}; // Agora é um mapa de datas
window.hasTrip = @json($hasTrip); // Corrigido para usar a variável PHP correta
window.dataInicioViagem = @json($dataInicio);
window.dataFimViagem = @json($dataFim);
window.destinoViagem = @json($destino);
window.origemViagem = @json($origem);
// Variável para cache dos pontos do banco
let pontosCache = [];

/**
 * Normaliza datas para o formato YYYY-MM-DD.
 * @param {string} dateStr - Data em string (pode ser YYYY-MM-DD, DD/MM/YYYY, etc).
 * @returns {string} Data normalizada no formato YYYY-MM-DD.
 */
function normalizeDate(dateStr) {
    if (!dateStr) return '';
    // Se já estiver no formato YYYY-MM-DD, retorna direto
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
    // Se estiver no formato DD/MM/YYYY, converte
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
        const [d, m, y] = dateStr.split('/');
        return `${y}-${m}-${d}`;
    }
    // Tenta converter outros formatos usando Date
    const d = new Date(dateStr);
    if (!isNaN(d.getTime())) {
        return d.toISOString().slice(0, 10);
    }
    return dateStr; // fallback
}

/**
 * Sincroniza os pontos do banco de dados com a variável local do itinerário.
 * @param {Array} pontos - Lista de pontos de interesse vindos do backend.
 */
function syncItineraryWithDatabase(pontos) {
    try {
        // Pega as datas únicas dos pontos (ordenadas)
        const uniqueDates = [...new Set(pontos.map(p => normalizeDate(p.data_ponto_interesse)))].sort();
        // Se não houver datas, usa a data de hoje como fallback
        const today = new Date().toISOString().slice(0, 10);
        const dateList = uniqueDates.length > 0 ? uniqueDates : [today];

        // Limpa e monta o objeto itinerary por data
        itinerary = {};
        dateList.forEach(date => {
            itinerary[date] = [];
        });

        // Converte os pontos do banco para o formato usado localmente
        pontos.forEach(ponto => {
            const date = normalizeDate(ponto.data_ponto_interesse);
            const localPlace = {
                id: ponto.id,
                name: ponto.nome_ponto_interesse,
                description: ponto.desc_ponto_interesse || '',
                lat: parseFloat(ponto.latitude),
                lng: parseFloat(ponto.longitude),
                type: ponto.categoria || 'attraction',
                rating: 4.0,
                address: ponto.desc_ponto_interesse || '',
                time: ponto.hora_ponto_interesse || '',
                database_id: ponto.id,
                data_ponto_interesse: ponto.data_ponto_interesse
            };
            if (!itinerary[date]) itinerary[date] = [];
            itinerary[date].push(localPlace);
        });
    } catch (e) {
        console.log('função syncItineraryWithDatabase falhou', e);
    }
}

/**
 * Renderiza o itinerário para a data selecionada.
 * @param {string} selectedDate - Data selecionada.
 * @param {Array} pontos - Lista de pontos de interesse.
 */
function renderItineraryForDate(selectedDate, pontos) {
    try {
        currentDate = selectedDate;
        const normalizedSelectedDate = normalizeDate(selectedDate);
        const filtered = pontos.filter(p => normalizeDate(p.data_ponto_interesse) === normalizedSelectedDate);
        renderItinerary(filtered);
    } catch (e) {
        console.log('função renderItineraryForDate falhou', e);
    }
}


/**
 * Atualiza as sugestões de pontos de interesse para a data selecionada.
 * Usa os dados carregados do Google Places API e do backend.
 */
function updateSuggestions() {
    try {
        const suggestionsList = document.getElementById('suggestions-list');
        if (!suggestionsList) return;
        const selectedDate = currentDate || (document.getElementById('datePicker') && document.getElementById('datePicker').value);
        const pontosNomes = (itinerary[selectedDate] || []).map(p => p.name.toLowerCase());
        const filteredPlaces = places.filter(place => {
            return !pontosNomes.includes(place.name.toLowerCase());
        }).slice(0, 5);
        suggestionsList.innerHTML = filteredPlaces.map(place => `
            <div class="p-3 border border-gray-200 rounded-xl hover:bg-cyan-50 cursor-pointer transition-all duration-200 hover:shadow-md" onclick="openPlaceDetailsModal('${place.id}')">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-900 mb-1">${place.name}</h4>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs border rounded-full text-gray-600 border-gray-300 font-medium">
                                ${getTypeLabel(place.type)}
                            </span>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                ${place.rating}
                            </div>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-cyan-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.log('função updateSuggestions falhou', e);
    }
}

// --- Inicialização ---
document.addEventListener('DOMContentLoaded', function() {
    // Registra o event listener para o botão do menu flutuante
    const openBtn = document.getElementById('openFloatingMenuBtn');
    if (openBtn) {
        openBtn.onclick = openFloatingMenu;
    }

    // Step 3: Set min/max for both datepickers
    if (window.hasTrip && window.dataInicioViagem && window.dataFimViagem) {
        const mainDatePicker = document.getElementById('datePicker');
        if (mainDatePicker) {
            mainDatePicker.setAttribute('min', window.dataInicioViagem);
            mainDatePicker.setAttribute('max', window.dataFimViagem);
        }
        const modalDatePicker = document.getElementById('itineraryDate');
        if (modalDatePicker) {
            modalDatePicker.setAttribute('min', window.dataInicioViagem);
            modalDatePicker.setAttribute('max', window.dataFimViagem);
        }
    }

    // Atualiza o itinerário ao carregar a página
    updateItineraryDisplay();
    setTimeout(() => {
        updateSuggestions();
    }, 1000);

    // Atualiza o itinerário sempre que a data do datepicker mudar
    const datePicker = document.getElementById('datePicker');
    if (datePicker) {
        datePicker.addEventListener('change', function() {
            updateItineraryDisplay();
        });
    }
});

// Initialize Google Map
window.initMap = function() {
    // Função para inicializar o mapa após obter as coordenadas
    function startMapWithCoords(coords) {
        map = new google.maps.Map(document.getElementById("map"), {
            center: coords,
            zoom: 12,
            disableDefaultUI: true,
            zoomControl: false,
            mapTypeControl: true,
            fullscreenControl: false,
            streetViewControl: false,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{ color: "#e3f2fd" }]
                },
                {
                    featureType: "landscape",
                    elementType: "geometry",
                    stylers: [{ color: "#f5f5f5" }]
                }
            ]
        });

        const service = new google.maps.places.PlacesService(map);
        service.nearbySearch(
            {
                location: coords,
                radius: 10000,
                type: 'tourist_attraction',
            },
            (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    places = [];
                    results.forEach(place => {
                        // [GOOGLE PLACES API] Cada 'place' vem da resposta da API Places
                        places.push({
                            id: place.place_id,
                            name: place.name,
                            lat: place.geometry.location.lat(), // [GOOGLE PLACES API] latitude
                            lng: place.geometry.location.lng(), // [GOOGLE PLACES API] longitude
                            type: getPlaceType(place.types),
                            rating: place.rating || 4.0,
                            address: place.vicinity,
                            opening_hours: place.opening_hours ? place.opening_hours.weekday_text : [],
                            description: place.vicinity || place.formatted_address || '',
                            photos: place.photos ? place.photos.map(p => p.getUrl({ 'maxWidth': 400, 'maxHeight': 400 })) : [] // [GOOGLE PLACES API] URLs das fotos
                        });
                    });
                    addMarkersToMap();
                    updateSuggestions();
                } else {
                    console.error('Falha na busca de locais:', status);
                }
            }
        );

        infoWindow = new google.maps.InfoWindow();
        initPlacesAutocomplete();
    }

    // Busca coordenadas do destino da viagem, se houver
    if (window.destinoViagem && typeof getCoordinatesFromAddress === 'function') {
        getCoordinatesFromAddress(window.destinoViagem)
            .then(coords => {
                startMapWithCoords(coords);
            })
            .catch(() => {
                // Fallback para coordenadas padrão da origem da viagem
                startMapWithOrigemCoords();
            });
    } else {
        startMapWithCoords({ lat: -10.8263593, lng: -42.7335083 });
    }
};

function startMapWithOrigemCoords() {
         if (window.origemViagem && typeof getCoordinatesFromAddress === 'function') {
            getCoordinatesFromAddress(window.origemViagem)
                .then(coords => {
                    startMapWithCoords(coords);
                    console.log('Mapa iniciado com coordenadas da origem da viagem:', coords);
                })
                .catch(() => {
                    // Fallback para coordenadas padrão (Xique-Xique, Bahia)
                    startMapWithCoords({ lat: -10.8263593, lng: -42.7335083 });
                });
    }
}

/**
 * Busca coordenadas de um endereço usando a API de Geocoding do Google.
 * @param {string} address - Endereço ou nome do local.
 * @returns {Promise<{lat: number, lng: number}>}
 */
async function getCoordinatesFromAddress(address) {
    return new Promise((resolve, reject) => {
        if (!address || address.trim() === '') {
            reject('Endereço vazio');
            return;
        }
        // [GOOGLE GEOCODING API] Busca coordenadas do endereço
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: address }, (results, status) => {
            if (status === 'OK' && results[0]) {
                // [GOOGLE GEOCODING API] Resultado encontrado
                const location = results[0].geometry.location;
                resolve({ lat: location.lat(), lng: location.lng() });
            } else {
                reject('Não foi possível encontrar o local: ' + status);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('locationInput');
    const searchInput = document.getElementById('searchInput');
    function getActiveInputValue() {
        if (locationInput && locationInput.value.trim() !== '') {
            return locationInput.value.trim();
        }
        if (searchInput && searchInput.value.trim() !== '') {
            return searchInput.value.trim();
        }
        return '';
    }

    function handleMapCentering(e) {
        if (e.key === 'Enter') {
            const value = getActiveInputValue();
            if (value !== '') {
                getCoordinatesFromAddress(value)
                    .then(coords => {
                        map.setCenter(coords);
                        map.setZoom(15);
                        showNotification('Mapa centralizado em: ' + value, 'info');
                    })
                    .catch(err => {
                        showNotification(err, 'error');
                    });
            }
        }
    }

    if (locationInput) {
        locationInput.addEventListener('keydown', handleMapCentering);
    }
    if (searchInput) {
        searchInput.addEventListener('keydown', handleMapCentering);
    }
});

// Função para inicializar autocomplete Google Places
function initPlacesAutocomplete() {
    const searchInput = document.getElementById('searchInput');
    const locationInput = document.getElementById('locationInput');
    
    // Helper to initialize autocomplete on a given input
    function setupAutocomplete(input) {
        // [GOOGLE PLACES AUTOCOMPLETE] Inicializa autocomplete do Google Places no input
        if (input && typeof google !== 'undefined' && google.maps && google.maps.places) {
            if (!input._autocompleteInitialized) {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['(regions)'],
                });
                autocomplete.addListener('place_changed', function() {
                    if (typeof updateFilterCount === 'function') updateFilterCount();
                });
                input._autocompleteInitialized = true;
            }
        }
    }
    
    setupAutocomplete(searchInput);
    setupAutocomplete(locationInput);
}

// Add markers to map
function addMarkersToMap() {
    // Clear existing markers
    markers.forEach(marker => marker.setMap(null));
    markers = [];

    places.forEach(place => {
        // The filtering by category will now be handled by the places array itself,
        // which is updated by applyMapFilters based on selected types.
        const marker = new google.maps.Marker({
            position: { lat: place.lat, lng: place.lng },
            map: map,
            title: place.name,
            icon: getMarkerIcon(place.type),
            animation: google.maps.Animation.DROP
        });

        marker.addListener('click', () => {
            showPlaceInfo(place, marker);
        });

        markers.push(marker);
    });
}

// Get marker icon based on place type
function getMarkerIcon(type) {
    const colors = {
        attraction: '#8B5CF6', // purple
        restaurant: '#F97316', // orange
        hotel: '#2563EB'        // blue
    };

    // Use a default color if the type doesn't have a specific color defined
    const color = colors[type] || '#6B7280'; // Gray for unknown types

    return {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#FFFFFF',
        strokeWeight: 3,
        scale: 10
    };
}

// Show place info in InfoWindow
function showPlaceInfo(place, marker) {
    // Corrige busca no itinerary para aceitar tanto id quanto database_id
    const selectedDate = currentDate || (document.getElementById('datePicker') && document.getElementById('datePicker').value);
    const isInItinerary = (itinerary[selectedDate] || []).find(p => p.id === place.id || p.id === place.place_id || p.database_id === place.id);
    const imageUrl = place.photos && place.photos.length > 0 ? place.photos[0] : 'https://via.placeholder.com/150x100?text=Sem+Foto';
    const content = `
        <div class="p-2 max-w-xs">
            <div class="mb-4">
                <img src="${imageUrl}" alt="${place.name}" class="w-full h-32 object-cover rounded-md shadow-sm">
            </div>
            <h3 class="font-bold text-gray-900 mb-1 text-lg">${place.name}</h3>
            <p class="text-sm text-gray-600 mb-2">${place.description}</p>
            <div class="flex items-center gap-2 mb-3">
                <span class="px-2 py-1 text-xs rounded-full font-medium ${getTypeColorClass(place.type)}">
                    ${getTypeLabel(place.type)}
                </span>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    ${place.rating}
                </div>
            </div>
            <button onclick="openPlaceDetailsModal('${place.id}')" class="w-full px-4 py-2 mb-2 text-sm text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors font-medium">
                Ver Mais Informações
            </button>
        </div>
    `;
    infoWindow.setContent(content);
    infoWindow.open(map, marker);
}


// Event listeners
document.addEventListener('DOMContentLoaded', function() {

    // Category buttons - Desktop (these will likely be replaced by the filter modal's logic)
    // Retaining them for now, but their impact will be less direct on marker display
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // This logic might become redundant as the filter modal will handle type selection
            currentCategory = this.dataset.category; 

            // Update active category visual (optional, could be removed if filter modal is primary)
            document.querySelectorAll('.category-btn').forEach(b => {
                const category = b.dataset.category;
                if (category === 'all') {
                    b.className = 'category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-gray-300 text-gray-700 hover:bg-gray-600 hover:text-white hover:border-transparent';
                } else if (category === 'attraction') {
                    b.className = 'category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-purple-300 text-purple-700 hover:bg-purple-600 hover:text-white hover:border-transparent';
                } else if (category === 'restaurant') {
                    b.className = 'category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-orange-300 text-orange-700 hover:bg-orange-600 hover:text-white hover:border-transparent';
                } else if (category === 'hotel') {
                    b.className = 'category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-transparent';
                }
            });

            if (currentCategory === 'all') {
                this.className = 'category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 bg-gradient-to-r from-gray-600 to-gray-700 text-white border-transparent shadow-sm';
            } else {
                const color = this.dataset.category === 'attraction' ? 'purple' :
                               this.dataset.category === 'restaurant' ? 'orange' : 'blue';
                this.className = `category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 bg-gradient-to-r from-${color}-600 to-${color}-700 text-white border-transparent shadow-sm`;
            }

            // addMarkersToMap(); // This call will now be handled by applyMapFilters via the modal
            updateSuggestions();
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');

    function handleSearch(query) {
        const filteredPlaces = places.filter(place =>
            place.name.toLowerCase().includes(query.toLowerCase()) ||
            place.description.toLowerCase().includes(query.toLowerCase())
        );

        // Clear existing markers
        markers.forEach(marker => marker.setMap(null));
        markers = [];

        // Add filtered markers
        filteredPlaces.forEach(place => {
            // No longer filtering by currentCategory here, as places array is already filtered
            const marker = new google.maps.Marker({
                position: { lat: place.lat, lng: place.lng },
                map: map,
                title: place.name,
                icon: getMarkerIcon(place.type),
                animation: google.maps.Animation.DROP
            });

            marker.addListener('click', () => {
                showPlaceInfo(place, marker);
            });

            markers.push(marker);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            if (e.target.value.trim() === '') {
                addMarkersToMap(); // If search is cleared, show all currently loaded places
            } else {
                handleSearch(e.target.value);
            }
        });
    }

    // Initialize
    updateItineraryDisplay();
    setTimeout(() => {
        updateSuggestions();
    }, 1000);
});

// --- Função para atualizar o itinerário do banco ---
/**
 * Atualiza o itinerário exibido na tela buscando os dados do backend (API interna Laravel).
 * Chama a rota /explore/itinerary (GET) e atualiza o DOM.
 * @returns {Promise<void>}
 */
async function updateItineraryDisplay() {
    if (!window.hasTrip) {
        document.getElementById('itinerary-content') && (document.getElementById('itinerary-content').innerHTML = '');
        document.getElementById('suggestions-list') && (document.getElementById('suggestions-list').innerHTML = '');
        return;
    }
    try {
        const response = await fetch('/explore/itinerary');
        if (!response || !response.ok) {
            console.error('Falha ao buscar /explore/itinerary: resposta nula, indefinida ou status não OK');
            return;
        }
        let pontos;
        try {
            pontos = await response.json();
        } catch (jsonErr) {
            console.error('Falha ao converter resposta em JSON:', jsonErr);
            return;
        }
        pontosCache = pontos; // Atualiza cache global
        // Filtra pontos para a data selecionada
        const datePicker = document.getElementById('datePicker');
        const selectedDate = datePicker && datePicker.value ? datePicker.value : (pontos && pontos.length > 0 ? normalizeDate(pontos[0].data_ponto_interesse) : null);
        if (selectedDate) {
            const pontosFiltrados = pontos.filter(p => normalizeDate(p.data_ponto_interesse) === normalizeDate(selectedDate));
            const desktopContent = document.getElementById('itinerary-content');
            if (desktopContent) {
                if (!pontosFiltrados || pontosFiltrados.length === 0) {
                    desktopContent.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                       <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                       </svg>
                       <span class="font-medium mb-1">Nenhuma atividade ainda</span>
                       <span class="text-sm text-center">Clique nos marcadores do mapa para adicionar atividades ao seu roteiro</span>
                     </div>`;
                } else {
                    desktopContent.innerHTML = `<div class="space-y-4">${pontosFiltrados.map((place, index) => `
                        <div class="p-4 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-all duration-200 group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 cursor-pointer" onclick="openPlaceDetailsModal('${place.placeid_ponto_interesse}', true, ${place.pk_id_ponto_interesse})">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">${index + 1}</span>
                                    </div>
                                    <div class="text-gray-900 font-semibold text-sm">${place.nome_ponto_interesse || ''}</div>
                                    <div class="text-gray-500 text-xs mt-1">${place.desc_ponto_interesse || ''}</div>
                                    <div class="text-gray-500 text-xs mt-1">${place.hora_ponto_interesse || ''}</div>
                                </div>
                                <button onclick="removeFromDatabase(${place.id})" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    </svg>
                                </button>
                            </div>
                        </div>`).join('')}</div>`;
                }
            }
        }
        updateSuggestions();
    } catch (e) {
        console.log('função updateItineraryDisplay falhou', e);
        console.error('Erro ao buscar ou processar pontos:', e);
    }
}

/**
 * Adiciona um ponto de interesse ao itinerário do usuário.
 * Faz POST para a API interna Laravel (/explore).
 * @param {string} placeId - ID do local (Google ou banco).
 * @param {string} selectedTime - Hora selecionada.
 * @param {string} selectedDate - Data selecionada.
 * @returns {Promise<void>}
 */
async function addToItinerary(placeId, selectedTime, selectedDate) {
    if (!window.hasTrip) {
        alert('Crie uma viagem antes de adicionar pontos!');
        return;
    }
    const place = currentDetailedPlace; 
    const data = {
        nome_ponto_interesse: place.name,
        desc_ponto_interesse: place.description || place.vicinity || '',
        latitude: place.lat || (place.geometry && place.geometry.location.lat ? place.geometry.location.lat() : null),
        longitude: place.lng || (place.geometry && place.geometry.location.lng ? place.geometry.location.lng() : null),
        categoria: place.type || '',
        hora_ponto_interesse: selectedTime,
        data_ponto_interesse: selectedDate || '',
        placeid_ponto_interesse: place.place_id || place.placeId || place.id || place.placeid_ponto_interesse || placeId || '',
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erro: Token de segurança não encontrado!', 'error');
        return;
    }
    try {
        const response = await fetch('/explore', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });
        const responseText = await response.text();
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            return;
        }
        updateItineraryDisplay();
    } catch (error) {
        showNotification('Erro ao adicionar ponto: ' + error.message, 'error');
    }
}
window.addToItinerary = addToItinerary;

/**
 * Remove um ponto de interesse do itinerário do usuário.
 * Faz DELETE para a API interna Laravel (/explore/{id}).
 * @param {number} pontoId - ID do ponto de interesse no banco de dados.
 */
function removePontoFromItinerary(pontoId) {
    fetch(`/explore/${pontoId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualize o itinerário na tela, removendo o ponto
            updateItineraryDisplay();
        } else {
            showNotification(data.error || 'Erro ao remover ponto', 'error');
        }
    })
    .catch(() => showNotification('Erro ao remover ponto', 'error'));
    closeModal();
    updateItineraryDisplay();
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&callback=initMap" async defer></script>
@endsection