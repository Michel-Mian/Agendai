// Utilitários globais para prevenir erros JavaScript

// Função para verificar se estamos na página correta
window.isOnPage = function(pageIdentifiers) {
    if (typeof pageIdentifiers === 'string') {
        pageIdentifiers = [pageIdentifiers];
    }
    
    return pageIdentifiers.some(identifier => {
        return document.getElementById(identifier) !== null ||
               document.querySelector(identifier) !== null ||
               window.location.pathname.includes(identifier);
    });
};

// Função para executar código apenas se elementos existirem
window.safeExecute = function(elementId, callback) {
    const element = document.getElementById(elementId);
    if (element && typeof callback === 'function') {
        callback(element);
    }
};

// Função para adicionar listeners seguros
window.safeAddEventListener = function(elementId, event, callback) {
    const element = document.getElementById(elementId);
    if (element && typeof callback === 'function') {
        element.addEventListener(event, callback);
    }
};

// Handler global de erro para prevenir crashes
window.addEventListener('error', function(e) {
    console.warn('Erro JavaScript capturado:', {
        message: e.message,
        source: e.filename,
        line: e.lineno,
        column: e.colno
    });
    
    // Não prevenir o comportamento padrão, apenas logar
    return false;
});

// Handler para promises rejeitadas
window.addEventListener('unhandledrejection', function(e) {
    console.warn('Promise rejeitada não tratada:', e.reason);
    
    // Prevenir que apareça no console como erro não tratado
    e.preventDefault();
});

// Inicialização segura para Google Maps
window.safeInitGoogleMaps = function(callback) {
    if (typeof google !== 'undefined' && google.maps) {
        if (typeof callback === 'function') {
            callback();
        }
    } else {
        console.warn('Google Maps não está disponível');
    }
};