/**
 * Inicialização do Autocomplete para Hotéis
 * @author AgendAI Development Team
 * @version 1.0.0
 */

// Variável global para armazenar a instância do autocomplete
let hotelLocationAutocomplete = null;

/**
 * Callback do Google Maps API - chamado automaticamente quando a API carrega
 */
window.initHotelsMap = function() {
    console.log('Google Maps API carregada - Inicializando autocomplete de hotéis');
    
    // Verificar se a classe PlacesAutocomplete está disponível
    if (typeof window.PlacesAutocomplete === 'undefined') {
        console.error('PlacesAutocomplete class não encontrada. Certifique-se de que placesAutocomplete.js foi carregado.');
        return;
    }
    
    // Verificar se o input existe
    const hotelQueryInput = document.getElementById('hotel-query');
    if (!hotelQueryInput) {
        console.warn('Input #hotel-query não encontrado. Autocomplete não será inicializado.');
        return;
    }
    
    // Inicializar autocomplete para o campo de destino do hotel
    hotelLocationAutocomplete = new window.PlacesAutocomplete('hotel-query', {
        types: ['(cities)'], // Buscar apenas cidades
        language: 'pt-BR',
        componentRestrictions: {}, // Sem restrição de país - buscar no mundo todo
        placeholder: 'Ex: Rio de Janeiro',
        debounceMs: 300,
        minChars: 2,
        iconClass: 'fa-map-marker-alt',
        iconColor: 'text-pink-500',
        errorMessage: 'Por favor, selecione uma cidade válida da lista de sugestões',
        onSelect: function(place) {
            console.log('Destino selecionado:', place.description);
            
            // Aqui você pode adicionar lógica adicional quando um local é selecionado
            // Por exemplo, preencher automaticamente campos relacionados
        }
    });
    
    console.log('Autocomplete de hotéis inicializado com sucesso');
};

/**
 * Inicialização quando o DOM estiver pronto
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado - Configurando formulário de hotéis');
    
    // Validação do formulário
    const hotelForm = document.getElementById('hotel-search-form');
    
    if (!hotelForm) {
        console.warn('Formulário de hotéis não encontrado');
        return;
    }
    
    // Interceptar o submit para validar o autocomplete
    hotelForm.addEventListener('submit', function(e) {
        // Verificar se o autocomplete foi inicializado
        if (!hotelLocationAutocomplete) {
            console.warn('Autocomplete ainda não foi inicializado');
            return; // Permitir submit se autocomplete não estiver carregado
        }
        
        // Validar o campo de destino
        const isValid = hotelLocationAutocomplete.validate();
        
        if (!isValid) {
            e.preventDefault();
            
            // Focar no input inválido
            const hotelQueryInput = document.getElementById('hotel-query');
            if (hotelQueryInput) {
                hotelQueryInput.focus();
                
                // Scroll suave até o campo
                hotelQueryInput.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            console.warn('Formulário de hotéis não submetido: campo de destino inválido');
            return false;
        }
        
        console.log('Formulário de hotéis validado com sucesso');
    });
    
    console.log('Validação do formulário de hotéis configurada');
});

/**
 * Função para exibir notificações
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo de notificação (success, error, info, warning)
 */
function showHotelNotification(message, type = 'info') {
    // Se existir Toast global, usar
    if (window.Toast) {
        switch(type) {
            case 'success':
                Toast.success(message);
                break;
            case 'error':
                Toast.error(message);
                break;
            case 'warning':
                Toast.warning(message);
                break;
            default:
                Toast.info(message);
        }
        return;
    }
    
    // Fallback: alert simples
    alert(message);
}

/**
 * Função auxiliar para resetar o formulário
 */
function resetHotelForm() {
    const form = document.getElementById('hotel-search-form');
    if (form) {
        form.reset();
    }
    
    if (hotelLocationAutocomplete) {
        hotelLocationAutocomplete.reset();
    }
}

// Exportar para uso global
window.hotelLocationAutocomplete = hotelLocationAutocomplete;
window.showHotelNotification = showHotelNotification;
window.resetHotelForm = resetHotelForm;

console.log('hotels-autocomplete.js carregado com sucesso');
