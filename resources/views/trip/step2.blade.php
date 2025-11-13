<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Detalhes da viagem</h2>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual seu orçamento total?</label>
        <input type="number" class="input" placeholder="R$" name="orcamento" id="orcamento" min="0">
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual será o meio de locomoção?<label class="text-red-600 text-base font-thin">*</label></label>
        <select class="input" id="meio_locomocao" name="meio_locomocao">
            <option value="carro_proprio">Carro (próprio)</option>
            <option value="carro_alugado">Carro (alugado)</option>
            <option value="onibus">Ônibus</option>
            <option value="aviao">Avião</option>
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

    <div id="cars-rent" class="hidden flex gap-6 mb-8">
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual hora deseja retirar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="datetime-local" 
                name="car_pickup_datetime" 
                id="car_pickup_datetime"
                class="input"
                step="1800"
            >
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
    <div id="dep_iata_container" class="hidden flex gap-6 mb-8">
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja decolar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="text" 
                name="dep_iata" 
                id="dep_iata"
                placeholder="ex: Guarulhos"
                class="input airport-autocomplete"
                autocomplete="off"
            >
            <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow"></div>
        </div>
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja pousar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="text" 
                name="arr_iata" 
                id="arr_iata"
                placeholder="ex: John F. Kennedy"
                class="input airport-autocomplete"
                autocomplete="off"
            >
            <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow"></div>
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

    // Função para atualizar visibilidade dos campos
    function updateFieldsVisibility() {
        const selectedValue = meioLocomocaoSelect.value;

        // Esconder todos primeiro
        carroProprioFields.classList.add('hidden');
        carsRent.classList.add('hidden');
        depIataContainer.classList.add('hidden');

        // Mostrar campos relevantes
        switch(selectedValue) {
            case 'carro_proprio':
                carroProprioFields.classList.remove('hidden');
                // Tornar campos obrigatórios
                autonomiaInput.setAttribute('required', 'required');
                tipoCombustivelSelect.setAttribute('required', 'required');
                break;
            case 'carro_alugado':
                carsRent.classList.remove('hidden');
                // Remover required dos campos de carro próprio
                autonomiaInput.removeAttribute('required');
                tipoCombustivelSelect.removeAttribute('required');
                break;
            case 'aviao':
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
    meioLocomocaoSelect.addEventListener('change', updateFieldsVisibility);

    // Inicializar estado correto
    updateFieldsVisibility();

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
});
</script>