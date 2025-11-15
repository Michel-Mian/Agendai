/**
 * InlineEditManager - Gerenciador de edição inline para detalhes da viagem
 * Responsável por controlar a edição de destino, origem, datas e orçamento
 */
class InlineEditManager {
    constructor(tripId) {
        this.tripId = tripId;
        this.initializeInlineEdits();
    }

    initializeInlineEdits() {
        this.initNomeEdit();
        this.initDestinoEdit();
        this.initOrigemEdit();
        this.initDatasEdit();
        this.initOrcamentoEdit();
        this.initPlacesAutocomplete();
    }

    // Inicializar Google Places Autocomplete
    initPlacesAutocomplete() {
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.log('Google Maps API não está disponível ainda');
            return;
        }

        console.log('Inicializando autocomplete para inputs de localização');
        const destinoInput = document.querySelector('.destino-input');
        const origemInput = document.querySelector('.origem-edit input');

        [destinoInput, origemInput].forEach((input, index) => {
            if (input && !input._autocompleteInitialized) {
                console.log(`Inicializando autocomplete para input ${index === 0 ? 'destino' : 'origem'}`);
                
                try {
                    const autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['(regions)'],
                    });
                    input._autocompleteInitialized = true;
                    input._placeSelected = false;

                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();
                        console.log('Lugar selecionado:', place);
                        if (place && place.place_id) {
                            input._placeSelected = true;
                            input.classList.remove('border-red-500');
                            console.log('Local válido selecionado');
                        } else {
                            input._placeSelected = false;
                            input.classList.add('border-red-500');
                            console.log('Local inválido');
                        }
                    });

                    input.addEventListener('input', function() {
                        input._placeSelected = false;
                        input.classList.remove('border-red-500');
                    });

                    input.addEventListener('blur', function(e) {
                        setTimeout(() => {
                            if (!input._placeSelected && input.value.trim() !== '') {
                                input.classList.add('border-red-500');
                                console.log('Campo não validado - valor não selecionado do autocomplete');
                            }
                        }, 200);
                    });

