<div id="ia-place-details-modal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 hidden" 
     style="background: rgba(17,24,39,0.3); backdrop-filter: blur(8px);">
    <div id="ia-place-details-modal-panel" 
         class="bg-white rounded-lg shadow-xl relative flex flex-col max-w-2xl w-full max-h-[90vh] overflow-y-auto transform scale-95 opacity-0 transition-all duration-300">
        
        <!-- Botão Fechar -->
        <button type="button" id="close-ia-place-details-btn" 
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Conteúdo do Modal -->
        <div id="ia-place-details-content" class="overflow-y-auto flex-1 w-full">
            <div class="bg-white rounded-lg">
                <!-- Container de Fotos -->
                <div id="ia-place-details-photos-container" class="flex space-x-2 overflow-x-auto pb-2"></div>
                
                <!-- Informações Principais -->
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2" id="ia-place-details-name">Nome do Local</h2>
                    <p class="text-sm text-gray-600 mb-4" id="ia-place-details-address">Endereço do Local</p>
                    
                    <!-- Badges e Avaliação -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span id="ia-place-details-type" 
                              class="px-3 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-800">
                            Tipo: N/A
                        </span>
                        <div id="ia-place-details-rating-container" class="flex items-center text-sm text-gray-500"></div>
                    </div>
                    
                    <!-- Informações Adicionais -->
                    <div id="ia-place-details-vicinity" class="text-gray-700 mb-4"></div>
                    <div id="ia-place-details-website" class="mb-2"></div>
                    <div id="ia-place-details-phone" class="text-gray-700 mb-4"></div>
                    
                    <!-- Horário de Funcionamento -->
                    <div id="ia-place-details-opening-hours"></div>
                    
                    <!-- Avaliações -->
                    <div id="ia-place-details-reviews"></div>
                </div>
            </div>
        </div>
        
        <!-- Rodapé com Ações -->
        <div class="p-8 border-t border-gray-200 flex flex-col sm:flex-row justify-end items-center gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <label for="ia-itineraryDate" class="text-gray-700 font-medium whitespace-nowrap">Data da visita:</label>
                    <input type="date" 
                           id="ia-itineraryDate" 
                           class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
                </div>
                <div class="flex items-center gap-2">
                    <label for="ia-itineraryTime" class="text-gray-700 font-medium whitespace-nowrap">Hora da visita:</label>
                    <select id="ia-itineraryTime" 
                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
                    </select>
                </div>
            </div>
            <button type="button" id="save-place-to-trip-btn"
                class="cursor-pointer px-6 py-3 text-sm font-medium text-black bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg w-full sm:w-auto">
                ➕ Adicionar ao Itinerário
            </button>
        </div>
    </div>
</div>

<script>
// Reutilizar funções do detailsmodal
function generateTimeOptions() {
    let options = '<option value="">Selecione um horário</option>';
    for (let hour = 0; hour < 24; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const hourStr = hour.toString().padStart(2, '0');
            const minuteStr = minute.toString().padStart(2, '0');
            const timeValue = `${hourStr}:${minuteStr}`;
            options += `<option value="${timeValue}">${timeValue}</option>`;
        }
    }
    return options;
}

// Inicializar select de horário ao carregar o modal
document.addEventListener('DOMContentLoaded', function() {
    const timeSelect = document.getElementById('ia-itineraryTime');
    if (timeSelect) {
        timeSelect.innerHTML = generateTimeOptions();
    }
    // Aplicar limites iniciais ao datepicker caso as variáveis globais já existam
    setItineraryDateLimits();
});

// Normaliza diferentes formatos de data para YYYY-MM-DD
function normalizeDate(dateStr) {
    if (!dateStr) return '';
    if (typeof dateStr !== 'string') return '';
    // Já está em YYYY-MM-DD
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
    // ISO com time
    if (dateStr.includes('T')) return dateStr.split('T')[0];
    // Formato DD/MM/YYYY
    if (dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            const dd = parts[0].padStart(2, '0');
            const mm = parts[1].padStart(2, '0');
            const yyyy = parts[2];
            return `${yyyy}-${mm}-${dd}`;
        }
    }
    return '';
}

