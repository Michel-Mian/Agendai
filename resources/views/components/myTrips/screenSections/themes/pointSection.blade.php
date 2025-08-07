<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-red-800">Pontos de Interesse</h2>
                    <p class="text-red-600 text-sm">{{ $pontosInteresse->count() }} {{ $pontosInteresse->count() == 1 ? 'local' : 'locais' }} selecionados</p>
                </div>
            </div>
            <a href="{{ route('explore.setTrip', ['id' => $viagem->pk_id_viagem]) }}" class="bg-white/20 hover:bg-white/30 text-red-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2">
                <i class="fas fa-map-location-dot"></i>
                <span>Editar no mapa</span>
            </a>
        </div>
    </div>
    
    <div class="p-6">
        @php
            $pontosOrdenados = $pontosInteresse->sortBy('data_ponto_interesse');
        @endphp
        @if($pontosOrdenados->count())
            <div class="space-y-4">
                @foreach($pontosOrdenados as $index => $ponto)
                    <div class="group bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="openPlaceDetailsModal('{{ $ponto->placeid_ponto_interesse }}', true, {{ $ponto->pk_id_ponto_interesse }}, '{{ $ponto->hora_ponto_interesse ? \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') : '' }}')">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $ponto->nome_ponto_interesse }}</h3>
                                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-clock text-red-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-calendar text-red-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('l') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($ponto->desc_ponto_interesse)
                                        <p class="text-gray-600 text-sm">{{ $ponto->desc_ponto_interesse }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-chevron-right text-red-500"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-red-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum ponto de interesse</h3>
                <p class="text-gray-500 mb-6">Explore o mapa e adicione locais interessantes para visitar</p>
                <a href="{{ route('explore.setTrip', ['id' => $viagem->pk_id_viagem]) }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-map-location-dot mr-2"></i>Explorar no mapa
                </a>
            </div>
        @endif
    </div>
</div>