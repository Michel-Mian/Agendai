    // Lista de continentes para autocomplete local
const continents = [
    "África", "América", "América do Norte", "América do Sul", "Ásia", "Europa", "Oceania", "Antártida"
];

// Função para inicializar autocomplete Google Places
function initPlacesAutocomplete() {
    if (locationInput && typeof google !== 'undefined' && google.maps && google.maps.places) {
        if (!locationInput._autocompleteInitialized) {
            const autocomplete = new google.maps.places.Autocomplete(locationInput, {
                types: ['(regions)'],
            });
            autocomplete.addListener('place_changed', function() {
                if (typeof updateFilterCount === 'function') updateFilterCount();
            });
            locationInput._autocompleteInitialized = true;
        }
    }
}

// Inicializa autocomplete ao focar no input
if (locationInput) {
    locationInput.addEventListener('focus', initPlacesAutocomplete);

    // Autocomplete local para continentes
    locationInput.addEventListener('input', function() {
        const value = locationInput.value.trim().toLowerCase();
        let datalist = document.getElementById('continents-datalist');
        if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = 'continents-datalist';
            document.body.appendChild(datalist);
            locationInput.setAttribute('list', 'continents-datalist');
        }
        datalist.innerHTML = '';
        if (value.length > 0) {
            continents.forEach(cont => {
                if (cont.toLowerCase().startsWith(value)) {
                    const option = document.createElement('option');
                    option.value = cont;
                    datalist.appendChild(option);
                }
            });
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    // Lista de todos os tipos de lugares
    const placeTypes = [
        { id: 'accounting', name: 'Contabilidade', category: 'Serviços' },
        { id: 'airport', name: 'Aeroporto', category: 'Transporte' },
        { id: 'amusement_park', name: 'Parque de Diversões', category: 'Entretenimento' },
        { id: 'aquarium', name: 'Aquário', category: 'Entretenimento' },
        { id: 'art_gallery', name: 'Galeria de Arte', category: 'Cultura' },
        { id: 'atm', name: 'Caixa Eletrônico', category: 'Serviços' },
        { id: 'bakery', name: 'Padaria', category: 'Alimentação' },
        { id: 'bank', name: 'Banco', category: 'Serviços' },
        { id: 'bar', name: 'Bar', category: 'Alimentação' },
        { id: 'beauty_salon', name: 'Salão de Beleza', category: 'Serviços' },
        { id: 'bicycle_store', name: 'Loja de Bicicletas', category: 'Compras' },
        { id: 'book_store', name: 'Livraria', category: 'Compras' },
        { id: 'bowling_alley', name: 'Boliche', category: 'Entretenimento' },
        { id: 'bus_station', name: 'Estação de Ônibus', category: 'Transporte' },
        { id: 'cafe', name: 'Café', category: 'Alimentação' },
        { id: 'campground', name: 'Camping', category: 'Hospedagem' },
        { id: 'car_dealer', name: 'Concessionária', category: 'Serviços' },
        { id: 'car_rental', name: 'Aluguel de Carros', category: 'Serviços' },
        { id: 'car_repair', name: 'Oficina Mecânica', category: 'Serviços' },
        { id: 'car_wash', name: 'Lava-Jato', category: 'Serviços' },
        { id: 'casino', name: 'Cassino', category: 'Entretenimento' },
        { id: 'cemetery', name: 'Cemitério', category: 'Outros' },
        { id: 'church', name: 'Igreja', category: 'Religião' },
        { id: 'city_hall', name: 'Prefeitura', category: 'Governo' },
        { id: 'clothing_store', name: 'Loja de Roupas', category: 'Compras' },
        { id: 'convenience_store', name: 'Loja de Conveniência', category: 'Compras' },
        { id: 'courthouse', name: 'Tribunal', category: 'Governo' },
        { id: 'dentist', name: 'Dentista', category: 'Saúde' },
        { id: 'department_store', name: 'Loja de Departamentos', category: 'Compras' },
        { id: 'doctor', name: 'Médico', category: 'Saúde' },
        { id: 'drugstore', name: 'Farmácia', category: 'Saúde' },
        { id: 'electrician', name: 'Eletricista', category: 'Serviços' },
        { id: 'electronics_store', name: 'Loja de Eletrônicos', category: 'Compras' },
        { id: 'embassy', name: 'Embaixada', category: 'Governo' },
        { id: 'fire_station', name: 'Corpo de Bombeiros', category: 'Emergência' },
        { id: 'florist', name: 'Floricultura', category: 'Compras' },
        { id: 'funeral_home', name: 'Funerária', category: 'Serviços' },
        { id: 'furniture_store', name: 'Loja de Móveis', category: 'Compras' },
        { id: 'gas_station', name: 'Posto de Gasolina', category: 'Serviços' },
        { id: 'gym', name: 'Academia', category: 'Saúde' },
        { id: 'hair_care', name: 'Cabeleireiro', category: 'Serviços' },
        { id: 'hardware_store', name: 'Loja de Ferragens', category: 'Compras' },
        { id: 'hindu_temple', name: 'Templo Hindu', category: 'Religião' },
        { id: 'home_goods_store', name: 'Loja de Casa', category: 'Compras' },
        { id: 'hospital', name: 'Hospital', category: 'Saúde' },
        { id: 'insurance_agency', name: 'Seguradora', category: 'Serviços' },
        { id: 'jewelry_store', name: 'Joalheria', category: 'Compras' },
        { id: 'laundry', name: 'Lavanderia', category: 'Serviços' },
        { id: 'lawyer', name: 'Advogado', category: 'Serviços' },
        { id: 'library', name: 'Biblioteca', category: 'Cultura' },
        { id: 'light_rail_station', name: 'Estação de Trem Leve', category: 'Transporte' },
        { id: 'liquor_store', name: 'Loja de Bebidas', category: 'Compras' },
        { id: 'local_government_office', name: 'Órgão Público Local', category: 'Governo' },
        { id: 'locksmith', name: 'Chaveiro', category: 'Serviços' },
        { id: 'lodging', name: 'Hospedagem', category: 'Hospedagem' },
        { id: 'meal_delivery', name: 'Entrega de Comida', category: 'Alimentação' },
        { id: 'meal_takeaway', name: 'Comida para Viagem', category: 'Alimentação' },
        { id: 'mosque', name: 'Mesquita', category: 'Religião' },
        { id: 'movie_rental', name: 'Locadora de Filmes', category: 'Entretenimento' },
        { id: 'movie_theater', name: 'Cinema', category: 'Entretenimento' },
        { id: 'moving_company', name: 'Empresa de Mudanças', category: 'Serviços' },
        { id: 'museum', name: 'Museu', category: 'Cultura' },
        { id: 'night_club', name: 'Boate', category: 'Entretenimento' },
        { id: 'painter', name: 'Pintor', category: 'Serviços' },
        { id: 'park', name: 'Parque', category: 'Lazer' },
        { id: 'parking', name: 'Estacionamento', category: 'Serviços' },
        { id: 'pet_store', name: 'Pet Shop', category: 'Compras' },
        { id: 'pharmacy', name: 'Farmácia', category: 'Saúde' },
        { id: 'physiotherapist', name: 'Fisioterapeuta', category: 'Saúde' },
        { id: 'plumber', name: 'Encanador', category: 'Serviços' },
        { id: 'police', name: 'Polícia', category: 'Emergência' },
        { id: 'post_office', name: 'Correios', category: 'Serviços' },
        { id: 'primary_school', name: 'Escola Primária', category: 'Educação' },
        { id: 'real_estate_agency', name: 'Imobiliária', category: 'Serviços' },
        { id: 'restaurant', name: 'Restaurante', category: 'Alimentação' },
        { id: 'roofing_contractor', name: 'Empresa de Telhados', category: 'Serviços' },
        { id: 'rv_park', name: 'Parque de Trailers', category: 'Hospedagem' },
        { id: 'school', name: 'Escola', category: 'Educação' },
        { id: 'secondary_school', name: 'Escola Secundária', category: 'Educação' },
        { id: 'shoe_store', name: 'Loja de Sapatos', category: 'Compras' },
        { id: 'shopping_mall', name: 'Shopping Center', category: 'Compras' },
        { id: 'spa', name: 'Spa', category: 'Saúde' },
        { id: 'stadium', name: 'Estádio', category: 'Entretenimento' },
        { id: 'storage', name: 'Depósito', category: 'Serviços' },
        { id: 'store', name: 'Loja', category: 'Compras' },
        { id: 'subway_station', name: 'Estação de Metrô', category: 'Transporte' },
        { id: 'supermarket', name: 'Supermercado', category: 'Compras' },
        { id: 'synagogue', name: 'Sinagoga', category: 'Religião' },
        { id: 'taxi_stand', name: 'Ponto de Táxi', category: 'Transporte' },
        { id: 'tourist_attraction', name: 'Atração Turística', category: 'Turismo' },
        { id: 'train_station', name: 'Estação de Trem', category: 'Transporte' },
        { id: 'transit_station', name: 'Estação de Transporte', category: 'Transporte' },
        { id: 'travel_agency', name: 'Agência de Viagens', category: 'Serviços' },
        { id: 'university', name: 'Universidade', category: 'Educação' },
        { id: 'veterinary_care', name: 'Veterinário', category: 'Saúde' },
        { id: 'zoo', name: 'Zoológico', category: 'Entretenimento' }
    ];

    // Estado dos filtros
    let selectedPlaces = [];
    let filteredPlaces = [...placeTypes];

    // Elementos DOM
    const filterButton = document.getElementById('filterButton');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const cancelButton = document.getElementById('cancelButton');
    const applyFilters = document.getElementById('applyFilters');
    const clearFilters = document.getElementById('clearFilters');
    const selectAll = document.getElementById('selectAll');
    const deselectAll = document.getElementById('deselectAll');
    const placeSearch = document.getElementById('placeSearch');
    const placesGrid = document.getElementById('placesGrid');
    const filterCount = document.getElementById('filterCount');
    const locationInput = document.getElementById('locationInput');
    const radiusInput = document.getElementById('radiusInput');

    // Verificar se todos os elementos existem
    if (!filterButton || !modalOverlay || !placesGrid) {
        console.error('Elementos do modal de filtro não encontrados');
        return;
    }

    // Função para renderizar os tipos de lugares
    function renderPlaces() {
    placesGrid.innerHTML = filteredPlaces.map(place => `
        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors ${selectedPlaces.includes(place.id) ? 'bg-blue-50 border-blue-300' : ''}">
            <input 
                type="checkbox" 
                value="${place.id}"
                ${selectedPlaces.includes(place.id) ? 'checked' : ''}
                class="mr-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                data-place-id="${place.id}"
            />
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">${place.name}</div>
                <div class="text-xs text-gray-500">${place.category}</div>
            </div>
        </label>
    `).join('');

    // Adicionar event listeners para os checkboxes
    placesGrid.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            togglePlace(this.dataset.placeId);
        });
    });
}

    // Função para alternar seleção de lugar
    function togglePlace(placeId) {
        if (selectedPlaces.includes(placeId)) {
            selectedPlaces = selectedPlaces.filter(id => id !== placeId);
        } else {
            selectedPlaces.push(placeId);
        }
        updateFilterCount();
        renderPlaces();
    }

    // Função para atualizar contador de filtros
    function updateFilterCount() {
        const totalFilters = selectedPlaces.length + 
                            (locationInput && locationInput.value.trim() ? 1 : 0) + 
                            (radiusInput && radiusInput.value.trim() ? 1 : 0);
        
        if (filterCount) {
            if (totalFilters > 0) {
                filterCount.textContent = totalFilters;
                filterCount.classList.remove('hidden');
            } else {
                filterCount.classList.add('hidden');
            }
        }
    }

    // Função para filtrar lugares por busca
    function filterPlacesBySearch() {
        if (!placeSearch) return;
        
        const searchTerm = placeSearch.value.toLowerCase();
        filteredPlaces = placeTypes.filter(place => 
            place.name.toLowerCase().includes(searchTerm) ||
            place.category.toLowerCase().includes(searchTerm)
        );
        renderPlaces();
    }

    // Event Listeners
    filterButton.addEventListener('click', function() {
        modalOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });

    if (selectAll) {
        selectAll.addEventListener('click', function() {
            selectedPlaces = filteredPlaces.map(place => place.id);
            updateFilterCount();
            renderPlaces();
        });
    }

    if (deselectAll) {
        deselectAll.addEventListener('click', function() {
            selectedPlaces = [];
            updateFilterCount();
            renderPlaces();
        });
    }

    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            selectedPlaces = [];
            if (locationInput) locationInput.value = '';
            if (radiusInput) radiusInput.value = '';
            if (placeSearch) placeSearch.value = '';
            filteredPlaces = [...placeTypes];
            updateFilterCount();
            renderPlaces();
        });
    }

    if (applyFilters) {
        applyFilters.addEventListener('click', function() {
            const filters = {
                places: selectedPlaces,
                location: locationInput ? locationInput.value.trim() : '',
                radius: radiusInput ? radiusInput.value.trim() : ''
            };
            
            console.log('Filtros aplicados:', filters);
            
            // Integração com o código existente
            if (typeof window.applyMapFilters === 'function') {
                window.applyMapFilters(filters);
            }
            
            // Emitir evento customizado
            const filterEvent = new CustomEvent('filtersApplied', { 
                detail: filters 
            });
            document.dispatchEvent(filterEvent);
            
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }

    if (placeSearch) {
        placeSearch.addEventListener('input', filterPlacesBySearch);
    }

    if (locationInput) {
        locationInput.addEventListener('input', updateFilterCount);
    }

    if (radiusInput) {
        radiusInput.addEventListener('input', updateFilterCount);
    }

    // Tecla ESC para fechar modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modalOverlay.classList.contains('hidden')) {
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });

    // Inicializar
    renderPlaces();
    updateFilterCount();
});