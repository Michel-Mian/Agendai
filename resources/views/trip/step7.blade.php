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

    <!-- Resumo do veículo selecionado -->
    <div class="bg-white rounded-xl shadow p-4 mb-6" id="selectedCarReviewContainer">
        <h4 class="text-lg font-semibold mb-3">Resumo do veículo selecionado</h4>
        <div id="selectedCarReview" class="text-sm text-gray-700">
            <p class="italic text-gray-500">Nenhum veículo selecionado.</p>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Observações sobre o veículo (opcional)</label>
            <textarea name="veiculo_observacoes" id="veiculo_observacoes" class="input mt-2" rows="3" placeholder="Observações que deseja salvar junto com o veículo..."></textarea>
        </div>
    </div>

    <!-- Cálculos de viagem com carro próprio -->
    <div class="bg-white rounded-xl shadow p-6 mb-6 hidden" id="carroProprioCalculos">
        <h4 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-700">
            <i class="fas fa-calculator"></i>
            Estimativa de Custos - Carro Próprio
        </h4>

        <!-- Mapa da Rota -->
        <div class="mb-6 rounded-lg overflow-hidden border-2 border-gray-200 shadow-lg">
            <div id="routeMap" style="height: 450px; width: 100%;"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Distância Total -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Distância Total</span>
                    <i class="fas fa-route text-blue-600"></i>
                </div>
                <p class="text-2xl font-bold text-blue-700" id="distancia_total_display">
                    <i class="fas fa-spinner fa-spin"></i> Calculando...
                </p>
                <p class="text-xs text-gray-500 mt-1" id="duracao_display"></p>
            </div>

            <!-- Combustível Necessário -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Combustível Necessário</span>
                    <i class="fas fa-gas-pump text-green-600"></i>
                </div>
                <p class="text-2xl font-bold text-green-700" id="combustivel_litros_display">
                    <i class="fas fa-spinner fa-spin"></i> Calculando...
                </p>
            </div>

            <!-- Custo de Combustível -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Custo de Combustível</span>
                    <i class="fas fa-money-bill-wave text-purple-600"></i>
                </div>
                <p class="text-2xl font-bold text-purple-700" id="custo_combustivel_display">
                    <i class="fas fa-spinner fa-spin"></i> Calculando...
                </p>
            </div>

            <!-- Pedágios Estimados -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Pedágios Estimados</span>
                    <i class="fas fa-road text-orange-600"></i>
                </div>
                <p class="text-2xl font-bold text-orange-700" id="pedagio_display">
                    <i class="fas fa-spinner fa-spin"></i> Calculando...
                </p>
            </div>
        </div>

        <!-- Custo Total -->
        <div class="mt-4 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Custo Total Estimado (Combustível + Pedágios)</p>
                    <p class="text-3xl font-bold mt-1" id="custo_total_display">
                        <i class="fas fa-spinner fa-spin"></i> Calculando...
                    </p>
                </div>
                <i class="fas fa-wallet text-4xl opacity-20"></i>
            </div>
        </div>

        <!-- Informações adicionais -->
        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold mb-1">Sobre os cálculos:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>Distância e rota calculadas via Google Maps Routes API</li>
                        <li>Pedágios: valores oficiais quando disponíveis na API, caso contrário estimados</li>
                        <li>Consumo baseado na autonomia informada do seu veículo</li>
                        <li>Valores são estimativas e podem variar conforme condições reais</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Campos hidden para enviar os dados -->
        <input type="hidden" name="distancia_total_km" id="distancia_total_km" value="">
        <input type="hidden" name="combustivel_litros" id="combustivel_litros" value="">
        <input type="hidden" name="custo_combustivel" id="custo_combustivel" value="">
        <input type="hidden" name="pedagio_estimado" id="pedagio_estimado" value="">
        <input type="hidden" name="pedagio_oficial" id="pedagio_oficial" value="0">
        <input type="hidden" name="duracao_segundos" id="duracao_segundos" value="">
        <input type="hidden" name="rota_detalhada" id="rota_detalhada" value="">
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