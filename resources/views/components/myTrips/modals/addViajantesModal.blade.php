<div id="add-viajante-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="add-viajante-modal-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" aria-hidden="true"></div>
    
    <div id="add-viajante-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="bg-green-600 px-8 py-6"> {{-- Increased padding --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4"> {{-- Increased space-x --}}
                    <div class="bg-white/20 rounded-lg p-3"> {{-- Increased padding --}}
                        <i class="fas fa-user-plus text-white text-2xl"></i> {{-- Increased icon size --}}
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Adicionar Viajante</h3> {{-- Text color changed to white for contrast, increased text size --}}
                        <p class="text-green-100 text-base">Inclua um novo membro à viagem</p> {{-- Text color adjusted for contrast, increased text size --}}
                    </div>
                </div>
                <button id="close-add-viajante-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors"> {{-- Increased padding --}}
                    <i class="fas fa-times text-xl"></i> {{-- Increased icon size --}}
                </button>
            </div>
        </div>
        
        <div class="p-8">
            @if ($errors->has('nome_viajante') || $errors->has('idade_viajante'))
                <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center space-x-3 mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i> {{-- Increased icon size --}}
                        <span class="font-medium text-red-800 text-lg">Erro na validação</span> {{-- Increased text size --}}
                    </div>
                    @if ($errors->has('nome_viajante'))
                        <div class="text-red-600 text-base">{{ $errors->first('nome_viajante') }}</div> {{-- Increased text size --}}
                    @endif
                    @if ($errors->has('idade_viajante'))
                        <div class="text-red-600 text-base">{{ $errors->first('idade_viajante') }}</div> {{-- Increased text size --}}
                    @endif
                </div>
            @endif
            
            <form id="add-viajante-form" method="POST" action="{{ route('viajantes.store') }}" class="space-y-8"> {{-- Increased space-y --}}
                @csrf
                <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
                
                <div class="space-y-3"> {{-- Increased space-y --}}
                    <label for="nome_viajante" class="flex items-center space-x-3 text-base font-semibold text-gray-700"> {{-- Increased space-x and text size --}}
                        <i class="fas fa-user text-green-500 text-xl"></i> {{-- Increased icon size --}}
                        <span>Nome do viajante</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="nome_viajante" 
                            name="nome_viajante" 
                            maxlength="100" 
                            class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base" {{-- Increased padding and text size --}}
                            placeholder="Digite o nome completo..." 
                            required
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <span class="text-sm text-gray-400 bg-white px-2 py-1 rounded-full border"> {{-- Increased text size --}}
                                <span id="nome_viajante_count">0</span>/100
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3"> {{-- Increased space-y --}}
                    <label for="idade_viajante" class="flex items-center space-x-3 text-base font-semibold text-gray-700"> {{-- Increased space-x and text size --}}
                        <i class="fas fa-birthday-cake text-green-500 text-xl"></i> {{-- Increased icon size --}}
                        <span>Idade</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="idade_viajante" 
                            name="idade_viajante" 
                            min="0" 
                            max="127" 
                            class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base" {{-- Increased padding and text size --}}
                            placeholder="Digite a idade..." 
                            required
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-calendar-alt text-gray-400 text-lg"></i> {{-- Increased icon size --}}
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center space-x-2"> {{-- Increased text size and space-x --}}
                        <i class="fas fa-info-circle text-base"></i> {{-- Increased icon size --}}
                        <span>Idade entre 0 e 127 anos</span>
                    </p>
                </div>
                
                <div class="flex space-x-4 pt-6"> {{-- Increased space-x and padding-top --}}
                    <button 
                        type="button" 
                        id="cancel-add-viajante-btn"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 text-lg" {{-- Increased padding and text size --}}
                    >
                        <i class="fas fa-times text-lg"></i> {{-- Increased icon size --}}
                        <span>Cancelar</span>
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 transform hover:scale-105 text-lg" {{-- Removed gradient, increased padding and text size --}}
                    >
                        <i class="fas fa-check text-lg"></i> {{-- Increased icon size --}}
                        <span>Confirmar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- Modal Adicionar Viajante ---
    const openAddViajanteModalBtn = document.getElementById('open-add-viajante-modal-btn');
    const closeAddViajanteModalBtn = document.getElementById('close-add-viajante-modal-btn');
    const cancelAddViajanteBtn = document.getElementById('cancel-add-viajante-btn');
    const addViajanteModal = document.getElementById('add-viajante-modal');
    const addViajanteModalPanel = document.getElementById('add-viajante-modal-panel');
    const addViajanteModalOverlay = document.getElementById('add-viajante-modal-overlay');

    const openAddViajanteModal = () => {
        addViajanteModal.classList.remove('hidden');
        addViajanteModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            addViajanteModalPanel.classList.remove('scale-95', 'opacity-0');
            addViajanteModalPanel.classList.add('scale-100', 'opacity-100');
            document.getElementById('nome_viajante').focus();
        }, 10);
    };

    const closeAddViajanteModal = () => {
        if (!addViajanteModalPanel) return;
        addViajanteModalPanel.classList.remove('scale-100', 'opacity-100');
        addViajanteModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            addViajanteModal.classList.add('hidden');
            addViajanteModal.classList.remove('flex');
            document.body.style.overflow = '';
            // Limpar formulário
            document.getElementById('add-viajante-form').reset();
            document.getElementById('nome_viajante_count').textContent = '0';
        }, 300);
    };

    if (openAddViajanteModalBtn) {
        openAddViajanteModalBtn.addEventListener('click', openAddViajanteModal);
    }
    if (closeAddViajanteModalBtn) closeAddViajanteModalBtn.addEventListener('click', closeAddViajanteModal);
    if (cancelAddViajanteBtn) cancelAddViajanteBtn.addEventListener('click', closeAddViajanteModal);
    if (addViajanteModalOverlay) addViajanteModalOverlay.addEventListener('click', closeAddViajanteModal);

    // Contador de caracteres do nome
    const nomeViajanteInput = document.getElementById('nome_viajante');
    const nomeViajanteCount = document.getElementById('nome_viajante_count');
    if (nomeViajanteInput && nomeViajanteCount) {
        nomeViajanteInput.addEventListener('input', function() {
            const count = this.value.length;
            nomeViajanteCount.textContent = count;
            
            // Mudança de cor baseada no limite
            if (count > 80) {
                nomeViajanteCount.parentElement.classList.add('text-red-500');
                nomeViajanteCount.parentElement.classList.remove('text-gray-400');
            } else {
                nomeViajanteCount.parentElement.classList.remove('text-red-500');
                nomeViajanteCount.parentElement.classList.add('text-gray-400');
            }
        });
        // Inicializa o contador
        nomeViajanteCount.textContent = nomeViajanteInput.value.length;
    }

    // Escape fecha o modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (addViajanteModal && !addViajanteModal.classList.contains('hidden')) {
                closeAddViajanteModal();
            }
        }
    });
</script>