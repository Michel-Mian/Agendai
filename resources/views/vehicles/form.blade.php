<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <form id="vehicle-search-form" class="space-y-6" data-no-loader="true">
        @csrf
        
        <!-- Linha 1: Local e Datas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Local de Retirada -->
            <div class="relative">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Local de Retirada
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="local_retirada" 
                        name="local_retirada"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Digite a cidade de retirada..."
                        data-places-autocomplete="true"
                        data-valid="false"
                        autocomplete="off"
                        required
                    >
                    <!-- Container para sugestões do autocomplete -->
                </div>
            </div>
            
            <!-- Data de Retirada -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>
                    Data de Retirada
                </label>
                <input 
                    type="date" 
                    id="data_retirada" 
                    name="data_retirada"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    min="{{ date('Y-m-d') }}"
                    required
                >
            </div>
            
            <!-- Data de Devolução -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                    Data de Devolução
                </label>
                <input 
                    type="date" 
                    id="data_devolucao" 
                    name="data_devolucao"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    min="{{ date('Y-m-d') }}"
                    required
                >
            </div>
        </div>
        
        <!-- Linha 2: Horas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Hora de Retirada -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Hora de Retirada
                </label>
                <select 
                    id="hora_retirada" 
                    name="hora_retirada"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    required
                >
                    @for($h = 0; $h < 24; $h++)
                        @foreach(['00', '30'] as $m)
                            <option value="{{ sprintf('%02d:%s', $h, $m) }}">
                                {{ sprintf('%02d:%s', $h, $m) }}
                            </option>
                        @endforeach
                    @endfor
                </select>
            </div>
            
            <!-- Hora de Devolução -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Hora de Devolução
                </label>
                <select 
                    id="hora_devolucao" 
                    name="hora_devolucao"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    required
                >
                    @for($h = 0; $h < 24; $h++)
                        @foreach(['00', '30'] as $m)
                            <option value="{{ sprintf('%02d:%s', $h, $m) }}">
                                {{ sprintf('%02d:%s', $h, $m) }}
                            </option>
                        @endforeach
                    @endfor
                </select>
            </div>
        </div>
        
        <!-- Botão de Busca -->
        <div class="flex justify-end">
            <button 
                type="submit" 
                id="btn-search-vehicles"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-md"
            >
                <i class="fas fa-search mr-2"></i>
                Buscar Veículos
            </button>
        </div>
    </form>
</div>
