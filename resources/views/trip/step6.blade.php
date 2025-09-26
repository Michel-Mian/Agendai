<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Revisão final</h2>
    
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl p-6 text-white mb-6">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fas fa-clipboard-check"></i>
            Confira seus dados:
        </h3>
        <div class="bg-white/10 rounded-lg p-4">
            <ul class="space-y-3 text-base" id="reviewList">
                <!-- Os dados preenchidos aparecerão aqui via JS -->
                <li class="text-blue-100 italic">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i>
                    Carregando informações da viagem...
                </li>
            </ul>
        </div>
    </div>

    <!-- Seção de informações importantes -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle text-amber-600"></i>
            <h4 class="text-amber-800 font-semibold">Informações importantes:</h4>
        </div>
        <ul class="text-amber-700 text-sm space-y-1">
            <li>• Verifique se todas as informações estão corretas antes de finalizar</li>
            <li>• As datas e destinos não poderão ser alterados após a criação da viagem</li>
            <li>• Certifique-se de que as preferências refletem seus interesses</li>
        </ul>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-times-circle text-red-600"></i>
                <h4 class="text-red-800 font-semibold">Erros encontrados:</h4>
            </div>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </button>
        <button type="submit" class="btn-primary flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            Finalizar Criação
        </button>
    </div>
</div>