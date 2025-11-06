<div id="vehicle-trip-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-suitcase text-blue-600 mr-2"></i>
                    Selecionar Viagem
                </h3>
                <button onclick="closeVehicleTripModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-4">
                Escolha a viagem onde deseja adicionar este veículo:
            </p>
            
            <div class="mb-6">
                <label for="trip-select" class="block text-sm font-medium text-gray-700 mb-2">
                    Viagem
                </label>
                <select 
                    id="trip-select" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Carregando...</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button 
                    onclick="closeVehicleTripModal()" 
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-lg transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    onclick="confirmVehicleSelection()" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
