document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de busca de voos
    if (!window.location.pathname.includes('flight') && !document.getElementById('type_trip')) {
        return;
    }

    // Interceptar o submit do formulário de busca
    const searchForm = document.querySelector('form[action*="flights.search"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Coletar dados do formulário
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            
            // Mostrar loader
            const flightsContainer = document.getElementById('flights-container');
            if (flightsContainer) {
                flightsContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mb-4"></div>
                        <p class="text-gray-600 text-lg font-medium">Buscando voos...</p>
                        <p class="text-gray-500 text-sm mt-2">Isso pode levar alguns instantes</p>
                    </div>
                `;
            }
            
            // Fazer a requisição
            fetch(this.action + '?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Criar um elemento temporário para parsear o HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extrair apenas o conteúdo do flights-container
                const newContent = tempDiv.querySelector('#flights-container');
                
                if (newContent && flightsContainer) {
                    flightsContainer.innerHTML = newContent.innerHTML;
                    
                    // Re-inicializar event listeners para os novos cards de voos
                    initializeFlightCardListeners();
                }
            })
            .catch(error => {
                console.error('Erro ao buscar voos:', error);
                if (flightsContainer) {
                    flightsContainer.innerHTML = `
                        <div class="text-center py-12 bg-red-50 rounded-lg shadow-md">
                            <i class="fas fa-exclamation-circle text-5xl text-red-500 mb-4"></i>
                            <p class="text-lg text-red-700 font-semibold">Erro ao buscar voos</p>
                            <p class="text-sm text-red-600 mt-2">Tente novamente mais tarde</p>
                        </div>
                    `;
                }
            });
        });
    }

    const typeTrip = document.getElementById('type_trip');
    const dateReturn = document.getElementById('date_return');

    // essa função torna obsoleto o campo de data de retorno quando a viagem for só de ida
    function toggleDateReturn() {
        if (typeTrip && dateReturn) {
            if (typeTrip.value == '2') { 
                dateReturn.disabled = true;
                dateReturn.value = '';
            } else {
                dateReturn.disabled = false;
            }
        }
    }

    const typeTrip2 = document.getElementById('type-trip');
    const dateReturn2 = document.getElementById('date-return');

    // essa função torna obsoleto o campo de data de retorno quando a viagem for só de ida
    function toggleDateReturn2() {
        if (typeTrip2 && dateReturn2) {
            if (typeTrip2.value == '2') { 
                dateReturn2.disabled = true;
                dateReturn2.value = '';
            } else {
                dateReturn2.disabled = false;
            }
        }
    }

    if (typeTrip) {
        typeTrip.addEventListener('change', toggleDateReturn);
        toggleDateReturn();
    }
    
    if (typeTrip2) {
        typeTrip2.addEventListener('change', toggleDateReturn2);
        toggleDateReturn2();
    }

        document.getElementById('open-filter-modal').onclick = function() {
            document.getElementById('filter-modal').classList.remove('hidden');
        }

        document.getElementById('close-filter-modal').onclick = function() {
        document.getElementById('filter-modal').classList.add('hidden');
        };
        // Fechar ao clicar fora do modal
        document.getElementById('filter-modal').addEventListener('click', function(e) {
            // Fecha só se clicar diretamente no fundo (no próprio modal, não nos filhos)
            if (e.target === this) {
                this.classList.add('hidden');
                document.querySelectorAll('.select-flight-checkbox').forEach(cb => cb.checked = false);
            }
        });

        // Função para configurar o autocomplete de aeroportos
        function setupAirportAutocomplete(inputId, suggestionsId) {
            const input = document.getElementById(inputId);
            const suggestions = document.getElementById(suggestionsId);

            input.addEventListener('input', function () {
                const query = this.value;
                if (query.length < 2) {
                    suggestions.innerHTML = '';
                    return;
                }
                fetch(`/autocomplete-airports?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        data.forEach(item => {
                            html += `<div class="px-2 py-1 hover:bg-gray-100 cursor-pointer" data-iata="${item.iata_code}">${item.name} (${item.iata_code}) - ${item.city}</div>`;
                        });
                        suggestions.innerHTML = html;
                    });
            });

            suggestions.addEventListener('click', function (e) {
                if (e.target && e.target.dataset.iata) {
                    input.value = e.target.dataset.iata;
                    suggestions.innerHTML = '';
                }
            });

            // Fecha sugestões ao clicar fora
            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                    suggestions.innerHTML = '';
                }
            });
        }

        // Inicialize para os dois campos
        setupAirportAutocomplete('dep_iata', 'dep_iata_suggestions');
        setupAirportAutocomplete('arr_iata', 'arr_iata_suggestions');

        // Função para inicializar listeners dos cards de voos
        function initializeFlightCardListeners() {
            let vooSelecionado = null;
            let precoSelecionado = null;
            
            // Botão "Ver Detalhes"
            document.querySelectorAll('.ver-detalhes-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const detalhes = document.getElementById(targetId);
                    if (detalhes) {
                        detalhes.classList.toggle('hidden');
                        
                        // Alterar o texto do botão e o ícone
                        const icon = this.querySelector('i');
                        if (detalhes.classList.contains('hidden')) {
                            this.innerHTML = 'Ver Detalhes <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>';
                        } else {
                            this.innerHTML = 'Ocultar Detalhes <i class="fa-solid fa-chevron-up ml-1 text-xs"></i>';
                        }
                    }
                });
            });

            // Quando marcar o checkbox do voo
            document.querySelectorAll('.select-flight-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        vooSelecionado = JSON.parse(this.dataset.voo);
                        precoSelecionado = parseFloat(this.dataset.preco);
                        const tripModal = document.getElementById('trip-modal');
                        if (tripModal) {
                            tripModal.classList.remove('hidden');
                        }
                    }
                });
            });

            // Quando clicar em "Selecionar" na viagem
            document.querySelectorAll('.btn-selecionar-viagem').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const tripId = this.dataset.tripId;
                    const orcamento = parseFloat(this.dataset.orcamento);
                    const dataInicioViagem = this.dataset.dataInicioViagem;

                    document.getElementById('flight_data').value = JSON.stringify(vooSelecionado);
                    document.getElementById('viagem_id').value = tripId;

                    document.getElementById('confirm-orcamento').textContent = `O orçamento da sua viagem é ${orcamento.toFixed(2)}`;
                    document.getElementById('confirm-preco').textContent = `O preço do voo é ${precoSelecionado.toFixed(2)}`;

                    const dataVoo = vooSelecionado?.flights?.[0]?.departure_airport?.time?.split(' ')[0];
                    let avisoData = '';
                    const dtViagem = new Date(dataInicioViagem);
                    const dtVoo = new Date(dataVoo);
                    const diffDias = Math.abs((dtVoo - dtViagem) / (1000 * 60 * 60 * 24));
                    if (diffDias > 7 || diffDias < 0) {
                        avisoData = `Atenção: a data do voo (${dataVoo}) está mais de 7 dias distante da data de início da viagem (${dataInicioViagem})!`;
                    }

                    const avisoDataElem = document.getElementById('aviso-data');
                    if (avisoDataElem) {
                        avisoDataElem.textContent = avisoData;
                        avisoDataElem.style.display = avisoData ? 'block' : 'none';
                    }

                    if (orcamento * 0.6 < precoSelecionado) {
                        document.getElementById('span-confirm-preco').classList.remove('hidden');
                        document.getElementById('span-confirm-orc').classList.remove('hidden');
                    }

                    if (orcamento * 0.6 < precoSelecionado || avisoData) {
                        document.getElementById('confirmation-modal').classList.remove('hidden');
                        e.preventDefault();
                    } else {
                        document.getElementById('flight_data_envio_direto').value = JSON.stringify(vooSelecionado);
                        document.getElementById('viagem_id_envio_direto').value = tripId;
                        document.getElementById('form-envio-direto').submit();
                    }
                });
            });
        }

        // Inicializar listeners na primeira carga
        initializeFlightCardListeners();

        // Fechar modal ao clicar fora
        document.getElementById('trip-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                // Desmarca todos os checkboxes ao fechar, se quiser
                document.querySelectorAll('.select-flight-checkbox').forEach(cb => cb.checked = false);
            }
        });

        // Botão cancelar do modal de confirmação
        const cancelBtn = document.getElementById('cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Impede o submit ou reload
                document.getElementById('confirmation-modal').classList.add('hidden');
            });
        }

        document.getElementById('btn-confirmar').addEventListener('click', function() {
            // Aqui os campos já devem estar preenchidos pelo fluxo anterior do JS
            document.querySelector('#confirmation-modal form').submit();
        });
});

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateDeparture = document.getElementById('date_departure');
    const dateReturn = document.getElementById('date_return');

    if (dateDeparture) {
        dateDeparture.min = today;
    }
    
    if (dateReturn) {
        dateReturn.min = dateDeparture && dateDeparture.value ? dateDeparture.value : today;
    }

    if (dateDeparture && dateReturn) {
        dateDeparture.addEventListener('change', function() {
            dateReturn.min = this.value;
            if (dateReturn.value < this.value) {
                dateReturn.value = '';
            }
        });
    }
});
