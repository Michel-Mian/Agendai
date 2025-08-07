<div id="add-viajante-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="add-viajante-modal-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" aria-hidden="true"></div>
    
    <div id="add-viajante-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
                        <div class="bg-green-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Adicionar Viajante</h3>
                        <p class="text-green-100 text-base">Inclua um novo membro à viagem</p>
                    </div>
                </div>
                <button id="close-add-viajante-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-8">
            @if ($errors->has('nome_viajante') || $errors->has('idade_viajante'))
                <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center space-x-3 mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                        <span class="font-medium text-red-800 text-lg">Erro na validação</span>
                    </div>
                    @if ($errors->has('nome_viajante'))
                        <div class="text-red-600 text-base">{{ $errors->first('nome_viajante') }}</div>
                    @endif
                    @if ($errors->has('idade_viajante'))
                        <div class="text-red-600 text-base">{{ $errors->first('idade_viajante') }}</div>
                    @endif
                </div>
            @endif
            
            <form id="add-viajante-form" method="POST" action="{{ route('viajantes.store') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
                
                <div class="space-y-3">
                    <label for="nome_viajante" class="flex items-center space-x-3 text-base font-semibold text-gray-700">
                        <i class="fas fa-user text-green-500 text-xl"></i>
                        <span>Nome do viajante</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="nome_viajante" 
                            name="nome_viajante" 
                            maxlength="100" 
                            class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base"
                            placeholder="Digite o nome completo..." 
                            required
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <span class="text-sm text-gray-400 bg-white px-2 py-1 rounded-full border">
                                <span id="nome_viajante_count">0</span>/100
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <label for="idade_viajante" class="flex items-center space-x-3 text-base font-semibold text-gray-700">
                        <i class="fas fa-birthday-cake text-green-500 text-xl"></i>
                        <span>Idade</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="idade_viajante" 
                            name="idade_viajante" 
                            min="0" 
                            max="127" 
                            class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base"
                            placeholder="Digite a idade..." 
                            required
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-calendar-alt text-gray-400 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center space-x-2">
                        <i class="fas fa-info-circle text-base"></i>
                        <span>Idade entre 0 e 127 anos</span>
                    </p>
                </div>
                
                <div class="flex space-x-4 pt-6">
                    <button 
                        type="button" 
                        id="cancel-add-viajante-btn"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 text-lg"
                    >
                        <i class="fas fa-times text-lg"></i>
                        <span>Cancelar</span>
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 transform hover:scale-105 text-lg"
                    >
                        <i class="fas fa-check text-lg"></i>
                        <span>Confirmar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const openAddViajanteModalBtn = document.getElementById('open-add-viajante-modal-btn');
    const closeAddViajanteModalBtn = document.getElementById('close-add-viajante-modal-btn');
    const cancelAddViajanteBtn = document.getElementById('cancel-add-viajante-btn');
    const addViajanteModal = document.getElementById('add-viajante-modal');
    const addViajanteModalPanel = document.getElementById('add-viajante-modal-panel');
    const addViajanteModalOverlay = document.getElementById('add-viajante-modal-overlay');

    const openAddViajanteModal = () => {
        addViajanteModal.classList.remove('hidden');
        addViajanteModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            addViajanteModalPanel.classList.remove('scale-95', 'opacity-0');
            addViajanteModalPanel.classList.add('scale-100', 'opacity-100');
            document.getElementById('nome_viajante').focus();
        }, 10);
    };

    const closeAddViajanteModal = () => {
        if (!addViajanteModalPanel) return;
        addViajanteModalPanel.classList.remove('scale-100', 'opacity-100');
        addViajanteModalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            addViajanteModal.classList.add('hidden');
            addViajanteModal.classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('add-viajante-form').reset();
            document.getElementById('nome_viajante_count').textContent = '0';
            const underageContainer = document.getElementById('underage-options-container');
            if (underageContainer) {
                underageContainer.remove();
            }
            const customAlert = document.getElementById('no-adult-alert');
            if (customAlert) {
                customAlert.remove();
            }
        }, 300);
    };

    if (openAddViajanteModalBtn) {
        openAddViajanteModalBtn.addEventListener('click', openAddViajanteModal);
    }
    if (closeAddViajanteModalBtn) closeAddViajanteModalBtn.addEventListener('click', closeAddViajanteModal);
    if (cancelAddViajanteBtn) cancelAddViajanteBtn.addEventListener('click', closeAddViajanteModal);
    if (addViajanteModalOverlay) addViajanteModalOverlay.addEventListener('click', closeAddViajanteModal);

    const nomeViajanteInput = document.getElementById('nome_viajante');
    const nomeViajanteCount = document.getElementById('nome_viajante_count');
    if (nomeViajanteInput && nomeViajanteCount) {
        nomeViajanteInput.addEventListener('input', function() {
            const count = this.value.length;
            nomeViajanteCount.textContent = count;
            
            if (count > 80) {
                nomeViajanteCount.parentElement.classList.add('text-red-500');
                nomeViajanteCount.parentElement.classList.remove('text-gray-400');
            } else {
                nomeViajanteCount.parentElement.classList.remove('text-red-500');
                nomeViajanteCount.parentElement.classList.add('text-gray-400');
            }
        });
        nomeViajanteCount.textContent = nomeViajanteInput.value.length;
    }

    // Escape fecha o modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (addViajanteModal && !addViajanteModal.classList.contains('hidden')) {
                closeAddViajanteModal();
            }
        }
    });

    // --- Lógica para viajantes menores de 18 anos ---
    const addViajanteForm = document.getElementById('add-viajante-form');
    const idadeViajanteInput = document.getElementById('idade_viajante');
    const submitButton = addViajanteForm.querySelector('button[type="submit"]');

    // Event listener para o botão de confirmar do formulário
    submitButton.addEventListener('click', function(event) {
        // Obtenha o valor da idade
        const idade = parseInt(idadeViajanteInput.value, 10);

        // Encontre o container existente para as opções de menor de idade
        let underageOptionsContainer = document.getElementById('underage-options-container');

        if (idade < 18) {
            // Se o viajante for menor de 18 anos
                if (!underageOptionsContainer) {
                    event.preventDefault(); 

                let maioresDe18Options = '<option value="">Selecione o responsável legal</option>';
                @php
                    $viajantesMaioresDe18 = $viagem->viajantes->filter(function ($viajante) {
                        return $viajante->idade >= 18;
                    });
                @endphp
                
                @if($viajantesMaioresDe18->isEmpty())
                    const ageInputContainer = document.querySelector('#idade_viajante').closest('.space-y-3');
                    let customAlert = document.getElementById('no-adult-alert');
                    
                    if (!customAlert) {
                        customAlert = document.createElement('div');
                        customAlert.id = 'no-adult-alert';
                        customAlert.className = 'mt-3 p-4 bg-red-50 border border-red-200 rounded-lg';
                        customAlert.innerHTML = `
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                                <div>
                                    <p class="font-medium text-red-800 text-base">Responsável legal necessário</p>
                                    <p class="text-red-600 text-sm mt-1">Para cadastrar um menor de 18 anos, é necessário primeiro adicionar um viajante maior de idade que será o responsável legal.</p>
                                </div>
                            </div>
                        `;
                        ageInputContainer.appendChild(customAlert);
                    }
                    
                    return;
                @endif
                
                @foreach($viajantesMaioresDe18 as $viajante)
                    maioresDe18Options += `<option value="{{ $viajante->pk_id_viajante }}">{{ $viajante->nome }} ({{ $viajante->idade }} anos)</option>`;
                @endforeach

                underageOptionsContainer = document.createElement('div');
                underageOptionsContainer.id = 'underage-options-container';
                underageOptionsContainer.className = 'space-y-3 mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg';
                
                underageOptionsContainer.innerHTML = `
                    <p class="text-sm font-semibold text-yellow-800 flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        <span>Informações adicionais para viajantes menores de 18 anos:</span>
                    </p>
                    <div class="flex items-center space-x-3">
                        <label for="responsavel_legal" class="sr-only">Responsável Legal</label>
                        <select 
                            id="responsavel_legal" 
                            name="responsavel_legal" 
                            class="w-full border-2 border-gray-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-gray-50 focus:bg-white text-base" 
                            required
                        >
                            ${maioresDe18Options}
                        </select>
                    </div>
                `;
                
                const lastSpaceYDiv = addViajanteForm.querySelector('.space-y-3:last-of-type');
                if (lastSpaceYDiv) {
                    lastSpaceYDiv.parentNode.insertBefore(underageOptionsContainer, lastSpaceYDiv.nextElementSibling);
                } else {
                    const actionButtonsDiv = addViajanteForm.querySelector('.flex.space-x-4.pt-6');
                    if (actionButtonsDiv) {
                        actionButtonsDiv.parentNode.insertBefore(underageOptionsContainer, actionButtonsDiv);
                    } else {
                        addViajanteForm.appendChild(underageOptionsContainer);
                    }
                }
            } else {
                const responsavelLegalSelect = document.getElementById('responsavel_legal');
                if (responsavelLegalSelect && responsavelLegalSelect.value === '') {
                    event.preventDefault();
                    alert('Por favor, selecione o responsável legal.');
                }
            }
        } else {
            if (underageOptionsContainer) {
                underageOptionsContainer.remove();
            }
        }
    });

    idadeViajanteInput.addEventListener('input', function() {
        const idade = parseInt(this.value, 10);
        const underageOptionsContainer = document.getElementById('underage-options-container');
        const customAlert = document.getElementById('no-adult-alert');

        if (idade >= 18) {
            if (underageOptionsContainer) {
                underageOptionsContainer.remove();
            }
            if (customAlert) {
                customAlert.remove();
            }
        }
    });
</script>