@extends('index')
@section('content')
    <script>
        // Definir ID da viagem atual globalmente
        window.currentTripId = {{ $viagem->pk_id_viagem }};
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
                                            <div>
                                                <h1 class="text-4xl font-bold text-gray-800 mb-1">{{ $viagem->destino_viagem }}</h1>
                                                <p class="text-blue-600 text-lg">Sua próxima aventura te espera</p>
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
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-plane-departure text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-green-800 font-semibold text-lg">Origem</div>
                                            <div class="text-green-600 text-sm">Ponto de partida</div>
                                        </div>
                                    </div>
                                    <div class="text-gray-800 font-bold text-xl">{{ $viagem->origem_viagem }}</div>
                                </div>
                                
                                <!-- Período -->
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-calendar-alt text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-blue-800 font-semibold text-lg">Período</div>
                                            <div class="text-blue-600 text-sm">Datas da viagem</div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="text-gray-800 font-bold">{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }}</div>
                                        <div class="text-gray-500 text-sm flex items-center">
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            até
                                        </div>
                                        <div class="text-gray-800 font-bold">{{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                
                                <!-- Orçamento -->
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                                    <div class="flex space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-wallet text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-purple-800 font-semibold text-lg">Orçamento</div>
                                            <div class="text-purple-600 text-sm">Valor planejado</div>
                                        </div>
                                        <div class="w-full flex items-center justify-end">
                                            <button class="m-0 bg-transparent cursor-pointer" id="modal_orc" type="button" title="Orçamento da Viagem">
                                                <i class="fa-solid fa-circle-info text-lg text-purple-600"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-gray-800 font-bold text-xl">R$ {{ number_format($viagem->orcamento_viagem, 2, ',', '.') }}</div>
                                    <div class="text-purple-600 text-sm mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Orçamento total
                                    </div>
                                    <div class="text-gray-800 font-bold text-xl">R$ 
                                        @php
                                            $orcamento_liquido = $viagem->orcamento_viagem;
                                        @endphp
                                        @foreach ($viagem->hotel as $hotel)
                                            @if($hotel->preco && $hotel->data_check_in && $hotel->data_check_out)
                                                @php
                                                    $checkin = \Carbon\Carbon::parse($hotel->data_check_in);
                                                    $checkout = \Carbon\Carbon::parse($hotel->data_check_out);
                                                    $noites = $checkin->diffInDays($checkout);
                                                    // Remove "R$", espaços e troca vírgula por ponto
                                                    $precoFloat = convertToFloat($hotel->preco);
                                                    $total = $precoFloat * $noites;
                                                    $orcamento_liquido -= $total;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @foreach ($voos as $voo)
                                            @if($voo->preco_voo)
                                                @php
                                                    $precoFloat = $voo->preco_voo;
                                                    $orcamento_liquido -= $precoFloat;
                                                @endphp
                                            @endif
                                        @endforeach
                                        {{ number_format($orcamento_liquido, 2, ',', '.') }}
                                    </div>
                                    <div class="text-purple-600 text-sm mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Orçamento líquido
                                    </div>
                                </div>
                                
                                <!-- Estatísticas rápidas -->
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center p-6">
                                            <i class="fas fa-chart-line text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-orange-800 font-semibold text-lg">Resumo</div>
                                            <div class="text-orange-600 text-sm">Estatísticas</div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 text-sm">Viajantes:</span>
                                            <span class="font-bold text-gray-800">{{ $viajantes->count() }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 text-sm">Objetivos:</span>
                                            <span class="font-bold text-gray-800">{{ $objetivos->count() }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 text-sm">Locais:</span>
                                            <span class="font-bold text-gray-800">{{ $pontosInteresse->count() }}</span>
                                        </div>
                                    </div>
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
                            @include('components/myTrips/screenSections/visaoGeral', ['viagem' => $viagem, 'usuario' => $usuario])
                            {{-- Add flights section here if not already included --}}
                            {{-- Add insurance section below flights section --}}
                            @include('components/myTrips/screenSections/themes/insuranceSection', ['seguros' => $seguros])
                        </div>
                        <div id="content-informacoes-estatisticas" class="tab-panel hidden">
                            @include('components/myTrips/screenSections/informacoesEstatisticas', ['viagem' => $viagem, 'usuario' => $usuario])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de orçamento -->
    @include('components.myTrips.modals.orcamentoModal')

    <!-- Modal de Viajantes -->
    @include('components.myTrips.modals.viajantesModal')

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

    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&libraries=geometry&callback=checkGoogleMapsLoaded" async defer></script>
    
    <!-- Script para controle das tabs -->
    <script>
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
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Show corresponding tab panel
            const panel = document.getElementById('content-' + tabName);
            panel.classList.remove('hidden');
            panel.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const openOrcamentoModalBtn = document.getElementById('modal_orc');
            const closeOrcamentoModalBtn = document.getElementById('close-orcamento-modal-btn');
            const closeOrcamentoModalFooterBtn = document.getElementById('close-objetivos-modal-footer-btn');
            const orcamentoModal = document.getElementById('orcamento-modal');
            const orcamentoModalPanel = document.getElementById('orcamento-modal-panel');
            const orcamentoModalOverlay = document.getElementById('orcamento-modal-overlay');

            // Função para abrir o modal
            const openOrcamentoModal = () => {
                orcamentoModal.classList.remove('hidden');
                orcamentoModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    orcamentoModalPanel.classList.remove('scale-95', 'opacity-0');
                    orcamentoModalPanel.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            // Função para fechar o modal
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

            // Eventos de abrir
            if (openOrcamentoModalBtn) openOrcamentoModalBtn.addEventListener('click', openOrcamentoModal);

            // Eventos de fechar
            if (closeOrcamentoModalBtn) closeOrcamentoModalBtn.addEventListener('click', closeOrcamentoModal);
            if (closeOrcamentoModalFooterBtn) closeOrcamentoModalFooterBtn.addEventListener('click', closeOrcamentoModal);
            if (orcamentoModalOverlay) orcamentoModalOverlay.addEventListener('click', closeOrcamentoModal);

            // Escape fecha o modal
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    if (orcamentoModal && !orcamentoModal.classList.contains('hidden')) {
                        closeOrcamentoModal();
                    }
                }
            });
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
    </style>

<script>
    
</script>
{{-- Mostra o seguro apenas se houver valor --}}
@php
    $seguroSelecionado = $seguroSelecionado ?? '';
@endphp
@if(!empty($seguroSelecionado))
<li>
    <b>Seguro de viagem:</b>
    <span id="seguro-detalhe">
        {{ $seguroSelecionado }}
    </span>
</li>
@endif
<script>
document.addEventListener("DOMContentLoaded", function () {
    let seguro = sessionStorage.getItem('selectedSeguroName');
    if (seguro) {
        document.getElementById('seguro-detalhe')?.innerText = seguro;
    }
});
</script>

{{-- Exibe o seguro de viagem escolhido, se houver --}}
@isset($viagem->seguro_nome)
    @if(!empty($viagem->seguro_nome))
        <li>
            <b>Seguro de viagem:</b>
            <span id="seguro-detalhe">
                {{ $viagem->seguro_nome }}
            </span>
        </li>
    @endif
@endisset
@endsection
