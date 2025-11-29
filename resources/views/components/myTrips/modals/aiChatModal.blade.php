<!-- Modal de Chat com IA -->
<div id="ai-chat-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div id="ai-chat-modal-panel" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 h-[90vh] flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        
        <!-- Header do Chat -->
        <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                    <img src="{{ asset('imgs/Bia.jpg') }}" alt="">
                </div>
                <div>   
                    <h3 class="text-xl font-bold text-black">Bia, sua assistente de viagem IA</h3>
                    <p class="text-purple-800 text-sm">Pronto para ajudar você a planejar melhor</p>
                </div>
            </div>
            <button id="close-ai-chat-btn" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Área de Mensagens -->
        <div id="chat-messages-container" class="flex-1 overflow-y-auto bg-gray-50 p-6 space-y-4">
            
            <!-- Mensagem de boas-vindas da IA -->
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('imgs/Bia.jpg') }}" alt="Bia">
                </div>
                <div class="flex-1">
                    <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm ">
                        <p class="text-gray-800 text-sm leading-relaxed">
                            Olá! 👋 Sou seu assistente de viagem com IA. Como posso ajudá-lo a planejar melhor sua viagem <strong>{{ $viagem->nome_viagem }}</strong>?
                        </p>
                        <p class="text-gray-600 text-xs mt-2">
                            Posso ajudar com sugestões de destinos, atividades, orçamento e muito mais!
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 ml-2 mt-1 block">Agora mesmo</span>
                </div>
            </div>

        </div>

        <!-- Área de Input -->
        <div class="bg-white border-t border-gray-200 p-4">
            <div class="flex items-end space-x-3">
                <div class="flex-1">
                    <textarea 
                        id="chat-message-input" 
                        rows="1"
                        placeholder="Digite sua mensagem..." 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none transition-all"
                        style="max-height: 120px;"
                    ></textarea>
                </div>
                <button 
                    id="send-message-btn" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                >
                    <span>Enviar</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            
            <!-- Sugestões de perguntas -->
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs text-gray-500 w-full mb-1">Sugestões rápidas:</span>
                <button class="suggestion-btn text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full transition-colors">
                    📍 Sugerir pontos turísticos
                </button>
                <button class="suggestion-btn text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full transition-colors">
                    🍽️ Restaurantes recomendados
                </button>
                <button class="suggestion-btn text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full transition-colors">
                    💰 Otimizar orçamento
                </button>
                <button class="suggestion-btn text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-full transition-colors">
                    📅 Melhor roteiro
                </button>
            </div>
        </div>

    </div>
</div>

