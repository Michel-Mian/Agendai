<div id="add-objetivo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur -->
    <div id="add-objetivo-modal-overlay" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="add-objetivo-modal-panel" class="relative w-full max-w-md transform rounded-lg bg-white p-6 shadow-xl transition-all duration-300 scale-95 opacity-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Adicionar Objetivo</h3>
            <button id="close-add-objetivo-modal-btn" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="add-objetivo-form" method="POST" action="{{ route('objetivos.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
            @if ($errors->has('nome_objetivo'))
                <div class="text-red-600 text-sm mb-2">{{ $errors->first('nome_objetivo') }}</div>
            @endif
            <div>
                <label for="nome_objetivo" class="block text-sm font-medium text-gray-700 mb-1">Nome do objetivo</label>
                <input type="text" id="nome_objetivo" name="nome_objetivo" maxlength="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-400 focus:border-transparent" placeholder="Digite o objetivo..." required>
                <div class="text-xs text-gray-500 mt-1"><span id="nome_objetivo_count">0</span>/100 caracteres</div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition-colors">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Modal Adicionar Objetivo ---
document.addEventListener('DOMContentLoaded', function () {
    const openAddObjetivoModalBtn = document.getElementById('open-add-objetivo-modal-btn');
    const closeAddObjetivoModalBtn = document.getElementById('close-add-objetivo-modal-btn');
    const addObjetivoModal = document.getElementById('add-objetivo-modal');
    const addObjetivoModalPanel = document.getElementById('add-objetivo-modal-panel');
    const addObjetivoModalOverlay = document.getElementById('add-objetivo-modal-overlay');
    const nomeObjetivoInput = document.getElementById('nome_objetivo');
    const nomeObjetivoCount = document.getElementById('nome_objetivo_count');

    if (nomeObjetivoInput && nomeObjetivoCount) {
        nomeObjetivoInput.addEventListener('input', function() {
            nomeObjetivoCount.textContent = this.value.length;
        });
        // Inicializa o contador ao abrir o modal
        nomeObjetivoCount.textContent = nomeObjetivoInput.value.length;
    }

    const openAddObjetivoModal = () => {
        addObjetivoModal.classList.remove('hidden');
        addObjetivoModal.classList.add('flex');
        setTimeout(() => {
            addObjetivoModalPanel.classList.remove('scale-95', 'opacity-0');
            addObjetivoModalPanel.classList.add('scale-100', 'opacity-100');
            if (nomeObjetivoInput && nomeObjetivoCount) {
                nomeObjetivoCount.textContent = nomeObjetivoInput.value.length;
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
        }, 300);
    };

    if (openAddObjetivoModalBtn) openAddObjetivoModalBtn.addEventListener('click', openAddObjetivoModal);
    if (closeAddObjetivoModalBtn) closeAddObjetivoModalBtn.addEventListener('click', closeAddObjetivoModal);
    if (addObjetivoModalOverlay) addObjetivoModalOverlay.addEventListener('click', closeAddObjetivoModal);

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && addObjetivoModal && !addObjetivoModal.classList.contains('hidden')) {
            closeAddObjetivoModal();
        }
    });
});
</script>