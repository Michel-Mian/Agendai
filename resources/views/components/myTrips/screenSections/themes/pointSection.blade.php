<!-- Painel lateral de pontos de interesse -->
<!-- Adicionado z-index alto e removido overflow-hidden problemático -->
<div id="points-panel" class="w-80 bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out h-[50rem] z-50 flex-shrink-0">
    <!-- Header do painel -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white p-4 flex items-center justify-between relative z-10">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 rounded-lg p-2">
                <i class="fas fa-map-marker-alt text-blue-900 text-lg"></i>
            </div>
            <div id="panel-header-text">
                <h2 class="text-lg font-bold text-blue-900">Pontos de Interesse</h2>
                <p class="text-blue-700 text-sm">{{ count($viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get()) }} {{ count($viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get()) == 1 ? 'local' : 'locais' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Botão de editar pontos -->
    <div class="p-4 border-b border-gray-200">
        <a href="{{ route('explore.setTrip', $viagem->pk_id_viagem) }}" class="w-full bg-blue-900 hover:bg-blue-800 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
            <i class="fas fa-edit"></i>
            <span>Editar Pontos de Interesse</span>
        </a>
    </div>
    
    <!-- Conteúdo do painel -->
    <div id="points-content" class="flex-1 overflow-y-auto p-4">
        @php
            $pontosOrdenados = $viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get();
            $pontosPorData = $pontosOrdenados->groupBy(function($ponto) {
                return \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('Y-m-d');
            });
        @endphp
        @if($pontosOrdenados->count())
            <div class="space-y-4">
                @php $globalIndex = 0; @endphp
                @foreach($pontosPorData as $data => $pontos)
                    <!-- Divisória com título do dia -->
                    <div class="border-b border-blue-200 pb-2 mb-4">
                        <h3 class="text-sm font-bold text-blue-900 bg-blue-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Dia {{ \Carbon\Carbon::parse($data)->format('d/m') }}
                        </h3>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        @foreach($pontos as $ponto)
                            <div class="group bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="focusOnPoint({{ $globalIndex }}, {{ $ponto->latitude }}, {{ $ponto->longitude }})">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                        {{ $globalIndex + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-800 text-sm truncate">{{ $ponto->nome_ponto_interesse }}</h3>
                                        
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 mt-1">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-calendar text-blue-900"></i>
                                                <span>{{ \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('d/m') }}</span>
                                            </div>
                                            @if($ponto->hora_ponto_interesse)
                                                <div class="flex items-center space-x-1">
                                                    <i class="fas fa-clock text-blue-900"></i>
                                                    <span>{{ \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($ponto->desc_ponto_interesse)
                                            <p class="text-gray-600 text-xs mt-1 line-clamp-2">{{ $ponto->desc_ponto_interesse }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-eye text-blue-900 text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            @php $globalIndex++; @endphp
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-blue-900 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum ponto</h3>
                <p class="text-gray-500 text-sm">Adicione locais interessantes para visitar</p>
            </div>
        @endif
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
