@extends('index')
@section('content')
<div class="h-screen w-screen overflow-hidden bg-gray-100 flex">
    @include('components/layout/sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Explorar Rio de Janeiro</h1>
                    <p class="text-sm text-gray-500">Descubra os melhores lugares da cidade</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="hidden sm:flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Exportar
                </button>
                <button class="flex items-center px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Salvar
                </button>
            </div>
        </header>

        <div class="relative flex-1 overflow-hidden">
            <div id="map" class="absolute inset-0 w-full h-full rounded-xl"></div>

            <div class="absolute right-6 top-6 bottom-6 hidden lg:flex w-96 flex-shrink-0 overflow-hidden z-10 shadow-lg rounded-2xl">
                <div class="flex flex-col h-full w-full bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="mb-6 flex-shrink-0 px-4 pt-4">
                        <div class="relative my-4">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Pesquisar lugares..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm transition-all duration-200"
                            />
                        </div>

                        <div class="flex flex-wrap justify-center">
                            @include('components.explore.filter-modal')
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col overflow-hidden">
                        <div class="flex border-b mb-4 flex-shrink-0">
                            <button class="day-tab px-4 py-2 font-medium border-b-2 border-blue-500 text-blue-600" data-day="1">Dia 1</button>
                            <button class="day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-day="2">Dia 2</button>
                            <button class="day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-day="3">Dia 3</button>
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
                </div>
            </div>
        </div>
    </div>
</div>

<div id="placeDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full max-h-[90vh] overflow-y-auto transform scale-95 opacity-0 transition-all duration-300 relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div id="modalContent" class="p-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800" id="detailedPlaceName">Nome do Local</h2>
            <p class="text-gray-600 mb-2" id="detailedPlaceAddress">Endereço do Local</p>
            <p class="text-gray-700 leading-relaxed" id="detailedPlaceDescription">
                Descrição detalhada do local será carregada aqui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <div id="detailedPlacePhotos" class="grid grid-cols-2 gap-4 mt-4">
                </div>
            <p class="text-gray-800 font-semibold mt-4" id="detailedPlaceRating">Avaliação: N/A</p>
            <p class="text-gray-800 font-semibold" id="detailedPlaceType">Tipo: N/A</p>
        </div>

        <div class="p-8 border-t border-gray-200 flex flex-col sm:flex-row justify-end items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="itineraryTime" class="text-gray-700 font-medium whitespace-nowrap">Hora da visita:</label>
                <input type="time" id="itineraryTime" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
            </div>

            <button onclick="addToItinerary(currentDetailedPlace.place_id, document.getElementById('itineraryTime').value); closeModal();" class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg w-full sm:w-auto">
                ➕ Adicionar ao Itinerário
            </button>
        </div>
    </div>
</div>
<script>
// Global variables
let map;
let markers = [];
let places = [];
let infoWindow;
let currentDay = 1;
let currentCategory = 'all'; // This will now be controlled by the filter modal
let itinerary = { 1: [], 2: [], 3: [] };
let currentDetailedPlace = null; // Para armazenar o lugar sendo exibido no modal

// Initialize Google Map
window.initMap = function() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: -22.9068, lng: -43.1729 }, // Rio de Janeiro
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

    // Initial nearbySearch
    service.nearbySearch(
        {
            location: { lat: -22.9068, lng: -43.1729 }, // Rio de Janeiro
            radius: 10000,
            type: 'tourist_attraction',
        },
        (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                places = [];
                results.forEach(place => {
                    places.push({
                        id: place.place_id,
                        name: place.name,
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng(),
                        type: getPlaceType(place.types),
                        rating: place.rating || 4.0,
                        address: place.vicinity,
                        opening_hours: place.opening_hours ? place.opening_hours.weekday_text : [],
                        description: place.vicinity || place.formatted_address || '',
                        photos: place.photos ? place.photos.map(p => p.getUrl({ 'maxWidth': 400, 'maxHeight': 400 })) : [] // Adiciona URLs das fotos
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
};

// Helper functions
function getPlaceType(types) {
    if (types.includes('tourist_attraction') || types.includes('museum') || types.includes('park')) return 'attraction';
    if (types.includes('restaurant') || types.includes('food') || types.includes('meal_takeaway')) return 'restaurant';
    if (types.includes('lodging') || types.includes('hotel')) return 'hotel';
    // Return the first type if none of the above match, or a generic 'place'
    return types.length > 0 ? types[0] : 'place';
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
    const isInItinerary = itinerary[currentDay].find(p => p.id === place.place_id);

    const imageUrl = place.photos && place.photos.length > 0 ? place.photos[0] : 'https://via.placeholder.com/150x100?text=Sem+Foto';

    const content = `
        <div class="p-2 max-w-xs">
            <div class="mb-4">
                <img src="${imageUrl}" alt="${place.name}" class="w-full h-32 object-cover rounded-md shadow-sm">
            </div>
            <h3 class="font-bold text-gray-900 mb-1 text-lg">${place.name}</h3>
            <p class="text-sm text-gray-600 mb-2">${place.description}</p>
            <div class="flex items-center gap-2 mb-3">
                <span class="px-2 py-0.5 text-xs rounded-full font-medium ${getTypeColorClass(place.type)}">
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

// Utility functions
function getTypeColorClass(type) {
    const colors = {
        attraction: 'bg-purple-100 text-purple-800',
        restaurant: 'bg-orange-100 text-orange-800',
        hotel: 'bg-blue-100 text-blue-800'
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
}

function getTypeLabel(type) {
    const labels = {
        attraction: 'Atração',
        restaurant: 'Restaurante',
        hotel: 'Hotel'
    };
    return labels[type] || type.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase()); // Format other types
}

// Add to itinerary
function addToItinerary(placeId, selectedTime) {
    let place = places.find(p => p.id === placeId);

    // Se não encontrar, tenta usar o currentDetailedPlace
    if (!place && currentDetailedPlace && currentDetailedPlace.place_id === placeId) {
        place = {
            id: currentDetailedPlace.place_id,
            name: currentDetailedPlace.name,
            lat: currentDetailedPlace.geometry.location.lat(),
            lng: currentDetailedPlace.geometry.location.lng(),
            type: getPlaceType(currentDetailedPlace.types),
            rating: currentDetailedPlace.rating || 4.0,
            address: currentDetailedPlace.vicinity || currentDetailedPlace.formatted_address || '',
            description: currentDetailedPlace.vicinity || currentDetailedPlace.formatted_address || '',
            opening_hours: currentDetailedPlace.opening_hours ? currentDetailedPlace.opening_hours.weekday_text : [],
            photos: currentDetailedPlace.photos ? currentDetailedPlace.photos.map(p => p.getUrl({ 'maxWidth': 400, 'maxHeight': 400 })) : []
        };
    }

    if (place) {
        const placeWithTime = { ...place, time: selectedTime };
        if (!itinerary[currentDay].find(p => p.id === place.id)) {
            itinerary[currentDay].push(placeWithTime);
            updateItineraryDisplay();
            updateSuggestions();
            showNotification(`${place.name} (${selectedTime}) adicionado ao Dia ${currentDay}!`, 'success');
            infoWindow.close();
        } else {
            showNotification(`${place.name} já está no Dia ${currentDay}!`, 'info');
        }
    } else {
        console.error("Local não encontrado com ID:", placeId);
    }
}
window.addToItinerary = addToItinerary;

// Remove from itinerary
function removeFromItinerary(placeIdToRemove) {
    // Sempre comparar com o campo 'id'
    const placeRemoved = itinerary[currentDay].find(p => p.id === placeIdToRemove);

    if (placeRemoved) {
        itinerary[currentDay] = itinerary[currentDay].filter(p => p.id !== placeIdToRemove);
        updateItineraryDisplay();
        updateSuggestions();
        showNotification(`${placeRemoved.name} removido do Dia ${currentDay}`, 'info');
        infoWindow.close();
    } else {
        console.warn("Place not found in itinerary for removal:", placeIdToRemove);
    }
}
window.removeFromItinerary = removeFromItinerary;

// Update itinerary display
function updateItineraryDisplay() {
    const desktopContent = document.getElementById('itinerary-content');
    // ...existing code...
    const currentDayItinerary = itinerary[currentDay];

    const content = currentDayItinerary.length === 0
        ? `<div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
           <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
           </svg>
           <span class="font-medium mb-1">Nenhuma atividade ainda</span>
           <span class="text-sm text-center">Clique nos marcadores do mapa para adicionar atividades</span>
         </div>`
        : `<div class="space-y-4">
           ${currentDayItinerary.map((place, index) => {
                // Use place.place_id se existir, senão place.id
                const placeIdForModal = place.place_id ? place.place_id : place.id;
                return `
               <div class="p-4 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 cursor-pointer" onclick="openPlaceDetailsModal('${placeIdForModal}')">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                    ${index + 1}
                                    </span>
                                    <h4 class="font-semibold text-gray-900">${place.name}</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">${place.description}</p>
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium ${getTypeColorClass(place.type)}">
                                    ${getTypeLabel(place.type)}
                                    </span>
                                    <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    ${place.time ? place.time : 'Sem horário definido'}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    ${place.rating}
                                </div>
                        </div>
                        <button onclick="event.stopPropagation(); removeFromItinerary('${place.id}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        </button>
                    </div>
               </div>
           `;
           }).join('')}
         </div>`;

    desktopContent.innerHTML = content;
    // if (mobileContent) mobileContent.innerHTML = content.replace('h-full', 'h-32');
}

// Update suggestions
function updateSuggestions() {
    const filteredPlaces = places.filter(place => {
        // Suggestions should only show places not already in the current itinerary day
        const notInItinerary = !itinerary[currentDay].find(p => p.id === place.id);
        return notInItinerary;
    }).slice(0, 5); // Limit to 5 suggestions

    const suggestionsList = document.getElementById('suggestions-list');
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
                        <span class="text-xs text-gray-500 font-medium">${place.time ? place.time : ''}</span>
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
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    notification.className = `fixed top-6 right-6 ${bgColor} text-white px-6 py-3 rounded-xl shadow-2xl z-50 transform translate-x-full transition-transform duration-300 font-medium`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// --- Funções do Modal ---
async function openPlaceDetailsModal(placeId) {
    infoWindow.close(); // Fecha a InfoWindow ao abrir o modal

    const modal = document.getElementById('placeDetailsModal');
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-4">Carregando detalhes...</p>
        </div>
    `;

    modal.classList.remove('hidden');
    // Animação de entrada
    setTimeout(() => {
        modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('div').classList.add('scale-100', 'opacity-100');
    }, 50);


    const service = new google.maps.places.PlacesService(map);
    const request = {
        placeId: placeId,
        fields: ['name', 'formatted_address', 'types', 'rating', 'user_ratings_total', 'photos', 'opening_hours', 'website', 'formatted_phone_number', 'reviews', 'geometry', 'vicinity']
    };

    service.getDetails(request, (placeDetails, status) => {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            currentDetailedPlace = placeDetails; // Armazena o objeto completo para uso posterior

            const photosHtml = placeDetails.photos ?
                `<div class="flex space-x-2 overflow-x-auto pb-2">
                    ${placeDetails.photos.slice(0, 5).map(photo => `<img src="${photo.getUrl({ 'maxWidth': 300, 'maxHeight': 200 })}" class="h-32 w-auto object-cover rounded-md shadow-sm" alt="Foto de ${placeDetails.name}">`).join('')}
                </div>` : '';

            const openingHoursHtml = placeDetails.opening_hours ?
                `<div class="mt-4">
                    <h4 class="font-semibold text-gray-800">Horário de Funcionamento:</h4>
                    <ul class="text-sm text-gray-600 list-disc list-inside">
                        ${placeDetails.opening_hours.weekday_text.map(day => `<li>${day}</li>`).join('')}
                    </ul>
                </div>` : '';

            const reviewsHtml = placeDetails.reviews && placeDetails.reviews.length > 0 ?
                `<div class="mt-4">
                    <h4 class="font-semibold text-gray-800">Avaliações:</h4>
                    <div class="space-y-3 mt-2 max-h-48 overflow-y-auto pr-2">
                        ${placeDetails.reviews.slice(0, 3).map(review => `
                            <div class="border-b border-gray-100 pb-3 last:border-b-0">
                                <div class="flex items-center mb-1">
                                    <span class="font-medium text-gray-700">${review.author_name}</span>
                                    <div class="flex items-center text-xs text-gray-500 ml-2">
                                        ${'⭐'.repeat(review.rating)}
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600">${review.text}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>` : '';

            modalContent.innerHTML = `
                <div class="bg-white rounded-lg">
                    ${photosHtml}
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">${placeDetails.name}</h2>
                        <p class="text-sm text-gray-600 mb-4">${placeDetails.formatted_address}</p>

                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1 text-xs rounded-full font-medium ${getTypeColorClass(getPlaceType(placeDetails.types))}">
                                ${getTypeLabel(getPlaceType(placeDetails.types))}
                            </span>
                            ${placeDetails.rating ? `
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                ${placeDetails.rating} (${placeDetails.user_ratings_total || 0} avaliações)
                            </div>` : ''}
                            <span class="text-sm text-gray-500 font-medium">
                                ${placeDetails.opening_hours ? placeDetails.opening_hours.weekday_text.join(' | ') : ''}
                            </span>  
                        </div>

                        <p class="text-gray-700 mb-4">${placeDetails.vicinity || placeDetails.formatted_address || ''}</p>

                        ${placeDetails.website ? `<p class="text-blue-600 hover:underline mb-2"><a href="${placeDetails.website}" target="_blank">Site Oficial</a></p>` : ''}
                        ${placeDetails.formatted_phone_number ? `<p class="text-gray-700">Telefone: ${placeDetails.formatted_phone_number}</p>` : ''}

                        ${openingHoursHtml}
                        ${reviewsHtml}
                    </div>
                </div>
            `;
        } else {
            modalContent.innerHTML = `
                <div class="p-8 text-center text-red-500">
                    <p>Não foi possível carregar os detalhes deste lugar.</p>
                    <p class="text-sm text-gray-500">${status}</p>
                </div>
            `;
            console.error('Erro ao carregar detalhes do lugar:', status);
        }
    });
}

function closeModal() {
    const modal = document.getElementById('placeDetailsModal');
    // Animação de saída
    modal.querySelector('div').classList.remove('scale-100', 'opacity-100');
    modal.querySelector('div').classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        currentDetailedPlace = null; // Limpa o lugar detalhado
    }, 300); // Deve corresponder à duração da transição CSS
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Day tabs - Desktop
    document.querySelectorAll('.day-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            currentDay = parseInt(this.dataset.day);

            // Update active tab
            document.querySelectorAll('.day-tab').forEach(t => {
                t.className = 'day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700';
            });
            this.className = 'day-tab px-4 py-2 font-medium border-b-2 border-blue-500 text-blue-600';

            updateItineraryDisplay();
            updateSuggestions();
            if (infoWindow) infoWindow.close();
        });
    });

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



console.log('Explore.js loaded successfully');
</script>
@endsection