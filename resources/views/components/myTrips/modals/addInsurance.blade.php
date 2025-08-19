<div id="add-insurance-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="add-insurance-modal-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" aria-hidden="true"></div>
    <div id="add-insurance-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="bg-green-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Adicionar Seguro</h2>
                        <p class="text-green-100 text-base">Inclua um novo seguro à viagem</p>
                    </div>
                </div>
                <button id="close-add-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <form id="add-insurance-form" method="POST" action="#" class="space-y-8">
                @csrf
                <div class="space-y-3">
                    <label for="nome_seguro" class="flex items-center space-x-3 text-base font-semibold text-gray-700">
                        <i class="fas fa-shield-alt text-green-500 text-xl"></i>
                        <span>Nome do Seguro</span>
                    </label>
                    <input type="text" id="nome_seguro" name="nome_seguro" maxlength="100" class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base" placeholder="Digite o nome do seguro..." required>
                </div>
                <div class="space-y-3">
                    <label for="detalhes_seguro" class="flex items-center space-x-3 text-base font-semibold text-gray-700">
                        <i class="fas fa-info-circle text-green-500 text-xl"></i>
                        <span>Detalhes</span>
                    </label>
                    <textarea id="detalhes_seguro" name="detalhes_seguro" rows="4" class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base" placeholder="Descreva os detalhes do seguro..." required></textarea>
                </div>
                <div class="flex space-x-4 pt-6">
                    <button type="button" id="cancel-add-insurance-btn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 text-lg">
                        <i class="fas fa-times text-lg"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 transform hover:scale-105 text-lg">
                        <i class="fas fa-check text-lg"></i>
                        <span>Salvar Seguro</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const openAddInsuranceModalBtn = document.getElementById('open-add-insurance-modal-btn');
    const closeAddInsuranceModalBtn = document.getElementById('close-add-insurance-modal-btn');
    const cancelAddInsuranceBtn = document.getElementById('cancel-add-insurance-btn');
    const addInsuranceModal = document.getElementById('add-insurance-modal');
    const addInsuranceModalPanel = document.getElementById('add-insurance-modal-panel');
    const addInsuranceModalOverlay = document.getElementById('add-insurance-modal-overlay');

    const openAddInsuranceModal = () => {
        addInsuranceModal.classList.remove('hidden');
        addInsuranceModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            addInsuranceModalPanel.classList.remove('scale-95', 'opacity-0');
            addInsuranceModalPanel.classList.add('scale-100', 'opacity-100');
            document.getElementById('nome_seguro').focus();
        }, 10);
    };

    const closeAddInsuranceModal = () => {
        if (!addInsuranceModalPanel) return;
        addInsuranceModalPanel.classList.remove('scale-100', 'opacity-100');
        addInsuranceModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            addInsuranceModal.classList.add('hidden');
            addInsuranceModal.classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('add-insurance-form').reset();
        }, 300);
    };

    if (openAddInsuranceModalBtn) openAddInsuranceModalBtn.addEventListener('click', openAddInsuranceModal);
    if (closeAddInsuranceModalBtn) closeAddInsuranceModalBtn.addEventListener('click', closeAddInsuranceModal);
    if (cancelAddInsuranceBtn) cancelAddInsuranceBtn.addEventListener('click', closeAddInsuranceModal);
    if (addInsuranceModalOverlay) addInsuranceModalOverlay.addEventListener('click', closeAddInsuranceModal);

    // Escape fecha o modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (addInsuranceModal && !addInsuranceModal.classList.contains('hidden')) {
                closeAddInsuranceModal();
            }
        }
    });
</script>
