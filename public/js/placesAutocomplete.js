/**
 * Sistema de Autocomplete com Google Places API
 * Reutilizável para qualquer input de localização
 * 
 * @author AgendAI Development Team
 * @version 1.0.0
 */

window.PlacesAutocomplete = class {
    /**
     * Construtor da classe
     * @param {string} inputId - ID do elemento input
     * @param {Object} options - Opções de configuração
     */
    constructor(inputId, options = {}) {
        this.inputId = inputId;
        this.input = document.getElementById(inputId);
        
        if (!this.input) {
            console.error(`Input com ID "${inputId}" não encontrado`);
            return;
        }
        
        this.autocompleteList = null;
        this.selectedIndex = -1;
        this.validPlaces = [];
        this.isValidSelection = false;
        this.debounceTimer = null;
        
        // Opções padrão
        this.options = {
            types: options.types || ['(cities)'],
            language: options.language || 'pt-BR',
            componentRestrictions: options.componentRestrictions !== undefined ? options.componentRestrictions : {},
            placeholder: options.placeholder || 'Digite para buscar...',
            debounceMs: options.debounceMs || 300,
            minChars: options.minChars || 2,
            iconClass: options.iconClass || 'fa-map-marker-alt',
            iconColor: options.iconColor || 'text-blue-500',
            errorMessage: options.errorMessage || 'Por favor, selecione uma localização válida da lista',
            ...options
        };
        
        this.init();
    }
    
    /**
     * Inicializa os event listeners e configurações
     */
    init() {
        if (this.input._placesAutocompleteInitialized) {
            console.warn(`Autocomplete já inicializado para #${this.inputId}`);
            return;
        }
        
        // Marcar como inicializado
        this.input._placesAutocompleteInitialized = true;
        this.input.setAttribute('data-places-autocomplete', 'true');
        this.input.setAttribute('data-valid', 'false');
        this.input.setAttribute('autocomplete', 'off');
        
        // Placeholder
        if (this.options.placeholder) {
            this.input.placeholder = this.options.placeholder;
        }
        
        // Event listener para input (com debounce)
        this.input.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            // Reset da validação quando o usuário digita
            this.isValidSelection = false;
            this.input.setAttribute('data-valid', 'false');
            this.removeValidationStyles();
            this.hideErrorMessage();
            
            // Limpar timeout anterior
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }
            
            // Se a query for muito curta, fechar autocomplete
            if (query.length < this.options.minChars) {
                this.closeAutocompleteList();
                return;
            }
            
            // Debounce
            this.debounceTimer = setTimeout(() => {
                this.searchPlaces(query);
            }, this.options.debounceMs);
        });
        
        // Event listener para navegação por teclado
        this.input.addEventListener('keydown', (e) => {
            if (!this.autocompleteList) return;
            
            const items = this.autocompleteList.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.updateSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                this.updateSelection(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    items[this.selectedIndex].click();
                }
            } else if (e.key === 'Escape') {
                this.closeAutocompleteList();
            }
        });
        
        // Event listener para blur (validação)
        this.input.addEventListener('blur', (e) => {
            // Delay para permitir clique nos itens da lista
            setTimeout(() => {
                if (!this.isValidSelection && this.input.value.trim() !== '') {
                    this.showErrorMessage();
                    this.input.value = '';
                    this.input.setAttribute('data-valid', 'false');
                    this.addInvalidStyle();
                }
                this.closeAutocompleteList();
            }, 150);
        });
        
        // Fechar autocomplete ao clicar fora
        document.addEventListener('click', (e) => {
            if (e.target !== this.input) {
                this.closeAutocompleteList();
            }
        });
        
        console.log(`PlacesAutocomplete inicializado para #${this.inputId}`);
    }
    
    /**
     * Busca lugares usando Google Places API
     * @param {string} query - Termo de busca
     */
    searchPlaces(query) {
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.error('Google Places API não carregada');
            return;
        }
        
        const service = new google.maps.places.AutocompleteService();
        
        const request = {
            input: query,
            types: this.options.types,
            language: this.options.language
        };
        
        // Adicionar componentRestrictions apenas se houver restrições
        if (this.options.componentRestrictions && Object.keys(this.options.componentRestrictions).length > 0) {
            request.componentRestrictions = this.options.componentRestrictions;
        }
        
        service.getPlacePredictions(request, (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                this.validPlaces = predictions;
                this.displayResults(predictions);
            } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                this.displayNoResults();
            } else {
                console.warn('Erro ao buscar lugares:', status);
                this.closeAutocompleteList();
            }
        });
    }
    
    /**
     * Exibe os resultados do autocomplete
     * @param {Array} places - Lista de lugares
     */
    displayResults(places) {
        this.closeAutocompleteList();
        
        if (places.length === 0) {
            this.displayNoResults();
            return;
        }
        
        // Criar container de autocomplete
        this.autocompleteList = document.createElement('div');
        this.autocompleteList.className = 'autocomplete-list';
        this.autocompleteList.id = `${this.inputId}_suggestions`;
        
        // Adicionar itens
        places.forEach((place, index) => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            
            // Ícone e texto
            item.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${this.options.iconClass} ${this.options.iconColor} mr-3"></i>
                    <div>
                        <div class="font-medium text-gray-800">${place.structured_formatting.main_text}</div>
                        <div class="text-sm text-gray-500">${place.structured_formatting.secondary_text || ''}</div>
                    </div>
                </div>
            `;
            
            // Event listener para seleção
            item.addEventListener('click', () => {
                this.selectPlace(place);
            });
            
            this.autocompleteList.appendChild(item);
        });
        
        // Inserir no container pai do input
        const container = this.input.closest('.relative') || this.input.parentNode;
        container.appendChild(this.autocompleteList);
        this.selectedIndex = -1;
    }
    
    /**
     * Exibe mensagem quando não há resultados
     */
    displayNoResults() {
        this.closeAutocompleteList();
        
        this.autocompleteList = document.createElement('div');
        this.autocompleteList.className = 'autocomplete-list';
        this.autocompleteList.id = `${this.inputId}_suggestions`;
        this.autocompleteList.innerHTML = `
            <div class="autocomplete-item text-gray-500 text-center">
                <i class="fas fa-search mr-2"></i>
                Nenhum resultado encontrado
            </div>
        `;
        
        const container = this.input.closest('.relative') || this.input.parentNode;
        container.appendChild(this.autocompleteList);
    }
    
    /**
     * Seleciona um lugar da lista
     * @param {Object} place - Objeto de lugar do Google Places
     */
    selectPlace(place) {
        // Preencher input
        this.input.value = place.description;
        
        // Marcar como válido
        this.isValidSelection = true;
        this.input.setAttribute('data-valid', 'true');
        this.input.setAttribute('data-place-id', place.place_id);
        
        // Feedback visual
        this.addValidStyle();
        this.hideErrorMessage();
        
        // Fechar lista
        this.closeAutocompleteList();
        
        // Callback personalizado
        if (typeof this.options.onSelect === 'function') {
            this.options.onSelect(place);
        }
        
        console.log('Lugar selecionado:', place.description);
    }
    
    /**
     * Atualiza a seleção visual na navegação por teclado
     * @param {NodeList} items - Lista de itens
     */
    updateSelection(items) {
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    /**
     * Fecha a lista de autocomplete
     */
    closeAutocompleteList() {
        if (this.autocompleteList) {
            this.autocompleteList.remove();
            this.autocompleteList = null;
        }
        this.selectedIndex = -1;
    }
    
    /**
     * Mostra mensagem de erro
     */
    showErrorMessage() {
        this.hideErrorMessage();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-500 text-sm mt-1';
        errorDiv.id = `${this.inputId}_error`;
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${this.options.errorMessage}`;
        
        const container = this.input.closest('.relative') || this.input.parentNode;
        container.appendChild(errorDiv);
    }
    
    /**
     * Esconde mensagem de erro
     */
    hideErrorMessage() {
        const existingError = document.getElementById(`${this.inputId}_error`);
        if (existingError) {
            existingError.remove();
        }
    }
    
    /**
     * Adiciona estilo de válido
     */
    addValidStyle() {
        this.input.classList.remove('input-invalid');
        this.input.classList.add('input-valid');
    }
    
    /**
     * Adiciona estilo de inválido
     */
    addInvalidStyle() {
        this.input.classList.remove('input-valid');
        this.input.classList.add('input-invalid');
    }
    
    /**
     * Remove estilos de validação
     */
    removeValidationStyles() {
        this.input.classList.remove('input-valid', 'input-invalid');
    }
    
    /**
     * Valida se o input tem um valor válido
     * @returns {boolean}
     */
    validate() {
        const isValid = this.input.getAttribute('data-valid') === 'true';
        
        if (!isValid && this.input.value.trim() !== '') {
            this.showErrorMessage();
            this.addInvalidStyle();
        }
        
        return isValid;
    }
    
    /**
     * Reseta o campo
     */
    reset() {
        this.input.value = '';
        this.input.setAttribute('data-valid', 'false');
        this.input.removeAttribute('data-place-id');
        this.isValidSelection = false;
        this.removeValidationStyles();
        this.hideErrorMessage();
        this.closeAutocompleteList();
    }
    
    /**
     * Obtém o valor atual
     * @returns {Object}
     */
    getValue() {
        return {
            description: this.input.value,
            isValid: this.input.getAttribute('data-valid') === 'true',
            placeId: this.input.getAttribute('data-place-id') || null
        };
    }
};

// Log de carregamento
console.log('PlacesAutocomplete class loaded successfully');
