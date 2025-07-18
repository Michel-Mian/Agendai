<div id="viajantes-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur aprimorado -->
    <div id="viajantes-modal-overlay" class="absolute inset-0 bg-gradient-to-br from-gray-900/60 to-gray-800/60 backdrop-blur-md" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="viajantes-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden max-h-[90vh]">
        <!-- Header com gradiente -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Todos os Viajantes</h3>
                        <p class="text-green-100 text-sm">{{ $viajantes->count() }} {{ $viajantes->count() == 1 ? 'viajante cadastrado' : 'viajantes cadastrados' }}</p>
                    </div>
                </div>
                <button id="close-viajantes-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors">
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
                        id="viajante-search-input" 
                        placeholder="Procurar viajante..." 
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <span class="text-xs text-gray-400 bg-gray-200 px-2 py-1 rounded-full" id="search-results-count-viajantes">
                            {{ $viajantes->count() }} resultados
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lista de viajantes com scroll -->
            <div class="flex-1 overflow-y-auto pr-2 space-y-3" style="max-height: calc(90vh - 250px);">
                @if($viajantes->count())
                    @foreach($viajantes as $viajante)
                        <div class="viajante-item group bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-4 border border-green-200 hover:shadow-md transition-all duration-200" data-nome="{{ strtolower($viajante->nome) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                        {{ strtoupper(substr($viajante->nome, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-lg">{{ $viajante->nome }}</h4>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-birthday-cake text-green-500"></i>
                                                <span>{{ $viajante->idade }} anos</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-user text-green-500"></i>
                                                <span>Viajante</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-3 rounded-lg transition-colors flex items-center space-x-2" title="Remover viajante" onclick="return confirm('Tem certeza que deseja remover este viajante?')">
                                        <i class="fas fa-trash text-sm"></i>
                                        <span class="text-xs font-medium">Remover</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-green-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum viajante encontrado</h3>
                        <p class="text-gray-500">Adicione viajantes para organizar melhor sua viagem</p>
                    </div>
                @endif
                
                <!-- Mensagem quando não há resultados na busca -->
                <div id="no-results-message-viajantes" class="hidden text-center py-12">
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
                    <button id="add-viajante-from-list" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Adicionar viajante</span>
                    </button>
                    <button id="close-viajantes-modal-footer-btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Modal Viajantes ---
    document.addEventListener('DOMContentLoaded', function () {
        const openViajantesModalBtn = document.getElementById('open-viajantes-modal-btn');
        const closeViajantesModalBtn = document.getElementById('close-viajantes-modal-btn');
        const closeViajantesModalFooterBtn = document.getElementById('close-viajantes-modal-footer-btn');
        const addViajanteFromListBtn = document.getElementById('add-viajante-from-list');
        const viajantesModal = document.getElementById('viajantes-modal');
        const viajantesModalPanel = document.getElementById('viajantes-modal-panel');
        const viajantesModalOverlay = document.getElementById('viajantes-modal-overlay');
        const viajanteSearchInput = document.getElementById('viajante-search-input');
        const viajanteItems = document.querySelectorAll('.viajante-item');
        const searchResultsCountViajantes = document.getElementById('search-results-count-viajantes');
        const noResultsMessageViajantes = document.getElementById('no-results-message-viajantes');

        const openViajantesModal = () => {
            viajantesModal.classList.remove('hidden');
            viajantesModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                viajantesModalPanel.classList.remove('scale-95', 'opacity-0');
                viajantesModalPanel.classList.add('scale-100', 'opacity-100');
                if (viajanteSearchInput) {
                    viajanteSearchInput.value = '';
                    viajanteSearchInput.dispatchEvent(new Event('input'));
                    viajanteSearchInput.focus();
                }
            }, 10);
        };

        const closeViajantesModal = () => {
            if (!viajantesModalPanel) return;
            viajantesModalPanel.classList.remove('scale-100', 'opacity-100');
            viajantesModalPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                viajantesModal.classList.add('hidden');
                viajantesModal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        };

        if (openViajantesModalBtn) openViajantesModalBtn.addEventListener('click', openViajantesModal);
        if (closeViajantesModalBtn) closeViajantesModalBtn.addEventListener('click', closeViajantesModal);
        if (closeViajantesModalFooterBtn) closeViajantesModalFooterBtn.addEventListener('click', closeViajantesModal);
        if (viajantesModalOverlay) viajantesModalOverlay.addEventListener('click', closeViajantesModal);

        // Botão para adicionar viajante
        if (addViajanteFromListBtn) {
            addViajanteFromListBtn.addEventListener('click', function() {
                closeViajantesModal();
                setTimeout(() => {
                    const addBtn = document.getElementById('open-add-viajante-modal-btn');
                    if (addBtn) addBtn.click();
                }, 300);
            });
        }

        // Funcionalidade de busca aprimorada
        if (viajanteSearchInput) {
            viajanteSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                viajanteItems.forEach(function(item) {
                    const nomeViajante = item.dataset.nome;
                    const isVisible = nomeViajante.includes(searchTerm);
                    item.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) visibleCount++;
                });
                
                // Atualizar contador de resultados
                if (searchResultsCountViajantes) {
                    searchResultsCountViajantes.textContent = `${visibleCount} resultado${visibleCount !== 1 ? 's' : ''}`;
                }
                
                // Mostrar/ocultar mensagem de "nenhum resultado"
                if (noResultsMessageViajantes) {
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResultsMessageViajantes.classList.remove('hidden');
                    } else {
                        noResultsMessageViajantes.classList.add('hidden');
                    }
                }
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && viajantesModal && !viajantesModal.classList.contains('hidden')) {
                closeViajantesModal();
            }
        });
    });
</script>
