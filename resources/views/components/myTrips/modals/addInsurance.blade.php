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
                        <h2 class="text-2xl font-bold text-white">Simular Seguros</h2>
                        <p class="text-green-100 text-base">Veja e selecione um seguro disponível para esta viagem</p>
                    </div>
                </div>
                <button id="close-add-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <div class="mb-4">
                <button id="buscar-seguros-modal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                    Buscar Seguros
                </button>
            </div>
            <div id="add-insurance-current" class="mb-4"></div>
            <div id="add-insurance-list" class="space-y-4 max-h-[400px] overflow-y-auto"></div>
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
const buscarSegurosBtn = document.getElementById('buscar-seguros-modal');
const addInsuranceList = document.getElementById('add-insurance-list');
const addInsuranceMessage = document.getElementById('add-insurance-message');
const addInsuranceCurrent = document.getElementById('add-insurance-current');

function renderCurrentInsurance(insurances) {
    const selected = insurances.find(s => s.is_selected);
    if (selected) {
        let dados = selected.dados;
        if (typeof dados === 'string') {
            try { dados = JSON.parse(dados); } catch (e) {}
        }
        addInsuranceCurrent.innerHTML = `
            <div class="border border-green-600 bg-green-50 rounded-xl p-4 mb-2 flex flex-col gap-2">
                <div class="font-bold text-green-800">${selected.site ?? selected.nome ?? 'Seguro Selecionado'}</div>
                <div class="text-sm text-gray-600">
                    ${Array.isArray(dados) ? dados.map(l => `<div>${l}</div>`).join('') : (dados ?? selected.detalhes ?? '')}
                </div>
                <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold w-fit">Atual</span>
            </div>
        `;
    } else {
        addInsuranceCurrent.innerHTML = '';
    }
}

function renderInsuranceCards(insurances) {
    addInsuranceList.innerHTML = '';
    if (insurances && insurances.length) {
        renderCurrentInsurance(insurances);
        insurances.forEach((seguro, idx) => {
            let dados = seguro.dados;
            if (typeof dados === 'string') {
                try { dados = JSON.parse(dados); } catch (e) {}
            }
            const isSelected = seguro.is_selected ? 'border-2 border-green-600 bg-green-50' : 'border border-gray-200 bg-white';
            addInsuranceList.innerHTML += `
                <div class="insurance-card-modal ${isSelected} rounded-xl p-4 mb-3 flex flex-col gap-2">
                    <div class="font-bold text-green-800">${seguro.site ?? seguro.nome ?? 'Seguro'}</div>
                    <div class="text-sm text-gray-600">
                        ${Array.isArray(dados) ? dados.map(l => `<div>${l}</div>`).join('') : (dados ?? seguro.detalhes ?? '')}
                    </div>
                    <div class="flex space-x-2 mt-2">
                        ${seguro.is_selected
                            ? `<span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">Selecionado</span>`
                            : `<button class="select-insurance-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors" data-id="${seguro.pk_id_seguro}">Selecionar este seguro</button>`
                        }
                    </div>
                </div>
            `;
        });
    } else {
        addInsuranceCurrent.innerHTML = '';
        addInsuranceList.innerHTML = '<div class="text-gray-500">Nenhum seguro cadastrado para esta viagem.</div>';
    }
}

function fetchAvailableInsurances() {
    addInsuranceList.innerHTML = '<div class="text-gray-500 text-center py-8">Carregando seguros...</div>';
    addInsuranceCurrent.innerHTML = '';
    const tripId = window.tripId || '{{ session('trip_id') }}';
    fetch(`/trip/insurances?trip_id=${tripId}`)
        .then(res => res.json())
        .then(data => {
            renderInsuranceCards(data.seguros);
        });
}

buscarSegurosBtn?.addEventListener('click', function() {
    addInsuranceList.innerHTML = '<div class="text-gray-500 text-center py-8">Buscando seguros...</div>';
    addInsuranceCurrent.innerHTML = '';
    // Simula os dados do formulário (ajuste conforme necessário)
    const motivo = 1, destino = 2, data_ida = '2024-07-20', data_volta = '2024-07-25', qtd_passageiros = 1, idades = [30];
    fetch('/trip/insurance-ajax', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ motivo, destino, data_ida, data_volta, qtd_passageiros, idades })
    })
    .then(res => res.json())
    .then(data => {
        if (data.frases && data.frases.length) {
            renderInsuranceCards(data.frases.map((seguro, idx) => ({
                site: seguro.site,
                dados: seguro.dados,
                link: seguro.link,
                is_selected: false,
                pk_id_seguro: 'simulado_' + idx
            })));
        } else {
            addInsuranceList.innerHTML = '<div class="text-red-500">Nenhum seguro encontrado.</div>';
        }
    });
});

addInsuranceList?.addEventListener('click', function(e) {
    if (e.target.classList.contains('select-insurance-btn')) {
        addInsuranceMessage.textContent = 'Alterando seguro...';
        addInsuranceMessage.classList.remove('hidden');
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
                setTimeout(() => {
                    addInsuranceMessage.classList.add('hidden');
                    closeAddInsuranceModal();
                    location.reload();
                }, 1200);
            } else {
                addInsuranceMessage.textContent = data.mensagem || 'Erro ao selecionar seguro.';
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
        addInsuranceCurrent.innerHTML = '';
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
<style>
#add-insurance-list {
    max-height: 400px;
    overflow-y: auto;
}
</style>
