@php
    $eventos = $eventos ?? collect();
@endphp

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mt-8">
    <div class="bg-gradient-to-r from-red-500 via-red-600 to-orange-600 px-8 py-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full translate-y-10 -translate-x-10"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-red-800 flex items-center mb-2">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-newspaper text-red-600 text-lg"></i>
                        </div>
                        Notícias e Informações
                    </h2>
                    <p class="text-red-600 text-sm">Selecione um destino para ver as últimas notícias</p>
                </div>
                @if(isset($viagem->destinos) && $viagem->destinos->count() > 0)
                <div class="w-1/3 relative z-10">
                    <select id="news-destination-select" class="block w-full bg-white text-gray-800 border-white/60 rounded-lg shadow-sm px-3 py-2 focus:ring-red-300 focus:border-red-300">
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
        <div id="news-skeleton-stats">
            <div class="animate-pulse space-y-4">
                <div class="flex space-x-4">
                    <div class="h-24 w-1/3 bg-gray-200 rounded-md"></div>
                    <div class="flex-1 space-y-3">
                        <div class="h-6 bg-gray-200 rounded-md"></div>
                        <div class="h-4 bg-gray-200 rounded-md w-5/6"></div>
                        <div class="h-4 bg-gray-200 rounded-md w-3/4"></div>
                    </div>
                </div>
                <div class="h-16 bg-gray-200 rounded-md"></div>
                <div class="h-16 bg-gray-200 rounded-md"></div>
            </div>
        </div>

        <div id="news-content-stats" class="hidden"></div>

        <div id="news-error-stats" class="hidden text-center py-10">
            <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
            <p class="text-red-600 font-semibold">Erro ao carregar as notícias.</p>
            <p class="text-gray-500 text-sm">Por favor, tente novamente mais tarde.</p>
        </div>
    </div>
</div>