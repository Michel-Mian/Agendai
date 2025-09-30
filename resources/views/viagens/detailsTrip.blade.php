@extends('index')
@section('content')
    <script>
        // Definir ID da viagem atual globalmente
        window.currentTripId = {{ $viagem->pk_id_viagem }};
        
        // Definir função de remoção no escopo global desde o início
        window.removePontoFromItinerary = function(pontoId) {
            if (!confirm('Tem certeza que deseja remover este ponto do itinerário?')) {
                return;
            }

            // Mostrar loading no botão
            const removeBtn = document.querySelector(`button[onclick="removePontoFromItinerary('${pontoId}')"]`);
            if (removeBtn) {
                removeBtn.disabled = true;
                removeBtn.innerHTML = '⏳ Removendo...';
            }

            fetch(`/explore/${pontoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showNotification === 'function') {
                        showNotification('Ponto removido do itinerário com sucesso!', 'success');
                    } else {
                        alert('Ponto removido do itinerário com sucesso!');
                    }
                    // Fechar o modal
                    if (typeof closeModal === 'function') {
                        closeModal();
                    }
                    // Atualizar o DOM removendo o ponto da interface sem recarregar
                    if (typeof updateItineraryAfterRemoval === 'function') {
                        updateItineraryAfterRemoval(pontoId);
                    } else {
                        // Fallback: recarregar a página se as funções não estiverem disponíveis
                        location.reload();
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification(data.error || 'Erro ao remover ponto', 'error');
                    } else {
                        alert(data.error || 'Erro ao remover ponto');
                    }
                    // Restaurar botão em caso de erro
                    if (removeBtn) {
                        removeBtn.disabled = false;
                        removeBtn.innerHTML = '🗑️ Remover do Itinerário';
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                if (typeof showNotification === 'function') {
                    showNotification('Erro ao remover ponto do itinerário', 'error');
                } else {
                    alert('Erro ao remover ponto do itinerário');
                }
                // Restaurar botão em caso de erro
                if (removeBtn) {
                    removeBtn.disabled = false;
                    removeBtn.innerHTML = '🗑️ Remover do Itinerário';
                }
            });
        };
        
        // Criar alias para compatibilidade
        function removePontoFromItinerary(pontoId) {
            return window.removePontoFromItinerary(pontoId);
        }
    </script>
    
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
            <div class="w-full px-4 py-10 md:py-16">
                <!-- Detalhes Gerais da Viagem - Design Aprimorado -->
                <div class="mb-8 mx-5">
                    <!-- Breadcrumb e Navegação -->
                    <div class="items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('myTrips') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-xl border border-gray-200 transition-all duration-200 text-sm font-medium shadow-sm hover:shadow-md group">
                                <i class="fa-solid fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i> 
                                <span>Minhas Viagens</span>
                            </a>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </div>
                            <span class="text-gray-600 font-medium">Detalhes da Viagem</span>
                        </div>
                    </div>

                    <!-- Card Principal com Informações da Viagem -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <!-- Header com gradiente -->
                        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 px-8 py-6 relative overflow-hidden">
                            <!-- Padrão decorativo de fundo -->
                            <div class="absolute inset-0 opacity-10" style="pointer-events: none;">
                                <div class="absolute top-0 left-0 w-40 h-40 bg-blue-700 rounded-full -translate-x-20 -translate-y-20"></div>
                                <div class="absolute bottom-0 right-0 w-32 h-32 bg-purple-700 rounded-full translate-x-16 translate-y-16"></div>
                                <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-green-700 rounded-full"></div>
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="bg-gray-700 rounded-xl p-3">
                                                <i class="fas fa-map-marked-alt text-white text-2xl"></i>
                                            </div>
                                            <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                                                <div class="relative group">
                                                    <div class="nome-display">
                                                        <h1 class="text-4xl font-bold text-gray-800 mb-1">{{ $viagem->nome_viagem }}</h1>
                                                        <p class="text-blue-600 text-lg">Sua próxima aventura te espera</p>
                                                    </div>
                                                    <div class="nome-edit hidden">
                                                        <input type="text" id="edit-nome-input" class="nome-input places-autocomplete text-4xl font-bold text-gray-800 mb-1 bg-white border-2 border-blue-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $viagem->nome_viagem }}" autocomplete="off">
                                                        <p class="text-blue-600 text-lg">Sua próxima aventura te espera</p>
                                                        <div class="flex space-x-2 mt-2">
                                                            <button class="save-nome-btn px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                                                <i class="fas fa-check mr-1"></i>Salvar
                                                            </button>
                                                            <button class="cancel-nome-btn px-3 py-1 bg-gray-400 text-white text-sm rounded hover:bg-gray-500 transition">
                                                                <i class="fas fa-times mr-1"></i>Cancelar
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button class="edit-nome-btn absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 bg-white hover:bg-blue-50 rounded-full shadow-lg border border-blue-200" title="Editar nome">
                                                        <i class="fas fa-edit text-blue-700 text-sm"></i>
                                                    </button>
                                                </div>
                                                <div class="flex items-center space-x-3 ml-0 md:ml-4 mt-3 md:mt-0">
                                                    <a href="{{ route('viagens.exportar_pdf', $viagem->pk_id_viagem) }}" class="inline-flex items-center px-4 py-2 bg-green-400 hover:bg-white hover:text-green-400 text-black hover:border-2 hover:border-green-400 font-semibold rounded-lg shadow transition" target="_blank">
                                                        <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
                                                    </a>
                                                    <button id="delete-trip-btn" data-trip-id="{{ $viagem->pk_id_viagem }}" class="inline-flex items-center px-4 py-2 text-slate-900 hover:text-white hover:bg-red-500 font-semibold rounded-lg shadow transition">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status da viagem -->
                                        <div class="flex items-center space-x-4">
                                            @php
                                                $dataInicio = \Carbon\Carbon::parse($viagem->data_inicio_viagem);
                                                $dataFim = \Carbon\Carbon::parse($viagem->data_final_viagem);
                                                $hoje = \Carbon\Carbon::now();

                                                if ($hoje->lt($dataInicio)) {
                                                    $status = 'Planejada';
                                                    $statusColor = 'bg-yellow-300 text-yellow-900 border-yellow-400';
                                                    $statusIcon = 'fas fa-clock';
                                                } elseif ($hoje->between($dataInicio, $dataFim)) {
                                                    $status = 'Em andamento';
                                                    $statusColor = 'bg-green-300 text-green-900 border-green-400';
                                                    $statusIcon = 'fas fa-play';
                                                } else {
                                                    $status = 'Concluída';
                                                    $statusColor = 'bg-gray-400 text-gray-900 border-gray-500';
                                                    $statusIcon = 'fas fa-check';
                                                }
                                            @endphp

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }} border">
                                                <i class="{{ $statusIcon }} mr-2"></i>
                                                {{ $status }}
                                            </span>

                                            <span class="text-gray-600 text-sm">
                                                {{ $dataInicio->diffInDays($dataFim) + 1 }} {{ $dataInicio->diffInDays($dataFim) + 1 == 1 ? 'dia' : 'dias' }} de viagem
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Avatar do criador -->
                                    <div class="text-right">
                                        <div class="flex items-center justify-end space-x-3 mb-2">
                                            <div class="text-right">
                                                <div class="text-gray-500 text-sm">Criada por</div>
                                                <div class="text-gray-900 font-semibold">{{ $usuario->name }}</div>
                                            </div>
                                            <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="text-gray-400 text-xs">
                                            {{ \Carbon\Carbon::parse($viagem->created_at)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        
                        <!-- Corpo do card com informações detalhadas -->
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Origem -->
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200 relative group">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-plane-departure text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-green-800 font-semibold text-lg">Origem</div>
                                            <div class="text-green-600 text-sm">Ponto de partida</div>
                                        </div>
                                        <button class="edit-origem-btn opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-green-200 rounded-lg" title="Editar origem">
                                            <i class="fas fa-edit text-green-700 text-sm"></i>
                                        </button>
                                    </div>
                                    <div class="origem-display">
                                        <div class="text-gray-800 font-bold text-xl">{{ $viagem->origem_viagem }}</div>
                                    </div>
                                    <div class="origem-edit hidden">
                                        <input type="text" id="edit-origem-input" class="origem-input places-autocomplete w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-800 font-bold text-xl bg-white" value="{{ $viagem->origem_viagem }}" autocomplete="off">
                                        <div class="flex space-x-2 mt-2">
                                            <button class="save-origem-btn px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                                <i class="fas fa-check mr-1"></i>Salvar
                                            </button>
                                            <button class="cancel-origem-btn px-3 py-1 bg-gray-400 text-white text-sm rounded hover:bg-gray-500 transition">
                                                <i class="fas fa-times mr-1"></i>Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Período -->
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 relative group">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-calendar-alt text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-blue-800 font-semibold text-lg">Período</div>
                                            <div class="text-blue-600 text-sm">Datas da viagem</div>
                                        </div>
                                        <button class="edit-datas-btn opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-blue-200 rounded-lg" title="Editar datas">
                                            <i class="fas fa-edit text-blue-700 text-sm"></i>
                                        </button>
                                    </div>
                                    <div class="datas-display">
                                        <div class="space-y-1">
                                            <div class="text-gray-800 font-bold">{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }}</div>
                                            <div class="text-gray-500 text-sm flex items-center">
                                                <i class="fas fa-arrow-down mr-1"></i>
                                                até
                                            </div>
                                            <div class="text-gray-800 font-bold">{{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="datas-edit hidden">
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs text-blue-700 mb-1">Data Início:</label>
                                                <input type="date" class="data-inicio-input w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 font-bold bg-white" value="{{ $viagem->data_inicio_viagem }}">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-blue-700 mb-1">Data Fim:</label>
                                                <input type="date" class="data-fim-input w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 font-bold bg-white" value="{{ $viagem->data_final_viagem }}">
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="save-datas-btn px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                                    <i class="fas fa-check mr-1"></i>Salvar
                                                </button>
                                                <button class="cancel-datas-btn px-3 py-1 bg-gray-400 text-white text-sm rounded hover:bg-gray-500 transition">
                                                    <i class="fas fa-times mr-1"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Orçamento -->
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 relative group">
                                    <div class="flex space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-wallet text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-purple-800 font-semibold text-lg">Orçamento</div>
                                            <div class="text-purple-600 text-sm">Valor planejado</div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button class="edit-orcamento-btn opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 hover:bg-purple-200 rounded-lg" title="Editar orçamento">
                                                <i class="fas fa-edit text-purple-700 text-sm"></i>
                                            </button>
                                            <button class="m-0 bg-transparent cursor-pointer" id="modal_orc" type="button" title="Orçamento da Viagem">
                                                <i class="fa-solid fa-circle-info text-lg text-purple-600"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="orcamento-display">
                                        <div class="text-gray-800 font-bold text-xl">R$ {{ number_format($viagem->orcamento_viagem, 2, ',', '.') }}</div>
                                        <div class="text-purple-600 text-sm mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Orçamento total
                                        </div>
                                        <div class="text-gray-800 font-bold text-xl">R$ {{ number_format($estatisticas['orcamento_liquido'], 2, ',', '.') }}</div>
                                        <div class="text-green-600 text-sm mt-1">
                                            <i class="fas fa-wallet mr-1"></i>
                                            Orçamento líquido
                                        </div>
                                    </div>
                                    <div class="orcamento-edit hidden">
                                        <div>
                                            <label class="block text-xs text-purple-700 mb-1">Orçamento Total (R$):</label>
                                            <input type="number" step="0.01" class="orcamento-input w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-800 font-bold text-xl bg-white" value="{{ $viagem->orcamento_viagem }}">
                                            <div class="flex space-x-2 mt-2">
                                                <button class="save-orcamento-btn px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition">
                                                    <i class="fas fa-check mr-1"></i>Salvar
                                                </button>
                                                <button class="cancel-orcamento-btn px-3 py-1 bg-gray-400 text-white text-sm rounded hover:bg-gray-500 transition">
                                                    <i class="fas fa-times mr-1"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lista de Destinos -->
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-orange-800 font-semibold text-lg">Destinos</div>
                                            <div class="text-orange-600 text-sm">
                                                {{ $viagem->destinos ? $viagem->destinos->count() : 0 }} 
                                                {{ $viagem->destinos && $viagem->destinos->count() == 1 ? 'destino' : 'destinos' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($viagem->destinos && $viagem->destinos->count() > 0)
                                        <div class="max-h-32 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                            @foreach($viagem->destinos as $index => $destino)
                                                <div class="flex items-center justify-between bg-white/60 rounded-lg p-3 border border-orange-200">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                            {{ $index + 1 }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-gray-800 text-sm">{{ $destino->nome_destino }}</div>
                                                            <div class="text-gray-600 text-xs">
                                                                {{ \Carbon\Carbon::parse($destino->data_chegada_destino)->format('d/m/Y') }} - 
                                                                {{ \Carbon\Carbon::parse($destino->data_partida_destino)->format('d/m/Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-orange-600">
                                                        <i class="fas fa-chevron-right text-xs"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-white/60 rounded-lg p-4 border border-orange-200 text-center">
                                            <i class="fas fa-map-marker text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-gray-600 text-sm">Nenhum destino cadastrado</p>
                                            <p class="text-gray-500 text-xs">Adicione destinos à sua viagem</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Barra de progresso da viagem -->
                            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-800 flex items-center">
                                        <i class="fas fa-tasks mr-2 text-blue-500"></i>
                                        Progresso da Viagem
                                    </h3>
                                    @php
                                        $totalDias = $dataInicio->diffInDays($dataFim) + 1;
                                        
                                        if ($hoje->lt($dataInicio)) {
                                            // Viagem ainda não começou
                                            $diasPassados = 0;
                                            $progresso = 0;
                                        } elseif ($hoje->gt($dataFim)) {
                                            // Viagem já terminou
                                            $diasPassados = $totalDias;
                                            $progresso = 100;
                                        } else {
                                            // Viagem em andamento
                                            $diasPassados = $dataInicio->diffInDays($hoje) + 1;
                                            $diasPassados = round($diasPassados, 1);
                                            $progresso = $totalDias > 0 ? ($diasPassados / $totalDias) * 100 : 0;
                                        }
                                    @endphp
                                    <span class="text-sm text-gray-900">
                                        {{ round($progresso) }}% concluído ({{ $diasPassados }}/{{ $totalDias }} dias)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    {{-- AQUI ESTÁ A MUDANÇA: Usando uma cor sólida para garantir a visualização --}}
                                    <div class="bg-blue-500 h-3 rounded-full transition-all duration-500" style="width: {{ $progresso }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-2">
                                    <span>{{ $dataInicio->format('d/m/Y') }}</span>
                                    <span>Hoje</span>
                                    <span>{{ $dataFim->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sistema de Tabs -->
                <div class="mx-5">
                    <!-- Tab Navigation Aprimorada -->
                    <!-- Tab Navigation Aprimorada -->
                    <div class="bg-white shadow-sm border border-gray-200 mb-6 p-2 rounded-b-xl">
                        <nav class="flex space-x-2">
                            <button 
                                id="tab-visao-geral" 
                                class="tab-button active flex-1 flex items-center justify-center space-x-2 py-3 px-4 rounded-b-lg font-medium text-sm transition-all duration-200"
                                onclick="switchTab('visao-geral')"
                            >
                                <i class="fas fa-eye"></i>
                                <span>Visão Geral</span>
                            </button>
                            <button 
                                id="tab-rotas-mapa" 
                                class="tab-button flex-1 flex items-center justify-center space-x-2 py-3 px-4 rounded-b-lg font-medium text-sm transition-all duration-200"
                                onclick="switchTab('rotas-mapa')"
                            >
                                <i class="fa-solid fa-map"></i>
                                <span>Suas Rotas</span>
                            </button>
                            <button 
                                id="tab-informacoes-estatisticas" 
                                class="tab-button flex-1 flex items-center justify-center space-x-2 py-3 px-4 rounded-b-lg font-medium text-sm transition-all duration-200"
                                onclick="switchTab('informacoes-estatisticas')"
                            >
                                <i class="fas fa-chart-bar"></i>
                                <span>Informações e Estatísticas</span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div id="content-visao-geral" class="tab-panel active">
                            @include('components/myTrips/screenSections/visaoGeral', [
                                'viagem' => $viagem, 
                                'usuario' => $usuario,
                                'hotel' => $hotel ?? collect()
                            ])
                            {{-- Add flights section here if not already included --}}
                        </div>
                        <div id="content-rotas-mapa" class="tab-panel hidden">
                            @include('components/myTrips/screenSections/rotasMapa', ['viagem' => $viagem])
                        </div>
                        <div id="content-informacoes-estatisticas" class="tab-panel hidden">
                            @include('components/myTrips/screenSections/informacoesEstatisticas', [
                                'viagem' => $viagem, 
                                'usuario' => $usuario,
                                'eventos' => $eventos ?? collect()
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de orçamento -->
    @include('components.myTrips.modals.orcamentoModal')

    <!-- Modal de confirmação de exclusão -->
    <div id="delete-trip-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div id="delete-trip-modal-panel" class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
            <!-- Header do modal -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-red-800">Confirmar Exclusão</h3>
                            <p class="text-red-600 text-sm">Esta ação não pode ser desfeita</p>
                        </div>
                    </div>
                    <button id="close-delete-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Corpo do modal -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Excluir viagem: {{ $viagem->nome_viagem }}?</h4>
                    <p class="text-gray-600 text-sm">
                        Todos os dados relacionados à viagem serão permanentemente removidos, incluindo:
                    </p>
                    <ul class="text-gray-500 text-sm mt-3 space-y-1">
                        <li>• Pontos de interesse e roteiros</li>
                        <li>• Informações de voos e hotéis</li>
                        <li>• Viajantes e objetivos</li>
                        <li>• Histórico e preferências</li>
                    </ul>
                </div>

                <!-- Campo de confirmação -->
                <div class="mb-6">
                    <label for="delete-confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Digite "EXCLUIR" para confirmar:
                    </label>
                    <input type="text" id="delete-confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Digite EXCLUIR">
                </div>

                <!-- Botões de ação -->
                <div class="flex space-x-3">
                    <button id="cancel-delete-btn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button id="confirm-delete-btn" disabled class="flex-1 bg-gray-300 text-gray-500 font-semibold py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 cursor-not-allowed">
                        <i class="fas fa-trash-alt"></i>
                        <span>Confirmar Exclusão</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Viajantes -->
    @include('components.myTrips.modals.viajantesModal')

    <!-- Modal de Detalhes dos Viajantes -->
    @include('components.myTrips.modals.viajantesDetailsModal')

    <!-- Modal de Objetivos -->
    @include('components.myTrips.modals.objetivosModal')

    <!-- Modal de Adicionar Objetivo -->
    @include('components.myTrips.modals.addObjetivosModal')

    <!-- Modal de Adicionar Viajante -->
    @include('components.myTrips.modals.addViajantesModal')

    @include('components/explore/detailsModal')

    <!-- Insurance modals (add and details) -->
    @include('components.myTrips.modals.addInsurance')
    @include('components.myTrips.modals.insuranceModal')

    <!-- Scripts externos -->
    <script src="{{ asset('js/inlineEditManager.js') }}"></script>
    <script src="{{ asset('js/lazyLoader.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places,geometry&callback=initInlineEditMap" async defer></script>
    
    <!-- Script para controle das tabs -->
    <script>
        // Função callback do Google Maps
        function checkGoogleMapsLoaded() {
            console.log('Google Maps carregado com sucesso');
        }
        
        function switchTab(tabName) {
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
                panel.classList.remove('active');
            });
            
            // Add active class to clicked tab button
            const tabButton = document.getElementById('tab-' + tabName);
            if (tabButton) {
                tabButton.classList.add('active');
            }
            
            // Show corresponding tab panel
            const panel = document.getElementById('content-' + tabName);
            if (panel) {
                panel.classList.remove('hidden');
                panel.classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Classe para gerenciar modais de forma organizada
            class TripModalManager {
                constructor() {
                    this.initializeDeleteModal();
                    this.initializeOrcamentoModal();
                }

                // Gerenciamento do modal de exclusão
                initializeDeleteModal() {
                    this.deleteTripBtn = document.getElementById('delete-trip-btn');
                    this.deleteTripModal = document.getElementById('delete-trip-modal');
                    this.deleteTripModalPanel = document.getElementById('delete-trip-modal-panel');
                    this.closeDeleteModalBtn = document.getElementById('close-delete-modal-btn');
                    this.cancelDeleteBtn = document.getElementById('cancel-delete-btn');
                    this.confirmDeleteBtn = document.getElementById('confirm-delete-btn');
                    this.deleteConfirmationInput = document.getElementById('delete-confirmation');

                    this.bindDeleteModalEvents();
                }

                bindDeleteModalEvents() {
                    if (this.deleteTripBtn) {
                        this.deleteTripBtn.addEventListener('click', () => this.openDeleteModal());
                    }

                    if (this.closeDeleteModalBtn) {
                        this.closeDeleteModalBtn.addEventListener('click', () => this.closeDeleteModal());
                    }

                    if (this.cancelDeleteBtn) {
                        this.cancelDeleteBtn.addEventListener('click', () => this.closeDeleteModal());
                    }

                    if (this.confirmDeleteBtn) {
                        this.confirmDeleteBtn.addEventListener('click', () => this.deleteTrip());
                    }

                    if (this.deleteConfirmationInput) {
                        this.deleteConfirmationInput.addEventListener('input', () => this.updateDeleteButton());
                        this.deleteConfirmationInput.addEventListener('keypress', (e) => {
                            if (e.key === 'Enter' && !this.confirmDeleteBtn.disabled) {
                                this.deleteTrip();
                            }
                        });
                    }

                    // Eventos globais
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.isDeleteModalOpen()) {
                            this.closeDeleteModal();
                        }
                    });

                    if (this.deleteTripModal) {
                        this.deleteTripModal.addEventListener('click', (e) => {
                            if (e.target === this.deleteTripModal) {
                                this.closeDeleteModal();
                            }
                        });
                    }
                }

                openDeleteModal() {
                    if (!this.deleteTripModal) return;
                    
                    this.deleteTripModal.classList.remove('hidden');
                    this.deleteTripModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                    
                    setTimeout(() => {
                        this.deleteTripModalPanel.classList.remove('scale-95', 'opacity-0');
                        this.deleteTripModalPanel.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }

                closeDeleteModal() {
                    if (!this.deleteTripModalPanel) return;
                    
                    this.deleteTripModalPanel.classList.remove('scale-100', 'opacity-100');
                    this.deleteTripModalPanel.classList.add('scale-95', 'opacity-0');
                    
                    setTimeout(() => {
                        this.deleteTripModal.classList.add('hidden');
                        this.deleteTripModal.classList.remove('flex');
                        document.body.style.overflow = '';
                        this.resetDeleteModal();
                    }, 300);
                }

                resetDeleteModal() {
                    if (this.deleteConfirmationInput) {
                        this.deleteConfirmationInput.value = '';
                    }
                    this.updateDeleteButton();
                }

                updateDeleteButton() {
                    if (!this.confirmDeleteBtn || !this.deleteConfirmationInput) return;
                    
                    const inputValue = this.deleteConfirmationInput.value.trim().toUpperCase();
                    const isValid = inputValue === 'EXCLUIR';

                    this.confirmDeleteBtn.disabled = !isValid;
                    
                    if (isValid) {
                        this.confirmDeleteBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                        this.confirmDeleteBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white', 'cursor-pointer');
                    } else {
                        this.confirmDeleteBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white', 'cursor-pointer');
                        this.confirmDeleteBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    }
                }

                async deleteTrip() {
                    const tripId = this.deleteTripBtn.dataset.tripId;
                    
                    this.setDeleteButtonLoading(true);

                    try {
                        const response = await fetch(`/viagens/${tripId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            setTimeout(() => {
                                window.location.href = '/myTrips';
                            }, 1500);
                        } else {
                            this.showNotification(data.message || 'Erro ao excluir viagem', 'error');
                            this.closeDeleteModal();
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        this.showNotification('Erro ao excluir viagem. Tente novamente.', 'error');
                        this.closeDeleteModal();
                    } finally {
                        this.setDeleteButtonLoading(false);
                    }
                }

                setDeleteButtonLoading(isLoading) {
                    if (!this.confirmDeleteBtn) return;
                    
                    this.confirmDeleteBtn.disabled = true;
                    
                    if (isLoading) {
                        this.confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Excluindo...';
                    } else {
                        this.confirmDeleteBtn.innerHTML = '<i class="fas fa-trash-alt mr-2"></i><span>Confirmar Exclusão</span>';
                    }
                }

                isDeleteModalOpen() {
                    return this.deleteTripModal && !this.deleteTripModal.classList.contains('hidden');
                }

                showNotification(message, type = 'info') {
                    if (typeof showNotification === 'function') {
                        showNotification(message, type);
                    } else {
                        alert(message);
                    }
                }

                // Gerenciamento do modal de orçamento (código existente)
                initializeOrcamentoModal() {
                    const openOrcamentoModalBtn = document.getElementById('modal_orc');
                    const closeOrcamentoModalBtn = document.getElementById('close-orcamento-modal-btn');
                    const closeOrcamentoModalFooterBtn = document.getElementById('close-objetivos-modal-footer-btn');
                    const orcamentoModal = document.getElementById('orcamento-modal');
                    const orcamentoModalPanel = document.getElementById('orcamento-modal-panel');
                    const orcamentoModalOverlay = document.getElementById('orcamento-modal-overlay');

                    const openOrcamentoModal = () => {
                        orcamentoModal.classList.remove('hidden');
                        orcamentoModal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => {
                            orcamentoModalPanel.classList.remove('scale-95', 'opacity-0');
                            orcamentoModalPanel.classList.add('scale-100', 'opacity-100');
                        }, 10);
                    };

                    const closeOrcamentoModal = () => {
                        if (!orcamentoModalPanel) return;
                        orcamentoModalPanel.classList.remove('scale-100', 'opacity-100');
                        orcamentoModalPanel.classList.add('scale-95', 'opacity-0');
                        setTimeout(() => {
                            orcamentoModal.classList.add('hidden');
                            orcamentoModal.classList.remove('flex');
                            document.body.style.overflow = '';
                        }, 300);
                    };

                    if (openOrcamentoModalBtn) openOrcamentoModalBtn.addEventListener('click', openOrcamentoModal);
                    if (closeOrcamentoModalBtn) closeOrcamentoModalBtn.addEventListener('click', closeOrcamentoModal);
                    if (closeOrcamentoModalFooterBtn) closeOrcamentoModalFooterBtn.addEventListener('click', closeOrcamentoModal);
                    if (orcamentoModalOverlay) orcamentoModalOverlay.addEventListener('click', closeOrcamentoModal);

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            if (orcamentoModal && !orcamentoModal.classList.contains('hidden')) {
                                closeOrcamentoModal();
                            }
                        }
                    });
                }
            }

            // Inicializar os gerenciadores
            window.inlineEditManager = new InlineEditManager(window.currentTripId);
            window.lazyLoader = new LazyLoader(window.currentTripId);
            new TripModalManager();

            // Fallback caso a API já esteja carregada
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                console.log('Google Maps já carregado - inicializando autocomplete imediatamente');
                window.inlineEditManager.initPlacesAutocomplete();
            } else {
                // Tentar novamente após um delay
                setTimeout(() => {
                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        console.log('Google Maps carregado após delay - inicializando autocomplete');
                        window.inlineEditManager.initPlacesAutocomplete();
                    } else {
                        console.log('Google Maps ainda não disponível após delay');
                    }
                }, 2000);
            }
        });
    </script>

    <!-- Estilos CSS aprimorados para as tabs -->
    <style>
        .tab-button {
            color: #6b7280;
            background-color: transparent;
        }
        
        .tab-button:hover {
            color: #374151;
            background-color: #f3f4f6;
        }
        
        .tab-button.active {
            color: #3b82f6;
            background-color: #dbeafe;
        }
        
        .tab-panel {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Animações para os cards */
        .bg-gradient-to-br {
            transition: transform 0.2s ease-in-out;
        }
        
        .bg-gradient-to-br:hover {
            transform: translateY(-2px);
        }
        
        /* Scrollbar customizado para lista de destinos */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #fb923c #fed7aa;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #fed7aa;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #fb923c;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }
    </style>

<script>
    
</script>
{{-- Mostra o seguro apenas se houver valor --}}

<script>
document.addEventListener("DOMContentLoaded", function () {
    let seguro = sessionStorage.getItem('selectedSeguroName');
    if (seguro) {
        document.getElementById('seguro-detalhe')?.innerText = seguro;
    }
});
</script>

<!-- Funções JavaScript necessárias para o modal de detalhes -->
<script>
    /**
     * Atualiza a interface após remoção de um ponto
     * @param {number} pontoId - ID do ponto removido
     */
    function updateItineraryAfterRemoval(pontoId) {
        // Remover do painel lateral (aba Suas Rotas)
        const pontosContainer = document.getElementById('pontos-container');
        if (pontosContainer) {
            const pontoElements = pontosContainer.querySelectorAll('.group');
            pontoElements.forEach(element => {
                // Verificar se este elemento corresponde ao ponto removido
                const onclickAttr = element.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(`focusOnPoint(${pontoId}`)) {
                    element.remove();
                }
            });
            
            // Se não há mais pontos na data atual, mostrar mensagem
            if (pontosContainer.children.length === 0) {
                const noPointsMessage = document.getElementById('no-points-message');
                if (noPointsMessage) {
                    noPointsMessage.style.display = 'block';
                }
                pontosContainer.innerHTML = '';
            }
        }

        // Atualizar contador de pontos
        const totalPontosElement = document.querySelector('.text-blue-700');
        if (totalPontosElement && totalPontosElement.textContent.includes('Ponto')) {
            const currentText = totalPontosElement.textContent;
            const currentCount = parseInt(currentText.match(/\d+/)?.[0]) || 0;
            const newCount = Math.max(0, currentCount - 1);
            const newText = newCount === 1 ? 
                `${newCount} Ponto de interesse` : 
                `${newCount} Pontos de interesse`;
            totalPontosElement.textContent = newText;
        }

        // Remover marcador do mapa se existir
        if (typeof pontosInteresseMarkers !== 'undefined') {
            pontosInteresseMarkers.forEach((markerObj, index) => {
                if (markerObj.pontoId == pontoId) {
                    markerObj.marker.setMap(null);
                    if (markerObj.infoWindow) {
                        markerObj.infoWindow.close();
                    }
                    pontosInteresseMarkers.splice(index, 1);
                }
            });
        }

        // Atualizar numeração dos pontos restantes
        refreshPontoNumbering();
    }
                `${newCount} Pontos de interesse`;
            totalPontosElement.textContent = newText;
        }

        // Remover marcador do mapa se existir
        if (typeof pontosInteresseMarkers !== 'undefined') {
            pontosInteresseMarkers.forEach((markerObj, index) => {
                if (markerObj.pontoId == pontoId) {
                    markerObj.marker.setMap(null);
                    if (markerObj.infoWindow) {
                        markerObj.infoWindow.close();
                    }
                    pontosInteresseMarkers.splice(index, 1);
                }
            });
        }

        // Atualizar numeração dos pontos restantes
        refreshPontoNumbering();
    }

    /**
     * Atualiza a numeração dos pontos após remoção
     */
    function refreshPontoNumbering() {
        const pontosContainer = document.getElementById('pontos-container');
        if (pontosContainer) {
            const pontoElements = pontosContainer.querySelectorAll('.group');
            pontoElements.forEach((element, index) => {
                const numberCircle = element.querySelector('.w-8.h-8');
                if (numberCircle) {
                    numberCircle.textContent = index + 1;
                }
            });
        }
    }

    /**
     * Mostra uma notificação para o usuário
     * @param {string} message - Mensagem a ser exibida
     * @param {string} type - Tipo da notificação ('success', 'error', 'info')
     */
    function showNotification(message, type = 'info') {
        // Criar elemento de notificação
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
        
        // Definir cores baseadas no tipo
        let bgColor, textColor, icon;
        switch(type) {
            case 'success':
                bgColor = 'bg-green-500';
                textColor = 'text-white';
                icon = '✅';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                icon = '❌';
                break;
            default:
                bgColor = 'bg-blue-500';
                textColor = 'text-white';
                icon = 'ℹ️';
        }
        
        notification.className += ` ${bgColor} ${textColor}`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <span>${icon}</span>
                <span>${message}</span>
            </div>
        `;
        
        // Adicionar ao DOM
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
</script>


@endsection
