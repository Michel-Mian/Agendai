<div id="trip-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-all duration-300 hidden">
    <div class="w-full max-w-lg mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden animate-fadeIn">
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 px-8 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white mb-1">Selecione uma viagem</h1>
                <p class="text-blue-100 text-sm">Escolha para qual viagem deseja salvar este voo da {{ $flight['flights'][0]['airline'] ?? 'Companhia desconhecida' }}</p>
            </div>
            <button onclick="document.getElementById('trip-modal').classList.add('hidden')" class="text-white text-2xl hover:text-blue-200 transition-colors">&times;</button>
        </div>
        <div class="p-6">
                <ul class="space-y-4">
                    @forelse($viagens as $viagem)
                        @if ($viagem->data_final_viagem > $flight['flights'][0]['departure_airport']['time'])
                                <li class="flex items-center justify-between bg-blue-50 hover:bg-blue-100 rounded-xl p-4 shadow cursor-pointer transition-all duration-150 group">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-map-marker-alt text-blue-500 text-lg"></i>
                                            <span class="text-lg font-bold text-blue-700">{{ $viagem->destino_viagem }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-blue-700 text-sm">Saindo de {{ $viagem->origem_viagem ?? 'sem origem'}}</span>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1 flex items-center space-x-2">
                                            <i class="fas fa-calendar-alt text-blue-400"></i>
                                            <span>{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn-selecionar-viagem bg-blue-600 group-hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition-all duration-150 cursor-pointer"
                                        data-voo='@json($flight)'
                                        data-trip-id="{{ $viagem->pk_id_viagem }}"
                                        data-orcamento="{{ $viagem->orcamento_viagem }}"
                                        data-data-inicio-viagem="{{ $viagem->data_inicio_viagem }}"
                                    >
                                        Selecionar
                                    </button>
                                </li>
                        @endif
                    @empty
                        <li class="text-center text-gray-400 py-8">
                            <i class="fas fa-suitcase-rolling text-3xl mb-2"></i>
                            <div>Nenhuma viagem encontrada.</div>
                        </li>
                    @endforelse
                </ul>
        </div>
    </div>
</div>
<form id="form-envio-direto" action="{{ route('flights.saveFlights') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="flight_data" id="flight_data_envio_direto">
    <input type="hidden" name="viagem_id" id="viagem_id_envio_direto">
</form>