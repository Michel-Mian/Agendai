<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-cloud-sun text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-blue-700">Previsão do Tempo</h3>
                    <p class="text-blue-400 text-sm">{{ $viagem->destino_viagem }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-blue-700 text-sm">Período da viagem</div>
                <div class="text-blue-400 font-medium">{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m') }}</div>
            </div>
        </div>
    </div>
    <div class="p-6">
        @php
            $dataPrevisao = isset($clima['daily']['time'][0]) ? \Carbon\Carbon::parse($clima['daily']['time'][0]) : null;
            $dataInicioViagem = \Carbon\Carbon::parse($viagem->data_inicio_viagem);
            $diasDiferenca = $dataPrevisao ? $dataPrevisao->diffInDays($dataInicioViagem, false) : null;
        @endphp

        @if(is_null($dataPrevisao) || $diasDiferenca > 7)
            <div class="text-orange-600 font-semibold flex items-center justify-center">
                A previsão do tempo só estará disponível até 7 dias antes do início da viagem. Volte mais próximo da data!
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @for ($i = 0; $i < count($clima['daily']['time']); $i++)
                    <div class="relative bg-gradient-to-br from-blue-100 via-blue-50 to-white rounded-2xl shadow-xl border border-blue-200 p-6 flex flex-col justify-between min-h-[220px] hover:scale-[1.02] transition-transform duration-200">
                        <span class="absolute top-3 right-4 bg-blue-500 text-white text-xs px-3 py-1 rounded-full shadow font-semibold z-10">
                            Dia {{ $i+1 }}
                        </span>
                        <div class="flex items-center space-x-5 mb-4">
                            <div class="bg-blue-500/30 rounded-full p-4 flex items-center justify-center shadow-md">
                                @php
                                    $maxProb = $clima['daily']['precipitation_probability_max'][$i] ?? 0;
                                    $icon = $maxProb <= 30 ? 'fa-sun' : ($maxProb >= 60 ? 'fa-cloud-rain' : 'fa-cloud-sun');
                                    $iconColor = $maxProb <= 30 ? 'text-yellow-400' : ($maxProb >= 60 ? 'text-blue-400' : 'text-orange-700');
                                @endphp
                                <i class="fas {{ $icon }} {{ $iconColor }} text-4xl"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold text-blue-700 flex items-center">
                                    {{ $clima['daily']['temperature_2m_max'][$i]  ?? '-'}}°C
                                    <span class="ml-2 text-base font-normal text-blue-400">máx</span>
                                </div>
                                <div class="text-lg text-blue-500">
                                    Mín: <span class="font-semibold">{{ $clima['daily']['temperature_2m_min'][$i] ?? '-' }}°C</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($clima['daily']['time'][$i])->translatedFormat('l, d/m/Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2 mt-2">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-tint text-blue-400"></i>
                                <span class="text-blue-700 text-sm">Precipitação:</span>
                                <span class="font-semibold text-blue-900">{{ $clima['daily']['precipitation_sum'][$i] ?? '-' }} mm</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-wind text-blue-400"></i>
                                <span class="text-blue-700 text-sm">Vento máx:</span>
                                <span class="font-semibold text-blue-900">{{ $clima['daily']['wind_speed_10m_max'][$i] ?? '-' }} km/h</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-cloud-showers-heavy text-blue-400"></i>
                                <span class="text-blue-700 text-sm">Prob. chuva:</span>
                                <span class="font-semibold text-blue-900">{{ $clima['daily']['precipitation_probability_max'][$i] ?? '-' }}%</span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</div>