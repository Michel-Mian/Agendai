<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-6 w-full">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <!-- Tipo de viagem -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="trip_type" class="text-sm text-gray-700 mb-2 font-semibold">Tipo</label>
                <select name="type_trip" id="type_trip" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Qualquer</option>
                    <option value="1">Ida e volta</option>
                    <option value="2">Só ida</option>
                </select>
            </div>
            <!-- Classe -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="class" class="text-sm text-gray-700 mb-2 font-semibold">Classe</label>
                <select name="class" id="class" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Qualquer</option>
                    <option value="1">Econômica</option>
                    <option value="2">Premium Econômica</option>
                    <option value="3">Executiva</option>
                    <option value="4">Primeira Classe</option>
                </select>
            </div>
            <!-- Ordenar -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="sort_by" class="text-sm text-gray-700 mb-2 font-semibold">Ordenar por</label>
                <select name="sort_by" id="sort_by" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="1">Melhores voos</option>
                    <option value="2">Preço</option>
                    <option value="3">Hora de partida</option>
                    <option value="4">Hora de chegada</option>
                    <option value="5">Duração</option>
                </select>
            </div>
            <!-- Filtro de preço -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="price" class="text-sm text-gray-700 mb-2 font-semibold">Preço máximo (R$)</label>
                <input type="number" min="0" step="50" name="price" id="price" placeholder="Ex: 2000" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition w-full md:w-32">
            </div>
        </div>
        <!-- Origem e destino -->
        <div class="flex flex-1 bg-gray-50 rounded-lg border border-gray-300 shadow-sm">
            <!-- Origem -->
            <div class="flex items-center px-4 py-3 gap-2 w-1/2 border-r border-gray-300 relative">
                <i class="fa-regular fa-dot-circle text-gray-600"></i>
                <input 
                    type="text" 
                    name="dep_iata" 
                    id="dep_iata"
                    placeholder="Origem (IATA)"
                    class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full airport-autocomplete"
                    autocomplete="off"
                >
                <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white rounded-lg max-h-40 overflow-y-auto shadow-lg z-20 border border-gray-200"></div>
            </div>
            <div class="flex items-center justify-center px-2 py-1">
                <i class="fa-solid fa-right-left text-gray-400 text-lg"></i>
            </div>
            <!-- Destino -->
            <div class="flex items-center px-4 py-3 gap-2 w-1/2 relative">
                <i class="fa-solid fa-location-dot text-gray-600"></i>
                <input 
                    type="text" 
                    name="arr_iata" 
                    id="arr_iata"
                    placeholder="Destino (IATA)"
                    class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full airport-autocomplete"
                    autocomplete="off"
                >
                <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white rounded-lg max-h-40 overflow-y-auto shadow-lg z-20 border border-gray-200"></div>                    
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Datas -->
            <div class="flex flex-1 flex-col xs:flex-row bg-gray-50 rounded-lg border border-gray-300 overflow-hidden shadow-sm">
                <div class="flex items-center px-4 py-3 gap-2 w-full xs:w-1/2 border-b xs:border-b-0 xs:border-r border-gray-300">
                    <input 
                        type="date" 
                        name="date_departure" 
                        id="date_departure"
                        class="bg-transparent border-0 focus:outline-none text-base text-gray-700 w-full"
                    >
                </div>
                <div class="flex items-center px-4 py-3 gap-2 w-full xs:w-1/2">
                    <input 
                        type="date" 
                        name="date_return" 
                        id="date_return"
                        class="bg-transparent border-0 focus:outline-none text-base text-gray-700 w-full"
                    >
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <button type="submit" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition flex items-center gap-2 text-base" aria-label="Pesquisar">
                <i class="fa-solid fa-magnifying-glass"></i>
                Pesquisar
            </button>
            <button type="button" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-lg shadow-md transition flex items-center gap-2 text-base" id="open-filter-modal">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </form>
</div>