// Global variables
let map;
let markers = [];
let places = [];
let infoWindow;
let currentDay = 1;
let currentCategory = 'all';
let itinerary = { 1: [], 2: [], 3: [] };

// Initialize Google Map
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 48.86447217071428, lng: 2.3484048697423208 },
        zoom: 12,
        zoomControl: true,
        mapTypeControl: false,
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
            location: { lat: 48.86447217071428, lng: 2.3484048697423208 },
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
                        place_id: place.place_id,
                        description: generateDescription(place.name, place.types),
                        duration: generateDuration(place.types)
                    });
                });
                addMarkersToMap(); 
                updateSuggestions();
            }
        }
    );

    infoWindow = new google.maps.InfoWindow();
}

// Helper functions
function getPlaceType(types) {
    if (types.includes('tourist_attraction') || types.includes('museum') || types.includes('park')) return 'attraction';
    if (types.includes('restaurant') || types.includes('food') || types.includes('meal_takeaway')) return 'restaurant';
    if (types.includes('lodging') || types.includes('hotel')) return 'hotel';
    return 'attraction';
}

function generateDescription(name, types) {
    const descriptions = {
        attraction: `Uma atração imperdível em ${name}`,
        restaurant: `Deliciosa experiência gastronômica em ${name}`,
        hotel: `Hospedagem confortável em ${name}`
    };
    const type = getPlaceType(types);
    return descriptions[type] || `Descubra ${name}`;
}

function generateDuration(types) {
    const durations = {
        attraction: ['1-2h', '2-3h', '30min-1h'],
        restaurant: ['1-2h', '45min-1h30'],
        hotel: ['Check-in']
    };
    const type = getPlaceType(types);
    const options = durations[type] || ['1h'];
    return options[Math.floor(Math.random() * options.length)];
}

// Add markers to map
function addMarkersToMap() {
    // Clear existing markers
    markers.forEach(marker => marker.setMap(null));
    markers = [];

    places.forEach(place => {
        if (currentCategory === 'all' || place.type === currentCategory) {
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
        }
    });
}

// Get marker icon based on place type
function getMarkerIcon(type) {
    const colors = {
        attraction: '#8B5CF6', // purple
        restaurant: '#F97316', // orange
        hotel: '#2563EB'       // blue
    };
    
    return {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: colors[type] || '#6B7280',
        fillOpacity: 1,
        strokeColor: '#FFFFFF',
        strokeWeight: 3,
        scale: 10
    };
}

