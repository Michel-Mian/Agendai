// Função global para autocomplete
function initPlacesAutocomplete() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && typeof google !== 'undefined' && google.maps && google.maps.places) {
        if (!searchInput._autocompleteInitialized) {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                types: ['(regions)'],
            });
            autocomplete.addListener('place_changed', function() {
                // Você pode tratar o place aqui se quiser
            });
            searchInput._autocompleteInitialized = true;
        }
    }
}

// Callback global do Google Maps
window.initMap = function() {
    initPlacesAutocomplete();
};

// Fallback caso a API já esteja carregada antes do DOM
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        initPlacesAutocomplete();
    }

    // --- Todo o resto do seu código de steps, botões, etc ---
    let currentStep = 0;
    const steps = document.querySelectorAll('.form-step');
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    let meioLocomocao = '';

    function showStep(idx) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === idx);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            el.classList.toggle('active', i === idx);
        });
    }

    nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', async function() {
            if (currentStep === 3) {
                const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
                const meio = meioSelect.value;
                meioLocomocao = meio;

                // Se for avião, mostra a etapa 5
                if (meio === 'Avião') {
                    document.querySelectorAll('.form-step')[4].classList.remove('hidden');
                } else {
                    document.querySelectorAll('.form-step')[4].classList.add('hidden');
                }

                // Colete os dados necessários do formulário
                const data = {
                    dep_iata: document.getElementById('dep_iata')?.value || '',
                    arr_iata: document.getElementById('arr_iata')?.value || '',
                    date_departure: document.getElementById('date_departure')?.value || '',
                    date_return: document.getElementById('date_return')?.value || '',
                };

                try {
                    // Busque os voos
                    const resVoos = await fetch('/formTrip/flights?' + new URLSearchParams(data));
                    const result = await resVoos.json();

                    // Renderize os voos na etapa 5
                    const container = document.getElementById('flights-container');
                    container.innerHTML = '';

                    if (result.flights && result.flights.length) {
                        for (let i = 0; i < result.flights.length; i++) {
                            const flight = result.flights[i];
                            const resCard = await fetch('/formTrip/card-flight?' + new URLSearchParams({
                                flight: JSON.stringify(flight),
                                index: i
                            }));
                            const cardData = await resCard.json();
                            container.innerHTML += cardData.html;
                        }
                    } else {
                        container.innerHTML = '<div class="text-gray-500">Nenhum voo encontrado para os critérios informados.</div>';
                    }
                } catch (e) {
                    // Se der erro, mostra mensagem e permite avançar
                    const container = document.getElementById('flights-container');
                    container.innerHTML = '<div class="text-red-500">Erro ao buscar voos.</div>';
                }
            }

            // Avanço de etapa
            if (currentStep === 3 && meioLocomocao !== 'Avião') {
                currentStep += 2;
            } else {
                currentStep++;
            }
            if (currentStep >= steps.length) currentStep = steps.length - 1;
            showStep(currentStep);
            if (currentStep === 5) preencherRevisao();
        });
    });

    prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            if (currentStep === 5 && meioLocomocao !== 'Avião') {
                currentStep -= 2;
            } else {
                currentStep--;
            }
            if (currentStep < 0) currentStep = 0;
            showStep(currentStep);
        });
    });

    function preencherRevisao() {
        const reviewList = document.getElementById('reviewList');
        if (!reviewList) return;
        const destino = document.getElementById('searchInput').value;
        const adultos = document.querySelectorAll('.form-step')[0].querySelectorAll('select')[0].value;
        const criancas = document.querySelectorAll('.form-step')[0].querySelectorAll('select')[1].value;
        const dataIda = document.querySelectorAll('.form-step')[0].querySelectorAll('input[type="date"]')[0].value;
        const dataVolta = document.querySelectorAll('.form-step')[0].querySelectorAll('input[type="date"]')[1].value;
        const meio = document.querySelectorAll('.form-step')[1].querySelector('select').value;
        const estadia = document.querySelectorAll('.form-step')[1].querySelectorAll('select')[1].value;
        const orcamento = document.querySelectorAll('.form-step')[3].querySelector('input[type="number"]').value;
        let seguro = '';
        document.querySelectorAll('.insurance-btn').forEach(btn => {
            if (btn.classList.contains('selected')) seguro = btn.innerText.trim();
        });
        let voo = '';
        if (meio === 'Avião') {
            document.querySelectorAll('.space-y-4 > div').forEach((div, i) => {
                if (div.classList.contains('border-blue-500')) {
                    voo = div.innerText.replace(/\s+/g, ' ').trim();
                }
            });
        }
        reviewList.innerHTML = `
            <li><b>Destino:</b> ${destino}</li>
            <li><b>Adultos:</b> ${adultos}</li>
            <li><b>Crianças:</b> ${criancas}</li>
            <li><b>Data de ida:</b> ${dataIda}</li>
            <li><b>Data de volta:</b> ${dataVolta}</li>
            <li><b>Meio de locomoção:</b> ${meio}</li>
            <li><b>Estadia:</b> ${estadia}</li>
            <li><b>Orçamento:</b> R$ ${orcamento}</li>
            <li><b>Seguro:</b> ${seguro}</li>
            ${meio === 'Avião' ? `<li><b>Voo escolhido:</b> ${voo}</li>` : ''}
        `;
    }

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

    showStep(currentStep);

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
    
    const meioSelect = document.querySelectorAll('.form-step')[1].querySelector('select');
    const depIataContainer = document.getElementById('dep_iata_container');

    meioSelect.addEventListener('change', function() {
        if (this.value === 'Avião') {
            depIataContainer.classList.remove('hidden');
        } else {
            depIataContainer.classList.add('hidden');
        }
    });

    // Se quiser já mostrar se "Avião" vier selecionado por padrão:
    if (meioSelect.value === 'Avião') {
        depIataContainer.classList.remove('hidden');
    } else {
        depIataContainer.classList.add('hidden');
    }
});