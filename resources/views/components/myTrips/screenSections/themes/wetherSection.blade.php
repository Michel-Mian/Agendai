<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <!-- Header com gradiente mais atrativo -->
    <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 px-8 py-6 relative overflow-hidden">
        <!-- Elementos decorativos -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-bold text-blue-900 flex items-center mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-cloud-sun text-blue-800 text-lg"></i>
                </div>
                Previsão do Tempo
            </h2>
            <p class="text-blue-600 text-sm">Condições climáticas detalhadas para {{ $viagem->destino_viagem }}</p>
        </div>
    </div>
    
    <!-- Skeleton Loading Melhorado -->
    <div id="weather-skeleton-stats" class="p-6">
        <!-- Resumo do clima atual -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-600 rounded-xl p-6 mb-6 animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="bg-gray-500 rounded h-6 w-32 mb-2"></div>
                    <div class="bg-gray-500 rounded h-4 w-24"></div>
                </div>
                <div class="bg-gray-600 rounded-full h-16 w-16"></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="bg-gray-600 rounded h-4 w-16 mx-auto mb-1"></div>
                    <div class="bg-gray-500 rounded h-6 w-12 mx-auto"></div>
                </div>
                <div class="text-center">
                    <div class="bg-gray-500 rounded h-4 w-16 mx-auto mb-1"></div>
                    <div class="bg-gray-500 rounded h-6 w-12 mx-auto"></div>
                </div>
                <div class="text-center">
                    <div class="bg-gray-500 rounded h-4 w-16 mx-auto mb-1"></div>
                    <div class="bg-gray-500 rounded h-6 w-12 mx-auto"></div>
                </div>
            </div>
        </div>
        
        <!-- Previsão dos próximos dias -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @for($i = 0; $i < 7; $i++)
            <div class="bg-gradient-to-b from-gray-50 to-gray-100 rounded-xl p-4 animate-pulse">
                <div class="bg-gray-200 rounded h-4 w-16 mx-auto mb-3"></div>
                <div class="bg-gray-200 rounded-full h-12 w-12 mx-auto mb-3"></div>
                <div class="bg-gray-200 rounded h-5 w-8 mx-auto mb-2"></div>
                <div class="bg-gray-200 rounded h-4 w-6 mx-auto mb-3"></div>
                <div class="space-y-2">
                    <div class="bg-gray-200 rounded h-3 w-full"></div>
                    <div class="bg-gray-200 rounded h-3 w-full"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    
    <!-- Conteúdo Real do Clima APRIMORADO -->
    <div id="weather-content-stats" class="p-6 hidden">
        <!-- Conteúdo será carregado via AJAX -->
    </div>
    
    <!-- Estado de Erro -->
    <div id="weather-error-stats" class="p-6 text-center hidden">
        <div class="text-gray-500">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Erro ao carregar clima</h3>
            <p class="text-gray-600 mb-4">Não foi possível carregar a previsão do tempo</p>
            <button onclick="loadWeatherDataStats(window.currentTripId)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-redo mr-2"></i>Tentar novamente
            </button>
        </div>
    </div>
</div>
