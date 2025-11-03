<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <span id="total-vehicles">0</span> veículos encontrados
        </h2>
        <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Role para ver mais opções
        </div>
    </div>
    
    <!-- Grid de Veículos -->
    <div id="vehicles-grid" class="grid grid-cols-1 gap-6">
        <!-- Cards serão inseridos aqui via JavaScript -->
    </div>
    
    <!-- Mensagem de sem resultados -->
    <div id="no-vehicles-message" class="hidden text-center py-12">
        <i class="fas fa-car text-gray-300 text-6xl mb-4"></i>
        <p class="text-xl text-gray-600">Nenhum veículo encontrado para os critérios selecionados.</p>
    </div>
</div>
