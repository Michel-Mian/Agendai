<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-4">
        <h2 class="text-xl font-semibold">Suas Viagens</h2>
        <a href="/myTrips" class="bg-white border rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 hover:bg-gray-100 cursor-pointer transition">
            <span>Ver Todas</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    @if(isset($viagens) && count($viagens) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($viagens as $viagem)
                <div class="bg-white rounded-xl shadow flex flex-col h-full">
                    <div class="flex-1 p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div class="font-semibold text-lg">{{ $viagem->destino_viagem }}</div>
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">
                                {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->isFuture() ? 'Próxima' : 'Concluída' }}
                            </span>
                        </div>
                        <div class="text-gray-500 text-sm mt-2">
                            {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }} • 
                            {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->diffInDays(\Carbon\Carbon::parse($viagem->data_final_viagem)) + 1 }} dias
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mt-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
                            {{ $viagem->viajantes->count() }} pessoas
                        </div>
                        <a href="{{ route('viagens', ['id' => $viagem->pk_id_viagem]) }}" class="text-blue-600 font-medium mt-4 inline-flex items-center">Ver Detalhes <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-10">
            <i class="fa-solid fa-plane-slash text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Nenhuma viagem encontrada. Que tal começar a planejar sua próxima aventura?</p>
        </div>
    @endif
</div>