@php
    // Fallback caso alguma variável não seja passada
    $proximaViagem = $proximaViagem ?? null;
    $totalViagens = $totalViagens ?? 0;
    $user = $user ?? null;
    $currencies = $currencies ?? [];
    $cotacao = $cotacao ?? null;
    
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 md:gap-12 mb-10 md:mb-16">

    {{-- Viagens Planejadas --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Viagens Planejadas</div>
            @if($totalViagens > 0)
                <div class="text-2xl md:text-3xl font-bold mt-4 md:mt-6">
                    {{ $totalViagens }} até agora!
                </div>
            @else
                <div class="text-red-600 mt-4 md:mt-6">Nenhuma viagem cadastrada</div>
            @endif
        </div>
        <div class="self-end mt-4">
            <span class="bg-green-100 p-2 rounded-lg">
                <i class="fa-solid fa-calendar" style="color:rgb(85, 201, 166);"></i>
            </span>
        </div>
    </div>

    {{-- Moeda Preferida --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Sua moeda preferida</div>
            <div class="text-2xl md:text-3xl font-bold mt-2">
                @if($user && isset($currencies[$user->currency]))
                    {{ $currencies[$user->currency] }} ({{ $user->currency }})
                @elseif($user)
                    {{ $user->currency }}
                @else
                    --
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
                <i class="fa-solid fa-file-invoice-dollar cursor-pointer hover:scale-130 transition-transform duration-200"
                   style="color: #74C0FC;" id="modal-currency"></i>
            </span>
        </div>
    </div>

    {{-- Próxima Viagem --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col justify-between h-full">
        <div>
            <div class="text-gray-500 text-sm">Próxima Viagem</div>
            @if($proximaViagem)
                <div class="text-lg md:text-xl font-bold mt-2">
                    {{ $proximaViagem->destino_viagem ?? 'Sem destino' }}
                </div>
                <div class="text-purple-600 text-xs mt-1">
                    Em {{ \Carbon\Carbon::parse($proximaViagem->data_inicio_viagem)->diffInDays(now()) }} dias
                </div>
            @else
                <div class="text-gray-500 text-sm mt-2">Nenhuma viagem cadastrada</div>
            @endif
        </div>
        <div class="self-end mt-4">
            <i class="fa-solid fa-umbrella-beach cursor-pointer" style="color: #74C0FC;"></i>
        </div>
    </div>
</div>
