<div class="flex justify-center items-center min-h-[30vh] bg-gray-50">
    <div class="w-full max-w-5xl rounded-2xl px-6 py-6">
        <form action="{{ route('flights.search') }}" method="GET" class="flex flex-col gap-4 w-full">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <!-- Tipo de viagem -->
                <div class="flex flex-col">
                    <label for="trip_type" class="text-xs text-gray-500 mb-1">Tipo</label>
                    <select name="trip_type" id="trip_type" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                        <option value="round">Ida e volta</option>
                        <option value="oneway">Só ida</option>
                    </select>
                </div>
                <!-- Passageiros -->
                <div class="flex flex-col">
                    <label for="passengers" class="text-xs text-gray-500 mb-1">Passageiros</label>
                    <input type="number" min="1" max="10" value="1" name="passengers" id="passengers" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                </div>
                <!-- Classe -->
                <div class="flex flex-col">
                    <label for="class" class="text-xs text-gray-500 mb-1">Classe</label>
                    <select name="class" id="class" class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                        <option value="economica">Econômica</option>
                        <option value="executiva">Executiva</option>
                        <option value="primeira">Primeira Classe</option>
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
                <div class="flex flex-1 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2 border-r border-gray-200">
                        <i class="fa-regular fa-dot-circle text-blue-600"></i>
                        <input 
                            type="text" 
                            name="dep_iata" 
                            id="dep_iata"
                            placeholder="Origem (IATA)"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full"
                        >
                    </div>
                    <div class="flex items-center px-2">
                        <i class="fa-solid fa-right-left text-gray-400"></i>
                    </div>
                    <div class="flex items-center px-3 py-2 gap-2 w-1/2">
                        <i class="fa-solid fa-location-dot text-blue-600"></i>
                        <input 
                            type="text" 
                            name="arr_iata" 
                            id="arr_iata"
                            placeholder="Destino (IATA)"
                            class="bg-transparent border-0 focus:outline-none text-base text-gray-700 placeholder-gray-400 w-full"
                        >
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
            <div class="flex justify-end mt-4">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-3 py-2 rounded-lg shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base"
                    aria-label="Pesquisar"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Pesquisar
                </button>
            </div>
        </form>
    </div>
</div>