<style>
    /* Scrollbar customizado para o chat */
    #chat-messages-container {
        scrollbar-width: thin;
        scrollbar-color: #a855f7 #f3f4f6;
    }
    
    #chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    #chat-messages-container::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }
    
    #chat-messages-container::-webkit-scrollbar-thumb {
        background: #a855f7;
        border-radius: 3px;
    }
    
    #chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: #9333ea;
    }

    /* Animação de digitação */
    @keyframes typing {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }

    .typing-indicator span {
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    /* Auto resize textarea */
    #chat-message-input {
        min-height: 48px;
    }

    /* Animação de pulso para o ícone do robô */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
        }
    }

    .animate-pulse {
        animation: pulse-glow 2s infinite;
    }

    /* Formatação de mensagens da IA */
    .ai-message-content {
        line-height: 1.6;
    }

    .ai-message-content strong {
        font-weight: 700;
        color: #7c3aed;
    }

    .ai-message-content ul {
        list-style-type: disc;
        margin-left: 1.25rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .ai-message-content ol {
        list-style-type: decimal;
        margin-left: 1.25rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .ai-message-content li {
        margin-bottom: 0.25rem;
        padding-left: 0.25rem;
    }

    .ai-message-content p {
        margin-bottom: 0.5rem;
    }

    .ai-message-content p:last-child {
        margin-bottom: 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const aiChatBtn = document.getElementById('ai-chat-btn');
    const aiChatModal = document.getElementById('ai-chat-modal');
    const aiChatModalPanel = document.getElementById('ai-chat-modal-panel');
    const closeAiChatBtn = document.getElementById('close-ai-chat-btn');
    const chatMessagesContainer = document.getElementById('chat-messages-container');
    const chatMessageInput = document.getElementById('chat-message-input');
    const sendMessageBtn = document.getElementById('send-message-btn');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    // Abrir modal
    function openAiChatModal() {
        aiChatModal.classList.remove('hidden');
        aiChatModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            aiChatModalPanel.classList.remove('scale-95', 'opacity-0');
            aiChatModalPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
        chatMessageInput.focus();
        
        // Carregar histórico do chat
        loadChatHistory();
    }

    // Fechar modal
    function closeAiChatModal() {
        aiChatModalPanel.classList.remove('scale-100', 'opacity-100');
        aiChatModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            aiChatModal.classList.add('hidden');
            aiChatModal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    }

    // Event listeners
    if (aiChatBtn) {
        aiChatBtn.addEventListener('click', openAiChatModal);
    }

    if (closeAiChatBtn) {
        closeAiChatBtn.addEventListener('click', closeAiChatModal);
    }

    // Fechar ao clicar fora
    aiChatModal.addEventListener('click', function(e) {
        if (e.target === aiChatModal) {
            closeAiChatModal();
        }
    });

    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !aiChatModal.classList.contains('hidden')) {
            closeAiChatModal();
        }
    });

    // Auto-resize textarea
    chatMessageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Enviar mensagem com Enter (Shift+Enter para nova linha)
    chatMessageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Botão enviar
    sendMessageBtn.addEventListener('click', sendMessage);

    // Sugestões rápidas
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            chatMessageInput.value = this.textContent.trim();
            chatMessageInput.focus();
            chatMessageInput.dispatchEvent(new Event('input'));
        });
    });

    // Função para enviar mensagem
    function sendMessage() {
        const message = chatMessageInput.value.trim();
        if (!message) return;

        // Adicionar mensagem do usuário ao chat
        addUserMessage(message);

        // Limpar input
        chatMessageInput.value = '';
        chatMessageInput.style.height = 'auto';

        // Desabilitar botão enquanto processa
        sendMessageBtn.disabled = true;

        // Mostrar indicador de digitação
        showTypingIndicator();

        // Enviar para o backend (será implementado depois)
        sendToBackend(message);
    }

    // Adicionar mensagem do usuário
    function addUserMessage(message) {
        const messageEl = document.createElement('div');
        messageEl.className = 'flex items-start space-x-3 justify-end';
        messageEl.innerHTML = `
            <div class="flex-1 flex flex-col items-end">
                <div class="bg-purple-600 text-white rounded-2xl rounded-tr-none p-4 shadow-sm max-w-xl">
                    <p class="text-sm leading-relaxed">${escapeHtml(message)}</p>
                </div>
                <span class="text-xs text-gray-400 mr-2 mt-1">${getCurrentTime()}</span>
            </div>
            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
        `;
        chatMessagesContainer.appendChild(messageEl);
        scrollToBottom();
    }

    // Adicionar mensagem da IA
    function addAiMessage(message) {
        const messageEl = document.createElement('div');
        messageEl.className = 'flex items-start space-x-3';
        messageEl.innerHTML = `
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('imgs/Bia.jpg') }}" alt="Bia">
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm ">
                    <div class="text-gray-800 text-sm ai-message-content">${window.processIaResponse(formatMessage(message))}</div>
                </div>
                <span class="text-xs text-gray-400 ml-2 mt-1 block">${getCurrentTime()}</span>
            </div>
        `;
        chatMessagesContainer.appendChild(messageEl);
        scrollToBottom();
    }

    // Mostrar indicador de digitação
    function showTypingIndicator() {
        const typingEl = document.createElement('div');
        typingEl.id = 'typing-indicator';
        typingEl.className = 'flex items-start space-x-3';
        typingEl.innerHTML = `
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('imgs/Bia.jpg') }}" alt="Bia">
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm ">
                    <div class="typing-indicator flex space-x-1">
                        <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                        <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                        <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                    </div>
                </div>
            </div>
        `;
        chatMessagesContainer.appendChild(typingEl);
        scrollToBottom();
    }

    // Remover indicador de digitação
    function removeTypingIndicator() {
        const typingEl = document.getElementById('typing-indicator');
        if (typingEl) {
            typingEl.remove();
        }
    }

    // Enviar para backend com streaming SSE
    function sendToBackend(message) {
        // Usar EventSource para streaming (SSE)
        const useStreaming = false; // DESABILITADO TEMPORARIAMENTE PARA DEBUG
        
        if (useStreaming) {
            sendWithStreaming(message);
        } else {
            sendWithoutStreaming(message);
        }
    }

    // Enviar com streaming (Server-Sent Events)
    function sendWithStreaming(message) {
        const url = '{{ route("ai.chat.stream") }}';
        
        // Criar mensagem vazia da IA que será preenchida gradualmente
        const aiMessageEl = createEmptyAiMessage();
        const textContainer = aiMessageEl.querySelector('.ai-message-text');
        
        // Fazer requisição POST e depois abrir SSE
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'text/event-stream'
            },
            body: JSON.stringify({
                message: message,
                trip_id: window.currentTripId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            
            function processStream() {
                reader.read().then(({ done, value }) => {
                    if (done) {
                        removeTypingIndicator();
                        sendMessageBtn.disabled = false;
                        return;
                    }
                    
                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop(); // Manter a última linha incompleta
                    
                    lines.forEach(line => {
                        if (line.startsWith('event: ')) {
                            const event = line.substring(7).trim();
                            
                            if (event === 'done') {
                                removeTypingIndicator();
                                sendMessageBtn.disabled = false;
                            }
                        } else if (line.startsWith('data: ')) {
                            try {
                                const jsonStr = line.substring(6);
                                const data = JSON.parse(jsonStr);
                                
                                if (data.content) {
                                    removeTypingIndicator();
                                    // Adicionar texto gradualmente
                                    const currentText = textContainer.textContent;
                                    textContainer.textContent = currentText + data.content;
                                    scrollToBottom();
                                }
                            } catch (e) {
                                console.error('Erro ao parsear SSE:', e);
                            }
                        }
                    });
                    
                    processStream();
                });
            }
            
            processStream();
        })
        .catch(error => {
            console.error('Erro no streaming:', error);
            removeTypingIndicator();
            aiMessageEl.remove();
            addAiMessage('Desculpe, ocorreu um erro. Tentando novamente sem streaming...');
            sendWithoutStreaming(message);
        });
    }

    // Enviar sem streaming (HTTP request normal)
    function sendWithoutStreaming(message) {
        // Enviar requisição AJAX para o backend
        fetch('{{ route("ai.chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                trip_id: window.currentTripId
            })
        })
        .then(response => response.json())
        .then(data => {
            removeTypingIndicator();
            
            if (data.success) {
                addAiMessage(data.message);
            } else {
                addAiMessage('Desculpe, houve um erro ao processar sua mensagem. Tente novamente.');
            }
            
            sendMessageBtn.disabled = false;
        })
        .catch(error => {
            console.error('Erro ao enviar mensagem:', error);
            removeTypingIndicator();
            addAiMessage('Desculpe, não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.');
            sendMessageBtn.disabled = false;
        });
    }

    // Criar elemento de mensagem vazia da IA (para streaming)
    function createEmptyAiMessage() {
        const messageEl = document.createElement('div');
        messageEl.className = 'flex items-start space-x-3';
        messageEl.innerHTML = `
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('imgs/Bia.jpg') }}" alt="Bia">
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm">
                    <p class="ai-message-text text-gray-800 text-sm leading-relaxed"></p>
                </div>
                <span class="text-xs text-gray-400 ml-2 mt-1 block">${getCurrentTime()}</span>
            </div>
        `;
        chatMessagesContainer.appendChild(messageEl);
        scrollToBottom();
        return messageEl;
    }

    // Funções auxiliares
    function scrollToBottom() {
        setTimeout(() => {
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }, 100);
    }

    function getCurrentTime() {
        return new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Formatar mensagem da IA com suporte a negrito e listas
    function formatMessage(text) {
        // Escapar HTML primeiro
        let formatted = escapeHtml(text);
        // Converter [[nome]] em link clicável
        formatted = formatted.replace(/\[\[(.*?)\]\]/g, (match, placeName) => {
            return `<a href="#" class="ia-place-link text-purple-600 hover:underline" data-place-name="${placeName}">${placeName}</a>`;
        });
        // Converter **texto** em <strong>texto</strong>
        formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // Detectar e converter listas numeradas (1. item, 2. item, etc)
        const numberedListRegex = /^(\d+\.\s.+)$/gm;
        if (numberedListRegex.test(formatted)) {
            const lines = formatted.split('\n');
            let inList = false;
            let result = [];
            lines.forEach(line => {
                const numberedMatch = line.match(/^(\d+)\.\s(.+)$/);
                if (numberedMatch) {
                    if (!inList) {
                        result.push('<ol>');
                        inList = true;
                    }
                    result.push(`<li>${numberedMatch[2]}</li>`);
                } else {
                    if (inList) {
                        result.push('</ol>');
                        inList = false;
                    }
                    if (line.trim()) {
                        result.push(`<p>${line}</p>`);
                    }
                }
            });
            if (inList) {
                result.push('</ol>');
            }
            formatted = result.join('');
        }
        // Detectar e converter listas com marcadores (- item, * item, • item)
        else {
            const bulletListRegex = /^([\-\*•]\s.+)$/gm;
            if (bulletListRegex.test(formatted)) {
                const lines = formatted.split('\n');
                let inList = false;
                let result = [];
                lines.forEach(line => {
                    const bulletMatch = line.match(/^[\-\*•]\s(.+)$/);
                    if (bulletMatch) {
                        if (!inList) {
                            result.push('<ul>');
                            inList = true;
                        }
                        result.push(`<li>${bulletMatch[1]}</li>`);
                    } else {
                        if (inList) {
                            result.push('</ul>');
                            inList = false;
                        }
                        if (line.trim()) {
                            result.push(`<p>${line}</p>`);
                        }
                    }
                });
                if (inList) {
                    result.push('</ul>');
                }
                formatted = result.join('');
            } else {
                // Sem lista, apenas quebrar em parágrafos
                formatted = formatted.split('\n\n').map(p => {
                    return p.trim() ? `<p>${p.replace(/\n/g, '<br>')}</p>` : '';
                }).join('');
            }
        }
        return formatted;
    }

    // Carregar histórico do chat
    function loadChatHistory() {
        fetch('{{ route("ai.chat.history") }}?trip_id=' + window.currentTripId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages && data.messages.length > 0) {
                // Remover mensagem de boas-vindas se houver histórico
                const welcomeMessage = chatMessagesContainer.querySelector('.flex.items-start.space-x-3:first-child');
                if (welcomeMessage) {
                    welcomeMessage.remove();
                }

                // Adicionar mensagens do histórico
                data.messages.forEach(msg => {
                    if (msg.role === 'user') {
                        addUserMessageWithTimestamp(msg.content, msg.timestamp);
                    } else if (msg.role === 'assistant') {
                        addAiMessageWithTimestamp(msg.content, msg.timestamp);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar histórico:', error);
        });
    }

    // Adicionar mensagem do usuário com timestamp customizado
    function addUserMessageWithTimestamp(message, timestamp) {
        const messageEl = document.createElement('div');
        messageEl.className = 'flex items-start space-x-3 justify-end';
        messageEl.innerHTML = `
            <div class="flex-1 flex flex-col items-end">
                <div class="bg-purple-600 text-white rounded-2xl rounded-tr-none p-4 shadow-sm max-w-xl">
                    <p class="text-sm leading-relaxed">${escapeHtml(message)}</p>
                </div>
                <span class="text-xs text-gray-400 mr-2 mt-1">${timestamp}</span>
            </div>
            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
        `;
        chatMessagesContainer.appendChild(messageEl);
        scrollToBottom();
    }

    // Adicionar mensagem da IA com timestamp customizado
    function addAiMessageWithTimestamp(message, timestamp) {
        const messageEl = document.createElement('div');
        messageEl.className = 'flex items-start space-x-3';
        messageEl.innerHTML = `
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('imgs/Bia.jpg') }}" alt="Bia">
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm">
                    <div class="text-gray-800 text-sm ai-message-content">${formatMessage(message)}</div>
                </div>
                <span class="text-xs text-gray-400 ml-2 mt-1 block">${timestamp}</span>
            </div>
        `;
        chatMessagesContainer.appendChild(messageEl);
        scrollToBottom();
    }
});
</script>
