/**
 * Inicialização do Autocomplete para Veículos
 * @author AgendAI Development Team
 * @version 1.0.0
 */

// Variável global para armazenar a instância do autocomplete
let vehicleLocationAutocomplete = null;

/**
 * Callback do Google Maps API - chamado automaticamente quando a API carrega
 */
window.initVehiclesMap = function() {
    console.log('Google Maps API carregada - Inicializando autocomplete de veículos');
    
    // Verificar se a classe PlacesAutocomplete está disponível
    if (typeof window.PlacesAutocomplete === 'undefined') {
        console.error('PlacesAutocomplete class não encontrada. Certifique-se de que placesAutocomplete.js foi carregado.');
        return;
    }
    
    // Verificar se o input existe
    const localInput = document.getElementById('local_retirada');
    if (!localInput) {
        console.warn('Input #local_retirada não encontrado. Autocomplete não será inicializado.');
        return;
    }
    
    // Inicializar autocomplete para o campo de local de retirada
    vehicleLocationAutocomplete = new window.PlacesAutocomplete('local_retirada', {
        types: ['(cities)'], // Buscar apenas cidades
        language: 'pt-BR',
        componentRestrictions: {}, // Sem restrição de país - buscar no mundo todo
        placeholder: 'Digite a cidade de retirada...',
        debounceMs: 300,
        minChars: 2,
        iconClass: 'fa-map-marker-alt',
        iconColor: 'text-blue-500',
        errorMessage: 'Por favor, selecione uma cidade válida da lista de sugestões',
        onSelect: function(place) {
            console.log('Local selecionado:', place.description);
            
            // Aqui você pode adicionar lógica adicional quando um local é selecionado
            // Por exemplo, preencher automaticamente campos relacionados
        }
    });
    
    console.log('Autocomplete de veículos inicializado com sucesso');
};

/**
 * Inicialização quando o DOM estiver pronto
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado - Configurando formulário de veículos');
    
    // Validação do formulário
    const vehicleForm = document.getElementById('vehicle-search-form');
    
    if (!vehicleForm) {
        console.warn('Formulário de veículos não encontrado');
        return;
    }
    
    // Interceptar o submit para validar o autocomplete
    vehicleForm.addEventListener('submit', function(e) {
        // Verificar se o autocomplete foi inicializado
        if (!vehicleLocationAutocomplete) {
            console.warn('Autocomplete ainda não foi inicializado');
            return; // Permitir submit se autocomplete não estiver carregado
        }
        
        // Validar o campo de local
        const isValid = vehicleLocationAutocomplete.validate();
        
        if (!isValid) {
            e.preventDefault();
            
            // Focar no input inválido
            const localInput = document.getElementById('local_retirada');
            if (localInput) {
                localInput.focus();
                
                // Scroll suave até o campo
                localInput.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            // Mostrar notificação
            showVehicleNotification(
                'Por favor, selecione uma cidade válida da lista de sugestões',
                'error'
            );
            
            return false;
        }
        
        // Se chegou aqui, a validação passou
        console.log('Formulário validado com sucesso');
    });
    
    // Validação das datas
    setupDateValidation();
});

/**
 * Configurar validação de datas
 */
function setupDateValidation() {
    const dataRetirada = document.getElementById('data_retirada');
    const dataDevolucao = document.getElementById('data_devolucao');
    
    if (!dataRetirada || !dataDevolucao) {
        return;
    }
    
    // Quando a data de retirada mudar, atualizar o mínimo da data de devolução
    dataRetirada.addEventListener('change', function() {
        const retiradaValue = this.value;
        if (retiradaValue) {
            dataDevolucao.min = retiradaValue;
            
            // Se a data de devolução for anterior à de retirada, limpar
            if (dataDevolucao.value && dataDevolucao.value < retiradaValue) {
                dataDevolucao.value = '';
                showVehicleNotification(
                    'A data de devolução deve ser posterior à data de retirada',
                    'warning'
                );
            }
        }
    });
    
    // Validar ao mudar a data de devolução
    dataDevolucao.addEventListener('change', function() {
        const retiradaValue = dataRetirada.value;
        const devolucaoValue = this.value;
        
        if (retiradaValue && devolucaoValue && devolucaoValue < retiradaValue) {
            this.value = '';
            showVehicleNotification(
                'A data de devolução deve ser posterior à data de retirada',
                'error'
            );
        }
    });
}

/**
 * Mostra notificação para o usuário
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo da notificação (success, error, warning, info)
 */
function showVehicleNotification(message, type = 'info') {
    // Remover notificações anteriores
    const existingNotifications = document.querySelectorAll('.vehicle-notification');
    existingNotifications.forEach(n => n.remove());
    
    // Criar notificação
    const notification = document.createElement('div');
    notification.className = 'vehicle-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 400px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        animation: slideInRight 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    // Definir cor baseada no tipo
    const colors = {
        success: '#10B981',
        error: '#EF4444',
        warning: '#F59E0B',
        info: '#3B82F6'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Adicionar ícone e mensagem
    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
    `;
    
    // Adicionar ao DOM
    document.body.appendChild(notification);
    
    // Adicionar animação CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Remover após 5 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
            style.remove();
        }, 300);
    }, 5000);
}

/**
 * Função auxiliar para resetar o formulário
 */
function resetVehicleForm() {
    const form = document.getElementById('vehicle-search-form');
    if (form) {
        form.reset();
    }
    
    if (vehicleLocationAutocomplete) {
        vehicleLocationAutocomplete.reset();
    }
}

// Expor funções globalmente para uso em outros scripts
window.vehicleLocationAutocomplete = vehicleLocationAutocomplete;
window.showVehicleNotification = showVehicleNotification;
window.resetVehicleForm = resetVehicleForm;

console.log('vehicles-autocomplete.js carregado com sucesso');
