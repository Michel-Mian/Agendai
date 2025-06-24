<div id="filter-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-30 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-2xl w-full relative border border-gray-100">
        <button id="close-filter-modal" class="absolute top-5 right-5 text-gray-400 hover:text-blue-600 text-3xl font-bold transition-colors">&times;</button>
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight mb-1">Filtros Avançados</h1>
            <p class="text-gray-500 text-sm">Refine sua busca de voos conforme sua preferência.</p>
        </div>
<form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-4 w-full">            <div class="flex gap-6">
                <div class="w-1/2">
                    <label for="origin" class="block text-xs font-semibold text-gray-600 mb-1">Origem</label>
                    <input type="text" id="dep_iata" name="dep_iata" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: GRU">
                </div>
                <div class="w-1/2">
                    <label for="destination" class="block text-xs font-semibold text-gray-600 mb-1">Destino</label>
                    <input type="text" id="arr_iata" name="arr_iata" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: JFK">
                </div>
            </div>
            <div class="flex gap-6">
                <div class="w-1/2">
                    <label for="" class="block text-xs font-semibold text-gray-600 mb-1">Data de Ida</label>
                    <input type="date" id="date_departure" name="date_departure" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
                <div class="w-1/2">
                    <label for="return-date" class="block text-xs font-semibold text-gray-600 mb-1">Data de Volta</label>
                    <input type="date" id="date_return" name="date_return" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
            </div>
            <div class="flex gap-6">
                <div class="w-1/2">
                    <label for="stops" class="block text-xs font-semibold text-gray-600 mb-1">Número de Paradas</label>
                    <select id="stops" name="stops" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <option value="">Qualquer</option>
                        <option value="0">Direto</option>
                        <option value="1">1 parada ou menos</option>
                        <option value="2">2 paradas ou mnenos</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="trip_type" class="block text-xs font-semibold text-gray-600 mb-2">Tipo</label>
                    <select name="type_trip" id="type_trip" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                        <option value="">Qualquer</option>
                        <option value="1">Ida e volta</option>
                        <option value="2">Só ida</option>
                    </select>
                </div>
                <div class="w-1/2">
                    <label for="airlines" class="block text-xs font-semibold text-gray-600 mb-1">Companhia Aérea</label>
                    <input type="text" id="airlines" name="airlines" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" placeholder="Ex: LATAM, Azul">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Excluir Companhias Aéreas</label>
                <div class="flex flex-wrap gap-3 max-h-32 overflow-y-auto bg-gray-50 rounded-xl p-4 shadow-inner">
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
            <div class="flex gap-6">
                <div class="w-1/2">
                    <label for="departure-time" class="block text-xs font-semibold text-gray-600 mb-1">Horário de Saída</label>
                    <input type="time" id="departure-time" name="departure_time" multiple class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
                <div class="w-1/2">
                    <label for="arrival-time" class="block text-xs font-semibold text-gray-600 mb-1">Horário de Chegada</label>
                    <input type="time" id="arrival-time" name="arrival_time" multiple class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
            </div>
            <div class="flex gap-6">
                <div class="w-1/2">
                    <label for="max-duration" class="block text-xs font-semibold text-gray-600 mb-1">Duração Máxima (h)</label>
                    <input type="number" id="max-duration" name="max_duration" min="1" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
                <div class="w-1/2">
                    <label for="max-price" class="block text-xs font-semibold text-gray-600 mb-1">Preço Máximo (R$)</label>
                    <input type="number" id="price" name="price" min="0" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                </div>
            </div>
            <div>
                <label for="cabin-class" class="block text-xs font-semibold text-gray-600 mb-1">Classe</label>
                <select id="class" name="class" class="block w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">Qualquer</option>
                    <option value="1">Econômica</option>
                    <option value="2">Premium Econômica</option>
                    <option value="3">Executiva</option>
                    <option value="4">Primeira Classe</option>
                </select>
            </div>
            <div class="flex justify-end pt-6">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold shadow hover:bg-blue-700 transition">Aplicar Filtros</button>
            </div>
        </form>
    </div>
</div>