// Define min/max do input date do modal com base nas datas da viagem
function setItineraryDateLimits() {
    const dateInput = document.getElementById('ia-itineraryDate');
    if (!dateInput) return;

    // Valores globais esperados (padrões do projeto)
    let rawStart = window.dataInicioViagem || window.data_inicio_viagem || window.tripStart || '';
    let rawEnd = window.dataFimViagem || window.data_fim_viagem || window.tripEnd || '';

    // Fallback: buscar atributos/data-attributes que podem conter as datas (ex.: modalSelectTrip, pointSection)
    if (!rawStart || !rawEnd) {
        const elDataAttrs = document.querySelector('[data-data-inicio-viagem], [data-data-fim-viagem]');
        if (elDataAttrs) {
            rawStart = rawStart || elDataAttrs.getAttribute('data-data-inicio-viagem') || rawStart;
            rawEnd = rawEnd || elDataAttrs.getAttribute('data-data-fim-viagem') || rawEnd;
        }

        // Fallback: inputs com classes usadas em detailsTrip
        if (!rawStart) {
            const elStart = document.querySelector('.data-inicio-input') || document.querySelector('input[name="data_inicio_viagem"]');
            if (elStart && elStart.value) rawStart = elStart.value;
        }
        if (!rawEnd) {
            const elEnd = document.querySelector('.data-fim-input') || document.querySelector('input[name="data_final_viagem"]');
            if (elEnd && elEnd.value) rawEnd = elEnd.value;
        }
    }

    const start = normalizeDate(rawStart);
    const end = normalizeDate(rawEnd);

    // Se não encontramos datas localmente, buscar via endpoint /trip/current
    if (!start && !end) {
        // Se houver um tripId explícito no modal ou global, passe como query para obter as datas corretas
        const modalTripId = (iaModal && iaModal.dataset && iaModal.dataset.tripId) ? iaModal.dataset.tripId : null;
        const tripIdToFetch = modalTripId || (window.currentTripId ? window.currentTripId : null);
        const url = tripIdToFetch ? `/trip/current?trip_id=${encodeURIComponent(tripIdToFetch)}` : '/trip/current';
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(resp => {
                if (resp.status === 204) return null;
                return resp.json();
            })
            .then(json => {
                if (!json) {
                    console.log('IA Modal: nenhuma viagem atual na sessão');
                    return;
                }
                if (json.success && json.data) {
                    const s = normalizeDate(json.data.data_inicio || json.data.data_inicio_viagem || json.data.data_inicio);
                    const e = normalizeDate(json.data.data_fim || json.data.data_fim_viagem || json.data.data_fim);
                    if (s) dateInput.setAttribute('min', s);
                    if (e) dateInput.setAttribute('max', e);
                    // ajustar valor
                    if (!dateInput.value && s) dateInput.value = s;
                    console.log('IA Modal date limits applied (from /trip/current):', { s, e });
                } else {
                    console.log('IA Modal: resposta inválida de /trip/current', json);
                }
            })
            .catch(err => console.error('Erro ao buscar /trip/current:', err));
        return; // vamos aplicar o resultado vindo do fetch
    }

    if (start) dateInput.setAttribute('min', start); else dateInput.removeAttribute('min');
    if (end) dateInput.setAttribute('max', end); else dateInput.removeAttribute('max');

    console.log('IA Modal date limits applied:', { start, end, rawStart, rawEnd });

    // Ajustar valor atual para ficar dentro do intervalo
    if (!dateInput.value) {
        if (start) dateInput.value = start;
    } else {
        if (start && dateInput.value < start) dateInput.value = start;
        if (end && dateInput.value > end) dateInput.value = end;
    }
}

// Observador para aplicar limites assim que o modal for mostrado
const iaModal = document.getElementById('ia-place-details-modal');
if (iaModal) {
    const mo = new MutationObserver((mutations) => {
        for (const m of mutations) {
            if (m.attributeName === 'class') {
                // quando a classe 'hidden' for removida, considera que modal abriu
                if (!iaModal.classList.contains('hidden')) {
                    // pequena espera para garantir que elementos internos foram renderizados
                    setTimeout(() => setItineraryDateLimits(), 50);
                }
            }
        }
    });
    mo.observe(iaModal, { attributes: true });
}
</script>