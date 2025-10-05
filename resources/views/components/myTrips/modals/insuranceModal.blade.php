{{-- resources/views/components/insuranceModal.blade.php --}}

<div id="insurance-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    {{-- Overlay --}}
    <div id="insurance-modal-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

    {{-- Painel --}}
    <div id="insurance-modal-panel" class="relative w-full max-w-5xl max-h-[95vh] transform rounded-2xl bg-gray-50 shadow-2xl transition-all duration-300 scale-95 opacity-0 flex flex-col">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-4">
                <div class="bg-white/10 rounded-lg p-2">
                    <i class="fas fa-shield-alt text-blue-500 text-xl"></i>
                </div>
                <div>
                    <h2 id="insurance-modal-title" class="text-xl font-bold text-blue-500">Seguro Viagem</h2>
                    <p id="insurance-modal-subtitle" class="text-gray-500 text-sm">Busque e selecione o melhor seguro</p>
                </div>
            </div>
            <button id="close-insurance-modal-btn" class="bg-white/10 hover:bg-white/20 text-gray-600 p-2 rounded-full transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Conteúdo Principal --}}
        <div class="flex-grow overflow-y-auto p-6">
            
            {{-- 1. Formulário de Busca (Estado Inicial) --}}
            <div id="insurance-search-form" class="">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="insurance-destino" class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
                        <select id="insurance-destino" name="destino" class="w-full border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="5">África</option>
                            <option value="14">América Central</option>
                            <option value="1">América do Norte</option>
                            <option value="4">América do Sul</option>
                            <option value="12">Argentina</option>
                            <option value="6">Ásia</option>
                            <option value="2" selected>Europa</option>
                            <option value="13">Internacional</option>
                            <option value="7">Oceania</option>
                            <option value="11">Oriente Médio</option>
                        </select>
                    </div>
                    <div>
                        <label for="insurance-data-ida" class="block text-sm font-medium text-gray-700 mb-1">Data de Ida</label>
                        <input type="date" id="insurance-data-ida" name="data_ida" class="w-full border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="insurance-data-volta" class="block text-sm font-medium text-gray-700 mb-1">Data de Volta</label>
                        <input type="date" id="insurance-data-volta" name="data_volta" class="w-full border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div id="insurance-viajantes-info" class="mb-6 p-4 bg-blue-50 border border-blue-500 rounded-lg text-sm text-blue-800">
                    {{-- Informações do viajante serão injetadas aqui via JS --}}
                </div>
                <div class="text-center">
                    <button id="start-insurance-search-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-transform hover:scale-105 shadow-md">
                        <i class="fas fa-search mr-2"></i>
                        Buscar Seguros
                    </button>
                </div>
            </div>

            {{-- 2. Tela de Loading --}}
            <div id="insurance-loading" class="hidden flex-col items-center justify-center py-8">
                {{-- Animação do Avião --}}
                <div class="loading-container-modal">
                    <div class="circle-modal"></div>
                    <div class="cloud-modal cloud1-modal"></div>
                    <div class="cloud-modal cloud2-modal"></div>
                    <div class="cloud-modal cloud3-modal"></div>
                    <div class="cloud-modal cloud4-modal"></div>
                    <div class="cloud-modal cloud5-modal"></div>
                    <div class="cloud-modal cloud6-modal"></div>
                    <div class="airplane-modal"></div>
                </div>
                <div class="loading-text-modal">Buscando...</div>

                {{-- Barra de Progresso (oculta, mas funcional para o JS) --}}
                <div class="mt-4 w-full max-w-sm hidden">
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div id="insurance-progress-bar" class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <div id="insurance-progress-text" class="text-xs text-gray-500 mt-1 text-center">Iniciando...</div>
            </div>

            {{-- 3. Tela de Resultados --}}
            <div id="insurance-results" class="hidden">
                 <div id="insurance-results-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Cards de seguro serão renderizados aqui pelo JS --}}
                 </div>
            </div>

            {{-- 4. Tela de Erro --}}
            <div id="insurance-error" class="hidden text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-red-800 mb-2">Ocorreu um Erro</h3>
                <p id="insurance-error-message" class="text-red-700 mb-6">Não foi possível buscar os seguros. Tente novamente.</p>
                <button id="retry-insurance-search-btn" class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Tentar Novamente
                </button>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="bg-gray-100 px-6 py-4 flex justify-end items-center space-x-3 flex-shrink-0 border-t">
            <button id="cancel-insurance-modal-btn" class="bg-white hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg border border-gray-300 transition-colors">
                Cancelar
            </button>
            <button id="confirm-insurance-selection-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed shadow" disabled>
                <i class="fas fa-check mr-2"></i>
                Confirmar Seleção
            </button>
        </div>
    </div>
