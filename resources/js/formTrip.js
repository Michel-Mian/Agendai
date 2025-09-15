// -------------------- Autocomplete Google Places (Destino) --------------------
function initPlacesAutocompleteStrict() {
    const fields = [
        { id: 'tripDestination' },
        { id: 'origem' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input && typeof google !== 'undefined' && google.maps && google.maps.places) {
            if (!input._autocompleteInitialized) {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['(regions)'],
                });
                input._autocompleteInitialized = true;

                // Armazena se o usuário selecionou uma sugestão válida
                input._placeSelected = false;

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place && place.place_id) {
                        input._placeSelected = true;
                        input.classList.remove('border-red-500');
                    } else {
                        input._placeSelected = false;
                        input.classList.add('border-red-500');
                    }
                });

                // Ao digitar, reseta o status de seleção
                input.addEventListener('input', function() {
                    input._placeSelected = false;
                    input.classList.remove('border-red-500');
                });

                // Ao sair do campo, verifica se selecionou uma sugestão
                input.addEventListener('blur', function(e) {
                    setTimeout(() => {
                        if (!input._placeSelected) {
                            input.classList.add('border-red-500');
                            input.focus();
                        }
                    }, 200);
                });
            }
        }
    });
}

// Callback global do Google Maps - definido imediatamente
window.initTripFormMap = function() {
    console.log('Google Maps API carregada, inicializando autocomplete...');
    initPlacesAutocompleteStrict();
};

// Garantir que a função está disponível globalmente
if (typeof window.initTripFormMap !== 'function') {
    window.initTripFormMap = function() {
        console.log('Fallback: Google Maps API carregada');
        if (typeof initPlacesAutocompleteStrict === 'function') {
            initPlacesAutocompleteStrict();
        }
    };
}

