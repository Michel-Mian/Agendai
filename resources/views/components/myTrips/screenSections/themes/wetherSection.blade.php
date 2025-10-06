<div class="rounded-3xl shadow-2xl border border-blue-200 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 overflow-hidden">
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 px-8 py-8">
        <div class="flex items-center gap-4">
            <div class="bg-gradient-to-tr from-yellow-200 via-blue-100 to-blue-300 rounded-2xl p-4 flex items-center justify-center shadow-lg">
                <i class="fas fa-cloud-sun text-yellow-400 drop-shadow-lg text-4xl md:text-5xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-blue-900 mb-1 tracking-tight">Previsão do Tempo</h2>
                <p class="text-blue-700 text-base md:text-lg font-medium">Veja a previsão detalhada para cada destino</p>
            </div>
        </div>
        @if(isset($viagem->destinos) && $viagem->destinos->count() > 0)
        <div class="w-full md:w-1/3">
            <label for="weather-destination-select" class="block text-blue-800 font-semibold mb-1">Destino</label>
            <select id="weather-destination-select" class="block w-full bg-white border border-blue-300 rounded-xl shadow-sm px-4 py-3 text-blue-900 font-semibold focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                @foreach($viagem->destinos as $destino)
                    <option value="{{ $destino->pk_id_destino }}">{{ $destino->nome_destino }} ({{ \Carbon\Carbon::parse($destino->data_chegada_destino)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($destino->data_partida_destino)->format('d/m/Y') }})</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <div class="px-6 pb-8">
        <div id="weather-skeleton-stats">
            <div class="animate-pulse space-y-4">
                <div class="h-8 bg-blue-200 rounded w-2/3 mx-auto"></div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    @for($i=0;$i<7;$i++)
                        <div class="h-24 bg-blue-100 rounded-2xl"></div>
                    @endfor
                </div>
            </div>
        </div>

        <div id="weather-content-stats" class="hidden"></div>

        <div id="weather-error-stats" class="hidden text-center py-10">
            <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
            <p class="text-red-700 font-bold">O clima será carregado a partir de 7 dias antes da data de início da viagem.</p>
            <p class="text-blue-700 text-base">Por favor, tente novamente mais tarde.</p>
        </div>
    </div>
</div>