<div class="form-step active">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Informações iniciais</h2>
    
    <!-- Nome da viagem -->
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Nome da sua viagem<label class="text-red-600 text-base font-thin">*</label></label>
        <input type="text" 
               id="nome_viagem" 
               name="nome_viagem" 
               class="input" 
               placeholder="Ex: Eurotrip 2025, Lua de mel em Paris, Aventura na Ásia..."
               maxlength="100"
               required>
        <p class="text-sm text-gray-500 mt-1">Dê um nome especial para a sua viagem dos sonhos</p>
    </div>
    
    <!-- Container de destinos -->
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Quais seus destinos?<label class="text-red-600 text-base font-thin">*</label></label>
        <div id="destinos-container">
            <!-- Primeiro destino (sempre presente) -->
            <div class="destino-item mb-6 p-4 border border-gray-200 rounded-lg" data-destino-index="0">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Destino 1<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="text" 
                               id="tripDestination_0" 
                               name="destinos[]" 
                               class="input destino-input" 
                               placeholder="Digite o destino dos sonhos..."
                               data-index="0"
                               data-new-autocomplete="true">
                    </div>
                    <button type="button" 
                            class="remove-destino-btn hidden bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors"
                            data-index="0">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Data de início<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="date" 
                               class="input destino-data-inicio" 
                               name="destino_data_inicio[]" 
                               id="destino_data_inicio_0"
                               data-destino-index="0"
                               min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Data de fim<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="date" 
                               class="input destino-data-fim" 
                               name="destino_data_fim[]" 
                               id="destino_data_fim_0"
                               data-destino-index="0"
                               min="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botão para adicionar mais destinos -->
        <button type="button" 
                id="add-destino-btn" 
                class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition-colors">
            <i class="fas fa-plus-circle"></i>
            Adicionar mais um destino
        </button>
    </div>

    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual sua origem?<label class="text-red-600 text-base font-thin">*</label></label>
        <div class="relative">
            <input type="text" 
                   id="origem" 
                   name="origem" 
                   class="input origem-input" 
                   placeholder="Digite de onde você sairá..."
                   data-new-autocomplete="true">
        </div>
    </div>
    <div class="flex gap-6 mb-6">
        <div class="flex-1">
            <label class="block text-gray-600 font-semibold mb-2">Nº de pessoas:<label class="text-red-600 text-base font-thin">*</label></label>
            <select class="input" name="num_pessoas" id="num_pessoas">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
            </select>
        </div>
    </div>
    <div id="idades-container" class="flex gap-4 mb-6"></div>
    <div class="flex justify-end">
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let destinoCounter = 0;
    const maxDestinos = 10; // Limite máximo de destinos
    
    // Elementos principais
    const destinosContainer = document.getElementById('destinos-container');
    const addDestinoBtn = document.getElementById('add-destino-btn');
    
    // Event listener para adicionar novo destino
    addDestinoBtn.addEventListener('click', function() {
        addNovoDestino();
    });
    
    // Event delegation para botões de remover
    destinosContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-destino-btn')) {
            const index = e.target.closest('.remove-destino-btn').dataset.index;
            removeDestino(index);
        }
    });
    
    function addNovoDestino() {
        if (destinoCounter >= maxDestinos - 1) {
            alert(`Você pode adicionar no máximo ${maxDestinos} destinos.`);
            return;
        }
        
        destinoCounter++;
        
        const novoDestinoHTML = `
            <div class="destino-item mb-6 p-4 border border-gray-200 rounded-lg animate-fade-in" data-destino-index="${destinoCounter}">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Destino ${destinoCounter + 1}<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="text" 
                               id="tripDestination_${destinoCounter}" 
                               name="destinos[]" 
                               class="input destino-input" 
                               placeholder="Digite outro destino..."
                               data-index="${destinoCounter}"
                               data-new-autocomplete="true">
                    </div>
                    <button type="button" 
                            class="remove-destino-btn bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors"
                            data-index="${destinoCounter}">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Data de início<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="date" 
                               class="input destino-data-inicio" 
                               name="destino_data_inicio[]" 
                               id="destino_data_inicio_${destinoCounter}"
                               data-destino-index="${destinoCounter}">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-600 font-semibold mb-2">Data de fim<label class="text-red-600 text-base font-thin">*</label></label>
                        <input type="date" 
                               class="input destino-data-fim" 
                               name="destino_data_fim[]" 
                               id="destino_data_fim_${destinoCounter}"
                               data-destino-index="${destinoCounter}">
                    </div>
                </div>
            </div>
        `;
        
        destinosContainer.insertAdjacentHTML('beforeend', novoDestinoHTML);
        
        // Inicializar autocomplete para o novo input
        window.initializeDestinationAutocomplete(destinoCounter);
        
        // Configurar validações de data para o novo destino
        setupDateValidation(destinoCounter);
        
        // Atualizar visibilidade dos botões de remover
        updateRemoveButtonsVisibility();
        
        // Scroll suave para o novo input
        const novoInput = document.getElementById(`tripDestination_${destinoCounter}`);
        novoInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Focar no novo input
        setTimeout(() => {
            novoInput.focus();
        }, 300);
    }
    
    function removeDestino(index) {
        const destinoItem = document.querySelector(`[data-destino-index="${index}"]`);
        if (destinoItem) {
            destinoItem.classList.add('animate-fade-out');
            setTimeout(() => {
                destinoItem.remove();
                updateRemoveButtonsVisibility();
                reorderDestinos();
            }, 300);
        }
    }
    
    function updateRemoveButtonsVisibility() {
        const allDestinos = document.querySelectorAll('.destino-item');
        const removeButtons = document.querySelectorAll('.remove-destino-btn');
        
        // Mostrar botões de remover apenas se houver mais de um destino
        removeButtons.forEach(btn => {
            if (allDestinos.length > 1) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        });
    }
    
    function reorderDestinos() {
        const allDestinos = document.querySelectorAll('.destino-item');
        allDestinos.forEach((item, newIndex) => {
            // Atualizar data-destino-index
            item.setAttribute('data-destino-index', newIndex);
            
            // Atualizar label do destino
            const label = item.querySelector('label');
            if (label) {
                label.innerHTML = `Destino ${newIndex + 1}<label class="text-red-600 text-base font-thin">*</label>`;
            }
            
            // Atualizar IDs dos inputs de destino
            const input = item.querySelector('.destino-input');
            input.id = `tripDestination_${newIndex}`;
            input.setAttribute('data-index', newIndex);
            
            // Atualizar IDs dos inputs de data
            const dataInicioInput = item.querySelector('.destino-data-inicio');
            if (dataInicioInput) {
                dataInicioInput.id = `destino_data_inicio_${newIndex}`;
                dataInicioInput.setAttribute('data-destino-index', newIndex);
            }
            
            const dataFimInput = item.querySelector('.destino-data-fim');
            if (dataFimInput) {
                dataFimInput.id = `destino_data_fim_${newIndex}`;
                dataFimInput.setAttribute('data-destino-index', newIndex);
            }
            
            // Atualizar data-index dos botões
            const removeBtn = item.querySelector('.remove-destino-btn');
            if (removeBtn) {
                removeBtn.setAttribute('data-index', newIndex);
            }
        });
        
        // Reconfigurar validações de data para todos os destinos
        allDestinos.forEach((item, index) => {
            setupDateValidation(index);
        });
        
        // Atualizar contador
        destinoCounter = allDestinos.length - 1;
    }
    
    // Função para configurar validações de data
    function setupDateValidation(destinoIndex) {
        const dataInicioInput = document.getElementById(`destino_data_inicio_${destinoIndex}`);
        const dataFimInput = document.getElementById(`destino_data_fim_${destinoIndex}`);
        
        if (!dataInicioInput || !dataFimInput) return;
        
        // Validação para data de início
        dataInicioInput.addEventListener('change', function() {
            validateDateSequence();
            updateMinDateForFim(destinoIndex);
        });
        
        // Validação para data de fim
        dataFimInput.addEventListener('change', function() {
            validateDateSequence();
        });
        
        // Configurar data mínima inicial
        updateMinDateForInicio(destinoIndex);
        updateMinDateForFim(destinoIndex);
    }
    
    function updateMinDateForInicio(destinoIndex) {
        const dataInicioInput = document.getElementById(`destino_data_inicio_${destinoIndex}`);
        if (!dataInicioInput) return;
        
        if (destinoIndex === 0) {
            // Primeiro destino pode começar a partir de hoje
            const today = new Date().toISOString().split('T')[0];
            dataInicioInput.setAttribute('min', today);
        } else {
            // Destinos subsequentes devem começar após o fim do destino anterior
            const prevDataFimInput = document.getElementById(`destino_data_fim_${destinoIndex - 1}`);
            if (prevDataFimInput && prevDataFimInput.value) {
                const nextDay = new Date(prevDataFimInput.value);
                nextDay.setDate(nextDay.getDate());
                dataInicioInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
            }
        }
    }
    
    function updateMinDateForFim(destinoIndex) {
        const dataInicioInput = document.getElementById(`destino_data_inicio_${destinoIndex}`);
        const dataFimInput = document.getElementById(`destino_data_fim_${destinoIndex}`);
        
        if (!dataInicioInput || !dataFimInput) return;
        
        if (dataInicioInput.value) {
            // Data de fim deve ser pelo menos no mesmo dia da data de início
            dataFimInput.setAttribute('min', dataInicioInput.value);
        }
    }
    
    function validateDateSequence() {
        const allDestinos = document.querySelectorAll('.destino-item');
        let isValid = true;
        
        allDestinos.forEach((item, index) => {
            const dataInicioInput = document.getElementById(`destino_data_inicio_${index}`);
            const dataFimInput = document.getElementById(`destino_data_fim_${index}`);
            
            if (!dataInicioInput || !dataFimInput) return;
            
            // Reset styles
            dataInicioInput.style.borderColor = '';
            dataFimInput.style.borderColor = '';
            window.hideErrorMessage(dataInicioInput);
            window.hideErrorMessage(dataFimInput);
            
            if (dataInicioInput.value && dataFimInput.value) {
                const dataInicio = new Date(dataInicioInput.value);
                const dataFim = new Date(dataFimInput.value);
                
                // Validar se data de fim não é anterior à data de início
                if (dataFim < dataInicio) {
                    dataFimInput.style.borderColor = '#ef4444';
                    window.showErrorMessage(dataFimInput, 'A data de fim não pode ser anterior à data de início');
                    isValid = false;
                }
                
                // Validar sequência com destino anterior
                if (index > 0) {
                    const prevDataFimInput = document.getElementById(`destino_data_fim_${index - 1}`);
                    if (prevDataFimInput && prevDataFimInput.value) {
                        const prevDataFim = new Date(prevDataFimInput.value);
                        if (dataInicio < prevDataFim) {
                            dataInicioInput.style.borderColor = '#ef4444';
                            window.showErrorMessage(dataInicioInput, 'A data de início deve ser posterior ao fim do destino anterior');
                            isValid = false;
                        }
                    }
                }
                
                // Atualizar datas mínimas para próximo destino
                updateMinDateForInicio(index + 1);
            }
        });
        
        return isValid;
    }
    
    // Função para validar todas as datas
    function validateAllDates() {
        const allDestinos = document.querySelectorAll('.destino-item');
        let allValid = true;
        
        allDestinos.forEach((item, index) => {
            const dataInicioInput = document.getElementById(`destino_data_inicio_${index}`);
            const dataFimInput = document.getElementById(`destino_data_fim_${index}`);
            
            // Verificar se ambos os campos têm valores
            if (!dataInicioInput.value.trim()) {
                dataInicioInput.style.borderColor = '#ef4444';
                window.showErrorMessage(dataInicioInput, 'A data de início é obrigatória');
                allValid = false;
            }
            
            if (!dataFimInput.value.trim()) {
                dataFimInput.style.borderColor = '#ef4444';
                window.showErrorMessage(dataFimInput, 'A data de fim é obrigatória');
                allValid = false;
            }
        });
        
        // Validar sequência de datas
        if (allValid) {
            allValid = validateDateSequence();
        }
        
        return allValid;
    }
    
    // Função para validar todos os destinos
    function validateAllDestinations() {
        const allInputs = document.querySelectorAll('.destino-input');
        let allValid = true;
        let hasAtLeastOne = false;
        
        allInputs.forEach(input => {
            const hasValue = input.value.trim() !== '';
            const isValid = input.getAttribute('data-valid') === 'true';
            
            if (hasValue) {
                hasAtLeastOne = true;
                if (!isValid) {
                    allValid = false;
                    input.style.borderColor = '#ef4444'; // Vermelho
                    window.showErrorMessage(input, 'Por favor, selecione um destino da lista de sugestões');
                }
            }
        });
        
        if (!hasAtLeastOne) {
            allValid = false;
            // Destacar o primeiro input
            const firstInput = document.getElementById('tripDestination_0');
            if (firstInput) {
                firstInput.style.borderColor = '#ef4444';
                window.showErrorMessage(firstInput, 'Pelo menos um destino é obrigatório');
            }
        }
        
        return allValid && hasAtLeastOne;
    }
    
    // Função para validar origem
    function validateOrigin() {
        const originInput = document.getElementById('origem');
        if (!originInput) return false;
        
        const hasValue = originInput.value.trim() !== '';
        const isValid = originInput.getAttribute('data-valid') === 'true';
        
        if (hasValue && !isValid) {
            originInput.style.borderColor = '#ef4444'; // Vermelho
            window.showErrorMessage(originInput, 'Por favor, selecione uma origem da lista de sugestões');
            return false;
        }
        
        if (!hasValue) {
            originInput.style.borderColor = '#ef4444'; // Vermelho
            window.showErrorMessage(originInput, 'A origem é obrigatória');
            return false;
        }
        
        return true;
    }
    
    // Função de validação geral
    function validateTripForm() {
        // Validar nome da viagem
        const nomeViagemInput = document.getElementById('nome_viagem');
        if (!nomeViagemInput || !nomeViagemInput.value.trim()) {
            nomeViagemInput.style.borderColor = '#ef4444';
            window.showErrorMessage && window.showErrorMessage(nomeViagemInput, 'O nome da viagem é obrigatório');
            nomeViagemInput.focus();
            return false;
        } else {
            nomeViagemInput.style.borderColor = '';
            window.hideErrorMessage && window.hideErrorMessage(nomeViagemInput);
        }
        
        const destinationsValid = validateAllDestinations();
        const originValid = validateOrigin();
        const datesValid = validateAllDates();
        
        return destinationsValid && originValid && datesValid;
    }
    
    // Expor funções globalmente para uso em outros scripts
    window.validateDestinations = validateAllDestinations;
    window.validateOrigin = validateOrigin;
    window.validateDates = validateAllDates;
    window.validateTripForm = validateTripForm;
    
    // Configurar validações de data para o primeiro destino
    document.addEventListener('DOMContentLoaded', function() {
        setupDateValidation(0);
        
        // Configurar validação para nome da viagem
        const nomeViagemInput = document.getElementById('nome_viagem');
        if (nomeViagemInput) {
            nomeViagemInput.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.style.borderColor = '#ef4444';
                    window.showErrorMessage && window.showErrorMessage(this, 'O nome da viagem é obrigatório');
                } else {
                    this.style.borderColor = '';
                    window.hideErrorMessage && window.hideErrorMessage(this);
                }
            });
            
            nomeViagemInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.style.borderColor = '';
                    window.hideErrorMessage && window.hideErrorMessage(this);
                }
            });
        }
    });
    
    // Validação para o primeiro destino (index 0)
    const dataInicioInput = document.getElementById('destino_data_inicio_0');
    const dataFimInput = document.getElementById('destino_data_fim_0');
    if (dataInicioInput && dataFimInput) {
        // Sempre garantir min da data de início como hoje
        const today = new Date().toISOString().split('T')[0];
        dataInicioInput.setAttribute('min', today);

        // Inicializa min da data de fim como hoje
        dataFimInput.setAttribute('min', today);

        // Atualiza min da data de fim ao mudar data de início
        dataInicioInput.addEventListener('change', function() {
            dataFimInput.setAttribute('min', this.value);
            // Se data de fim for menor que início, limpa e mostra erro
            if (dataFimInput.value && dataFimInput.value < this.value) {
                dataFimInput.value = '';
                dataFimInput.style.borderColor = '#ef4444';
                window.showErrorMessage && window.showErrorMessage(dataFimInput, 'A data de fim não pode ser anterior à data de início');
                setTimeout(() => {
                    dataFimInput.style.borderColor = '';
                    window.hideErrorMessage && window.hideErrorMessage(dataFimInput);
                }, 2500);
            }
        });

        // Garante que data de fim nunca seja menor que início
        dataFimInput.addEventListener('change', function() {
            if (dataInicioInput.value && this.value < dataInicioInput.value) {
                this.value = '';
                this.style.borderColor = '#ef4444';
                window.showErrorMessage && window.showErrorMessage(this, 'A data de fim não pode ser anterior à data de início');
                setTimeout(() => {
                    this.style.borderColor = '';
                    window.hideErrorMessage && window.hideErrorMessage(this);
                }, 2500);
            }
        });

        // Sempre que o campo de fim receber foco, atualiza o min para garantir
        dataFimInput.addEventListener('focus', function() {
            const minDate = dataInicioInput.value || today;
            dataFimInput.setAttribute('min', minDate);
        });
    }
});
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

