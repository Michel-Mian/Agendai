<div id="add-objetivo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur aprimorado -->
    <div id="add-objetivo-modal-overlay" class="absolute inset-0 bg-gradient-to-br from-gray-900/60 to-gray-800/60 backdrop-blur-md" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="add-objetivo-modal-panel" class="relative w-full max-w-md transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <!-- Header com gradiente -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-purple-800">Adicionar Objetivo</h3>
                        <p class="text-purple-600 text-sm">Defina um novo objetivo para sua viagem</p>
                    </div>
                </div>
                <button id="close-add-objetivo-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Corpo do modal -->
        <div class="p-6">
            <!-- Mensagens de erro -->
            @if ($errors->has('nome_objetivo'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span class="font-medium text-red-800">Erro na validação</span>
                    </div>
                    <div class="text-red-600 text-sm">{{ $errors->first('nome_objetivo') }}</div>
                </div>
            @endif
            
            <form id="add-objetivo-form" method="POST" action="{{ route('objetivos.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
                
                <!-- Campo Nome do Objetivo -->
                <div class="space-y-2">
                    <label for="nome_objetivo" class="flex items-center space-x-2 text-sm font-semibold text-gray-700">
                        <i class="fas fa-target text-purple-500"></i>
                        <span>Nome do objetivo</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="nome_objetivo" 
                            name="nome_objetivo" 
                            maxlength="100" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-200 bg-gray-50 focus:bg-white" 
                            placeholder="Ex: Visitar museus locais, Experimentar culinária típica..." 
                            required
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full border">
                                <span id="nome_objetivo_count">0</span>/100
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center space-x-1">
                        <i class="fas fa-lightbulb"></i>
                        <span>Seja específico sobre o que deseja alcançar</span>
                    </p>
                </div>
                
                <!-- Sugestões de objetivos -->
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-magic text-purple-500"></i>
                        <span class="text-sm font-semibold text-purple-800">Sugestões populares</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="suggestion-btn bg-white hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs border border-purple-200 transition-colors" data-suggestion="Conhecer pontos turísticos">
                            Conhecer pontos turísticos
                        </button>
                        <button type="button" class="suggestion-btn bg-white hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs border border-purple-200 transition-colors" data-suggestion="Experimentar comida local">
                            Experimentar comida local
                        </button>
                        <button type="button" class="suggestion-btn bg-white hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs border border-purple-200 transition-colors" data-suggestion="Fazer compras">
                            Fazer compras
                        </button>
                        <button type="button" class="suggestion-btn bg-white hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs border border-purple-200 transition-colors" data-suggestion="Relaxar e descansar">
                            Relaxar e descansar
                        </button>
                    </div>
                </div>
                
                <!-- Botões de ação -->
                <div class="flex space-x-3 pt-4">
                    <button 
                        type="button" 
                        id="cancel-add-objetivo-btn"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
                    >
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-purple-200 hover:from-purple-700 hover:to-purple-800 text-purple-800 font-semibold py-3 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 transform hover:scale-105"
                    >
                        <i class="fas fa-check"></i>
                        <span>Confirmar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- Modal Adicionar Objetivo ---
    document.addEventListener('DOMContentLoaded', function () {
        const openAddObjetivoModalBtn = document.getElementById('open-add-objetivo-modal-btn');
        const closeAddObjetivoModalBtn = document.getElementById('close-add-objetivo-modal-btn');
        const cancelAddObjetivoBtn = document.getElementById('cancel-add-objetivo-btn');
        const addObjetivoModal = document.getElementById('add-objetivo-modal');
        const addObjetivoModalPanel = document.getElementById('add-objetivo-modal-panel');
        const addObjetivoModalOverlay = document.getElementById('add-objetivo-modal-overlay');
        const nomeObjetivoInput = document.getElementById('nome_objetivo');
        const nomeObjetivoCount = document.getElementById('nome_objetivo_count');

        // Contador de caracteres
        if (nomeObjetivoInput && nomeObjetivoCount) {
            nomeObjetivoInput.addEventListener('input', function() {
                const count = this.value.length;
                nomeObjetivoCount.textContent = count;
                
                // Mudança de cor baseada no limite
                if (count > 80) {
                    nomeObjetivoCount.parentElement.classList.add('text-red-500');
                    nomeObjetivoCount.parentElement.classList.remove('text-gray-400');
                } else {
                    nomeObjetivoCount.parentElement.classList.remove('text-red-500');
                    nomeObjetivoCount.parentElement.classList.add('text-gray-400');
                }
            });
            // Inicializa o contador
            nomeObjetivoCount.textContent = nomeObjetivoInput.value.length;
        }

        // Sugestões de objetivos
        const suggestionBtns = document.querySelectorAll('.suggestion-btn');
        suggestionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const suggestion = this.dataset.suggestion;
                if (nomeObjetivoInput) {
                    nomeObjetivoInput.value = suggestion;
                    nomeObjetivoInput.dispatchEvent(new Event('input'));
                    nomeObjetivoInput.focus();
                }
            });
        });

        const openAddObjetivoModal = () => {
            addObjetivoModal.classList.remove('hidden');
            addObjetivoModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                addObjetivoModalPanel.classList.remove('scale-95', 'opacity-0');
                addObjetivoModalPanel.classList.add('scale-100', 'opacity-100');
                if (nomeObjetivoInput && nomeObjetivoCount) {
                    nomeObjetivoCount.textContent = nomeObjetivoInput.value.length;
                    nomeObjetivoInput.focus();
                }
            }, 10);
        };

        const closeAddObjetivoModal = () => {
            if (!addObjetivoModalPanel) return;
            addObjetivoModalPanel.classList.remove('scale-100', 'opacity-100');
            addObjetivoModalPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                addObjetivoModal.classList.add('hidden');
                addObjetivoModal.classList.remove('flex');
                document.body.style.overflow = '';
                // Limpar formulário
                document.getElementById('add-objetivo-form').reset();
                if (nomeObjetivoCount) nomeObjetivoCount.textContent = '0';
            }, 300);
        };

        if (openAddObjetivoModalBtn) openAddObjetivoModalBtn.addEventListener('click', openAddObjetivoModal);
        if (closeAddObjetivoModalBtn) closeAddObjetivoModalBtn.addEventListener('click', closeAddObjetivoModal);
        if (cancelAddObjetivoBtn) cancelAddObjetivoBtn.addEventListener('click', closeAddObjetivoModal);
        if (addObjetivoModalOverlay) addObjetivoModalOverlay.addEventListener('click', closeAddObjetivoModal);

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && addObjetivoModal && !addObjetivoModal.classList.contains('hidden')) {
                closeAddObjetivoModal();
            }
        });
    });
</script>
