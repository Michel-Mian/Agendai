<div class="space-y-8">
    <!-- Seção de Objetivos e Viajantes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card de Objetivos -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <i class="fas fa-bullseye text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-purple-800">Objetivos</h2>
                            <p class="text-purple-600 text-sm">{{ $objetivos->count() }} {{ $objetivos->count() == 1 ? 'objetivo' : 'objetivos' }}</p>
                        </div>
                    </div>
                    <button type="button" id="open-add-objetivo-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors" title="Adicionar objetivo">
                        <i class="fas fa-plus text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                @if($objetivos->count())
                    <div class="space-y-3">
                        @php
                            $objetivosExibidos = ($objetivos->count() > 5) ? $objetivos->take(3) : $objetivos;
                        @endphp
                        @foreach($objetivosExibidos as $index => $objetivo)
                            <div class="group flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $objetivo->nome }}</span>
                                </div>
                                <form action="{{ route('objetivos.destroy', ['id' => $objetivo->pk_id_objetivo]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors" title="Remover objetivo">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($objetivos->count() > 5)
                        <div class="mt-4">
                            <button id="open-objetivos-modal-btn" class="w-full bg-gradient-to-r from-purple-100 to-purple-200 hover:from-purple-200 hover:to-purple-300 text-purple-700 font-medium py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-eye"></i>
                                <span>Ver todos os objetivos ({{ $objetivos->count() }})</span>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bullseye text-purple-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 mb-4">Nenhum objetivo cadastrado</p>
                        <button type="button" id="open-add-objetivo-modal-btn-empty" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Adicionar primeiro objetivo
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card de Viajantes -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-green-800">Viajantes</h2>
                            <p class="text-green-600 text-sm">{{ $viajantes->count() }} {{ $viajantes->count() == 1 ? 'viajante' : 'viajantes' }}</p>
                        </div>
                    </div>
                    <button type="button" id="open-add-viajante-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors" title="Adicionar viajante">
                        <i class="fas fa-user-plus text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                @if($viajantes->count())
                    <div class="space-y-3">
                        @php
                            $viajantesExibidos = ($viajantes->count() > 5) ? $viajantes->take(3) : $viajantes;
                        @endphp
                        @foreach($viajantesExibidos as $viajante)
                            <div class="group flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($viajante->nome, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $viajante->nome }}</div>
                                        <div class="text-sm text-gray-600">{{ $viajante->idade }} anos</div>
                                    </div>
                                </div>
                                <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors" title="Remover viajante">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($viajantes->count() > 5)
                        <div class="mt-4">
                            <button id="open-viajantes-modal-btn" class="w-full bg-gradient-to-r from-green-100 to-green-200 hover:from-green-200 hover:to-green-300 text-green-700 font-medium py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-eye"></i>
                                <span>Ver todos os viajantes ({{ $viajantes->count() }})</span>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-green-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 mb-4">Nenhum viajante cadastrado</p>
                        <button type="button" id="open-add-viajante-modal-btn-empty" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Adicionar primeiro viajante
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Seção de Voos -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-plane text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-blue-800">Voos</h2>
                        <p class="text-blue-600 text-sm">{{ $voos->count() }} {{ $voos->count() == 1 ? 'voo' : 'voos' }} cadastrados</p>
                    </div>
                </div>
                <button class="bg-white/20 hover:bg-white/30 text-blue-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Adicionar voo
                </button>
            </div>
        </div>
        
        <div class="p-6">
            @if($voos->count())
                <div class="overflow-hidden">
                    <div class="space-y-4">
                        @foreach($voos as $index => $voo)
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $voo->desc_aeronave_voo }}</div>
                                            <div class="text-sm text-gray-600">{{ $voo->companhia_voo }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($voo->data_hora_voo)->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($voo->data_hora_voo)->format('H:i') }}</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm text-gray-500">Origem</div>
                                            <div class="font-semibold text-gray-800">{{ $voo->origem_voo }}</div>
                                        </div>
                                        <div class="flex items-center space-x-2 text-blue-500">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <div class="w-8 h-0.5 bg-blue-500"></div>
                                            <i class="fas fa-plane text-sm"></i>
                                            <div class="w-8 h-0.5 bg-blue-500"></div>
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm text-gray-500">Destino</div>
                                            <div class="font-semibold text-gray-800">{{ $voo->destino_voo }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plane text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum voo cadastrado</h3>
                    <p class="text-gray-500 mb-6">Adicione informações sobre seus voos para manter tudo organizado</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Adicionar primeiro voo
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Seção de Pontos de Interesse -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
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
</div>

<style>
    /* Animações e transições personalizadas */
    .group:hover .opacity-0 {
        opacity: 1;
    }
    
    .transition-all {
        transition: all 0.3s ease-in-out;
    }
    
    /* Efeitos de hover suaves */
    .hover\:shadow-md:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Gradientes personalizados para melhor visual */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }
    
    /* Animação para os cards */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .space-y-3 > * {
        animation: slideIn 0.5s ease-out;
    }
</style>

<script>
    // Adicionar event listeners para os botões vazios
    document.addEventListener('DOMContentLoaded', function() {
        // Botão de adicionar objetivo quando vazio
        const addObjetivoEmptyBtn = document.getElementById('open-add-objetivo-modal-btn-empty');
        if (addObjetivoEmptyBtn) {
            addObjetivoEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-objetivo-modal-btn').click();
            });
        }
        
        // Botão de adicionar viajante quando vazio
        const addViajanteEmptyBtn = document.getElementById('open-add-viajante-modal-btn-empty');
        if (addViajanteEmptyBtn) {
            addViajanteEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-viajante-modal-btn').click();
            });
        }
    });
</script>
