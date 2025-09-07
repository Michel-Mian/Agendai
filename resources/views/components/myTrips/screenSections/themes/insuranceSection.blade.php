@php
    // Garantir que $seguros sempre existe
    $seguros = $seguros ?? collect();
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mt-8">
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green-800">Seguro Viagem</h2>
                    <p class="text-green-600 text-sm">{{ $seguros->count() }} {{ $seguros->count() == 1 ? 'seguro' : 'seguros' }} cadastrados</p>
                </div>
            </div>
            <button type="button" id="open-add-insurance-modal-btn" class="bg-white/20 hover:bg-white/30 text-green-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Adicionar seguro
            </button>
        </div>
    </div>
    <div class="p-6">
        @if($seguros->count() > 0)
            @php
                $seguroSelecionado = $seguros->where('is_selected', true)->last();
            @endphp
            @if($seguroSelecionado)
                <div class="mb-6">
                    <div class="bg-green-100 border border-green-300 rounded-lg p-4 flex items-center space-x-4">
                        <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                        <div>
                            <div class="font-bold text-green-800">{{ $seguroSelecionado->site ?? 'Seguro' }}</div>
                            <div class="text-sm text-gray-700">
                                @php
                                    $dados = $seguroSelecionado->dados;
                                    if (is_string($dados)) {
                                        try { $dados = json_decode($dados, true); } catch (\Exception $e) {}
                                    }
                                @endphp
                                {!! is_array($dados) ? implode('<br>', $dados) : ($dados ?? '') !!}
                            </div>
                        </div>
                        <button type="button" class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg" onclick="window.openInsuranceModal()">
                            Trocar seguro
                        </button>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum seguro cadastrado</h3>
                <p class="text-gray-500 mb-6">Adicione informações sobre seus seguros para manter tudo organizado</p>
                <button type="button" id="open-add-insurance-modal-btn-empty" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Adicionar primeiro seguro
                </button>
            </div>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addInsuranceEmptyBtn = document.getElementById('open-add-insurance-modal-btn-empty');
        if (addInsuranceEmptyBtn) {
            addInsuranceEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-insurance-modal-btn').click();
            });
        }
    });
</script>
