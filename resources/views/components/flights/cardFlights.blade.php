<div class="bg-white rounded-lg shadow-md px-8 py-6 mb-10">
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
            <span class="text-xs text-gray-500 align-center">
                {{-- Aeroporto de saída --}}
                {{ $flight['flights'][0]['departure_airport']['name'] ?? '--' }}
            </span>
        </div>
        <!-- Duração total -->
        <div class="flex flex-col items-center w-32">
            <span class="text-sm text-gray-500">
                {{-- Duração --}}
                {{ $flight['total_duration'] ?? '--' }} min
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
                {{ $flight['flights'][count($flight['flights'])-1]['arrival_airport']['name'] ?? '--' }}
            </span>
        </div>
        <!-- Preço e Seleção -->
        <div class="flex flex-col items-end flex-1">
            <span class="text-blue-600 text-2xl font-bold">
                R$ {{ number_format($flight['price'] ?? 0, 2, ',', '.') }}
            </span>
            <span class="text-xs text-gray-500">por pessoa</span>
        </div>
    </div>
    <!-- Extensões e informações extras -->
    <div class="flex flex-wrap gap-2 mt-4">
        @if(isset($flight['extensions']) && is_array($flight['extensions']))
            @foreach($flight['extensions'] as $ext)
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $ext }}</span>
            @endforeach
        @endif
    </div>
</div>