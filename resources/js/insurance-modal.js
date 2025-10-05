/**
 * Módulo para o Modal de Seguros - Refatorado
 * Gerencia a busca, exibição e seleção de seguros para um viajante específico.
 */
window.InsuranceModal = (function() {
    'use strict';

    let state = {
        isOpen: false,
        isSearching: false,
        viagem: null,
        viajante: null,
        pollInterval: null,
        attemptCount: 0,
        maxAttempts: 20, // Aumentado para ~1 minuto (20 * 3s)
        startTime: null,
        currentInsurances: [],
        selectedInsurance: null
    };

    const elements = {};

    function init() {
        cacheElements();
        bindEvents();
    }

    function cacheElements() {
        elements.modal = document.getElementById('insurance-modal');
        elements.overlay = document.getElementById('insurance-modal-overlay');
        elements.panel = document.getElementById('insurance-modal-panel');
        elements.closeBtn = document.getElementById('close-insurance-modal-btn');
        elements.cancelBtn = document.getElementById('cancel-insurance-modal-btn');
        elements.confirmBtn = document.getElementById('confirm-insurance-selection-btn');
        elements.form = document.getElementById('insurance-search-form');
        elements.destinoSelect = document.getElementById('insurance-destino');
        elements.dataIdaInput = document.getElementById('insurance-data-ida');
        elements.dataVoltaInput = document.getElementById('insurance-data-volta');
        elements.viajantesInfo = document.getElementById('insurance-viajantes-info');
        elements.searchBtn = document.getElementById('start-insurance-search-btn');
        elements.retryBtn = document.getElementById('retry-insurance-search-btn');
        elements.loading = document.getElementById('insurance-loading');
        elements.results = document.getElementById('insurance-results');
        elements.error = document.getElementById('insurance-error');
        elements.resultsList = document.getElementById('insurance-results-list');
        elements.errorMessage = document.getElementById('insurance-error-message');
        elements.progressBar = document.getElementById('insurance-progress-bar');
        elements.progressText = document.getElementById('insurance-progress-text');
    }

    function bindEvents() {
        elements.closeBtn?.addEventListener('click', close);
        elements.cancelBtn?.addEventListener('click', close);
        elements.overlay?.addEventListener('click', close);
        elements.searchBtn?.addEventListener('click', startSearch);
        elements.retryBtn?.addEventListener('click', startSearch);
        elements.confirmBtn?.addEventListener('click', confirmSelection);
        document.addEventListener('keydown', e => e.key === 'Escape' && state.isOpen && close());
    }

    function open(data = {}) {
        if (!data.viagem || !data.viajante) {
            console.error('[InsuranceModal] É necessário fornecer dados da viagem e do viajante.');
            return;
        }
        state.viagem = data.viagem;
        state.viajante = data.viajante;
        state.isOpen = true;

        elements.modal.classList.remove('hidden');
        elements.modal.classList.add('flex');
        requestAnimationFrame(() => {
            elements.panel.classList.remove('scale-95', 'opacity-0');
            elements.panel.classList.add('scale-100', 'opacity-100');
        });

        prefillForm();
        resetUI();
    }

    function close() {
        if (!state.isOpen) return;
        
        stopPolling();
        
        elements.panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            elements.modal.classList.add('hidden');
            elements.modal.classList.remove('flex');
            state.isOpen = false;
            state.isSearching = false;
        }, 300);
    }

    function prefillForm() {
        document.getElementById('insurance-modal-title').textContent = `Seguro para ${state.viajante.nome}`;
        elements.viajantesInfo.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-user-shield text-green-600"></i>
                <span>Buscando seguro para <strong>${state.viajante.nome}</strong> (${state.viajante.idade} anos).</span>
            </div>`;
        
        if (state.viagem.data_inicio_viagem) {
            elements.dataIdaInput.value = state.viagem.data_inicio_viagem.split(' ')[0];
        }
        if (state.viagem.data_final_viagem) {
            elements.dataVoltaInput.value = state.viagem.data_final_viagem.split(' ')[0];
        }
    }

    function resetUI() {
        elements.form.classList.remove('hidden');
        elements.loading.classList.add('hidden');
        elements.results.classList.add('hidden');
        elements.error.classList.add('hidden');
        elements.confirmBtn.disabled = true;
        elements.resultsList.innerHTML = '';
        state.selectedInsurance = null;
    }

    function startSearch() {
        const formData = getFormData();
        if (!formData) return;

        state.isSearching = true;
        state.attemptCount = 0;
        state.startTime = Date.now();
        
        elements.form.classList.add('hidden');
        elements.loading.classList.remove('hidden');
        
        searchAttempt(formData); // Primeira chamada imediata
        state.pollInterval = setInterval(() => searchAttempt(formData), 3000);
    }

    function getFormData() {
        if (!elements.destinoSelect.value) { alert('Selecione um destino.'); return null; }
        if (!elements.dataIdaInput.value) { alert('Informe a data de ida.'); return null; }
        if (!elements.dataVoltaInput.value) { alert('Informe a data de volta.'); return null; }
        if (new Date(elements.dataVoltaInput.value) < new Date(elements.dataIdaInput.value)) {
            alert('A data de volta deve ser igual ou posterior à data de ida.'); return null;
        }

        return {
            destino: parseInt(elements.destinoSelect.value),
            data_ida: elements.dataIdaInput.value,
            data_volta: elements.dataVoltaInput.value
        };
    }
    
    function searchAttempt(formData) {
        if (state.attemptCount >= state.maxAttempts) {
            handleSearchTimeout();
            return;
        }
        state.attemptCount++;
        updateProgress();

        // Usando a rota nomeada que você já tem no web.php
        fetch(window.APP_ROUTES.searchInsurance, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.ok ? res.json() : Promise.reject(new Error(`HTTP ${res.status}`)))
        .then(data => {
            if (data.error) throw new Error(data.error);

            if (data.frases && data.frases.length > 0) {
                handleSearchSuccess(data.frases);
            }
        })
        .catch(err => {
            console.error('[InsuranceModal] Erro na busca:', err);
            handleSearchError(err.message);
        });
    }

    function updateProgress() {
        const progress = Math.min((state.attemptCount / state.maxAttempts) * 100, 95);
        const elapsed = Math.floor((Date.now() - state.startTime) / 1000);
        elements.progressBar.style.width = `${progress}%`;
        elements.progressText.textContent = `Buscando... (${elapsed}s)`;
    }
    
    function stopPolling() {
        if (state.pollInterval) {
            clearInterval(state.pollInterval);
            state.pollInterval = null;
        }
        state.isSearching = false;
    }
    
    function handleSearchSuccess(insurances) {
        stopPolling();
        state.currentInsurances = insurances;
        elements.loading.classList.add('hidden');
        elements.results.classList.remove('hidden');
        renderInsurances(insurances);
    }
    
    function handleSearchError(message) {
        stopPolling();
        elements.loading.classList.add('hidden');
        elements.error.classList.remove('hidden');
        elements.errorMessage.textContent = `Ocorreu um erro: ${message}. Por favor, tente novamente.`;
    }

    function handleSearchTimeout() {
        stopPolling();
        elements.loading.classList.add('hidden');
        elements.error.classList.remove('hidden');
        elements.errorMessage.textContent = 'A busca demorou mais que o esperado ou não encontrou resultados. Verifique os dados e tente novamente.';
    }

    function renderInsurances(insurances) {
        let html = '';
        insurances.forEach((insurance, index) => {
            html += `
                <div class="insurance-seguro-card" data-insurance-index="${index}">
                    <div class="p-4 border-b">
                        <p class="text-sm text-gray-500">${insurance.seguradora}</p>
                        <h3 class="font-bold text-gray-800">${insurance.plano}</h3>
                    </div>
                    <div class="p-4 flex-grow grid grid-cols-2 gap-3 text-sm">
                        <div><i class="fas fa-user-doctor text-green-500 mr-2"></i>${insurance.coberturas.medica}</div>
                        <div><i class="fas fa-suitcase-rolling text-green-500 mr-2"></i>${insurance.coberturas.bagagem}</div>
                    </div>
                    <div class="p-4 bg-gray-50 text-right">
                        <span class="text-xs text-green-700 font-semibold">PIX</span>
                        <p class="text-xl font-extrabold text-green-600">${insurance.precos.pix}</p>
                    </div>
                </div>
            `;
        });
        elements.resultsList.innerHTML = html;
        
        elements.resultsList.querySelectorAll('.insurance-seguro-card').forEach(card => {
            card.addEventListener('click', () => selectInsurance(card));
        });
    }

    function selectInsurance(cardElement) {
        elements.resultsList.querySelectorAll('.insurance-seguro-card').forEach(c => c.classList.remove('selected'));
        cardElement.classList.add('selected');
        
        const insuranceIndex = parseInt(cardElement.dataset.insuranceIndex, 10);
        state.selectedInsurance = state.currentInsurances[insuranceIndex];
        
        elements.confirmBtn.disabled = false;
    }

    function confirmSelection() {
        if (!state.selectedInsurance) return;

        elements.confirmBtn.disabled = true;
        elements.confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Salvando...';
        
        fetch(window.APP_ROUTES.saveTravelerInsurance, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                fk_id_viagem: state.viagem.pk_id_viagem,
                fk_id_viajante: state.viajante.pk_id_viajante,
                seguro_data: state.selectedInsurance
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Dispara evento para a outra tela "escutar" a mudança
                window.dispatchEvent(new CustomEvent('insuranceUpdated', { 
                    detail: { 
                        viajanteId: state.viajante.pk_id_viajante, 
                        seguro: data.seguro 
                    }
                }));
                close();
            } else {
                throw new Error(data.message || 'Erro ao salvar o seguro.');
            }
        })
        .catch(err => {
            alert(err.message);
        })
        .finally(() => {
            elements.confirmBtn.disabled = false;
            elements.confirmBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Confirmar Seleção';
        });
    }

    return { init, open };
})();

document.addEventListener('DOMContentLoaded', () => {
    window.InsuranceModal.init();
});

// Função global para ser chamada de outros scripts
window.openInsuranceModal = function(data) {
    window.InsuranceModal.open(data);
};