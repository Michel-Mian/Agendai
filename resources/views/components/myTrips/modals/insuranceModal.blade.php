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
            <div id="selected-insurance-info" class="mb-6"></div>
            <div id="insurance-list" class="flex flex-wrap gap-6 max-h-[400px] overflow-y-auto"></div>
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
document.addEventListener("DOMContentLoaded", function () {
    // Função para renderizar o seguro selecionado
    function renderSelectedInsurance(insurances) {
        const selectedDiv = document.getElementById('selected-insurance-info');
        if (!selectedDiv) return;
        const selected = (insurances || []).find(s => s.is_selected);
        if (selected) {
            let dados = selected.dados;
            if (typeof dados === 'string') {
                try { dados = JSON.parse(dados); } catch (e) {}
            }
            selectedDiv.innerHTML = `
                <div class="bg-green-100 border border-green-300 rounded-lg p-4 flex items-center space-x-4 mb-2">
                    <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                    <div>
                        <div class="font-bold text-green-800">${selected.site ?? selected.nome ?? 'Seguro'}</div>
                        <div class="text-sm text-gray-700">
                            ${Array.isArray(dados) ? dados.join('<br>') : (dados ?? selected.detalhes ?? '')}
                        </div>
                    </div>
                    <span class="ml-auto bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">Selecionado</span>
                </div>
            `;
        } else {
            selectedDiv.innerHTML = '';
        }
    }

    // Função para renderizar os cards de seguros
    function renderInsuranceCards(insurances) {
        const list = document.getElementById('insurance-list');
        if (!list) return;
        list.innerHTML = '';
        (insurances || []).forEach((seguro, idx) => {
            let dados = seguro.dados;
            if (typeof dados === 'string') {
                try { dados = JSON.parse(dados); } catch (e) {}
            }
            let selected = seguro.is_selected ? 'selected' : '';
            let html = `
                <div class="seguro-card border rounded-xl p-4 mb-3 cursor-pointer ${selected}" data-idx="${idx}" data-seguro='${JSON.stringify(seguro)}'>
                    <div class="font-bold text-blue-700">${seguro.site ?? seguro.nome ?? 'Seguro'}</div>
                    <div class="text-sm text-gray-700 mb-2">
                        ${Array.isArray(dados) ? dados.join('<br>') : (dados ?? seguro.detalhes ?? '')}
                    </div>
                    ${seguro.preco ? `<div class="text-green-700 font-bold">${seguro.preco}</div>` : ''}
                    ${seguro.link ? `<a href="${seguro.link}" target="_blank" class="text-blue-500 underline">Ver detalhes</a>` : ''}
                </div>
            `;
            list.innerHTML += html;
        });

        // Evento de seleção/troca de seguro
        document.querySelectorAll('.seguro-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.seguro-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                // Salva seleção no backend
                let seguroData = JSON.parse(card.getAttribute('data-seguro'));
                seguroData.is_selected = true;
                fetch("/trip/update-insurance", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value
                    },
                    body: JSON.stringify({ seguro_id: seguroData.pk_id_seguro })
                }).then(() => {
                    // Atualiza sessionStorage para revisão
                    let fullName = (seguroData.site || '') + (seguroData.dados && seguroData.dados[0] ? ' ' + seguroData.dados[0] : '');
                    sessionStorage.setItem('selectedSeguroName', fullName.trim());
                    document.getElementById('insurance-change-message').innerText = "Seguro alterado!";
                    document.getElementById('insurance-change-message').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('insurance-change-message').classList.add('hidden');
                    }, 1200);
                    // Dispara evento para atualizar o seguro na tela principal
                    window.dispatchEvent(new CustomEvent('insuranceChanged', { detail: { seguro: seguroData } }));
                });
            });
        });
    }

    // Carrega seguros ao abrir o modal
    function fetchInsurances() {
        const tripId = window.tripId || '{{ session('trip_id') }}';
        fetch(`/trip/insurances?trip_id=${tripId}`)
            .then(res => res.json())
            .then(data => {
                renderSelectedInsurance(data.seguros);
                renderInsuranceCards(data.seguros);
            });
    }

    // Carrega seguros instantaneamente ao abrir o modal
    window.addEventListener('openInsuranceModal', function() {
        fetchInsurances();
    });

    // Atualiza seguro selecionado ao fechar o modal (opcional)
    document.getElementById('close-insurance-modal-btn-footer')?.addEventListener('click', function() {
        fetchInsurances();
    });
});
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