// Fallback caso a API já esteja carregada antes do DOM
document.addEventListener('DOMContentLoaded', function() {

    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        initPlacesAutocompleteStrict();
    }

    // -------------------- Variáveis Globais e Steps --------------------
    let currentStep = 0;
    const steps = document.querySelectorAll('.form-step');
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    let meioLocomocao = document.querySelectorAll('.form-step')[1].querySelector('select').value;
    let voosCarregados = []; // Armazena voos carregados

    // -------------------- Função para mostrar o passo atual --------------------
    function showStep(idx) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === idx);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            el.classList.toggle('active', i === idx);
        });

        // Se for o último passo, preenche a revisão
        if (idx === steps.length - 1) {
            preencherRevisao();
        }
    }

    // -------------------- Função para buscar voos --------------------
    async function searchFlights() {
        if (meioLocomocao === 'Avião') {
            document.querySelectorAll('.form-step')[4].classList.remove('hidden');
            const data = {
                dep_iata: document.getElementById('dep_iata')?.value || '',
                arr_iata: document.getElementById('arr_iata')?.value || '',
                date_departure: document.getElementById('date_departure')?.value || '',
                date_return: document.getElementById('date_return')?.value || '',
            };
            const container = document.getElementById('flights-container');
            container.innerHTML = '<div class="text-gray-500">Carregando voos...</div>';
            let timeout = false;
            const timer = setTimeout(() => {
                timeout = true;
                container.innerHTML = '<div class="text-red-500">Tempo esgotado ao buscar voos.</div>';
            }, 8000);
            try {
                const resVoos = await fetch('/formTrip/flights?' + new URLSearchParams(data));
                if (timeout) return;
                clearTimeout(timer);
                const result = await resVoos.json();
                if (result.flights && result.flights.length) {
                    voosCarregados = result.flights;
                    container.innerHTML = '';
                    for (let i = 0; i < result.flights.length; i++) {
                        const flight = result.flights[i];
                        const resCard = await fetch('/formTrip/card-flight?' + new URLSearchParams({
                            flight: JSON.stringify(flight),
                            index: i
                        }));
                        const cardData = await resCard.json();
                        container.innerHTML += cardData.html;
                    }
                    // Adiciona listeners nos checkboxes dos voos
                    document.querySelectorAll('.select-flight-checkbox').forEach((checkbox, idx) => {
                        checkbox.addEventListener('change', function() {
                            if (this.checked) {
                                document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                                document.getElementById('selected_flight_index').value = idx;
                            }
                        });
                    });
                } else {
                    container.innerHTML = '<div class="text-gray-500">Nenhum voo encontrado para os critérios informados.</div>';
                }
            } catch (e) {
                if (!timeout) container.innerHTML = '<div class="text-red-500">Erro ao buscar voos.</div>';
            }
        } else {
            document.querySelectorAll('.form-step')[4].classList.add('hidden');
        }
    }

    // Função para formatar data yyyy-mm-dd para dd/mm/aaaa
    function formatarDataBR(data) {
        if (!data) return '';
        const [ano, mes, dia] = data.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    function preencherRevisao() {
        const reviewList = document.getElementById('reviewList'); //
        if (!reviewList) return;

        // Pegue os campos do DOM conforme o seu form
        const destino = document.getElementById('tripDestination')?.value || '';
        const adultosSelect = document.querySelectorAll('.form-step')[0]?.querySelectorAll('select')[0];
        const adultos = adultosSelect ? adultosSelect.value : '';
        const dataIdaInput = document.querySelectorAll('.form-step')[0]?.querySelectorAll('input[type="date"]')[0];
        const dataVoltaInput = document.querySelectorAll('.form-step')[0]?.querySelectorAll('input[type="date"]')[1];
        const dataIda = dataIdaInput ? dataIdaInput.value : '';
        const dataVolta = dataVoltaInput ? dataVoltaInput.value : '';
        const meioSelect = document.querySelectorAll('.form-step')[1]?.querySelector('select');
        const meio = meioSelect ? meioSelect.value : '';
        const orcamentoInput = document.querySelectorAll('.form-step')[1]?.querySelector('input[type="number"]');
        const orcamento = orcamentoInput ? orcamentoInput.value : '';
        const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
        const idades = Array.from(idadeInputs).map(input => input.value).filter(value => value !== '');
        const seguroSelect = document.getElementById('seguroViagem');
        const seguro = seguroSelect ? seguroSelect.value : '';
        let nomeSeguro = '';
        if (seguro === 'Sim') {
            nomeSeguro = sessionStorage.getItem('selectedSeguroName') || '';
        }

        // Recupera preferências do input hidden (atualizado no step3)
        const preferencesInput = document.getElementById('preferences');
        let preferences = [];
        if (preferencesInput && preferencesInput.value) {
            preferences = preferencesInput.value.split(',').map(p => p.trim()).filter(p => p.length > 0);
        }

        let vooInfoHtml = '';
        const selectedFlightDataInput = document.getElementById('selected_flight_data');
        if (meio === 'Avião' && selectedFlightDataInput && selectedFlightDataInput.value) {
            try {
                const flightData = JSON.parse(selectedFlightDataInput.value);
                const airline = flightData.flights[0]?.airline || 'Não selecionada';
                vooInfoHtml = `<li><b>Companhia aérea:</b> ${airline}</li>`;
            } catch (e) {
                console.error("Erro ao ler dados do voo:", e);
                vooInfoHtml = `<li><b>Companhia aérea:</b> Erro ao ler dados</li>`;
            }
        }

        reviewList.innerHTML = `
            <li><b>Destino:</b> ${destino}</li>
            <li><b>Adultos:</b> ${adultos}</li>
            <li><b>Idades dos passageiros:</b> ${idades.length > 0 ? idades.join(', ') : 'Nenhuma'}</li>
            <li><b>Data de ida:</b> ${formatarDataBR(dataIda)}</li>
            <li><b>Data de volta:</b> ${formatarDataBR(dataVolta)}</li>
            <li><b>Meio de locomoção:</b> ${meio}</li>
            <li><b>Orçamento:</b> R$ ${orcamento}</li>
            ${vooInfoHtml}
            ${seguro === 'Sim' && nomeSeguro ? `<li><b style="color:#fff">Seguro de viagem:</b> ${nomeSeguro}</li>` : ''}
            <li><b>Preferências:</b> ${preferences.length > 0 ? preferences.join(', ') : 'Nenhuma'}</li>
        `;
    }

    // -------------------- Eventos dos botões de navegação --------------------
    nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', async function() {
            // Adicione esta linha:
            if (!validarStep(currentStep)) return;

            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value; // Atualiza sempre

            // Lógica de navegação entre os passos
            if (currentStep === 2) {
                if (seguro && seguro.value === 'Não' && meioLocomocao !== 'Avião') {
                    currentStep += 3;
                } else if (seguro && seguro.value === 'Não' && meioLocomocao === 'Avião') {
                    currentStep += 2;
                    await searchFlights();
                } else if (seguro && seguro.value === 'Sim') {
                    currentStep++;
                    // Usuário pode clicar manualmente no botão "Buscar Seguros" no step 4
                }
            } else if (currentStep === 3) {
                if (meioLocomocao !== 'Avião') {
                    currentStep += 2;
                } else {
                    currentStep++;
                    await searchFlights();
                }
            } else {
                currentStep++;
            }

            if (currentStep >= steps.length) currentStep = steps.length - 1;
            showStep(currentStep);
        });
    });

    prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            const seguro = document.getElementById('seguroViagem');
            const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
            meioLocomocao = meioSelect.value;

            // Se está no passo 5 ou 6 e seguro é "Não", pule a etapa de seguros ao voltar
            if (
                (currentStep === 5 && seguro && seguro.value === 'Não' && meioLocomocao !== 'Avião') ||
                (currentStep === 6 && seguro && seguro.value === 'Não' && meioLocomocao === 'Avião')
            ) {
                currentStep -= 3;
            } else if (currentStep === 4 && meioLocomocao === 'Avião' && seguro && seguro.value === 'Não') {
                currentStep -= 2;
            } else {
                currentStep--;
            }
            if (currentStep < 0) currentStep = 0;
            showStep(currentStep);
        });
    });

    // -------------------- Eventos de seleção de seguro e preferências --------------------
    document.querySelectorAll('.insurance-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.insurance-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
        });
    });

    document.querySelectorAll('.pref-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.classList.toggle('selected');
        });
    });

    // -------------------- Autocomplete de aeroportos --------------------
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

    // Inicializa autocomplete para os campos de IATA
    setupAirportAutocomplete('dep_iata', 'dep_iata_suggestions');
    setupAirportAutocomplete('arr_iata', 'arr_iata_suggestions');

    // -------------------- Exibição dinâmica de campos conforme seleção --------------------
    const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
    const depIataContainer = document.getElementById('dep_iata_container');
    const seguro = document.getElementById('seguroViagem');
    const insuranceOptions = document.getElementById('insurance-options');

    meioSelect.addEventListener('change', function() {
        meioLocomocao = this.value;
        if (this.value === 'Avião') {
            depIataContainer.classList.remove('hidden');
        } else {
            depIataContainer.classList.add('hidden');
        }
    });

    seguro.addEventListener('change', function() {
        if (this.value === 'Sim') {
            insuranceOptions.classList.remove('hidden');
        } else {
            insuranceOptions.classList.add('hidden');
        }
    });

    // Exibe campo de IATA se "Avião" vier selecionado por padrão
    if (meioSelect.value === 'Avião') {
        depIataContainer.classList.remove('hidden');
    } else {
        depIataContainer.classList.add('hidden');
    }

    // -------------------- Inicialização --------------------
    showStep(currentStep);
});

