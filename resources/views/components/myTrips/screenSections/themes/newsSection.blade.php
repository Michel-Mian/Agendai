<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-newspaper text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-red-800">Notícias da Região</h3>
                    <p class="text-red-300 text-sm">{{ $viagem->destino_viagem }}</p>
                </div>
            </div>
            <button class="hover:bg-red-600 text-red-400 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Ver todas
            </button>
        </div>
    </div>
    
    <div class="p-6">
        <div class="space-y-4">
            <!-- Notícia Principal -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                <div class="flex items-start space-x-4">
                    <div class="w-24 h-24 bg-gray-300 rounded-lg flex-shrink-0 flex items-center justify-center">
                        <i class="fas fa-image text-gray-500 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">DESTAQUE</span>
                            <span class="text-gray-500 text-sm">há 2 horas</span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-2">Festival de Verão movimenta o turismo local</h4>
                        <p class="text-gray-600 text-sm mb-3">O tradicional festival de verão da região promete atrair milhares de visitantes com apresentações musicais, gastronomia local e atividades culturais...</p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-eye"></i>
                                <span>1.2k visualizações</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-share"></i>
                                <span>45 compartilhamentos</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Outras Notícias -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">GASTRONOMIA</span>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-1">Novo restaurante premiado abre na região</h5>
                            <p class="text-gray-600 text-sm mb-2">Chef renomado inaugura estabelecimento com foco na culinária regional...</p>
                            <span class="text-gray-500 text-xs">há 4 horas</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-plane text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">TRANSPORTE</span>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-1">Aeroporto amplia voos internacionais</h5>
                            <p class="text-gray-600 text-sm mb-2">Novas rotas facilitam acesso de turistas estrangeiros à região...</p>
                            <span class="text-gray-500 text-xs">há 6 horas</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">EVENTOS</span>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-1">Exposição de arte contemporânea</h5>
                            <p class="text-gray-600 text-sm mb-2">Museu local recebe obras de artistas nacionais e internacionais...</p>
                            <span class="text-gray-500 text-xs">há 8 horas</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">AVISO</span>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-1">Obras na via principal</h5>
                            <p class="text-gray-600 text-sm mb-2">Trânsito pode ser afetado durante o período de manutenção...</p>
                            <span class="text-gray-500 text-xs">há 12 horas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>