<div id="currency-modal" class="fixed inset-0 z-50 flex items-start justify-center pt-20 sm:pt-32 hidden border-t-4 border-gray-200">
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-8 max-w-xs sm:max-w-xl w-full relative mx-2 sm:mx-0"
         style="box-shadow: 0 0 0 4px rgba(30, 41, 59, 0.25), 0 10px 25px 0 rgba(0,0,0,0.15);">
        <button id="close-currency-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors">&times;</button>
        <div class="text-center">
            <div class="text-xl sm:text-2xl font-semibold mb-2">Cotação</div>
            <div class="text-gray-600 mb-4 text-sm sm:text-base">
                Sua moeda preferida: 
                <span class="font-bold">
                    @if(isset($currencies[$user->currency]))
                        {{ $currencies[$user->currency] }} ({{ $user->currency }})
                    @else
                        {{ $user->currency }}
                    @endif
                </span>
            </div>
            <div>
                <input type="number" id="conversion-period" min="7" max="365" class="border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition w-full px-3 py-2 mb-4 text-sm sm:text-base" placeholder="Digite (em dias) o período de conversão" value="{{ old('valorConversao') }}">
            </div>
            @if(!empty($historico) && !is_null($cotacao))
                <div class="overflow-x-auto">
                    <canvas id="graficoMoeda" data-historico="{{ json_encode($historico) }}"></canvas>
                </div>
            @elseif(is_null($cotacao))
                <div class="text-red-600">Moeda não suportada para conversão.</div>
            @endif
        </div>
    </div>
</div>