</div>

{{-- Estilos para os cards --}}
<style>
.insurance-seguro-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease-in-out;
    overflow: hidden;
    cursor: pointer;
}
.insurance-seguro-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.08);
}
.insurance-seguro-card.selected {
    border-color: #0088FF; /* Cor azul para combinar com a animação */
    box-shadow: 0 0 0 3px rgba(0, 136, 255, 0.3);
}
</style>

{{-- Estilos para a animação de loading (Copiado do index.blade.php) --}}
<style>
/* ===== Animação de Loading para o Modal ===== */
.loading-container-modal {
    position: relative;
    width: 300px;
    height: 250px;
    overflow: hidden;
    margin: 0 auto;
    transform: scale(0.8);
}
.circle-modal {
    position: absolute;
    top: 50%; left: 50%;
    width: 200px; height: 200px;
    background: #0088FF;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 40px 0 #0088FF33;
}
.cloud-modal {
    position: absolute;
    background: url('/imgs/loading/clouds.png') no-repeat center/cover;
    opacity: 0.9;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}
.cloud1-modal { width: 90px; height: 90px; top: 32%; left: 44%; animation: moveCloud1Modal 14s linear infinite; }
.cloud2-modal { width: 70px; height: 70px; top: 28%; left: 60%; animation: moveCloud2Modal 16s linear infinite; animation-delay: 4s; }
.cloud3-modal { width: 60px; height: 60px; top: 48%; left: 35%; animation: moveCloud3Modal 13s linear infinite; animation-delay: 8s; }
.cloud4-modal { width: 80px; height: 80px; top: 46%; left: 68%; animation: moveCloud4Modal 15s linear infinite; animation-delay: 2s; }
.cloud5-modal { width: 50px; height: 50px; top: 42%; left: 53%; animation: moveCloud5Modal 12s linear infinite; animation-delay: 6s; }
.cloud6-modal { width: 65px; height: 65px; top: 30%; left: 57%; animation: moveCloud6Modal 17s linear infinite; animation-delay: 3s; }

.airplane-modal {
    position: absolute;
    top: 50%; left: 50%;
    width: 200px; height: 200px;
    background: url('/imgs/loading/plane.png') no-repeat center/cover;
    transform: translate(-50%, -50%) rotate(10deg);
    animation: flyModal 4s ease-in-out infinite;
    z-index: 10;
}
.loading-text-modal {
    position: relative;
    text-align: center;
    margin-top: -20px;
    font-size: 1.5rem;
    color: #0088FF;
    font-weight: 600;
    letter-spacing: 1px;
    text-shadow: 0 2px 8px #e0e7ef;
    z-index: 20;
}

/* Animações Keyframes */
@keyframes moveCloud1Modal { 0% { left: 44%; } 50% { left: 48%; } 100% { left: 44%; } }
@keyframes moveCloud2Modal { 0% { left: 60%; } 50% { left: 64%; } 100% { left: 60%; } }
@keyframes moveCloud3Modal { 0% { left: 35%; } 50% { left: 39%; } 100% { left: 35%; } }
@keyframes moveCloud4Modal { 0% { left: 68%; } 50% { left: 72%; } 100% { left: 68%; } }
@keyframes moveCloud5Modal { 0% { left: 53%; } 50% { left: 57%; } 100% { left: 53%; } }
@keyframes moveCloud6Modal { 0% { left: 57%; } 50% { left: 61%; } 100% { left: 57%; } }
@keyframes flyModal {
    0%   { transform: translate(-50%, -50%) rotate(10deg) translateY(0); }
    50%  { transform: translate(-50%, -50%) rotate(12deg) translateY(-8px); }
    100% { transform: translate(-50%, -50%) rotate(10deg) translateY(0); }
}
</style>