                    console.log(`Autocomplete configurado com sucesso para ${index === 0 ? 'destino' : 'origem'}`);
                } catch (error) {
                    console.error('Erro ao configurar autocomplete:', error);
                }
            }
        });
    }

    // Edição do Nome da Viagem
    initNomeEdit() {
        const editBtn = document.querySelector('.edit-nome-btn');
        const saveBtn = document.querySelector('.save-nome-btn');
        const cancelBtn = document.querySelector('.cancel-nome-btn');
        const displayDiv = document.querySelector('.nome-display');
        const editDiv = document.querySelector('.nome-edit');
        const input = editDiv?.querySelector('input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                displayDiv.classList.add('hidden');
                editDiv.classList.remove('hidden');
                input.focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                input.value = input.defaultValue;
                editDiv.classList.add('hidden');
                displayDiv.classList.remove('hidden');
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveField('nome_viagem', input.value, 'nome');
            });
        }
    }

    // Edição do Destino
    initDestinoEdit() {
        const editBtn = document.querySelector('.edit-destino-btn');
        const saveBtn = document.querySelector('.save-destino-btn');
        const cancelBtn = document.querySelector('.cancel-destino-btn');
        const displayDiv = document.querySelector('.destino-display');
        const editDiv = document.querySelector('.destino-edit');
        const input = document.querySelector('.destino-input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                displayDiv.classList.add('hidden');
                editDiv.classList.remove('hidden');
                input.focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                input.value = input.defaultValue;
                editDiv.classList.add('hidden');
                displayDiv.classList.remove('hidden');
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                console.log('Tentando salvar destino. PlaceSelected:', input._placeSelected, 'Valor:', input.value);
                // Validar se um local válido foi selecionado do autocomplete
                if (input._placeSelected === false && input.value.trim() !== '') {
                    console.log('Validação falhou - local não selecionado do autocomplete');
                    this.showNotification('Por favor, selecione um destino válido da lista de sugestões.', 'error');
                    input.focus();
                    return;
                }
                console.log('Validação passou - salvando destino');
                this.saveField('destino_viagem', input.value, 'destino');
            });
        }
    }

    // Edição da Origem
    initOrigemEdit() {
        const editBtn = document.querySelector('.edit-origem-btn');
        const saveBtn = document.querySelector('.save-origem-btn');
        const cancelBtn = document.querySelector('.cancel-origem-btn');
        const displayDiv = document.querySelector('.origem-display');
        const editDiv = document.querySelector('.origem-edit');
        const input = editDiv?.querySelector('input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                displayDiv.classList.add('hidden');
                editDiv.classList.remove('hidden');
                input.focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                input.value = input.defaultValue;
                editDiv.classList.add('hidden');
                displayDiv.classList.remove('hidden');
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                console.log('Tentando salvar origem. PlaceSelected:', input._placeSelected, 'Valor:', input.value);
                // Validar se um local válido foi selecionado do autocomplete
                if (input._placeSelected === false && input.value.trim() !== '') {
                    console.log('Validação falhou - local não selecionado do autocomplete');
                    this.showNotification('Por favor, selecione uma origem válida da lista de sugestões.', 'error');
                    input.focus();
                    return;
                }
                console.log('Validação passou - salvando origem');
                this.saveField('origem_viagem', input.value, 'origem');
            });
        }
    }

    // Edição das Datas
    initDatasEdit() {
        const editBtn = document.querySelector('.edit-datas-btn');
        const saveBtn = document.querySelector('.save-datas-btn');
        const cancelBtn = document.querySelector('.cancel-datas-btn');
        const displayDiv = document.querySelector('.datas-display');
        const editDiv = document.querySelector('.datas-edit');
        const inputInicio = document.querySelector('.data-inicio-input');
        const inputFim = document.querySelector('.data-fim-input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                displayDiv.classList.add('hidden');
                editDiv.classList.remove('hidden');
                inputInicio.focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                inputInicio.value = inputInicio.defaultValue;
                inputFim.value = inputFim.defaultValue;
                editDiv.classList.add('hidden');
                displayDiv.classList.remove('hidden');
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                const data = {
                    data_inicio_viagem: inputInicio.value,
                    data_final_viagem: inputFim.value
                };
                this.saveField('datas', data, 'datas');
            });
        }
    }

    // Edição do Orçamento
    initOrcamentoEdit() {
        const editBtn = document.querySelector('.edit-orcamento-btn');
        const saveBtn = document.querySelector('.save-orcamento-btn');
        const cancelBtn = document.querySelector('.cancel-orcamento-btn');
        const displayDiv = document.querySelector('.orcamento-display');
        const editDiv = document.querySelector('.orcamento-edit');
        const input = document.querySelector('.orcamento-input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                displayDiv.classList.add('hidden');
                editDiv.classList.remove('hidden');
                input.focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                input.value = input.defaultValue;
                editDiv.classList.add('hidden');
                displayDiv.classList.remove('hidden');
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveField('orcamento_viagem', parseFloat(input.value), 'orcamento');
            });
        }
    }

    // Função para salvar alterações
    async saveField(fieldName, value, type) {
        const saveBtn = document.querySelector(`.save-${type}-btn`);
        const originalText = saveBtn.innerHTML;
        
        // Mostrar loading
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

        try {
            const payload = typeof fieldName === 'string' && fieldName !== 'datas' 
                ? { [fieldName]: value }
                : value;

            const response = await fetch(`/viagens/${this.tripId}/update`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                // Fechar modo de edição
                this.closeEditMode(type);
                // Atualizar interface com novos valores
                this.updateInterface(type, value, data);
            } else {
                this.showNotification(data.message || 'Erro ao salvar alterações', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showNotification('Erro ao salvar alterações. Tente novamente.', 'error');
        } finally {
            // Restaurar botão
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }

    closeEditMode(type) {
        const displayDiv = document.querySelector(`.${type}-display`);
        const editDiv = document.querySelector(`.${type}-edit`);
        
        editDiv.classList.add('hidden');
        displayDiv.classList.remove('hidden');
    }

    updateInterface(type, value, serverData) {
        switch(type) {
            case 'nome':
                // Atualizar o título do nome da viagem no header
                const nomeTitle = document.querySelector('.nome-display h1');
                if (nomeTitle) {
                    nomeTitle.textContent = value;
                    this.highlightUpdatedElement(nomeTitle);
                }
                // Atualizar o input para manter sincronizado
                const nomeInput = document.querySelector('.nome-input');
                if (nomeInput) {
                    nomeInput.value = value;
                    nomeInput.defaultValue = value;
                }
                break;

            case 'destino':
                // Atualizar o título do destino no header
                const destinoTitle = document.querySelector('.destino-display h1');
                if (destinoTitle) {
                    destinoTitle.textContent = value;
                    this.highlightUpdatedElement(destinoTitle);
                }
                // Atualizar o input para manter sincronizado
                const destinoInput = document.querySelector('.destino-input');
                if (destinoInput) {
                    destinoInput.value = value;
                    destinoInput.defaultValue = value;
                }
                break;

            case 'origem':
                // Atualizar o valor no card de origem
                const origemDisplay = document.querySelector('.origem-display .text-gray-800');
                if (origemDisplay) {
                    origemDisplay.textContent = value;
                    this.highlightUpdatedElement(origemDisplay);
                }
                // Atualizar o input para manter sincronizado
                const origemInput = document.querySelector('.origem-edit input');
                if (origemInput) {
                    origemInput.value = value;
                    origemInput.defaultValue = value;
                }
                break;

            case 'datas':
                // Atualizar as datas no card
                const dataInicioDisplay = document.querySelector('.datas-display .space-y-1 .text-gray-800:first-child');
                const dataFimDisplay = document.querySelector('.datas-display .space-y-1 .text-gray-800:last-child');
                
                if (dataInicioDisplay && value.data_inicio_viagem) {
                    const dataInicio = new Date(value.data_inicio_viagem);
                    dataInicioDisplay.textContent = dataInicio.toLocaleDateString('pt-BR');
                    this.highlightUpdatedElement(dataInicioDisplay);
                }
                
                if (dataFimDisplay && value.data_final_viagem) {
                    const dataFim = new Date(value.data_final_viagem);
                    dataFimDisplay.textContent = dataFim.toLocaleDateString('pt-BR');
                    this.highlightUpdatedElement(dataFimDisplay);
                }

                // Atualizar os inputs para manter sincronizados
                const dataInicioInput = document.querySelector('.data-inicio-input');
                const dataFimInput = document.querySelector('.data-fim-input');
                
                if (dataInicioInput && value.data_inicio_viagem) {
                    dataInicioInput.value = value.data_inicio_viagem;
                    dataInicioInput.defaultValue = value.data_inicio_viagem;
                }
                
                if (dataFimInput && value.data_final_viagem) {
                    dataFimInput.value = value.data_final_viagem;
                    dataFimInput.defaultValue = value.data_final_viagem;
                }

                // Atualizar duração da viagem
                this.updateTripDuration(value.data_inicio_viagem, value.data_final_viagem);
                break;

            case 'orcamento':
                // Atualizar o valor no card de orçamento
                const orcamentoDisplay = document.querySelector('.orcamento-display .text-gray-800.font-bold.text-xl');
                if (orcamentoDisplay) {
                    const valorFormatado = new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(value);
                    orcamentoDisplay.textContent = valorFormatado;
                    this.highlightUpdatedElement(orcamentoDisplay);
                }
                
                // Atualizar o input para manter sincronizado
                const orcamentoInput = document.querySelector('.orcamento-input');
                if (orcamentoInput) {
                    orcamentoInput.value = value;
                    orcamentoInput.defaultValue = value;
                }

                // Recalcular e atualizar orçamento líquido
                const liquido = serverData && serverData.data && typeof serverData.data.orcamento_liquido !== 'undefined'
                    ? serverData.data.orcamento_liquido
                    : value; // fallback
                this.updateOrcamentoLiquido(liquido);
                break;
        }
    }

    updateTripDuration(dataInicio, dataFim) {
        // Atualizar a duração da viagem no status
        const durationElement = document.querySelector('.text-gray-600.text-sm');
        if (durationElement && durationElement.textContent.includes('dia')) {
            const inicio = new Date(dataInicio);
            const fim = new Date(dataFim);
            const diffTime = Math.abs(fim - inicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            const texto = diffDays === 1 ? 'dia' : 'dias';
            durationElement.textContent = `${diffDays} ${texto} de viagem`;
        }

        // Atualizar barra de progresso se necessário
        this.updateProgressBar(dataInicio, dataFim);
    }

    updateProgressBar(dataInicio, dataFim) {
        const progressBar = document.querySelector('.bg-blue-500.h-3.rounded-full');
        const progressText = document.querySelector('.text-sm.text-gray-900');
        
        if (progressBar && progressText) {
            const hoje = new Date();
            const inicio = new Date(dataInicio);
            const fim = new Date(dataFim);
            
            let progresso = 0;
            let diasPassados = 0;
            const totalDias = Math.ceil((fim - inicio) / (1000 * 60 * 60 * 24)) + 1;
            
            if (hoje < inicio) {
                progresso = 0;
                diasPassados = 0;
            } else if (hoje > fim) {
                progresso = 100;
                diasPassados = totalDias;
            } else {
                diasPassados = Math.ceil((hoje - inicio) / (1000 * 60 * 60 * 24)) + 1;
                progresso = (diasPassados / totalDias) * 100;
            }
            
            progressBar.style.width = `${progresso}%`;
            progressText.textContent = `${Math.round(progresso)}% concluído (${diasPassados}/${totalDias} dias)`;
        }
    }

    updateOrcamentoLiquido(novoOrcamento) {
        const orcamentoLiquidoElement = document.querySelectorAll('.orcamento-display .text-gray-800.font-bold.text-xl')[1];
        if (orcamentoLiquidoElement) {
            const valorFormatado = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(novoOrcamento);
            orcamentoLiquidoElement.textContent = valorFormatado;
            this.highlightUpdatedElement(orcamentoLiquidoElement);
        }
    }

    highlightUpdatedElement(element) {
        // Adicionar uma animação sutil para mostrar que o elemento foi atualizado
        element.style.transition = 'all 0.3s ease';
        element.style.backgroundColor = '#dcfce7'; // verde claro
        element.style.borderRadius = '4px';
        element.style.padding = '2px 4px';
        
        setTimeout(() => {
            element.style.backgroundColor = '';
            element.style.padding = '';
        }, 1000);
    }

    showNotification(message, type = 'info') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }
}

// Callback global para Google Maps API
window.initInlineEditMap = function() {
    console.log('Google Maps carregado com sucesso via callback - inicializando autocomplete');
    if (window.inlineEditManager) {
        window.inlineEditManager.initPlacesAutocomplete();
    }
};
