<div id="vehicle-trip-modal" class="hidden fixed inset-0 backdrop-blur-md z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <button id="close-vehicle-trip-modal-btn" type="button" aria-label="Fechar" onclick="closeVehicleTripModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times text-xl"></i>
        </button>
        <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-car-side text-blue-600 text-xl"></i>
                <h3 class="text-2xl font-bold text-gray-900">Selecionar Viagem</h3>
            </div>
            <p id="vehicle-trip-modal-hint" class="text-sm text-gray-600 mb-5">
                Selecione uma viagem cujo período cubra as datas da sua locação (retirada até devolução).
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
                <p id="vehicle-trip-empty" class="hidden mt-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md p-2">
                    Nenhuma viagem cobre o período selecionado. Ajuste as datas ou crie uma nova viagem.
                </p>
            </div>

            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="closeVehicleTripModal()" 
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="button"
                    onclick="confirmVehicleSelection()" 
                    id="confirm-vehicle-trip-btn"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