// -------------------- Eventos de seleção de voo --------------------
document.addEventListener('DOMContentLoaded', function() {
    const flightsContainer = document.getElementById('flights-container');
    if (flightsContainer) {
        flightsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('select-flight-checkbox')) {
                // Desmarca todos os outros checkboxes
                document.querySelectorAll('.select-flight-checkbox').forEach(cb => {
                    if (cb !== e.target) cb.checked = false;
                });
                // Remove destaque de todos os cards
                document.querySelectorAll('.flight-card').forEach(card => card.classList.remove('border-4', 'border-blue-600'));
                // Se marcado, destaca o card e salva o índice
                if (e.target.checked) {
                    const idx = parseInt(e.target.dataset.index);
                    document.getElementById('selected_flight_index').value = idx;
                    document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                } else {
                    document.getElementById('selected_flight_index').value = '';
                    document.getElementById('selected_flight_data').value = '';
                }
            }
        });
    }

    // Listener individual para cada checkbox de voo
    document.querySelectorAll('.select-flight-checkbox').forEach((checkbox, idx) => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('selected_flight_data').value = JSON.stringify(voosCarregados[idx]);
                document.getElementById('selected_flight_index').value = idx;
            }
        });
    });
});

// -------------------- Evento de submit do formulário --------------------
document.getElementById('multiStepForm').addEventListener('submit', function (e) {
    console.log('submit!');
    // e.preventDefault(); // comente esta linha para testar
    // this.submit(); // comente esta linha para testar
});

