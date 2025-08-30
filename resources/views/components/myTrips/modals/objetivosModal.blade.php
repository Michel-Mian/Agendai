<div id="objetivos-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur aprimorado -->
    <div id="objetivos-modal-overlay" class="absolute inset-0 bg-gradient-to-br from-gray-900/60 to-gray-800/60 backdrop-blur-md" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="objetivos-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden max-h-[90vh]">
        <!-- Header com gradiente -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-list-ul text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-purple-800">Todos os Objetivos</h3>
                        <p class="text-purple-400 text-sm">{{ $objetivos->count() }} {{ $objetivos->count() == 1 ? 'objetivo cadastrado' : 'objetivos cadastrados' }}</p>
                    </div>
                </div>
                <button id="close-objetivos-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Corpo do modal -->
        <div class="p-6 flex flex-col max-h-[calc(90vh-120px)]">
            <!-- Barra de busca aprimorada -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="objetivo-search-input" 
                        placeholder="Procurar objetivo..." 
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-200 bg-gray-50 focus:bg-white"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <span class="text-xs text-gray-400 bg-gray-200 px-2 py-1 rounded-full" id="search-results-count">
                            {{ $objetivos->count() }} resultados
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lista de objetivos com scroll -->
            <div class="flex-1 overflow-y-auto pr-2 space-y-3" style="max-height: calc(90vh - 250px);">
                @if($objetivos->count())
                    @foreach($objetivos as $index => $objetivo)
                        <div class="objetivo-item group bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200 hover:shadow-md transition-all duration-200" data-nome="{{ strtolower($objetivo->nome) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $objetivo->nome }}</h4>
                                        <p class="text-sm text-gray-600">Objetivo da viagem</p>
                                    </div>
                                </div>
                                <form action="{{ route('objetivos.destroy', ['id' => $objetivo->pk_id_objetivo]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-3 rounded-lg transition-colors flex items-center space-x-2" title="Remover objetivo" onclick="return confirm('Tem certeza que deseja remover este objetivo?')">
                                        <i class="fas fa-trash text-sm"></i>
                                        <span class="text-xs font-medium">Remover</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bullseye text-purple-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum objetivo encontrado</h3>
                        <p class="text-gray-500">Adicione objetivos para organizar melhor sua viagem</p>
                    </div>
                @endif
                
                <!-- Mensagem quando não há resultados na busca -->
                <div id="no-results-message" class="hidden text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum resultado encontrado</h3>
                    <p class="text-gray-500">Tente usar outros termos de busca</p>
                </div>
            </div>
            
            <!-- Footer com ações -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <button id="add-objetivo-from-list" class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Adicionar objetivo</span>
                    </button>
                    <button id="close-objetivos-modal-footer-btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Modal Objetivos ---
    document.addEventListener('DOMContentLoaded', function () {
        // ID da viagem atual
        const currentTripId = window.currentTripId || {{ $viagem->pk_id_viagem ?? 'null' }};
        
        const openObjetivosModalBtn = document.getElementById('open-objetivos-modal-btn');
        const closeObjetivosModalBtn = document.getElementById('close-objetivos-modal-btn');
        const closeObjetivosModalFooterBtn = document.getElementById('close-objetivos-modal-footer-btn');
        const addObjetivoFromListBtn = document.getElementById('add-objetivo-from-list');
        const objetivosModal = document.getElementById('objetivos-modal');
        const objetivosModalPanel = document.getElementById('objetivos-modal-panel');
        const objetivosModalOverlay = document.getElementById('objetivos-modal-overlay');
        const objetivoSearchInput = document.getElementById('objetivo-search-input');
        const objetivoItems = document.querySelectorAll('.objetivo-item');
        const searchResultsCount = document.getElementById('search-results-count');
        const noResultsMessage = document.getElementById('no-results-message');

        const openObjetivosModal = () => {
            objetivosModal.classList.remove('hidden');
            objetivosModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                objetivosModalPanel.classList.remove('scale-95', 'opacity-0');
                objetivosModalPanel.classList.add('scale-100', 'opacity-100');
                if (objetivoSearchInput) {
                    objetivoSearchInput.value = '';
                    objetivoSearchInput.dispatchEvent(new Event('input'));
                    objetivoSearchInput.focus();
                }
            }, 10);
        };

        const closeObjetivosModal = () => {
            if (!objetivosModalPanel) return;
            objetivosModalPanel.classList.remove('scale-100', 'opacity-100');
            objetivosModalPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                objetivosModal.classList.add('hidden');
                objetivosModal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        };

        if (openObjetivosModalBtn) openObjetivosModalBtn.addEventListener('click', openObjetivosModal);
        if (closeObjetivosModalBtn) closeObjetivosModalBtn.addEventListener('click', closeObjetivosModal);
        if (closeObjetivosModalFooterBtn) closeObjetivosModalFooterBtn.addEventListener('click', closeObjetivosModal);
        if (objetivosModalOverlay) objetivosModalOverlay.addEventListener('click', closeObjetivosModal);

        // Botão para adicionar objetivo
        if (addObjetivoFromListBtn) {
            addObjetivoFromListBtn.addEventListener('click', function() {
                closeObjetivosModal();
                setTimeout(() => {
                    const addBtn = document.getElementById('open-add-objetivo-modal-btn');
                    if (addBtn) addBtn.click();
                }, 300);
            });
        }

        // Funcionalidade de busca aprimorada
        if (objetivoSearchInput) {
            objetivoSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                objetivoItems.forEach(function(item) {
                    const nomeObjetivo = item.dataset.nome;
                    const isVisible = nomeObjetivo.includes(searchTerm);
                    item.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) visibleCount++;
                });
                
                // Atualizar contador de resultados
                if (searchResultsCount) {
                    searchResultsCount.textContent = `${visibleCount} resultado${visibleCount !== 1 ? 's' : ''}`;
                }
                
                // Mostrar/ocultar mensagem de "nenhum resultado"
                if (noResultsMessage) {
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }
                }
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

        // Adicionar event listeners para os objetivos no modal
        document.querySelectorAll('.objetivo-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Evitar que o clique no botão de remover dispare esta função
                if (e.target.closest('form')) return;
                
                const objetivoNome = this.querySelector('h4').textContent.trim();
                const filters = getGooglePlacesFilters(objetivoNome);
                
                // Codificar os filtros para passar na URL
                const encodedFilters = encodeURIComponent(JSON.stringify(filters));
                
                console.log('Redirecionando do modal com filtros:', {
                    objetivo: objetivoNome,
                    filters: filters,
                    tripId: currentTripId
                });
                
                // Redirecionar para a página explore com os filtros E definindo a viagem correta
                window.location.href = `/explore/set-trip/${currentTripId}?filters=${encodedFilters}&objective=${encodeURIComponent(objetivoNome)}`;
            });
            
            // Adicionar cursor pointer para indicar que é clicável
            item.style.cursor = 'pointer';
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && objetivosModal && !objetivosModal.classList.contains('hidden')) {
                closeObjetivosModal();
            }
        });
    });
</script>