// Show place info in InfoWindow
function showPlaceInfo(place, marker) {
    const isInItinerary = itinerary[currentDay].find(p => p.id === place.id);
    
    const content = `
        <div class="p-4 max-w-xs">
            <h3 class="font-bold text-gray-900 mb-2 text-lg">${place.name}</h3>
            <p class="text-sm text-gray-600 mb-3">${place.description}</p>
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 text-xs rounded-full font-medium ${getTypeColorClass(place.type)}">
                    ${getTypeLabel(place.type)}
                </span>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    ${place.rating}
                </div>
                <span class="text-sm text-gray-500 font-medium">${place.duration}</span>
            </div>
            ${isInItinerary 
                ? `<button onclick="removeFromItinerary('${place.id}')" class="w-full px-4 py-2 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                     ❌ Remover do Dia ${currentDay}
                   </button>`
                : `<button onclick="addToItinerary('${place.id}')" class="w-full px-4 py-2 text-sm bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all font-medium">
                     ➕ Adicionar ao Dia ${currentDay}
                   </button>`
            }
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
    return labels[type] || type;
}

// Add to itinerary
function addToItinerary(placeId) {
    const place = places.find(p => p.id === placeId);
    if (place && !itinerary[currentDay].find(p => p.id === place.id)) {
        itinerary[currentDay].push(place);
        updateItineraryDisplay();
        updateSuggestions();
        showNotification(`${place.name} adicionado ao Dia ${currentDay}!`, 'success');
        infoWindow.close();
    }
}

// Remove from itinerary
function removeFromItinerary(placeId) {
    const place = places.find(p => p.id === placeId);
    itinerary[currentDay] = itinerary[currentDay].filter(p => p.id !== placeId);
    updateItineraryDisplay();
    updateSuggestions();
    showNotification(`${place?.name || 'Item'} removido do Dia ${currentDay}`, 'info');
    infoWindow.close();
}

// Update itinerary display
function updateItineraryDisplay() {
    const desktopContent = document.getElementById('itinerary-content');
    const mobileContent = document.getElementById('mobile-itinerary-content');
    
    const content = itinerary[currentDay].length === 0 
        ? `<div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
         <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
         </svg>
         <span class="font-medium mb-1">Nenhuma atividade ainda</span>
         <span class="text-sm text-center">Clique nos marcadores do mapa para adicionar atividades</span>
       </div>`
        : `<div class="space-y-4">
         ${itinerary[currentDay].map((place, index) => `
             <div class="p-4 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-all duration-200">
                 <div class="flex items-start justify-between">
                     <div class="flex-1">
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
                                 ${place.duration}
                             </div>
                             <div class="flex items-center text-sm text-gray-500">
                                 <svg class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                                     <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                 </svg>
                                 ${place.rating}
                             </div>
                         </div>
                     </div>
                     <button onclick="removeFromItinerary('${place.id}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                         </svg>
                     </button>
                 </div>
             </div>
         `).join('')}
       </div>`;

    desktopContent.innerHTML = content;
    mobileContent.innerHTML = content.replace('h-full', 'h-32');
}

// Update suggestions
function updateSuggestions() {
    const filteredPlaces = places.filter(place => {
        const matchesCategory = currentCategory === 'all' || place.type === currentCategory;
        const notInItinerary = !itinerary[currentDay].find(p => p.id === place.id);
        return matchesCategory && notInItinerary;
    }).slice(0, 5); // Limit to 5 suggestions

    const suggestionsList = document.getElementById('suggestions-list');
    suggestionsList.innerHTML = filteredPlaces.map(place => `
        <div class="p-3 border border-gray-200 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 cursor-pointer transition-all duration-200 hover:shadow-md" onclick="addToItinerary('${place.id}')">
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
                        <span class="text-xs text-gray-500 font-medium">${place.duration}</span>
                    </div>
                </div>
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
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

    // Day tabs - Mobile
    document.querySelectorAll('.mobile-day-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            currentDay = parseInt(this.dataset.day);
            
            // Update active tab
            document.querySelectorAll('.mobile-day-tab').forEach(t => {
                t.className = 'mobile-day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500';
            });
            this.className = 'mobile-day-tab px-4 py-2 font-medium border-b-2 border-blue-500 text-blue-600';
            
            updateItineraryDisplay();
        });
    });

    // Category buttons - Desktop
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentCategory = this.dataset.category;
            
            // Update active category
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
            
            addMarkersToMap();
            updateSuggestions();
        });
    });

    // Category buttons - Mobile
    document.querySelectorAll('.mobile-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentCategory = this.dataset.category;
            
            // Update active category
            document.querySelectorAll('.mobile-category-btn').forEach(b => {
                const category = b.dataset.category;
                if (category === 'all') {
                    b.className = 'mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-gray-300 text-gray-700';
                } else if (category === 'attraction') {
                    b.className = 'mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-purple-300 text-purple-700';
                } else if (category === 'restaurant') {
                    b.className = 'mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-orange-300 text-orange-700';
                } else if (category === 'hotel') {
                    b.className = 'mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 border-blue-300 text-blue-700';
                }
            });
            
            if (currentCategory === 'all') {
                this.className = 'mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 bg-gradient-to-r from-gray-600 to-gray-700 text-white border-transparent';
            } else {
                const color = this.dataset.category === 'attraction' ? 'purple' : 
                             this.dataset.category === 'restaurant' ? 'orange' : 'blue';
                this.className = `mobile-category-btn px-4 py-2 text-sm rounded-xl border-2 transition-all duration-200 bg-gradient-to-r from-${color}-600 to-${color}-700 text-white border-transparent`;
            }
            
            addMarkersToMap();
        });
    });

    // Mobile panel toggle
    const mobileToggle = document.getElementById('mobile-panel-toggle');
    const mobileOverlay = document.getElementById('mobile-panel-overlay');
    const mobilePanel = document.getElementById('mobile-panel');

    if (mobileToggle && mobileOverlay && mobilePanel) {
        mobileToggle.addEventListener('click', function() {
            mobileOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobilePanel.classList.remove('translate-y-full');
            }, 10);
        });

        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                mobilePanel.classList.add('translate-y-full');
                setTimeout(() => {
                    mobileOverlay.classList.add('hidden');
                }, 300);
            }
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    
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
            if (currentCategory === 'all' || place.type === currentCategory) {
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
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            if (e.target.value.trim() === '') {
                addMarkersToMap();
            } else {
                handleSearch(e.target.value);
            }
        });
    }
    
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', (e) => {
            if (e.target.value.trim() === '') {
                addMarkersToMap();
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

// Global function to handle filter application
window.applyMapFilters = function(filters) {
    console.log('Filtros aplicados:', filters);
    
    if (filters.location && filters.location.trim()) {
        // Geocode the location
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: filters.location }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const location = results[0].geometry.location;
                map.setCenter(location);
                map.setZoom(13);
                
                // Search for places in the new location
                const service = new google.maps.places.PlacesService(map);
                const radius = filters.radius ? parseInt(filters.radius) * 1000 : 5000; // Convert km to meters
                
                let searchTypes = ['tourist_attraction', 'restaurant', 'lodging'];
                if (filters.places && filters.places.length > 0) {
                    searchTypes = filters.places;
                }
                
                service.nearbySearch({
                    location: location,
                    radius: radius,
                    type: searchTypes.join('|')
                }, (results, status) => {
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
                                place_id: place.place_id,
                                description: generateDescription(place.name, place.types),
                                duration: generateDuration(place.types)
                            });
                        });
                        addMarkersToMap();
                        updateSuggestions();
                        showNotification(`🗺️ Encontrados ${places.length} lugares em ${filters.location}!`, 'success');
                    }
                });
            } else {
                showNotification('Localização não encontrada. Tente novamente.', 'error');
            }
        });
    }
};

window.initMap = initMap;

console.log('Explore.js loaded successfully');