.animate-fade-out {
    animation: fadeOut 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}

.autocomplete-list {
    z-index: 1000;
}

.autocomplete-item:hover {
    background-color: #f3f4f6;
}

.destino-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.origem-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>

<!-- Google Maps API -->
<script>
// Mover initializeDestinationAutocomplete para escopo global
window.initializeDestinationAutocomplete = function(index) {
    const input = document.getElementById(`tripDestination_${index}`);
    if (!input) {
        console.error(`Input tripDestination_${index} não encontrado`);
        return;
    }
    
    // Limpar qualquer autocomplete anterior do Google Places
    if (input._autocompleteInitialized) {

        input._autocompleteInitialized = false;
    }
    
    // Marcar como usando nosso novo sistema
    input.setAttribute('data-new-autocomplete', 'true');
    
    let autocompleteList = null;
    let selectedIndex = -1;
    let validDestinations = [];
    let isValidSelection = false;
    
    // Reset da validação quando o usuário começa a digitar novamente
    input.addEventListener('input', function(e) {
        input.removeAttribute('data-valid');
        input.removeAttribute('data-place-id');
        hideErrorMessage(input);
        isValidSelection = false;
    });
    
    // Debounce para evitar muitas requisições
    let debounceTimer;
    
    input.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                closeAutocompleteList();
                return;
            }
            
            searchDestinations(query, index);
        }, 300);
    });
    
    input.addEventListener('keydown', function(e) {
        if (!autocompleteList) return;
        
        const items = autocompleteList.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                selectDestination(items[selectedIndex], input);
            }
        } else if (e.key === 'Escape') {
            closeAutocompleteList();
        }
    });
    
    input.addEventListener('blur', function(e) {
        // Delay para permitir clique em itens do autocomplete
        setTimeout(() => {
            const hasValidSelection = input.getAttribute('data-valid') === 'true';
            
            if (!hasValidSelection && input.value.trim() !== '') {
                // Se não é uma seleção válida, limpar o campo
                input.value = '';
                input.removeAttribute('data-place-id');
                input.removeAttribute('data-valid');
                input.style.borderColor = '#ef4444'; // Vermelho
                
                // Mostrar mensagem de erro
                showErrorMessage(input, 'Por favor, selecione um destino da lista de sugestões');
                
                setTimeout(() => {
                    input.style.borderColor = '';
                    hideErrorMessage(input);
                }, 3000);
            }
            closeAutocompleteList();
        }, 150);
    });
    
    function searchDestinations(query, inputIndex) {
        if (!window.google || !window.google.maps || !window.google.maps.places) {
            console.error('Google Maps Places API não está carregada');
            return;
        }
        
        const service = new google.maps.places.AutocompleteService();
        
        service.getPlacePredictions({
            input: query,
            types: ['(cities)'],
            language: 'pt-BR'
        }, (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                validDestinations = predictions.map(prediction => ({
                    name: prediction.structured_formatting.main_text,
                    description: prediction.structured_formatting.secondary_text,
                    place_id: prediction.place_id,
                    full_description: prediction.description
                }));
                displayAutocompleteResults(validDestinations, inputIndex);
            } else {
                validDestinations = [];
                displayAutocompleteResults([], inputIndex);
            }
        });
    }
    
    function displayAutocompleteResults(destinations, inputIndex) {
        closeAutocompleteList();
        
        if (!destinations || destinations.length === 0) return;
        
        const inputElement = document.getElementById(`tripDestination_${inputIndex}`);
        if (!inputElement) return;
        
        autocompleteList = document.createElement('div');
        autocompleteList.className = 'autocomplete-list absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1';
        autocompleteList.style.top = inputElement.offsetHeight + 'px';
        
        destinations.forEach((destination, index) => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
            item.setAttribute('data-place-id', destination.place_id);
            
            item.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-blue-500"></i>
                    <div>
                        <div class="font-medium text-gray-800">${destination.name}</div>
                        <div class="text-sm text-gray-500">${destination.description || ''}</div>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                selectDestination(this, inputElement);
            });
            
            autocompleteList.appendChild(item);
        });
        
        // Posicionar relativo ao input
        const inputRect = inputElement.getBoundingClientRect();
        const parentRect = inputElement.offsetParent.getBoundingClientRect();
        
        autocompleteList.style.position = 'absolute';
        autocompleteList.style.left = '0';
        autocompleteList.style.right = '0';
        
        inputElement.parentNode.style.position = 'relative';
        inputElement.parentNode.appendChild(autocompleteList);
        
        selectedIndex = -1;
    }
    
    function selectDestination(item, inputElement) {
        const destinationName = item.querySelector('.font-medium').textContent;
        const destinationIndex = inputElement.id.split('_')[1];
        
        inputElement.value = destinationName;
        inputElement.setAttribute('data-place-id', item.getAttribute('data-place-id'));
        inputElement.setAttribute('data-valid', 'true');
        isValidSelection = true;
        
        // Limpar erro e aplicar estilo de sucesso
        hideErrorMessage(inputElement);
        inputElement.style.borderColor = '#10b981'; // Verde
        setTimeout(() => {
            inputElement.style.borderColor = '';
        }, 2000);
        closeAutocompleteList();
    }
    
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('bg-blue-100');
            } else {
                item.classList.remove('bg-blue-100');
            }
        });
    }
    
    function showErrorMessage(inputElement, message) {
        // Remove mensagem anterior se existir
        hideErrorMessage(inputElement);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-500 text-sm mt-1';
        errorDiv.textContent = message;
        errorDiv.setAttribute('data-error-for', inputElement.id);
        
        inputElement.parentNode.appendChild(errorDiv);
    }
    
    function hideErrorMessage(inputElement) {
        const existingError = inputElement.parentNode.querySelector(`[data-error-for="${inputElement.id}"]`);
        if (existingError) {
            existingError.remove();
        }
    }
    
    function closeAutocompleteList() {
        if (autocompleteList) {
            autocompleteList.remove();
            autocompleteList = null;
        }
        selectedIndex = -1;
    }
    
    // Fechar autocomplete ao clicar fora
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !autocompleteList?.contains(e.target)) {
            closeAutocompleteList();
        }
    });
};

