<div class="flex justify-center items-center min-h-[30vh] bg-gray-50">
    <div class="w-full max-w-5xl rounded-2xl px-6 py-6">
        <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-4 w-full">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <!-- Tipo de viagem -->
                <div class="flex flex-col">
                    <label for="trip_type" class="text-xs text-gray-500 mb-1">Tipo</label>
                    <select name="type_trip" id="type_trip" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                        <option value="">Qualquer</option>
                        <option value="1">Ida e volta</option>
                        <option value="2">Só ida</option>
                    </select>
                </div>
                <!-- Classe -->
                <div class="flex flex-col">
                    <label for="class" class="text-xs text-gray-500 mb-1">Classe</label>
                    <select name="class" id="class" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                        <option value="">Qualquer</option>
                        <option value="1">Econômica</option>
                        <option value="2">Premium Econômica</option>
                        <option value="3">Executiva</option>
                        <option value="4">Primeira Classe</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="class" class="text-xs text-gray-500 mb-1">Ordenar por</label>
                    <select name="sort_by" id="sort_by" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                        <option value="1">Melhores voos</option>
                        <option value="2">Preço</option>
                        <option value="3">Hora de partida</option>
                        <option value="4">Hora de chegada</option>
                        <option value="5">Duração</option>
                    </select>
                </div>
                <!-- Filtro de preço -->
                <div class="flex flex-col">
                    <label for="price" class="text-xs text-gray-500 mb-1">Preço máximo (R$)</label>
                    <input type="number" min="0" step="50" name="price" id="price" placeholder="Ex: 2000" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none w-28">
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-4 mt-2">
                <!-- Origem e destino -->
                <div class="flex flex-1 bg-gray-100 rounded-lg border border-gray-200">
                    <!-- Origem -->
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2 border-r border-gray-200 relative">
                        <i class="fa-regular fa-dot-circle text-blue-600"></i>
                        <input 
                            type="text" 
                            name="dep_iata" 
                            id="dep_iata"
                            placeholder="Origem (IATA)"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full airport-autocomplete"
                            autocomplete="off"
                        >
                        <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow">
                        </div>
                    </div>
                    <div class="flex items-center px-2">
                        <i class="fa-solid fa-right-left text-gray-400"></i>
                    </div>
                    <!-- Destino -->
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2 relative">
                        <i class="fa-solid fa-location-dot text-blue-600"></i>
                        <input 
                            type="text" 
                            name="arr_iata" 
                            id="arr_iata"
                            placeholder="Destino (IATA)"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full airport-autocomplete"
                            autocomplete="off"
                        >
                        <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow">
                        </div>                    
                    </div>
                </div>
                <!-- Datas -->
                <div class="flex flex-1 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2 border-r border-gray-200">
                        <i class="fa-regular fa-calendar text-blue-600"></i>
                        <input 
                            type="date" 
                            name="date_departure" 
                            id="date_departure"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 w-full"
                        >
                    </div>
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2">
                        <input 
                            type="date" 
                            name="date_return" 
                            id="date_return"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 w-full"
                        >
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <button type="submit" class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-3 py-2 rounded-lg shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base" aria-label="Pesquisar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Pesquisar
                </button>
                <button type="button" class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-3 py-2 rounded-lg shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base" id="open-filter-modal">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>