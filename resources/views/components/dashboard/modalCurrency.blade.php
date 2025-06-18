<div id="currency-modal" class="fixed inset-0 z-0 flex items-start justify-center pt-35 bg-opacity-10 backdrop-blur-sm bg-opacity-30 hidden border-t-4 border-gray-200">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-xl w-full relative">
        <button id="close-currency-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-200 text-2xl font-bold">&times;</button>
        <div class="text-center">
            <div class="text-2xl font-semibold mb-2">Cotação</div>
            <div class="text-gray-600 mb-4">
                Sua moeda preferida: 
                <span class="font-bold">
                    @if(isset($currencies[$user->currency]))
                        {{ $currencies[$user->currency] }} ({{ $user->currency }})
                    @else
                        {{ $user->currency }}
                    @endif
                </span>
            </div>
            <div class="">
                <input type="number" id="conversion-period" min="7" max="365" class="border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition w-full px-4 py-2 mb-4" placeholder="Digite (em dias) o período de conversão" value="{{ old('valorConversao') }}">
            </div>
            @if(!empty($historico) && !is_null($cotacao))
                <canvas id="graficoMoeda" data-historico="{{ json_encode($historico) }}"></canvas>
            @elseif(is_null($cotacao))
                <div class="text-red-600">Moeda não suportada para conversão.</div>
            @endif
        </div>
    </div>
</div>