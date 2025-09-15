<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
    {{-- CABEÇALHO: Permanece o mesmo --}}
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-shield-halved text-gray-400 text-2xl"></i>
                <h2 class="text-lg font-semibold text-gray-800">Seguro Viagem</h2>
            </div>
            <button type="button" id="open-add-insurance-modal-btn" class="ml-auto bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" onclick="alert('Funcionalidade em desenvolvimento!')">
                {{-- Botão permanece o mesmo --}}
                <i class="fas fa-plus mr-2"></i>Adicionar
            </button>
        </div>
    </div>
    
    <div class="p-6">
        @php
            $seguroSelecionado = null;
            if (isset($seguros) && count($seguros)) {
                $seguroSelecionado = $seguros->where('is_selected', true)->last();
            }
        @endphp

        @if($seguroSelecionado)
            {{-- SEGURO SELECIONADO: Com os novos detalhes --}}
            <div id="selected-insurance-session">
                <div class="border-l-4 border-green-500 pl-4 py-2 flex items-center justify-between transition-all duration-300 ease-in-out hover:shadow-md hover:bg-gray-50">
                    {{-- Coluna de Informações --}}
                    <div class="flex-grow space-y-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $seguroSelecionado->seguradora }}</p>
                            <p class="text-sm text-gray-500">{{ $seguroSelecionado->plano }}</p>
                            {{-- NOVO: Detalhes Etários --}}
                            @if($seguroSelecionado->detalhes_etarios)
                                <p class="text-xs text-gray-400 mt-1">{{ $seguroSelecionado->detalhes_etarios }}</p>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-4 text-xs text-gray-600">
                            @if($seguroSelecionado->cobertura_medica)
                                <div class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-user-doctor w-4 text-center text-gray-400"></i>
                                    <span>{{ $seguroSelecionado->cobertura_medica }}</span>
                                </div>
                            @endif
                            @if($seguroSelecionado->cobertura_bagagem)
                                <div class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-suitcase-rolling w-4 text-center text-gray-400"></i>
                                    <span>{{ $seguroSelecionado->cobertura_bagagem }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            @if($seguroSelecionado->preco_cartao)
                                <p class="text-base font-semibold text-gray-900">
                                    R$ {{ number_format($seguroSelecionado->preco_cartao, 2, ',', '.') }}
                                    {{-- NOVO: Parcelamento --}}
                                    @if($seguroSelecionado->parcelamento_cartao)
                                        <span class="text-xs font-normal text-gray-500 ml-1">({{ $seguroSelecionado->parcelamento_cartao }})</span>
                                    @endif
                                </p>
                            @endif
                            {{-- NOVO: Preço PIX --}}
                            @if($seguroSelecionado->preco_pix)
                                <p class="text-sm font-medium text-green-600">
                                    ou R$ {{ number_format($seguroSelecionado->preco_pix, 2, ',', '.') }} no PIX
                                </p>
                            @endif
                        </div>

                        {{-- NOVO: Link --}}
                        @if($seguroSelecionado->link)
                            <a href="{{ $seguroSelecionado->link }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                Ver detalhes no site
                            </a>
                        @endif
                    </div>

                    {{-- Coluna de Ações --}}
                    <div class="flex-shrink-0 ml-4 self-start">
                        <button type="button" class="text-sm font-medium text-blue-600 hover:text-blue-500" onclick="alert('Funcionalidade em desenvolvimento!')">
                            Trocar →
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- ESTADO VAZIO: Permanece o mesmo --}}
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-halved text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhum seguro adicionado</h3>
                <p class="text-gray-500 mb-6 max-w-xs mx-auto">Adicione um seguro para manter os detalhes importantes da sua viagem organizados.</p>
                <button type="button" id="open-add-insurance-modal-btn-empty" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg transition-colors font-semibold" onclick="openAddInsuranceModal()">
                    Adicionar seguro
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (lógica do botão de estado vazio) ...
        const addInsuranceEmptyBtn = document.getElementById('open-add-insurance-modal-btn-empty');
        if (addInsuranceEmptyBtn) {
            addInsuranceEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-insurance-modal-btn').click();
            });
        }

        // TEMPLATE JAVASCRIPT: Atualizado para incluir as novas informações
        window.addEventListener('insuranceChanged', function(e) {
            const seguro = e.detail && e.detail.seguro;
            if (!seguro) return;
            
            const precoCartaoFmt = seguro.preco_cartao 
                ? parseFloat(seguro.preco_cartao).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '';
            const precoPixFmt = seguro.preco_pix
                ? parseFloat(seguro.preco_pix).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '';

            const html = `
                <div class="border-l-4 border-green-500 pl-4 py-2 flex items-center justify-between">
                    <div class="flex-grow space-y-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${seguro.seguradora}</p>
                            <p class="text-sm text-gray-500">${seguro.plano}</p>
                            ${seguro.detalhes_etarios ? `<p class="text-xs text-gray-400 mt-1">${seguro.detalhes_etarios}</p>` : ''}
                        </div>
                        <div class="flex items-center space-x-4 text-xs text-gray-600">
                            ${seguro.cobertura_medica ? `
                                <div class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-user-doctor w-4 text-center text-gray-400"></i>
                                    <span>${seguro.cobertura_medica}</span>
                                </div>` : ''}
                            ${seguro.cobertura_bagagem ? `
                                <div class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-suitcase-rolling w-4 text-center text-gray-400"></i>
                                    <span>${seguro.cobertura_bagagem}</span>
                                </div>` : ''}
                        </div>
                        <div>
                            ${precoCartaoFmt ? `
                                <p class="text-base font-semibold text-gray-900">
                                    R$ ${precoCartaoFmt}
                                    ${seguro.parcelamento_cartao ? `<span class="text-xs font-normal text-gray-500 ml-1">(${seguro.parcelamento_cartao})</span>` : ''}
                                </p>` : ''}
                            ${precoPixFmt ? `
                                <p class="text-sm font-medium text-green-600">
                                    ou R$ ${precoPixFmt} no PIX
                                </p>` : ''}
                        </div>
                        ${seguro.link ? `
                            <a href="${seguro.link}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                Ver detalhes no site
                            </a>` : ''}
                    </div>
                    <div class="flex-shrink-0 ml-4 self-start">
                        <button type="button" class="text-sm font-medium text-blue-600 hover:text-blue-500" onclick="window.openInsuranceModal()">
                            Trocar →
                        </button>
                    </div>
                </div>
            `;
            
            const container = document.getElementById('selected-insurance-session');
            if (container) {
                container.innerHTML = html;
            } else {
                const parent = document.querySelector('.p-6');
                const emptyState = parent.querySelector('.text-center.py-12');
                if(emptyState) {
                    emptyState.outerHTML = `<div id="selected-insurance-session">${html}</div>`;
                }
            }
        });
    });
    
    window.openInsuranceModal = function() {
        const modal = document.getElementById('insurance-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            window.dispatchEvent(new Event('openInsuranceModal'));
        }
    }
</script>