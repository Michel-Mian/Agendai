<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-cloud-sun text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-blue-700">Previsão do Tempo</h3>
                    <p class="text-blue-400 text-sm">{{ $viagem->destino_viagem }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-blue-700 text-sm">Período da viagem</div>
                <div class="text-blue-400 font-medium">{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m') }}</div>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Clima Atual -->
        <div class="bg-gradient-to-br from-sky-50 to-blue-100 rounded-lg p-6 mb-6 border border-sky-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="text-6xl">☀️</div>
                    <div>
                        <div class="text-3xl font-bold text-gray-800">28°C</div>
                        <div class="text-gray-600">Ensolarado</div>
                        <div class="text-sm text-gray-500">Sensação térmica: 31°C</div>
                    </div>
                </div>
                <div class="text-right space-y-2">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-eye text-sm"></i>
                        <span class="text-sm">Visibilidade: 10km</span>
                    </div>
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-wind text-sm"></i>
                        <span class="text-sm">Vento: 15 km/h</span>
                    </div>
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-tint text-sm"></i>
                        <span class="text-sm">Umidade: 65%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Previsão dos Próximos Dias -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Hoje</div>
                <div class="text-2xl mb-2">☀️</div>
                <div class="text-lg font-bold text-gray-800">28°</div>
                <div class="text-sm text-gray-500">18°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Amanhã</div>
                <div class="text-2xl mb-2">⛅</div>
                <div class="text-lg font-bold text-gray-800">26°</div>
                <div class="text-sm text-gray-500">19°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Qua</div>
                <div class="text-2xl mb-2">🌧️</div>
                <div class="text-lg font-bold text-gray-800">23°</div>
                <div class="text-sm text-gray-500">16°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Qui</div>
                <div class="text-2xl mb-2">🌤️</div>
                <div class="text-lg font-bold text-gray-800">25°</div>
                <div class="text-sm text-gray-500">17°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Sex</div>
                <div class="text-2xl mb-2">☀️</div>
                <div class="text-lg font-bold text-gray-800">29°</div>
                <div class="text-sm text-gray-500">20°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Sáb</div>
                <div class="text-2xl mb-2">☀️</div>
                <div class="text-lg font-bold text-gray-800">31°</div>
                <div class="text-sm text-gray-500">22°</div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                <div class="text-sm font-medium text-gray-600 mb-2">Dom</div>
                <div class="text-2xl mb-2">⛅</div>
                <div class="text-lg font-bold text-gray-800">27°</div>
                <div class="text-sm text-gray-500">19°</div>
            </div>
        </div>
    </div>
</div>