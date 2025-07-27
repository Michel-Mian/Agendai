<div class="trip-card bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group mb-6" style="--index: {{ $loop->index ?? 0 }}">
    <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-800"></div>
    
    <div class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></div>
                    <h2 class="text-2xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                        {{ $viagem->destino_viagem }}
                    </h2>
                </div>
                
                <div class="flex items-center text-gray-500 text-sm mb-3">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <i class="fa-regular fa-calendar text-green-600"></i>
                    </div>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
            
            @php
                $hoje = \Carbon\Carbon::now();
                $dataInicio = \Carbon\Carbon::parse($viagem->data_inicio_viagem);
                $dataFim = \Carbon\Carbon::parse($viagem->data_final_viagem);
                
                if ($hoje->lt($dataInicio)) {
                    $status = ['text' => 'Planejada', 'color' => 'blue'];
                } elseif ($hoje->between($dataInicio, $dataFim)) {
                    $status = ['text' => 'Em andamento', 'color' => 'green'];
                } else {
                    $status = ['text' => 'Concluída', 'color' => 'gray'];
                }
            @endphp
            <span class="px-3 py-1 bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-700 text-xs font-medium rounded-full">
                {{ $status['text'] }}
            </span>
        </div>
        
        <div class="space-y-3 mb-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Origem:</span>
                    <span class="ml-2 font-medium text-gray-800">{{ $viagem->origem_viagem }}</span>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Orçamento:</span>
                    <span class="ml-2 font-bold text-green-600">R$ {{ number_format($viagem->orcamento_viagem, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3 mb-6">
            @php
                $dias = \Carbon\Carbon::parse($viagem->data_inicio_viagem)->diffInDays(\Carbon\Carbon::parse($viagem->data_final_viagem)) + 1;
                $qtdViajantes = isset($viagem->viajantes) ? $viagem->viajantes->count() : 0;
            @endphp
            
            <div class="badge-days flex items-center px-3 py-2 rounded-lg">
                <i class="fas fa-calendar-days text-gray-600 mr-2 text-sm"></i>
                <span class="text-gray-700 font-medium text-sm">
                    {{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }}
                </span>
            </div>
            
            <div class="badge-travelers flex items-center px-3 py-2 rounded-lg">
                <i class="fas fa-users text-blue-600 mr-2 text-sm"></i>
                <span class="text-blue-700 font-medium text-sm">
                    {{ $qtdViajantes }} {{ $qtdViajantes == 1 ? 'viajante' : 'viajantes' }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 text-gray-400">
                <i class="fas fa-clock text-xs"></i>
                <span class="text-xs">
                    Atualizada em {{ \Carbon\Carbon::parse($viagem->updated_at)->format('d/m/Y') }}
                </span>
            </div>
            
            <a href="{{ route('viagens', ['id' => $viagem->pk_id_viagem]) }}" 
               class="group flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                <span>Ver Detalhes</span>
                <i class="fa-solid fa-arrow-right ml-2 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
    
</div>