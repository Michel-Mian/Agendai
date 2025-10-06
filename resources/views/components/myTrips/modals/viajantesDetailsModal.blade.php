<div id="viajantes-details-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="viajantes-details-modal-overlay" class="absolute inset-0 bg-gradient-to-br from-gray-900/60 to-gray-800/60 backdrop-blur-md" aria-hidden="true"></div>

    <div id="viajantes-details-modal-panel" class="relative w-full max-w-7xl max-h-[95vh] transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-users text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-green-500">Detalhes dos Viajantes</h3>
                        <p class="text-green-600 text-sm">{{ $viajantes->count() }} {{ $viajantes->count() == 1 ? 'viajante cadastrado' : 'viajantes cadastrados' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="add-viajante-from-details" class="bg-white/20 hover:bg-white/30 text-green-500 px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Adicionar</span>
                    </button>
                    <button id="close-viajantes-details-modal-btn" class="bg-white/20 hover:bg-white/30 text-green-500 p-2 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="flex h-[calc(95vh-80px)]">
            <!-- Sidebar -->
            <div class="w-80 bg-gray-50 border-r border-gray-200 flex flex-col">
                <!-- Search -->
                <div class="p-4 border-b border-gray-200">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="viajante-details-search-input" 
                            placeholder="Procurar viajante..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200"
                        >
                    </div>
                    <div class="mt-2 text-xs text-gray-500 text-center">
                        <span id="search-results-count-details">{{ $viajantes->count() }} resultado{{ $viajantes->count() != 1 ? 's' : '' }}</span>
                    </div>
                </div>

                <!-- Travelers List -->
                <div class="flex-1 overflow-y-auto p-4">
                    @if($viajantes->count())
                        <div class="space-y-2">
                            @foreach($viajantes as $index => $viajante)
                                <div class="viajante-sidebar-item cursor-pointer p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all duration-200 {{ $index === 0 ? 'bg-green-100 border-green-300' : 'bg-white' }}" 
                                     data-viajante-id="{{ $viajante->pk_id_viajante }}" 
                                     data-nome="{{ strtolower($viajante->nome) }}">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                            {{ strtoupper(substr($viajante->nome, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-gray-900 truncate">{{ $viajante->nome }}</h4>
                                            <p class="text-sm text-gray-500">{{ $viajante->idade }} anos</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Nenhum viajante cadastrado</p>
                        </div>
                    @endif
                    
                    <div id="no-results-message-details" class="hidden text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-search text-gray-400 text-lg"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Nenhum resultado encontrado</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div id="main-content" class="flex-1 flex flex-col px-0">
                @if($viajantes->count())
                    @foreach($viajantes as $index => $viajante)
                        <div id="viajante-details-{{ $viajante->pk_id_viajante }}" class="viajante-details-content h-full {{ $index !== 0 ? 'hidden' : '' }}">
                            <div class="h-full overflow-y-auto p-6">
                                <!-- Cabeçalho do viajante -->
                                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 mb-6 border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-2xl viajante-avatar">
                                                {{ strtoupper(substr($viajante->nome, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h2 class="text-2xl font-bold text-gray-900 viajante-nome-display">{{ $viajante->nome }}</h2>
                                                <p class="text-green-600 font-medium viajante-idade-display">{{ $viajante->idade }} anos</p>

                                                <!-- Hidden editable fields (shown when editing) -->
                                                <div class="viajante-edit-fields hidden mt-2">
                                                    <input type="text" name="nome" class="nome-input w-full border border-gray-300 rounded px-3 py-2 mb-2" value="{{ $viajante->nome }}">
                                                    <div class="flex items-center space-x-3">
                                                        <input type="number" name="idade" class="idade-input w-32 border border-gray-300 rounded px-3 py-2" value="{{ $viajante->idade }}" min="0" max="127">
                                                        <div class="responsavel-container hidden">
                                                            <select class="responsavel-select border border-gray-300 rounded px-3 py-2" name="responsavel_viajante_id">
                                                                <option value="">Selecione o responsável</option>
                                                                @foreach($viajantes as $resp)
                                                                    @if($resp->pk_id_viajante != $viajante->pk_id_viajante)
                                                                        <option value="{{ $resp->pk_id_viajante }}">{{ $resp->nome }} ({{ $resp->idade }} anos)</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1 responsavel-help hidden">Viajante menor de 18 anos precisa de um responsável da mesma viagem.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <button class="edit-viajante-btn bg-blue-100 hover:bg-blue-200 text-blue-600 p-3 rounded-lg transition-colors" title="Editar viajante" data-viajante-id="{{ $viajante->pk_id_viajante }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="save-viajante-btn bg-green-100 hover:bg-green-200 text-green-600 p-3 rounded-lg transition-colors hidden" data-viajante-id="{{ $viajante->pk_id_viajante }}" title="Salvar alterações">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="cancel-viajante-btn bg-gray-100 hover:bg-gray-200 text-gray-600 p-3 rounded-lg transition-colors hidden" data-viajante-id="{{ $viajante->pk_id_viajante }}" title="Cancelar edição">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-3 rounded-lg transition-colors" title="Remover viajante" onclick="return confirm('Tem certeza que deseja remover este viajante?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações detalhadas -->
                                <div class="space-y-6">
                                    <!-- Informações básicas -->
                                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-user text-green-500 mr-2"></i>
                                            Informações Básicas
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-gray-600">Nome completo:</span>
                                                <span class="font-medium text-gray-900 viajante-nome-field">{{ $viajante->nome }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-gray-600">Idade:</span>
                                                <span class="font-medium text-gray-900 viajante-idade-field">{{ $viajante->idade }} anos</span>
                                            </div>
                                            @if($viajante->idade < 18)
                                                @php
                                                    $responsavel = $viajantes->firstWhere('pk_id_viajante', $viajante->responsavel_viajante_id);
                                                @endphp
                                                <div class="flex justify-between items-center py-2">
                                                    <span class="text-gray-600">Responsável:</span>
                                                    @if($responsavel)
                                                        <span class="font-medium text-gray-900">{{ $responsavel->nome }} ({{ $responsavel->idade }} anos)</span>
                                                    @else
                                                        <span class="font-medium italic text-gray-600">Não definido</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-gray-600">Data de cadastro:</span>
                                                <span class="font-medium text-gray-900">{{ $viajante->created_at ? $viajante->created_at->format('d/m/Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2">
                                                <span class="text-gray-600">Última atualização:</span>
                                                <span class="font-medium text-gray-900">{{ $viajante->updated_at ? $viajante->updated_at->format('d/m/Y') : 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observações/Notas -->
                                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-sticky-note text-green-500 mr-2"></i>
                                            Observações
                                        </h3>
                                        <div class="bg-gray-50 rounded-lg p-4 min-h-[100px]">
                                            @if($viajante->observacoes && trim($viajante->observacoes) !== '')
                                                <p class="text-gray-800 whitespace-pre-wrap">{{ $viajante->observacoes }}</p>
                                            @else
                                                <p class="text-gray-600 italic">Nenhuma observação cadastrada para este viajante.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Seguro do Viajante -->
                                    @php
                                        $seguroViajante = $viajante->seguros->first();
                                    @endphp
                                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                                <i class="fas fa-shield-halved text-green-500 mr-2"></i>
                                                Seguro Viagem
                                            </h3>
                                            @if($seguroViajante)
                                                <div class="ml-4">
                                                    <button type="button" data-viajante-id="{{ $viajante->pk_id_viajante }}" class="js-open-insurance-modal bg-white border border-green-600 hover:bg-green-50 text-green-600 px-3 py-1 rounded text-sm transition-colors">
                                                        Alterar Seguro
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        @if($seguroViajante)
                                            <div class="border-l-4 border-green-500 pl-4 py-3 bg-green-50 rounded-r-lg">
                                                <div class="space-y-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-800">{{ $seguroViajante->seguradora }}</p>
                                                        <p class="text-sm text-gray-600">{{ $seguroViajante->plano }}</p>
                                                        @if($seguroViajante->detalhes_etarios)
                                                            <p class="text-xs text-gray-500 mt-1">{{ $seguroViajante->detalhes_etarios }}</p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        @if($seguroViajante->cobertura_medica)
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-user-doctor text-green-600 text-sm"></i>
                                                                <span class="text-sm text-gray-700">{{ $seguroViajante->cobertura_medica }}</span>
                                                            </div>
                                                        @endif
                                                        @if($seguroViajante->cobertura_bagagem)
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-suitcase-rolling text-green-600 text-sm"></i>
                                                                <span class="text-sm text-gray-700">{{ $seguroViajante->cobertura_bagagem }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between pt-2 border-t border-green-200">
                                                        <div class="flex space-x-4">
                                                            @if($seguroViajante->preco_pix)
                                                                <div class="text-sm">
                                                                    <span class="text-gray-600">PIX:</span>
                                                                    <span class="font-semibold text-green-700">{{ $seguroViajante->preco_pix }}</span>
                                                                </div>
                                                            @endif
                                                            @if($seguroViajante->preco_cartao)
                                                                <div class="text-sm">
                                                                    <span class="text-gray-600">Cartão:</span>
                                                                    <span class="font-semibold text-gray-700">{{ $seguroViajante->preco_cartao }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if($seguroViajante->link)
                                                            <a href="{{ $seguroViajante->link }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                                Ver Plano
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                                <i class="fas fa-shield-exclamation text-gray-400 text-2xl mb-2"></i>
                                                <p class="text-gray-600 italic">Nenhum seguro contratado para este viajante.</p>
                                                <p class="text-xs text-gray-500 mt-1">Considere contratar um seguro para maior segurança na viagem.</p>
                                                <div class="mt-3">
                                                    <button type="button" data-viajante-id="{{ $viajante->pk_id_viajante }}" class="js-open-insurance-modal bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors inline-flex items-center mx-auto">
                                                        Procurar Seguro
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Nenhum viajante cadastrado</h3>
                            <p class="text-gray-500 mb-6">Adicione viajantes para visualizar seus detalhes aqui</p>
                            <button id="add-first-viajante-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center space-x-2 mx-auto">
                                <i class="fas fa-user-plus"></i>
                                <span>Adicionar primeiro viajante</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===================================================================
    // PASSO 1: DECLARAÇÃO DE TODAS AS VARIÁVEIS E CONSTANTES
    // ===================================================================
    const openViajantesDetailsModalBtn = document.getElementById('open-viajantes-details-modal-btn');
    const closeViajantesDetailsModalBtn = document.getElementById('close-viajantes-details-modal-btn');
    const addViajanteFromDetailsBtn = document.getElementById('add-viajante-from-details');
    const addFirstViajanteBtn = document.getElementById('add-first-viajante-btn');
    const viajantesDetailsModal = document.getElementById('viajantes-details-modal');
    const viajantesDetailsModalPanel = document.getElementById('viajantes-details-modal-panel');
    const viajantesDetailsModalOverlay = document.getElementById('viajantes-details-modal-overlay');
    const viajanteDetailsSearchInput = document.getElementById('viajante-details-search-input');
    const viajanteSidebarItems = document.querySelectorAll('.viajante-sidebar-item');
    const viajanteDetailsContents = document.querySelectorAll('.viajante-details-content');
    const searchResultsCountDetails = document.getElementById('search-results-count-details');
    const noResultsMessageDetails = document.getElementById('no-results-message-details');
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

    // Variáveis de dados vindas do Blade
    const viagemData = @json($viagem);
    const todosOsViajantes = @json($viajantes->values());
    const viajantesMapeadosPorId = @json($viajantes->keyBy('pk_id_viajante'));

    // ===================================================================
    // PASSO 2: DEFINIÇÃO DE TODAS AS FUNÇÕES
    // ===================================================================

    const openViajantesDetailsModal = () => {
        if (!viajantesDetailsModal) return;
        viajantesDetailsModal.classList.remove('hidden');
        viajantesDetailsModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            viajantesDetailsModalPanel.classList.remove('scale-95', 'opacity-0');
            viajantesDetailsModalPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    const closeViajantesDetailsModal = () => {
        if (!viajantesDetailsModalPanel) return;
        viajantesDetailsModalPanel.classList.remove('scale-100', 'opacity-100');
        viajantesDetailsModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            viajantesDetailsModal.classList.add('hidden');
            viajantesDetailsModal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    };

    const selectViajante = (viajanteId) => {
        viajanteSidebarItems.forEach(item => {
            item.classList.remove('bg-green-100', 'border-green-300');
            item.classList.add('bg-white', 'border-gray-200');
        });
        viajanteDetailsContents.forEach(content => content.classList.add('hidden'));
        const selectedItem = document.querySelector(`.viajante-sidebar-item[data-viajante-id="${viajanteId}"]`);
        if (selectedItem) {
            selectedItem.classList.remove('bg-white', 'border-gray-200');
            selectedItem.classList.add('bg-green-100', 'border-green-300');
        }
        const selectedContent = document.getElementById(`viajante-details-${viajanteId}`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
    };

    function toggleEditMode(viajanteId, editing) {
        const content = document.getElementById(`viajante-details-${viajanteId}`);
        if (!content) return;
        content.querySelector('.viajante-edit-fields')?.classList.toggle('hidden', !editing);
        content.querySelector('.viajante-nome-display')?.classList.toggle('hidden', editing);
        content.querySelector('.viajante-idade-display')?.classList.toggle('hidden', editing);
        document.querySelector(`.edit-viajante-btn[data-viajante-id="${viajanteId}"]`)?.classList.toggle('hidden', editing);
        document.querySelector(`.save-viajante-btn[data-viajante-id="${viajanteId}"]`)?.classList.toggle('hidden', !editing);
        document.querySelector(`.cancel-viajante-btn[data-viajante-id="${viajanteId}"]`)?.classList.toggle('hidden', !editing);
    }

    function handleIdadeInputChange(content) {
        const idadeInput = content.querySelector('.idade-input');
        const responsavelContainer = content.querySelector('.responsavel-container');
        const responsavelHelp = content.querySelector('.responsavel-help');
        if (!idadeInput || !responsavelContainer) return;
        const val = parseInt(idadeInput.value);
        if (!isNaN(val) && val < 18) {
            responsavelContainer.classList.remove('hidden');
            if (responsavelHelp) responsavelHelp.classList.remove('hidden');
        } else {
            responsavelContainer.classList.add('hidden');
            if (responsavelHelp) responsavelHelp.classList.add('hidden');
        }
    }
    
    // ===================================================================
    // PASSO 3: ANEXAR TODOS OS EVENT LISTENERS
    // ===================================================================

    // Listeners do Modal de Detalhes
    if (openViajantesDetailsModalBtn) openViajantesDetailsModalBtn.addEventListener('click', openViajantesDetailsModal);
    if (closeViajantesDetailsModalBtn) closeViajantesDetailsModalBtn.addEventListener('click', closeViajantesDetailsModal);
    if (viajantesDetailsModalOverlay) viajantesDetailsModalOverlay.addEventListener('click', closeViajantesDetailsModal);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && viajantesDetailsModal && !viajantesDetailsModal.classList.contains('hidden')) {
            closeViajantesDetailsModal();
        }
    });

    // Listener para abrir o Modal de Seguros (usando event delegation)
    if (viajantesDetailsModalPanel) {
        viajantesDetailsModalPanel.addEventListener('click', function(event) {
            const insuranceButton = event.target.closest('.js-open-insurance-modal');
            if (!insuranceButton) return;
            
            event.preventDefault();

            if (typeof window.openInsuranceModal === 'function') {
                const viajanteId = parseInt(insuranceButton.dataset.viajanteId, 10);
                const viajanteEspecifico = viajantesMapeadosPorId[viajanteId];

                if (viajanteEspecifico) {
                    window.openInsuranceModal({
                        viagem: viagemData,
                        viajante: viajanteEspecifico
                    });
                } else {
                    console.error('Viajante com ID ' + viajanteId + ' não encontrado.');
                }
            } else {
                alert('Erro: A função para abrir o modal de seguros (openInsuranceModal) não foi encontrada.');
                console.error('Verifique se o script "insurance-modal.js" está sendo carregado corretamente.');
            }
        });
    }

    // Listeners da Sidebar de Viajantes
    viajanteSidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            selectViajante(this.dataset.viajanteId);
        });
    });

    // Listeners dos botões de adicionar viajante
    function handleAddViajanteClick() {
        closeViajantesDetailsModal();
        setTimeout(() => {
            const addBtn = document.querySelector('[id*="open-add-viajante-modal-btn"]:not([id$="-empty"])');
            if (addBtn) addBtn.click();
        }, 300);
    }
    if (addViajanteFromDetailsBtn) addViajanteFromDetailsBtn.addEventListener('click', handleAddViajanteClick);
    if (addFirstViajanteBtn) addFirstViajanteBtn.addEventListener('click', handleAddViajanteClick);

    // Listener do campo de busca
    if (viajanteDetailsSearchInput) {
        viajanteDetailsSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            viajanteSidebarItems.forEach(item => {
                const isVisible = item.dataset.nome.includes(searchTerm);
                item.style.display = isVisible ? 'block' : 'none';
                if (isVisible) visibleCount++;
            });
            if (searchResultsCountDetails) searchResultsCountDetails.textContent = `${visibleCount} resultado${visibleCount !== 1 ? 's' : ''}`;
            if (noResultsMessageDetails) noResultsMessageDetails.classList.toggle('hidden', !(visibleCount === 0 && searchTerm !== ''));
        });
    }

    // Listener geral para botões de Editar/Salvar/Cancelar
    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.edit-viajante-btn');
        if (editBtn) {
            const id = editBtn.dataset.viajanteId;
            toggleEditMode(id, true);
            const content = document.getElementById(`viajante-details-${id}`);
            if (content) {
                const idadeInput = content.querySelector('.idade-input');
                if (idadeInput) {
                    handleIdadeInputChange(content);
                    idadeInput.addEventListener('input', () => handleIdadeInputChange(content));
                }
            }
            return;
        }

        const cancelBtn = e.target.closest('.cancel-viajante-btn');
        if (cancelBtn) {
            const id = cancelBtn.dataset.viajanteId;
            const content = document.getElementById(`viajante-details-${id}`);
            if (content) {
                const nomeInput = content.querySelector('.nome-input');
                const idadeInput = content.querySelector('.idade-input');
                const nomeField = content.querySelector('.viajante-nome-field');
                const idadeField = content.querySelector('.viajante-idade-field');
                if (nomeInput && nomeField) nomeInput.value = nomeField.textContent.trim();
                if (idadeInput && idadeField) idadeInput.value = parseInt(idadeField.textContent) || idadeInput.value;
            }
            toggleEditMode(id, false);
            return;
        }

        const saveBtn = e.target.closest('.save-viajante-btn');
        if (saveBtn) {
            const id = saveBtn.dataset.viajanteId;
            const content = document.getElementById(`viajante-details-${id}`);
            if (!content) return;

            const nome = content.querySelector('.nome-input').value.trim();
            const idade = parseInt(content.querySelector('.idade-input').value);
            const responsavelId = content.querySelector('.responsavel-select')?.value || null;

            if (!nome || idade === null || isNaN(idade) || idade < 0) {
                alert('Por favor, informe um nome e idade válidos.');
                return;
            }

            const body = { nome, idade };
            if (idade < 18 && responsavelId) {
                body.responsavel_viajante_id = responsavelId;
            }

            fetch(`/viajantes/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? {'X-CSRF-TOKEN': csrfToken} : {})
                },
                body: JSON.stringify(body)
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    // Update UI fields
                    const sidebarItem = document.querySelector(`.viajante-sidebar-item[data-viajante-id="${id}"]`);
                    content.querySelector('.viajante-nome-display').textContent = nome;
                    content.querySelector('.viajante-idade-display').textContent = `${idade} anos`;
                    content.querySelector('.viajante-nome-field').textContent = nome;
                    content.querySelector('.viajante-idade-field').textContent = `${idade} anos`;
                    content.querySelector('.viajante-avatar').textContent = nome.charAt(0).toUpperCase();
                    if(sidebarItem) {
                        sidebarItem.querySelector('h4').textContent = nome;
                        sidebarItem.querySelector('p').textContent = `${idade} anos`;
                        sidebarItem.dataset.nome = nome.toLowerCase();
                    }
                    toggleEditMode(id, false);
                } else {
                    alert(data.message || 'Erro ao atualizar viajante.');
                }
            }).catch(err => {
                console.error('Erro ao salvar viajante:', err);
                alert('Erro de comunicação ao salvar. Verifique o console.');
            });
        }
    });
    
    // Listener para atualização dinâmica do seguro
    window.addEventListener('insuranceUpdated', function(event) {
        const { viajanteId, seguro } = event.detail;
        const viajanteContent = document.getElementById(`viajante-details-${viajanteId}`);
        if (!viajanteContent) return;

        const insuranceSection = viajanteContent.querySelector('.bg-white.rounded-xl.border.border-gray-200:has(.fa-shield-halved)');
        if (!insuranceSection) return;

        const newInsuranceHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-shield-halved text-green-500 mr-2"></i> Seguro Viagem
                </h3>
                <div>
                    <button type="button" data-viajante-id="${viajanteId}" class="js-open-insurance-modal bg-white border border-green-600 hover:bg-green-50 text-green-600 px-3 py-1 rounded text-sm transition-colors">Alterar Seguro</button>
                </div>
            </div>
            <div class="border-l-4 border-green-500 pl-4 py-3 bg-green-50 rounded-r-lg">
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">${seguro.seguradora}</p>
                        <p class="text-sm text-gray-600">${seguro.plano}</p>
                        ${seguro.detalhes_etarios ? `<p class="text-xs text-gray-500 mt-1">${seguro.detalhes_etarios}</p>` : ''}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${seguro.cobertura_medica ? `<div class="flex items-center space-x-2"><i class="fas fa-user-doctor text-green-600 text-sm"></i><span class="text-sm text-gray-700">${seguro.cobertura_medica}</span></div>` : ''}
                        ${seguro.cobertura_bagagem ? `<div class="flex items-center space-x-2"><i class="fas fa-suitcase-rolling text-green-600 text-sm"></i><span class="text-sm text-gray-700">${seguro.cobertura_bagagem}</span></div>` : ''}
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-green-200">
                        <div class="flex space-x-4">
                            ${seguro.preco_pix ? `<div class="text-sm"><span class="text-gray-600">PIX:</span><span class="font-semibold text-green-700">${seguro.preco_pix}</span></div>` : ''}
                            ${seguro.preco_cartao ? `<div class="text-sm"><span class="text-gray-600">Cartão:</span><span class="font-semibold text-gray-700">${seguro.preco_cartao}</span></div>` : ''}
                        </div>
                        ${seguro.link ? `<a href="${seguro.link}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors"><i class="fas fa-external-link-alt mr-1"></i> Ver Plano</a>` : ''}
                    </div>
                </div>
            </div>
        `;
        insuranceSection.innerHTML = newInsuranceHTML;
    });

});
</script>