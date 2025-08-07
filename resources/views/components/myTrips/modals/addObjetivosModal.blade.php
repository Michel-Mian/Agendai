<div id="add-objetivo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="add-objetivo-modal-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" aria-hidden="true"></div>

    <div id="add-objetivo-modal-panel" class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="bg-purple-600 px-8 py-6"> {{-- Increased padding --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4"> {{-- Increased space-x --}}
                    <div class="bg-white/20 rounded-lg p-3"> {{-- Increased padding --}}
                        <i class="fas fa-bullseye text-white text-2xl"></i> {{-- Increased icon size --}}
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Adicionar Objetivo</h3> {{-- Text color changed to white for contrast on purple, increased text size --}}
                        <p class="text-purple-100 text-base">Escolha um objetivo para sua viagem</p> {{-- Text color adjusted for contrast, increased text size --}}
                    </div>
                </div>
                <button id="close-add-objetivo-modal-btn" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-lg transition-colors"> {{-- Increased padding --}}
                    <i class="fas fa-times text-xl"></i> {{-- Increased icon size --}}
                </button>
            </div>
        </div>
        
        <div class="p-8"> {{-- Increased padding --}}
            @if ($errors->has('nome_objetivo'))
                <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg"> {{-- Increased padding and margin --}}
                    <div class="flex items-center space-x-3 mb-3"> {{-- Increased space-x and margin --}}
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i> {{-- Increased icon size --}}
                        <span class="font-medium text-red-800 text-lg">Erro na validação</span> {{-- Increased text size --}}
                    </div>
                    <div class="text-red-600 text-base">{{ $errors->first('nome_objetivo') }}</div> {{-- Increased text size --}}
                </div>
            @endif
            
            <form id="add-objetivo-form" method="POST" action="{{ route('objetivos.store') }}" class="space-y-8"> {{-- Increased space-y --}}
                @csrf
                <input type="hidden" name="viagem_id" value="{{ $viagem->pk_id_viagem }}">
                
                <input type="hidden" id="selected_nome_objetivo" name="nome_objetivo" value="">

                <div class="space-y-6"> {{-- Increased space-y --}}
                    <label class="flex items-center space-x-3 text-base font-semibold text-gray-700"> {{-- Increased space-x and text size --}}
                        <i class="fas fa-th-list text-purple-500 text-xl"></i> {{-- Increased icon size --}}
                        <span>Selecione uma categoria de objetivo </span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        
                        @php
                            // Create a simple array of existing objective names for easy checking
                            $existingObjectiveNames = $objetivos->pluck('nome')->toArray();
                        @endphp

                         @foreach([
                            ['name' => 'Cultura e história', 'svg_icon' => '<img src="' . asset('imgs/objectives/open-book.png') . '" alt="Cultura e história" class="w-10 h-10 mb-2">'],
                            ['name' => 'Gastronomia', 'svg_icon' => '<img src="' . asset('imgs/objectives/restaurant.png') . '" alt="Gastronomia" class="w-10 h-10 mb-2">'],
                            ['name' => 'Natureza', 'svg_icon' => '<img src="' . asset('imgs/objectives/landscape.png') . '" alt="Natureza" class="w-10 h-10 mb-2">'],
                            ['name' => 'Aventura', 'svg_icon' => '<img src="' . asset('imgs/objectives/hiking.png') . '" alt="Aventura" class="w-10 h-10 mb-2">'],
                            ['name' => 'Praia', 'svg_icon' => '<img src="' . asset('imgs/objectives/beach-umbrella.png') . '" alt="Praia" class="w-10 h-10 mb-2">'],
                            ['name' => 'Vida noturna', 'svg_icon' => '<img src="' . asset('imgs/objectives/moon.png') . '" alt="Vida noturna" class="w-10 h-10 mb-2">'],
                            ['name' => 'Compras', 'svg_icon' => '<img src="' . asset('imgs/objectives/shopping-cart.png') . '" alt="Compras" class="w-10 h-10 mb-2">'],
                            ['name' => 'Arte e museus', 'svg_icon' => '<img src="' . asset('imgs/objectives/museum.png') . '" alt="Arte e museus" class="w-10 h-10 mb-2">'],
                        ] as $objectiveOption)
                            @php
                                $isDisabled = in_array($objectiveOption['name'], $existingObjectiveNames);
                                $buttonClasses = 'objective-option-btn flex flex-col items-center justify-center p-5 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-700 text-base font-medium'; // Increased padding, text size
                                $buttonClasses .= $isDisabled ? ' opacity-50 cursor-not-allowed' : ' hover:border-purple-400 hover:shadow-md transition-all duration-200';
                            @endphp
                            <button
                                type="button"
                                class="{{ $buttonClasses }}"
                                data-objective="{{ $objectiveOption['name'] }}"
                                {{ $isDisabled ? 'disabled' : '' }}
                            >
                                {!! $objectiveOption['svg_icon'] !!} {{-- Render IMG directly --}}
                                {{ $objectiveOption['name'] }}
                            </button>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 flex items-center space-x-2"> {{-- Increased text size and space-x --}}
                        <i class="fas fa-lightbulb text-base"></i> {{-- Increased icon size --}}
                        <span>Selecione a categoria que melhor representa seu objetivo principal. Opções desativadas já foram adicionadas.</span>
                    </p>
                </div>
                
                <div class="flex space-x-4 pt-6"> {{-- Increased space-x and padding-top --}}
                    <button 
                        type="button" 
                        id="cancel-add-objetivo-btn"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 text-lg" {{-- Increased padding and text size --}}
                    >
                        <i class="fas fa-times text-lg"></i> {{-- Increased icon size --}}
                        <span>Cancelar</span>
                    </button>
                    <button 
                        type="submit" 
                        id="submit-objective-btn"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 transform hover:scale-105 text-lg" {{-- Removed gradient, increased padding and text size --}}
                    >
                        <i class="fas fa-check text-lg"></i> {{-- Increased icon size --}}
                        <span>Confirmar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openAddObjetivoModalBtn = document.getElementById('open-add-objetivo-modal-btn');
        const closeAddObjetivoModalBtn = document.getElementById('close-add-objetivo-modal-btn');
        const cancelAddObjetivoBtn = document.getElementById('cancel-add-objetivo-btn');
        const addObjetivoModal = document.getElementById('add-objetivo-modal');
        const addObjetivoModalPanel = document.getElementById('add-objetivo-modal-panel');
        const addObjetivoModalOverlay = document.getElementById('add-objetivo-modal-overlay');
        const selectedNomeObjetivoInput = document.getElementById('selected_nome_objetivo'); 
        const objectiveOptionBtns = document.querySelectorAll('.objective-option-btn'); 
        const submitObjectiveBtn = document.getElementById('submit-objective-btn'); 

        let selectedObjective = null; 

        // Function to update the submit button state
        const updateSubmitButtonState = () => {
            if (selectedObjective) {
                submitObjectiveBtn.classList.remove('bg-purple-200', 'text-purple-800', 'cursor-not-allowed');
                submitObjectiveBtn.classList.add('bg-purple-600', 'hover:bg-purple-700', 'text-white', 'cursor-pointer');
                submitObjectiveBtn.disabled = false;
            } else {
                submitObjectiveBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'text-white', 'cursor-pointer');
                submitObjectiveBtn.classList.add('bg-purple-200', 'text-purple-800', 'cursor-not-allowed');
                submitObjectiveBtn.disabled = true;
            }
        };

        // Handle clicks on objective options
        objectiveOptionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Only proceed if the button is NOT disabled
                if (this.disabled) {
                    return; 
                }

                // Remove 'selected' style from previously selected button
                const currentSelected = document.querySelector('.objective-option-btn.border-purple-600');
                if (currentSelected) {
                    currentSelected.classList.remove('border-purple-600', 'ring-2', 'ring-purple-200', 'shadow-md');
                }

                // Add 'selected' style to the clicked button
                this.classList.add('border-purple-600', 'ring-2', 'ring-purple-200', 'shadow-md');
                
                selectedObjective = this.dataset.objective;
                selectedNomeObjetivoInput.value = selectedObjective; 
                updateSubmitButtonState(); 
            });
        });

        const openAddObjetivoModal = () => {
            addObjetivoModal.classList.remove('hidden');
            addObjetivoModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                addObjetivoModalPanel.classList.remove('scale-95', 'opacity-0');
                addObjetivoModalPanel.classList.add('scale-100', 'opacity-100');
                
                // Clear any previous selection when opening the modal
                selectedObjective = null;
                selectedNomeObjetivoInput.value = '';
                const currentSelected = document.querySelector('.objective-option-btn.border-purple-600');
                if (currentSelected) {
                    currentSelected.classList.remove('border-purple-600', 'ring-2', 'ring-purple-200', 'shadow-md');
                }
                updateSubmitButtonState(); 
            }, 10);
        };

        const closeAddObjetivoModal = () => {
            if (!addObjetivoModalPanel) return;
            addObjetivoModalPanel.classList.remove('scale-100', 'opacity-100');
            addObjetivoModalPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                addObjetivoModal.classList.add('hidden');
                addObjetivoModal.classList.remove('flex');
                document.body.style.overflow = '';
                
                // Clear form and selection
                document.getElementById('add-objetivo-form').reset();
                selectedObjective = null;
                selectedNomeObjetivoInput.value = '';
                const currentSelected = document.querySelector('.objective-option-btn.border-purple-600');
                if (currentSelected) {
                    currentSelected.classList.remove('border-purple-600', 'ring-2', 'ring-purple-200', 'shadow-md');
                }
                updateSubmitButtonState(); 
            }, 300);
        };

        if (openAddObjetivoModalBtn) openAddObjetivoModalBtn.addEventListener('click', openAddObjetivoModal);
        if (closeAddObjetivoModalBtn) closeAddObjetivoModalBtn.addEventListener('click', closeAddObjetivoModal);
        if (cancelAddObjetivoBtn) cancelAddObjetivoBtn.addEventListener('click', closeAddObjetivoModal);
        if (addObjetivoModalOverlay) addObjetivoModalOverlay.addEventListener('click', closeAddObjetivoModal);

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && addObjetivoModal && !addObjetivoModal.classList.contains('hidden')) {
                closeAddObjetivoModal();
            }
        });

        // Initial state of the submit button
        updateSubmitButtonState();
    });
</script>