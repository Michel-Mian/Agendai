<div id="filter-modal" class="fixed inset-0 bg-opacity-40 backdrop-blur-md overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden">
    <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
        
        <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Filtros Avançados</h3>
            <button type="button" id="close-filter-modal" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-6">Refine sua busca de voos conforme sua preferência.</p>

        <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-6 w-full">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fa-solid fa-plane-departure text-gray-600 mr-2"></i>
                        Rotas e Datas
                    </h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_iata" class="block text-sm font-medium text-gray-700 mb-2">Origem (IATA)</label>
                            <input type="text" id="dep_iata" name="dep_iata" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                placeholder="Ex: GRU">
                        </div>
                        <div>
                            <label for="arr_iata" class="block text-sm font-medium text-gray-700 mb-2">Destino (IATA)</label>
                            <input type="text" id="arr_iata" name="arr_iata" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                placeholder="Ex: JFK">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_departure" class="block text-sm font-medium text-gray-700 mb-2">Data de Ida</label>
                            <input type="date" id="date_departure" name="date_departure" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                        </div>
                        <div>
                            <label for="date-return" class="block text-sm font-medium text-gray-700 mb-2">Data de Volta</label>
                            <input type="date" id="date-return" name="date_return" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stops" class="block text-sm font-medium text-gray-700 mb-2">Número de Paradas</label>
                            <select id="stops" name="stops"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                                <option value="0">Qualquer</option>
                                <option value="1">Direto</option>
                                <option value="2">1 parada ou menos</option>
                                <option value="3">2 paradas ou menos</option>
                            </select>
                        </div>
                        <div>
                            <label for="type-trip" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <select name="type_trip" id="type-trip"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                                <option value="0">Qualquer</option>
                                <option value="1">Ida e volta</option>
                                <option value="2">Só ida</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fa-solid fa-clock text-gray-600 mr-2"></i>
                        Horários e Tarifas
                    </h4>
                    
                    <div>
                        <label for="departure-time" class="block text-sm font-medium text-gray-700 mb-2">Intervalo de horário de Saída</label>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="time" id="departure-time" name="departure_time[]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                            <input type="time" name="departure_time[]" value="" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                        </div>
                    </div>
                    
                    <div>
                        <label for="arrival-time" class="block text-sm font-medium text-gray-700 mb-2">Intervalo de horário de Chegada</label>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="time" id="arrival_time[]" name="arrival_time[]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                            <input type="time" name="arrival_time[]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max-duration" class="block text-sm font-medium text-gray-700 mb-2">Duração Máxima (h)</label>
                            <input type="number" id="max_duration" name="max_duration" min="1" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800"
                                placeholder="Ex: 10">
                        </div>
                        <div>
                            <label for="max-price" class="block text-sm font-medium text-gray-700 mb-2">Preço Máximo (R$)</label>
                            <input type="number" id="price" name="price" min="0" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800"
                                placeholder="Ex: 2500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                        <select id="class" name="class"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800">
                            <option value="">Qualquer</option>
                            <option value="1">Econômica</option>
                            <option value="2">Premium Econômica</option>
                            <option value="3">Executiva</option>
                            <option value="4">Primeira Classe</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fa-solid fa-tag text-gray-600 mr-2"></i>
                    Companhias Aéreas
                </h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="airlines" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Nome</label>
                        <input type="text" id="airlines" name="airlines" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                            placeholder="Ex: LATAM, Azul">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Excluir Companhias Aéreas</label>
                        <div class="p-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 max-h-40 overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
                                @if(isset($airlines) && count($airlines) > 0)
                                    @foreach($airlines as $name => $iata)
                                        <label class="flex items-center p-2 hover:bg-white rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="exclude_airlines[]" value="{{ $iata }}"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded mr-3" />
                                            <span class="text-sm text-gray-800">{{ $name }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-400 p-2 col-span-2">Nenhuma companhia disponível</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold shadow hover:bg-blue-700 transition">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>