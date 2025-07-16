<div id="viajantes-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Fundo com blur -->
    <div id="viajantes-modal-overlay" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>

    <!-- Conteúdo do Modal -->
    <div id="viajantes-modal-panel" class="relative w-full max-w-lg transform rounded-lg bg-white p-6 shadow-xl transition-all duration-300 scale-95 opacity-0">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-800">Todos os Viajantes</h3>
            <button id="close-viajantes-modal-btn" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="mt-4 ">
            <!-- Input de busca -->
            <div class="relative mb-4">
                <input type="text" id="viajante-search-input" placeholder="Procurar viajante..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Lista de viajantes com scroll -->
            <div class="max-h-96 overflow-y-auto pr-2">
                <ul class="space-y-2">
                    @foreach($viajantes as $viajante)
                        <li class="viajante-item group flex items-center justify-between transition-colors duration-150 hover:bg-gray-100 rounded px-2 py-2" data-nome="{{ strtolower($viajante->nome) }}">
                            <div>
                                <span class="font-semibold">{{ $viajante->nome }}</span>
                                <span class="text-gray-500 text-sm">- {{ $viajante->idade }} anos</span>
                            </div>
                            <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 bg-red-100 hover:bg-red-200 rounded p-1.5 text-xs font-semibold transition-colors duration-150 flex items-center" title="Remover viajante">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" /></svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Modal Viajantes ---
document.addEventListener('DOMContentLoaded', function () {
    const openViajantesModalBtn = document.getElementById('open-viajantes-modal-btn');
    const closeViajantesModalBtn = document.getElementById('close-viajantes-modal-btn');
    const viajantesModal = document.getElementById('viajantes-modal');
    const viajantesModalPanel = document.getElementById('viajantes-modal-panel');
    const viajantesModalOverlay = document.getElementById('viajantes-modal-overlay');
    const viajanteSearchInput = document.getElementById('viajante-search-input');
    const viajanteItems = document.querySelectorAll('.viajante-item');

    const openViajantesModal = () => {
        viajantesModal.classList.remove('hidden');
        viajantesModal.classList.add('flex');
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
        }, 300);
    };

    if (openViajantesModalBtn) openViajantesModalBtn.addEventListener('click', openViajantesModal);
    if (closeViajantesModalBtn) closeViajantesModalBtn.addEventListener('click', closeViajantesModal);
    if (viajantesModalOverlay) viajantesModalOverlay.addEventListener('click', closeViajantesModal);

    if (viajanteSearchInput) {
        viajanteSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            viajanteItems.forEach(function(item) {
                const nomeViajante = item.dataset.nome;
                item.style.display = nomeViajante.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && viajantesModal && !viajantesModal.classList.contains('hidden')) {
            closeViajantesModal();
        }
    });
});
</script>