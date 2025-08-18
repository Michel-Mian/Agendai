<div id="insurance-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="insurance-modal-overlay" class="absolute inset-0 bg-green-900/60 backdrop-blur-md" aria-hidden="true"></div>
    <div id="insurance-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="bg-green-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <i class="fas fa-shield-alt text-green-200 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Detalhes do Seguro</h2>
                    </div>
                </div>
                <button id="close-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <div id="insurance-details-content">
                <!-- Conteúdo dinâmico do seguro selecionado -->
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" id="close-insurance-modal-btn-footer" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">Fechar</button>
            </div>
        </div>
    </div>
</div>
<script>
    const closeInsuranceModalBtn = document.getElementById('close-insurance-modal-btn');
    const closeInsuranceModalFooterBtn = document.getElementById('close-insurance-modal-btn-footer');
    const insuranceModal = document.getElementById('insurance-modal');
    const insuranceModalPanel = document.getElementById('insurance-modal-panel');
    const insuranceModalOverlay = document.getElementById('insurance-modal-overlay');

    const closeInsuranceModal = () => {
        if (!insuranceModalPanel) return;
        insuranceModalPanel.classList.remove('scale-100', 'opacity-100');
        insuranceModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            insuranceModal.classList.add('hidden');
            insuranceModal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    };

    if (closeInsuranceModalBtn) closeInsuranceModalBtn.addEventListener('click', closeInsuranceModal);
    if (closeInsuranceModalFooterBtn) closeInsuranceModalFooterBtn.addEventListener('click', closeInsuranceModal);
    if (insuranceModalOverlay) insuranceModalOverlay.addEventListener('click', closeInsuranceModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (insuranceModal && !insuranceModal.classList.contains('hidden')) {
                closeInsuranceModal();
            }
        }
    });
</script>
