<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 px-8 py-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-blue-900 flex items-center mb-2">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-cloud-sun text-blue-800 text-lg"></i>
                        </div>
                        Previsão do Tempo
                    </h2>
                    <p class="text-blue-600 text-sm">Selecione um destino para ver o clima</p>
                </div>
                @if(isset($viagem->destinos) && $viagem->destinos->count() > 0)
                <div class="w-1/3">
                    <select id="weather-destination-select" class="block w-full bg-white/20 text-white border-white/30 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-300">
                        @foreach($viagem->destinos as $destino)
                            <option value="{{ $destino->pk_id_destino }}">{{ $destino->nome_destino }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="p-6">
        <div id="weather-skeleton-stats">
            <div class="animate-pulse space-y-4">
                <div class="h-10 bg-gray-200 rounded-md w-3/4"></div>
                <div class="grid grid-cols-7 gap-4">
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                </div>
            </div>
        </div>

        <div id="weather-content-stats" class="hidden"></div>

        <div id="weather-error-stats" class="hidden text-center py-10">
            <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
            <p class="text-red-600 font-semibold">Erro ao carregar dados do clima.</p>
            <p class="text-gray-500 text-sm">Por favor, tente novamente mais tarde.</p>
        </div>
    </div>
</div>