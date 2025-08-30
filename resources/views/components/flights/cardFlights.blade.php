<div class="flight-card relative flex bg-white rounded-lg shadow-md px-8 py-6 mb-10 transition-all duration-200" data-index="{{ $index }}">
    <!-- Checkbox lateral -->
    <div class="absolute left-0 top-1/2 -translate-y-1/2 pl-4">
        <input type="checkbox"
            class="select-flight-checkbox"
            data-voo='@json($flight)'
            data-preco="{{ $flight['price'] }}"
            data-index="{{ $index }}"
        >
    </div>
    <div class="flex-1 pl-12">
        <div class="flex items-center">
            <!-- Companhia -->
            <div class="flex items-center w-48">
                <img src="{{ $flight['airline_logo'] ?? '' }}" alt="Logo" class="h-6 mr-2">
                <div>
                    <span class="block text-base font-semibold text-gray-800">
                        {{-- Nome da companhia --}}
                        {{ $flight['flights'][0]['airline'] ?? 'Companhia desconhecida' }}
                    </span>
                    <span class="block text-xs text-gray-500">
                        {{-- Número do voo --}}
                        {{ $flight['flights'][0]['flight_number'] ?? 'Voo' }}
                    </span>
                </div>
            </div>
            <!-- Horário de saída -->
            <div class="flex flex-col items-center w-32">
                <span class="text-2xl font-bold text-black">
                    {{-- Horário de saída --}}
                    {{ isset($flight['flights'][0]['departure_airport']['time']) ? \Carbon\Carbon::parse($flight['flights'][0]['departure_airport']['time'])->format('H:i') : '--:--' }}
                </span>
                <span class="text-xs text-gray-500 align-center">
                    {{-- Aeroporto de saída --}}
                    {{ $flight['flights'][0]['departure_airport']['id'] ?? '--' }}
                </span>
                <span class="text-xs text-gray-500 align-center text-center">
                    {{-- Aeroporto de saída --}}
                    {{ $flight['flights'][0]['departure_airport']['name'] ?? '--' }}
                </span>
            </div>
            <!-- Duração total -->
            <div class="flex flex-col items-center w-32">
                <span class="text-sm text-gray-500">
                    {{-- Duração --}}
                    {{ number_format($flight['total_duration']/60, 0, ':', ',') ?? '--' }} horas
                </span>
                <i class="fa-solid fa-plane text-gray-400 my-1"></i>
                <span class="text-xs text-gray-500">
                    {{-- Tipo de viagem --}}
                    {{ $flight['type'] ?? '' }}
                </span>
                <span class="text-xs text-gray-500">
                    {{-- Classe de viagem --}}
                    {{ $flight['flights'][0]['travel_class'] ?? 'não tem' }}
                </span>
            </div>
            <!-- Horário de chegada -->
            <div class="flex flex-col items-center w-32">
                <span class="text-2xl font-bold text-black">
                    {{-- Horário de chegada --}}
                    {{ isset($flight['flights'][count($flight['flights'])-1]['arrival_airport']['time']) ? \Carbon\Carbon::parse($flight['flights'][count($flight['flights'])-1]['arrival_airport']['time'])->format('H:i') : '--:--' }}
                </span>
                <span class="text-xs text-gray-500 align-center">
                    {{-- Aeroporto de chegada --}}
                    {{ $flight['flights'][count($flight['flights'])-1]['arrival_airport']['id'] ?? '--' }}
                </span>
                <span class="text-xs text-gray-500 align-center text-center">
                    {{-- Aeroporto de chegada --}}
                    {{ $flight['flights'][count($flight['flights'])-1]['arrival_airport']['name'] ?? '--' }}
                </span>
            </div>
            <!-- Preço e Seleção -->
            <div class="flex flex-col items-end flex-1">
                <span class="text-blue-600 text-2xl font-bold">
                    {{ number_format($flight['price'] ?? 0, 2, ',', '.') }} {{ $user->currency ?? 'R$' }}
                </span>
                <span class="text-xs text-gray-500">por pessoa</span>
            </div>
        </div>
        <button class="ml-auto text-blue-600 font-semibold flex items-center hover:underline ver-detalhes-btn" data-target="detalhes-{{ $index }}">
        Ver Detalhes <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
        </button>
        <div id="detalhes-{{ $index }}" class="detalhes-viagem hidden px-8 py-8 bg-gray-50 rounded-xl shadow-inner mt-6 transition-all duration-300">
        <h3 class="text-2xl font-bold mb-6 text-blue-700 flex items-center gap-2">
            <i class="fa-solid fa-plane-departure"></i> Detalhes do Voo
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informações principais -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-plane text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Companhia:</span>
                    <span class="companhia-aerea">{{ is_array($flight['flights'][0]['airline']) ? implode(', ', $flight['flights'][0]['airline']) : ($flight['flights'][0]['airline'] ?? 'Desconhecida') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-hashtag text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Nº do Voo:</span>
                    <span>{{ $flight['flights'][0]['flight_number'] ?? '--' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-chair text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Classe:</span>
                    <span>{{ $flight['flights'][0]['travel_class'] ?? '--' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-clock text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Duração Total:</span>
                    <span>{{ number_format($flight['total_duration']/60, 2, 'h ', 'min') ?? '--' }} min</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-wifi text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Multimídia:</span>
                    <span>
                        @if(isset($flight['flights'][0]['extensions'][3]) && $flight['flights'][0]['extensions'][3]) Disponível
                        @else Não disponível
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-ruler-horizontal text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Espaço para as pernas:</span>
                    <span>
                        {{ $flight['flights'][0]['legroom'] ?? 'Padrão' }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-wifi text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Wifi:</span>
                    <span>
                        @if(isset($flight['flights'][0]['extensions'][1]) && $flight['flights'][0]['extensions'][1]) Disponível
                        @else Não disponível
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-plug text-blue-500"></i>
                    <span class="font-semibold text-gray-700">Tomada para celular:</span>
                    <span>
                        @if(isset($flight['flights'][0]['extensions'][2]) && $flight['flights'][0]['extensions'][2]) Disponível
                        @else Não disponível
                        @endif
                    </span>
                </div>
            </div>
            <!-- Trecho do voo -->
            <div class="space-y-6">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fa-solid fa-plane-departure text-green-600 mr-2"></i>
                        <span class="font-semibold text-gray-700">Saída:</span>
                        <span class="ml-2">{{ $flight['flights'][0]['departure_airport']['name'] ?? '--' }} ({{ $flight['flights'][0]['departure_airport']['id'] ?? '--' }})</span>
                        <span class="ml-4 text-gray-500">{{ isset($flight['flights'][0]['departure_airport']['time']) ? \Carbon\Carbon::parse($flight['flights'][0]['departure_airport']['time'])->format('d/m/Y H:i') : '--' }}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fa-solid fa-plane-arrival text-red-600 mr-2"></i>
                        <span class="font-semibold text-gray-700">Chegada:</span>
                        <span class="ml-2">{{ $flight['flights'][count($flight['flights'])-1]['arrival_airport']['name'] ?? '--' }} ({{ $flight['flights'][count($flight['flights'])-1]['arrival_airport']['id'] ?? '--' }})</span>
                        <span class="ml-4 text-gray-500">{{ isset($flight['flights'][count($flight['flights'])-1]['arrival_airport']['time']) ? \Carbon\Carbon::parse($flight['flights'][count($flight['flights'])-1]['arrival_airport']['time'])->format('d/m/Y H:i') : '--' }}</span>
                    </div>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Paradas:</span>
                    <span>
                        {{ isset($flight['stops']) ? $flight['stops'] : (count($flight['flights']) - 1) }}
                    </span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Extensões:</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @if(isset($flight['extensions']) && is_array($flight['extensions']))
                            @foreach($flight['extensions'] as $ext)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $ext }}</span>
                            @endforeach
                        @else
                            <span class="text-xs text-gray-400">Nenhuma extensão</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>