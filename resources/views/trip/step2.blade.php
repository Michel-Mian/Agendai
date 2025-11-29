<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Detalhes da viagem</h2>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual seu orçamento total?</label>
        <input type="number" class="input" placeholder="R$" name="orcamento" id="orcamento" min="0">
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual será o meio de locomoção?<label class="text-red-600 text-base font-thin">*</label></label>
        <select class="input" id="meio_locomocao" name="meio_locomocao">
            <option value="Carro (próprio)">Carro (próprio)</option>
            <option value="Carro (alugado)">Carro (alugado)</option>
            <option value="Ônibus">Ônibus</option>
            <option value="Avião">Avião</option>
        </select>
    </div>

    <!-- Campos para Carro Próprio -->
    <div id="carro-proprio-fields" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center gap-2">
            <i class="fas fa-car"></i>
            Informações do seu veículo
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-600 font-semibold mb-2">
                    Autonomia do veículo (km/litro)<label class="text-red-600 text-base font-thin">*</label>
                </label>
                <input 
                    type="number" 
                    name="autonomia_veiculo" 
                    id="autonomia_veiculo"
                    class="input" 
                    placeholder="Ex: 12.5"
                    step="0.1"
                    min="1"
                    max="50"
                >
                <p class="text-sm text-gray-500 mt-1">Quantos km seu carro faz por litro?</p>
            </div>

            <div>
                <label class="block text-gray-600 font-semibold mb-2">
                    Tipo de combustível<label class="text-red-600 text-base font-thin">*</label>
                </label>
                <select name="tipo_combustivel" id="tipo_combustivel" class="input">
                    <option value="gasolina">Gasolina</option>
                    <option value="etanol">Etanol</option>
                    <option value="diesel">Diesel</option>
                    <option value="gnv">GNV</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-600 font-semibold mb-2">
                    Preço do combustível por litro (opcional)
                </label>
                <input 
                    type="number" 
                    name="preco_combustivel" 
                    id="preco_combustivel"
                    class="input" 
                    placeholder="Ex: 5.89"
                    step="0.01"
                    min="0"
                >
                <p class="text-sm text-gray-500 mt-1">Deixe em branco para usar o preço médio nacional</p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-blue-100 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Como funciona?</p>
                    <p>Calcularemos automaticamente a distância total da viagem, estimativa de pedágios, quantidade de litros necessários e custo total de combustível baseado nas informações fornecidas.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Campos para Aluguel de Carro -->
    <div id="cars-rent" class="hidden mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
        <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center gap-2">
            <i class="fas fa-car-side"></i>
            Informações do aluguel
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-600 font-semibold mb-2">Qual hora deseja retirar?<label class="text-red-600 text-base font-thin">*</label></label>
                <input 
                    type="datetime-local" 
                    name="car_pickup_datetime" 
                    id="car_pickup_datetime"
                    class="input"
                    step="1800"
                >
            </div>
            <div>
                <label class="block text-gray-600 font-semibold mb-2">Qual hora deseja devolver?<label class="text-red-600 text-base font-thin">*</label></label>
                <input 
                    type="datetime-local" 
                    name="car_return_datetime" 
                    id="car_return_datetime"
                    class="input"
                    step="1800"
                >
            </div>
        </div>
    </div>
    <!-- Campos para Avião -->
    <div id="dep_iata_container" class="hidden mb-6 p-4 bg-sky-50 border border-sky-200 rounded-lg">
        <h3 class="text-lg font-semibold text-sky-800 mb-4 flex items-center gap-2">
            <i class="fas fa-plane"></i>
            Informações do voo
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
                <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja decolar?<label class="text-red-600 text-base font-thin">*</label></label>
                <input 
                    type="text" 
                    name="dep_iata" 
                    id="dep_iata"
                    placeholder="ex: Guarulhos"
                    class="input airport-autocomplete"
                    autocomplete="off"
                >
                <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow z-10"></div>
            </div>
            <div class="relative">
                <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja pousar?<label class="text-red-600 text-base font-thin">*</label></label>
                <input 
                    type="text" 
                    name="arr_iata" 
                    id="arr_iata"
                    placeholder="ex: John F. Kennedy"
                    class="input airport-autocomplete"
                    autocomplete="off"
                >
                <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow z-10"></div>
            </div>
        </div>
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Deseja contratar um seguro?<label class="text-red-600 text-base font-thin">*</label></label>
        <select class="input" id="seguroViagem" name="seguroViagem">
            <option value="Não">Não</option>
            <option value="Sim">Sim</option>
        </select>
    </div>
    <div class="hidden flex gap-6 mb-8" id="insurance-options"> 
        <div class="mb-8 relative">
            <label for="destino" class="block text-gray-600 font-semibold mb-2">Destino:<label class="text-red-600 text-base font-thin">*</label></label>
            <select name="destino" id="MainContent_Cotador_selContinente" class="input">
                <option value="">Selecione o destino</option>
                <option value="5" {{ old('destino') == '5' ? 'selected' : '' }}>África</option>
                <option value="14" {{ old('destino') == '14' ? 'selected' : '' }}>América Central</option>
                <option value="1" {{ old('destino') == '1' ? 'selected' : '' }}>América do Norte</option>
                <option value="4" {{ old('destino') == '4' ? 'selected' : '' }}>América do Sul</option>
                <option value="12" {{ old('destino') == '12' ? 'selected' : '' }}>Argentina</option>
                <option value="6" {{ old('destino') == '6' ? 'selected' : '' }}>Ásia</option>
                <option value="2" {{ old('destino') == '2' ? 'selected' : '' }}>Europa</option>
                <option value="13" {{ old('destino') == '13' ? 'selected' : '' }}>Internacional</option>
                <option value="7" {{ old('destino') == '7' ? 'selected' : '' }}>Oceania</option>
                <option value="11" {{ old('destino') == '11' ? 'selected' : '' }}>Oriente Médio</option>
            </select>
        </div>
    </div>
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const meioLocomocaoSelect = document.getElementById('meio_locomocao');
    const carroProprioFields = document.getElementById('carro-proprio-fields');
    const carsRent = document.getElementById('cars-rent');
    const depIataContainer = document.getElementById('dep_iata_container');
    const autonomiaInput = document.getElementById('autonomia_veiculo');
    const tipoCombustivelSelect = document.getElementById('tipo_combustivel');
    const precoCombustivelInput = document.getElementById('preco_combustivel');

    // Função para obter a primeira data de início e a última data de fim dos destinos
    function getViageDates() {
        const dataInicioInputs = document.querySelectorAll('.destino-data-inicio');
        const dataFimInputs = document.querySelectorAll('.destino-data-fim');
        
        let primeiraDataInicio = null;
        let ultimaDataFim = null;
        
        // Pegar a primeira data de início (do primeiro destino)
        if (dataInicioInputs.length > 0 && dataInicioInputs[0].value) {
            primeiraDataInicio = dataInicioInputs[0].value;
        }
        
        // Pegar a última data de fim (do último destino com data preenchida)
        for (let i = dataFimInputs.length - 1; i >= 0; i--) {
            if (dataFimInputs[i].value) {
                ultimaDataFim = dataFimInputs[i].value;
                break;
            }
        }
        
        return { primeiraDataInicio, ultimaDataFim };
    }
    
    // Função para preencher as datas do aluguel
    function preencherDatasAluguel() {
        const { primeiraDataInicio, ultimaDataFim } = getViageDates();
        
        const carPickupInput = document.getElementById('car_pickup_datetime');
        const carReturnInput = document.getElementById('car_return_datetime');
        
        if (carPickupInput && primeiraDataInicio) {
            // Definir data de retirada como 10:00 do primeiro dia
            carPickupInput.value = `${primeiraDataInicio}T10:00`;
            // Definir data mínima como a primeira data de início
            carPickupInput.setAttribute('min', `${primeiraDataInicio}T00:00`);
        }
        
        if (carReturnInput && ultimaDataFim) {
            // Definir data de devolução como 18:00 do último dia
            carReturnInput.value = `${ultimaDataFim}T18:00`;
            // Definir data máxima como a última data de fim
            carReturnInput.setAttribute('max', `${ultimaDataFim}T23:59`);
        }
        
        // Configurar validação entre os campos
        if (carPickupInput && carReturnInput) {
            // A data de retirada não pode ser depois da última data da viagem
            if (ultimaDataFim) {
                carPickupInput.setAttribute('max', `${ultimaDataFim}T23:59`);
            }
            
            // A data de devolução não pode ser antes da primeira data da viagem
            if (primeiraDataInicio) {
                carReturnInput.setAttribute('min', `${primeiraDataInicio}T00:00`);
            }
            
            // Quando mudar a data de retirada, atualizar o mínimo da devolução
            carPickupInput.addEventListener('change', function() {
                if (this.value) {
                    carReturnInput.setAttribute('min', this.value);
                    // Se a devolução for antes da retirada, limpar
                    if (carReturnInput.value && carReturnInput.value < this.value) {
                        carReturnInput.value = '';
                    }
                }
            });
            
            // Quando mudar a data de devolução, atualizar o máximo da retirada
            carReturnInput.addEventListener('change', function() {
                if (this.value) {
                    carPickupInput.setAttribute('max', this.value);
                    // Se a retirada for depois da devolução, limpar
                    if (carPickupInput.value && carPickupInput.value > this.value) {
                        carPickupInput.value = '';
                    }
                }
            });
        }
    }

    // Função para atualizar visibilidade dos campos
    function updateFieldsVisibility() {
        const selectedValue = meioLocomocaoSelect.value;

        // Esconder todos primeiro
        carroProprioFields.classList.add('hidden');
        carsRent.classList.add('hidden');
        depIataContainer.classList.add('hidden');

        // Mostrar campos relevantes
        switch(selectedValue) {
            case 'Carro (próprio)':
                carroProprioFields.classList.remove('hidden');
                // Tornar campos obrigatórios
                autonomiaInput.setAttribute('required', 'required');
                tipoCombustivelSelect.setAttribute('required', 'required');
                break;
            case 'Carro (alugado)':
                carsRent.classList.remove('hidden');
                // Preencher datas automaticamente
                preencherDatasAluguel();
                // Remover required dos campos de carro próprio
                autonomiaInput.removeAttribute('required');
                tipoCombustivelSelect.removeAttribute('required');
                break;
            case 'Avião':
                depIataContainer.classList.remove('hidden');
                autonomiaInput.removeAttribute('required');
                tipoCombustivelSelect.removeAttribute('required');
                break;
            default:
                autonomiaInput.removeAttribute('required');
                tipoCombustivelSelect.removeAttribute('required');
                break;
        }
    }

    // Atualizar quando mudar seleção
    meioLocomocaoSelect.addEventListener('change', function() {
        updateFieldsVisibility();
        
        // Atualizar indicadores de progresso
        if (typeof window.updateProgressIndicators === 'function') {
            window.updateProgressIndicators();
        }
    });

    // Inicializar estado correto
    updateFieldsVisibility();
    
    // Preencher datas se já estiverem disponíveis e meio for Carro (alugado)
    if (meioLocomocaoSelect.value === 'Carro (alugado)') {
        setTimeout(() => preencherDatasAluguel(), 200);
    }
    
    // Inicializar indicadores de progresso
    if (typeof window.updateProgressIndicators === 'function') {
        setTimeout(() => window.updateProgressIndicators(), 100);
    }

    // Atualizar preço sugerido baseado no tipo de combustível
    tipoCombustivelSelect.addEventListener('change', function() {
        if (!precoCombustivelInput.value) {
            const precosSugeridos = {
                'gasolina': '5.89',
                'etanol': '4.29',
                'diesel': '5.99',
                'gnv': '4.50'
            };
            precoCombustivelInput.placeholder = `Preço médio: R$ ${precosSugeridos[this.value]}`;
        }
    });

    // Inicializar placeholder do preço
    if (tipoCombustivelSelect.value) {
        tipoCombustivelSelect.dispatchEvent(new Event('change'));
    }
    
    // Observar mudanças nas datas dos destinos para atualizar datas de aluguel
    const observarDatasDestinos = () => {
        const dataInicioInputs = document.querySelectorAll('.destino-data-inicio');
        const dataFimInputs = document.querySelectorAll('.destino-data-fim');
        
        dataInicioInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (meioLocomocaoSelect.value === 'Carro (alugado)') {
                    preencherDatasAluguel();
                }
            });
        });
        
        dataFimInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (meioLocomocaoSelect.value === 'Carro (alugado)') {
                    preencherDatasAluguel();
                }
            });
        });
    };
    
    // Observar mudanças iniciais
    observarDatasDestinos();
    
    // Re-observar quando novos destinos forem adicionados (usar MutationObserver)
    const destinosContainer = document.getElementById('destinos-container');
    if (destinosContainer) {
        const observer = new MutationObserver(function(mutations) {
            observarDatasDestinos();
        });
        
        observer.observe(destinosContainer, {
            childList: true,
            subtree: true
        });
    }
});
</script>