// Expor funções de erro globalmente
window.showErrorMessage = function(inputElement, message) {
    // Remove mensagem anterior se existir
    window.hideErrorMessage(inputElement);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    errorDiv.setAttribute('data-error-for', inputElement.id);
    
    inputElement.parentNode.appendChild(errorDiv);
};

window.hideErrorMessage = function(inputElement) {
    const existingError = inputElement.parentNode.querySelector(`[data-error-for="${inputElement.id}"]`);
    if (existingError) {
        existingError.remove();
    }
};

// Função específica para inicializar o autocomplete da origem
window.initializeOriginAutocomplete = function() {
    const input = document.getElementById('origem');
    if (!input) {
        console.error('Input origem não encontrado');
        return;
    }
    
    // Limpar qualquer autocomplete anterior do Google Places
    if (input._autocompleteInitialized) {

        input._autocompleteInitialized = false;
        // Limpar eventos anteriores se existirem
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);
        // Referenciar o novo input
        const cleanInput = document.getElementById('origem');
        return window.initializeOriginAutocomplete(); // Reinicializar com input limpo
    }
    
    // Marcar como usando nosso novo sistema
    input.setAttribute('data-new-autocomplete', 'true');
    
    let autocompleteList = null;
    let selectedIndex = -1;
    let validDestinations = [];
    let isValidSelection = false;
    
    // Reset da validação quando o usuário começa a digitar novamente
    input.addEventListener('input', function(e) {
        input.removeAttribute('data-valid');
        input.removeAttribute('data-place-id');
        window.hideErrorMessage(input);
        isValidSelection = false;
    });
    
    // Debounce para evitar muitas requisições
    let debounceTimer;
    
    input.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                closeAutocompleteList();
                return;
            }
            
            searchOrigins(query);
        }, 300);
    });
    
    input.addEventListener('keydown', function(e) {
        if (!autocompleteList) return;
        
        const items = autocompleteList.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                selectOrigin(items[selectedIndex], input);
            }
        } else if (e.key === 'Escape') {
            closeAutocompleteList();
        }
    });
    
    input.addEventListener('blur', function(e) {
        // Delay para permitir clique em itens do autocomplete
        setTimeout(() => {
            const hasValidSelection = input.getAttribute('data-valid') === 'true';
            
            if (!hasValidSelection && input.value.trim() !== '') {
                // Se não é uma seleção válida, limpar o campo
                input.value = '';
                input.removeAttribute('data-place-id');
                input.removeAttribute('data-valid');
                input.style.borderColor = '#ef4444'; // Vermelho
                
                // Mostrar mensagem de erro
                window.showErrorMessage(input, 'Por favor, selecione uma origem da lista de sugestões');
                
                setTimeout(() => {
                    input.style.borderColor = '';
                    window.hideErrorMessage(input);
                }, 3000);
            }
            closeAutocompleteList();
        }, 150);
    });
    
    function searchOrigins(query) {
        if (!window.google || !window.google.maps || !window.google.maps.places) {
            console.error('Google Maps Places API não está carregada');
            return;
        }
        
        const service = new google.maps.places.AutocompleteService();
        
        service.getPlacePredictions({
            input: query,
            types: ['(cities)'],
            language: 'pt-BR'
        }, (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                validDestinations = predictions.map(prediction => ({
                    name: prediction.structured_formatting.main_text,
                    description: prediction.structured_formatting.secondary_text,
                    place_id: prediction.place_id,
                    full_description: prediction.description
                }));
                displayOriginResults(validDestinations);
            } else {
                validDestinations = [];
                displayOriginResults([]);
            }
        });
    }
    
    function displayOriginResults(destinations) {
        closeAutocompleteList();
        
        if (!destinations || destinations.length === 0) return;
        
        const inputElement = document.getElementById('origem');
        if (!inputElement) return;
        
        autocompleteList = document.createElement('div');
        autocompleteList.className = 'autocomplete-list absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1';
        autocompleteList.style.top = inputElement.offsetHeight + 'px';
        
        destinations.forEach((destination, index) => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
            item.setAttribute('data-place-id', destination.place_id);
            
            item.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-home text-green-500"></i>
                    <div>
                        <div class="font-medium text-gray-800">${destination.name}</div>
                        <div class="text-sm text-gray-500">${destination.description || ''}</div>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                selectOrigin(this, inputElement);
            });
            
            autocompleteList.appendChild(item);
        });
        
        // Posicionar relativo ao input
        autocompleteList.style.position = 'absolute';
        autocompleteList.style.left = '0';
        autocompleteList.style.right = '0';
        
        inputElement.parentNode.style.position = 'relative';
        inputElement.parentNode.appendChild(autocompleteList);
        
        selectedIndex = -1;
    }
    
    function selectOrigin(item, inputElement) {
        const originName = item.querySelector('.font-medium').textContent;
        
        inputElement.value = originName;
        inputElement.setAttribute('data-place-id', item.getAttribute('data-place-id'));
        inputElement.setAttribute('data-valid', 'true');
        isValidSelection = true;
        
        // Limpar erro e aplicar estilo de sucesso
        window.hideErrorMessage(inputElement);
        inputElement.style.borderColor = '#10b981'; // Verde
        setTimeout(() => {
            inputElement.style.borderColor = '';
        }, 2000);
        closeAutocompleteList();
    }
    
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('bg-blue-100');
            } else {
                item.classList.remove('bg-blue-100');
            }
        });
    }
    
    function closeAutocompleteList() {
        if (autocompleteList) {
            autocompleteList.remove();
            autocompleteList = null;
        }
        selectedIndex = -1;
    }
    
    // Fechar autocomplete ao clicar fora
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !autocompleteList?.contains(e.target)) {
            closeAutocompleteList();
        }
    });
};

function initStepFormMap() {

    // Inicializar autocomplete para o primeiro destino
    window.initializeDestinationAutocomplete(0);
    // Inicializar autocomplete para a origem
    window.initializeOriginAutocomplete();
}

// Fallback caso o Google Maps já esteja carregado
if (window.google && window.google.maps) {

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            window.initializeDestinationAutocomplete(0);
            window.initializeOriginAutocomplete();
        }, 100);
    });
} else {
    // Se não estiver carregado, aguardar o callback
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if (window.google && window.google.maps) {

                window.initializeDestinationAutocomplete(0);
                window.initializeOriginAutocomplete();
            }
        }, 1000);
    });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&callback=initStepFormMap" async defer></script>