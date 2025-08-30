<!-- Painel lateral de pontos de interesse -->
<!-- Adicionado z-index alto e removido overflow-hidden problemático -->
<div id="points-panel" class="w-80 bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out h-[50rem] z-50 flex-shrink-0">
    <!-- Header do painel -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white p-4 flex items-center justify-between relative z-10">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 rounded-lg p-2">
                <i class="fas fa-map-marker-alt text-blue-900 text-lg"></i>
            </div>
            <div id="panel-header-text">
                <h2 class="text-lg font-bold text-blue-900">Pontos de Interesse</h2>
                <p class="text-blue-700 text-sm">{{ count($viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get()) }} {{ count($viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get()) == 1 ? 'Ponto de interesse' : 'Pontos de interesse' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Botão de editar pontos -->
    <div class="p-4 border-b border-gray-200">
        <a href="{{ route('explore.setTrip', $viagem->pk_id_viagem) }}" class="w-full bg-blue-900 hover:bg-blue-800 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
            <i class="fas fa-edit"></i>
            <span>Editar Pontos de Interesse</span>
        </a>
    </div>
    <div class="p-4 border-b border-gray-200">
    <input type="date" id="datepicker"
           class="border border-gray-300 rounded-lg py-2 px-4 w-full"
           placeholder="Selecionar Data"
           min="{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('Y-m-d') }}"
           max="{{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('Y-m-d') }}"
           value="{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('Y-m-d') }}">
    </div>
    
    <!-- Conteúdo do painel -->
    <div id="points-content" class="flex-1 overflow-y-auto p-4">
        @php
            $pontosOrdenados = $viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get();
            $dataInicial = \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('Y-m-d');
        @endphp
        
        <div id="pontos-container">
            <!-- Os pontos serão carregados via JavaScript baseado na data selecionada -->
        </div>
        
        <!-- Fallback se não houver pontos -->
        <div id="no-points-message" class="text-center py-8" style="display: none;">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-map-marker-alt text-blue-900 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum ponto</h3>
            <p class="text-gray-500 text-sm">Adicione locais interessantes para visitar nesta data</p>
        </div>
    </div>
</div>

@php
    $pontosData = $pontosOrdenados->map(function($ponto, $index) {
        return [
            'id' => $ponto->id,
            'nome' => $ponto->nome_ponto_interesse,
            'desc' => $ponto->desc_ponto_interesse,
            'data' => \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('Y-m-d'),
            'hora' => $ponto->hora_ponto_interesse ? \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') : null,
            'latitude' => $ponto->latitude,
            'longitude' => $ponto->longitude,
            'index' => $index
        ];
    });
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    const datepicker = document.getElementById('datepicker');
    const pontosContainer = document.getElementById('pontos-container');
    const noPointsMessage = document.getElementById('no-points-message');
    
    // Dados dos pontos (convertido do PHP para JavaScript)
    const pontosData = @json($pontosData);
    
    function renderPontos(selectedDate) {
        const pontosFiltrados = pontosData.filter(ponto => ponto.data === selectedDate);
        
        if (pontosFiltrados.length === 0) {
            pontosContainer.innerHTML = '';
            noPointsMessage.style.display = 'block';
            return;
        }
        
        noPointsMessage.style.display = 'none';
        
        let html = '<div class="space-y-3">';
        pontosFiltrados.forEach((ponto, index) => {
            html += `
                <div class="group bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="focusOnPoint(${ponto.index}, ${ponto.latitude}, ${ponto.longitude})">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            ${index + 1}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">${ponto.nome}</h3>
                            
                            <div class="flex items-center space-x-2 text-xs text-gray-600 mt-1">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-blue-900"></i>
                                    <span>${new Date(ponto.data).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })}</span>
                                </div>
                                ${ponto.hora ? `
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-clock text-blue-900"></i>
                                        <span>${ponto.hora}</span>
                                    </div>
                                ` : ''}
                            </div>
                            
                            ${ponto.desc ? `<p class="text-gray-600 text-xs mt-1 line-clamp-2">${ponto.desc}</p>` : ''}
                        </div>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-eye text-blue-900 text-sm"></i>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        pontosContainer.innerHTML = html;
    }
    
    // Renderizar pontos da data inicial
    renderPontos(datepicker.value);
    
    // Escutar mudanças no datepicker
    datepicker.addEventListener('change', function() {
        renderPontos(this.value);
    });
});
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
