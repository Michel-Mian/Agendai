<!-- Botão de Filtro melhorado -->
<button id="filterButton" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
    </svg>
    Filtros
    <span id="filterCount" class="ml-2 px-2 py-1 bg-blue-500 text-xs rounded-full font-medium hidden">0</span>
</button>

<!-- Modal Overlay melhorado -->
<div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-gray-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Filtros de Busca</h2>
                        <p class="text-sm text-gray-600">Personalize sua experiência de exploração</p>
                    </div>
                </div>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                <!-- Localização e Raio melhorados -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-2">
                        <label for="locationInput" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Localização
                        </label>
                        <input 
                            type="text" 
                            id="locationInput"
                            placeholder="Ex: Rio de Janeiro..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p class="text-xs text-gray-500">Digite o nome da cidade ou país</p>
                    </div>
                    <div class="space-y-2">
                        <label for="radiusInput" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Raio de Busca (km)
                        </label>
                        <input 
                            type="number" 
                            id="radiusInput"
                            placeholder="Ex: 5"
                            min="1"
                            max="50"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p class="text-xs text-gray-500">Entre 1 e 50 quilômetros</p>
                    </div>
                </div>

                <!-- Tipos de Lugares melhorados -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Tipos de Lugares</h3>
                                <p class="text-sm text-gray-600">Selecione os tipos que deseja encontrar</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button id="selectAll" class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 font-medium">
                                Selecionar Todos
                            </button>
                            <button id="deselectAll" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 font-medium">
                                Desmarcar Todos
                            </button>
                        </div>
                    </div>

                    <!-- Search Box for Places -->
                    <div class="mb-6">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="placeSearch"
                                placeholder="Buscar tipo de lugar..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Places Grid -->
                    <div id="placesGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        <!-- Places will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer melhorado -->
            <div class="flex items-center justify-between p-6 border-t border-gray-200 bg-gray-50">
                <button id="clearFilters" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 font-medium">
                    Limpar Filtros
                </button>
                <div class="flex gap-3">
                    <button id="cancelButton" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 font-medium">
                        Cancelar
                    </button>
                    <button id="applyFilters" class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-sm">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
