<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-bullseye text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-purple-800">Objetivos</h2>
                    <p class="text-purple-200 text-sm">{{ $objetivos->count() }} {{ $objetivos->count() == 1 ? 'objetivo' : 'objetivos' }}</p>
                </div>
            </div>
            <button type="button" id="open-add-objetivo-modal-btn" class="bg-white border-2 border-purple-400 hover:bg-purple-100 cursor-pointer text-purple-700 p-2 rounded-lg transition-colors" title="Adicionar objetivo">
                <i class="fas fa-plus text-lg text-purple-400"></i> Adicionar Objetivos
            </button>
        </div>
    </div>

    <div class="p-6">
        @if($objetivos->count())
            <div class="space-y-4">
                @php
                    $objetivosExibidos = ($objetivos->count() > 3) ? $objetivos->take(3) : $objetivos;
                @endphp
                @include('components/myTrips/cardObjectives', ['showClickableHint' => true])
            </div>
            
            @if($objetivos->count() > 3)
                <div class="mt-4">
                    <button id="open-objetivos-modal-btn" class="w-full bg-gradient-to-r from-purple-100 to-purple-200 hover:from-purple-200 hover:to-purple-300 text-purple-700 font-medium py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-eye"></i>
                        <span>Ver todos os objetivos ({{ $objetivos->count() }})</span>
                    </button>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bullseye text-purple-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 mb-4">Nenhum objetivo cadastrado</p>
                <button type="button" id="open-add-objetivo-modal-btn-empty" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Adicionar primeiro objetivo
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ID da viagem atual
        const currentTripId = {{ $viagem->pk_id_viagem ?? 'null' }};
        console.log('🆔 Trip ID atual:', currentTripId);
        
        const addObjetivoEmptyBtn = document.getElementById('open-add-objetivo-modal-btn-empty');
        if (addObjetivoEmptyBtn) {
            addObjetivoEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-objetivo-modal-btn').click();
            });
        }

        // Função para mapear objetivos para filtros do Google Places API
        function getGooglePlacesFilters(objetivoNome) {
            const objetivoFilters = {
                'Cultura e história': ['museum', 'tourist_attraction', 'library', 'church', 'historical'],
                'Gastronomia': ['restaurant', 'cafe', 'bar', 'food', 'meal_takeaway'],
                'Aventura': ['amusement_park', 'park', 'zoo', 'aquarium', 'bowling_alley'],
                'Negócios': ['business', 'conference_center', 'embassy'],
                'Relaxamento': ['spa', 'park', 'beach', 'resort'],
                'Compras': ['shopping_mall', 'store', 'clothing_store', 'electronics_store', 'jewelry_store'],
                'Vida noturna': ['night_club', 'bar', 'casino'],
                'Arte e museus': ['museum', 'art_gallery', 'library'],
                'Esportes': ['gym', 'stadium', 'sports_complex'],
                'Natureza': ['park', 'zoo', 'aquarium', 'natural_feature'],
                'Educação': ['university', 'school', 'library'],
                'Entretenimento': ['movie_theater', 'amusement_park', 'casino', 'bowling_alley'],
                'Religião': ['church', 'mosque', 'synagogue', 'hindu_temple']
            };
            
            return objetivoFilters[objetivoNome] || ['tourist_attraction'];
        }

        // Adicionar event listeners para os cards de objetivos
        document.querySelectorAll('.objetivo-card').forEach(card => {
            card.addEventListener('click', function() {
                const objetivoNome = this.dataset.objetivoNome;
                const filters = getGooglePlacesFilters(objetivoNome);
                
                // Codificar os filtros para passar na URL
                const encodedFilters = encodeURIComponent(JSON.stringify(filters));
                
                console.log('🎯 Clique no objetivo detectado:', {
                    objetivo: objetivoNome,
                    filters: filters,
                    encodedFilters: encodedFilters,
                    tripId: currentTripId
                });
                
                // Redirecionar para a página explore com os filtros E definindo a viagem correta
                const url = `/explore/set-trip/${currentTripId}?filters=${encodedFilters}&objective=${encodeURIComponent(objetivoNome)}`;
                console.log('🚀 Redirecionando para:', url);
                
                window.location.href = url;
            });
        });
    });
</script>