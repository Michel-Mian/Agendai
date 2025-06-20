<div class="bg-white rounded-lg shadow-md px-8 py-6 mb-10">
    <div class="flex items-center">
        <!-- Companhia -->
        <div class="flex items-center w-48">
            <i class="fa-solid fa-plane-departure text-2xl mr-3"></i>
            <div>
                <span class="block text-base font-semibold text-gray-800">
                    {{ $flight['airline']['name'] ?? 'Companhia desconhecida' }}
                </span>
                <span class="block text-xs text-gray-500">
                    {{ $flight['flight']['iata'] ?? 'Voo' }}
                </span>
            </div>
        </div>
        <!-- Horário de saída -->
        <div class="flex flex-col items-center w-32">
            <span class="text-2xl font-bold text-black">
                {{ \Carbon\Carbon::parse($flight['departure']['scheduled'])->format('H:i') ?? '--:--' }}
            </span>
            <span class="text-xs text-gray-500">
                {{ $flight['departure']['iata'] ?? '--' }}
            </span>
            <span class="text-xs text-gray-400">
                {{ $flight['departure']['airport'] ?? '' }}
            </span>
        </div>
        <!-- Duração e Direto -->
        <div class="flex flex-col items-center w-32">
            <span class="text-sm text-gray-500">
                @php
                    if(isset($flight['departure']['scheduled'], $flight['arrival']['scheduled'])) {
                        $dep = \Carbon\Carbon::parse($flight['departure']['scheduled']);
                        $arr = \Carbon\Carbon::parse($flight['arrival']['scheduled']);
                        $diff = $dep->diff($arr);
                        echo $diff->h . 'h ' . $diff->i . 'm';
                    } else {
                        echo '--';
                    }
                @endphp
            </span>
            <i class="fa-solid fa-plane text-gray-400 my-1"></i>
            <span class="text-xs text-gray-500">
                {{ $flight['flight']['codeshared'] ? 'Com escala' : 'Direto' }}
            </span>
        </div>
        <!-- Horário de chegada -->
        <div class="flex flex-col items-center w-32">
            <span class="text-2xl font-bold text-black">
                {{ \Carbon\Carbon::parse($flight['arrival']['scheduled'])->format('H:i') ?? '--:--' }}
            </span>
            <span class="text-xs text-gray-500">
                {{ $flight['arrival']['iata'] ?? '--' }}
            </span>
            <span class="text-xs text-gray-400">
                {{ $flight['arrival']['airport'] ?? '' }}
            </span>
        </div>
        <!-- Preço e Seleção -->
        <div class="flex flex-col items-end flex-1">
            <span class="text-blue-600 text-2xl font-bold">
                @if(isset($flight['price']))
                    R$ {{ number_format($flight['price'], 2, ',', '.') }}
                @else
                    Preço não disponível
                @endif
            </span>
            <span class="text-xs text-gray-500">por pessoa</span>
            <button class="border border-blue-600 text-blue-600 px-4 py-1 rounded hover:bg-green-50 text-sm font-semibold my-2 cursor-pointer">Mais Detalhes</button>
        </div>
    </div>
    <!-- Benefícios centralizados -->
    <div class="flex justify-center space-x-2 mt-6 border-t pt-4 border-gray-200">
        <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
            {{ $flight['baggage']['checked'] ?? null}}
        </span>
        <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
            {{ $flight['baggage']['carry_on'] ?? null }}
        </span>
        <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
            {{ $flight['baggage']['meal'] ?? null }}
        </span>
        {{ $flight['baggage']['wifi'] ?? null }}
        {{ $flight['flight_date'] ?? 'sem data'}}
    </div>
</div>