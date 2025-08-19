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
                        <h2 class="text-2xl font-bold text-white">Escolher Seguro</h2>
                        <p class="text-green-100 text-base">Selecione um seguro disponível para esta viagem</p>
                    </div>
                </div>
                <button id="close-add-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <div id="add-insurance-list" class="space-y-4"></div>
            <div id="add-insurance-message" class="mt-4 text-green-600 font-semibold hidden"></div>
            <div class="flex justify-end mt-6">
                <button type="button" id="cancel-add-insurance-btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-colors">
                    Fechar
                </button>
            </div>
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
const addInsuranceList = document.getElementById('add-insurance-list');
const addInsuranceMessage = document.getElementById('add-insurance-message');

function fetchAvailableInsurances() {
    // Usa o trip_id da sessão, que deve ser o da viagem atual
    const tripId = window.tripId || '{{ session('trip_id') }}';
    fetch(`/trip/insurances?trip_id=${tripId}`)
        .then(res => res.json())
        .then(data => {
            addInsuranceList.innerHTML = '';
            if (data.seguros && data.seguros.length) {
                data.seguros.forEach(seguro => {
                    const div = document.createElement('div');
                    div.className = 'p-4 bg-green-50 border border-green-200 rounded-lg flex flex-col space-y-2 insurance-card-modal';
                    div.innerHTML = `
                        <div class="font-bold text-green-800">${seguro.site ?? seguro.nome ?? 'Seguro'}</div>
                        <div class="text-sm text-gray-600">${Array.isArray(seguro.dados) ? seguro.dados.join('<br>') : seguro.dados ?? seguro.detalhes ?? ''}</div>
                        <div class="flex space-x-2 mt-2">
                            ${seguro.is_selected ? `<span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">Selecionado</span>` : `
                            <button class="select-insurance-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors" data-id="${seguro.pk_id_seguro}">
                                Selecionar este seguro
                            </button>
                            `}
                        </div>
                    `;
                    addInsuranceList.appendChild(div);
                });
            } else {
                addInsuranceList.innerHTML = '<div class="text-gray-500">Nenhum seguro cadastrado para esta viagem.</div>';
            }
        });
}

addInsuranceList?.addEventListener('click', function(e) {
    if (e.target.classList.contains('select-insurance-btn')) {
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
                addInsuranceMessage.textContent = data.mensagem;
                addInsuranceMessage.classList.remove('hidden');
                setTimeout(() => {
                    addInsuranceMessage.classList.add('hidden');
                    closeAddInsuranceModal();
                    location.reload();
                }, 1200);
            } else {
                addInsuranceMessage.textContent = data.mensagem || 'Erro ao selecionar seguro.';
                addInsuranceMessage.classList.remove('hidden');
            }
        });
    }
});

const openAddInsuranceModal = () => {
    addInsuranceModal.classList.remove('hidden');
    addInsuranceModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        addInsuranceModalPanel.classList.remove('scale-95', 'opacity-0');
        addInsuranceModalPanel.classList.add('scale-100', 'opacity-100');
        fetchAvailableInsurances();
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
        addInsuranceList.innerHTML = '';
        addInsuranceMessage.classList.add('hidden');
    }, 300);
};

if (openAddInsuranceModalBtn) openAddInsuranceModalBtn.addEventListener('click', openAddInsuranceModal);
if (closeAddInsuranceModalBtn) closeAddInsuranceModalBtn.addEventListener('click', closeAddInsuranceModal);
if (cancelAddInsuranceBtn) cancelAddInsuranceBtn.addEventListener('click', closeAddInsuranceModal);
if (addInsuranceModalOverlay) addInsuranceModalOverlay.addEventListener('click', closeAddInsuranceModal);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        if (addInsuranceModal && !addInsuranceModal.classList.contains('hidden')) {
            closeAddInsuranceModal();
        }
    }
});
</script>
