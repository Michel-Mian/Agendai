@php
    // Garantir que $eventos sempre existe
    $eventos = $eventos ?? collect();
@endphp

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <!-- Header com gradiente -->
    <div class="bg-gradient-to-r from-red-500 via-red-600 to-orange-600 px-8 py-6 relative overflow-hidden">
        <!-- Elementos decorativos -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full translate-y-10 -translate-x-10"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-bold text-red-800 flex items-center mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-newspaper text-red-600 text-lg"></i>
                </div>
                Notícias e Informações
            </h2>
            <p class="text-red-600 text-sm">Últimas notícias e eventos de {{ $viagem->destino_viagem }}</p>
        </div>
    </div>
    
    <!-- Skeleton Loading Melhorado -->
    <div id="news-skeleton-stats" class="p-6">
        <!-- Notícia em destaque -->
        <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-xl p-6 mb-6 animate-pulse">
            <div class="flex items-start space-x-4">
                <div class="bg-gray-200 rounded-lg h-24 w-32 flex-shrink-0"></div>
                <div class="flex-1">
                    <div class="bg-gray-200 rounded h-6 w-3/4 mb-2"></div>
                    <div class="bg-gray-200 rounded h-4 w-full mb-2"></div>
                    <div class="bg-gray-200 rounded h-4 w-2/3 mb-3"></div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-gray-200 rounded h-3 w-20"></div>
                        <div class="bg-gray-200 rounded h-3 w-24"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grid de outras notícias -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @for($i = 0; $i < 6; $i++)
            <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow animate-pulse">
                <div class="flex items-start space-x-3">
                    <div class="bg-gray-200 rounded-lg h-16 w-20 flex-shrink-0"></div>
                    <div class="flex-1">
                        <div class="bg-gray-200 rounded h-5 w-full mb-2"></div>
                        <div class="bg-gray-200 rounded h-4 w-3/4 mb-2"></div>
                        <div class="bg-gray-200 rounded h-3 w-16"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    
    <!-- Conteúdo Real das Notícias APRIMORADO -->
    <div id="news-content-stats" class="p-6 hidden">
        <!-- Conteúdo será carregado via AJAX -->
    </div>
    
    <!-- Estado de Erro -->
    <div id="news-error-stats" class="p-6 text-center hidden">
        <div class="text-gray-500">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-orange-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Erro ao carregar notícias</h3>
            <p class="text-gray-600 mb-4">Não foi possível carregar as notícias</p>
            <button onclick="loadNewsDataStats(window.currentTripId)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-redo mr-2"></i>Tentar novamente
            </button>
        </div>
    </div>
</div>
