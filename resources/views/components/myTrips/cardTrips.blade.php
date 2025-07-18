<div class="bg-white rounded-lg shadow-md mb-6 mb-10 flex items-center justify-between p-6">
    <div class="flex-1">
        <h2 class="text-2xl font-bold mr-3">{{ $viagem->destino_viagem }}</h2>
        <div class="flex items-center text-gray-500 text-sm mt-2">
            <i class="fa-regular fa-calendar mr-2" style="color:rgb(65, 160, 131);"></i>
            {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}
        </div>
        <p class="mt-4 text-gray-700">
            Origem: {{ $viagem->origem_viagem }}<br>
            Orçamento: R$ {{ number_format($viagem->orcamento_viagem, 2, ',', '.') }}
        </p>
        <div class="flex space-x-4 mt-4">
            @php
                $dias = \Carbon\Carbon::parse($viagem->data_inicio_viagem)->diffInDays(\Carbon\Carbon::parse($viagem->data_final_viagem)) + 1;
                $qtdViajantes = isset($viagem->viajantes) ? $viagem->viajantes->count() : 0;
            @endphp
            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }}</span>
            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">{{ $qtdViajantes }} {{ $qtdViajantes == 1 ? 'viajante' : 'viajantes' }}</span>
        </div>
    </div>
    <div class="flex items-center">
        <a href="{{ route('viagens', ['id' => $viagem->pk_id_viagem]) }}" class="text-blue-600 font-semibold flex items-center hover:underline ver-detalhes-btn mr-3">
            Ver Detalhes <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
        </a>
    </div>
</div>