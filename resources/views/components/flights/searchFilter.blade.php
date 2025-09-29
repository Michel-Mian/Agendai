<div class="flex justify-center items-center min-h-[30vh] px-2">
    <div class="w-full max-w-5xl rounded-3xl px-4 sm:px-8 py-6 sm:py-10">
        <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-6 w-full">
            <div class="flex flex-col md:flex-row md:items-end gap-6">
                <!-- Tipo de viagem -->
                <div class="flex flex-col w-full md:w-auto">
                    <label for="trip_type" class="text-xs text-blue-600 mb-1 font-semibold">Tipo</label>
                    <select name="type_trip" id="type_trip" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-sm text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                        <option value="">Qualquer</option>
                        <option value="1">Ida e volta</option>
                        <option value="2">Só ida</option>
                    </select>
                </div>
                <!-- Classe -->
                <div class="flex flex-col w-full md:w-auto">
                    <label for="class" class="text-xs text-blue-600 mb-1 font-semibold">Classe</label>
                    <select name="class" id="class" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-sm text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                        <option value="">Qualquer</option>
                        <option value="1">Econômica</option>
                        <option value="2">Premium Econômica</option>
                        <option value="3">Executiva</option>
                        <option value="4">Primeira Classe</option>
                    </select>
                </div>
                <!-- Ordenar -->
                <div class="flex flex-col w-full md:w-auto">
                    <label for="sort_by" class="text-xs text-blue-600 mb-1 font-semibold">Ordenar por</label>
                    <select name="sort_by" id="sort_by" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-sm text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                        <option value="1">Melhores voos</option>
                        <option value="2">Preço</option>
                        <option value="3">Hora de partida</option>
                        <option value="4">Hora de chegada</option>
                        <option value="5">Duração</option>
                    </select>
                </div>
                <!-- Filtro de preço -->
                <div class="flex flex-col w-full md:w-auto">
                    <label for="price" class="text-xs text-blue-600 mb-1 font-semibold">Preço máximo (R$)</label>
                    <input type="number" min="0" step="50" name="price" id="price" placeholder="Ex: 2000" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-sm text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition w-full md:w-32">
                </div>
            </div>
            <!-- Origem e destino -->
            <div class="flex flex-1 bg-blue-50 rounded-xl border border-blue-200 shadow-inner">
                <!-- Origem -->
                <div class="flex items-center px-4 py-3 gap-2 w-1/2 border-r border-blue-200 relative">
                    <i class="fa-regular fa-dot-circle text-blue-600"></i>
                    <input 
                        type="text" 
                        name="dep_iata" 
                        id="dep_iata"
                        placeholder="Origem (IATA)"
                        class="bg-transparent border-0 focus:outline-none text-base text-blue-700 placeholder-blue-400 w-full airport-autocomplete"
                        autocomplete="off"
                    >
                    <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white rounded max-h-40 overflow-y-auto shadow z-20 border-0"></div>
                </div>
                <div class="flex items-center justify-center px-2 py-1">
                    <i class="fa-solid fa-right-left text-blue-400 text-lg"></i>
                </div>
                <!-- Destino -->
                <div class="flex items-center px-4 py-3 gap-2 w-1/2 relative">
                    <i class="fa-solid fa-location-dot text-blue-600"></i>
                    <input 
                        type="text" 
                        name="arr_iata" 
                        id="arr_iata"
                        placeholder="Destino (IATA)"
                        class="bg-transparent border-0 focus:outline-none text-base text-blue-700 placeholder-blue-400 w-full airport-autocomplete"
                        autocomplete="off"
                    >
                    <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white rounded max-h-40 overflow-y-auto shadow z-20 border-0"></div>                    
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-6 mt-2">
                <!-- Datas -->
                <div class="flex flex-1 flex-col xs:flex-row bg-blue-50 rounded-xl border border-blue-200 overflow-hidden shadow-inner">
                    <div class="flex items-center px-4 py-3 gap-2 w-full xs:w-1/2 border-b xs:border-b-0 xs:border-r border-blue-200">
                        
                        <input 
                            type="date" 
                            name="date_departure" 
                            id="date_departure"
                            class="bg-transparent border-0 focus:outline-none text-base text-blue-700 w-full"
                        >
                    </div>
                    <div class="flex items-center px-4 py-3 gap-2 w-full xs:w-1/2">
                        <input 
                            type="date" 
                            name="date_return" 
                            id="date_return"
                            class="bg-transparent border-0 focus:outline-none text-base text-blue-700 w-full"
                        >
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-6 gap-3">
                <button type="submit" class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base" aria-label="Pesquisar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Pesquisar
                </button>
                <button type="button" class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base" id="open-filter-modal">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>