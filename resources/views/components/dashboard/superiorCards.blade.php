<div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 md:gap-12 mb-10 md:mb-16">
    {{-- CARD 1 — Viagens Planejadas (conta usando a collection) --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Viagens Planejadas</div>

            @php
                $totalViagens = isset($viagens) ? $viagens->count() : 0;
            @endphp

            <div class="text-2xl md:text-3xl font-bold mt-4 md:mt-6">
                @if($totalViagens > 0)
                    {{ $totalViagens }} até agora!
                @else
                    Nenhuma viagem cadastrada
                @endif
            </div>
        </div>
        <div class="self-end mt-4">
            <span class="bg-green-100 p-2 rounded-lg">
                <i class="fa-solid fa-calendar" style="color:rgb(85, 201, 166);"></i>
            </span>
        </div>
    </div>

    {{-- CARD 2 — Sua moeda preferida (NÃO ALTERADO) --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Sua moeda preferida</div>
            <div class="text-2xl md:text-3xl font-bold mt-2">
                @if(isset($currencies[$user->currency]))
                    {{ $currencies[$user->currency] }} ({{ $user->currency }})
                @else
                    {{ $user->currency }}
                @endif
            </div>
            @if(is_null($cotacao))
                <div class="text-red-600">Moeda não suportada para conversão.</div>
            @else
                <div class="text-gray-500 text-sm mt-2">
                    US$ {{ number_format($cotacao, 2, ',', '.') }} em relação ao dólar americano
                </div>
            @endif
        </div>
        <div class="self-end mt-4">
            <span class="bg-blue-100 p-2 rounded-lg">
                <i class="fa-solid fa-file-invoice-dollar cursor-pointer hover:scale-130 transition-transform duration-200" style="color: #74C0FC;" id="modal-currency"></i>
            </span>
        </div>
    </div>

    {{-- CARD 3 — Próxima Viagem (descobre com foreach) --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Próxima Viagem</div>

            @php
                use Carbon\Carbon;
                $proximaViagem = null;
                $hoje = Carbon::today();

                if(isset($viagens)) {
                    foreach ($viagens as $v) {
                        // ajuste os nomes das colunas conforme seu banco:
                        $dataInicio = Carbon::parse($v->data_inicio_viagem);
                        if ($dataInicio->gte($hoje)) {
                            $proximaViagem = $v;
                            break; // achou a primeira futura, para o loop
                        }
                    }
                }

                $diasRestantes = $proximaViagem
                    ? Carbon::today()->diffInDays(Carbon::parse($proximaViagem->data_inicio_viagem))
                    : null;
            @endphp

            @if($proximaViagem)
                <div class="text-lg md:text-xl font-bold mt-2">
                    {{ $proximaViagem->nome_viagem }}
                </div>
                @if(!is_null($diasRestantes) && $diasRestantes >= 0)
                    <div class="text-purple-600 text-xs mt-1">Em {{ $diasRestantes }} dias</div>
                @endif
            @else
                <div class="text-lg md:text-xl font-bold mt-2">Nenhuma viagem cadastrada</div>
            @endif
        </div>
        <div class="self-end mt-4">
            <i class="fa-solid fa-umbrella-beach cursor-pointer" style="color: #74C0FC;"></i>
        </div>
    </div>
</div>
