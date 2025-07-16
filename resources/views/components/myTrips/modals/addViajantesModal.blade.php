<div id="add-viajante-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur -->
    <div id="add-viajante-modal-overlay" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>
    <!-- Conteúdo do Modal -->
    <div id="add-viajante-modal-panel" class="relative w-full max-w-md transform rounded-lg bg-white p-6 shadow-xl transition-all duration-300 scale-95 opacity-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Adicionar Viajante</h3>
            <button id="close-add-viajante-modal-btn" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="add-viajante-form" method="POST" action="{{ route('viajantes.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
            @if ($errors->has('nome_viajante'))
                <div class="text-red-600 text-sm mb-2">{{ $errors->first('nome_viajante') }}</div>
            @endif
            @if ($errors->has('idade_viajante'))
                <div class="text-red-600 text-sm mb-2">{{ $errors->first('idade_viajante') }}</div>
            @endif
            <div>
                <label for="nome_viajante" class="block text-sm font-medium text-gray-700 mb-1">Nome do viajante</label>
                <input type="text" id="nome_viajante" name="nome_viajante" maxlength="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-400 focus:border-transparent" placeholder="Digite o nome..." required>
                <div class="text-xs text-gray-500 mt-1"><span id="nome_viajante_count">0</span>/100 caracteres</div>
            </div>
            <div>
                <label for="idade_viajante" class="block text-sm font-medium text-gray-700 mb-1">Idade</label>
                <input type="number" id="idade_viajante" name="idade_viajante" min="0" max="127" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-400 focus:border-transparent" placeholder="Digite a idade..." required>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition-colors">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Modal Adicionar Viajante ---
    const openAddViajanteModalBtn = document.getElementById('open-add-viajante-modal-btn');
    const closeAddViajanteModalBtn = document.getElementById('close-add-viajante-modal-btn');
    const addViajanteModal = document.getElementById('add-viajante-modal');
    const addViajanteModalPanel = document.getElementById('add-viajante-modal-panel');
    const addViajanteModalOverlay = document.getElementById('add-viajante-modal-overlay');

    const openAddViajanteModal = () => {
        addViajanteModal.classList.remove('hidden');
        addViajanteModal.classList.add('flex');
        setTimeout(() => {
            addViajanteModalPanel.classList.remove('scale-95', 'opacity-0');
            addViajanteModalPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    const closeAddViajanteModal = () => {
        if (!addViajanteModalPanel) return;
        addViajanteModalPanel.classList.remove('scale-100', 'opacity-100');
        addViajanteModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            addViajanteModal.classList.add('hidden');
            addViajanteModal.classList.remove('flex');
        }, 300);
    };

    if (openAddViajanteModalBtn) {
        openAddViajanteModalBtn.addEventListener('click', openAddViajanteModal);
    }
    if (closeAddViajanteModalBtn) closeAddViajanteModalBtn.addEventListener('click', closeAddViajanteModal);
    if (addViajanteModalOverlay) addViajanteModalOverlay.addEventListener('click', closeAddViajanteModal);

    // Contador de caracteres do nome
    const nomeViajanteInput = document.getElementById('nome_viajante');
    const nomeViajanteCount = document.getElementById('nome_viajante_count');
    if (nomeViajanteInput && nomeViajanteCount) {
        nomeViajanteInput.addEventListener('input', function() {
            nomeViajanteCount.textContent = this.value.length;
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