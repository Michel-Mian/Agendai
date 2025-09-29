document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de busca de voos
    if (!window.location.pathname.includes('flight') && !document.getElementById('type_trip')) {
        return;
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


        let vooSelecionado = null;
        let precoSelecionado = null;

        // Quando marcar o checkbox do voo
        document.querySelectorAll('.select-flight-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    vooSelecionado = JSON.parse(this.dataset.voo);
                    precoSelecionado = parseFloat(this.dataset.preco);
                    document.getElementById('trip-modal').classList.remove('hidden');
                }
            });
        });

        // Quando clicar em "Selecionar" na viagem
        document.querySelectorAll('.btn-selecionar-viagem').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const tripId = this.dataset.tripId;
                const orcamento = parseFloat(this.dataset.orcamento);
                const dataInicioViagem = this.dataset.dataInicioViagem; // formato: "YYYY-MM-DD"

                // Use os dados do voo selecionado
                document.getElementById('flight_data').value = JSON.stringify(vooSelecionado);
                document.getElementById('viagem_id').value = tripId;

                // Atualiza os textos do modal de confirmação
                document.getElementById('confirm-orcamento').textContent = `O orçamento da sua viagem é ${orcamento.toFixed(2)}`;
                document.getElementById('confirm-preco').textContent = `O preço do voo é ${precoSelecionado.toFixed(2)}`;

                // Pegue a data do voo (do voo selecionado)
                console.log('dataInicioViagem:', dataInicioViagem);
                console.log('vooSelecionado:', vooSelecionado);
                const dataVoo = vooSelecionado?.flights?.[0]?.departure_airport?.time?.split(' ')[0];
                let avisoData = '';
                    const dtViagem = new Date(dataInicioViagem);
                    const dtVoo = new Date(dataVoo);
                    const diffDias = Math.abs((dtVoo - dtViagem) / (1000 * 60 * 60 * 24));
                    console.log('diffDias:', diffDias);
                    if (diffDias > 7 || diffDias < 0) {
                        avisoData = `Atenção: a data do voo (${dataVoo}) está mais de 7 dias distante da data de início da viagem (${dataInicioViagem})!`;
                    }
                

                // Mostra o aviso no modal, se necessário
                const avisoDataElem = document.getElementById('aviso-data');
                if (avisoDataElem) {
                    avisoDataElem.textContent = avisoData;
                    avisoDataElem.style.display = avisoData ? 'block' : 'none';
                }

                if (orcamento * 0.6 < precoSelecionado) {
                    document.getElementById('span-confirm-preco').classList.remove('hidden');
                    document.getElementById('span-confirm-orc').classList.remove('hidden');
                }

                // Verificação do orçamento
                if (orcamento * 0.6 < precoSelecionado || avisoData) {
                    document.getElementById('confirmation-modal').classList.remove('hidden');
                    e.preventDefault();
                }
                else{
                    document.getElementById('flight_data_envio_direto').value = JSON.stringify(vooSelecionado);
                    document.getElementById('viagem_id_envio_direto').value = tripId;
                    document.getElementById('form-envio-direto').submit();                
                }
            });
        });

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
