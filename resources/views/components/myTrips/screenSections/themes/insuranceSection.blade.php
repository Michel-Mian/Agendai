<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green-800">Seguro Viagem</h2>
                    <p class="text-green-600 text-sm">{{ isset($seguros) ? count($seguros) : 0 }} {{ (isset($seguros) && count($seguros) == 1) ? 'seguro' : 'seguros' }}</p>
                </div>
            </div>
            <button type="button" id="open-add-insurance-modal-btn" class="bg-white hover:bg-green-100 cursor-pointer text-green-500 border-2 border-green-400 p-2 rounded-lg transition-colors" title="Adicionar seguro">
                <i class="fas fa-plus text-lg"></i> Adicionar Seguro
            </button>
        </div>
    </div>
    <div class="p-6">
        @if(isset($seguros) && count($seguros))
            <div class="space-y-3">
                @foreach($seguros as $seguro)
                    <div class="group flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition-all duration-200">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $seguro->nome }}</div>
                            <div class="text-sm text-gray-600">{{ $seguro->detalhes }}</div>
                        </div>
                        <form action="#" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            <button type="button" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors" title="Remover seguro">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 mb-4">Nenhum seguro cadastrado</p>
                <button type="button" id="open-add-insurance-modal-btn-empty" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Adicionar primeiro seguro
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
        // Add insurance modal open logic for main button
        const addInsuranceBtn = document.getElementById('open-add-insurance-modal-btn');
        if (addInsuranceBtn) {
            addInsuranceBtn.addEventListener('click', function() {
                const modal = document.getElementById('add-insurance-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        const panel = document.getElementById('add-insurance-modal-panel');
                        if (panel) {
                            panel.classList.remove('scale-95', 'opacity-0');
                            panel.classList.add('scale-100', 'opacity-100');
                            const input = document.getElementById('nome_seguro');
                            if (input) input.focus();
                        }
                    }, 10);
                }
            });
        }
    });
</script>
