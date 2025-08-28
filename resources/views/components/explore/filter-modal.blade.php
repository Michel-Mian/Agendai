<button id="filterButton" class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm w-full sm:w-auto">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
    </svg>
    Filtros
    <span id="filterCount" class="ml-2 px-2 py-1 bg-blue-500 text-xs rounded-full font-medium hidden">0</span>
</button>

<!-- Modal Overlay -->
<div id="modalOverlay" class="fixed inset-0 z-50 bg-black/40 flex justify-center items-end sm:items-center transition-all duration-300 hidden">
    <!-- Modal Content -->
    <div class="explore-filter-modal-base">
        <!--<div
        class="w-full max-w-[480px] sm:max-w-xl bg-white rounded-t-2xl sm:rounded-xl shadow-xl flex flex-col
               transition-all duration-300
               overflow-hidden
               border-t border-gray-200
               fixed bottom-0 left-1/2 -translate-x-1/2 sm:static sm:translate-x-0
               "
        style="min-height: 35vh; max-height: 75vh;"
        style="min-width: 15vw; max-width: 75vw;"
    >-->
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Filtros de Busca</h2>
                    <p class="text-xs sm:text-sm text-gray-600">Personalize sua experiência de exploração</p>
                </div>
            </div>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <div class="space-y-2">
                    <label for="locationInput" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Localização
                    </label>
                    <input
                        type="text"
                        id="locationInput"
                        placeholder="Ex: Rio de Janeiro..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        list="continents-datalist" />
                    <datalist id="continents-datalist"></datalist>
                    <p class="text-xs text-gray-500">Digite o nome da cidade ou país</p>
                </div>
                <div class="space-y-2">
                    <label for="radiusInput" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Raio de Busca (Metros)
                    </label>
                    <input
                        type="number"
                        id="radiusInput"
                        placeholder="Ex: 5"
                        min="1"
                        max="50000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                    <p class="text-xs text-gray-500">Entre 1 e 50.000 metros</p>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-2">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="0" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Tipos de Lugares</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Selecione os tipos que deseja encontrar</p>
                        </div>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button id="selectAll" class="px-3 py-2 text-xs sm:text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 font-medium">
                            Selecionar Todos
                        </button>
                        <button id="deselectAll" class="px-3 py-2 text-xs sm:text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 font-medium">
                            Desmarcar Todos
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            id="placeSearch"
                            placeholder="Buscar tipo de lugar..."
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                    </div>
                </div>

                <div id="placesGrid" class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 gap-2 max-h-32 sm:max-h-72 overflow-y-auto border border-gray-200 rounded-lg p-2 break-words">
                    <!-- Itens renderizados via JS -->
                </div>
            </div>
        </div>


        <!-- Footer -->
        <div class="flex flex-row flex-wrap items-center justify-center sm:justify-end gap-2 p-4 border-t border-gray-100">
            <button id="clearFilters" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 text-xs sm:text-sm">
                Limpar
            </button>
            <button id="cancelButton" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 text-xs sm:text-sm">
                Cancelar
            </button>
            <button id="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-xs sm:text-sm shadow-sm">
                Aplicar
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lista de todos os tipos de lugares (expanded for more options)
        const placeTypes = [{
                id: 'accounting',
                name: 'Contabilidade',
                category: 'Serviços'
            },
            {
                id: 'airport',
                name: 'Aeroporto',
                category: 'Transporte'
            },
            {
                id: 'amusement_park',
                name: 'Parque de Diversões',
                category: 'Entretenimento'
            },
            {
                id: 'aquarium',
                name: 'Aquário',
                category: 'Entretenimento'
            },
            {
                id: 'art_gallery',
                name: 'Galeria de Arte',
                category: 'Cultura'
            },
            {
                id: 'atm',
                name: 'Caixa Eletrônico',
                category: 'Serviços'
            },
            {
                id: 'bakery',
                name: 'Padaria',
                category: 'Alimentação'
            },
            {
                id: 'bank',
                name: 'Banco',
                category: 'Serviços'
            },
            {
                id: 'bar',
                name: 'Bar',
                category: 'Alimentação'
            },
            {
                id: 'beauty_salon',
                name: 'Salão de Beleza',
                category: 'Serviços'
            },
            {
                id: 'bicycle_store',
                name: 'Loja de Bicicletas',
                category: 'Compras'
            },
            {
                id: 'book_store',
                name: 'Livraria',
                category: 'Compras'
            },
            {
                id: 'bowling_alley',
                name: 'Boliche',
                category: 'Entretenimento'
            },
            {
                id: 'bus_station',
                name: 'Estação de Ônibus',
                category: 'Transporte'
            },
            {
                id: 'cafe',
                name: 'Café',
                category: 'Alimentação'
            },
            {
                id: 'campground',
                name: 'Camping',
                category: 'Hospedagem'
            },
            {
                id: 'car_dealer',
                name: 'Concessionária',
                category: 'Serviços'
            },
            {
                id: 'car_rental',
                name: 'Aluguel de Carros',
                category: 'Serviços'
            },
            {
                id: 'car_repair',
                name: 'Oficina Mecânica',
                category: 'Serviços'
            },
            {
                id: 'car_wash',
                name: 'Lava-Jato',
                category: 'Serviços'
            },
            {
                id: 'casino',
                name: 'Cassino',
                category: 'Entretenimento'
            },
            {
                id: 'cemetery',
                name: 'Cemitério',
                category: 'Outros'
            },
            {
                id: 'church',
                name: 'Igreja',
                category: 'Religião'
            },
            {
                id: 'city_hall',
                name: 'Prefeitura',
                category: 'Governo'
            },
            {
                id: 'clothing_store',
                name: 'Loja de Roupas',
                category: 'Compras'
            },
            {
                id: 'convenience_store',
                name: 'Loja de Conveniência',
                category: 'Compras'
            },
            {
                id: 'courthouse',
                name: 'Tribunal',
                category: 'Governo'
            },
            {
                id: 'dentist',
                name: 'Dentista',
                category: 'Saúde'
            },
            {
                id: 'department_store',
                name: 'Loja de Departamentos',
                category: 'Compras'
            },
            {
                id: 'doctor',
                name: 'Médico',
                category: 'Saúde'
            },
            {
                id: 'drugstore',
                name: 'Farmácia',
                category: 'Saúde'
            },
            {
                id: 'electrician',
                name: 'Eletricista',
                category: 'Serviços'
            },
            {
                id: 'electronics_store',
                name: 'Loja de Eletrônicos',
                category: 'Compras'
            },
            {
                id: 'embassy',
                name: 'Embaixada',
                category: 'Governo'
            },
            {
                id: 'fire_station',
                name: 'Corpo de Bombeiros',
                category: 'Emergência'
            },
            {
                id: 'florist',
                name: 'Floricultura',
                category: 'Compras'
            },
            {
                id: 'funeral_home',
                name: 'Funerária',
                category: 'Serviços'
            },
            {
                id: 'furniture_store',
                name: 'Loja de Móveis',
                category: 'Compras'
            },
            {
                id: 'gas_station',
                name: 'Posto de Gasolina',
                category: 'Serviços'
            },
            {
                id: 'gym',
                name: 'Academia',
                category: 'Saúde'
            },
            {
                id: 'hair_care',
                name: 'Cabeleireiro',
                category: 'Serviços'
            },
            {
                id: 'hardware_store',
                name: 'Loja de Ferragens',
                category: 'Compras'
            },
            {
                id: 'hindu_temple',
                name: 'Templo Hindu',
                category: 'Religião'
            },
            {
                id: 'home_goods_store',
                name: 'Loja de Casa',
                category: 'Compras'
            },
            {
                id: 'hospital',
                name: 'Hospital',
                category: 'Saúde'
            },
            {
                id: 'insurance_agency',
                name: 'Seguradora',
                category: 'Serviços'
            },
            {
                id: 'jewelry_store',
                name: 'Joalheria',
                category: 'Compras'
            },
            {
                id: 'laundry',
                name: 'Lavanderia',
                category: 'Serviços'
            },
            {
                id: 'lawyer',
                name: 'Advogado',
                category: 'Serviços'
            },
            {
                id: 'library',
                name: 'Biblioteca',
                category: 'Cultura'
            },
            {
                id: 'light_rail_station',
                name: 'Estação de Trem Leve',
                category: 'Transporte'
            },
            {
                id: 'liquor_store',
                name: 'Loja de Bebidas',
                category: 'Compras'
            },
            {
                id: 'local_government_office',
                name: 'Órgão Público Local',
                category: 'Governo'
            },
            {
                id: 'locksmith',
                name: 'Chaveiro',
                category: 'Serviços'
            },
            {
                id: 'lodging',
                name: 'Hospedagem',
                category: 'Hospedagem'
            },
            {
                id: 'meal_delivery',
                name: 'Entrega de Comida',
                category: 'Alimentação'
            },
            {
                id: 'meal_takeaway',
                name: 'Comida para Viagem',
                category: 'Alimentação'
            },
            {
                id: 'mosque',
                name: 'Mesquita',
                category: 'Religião'
            },
            {
                id: 'movie_rental',
                name: 'Locadora de Filmes',
                category: 'Entretenimento'
            },
            {
                id: 'movie_theater',
                name: 'Cinema',
                category: 'Entretenimento'
            },
            {
                id: 'moving_company',
                name: 'Empresa de Mudanças',
                category: 'Serviços'
            },
            {
                id: 'museum',
                name: 'Museu',
                category: 'Cultura'
            },
            {
                id: 'night_club',
                name: 'Boate',
                category: 'Entretenimento'
            },
            {
                id: 'painter',
                name: 'Pintor',
                category: 'Serviços'
            },
            {
                id: 'park',
                name: 'Parque',
                category: 'Lazer'
            },
            {
                id: 'parking',
                name: 'Estacionamento',
                category: 'Serviços'
            },
            {
                id: 'pet_store',
                name: 'Pet Shop',
                category: 'Compras'
            },
            {
                id: 'pharmacy',
                name: 'Farmácia',
                category: 'Saúde'
            },
            {
                id: 'physiotherapist',
                name: 'Fisioterapeuta',
                category: 'Saúde'
            },
            {
                id: 'plumber',
                name: 'Encanador',
                category: 'Serviços'
            },
            {
                id: 'police',
                name: 'Polícia',
                category: 'Emergência'
            },
            {
                id: 'post_office',
                name: 'Correios',
                category: 'Serviços'
            },
            {
                id: 'primary_school',
                name: 'Escola Primária',
                category: 'Educação'
            },
            {
                id: 'real_estate_agency',
                name: 'Imobiliária',
                category: 'Serviços'
            },
            {
                id: 'restaurant',
                name: 'Restaurante',
                category: 'Alimentação'
            },
            {
                id: 'roofing_contractor',
                name: 'Empresa de Telhados',
                category: 'Serviços'
            },
            {
                id: 'rv_park',
                name: 'Parque de Trailers',
                category: 'Hospedagem'
            },
            {
                id: 'school',
                name: 'Escola',
                category: 'Educação'
            },
            {
                id: 'secondary_school',
                name: 'Escola Secundária',
                category: 'Educação'
            },
            {
                id: 'shoe_store',
                name: 'Loja de Sapatos',
                category: 'Compras'
            },
            {
                id: 'shopping_mall',
                name: 'Shopping Center',
                category: 'Compras'
            },
            {
                id: 'spa',
                name: 'Spa',
                category: 'Saúde'
            },
            {
                id: 'stadium',
                name: 'Estádio',
                category: 'Entretenimento'
            },
            {
                id: 'storage',
                name: 'Depósito',
                category: 'Serviços'
            },
            {
                id: 'store',
                name: 'Loja',
                category: 'Compras'
            },
            {
                id: 'subway_station',
                name: 'Estação de Metrô',
                category: 'Transporte'
            },
            {
                id: 'supermarket',
                name: 'Supermercado',
                category: 'Compras'
            },
            {
                id: 'synagogue',
                name: 'Sinagoga',
                category: 'Religião'
            },
            {
                id: 'taxi_stand',
                name: 'Ponto de Táxi',
                category: 'Transporte'
            },
            {
                id: 'tourist_attraction',
                name: 'Atração Turística',
                category: 'Turismo'
            },
            {
                id: 'train_station',
                name: 'Estação de Trem',
                category: 'Transporte'
            },
            {
                id: 'transit_station',
                name: 'Estação de Transporte',
                category: 'Transporte'
            },
            {
                id: 'travel_agency',
                name: 'Agência de Viagens',
                category: 'Serviços'
            },
            {
                id: 'university',
                name: 'Universidade',
                category: 'Educação'
            },
            {
                id: 'veterinary_care',
                name: 'Veterinário',
                category: 'Saúde'
            },
            {
                id: 'zoo',
                name: 'Zoológico',
                category: 'Entretenimento'
            }
        ];

        // Estado dos filtros
        let selectedPlaces = [];
        let filteredPlaces = [...placeTypes];

        // Elementos DOM
        const filterButton = document.getElementById('filterButton');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModal = document.getElementById('closeModal');
        const cancelButton = document.getElementById('cancelButton');
        const applyFiltersBtn = document.getElementById('applyFilters'); // Renamed to avoid conflict
        const clearFilters = document.getElementById('clearFilters');
        const selectAll = document.getElementById('selectAll');
        const deselectAll = document.getElementById('deselectAll');
        const placeSearch = document.getElementById('placeSearch');
        const placesGrid = document.getElementById('placesGrid');
        const filterCount = document.getElementById('filterCount');
        const locationInput = document.getElementById('locationInput');
        const radiusInput = document.getElementById('radiusInput');

        // Verificar se todos os elementos existem
        if (!filterButton || !modalOverlay || !placesGrid || !applyFiltersBtn) {
            console.error('Elementos do modal de filtro não encontrados');
            return;
        }

        // Function to render the place types
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

            // Add event listeners for the checkboxes
            placesGrid.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    togglePlace(this.dataset.placeId);
                });
            });
        }

        // Function to toggle place selection
        function togglePlace(placeId) {
            if (selectedPlaces.includes(placeId)) {
                selectedPlaces = selectedPlaces.filter(id => id !== placeId);
            } else {
                selectedPlaces.push(placeId);
            }
            updateFilterCount();
            renderPlaces();
        }

        // Function to update filter count
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

        // Function to filter places by search
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
            initPlacesAutocomplete(); // Ensure autocomplete is initialized when modal opens
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

        // Apply Filters button click event
        applyFiltersBtn.addEventListener('click', function() {
            const filters = {
                places: selectedPlaces,
                location: locationInput ? locationInput.value.trim() : '',
                radius: radiusInput ? radiusInput.value.trim() : ''
            };

            console.log('Filtros aplicados:', filters);

            // Call the global function to apply filters to the map
            if (typeof window.applyMapFilters === 'function') {
                window.applyMapFilters(filters);
            }

            modalOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });

        if (placeSearch) {
            placeSearch.addEventListener('input', filterPlacesBySearch);
        }

        if (locationInput) {
            locationInput.addEventListener('input', updateFilterCount);
            // Autocomplete local for continents
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

    // Global function to handle filter application
    window.applyMapFilters = function(filters) {
        console.log('🔍 applyMapFilters chamada com:', filters);
        
        if (!map || !google || !google.maps || !google.maps.places) {
            console.error('❌ Mapa ou Google Places API não disponível');
            return;
        }

        const service = new google.maps.places.PlacesService(map);
        const radius = filters.radius ? parseInt(filters.radius) : 10000;

        // Função para aplicar busca com tipos específicos
        function searchWithTypes(location, searchTypes) {
            console.log('🎯 Buscando tipos:', searchTypes, 'em:', location);
            
            // Limpar marcadores existentes
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            places = [];

            let allResults = [];
            let pendingSearches = searchTypes.length;

            if (pendingSearches === 0) {
                console.warn('⚠️ Nenhum tipo de lugar para buscar');
                return;
            }

            searchTypes.forEach(type => {
                console.log(`🔍 Buscando tipo: ${type}`);
                
                service.nearbySearch({
                    location: location,
                    radius: radius,
                    type: type
                }, (results, status) => {
                    console.log(`📍 Resultado para ${type}:`, status, results?.length || 0, 'lugares');
                    
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        if (results && results.length > 0) {
                            allResults = allResults.concat(results);
                            console.log(`✅ Adicionados ${results.length} lugares para ${type}`);
                        }
                    } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                        console.log(`ℹ️ Nenhum resultado para ${type}`);
                    } else {
                        console.error(`❌ Erro na busca para ${type}:`, status);
                    }
                    
                    pendingSearches--;
                    console.log(`⏳ Buscas restantes: ${pendingSearches}`);
                    
                    if (pendingSearches === 0) {
                        console.log('🎉 Todas as buscas concluídas. Total de resultados:', allResults.length);
                        
                        // Remove duplicatas
                        const uniquePlaces = [];
                        const ids = new Set();
                        allResults.forEach(place => {
                            if (!ids.has(place.place_id)) {
                                ids.add(place.place_id);
                                uniquePlaces.push(place);
                            }
                        });

                        console.log(`🔗 Após remoção de duplicatas: ${uniquePlaces.length} lugares únicos`);

                        // Atualizar array global places
                        places = uniquePlaces.map(place => ({
                            id: place.place_id,
                            name: place.name,
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                            type: getPlaceType(place.types),
                            rating: place.rating || 4.0,
                            address: place.vicinity || place.formatted_address || '',
                            place_id: place.place_id,
                            description: place.vicinity || place.formatted_address || '',
                            opening_hours: place.opening_hours ? place.opening_hours.weekday_text : [],
                            photos: place.photos ? place.photos.map(p => p.getUrl({
                                'maxWidth': 400,
                                'maxHeight': 400
                            })) : []
                        }));

                        console.log('📊 Places array atualizado:', places.length, 'lugares');
                        
                        // Atualizar marcadores e sugestões
                        if (typeof addMarkersToMap === 'function') {
                            addMarkersToMap();
                            console.log('🗺️ Marcadores adicionados ao mapa');
                        }
                        
                        if (typeof updateSuggestions === 'function') {
                            updateSuggestions();
                            console.log('💡 Sugestões atualizadas');
                        }

                        // Mostrar notificação de sucesso
                        const message = `🎯 Encontrados ${places.length} lugares para ${filters.objective || 'filtros aplicados'}!`;
                        if (typeof showNotification === 'function') {
                            showNotification(message, 'success');
                        }
                    }
                });
            });
        }

        // Se há localização especificada, geocodificar primeiro
        if (filters.location && filters.location.trim()) {
            console.log('📍 Geocodificando localização:', filters.location);
            
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                address: filters.location
            }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const newLocation = results[0].geometry.location;
                    console.log('✅ Localização encontrada:', newLocation.lat(), newLocation.lng());
                    
                    map.setCenter(newLocation);
                    map.setZoom(13);
                    
                    const searchTypes = filters.places && filters.places.length > 0 
                        ? filters.places 
                        : ['tourist_attraction'];
                    
                    searchWithTypes(newLocation, searchTypes);
                } else {
                    console.error('❌ Falha na geocodificação:', status);
                    if (typeof showNotification === 'function') {
                        showNotification('Localização não encontrada. Usando centro atual do mapa.', 'warning');
                    }
                    
                    // Usar centro atual do mapa como fallback
                    const searchTypes = filters.places && filters.places.length > 0 
                        ? filters.places 
                        : ['tourist_attraction'];
                    
                    searchWithTypes(map.getCenter(), searchTypes);
                }
            });
        } else {
            // Usar centro atual do mapa
            console.log('📍 Usando centro atual do mapa');
            
            const searchTypes = filters.places && filters.places.length > 0 
                ? filters.places 
                : ['tourist_attraction'];
            
            searchWithTypes(map.getCenter(), searchTypes);
        }
    };
</script>