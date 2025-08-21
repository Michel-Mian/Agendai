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
                        <h2 class="text-2xl font-bold text-white">Trocar Seguro</h2>
                        <p class="text-green-100 text-base">Escolha um seguro para substituir o atual ou busque novos</p>
                    </div>
                </div>
                <button id="close-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-8">
            <button id="buscar-novos-seguros-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold mb-4">
                Buscar Novos Seguros
            </button>
            <div id="insurance-list" class="flex flex-wrap gap-6 max-h-[400px] overflow-y-auto"></div>
            <div id="insurance-scraping-list" class="flex flex-wrap gap-6 mt-6"></div>
            <div id="insurance-change-message" class="mt-4 text-green-600 font-semibold hidden"></div>
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
const buscarNovosSegurosBtn = document.getElementById('buscar-novos-seguros-btn');
const insuranceScrapingList = document.getElementById('insurance-scraping-list');

// 1. Carrega todos os seguros do banco (sem duplicidade do selecionado)
function renderInsuranceCards(insurances) {
    insuranceList.innerHTML = '';
    if (insurances && insurances.length) {
        let selectedFound = false;
        insurances.forEach((seguro, idx) => {
            let dados = seguro.dados;
            if (typeof dados === 'string') {
                try { dados = JSON.parse(dados); } catch (e) {}
            }
            const isSelected = seguro.is_selected;
            // Só destaca o selecionado uma vez
            if (isSelected && selectedFound) return;
            if (isSelected) selectedFound = true;
            insuranceList.innerHTML += `
                <div class="insurance-card ${isSelected ? 'selected border-2 border-green-600 bg-green-50 shadow-lg' : 'border border-gray-200 bg-white'} rounded-xl p-4 flex-1 min-w-[260px] max-w-[320px] flex flex-col justify-between cursor-pointer transition-all duration-200 mb-3"
                    data-id="${seguro.pk_id_seguro}">
                    <h5 class="font-bold text-green-800 mb-2">${seguro.site ?? seguro.nome ?? 'Seguro'}</h5>
                    <div class="insurance-data flex flex-col gap-2 mb-2">
                        ${Array.isArray(dados) ? dados.map(l => `<div>${l}</div>`).join('') : (dados ?? seguro.detalhes ?? '')}
                    </div>
                    ${seguro.link ? `<a href="${seguro.link}" target="_blank" class="text-green-600 font-semibold underline mb-2">Ver detalhes</a>` : ''}
                    <div class="flex space-x-2 mt-2">
                        ${isSelected
                            ? `<span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">Selecionado</span>`
                            : `<span class="text-green-600 text-xs">Clique para selecionar</span>`
                        }
                    </div>
                </div>
            `;
        });
    } else {
        insuranceList.innerHTML = '<div class="text-gray-500">Nenhum seguro cadastrado para esta viagem.</div>';
    }
}

// 2. Carrega todos os seguros do scraping (todos scripts)
function renderScrapingCards(scrapingInsurances) {
    insuranceScrapingList.innerHTML = '';
    if (scrapingInsurances && scrapingInsurances.length) {
        scrapingInsurances.forEach((seguro, idx) => {
            let dados = seguro.dados;
            if (typeof dados === 'string') {
                try { dados = JSON.parse(dados); } catch (e) {}
            }
            insuranceScrapingList.innerHTML += `
                <div class="insurance-card border border-blue-300 rounded-xl p-4 flex-1 min-w-[260px] max-w-[320px] flex flex-col justify-between cursor-pointer transition-all duration-200 mb-3 bg-blue-50"
                    data-seguro='${JSON.stringify(seguro)}'>
                    <h5 class="font-bold text-blue-800 mb-2">${seguro.site ?? seguro.nome ?? 'Seguro'}</h5>
                    <div class="insurance-data flex flex-col gap-2 mb-2">
                        ${Array.isArray(dados) ? dados.map(l => `<div>${l}</div>`).join('') : (dados ?? seguro.detalhes ?? '')}
                    </div>
                    ${seguro.link ? `<a href="${seguro.link}" target="_blank" class="text-blue-600 font-semibold underline mb-2">Ver detalhes</a>` : ''}
                    <div class="flex space-x-2 mt-2">
                        <span class="text-blue-600 text-xs">Clique para adicionar</span>
                    </div>
                </div>
            `;
        });
    }
}

function fetchInsurances() {
    insuranceList.innerHTML = '<div class="text-gray-500 text-center py-8 w-full">Carregando seguros...</div>';
    insuranceScrapingList.innerHTML = '';
    const tripId = window.tripId || '{{ session('trip_id') }}';
    fetch(`/trip/insurances?trip_id=${tripId}`)
        .then(res => res.json())
        .then(data => {
            // Mostra todos os seguros cadastrados, não só o selecionado
            renderInsuranceCards(data.seguros);
        });
}

buscarNovosSegurosBtn?.addEventListener('click', function() {
    insuranceScrapingList.innerHTML = '<div class="text-gray-500 text-center py-8 w-full">Buscando seguros...</div>';
    // Use os dados reais do formulário ou da viagem
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
        // Mostra todos os seguros dos scripts (não só EasySeguroViagem)
        if (data.frases && data.frases.length) {
            renderScrapingCards(data.frases);
        } else {
            insuranceScrapingList.innerHTML = '<div class="text-red-500">Nenhum seguro encontrado.</div>';
        }
    });
});

// Troca o seguro selecionado no banco e atualiza instantaneamente o modal
insuranceList?.addEventListener('click', function(e) {
    const card = e.target.closest('.insurance-card[data-id]');
    if (card && card.dataset.id) {
        insuranceChangeMessage.textContent = 'Alterando seguro...';
        insuranceChangeMessage.classList.remove('hidden');
        const seguroId = card.dataset.id;
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
                setTimeout(() => {
                    insuranceChangeMessage.classList.add('hidden');
                    fetchInsurances(); // Atualiza o modal instantaneamente
                }, 800);
            } else {
                insuranceChangeMessage.textContent = data.mensagem || 'Erro ao trocar seguro.';
            }
        });
    }
});

// Adiciona seguro do scraping e marca como selecionado, desmarcando os outros, atualiza instantaneamente
insuranceScrapingList?.addEventListener('click', function(e) {
    const card = e.target.closest('.insurance-card[data-seguro]');
    if (card && card.dataset.seguro) {
        insuranceChangeMessage.textContent = 'Adicionando seguro...';
        insuranceChangeMessage.classList.remove('hidden');
        const seguroData = card.dataset.seguro;
        const tripId = window.tripId || '{{ session('trip_id') }}';
        fetch('/trip/salvar-seguro', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ...JSON.parse(seguroData), trip_id: tripId, is_selected: true })
        })
        .then(res => res.json())
        .then(data => {
            insuranceChangeMessage.textContent = data.mensagem || 'Seguro adicionado!';
            setTimeout(() => {
                insuranceChangeMessage.classList.add('hidden');
                fetchInsurances(); // Atualiza o modal instantaneamente
            }, 800);
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
        insuranceScrapingList.innerHTML = '';
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

window.openInsuranceModal = openInsuranceModal;
</script>
<style>
#insurance-list, #insurance-scraping-list {
    max-height: 400px;
    overflow-y: auto;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}
.insurance-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: #fff;
    min-height: 260px;
    max-height: 260px;
    height: 260px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    transition: border-color 0.2s, background 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    cursor: pointer;
    padding: 18px 16px;
    margin-bottom: 12px;
    overflow: hidden;
}
.insurance-card.selected,
.insurance-card.border-green-600 {
    border: 2.5px solid #2ecc40 !important;
    background: #eafaf1 !important;
    box-shadow: 0 0 0 2px #2ecc4033 !important;
}
.insurance-card h5 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}
.insurance-card .insurance-data {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 10px;
}
.insurance-card .insurance-data > div {
    padding: 2px 0;
    border-bottom: 1px solid #f0f0f0;
    word-break: break-word;
}
.insurance-card a {
    margin-top: 8px;
    color: #2ecc40;
    font-weight: 500;
    text-decoration: underline;
}
</style>