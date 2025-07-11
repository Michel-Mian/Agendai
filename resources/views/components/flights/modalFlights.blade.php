<div id="filter-modal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 transition-all duration-300 hidden">
    <div class="explore-modal-base">
        <button id="close-filter-modal" class="absolute top-4 right-4 text-gray-400 hover:text-blue-600 text-3xl font-bold transition-colors z-10">&times;</button>
        <div class="mb-6 px-4 pt-6">
            <h1 class="text-lg sm:text-2xl font-extrabold text-gray-800 tracking-tight mb-1">Filtros Avançados</h1>
            <p class="text-gray-500 text-xs sm:text-sm">Refine sua busca de voos conforme sua preferência.</p>
        </div>
        <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-4 w-full px-4 pb-6">            
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="origin" class="block text-xs font-semibold text-gray-600 mb-1">Origem</label>
                    <input type="text" id="dep_iata" name="dep_iata" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: GRU">
                </div>
                <div class="w-full sm:w-1/2">
                    <label for="destination" class="block text-xs font-semibold text-gray-600 mb-1">Destino</label>
                    <input type="text" id="arr_iata" name="arr_iata" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: JFK">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="date_departure" class="block text-xs font-semibold text-gray-600 mb-1">Data de Ida</label>
                    <input type="date" id="date_departure" name="date_departure" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
                <div class="w-full sm:w-1/2">
                    <label for="date-return" class="block text-xs font-semibold text-gray-600 mb-1">Data de Volta</label>
                    <input type="date" id="date-return" name="date_return" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="stops" class="block text-xs font-semibold text-gray-600 mb-1">Número de Paradas</label>
                    <select id="stops" name="stops" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <option value="0">Qualquer</option>
                        <option value="1">Direto</option>
                        <option value="2">1 parada ou menos</option>
                        <option value="3">2 paradas ou menos</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/2">
                    <label for="trip_type" class="block text-xs font-semibold text-gray-600 mb-2">Tipo</label>
                    <select name="type_trip" id="type-trip" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <option value="0">Qualquer</option>
                        <option value="1">Ida e volta</option>
                        <option value="2">Só ida</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="airlines" class="block text-xs font-semibold text-gray-600 mb-1">Companhia Aérea</label>
                    <input type="text" id="airlines" name="airlines" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: LATAM, Azul">
                </div>
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Excluir Companhias Aéreas</label>
                    <div class="flex flex-wrap gap-2 max-h-24 overflow-y-auto bg-gray-50 rounded-xl p-2 shadow-inner">
                        @if(isset($airlines) && count($airlines) > 0)
                            @foreach($airlines as $name => $iata)
                                <label class="inline-flex items-center px-3 py-2 bg-white rounded-lg shadow hover:bg-blue-50 transition-colors cursor-pointer">
                                    <input type="checkbox" name="exclude_airlines[]" value="{{ $iata }}"
                                        class="form-checkbox text-blue-600 rounded focus:ring-0 focus:border-blue-300" />
                                    <span class="ml-2 text-sm text-gray-700">{{ $name }}</span>
                                </label>
                            @endforeach
                        @else
                            <span class="text-xs text-gray-400">Nenhuma companhia disponível</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="departure-time" class="block text-xs font-semibold text-gray-600 mb-1">Intervalo de horário de Saída</label>
                    <div class="flex gap-2">
                        <input type="time" id="departure-time" name="departure_time[]" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <input type="time" name="departure_time[]" value="" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                </div>
                <div class="w-full sm:w-1/2">
                    <label for="arrival-time" class="block text-xs font-semibold text-gray-600 mb-1">Intervalo de horário de Chegada</label>
                    <div class="flex gap-2">
                        <input type="time" id="arrival_time[]" name="arrival_time[]" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <input type="time" name="arrival_time[]" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label for="max-duration" class="block text-xs font-semibold text-gray-600 mb-1">Duração Máxima (h)</label>
                    <input type="number" id="max_duration" name="max_duration" min="1" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
                <div class="w-full sm:w-1/2">
                    <label for="max-price" class="block text-xs font-semibold text-gray-600 mb-1">Preço Máximo (R$)</label>
                    <input type="number" id="price" name="price" min="0" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full">
                    <label for="cabin-class" class="block text-xs font-semibold text-gray-600 mb-1">Classe</label>
                    <select id="class" name="class" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <option value="">Qualquer</option>
                        <option value="1">Econômica</option>
                        <option value="2">Premium Econômica</option>
                        <option value="3">Executiva</option>
                        <option value="4">Primeira Classe</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-blue-700 transition text-xs sm:text-base">Aplicar Filtros</button>
            </div>
        </form>
    </div>
</div>