// -------------------- Tratamento de erros e mensagens de feedback --------------------
function validarStep(idx) {
    if (idx === 0) {
        const destino = document.getElementById('tripDestination');
        const origem = document.getElementById('origem');
        const adultos = document.querySelectorAll('.form-step')[0]?.querySelectorAll('select')[0];
        const dataIda = document.querySelectorAll('.form-step')[0]?.querySelectorAll('input[type="date"]')[0];
        const dataVolta = document.querySelectorAll('.form-step')[0]?.querySelectorAll('input[type="date"]')[1];

        if (!destino.value.trim()) {
            alert('Informe o destino.');
            destino.focus();
            return false;
        }
        if (!destino._placeSelected) {
            alert('Selecione um destino válido da lista sugerida.');
            destino.classList.add('border-red-500');
            destino.focus();
            return false;
        }
        if (!origem.value.trim()) {
            alert('Informe a origem.');
            origem.focus();
            return false;
        }
        if (!origem._placeSelected) {
            alert('Selecione uma origem válida da lista sugerida.');
            origem.classList.add('border-red-500');
            origem.focus();
            return false;
        }
        if (!adultos || !adultos.value) {
            alert('Informe o número de adultos.');
            adultos.focus();
            return false;
        }
        if (!dataIda.value) {
            alert('Informe a data de ida.');
            dataIda.focus();
            return false;
        }
        if (!dataVolta.value) {
            alert('Informe a data de volta.');
            dataVolta.focus();
            return false;
        }
        if (dataVolta.value < dataIda.value) {
            alert('A data de volta não pode ser menor que a data de ida.');
            dataVolta.focus();
            return false;
        }
        if (new Date(dataIda.value) < new Date()) {
            alert('A data de ida não pode ser no passado.');
            dataIda.focus();
            return false;
        }
        if (new Date(dataVolta.value) < dataIda.value)  {
            alert('A data de volta não pode ser anterior à data de ida.');
            dataVolta.focus();
            return false;
        }
        const idadeInputs = document.querySelectorAll('#idades-container input[name="idades[]"]');
        let algumVazio = false;
        idadeInputs.forEach(input => {
            if (!input.value.trim()) {
                algumVazio = true;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });
        if (idadeInputs.length === 0 || algumVazio) {
            alert('Preencha todas as idades dos viajantes.');
            if (idadeInputs.length > 0) idadeInputs[0].focus();
            return false;
        }
    }
    

    if (idx === 1) {
        const orcamento = document.querySelectorAll('.form-step')[1]?.querySelector('input[type="number"]');
        const meioLocomocao = document.querySelectorAll('.form-step')[1]?.querySelectorAll('select')[0];
        const seguro = document.getElementById('seguroViagem');
        if (!orcamento.value || Number(orcamento.value) <= 0) {
            alert('Informe um orçamento válido.');
            orcamento.focus();
            return false;
        }
        if (meioLocomocao && meioLocomocao.value === 'Avião') {
            const depIata = document.getElementById('dep_iata');
            const arrIata = document.getElementById('arr_iata');
            if (!depIata.value.trim()) {
                alert('Informe o aeroporto de partida.');
                depIata.focus();
                return false;
            }
            if (!arrIata.value.trim()) {
                alert('Informe o aeroporto de chegada.');
                arrIata.focus();
                return false;
            }
        }
        if (seguro && seguro.value === 'Sim') {
            const destinoSeguro = document.getElementById('MainContent_Cotador_selContinente');
            
            if (!destinoSeguro || !destinoSeguro.value || destinoSeguro.value === '') {
                alert('Selecione um destino para a viagem.');
                if (destinoSeguro) destinoSeguro.focus();
                return false;
            }
        }

    }
    return true;
}

// -------------------- Formatação data picker --------------------
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateDeparture = document.getElementById('date_departure');
    const dateReturn = document.getElementById('date_return');

    dateDeparture.min = today;
    dateReturn.min = dateDeparture.value || today;

    dateDeparture.addEventListener('change', function() {
        dateReturn.min = this.value;
        if (dateReturn.value < this.value){}
    });
});

// -------------------- Mostrar/Ocultar detalhes do voo --------------------
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('ver-detalhes-btn') || e.target.closest('.ver-detalhes-btn')) {
            const btn = e.target.closest('.ver-detalhes-btn');
            const targetId = btn.getAttribute('data-target');
            const detalhes = document.getElementById(targetId);
            if (detalhes) {
                detalhes.classList.toggle('hidden');
            }
        }
    });
});

// -------------------- Seleção de preferências (step 3) --------------------
document.addEventListener('DOMContentLoaded', function() {
    const prefBtns = document.querySelectorAll('.pref-btn');
    const preferencesInput = document.getElementById('preferences');

    let selectedPrefs = [];

    prefBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const pref = btn.querySelector('span.text-gray-700').innerText;
            btn.classList.toggle('bg-blue-100');
            if (selectedPrefs.includes(pref)) {
                selectedPrefs = selectedPrefs.filter(p => p !== pref);
            } else {
                selectedPrefs.push(pref);
            }
            // Atualiza o input hidden com todas as preferências selecionadas
            preferencesInput.value = selectedPrefs.join(',');
        });
    });
});