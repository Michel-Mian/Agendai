<div id="insurance-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="insurance-modal-overlay" class="absolute inset-0 bg-green-900/60 backdrop-blur-md" aria-hidden="true"></div>
    <div id="insurance-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="bg-green-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Detalhes do Seguro</h2>
                        <p class="text-green-100 text-base">Veja as informações do seguro selecionado</p>
                    </div>
                </div>
                <button id="close-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <div id="insurance-details-content">
                <div id="insurance-list" class="space-y-4"></div>
                <div id="insurance-change-message" class="mt-4 text-green-600 font-semibold hidden"></div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" id="close-insurance-modal-btn-footer" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-colors">
                    Fechar
                </button>
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
const insuranceList = document.getElementById('insurance-list');
const insuranceChangeMessage = document.getElementById('insurance-change-message');

function fetchInsurances() {
    const tripId = window.tripId || '{{ session('trip_id') }}';
    fetch(`/trip/insurances?trip_id=${tripId}`)
        .then(res => res.json())
        .then(data => {
            insuranceList.innerHTML = '';
            if (data.seguros && data.seguros.length) {
                data.seguros.forEach(seguro => {
                    const div = document.createElement('div');
                    div.className = 'p-4 bg-green-50 border border-green-200 rounded-lg flex flex-col space-y-2';
                    div.innerHTML = `
                        <div class="font-bold text-green-800">${seguro.site ?? seguro.nome ?? 'Seguro'}</div>
                        <div class="text-sm text-gray-600">${seguro.dados ?? seguro.detalhes ?? ''}</div>
                        <div class="flex space-x-2 mt-2">
                            ${seguro.is_selected ? `<span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">Selecionado</span>` : `
                            <button class="change-insurance-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors" data-id="${seguro.pk_id_seguro}">
                                Trocar para este seguro
                            </button>
                            `}
                        </div>
                    `;
                    insuranceList.appendChild(div);
                });
            } else {
                insuranceList.innerHTML = '<div class="text-gray-500">Nenhum seguro cadastrado para esta viagem.</div>';
            }
        });
}

insuranceList?.addEventListener('click', function(e) {
    if (e.target.classList.contains('change-insurance-btn')) {
        const seguroId = e.target.getAttribute('data-id');
        const tripId = window.tripId || '{{ session('trip_id') }}';
        fetch('/trip/update-insurance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seguro_id: seguroId, trip_id: tripId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                insuranceChangeMessage.textContent = data.mensagem;
                insuranceChangeMessage.classList.remove('hidden');
                setTimeout(() => {
                    insuranceChangeMessage.classList.add('hidden');
                    closeInsuranceModal();
                    // Atualiza a seção de seguros na tela principal (opcional)
                    location.reload();
                }, 1500);
            } else {
                insuranceChangeMessage.textContent = data.mensagem || 'Erro ao trocar seguro.';
                insuranceChangeMessage.classList.remove('hidden');
            }
        });
    }
});

const openInsuranceModal = () => {
    insuranceModal.classList.remove('hidden');
    insuranceModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        insuranceModalPanel.classList.remove('scale-95', 'opacity-0');
        insuranceModalPanel.classList.add('scale-100', 'opacity-100');
        fetchInsurances();
    }, 10);
};

const closeInsuranceModal = () => {
    if (!insuranceModalPanel) return;
    insuranceModalPanel.classList.remove('scale-100', 'opacity-100');
    insuranceModalPanel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        insuranceModal.classList.add('hidden');
        insuranceModal.classList.remove('flex');
        document.body.style.overflow = '';
        insuranceList.innerHTML = '';
        insuranceChangeMessage.classList.add('hidden');
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

// Expose openInsuranceModal globally if needed
window.openInsuranceModal = openInsuranceModal;
</script>
