<div id="orcamento-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur aprimorado -->
    <div id="orcamento-modal-overlay" class="absolute inset-0 bg-gradient-to-br from-gray-900/60 to-gray-800/60 backdrop-blur-md" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="orcamento-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden max-h-[90vh]">
        <!-- Header com gradiente -->
        <div class="bg-purple-500 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <i class="fa-solid fa-money-bills text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Detalhes dos gastos da viagem</h3>
                        <p class="text-green-100 text-base">Acompanhe os seu orçamento e como ele esta sendo utilizado</p>
                    </div>
                </div>
                <button id="close-orcamento-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Corpo do Modal -->
        <div class="p-8 overflow-y-auto max-h-[60vh]">
            <h4 class="text-xl font-semibold text-gray-800 mb-4">Hotéis</h4>
            @forelse ($viagem->hotel as $hotel)
                @php
                    $checkin = \Carbon\Carbon::parse($hotel->data_check_in);
                    $checkout = \Carbon\Carbon::parse($hotel->data_check_out);
                    $noites = $checkin->diffInDays($checkout);
                    $preco_total_hotel = $hotel->preco * $noites;
                @endphp
                <div class="mb-4 p-4 rounded-lg border border-gray-100 shadow-sm bg-gray-50">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">{{ $hotel->nome_hotel }}</span>
                        <div class="flex flex-col gap-1">
                            <span class="text-green-700 font-bold">preço por noite R$ {{ number_format($hotel->preco, 2, ',', '.') }}</span>
                            <span class="text-green-700 font-bold">preço total R$ {{ number_format($preco_total_hotel, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-2 text-sm text-gray-600">
                        <span>Check-in: {{ \Carbon\Carbon::parse($hotel->data_check_in)->format('d/m/Y') }}</span>
                        <span>Check-out: {{ \Carbon\Carbon::parse($hotel->data_check_out)->format('d/m/Y') }}</span>
                        <span>{{ $noites }} {{ $noites == 1 ? 'noite' : 'noites' }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 mb-4">Nenhum hotel cadastrado.</p>
            @endforelse

            <hr class="my-6">

            <h4 class="text-xl font-semibold text-gray-800 mb-4">Voos</h4>
            @php
                $numero_viajantes = $viajantes->count();
            @endphp
            <div class="mb-2 text-sm text-gray-600">
                <i class="fas fa-users mr-1"></i>
                Número de passageiros: <span class="font-semibold">{{ $numero_viajantes }}</span>
            </div>
            
            @forelse ($voos as $voo)
                @php
                    $preco_total_voo = $voo->preco_voo * $numero_viajantes;
                @endphp
                <div class="mb-4 p-4 rounded-lg border border-gray-100 shadow-sm bg-gray-50">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">{{ $voo->companhia_voo }} - {{ $voo->numero_voo ?? $voo->flight_number ?? '' }}</span>
                        <div class="flex flex-col gap-1 text-right">
                            <span class="text-blue-700 font-bold">Por passageiro: R$ {{ number_format($voo->preco_voo, 2, ',', '.') }}</span>
                            <span class="text-blue-700 font-bold">Total ({{ $numero_viajantes }}x): R$ {{ number_format($preco_total_voo, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-2 text-sm text-gray-600">
                        <span>Decolagem inicial: {{ \Carbon\Carbon::parse($voo->data_hora_partida)->format('d/m/Y H:i') }}</span>
                        <span>Pouso final: {{ \Carbon\Carbon::parse($voo->data_hora_chegada)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 mb-4">Nenhum voo cadastrado.</p>
            @endforelse

            <hr class="my-6">

            <h4 class="text-xl font-semibold text-gray-800 mb-4">Seguros de Viagem</h4>
            
            @forelse ($seguros ?? collect() as $seguro)
                @php
                    // Usar preco_pix como padrão, ou preco_cartao se preco_pix não estiver disponível
                    $preco_seguro = $seguro->preco_pix ?? $seguro->preco_cartao ?? 0;
                    $preco_total_seguro = $preco_seguro * $numero_viajantes;
                @endphp
                <div class="mb-4 p-4 rounded-lg border border-gray-100 shadow-sm bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-700">{{ $seguro->plano ?? 'Seguro de Viagem' }}</span>
                            <span class="text-sm text-gray-500">{{ $seguro->seguradora }}</span>
                        </div>
                        <div class="flex flex-col gap-1 text-right">
                            <span class="text-purple-700 font-bold">Por pessoa: R$ {{ number_format($preco_seguro, 2, ',', '.') }}</span>
                            <span class="text-purple-700 font-bold">Total ({{ $numero_viajantes }}x): R$ {{ number_format($preco_total_seguro, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    @if($seguro->cobertura_medica)
                        <div class="mt-2 text-sm text-gray-600">
                            <strong>Cobertura Médica:</strong> {{ $seguro->cobertura_medica }}
                        </div>
                    @endif
                    @if($seguro->cobertura_bagagem)
                        <div class="mt-2 text-sm text-gray-600">
                            <strong>Cobertura Bagagem:</strong> {{ $seguro->cobertura_bagagem }}
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-400 mb-4">Nenhum seguro cadastrado.</p>
            @endforelse

            <hr class="my-6">

            <div class="flex justify-between items-center mt-6">
                <h4 class="text-lg font-semibold text-gray-800">Total do Orçamento</h4>
                @php
                    $preco_total = 0;
                    
                    // Soma dos hotéis
                    foreach ($viagem->hotel as $hotel) {
                        $checkin = \Carbon\Carbon::parse($hotel->data_check_in);
                        $checkout = \Carbon\Carbon::parse($hotel->data_check_out);
                        $noites = $checkin->diffInDays($checkout);
                        $preco_total += $hotel->preco * $noites;
                    }
                    
                    // Soma dos voos (multiplicado pelo número de viajantes)
                    foreach ($voos as $voo) {
                        $preco_total += $voo->preco_voo * $numero_viajantes;
                    }
                    
                    // Soma dos seguros (multiplicado pelo número de viajantes)
                    foreach (($seguros ?? collect()) as $seguro) {
                        $preco_seguro = $seguro->preco_pix ?? $seguro->preco_cartao ?? 0;
                        $preco_total += $preco_seguro * $numero_viajantes;
                    }
                @endphp
                
                <span class="text-green-600 text-2xl font-bold">R$ {{ number_format($preco_total, 2, ',', '.') }}</span>
            </div>

            <!-- Breakdown do total -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <h5 class="text-sm font-semibold text-gray-700 mb-2">Detalhamento do total:</h5>
                @php
                    $total_hoteis = 0;
                    $total_voos = 0;
                    $total_seguros = 0;
                    
                    foreach ($viagem->hotel as $hotel) {
                        $checkin = \Carbon\Carbon::parse($hotel->data_check_in);
                        $checkout = \Carbon\Carbon::parse($hotel->data_check_out);
                        $noites = $checkin->diffInDays($checkout);
                        $total_hoteis += $hotel->preco * $noites;
                    }
                    
                    foreach ($voos as $voo) {
                        $total_voos += $voo->preco_voo * $numero_viajantes;
                    }
                    
                    foreach (($seguros ?? collect()) as $seguro) {
                        $preco_seguro = $seguro->preco_pix ?? $seguro->preco_cartao ?? 0;
                        $total_seguros += $preco_seguro * $numero_viajantes;
                    }
                @endphp
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-gray-600">Hospedagem</div>
                        <div class="font-bold text-green-600">R$ {{ number_format($total_hoteis, 2, ',', '.') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-600">Voos</div>
                        <div class="font-bold text-blue-600">R$ {{ number_format($total_voos, 2, ',', '.') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-600">Seguros</div>
                        <div class="font-bold text-purple-600">R$ {{ number_format($total_seguros, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer com ações -->
        <div class="px-8 pb-8 pt-4 border-t border-gray-200 flex justify-end">
            <button id="close-objetivos-modal-footer-btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                Fechar
            </button>
        </div>
    </div>
</div>
