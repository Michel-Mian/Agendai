document.addEventListener('DOMContentLoaded', function() {
        const typeTrip = document.getElementById('type_trip');
        const dateReturn = document.getElementById('date_return');

        // essa função torna obsoleto o campo de data de retorno quando a viagem for só de ida
        function toggleDateReturn() {
            if (typeTrip.value == '2') { 
                dateReturn.disabled = true;
                dateReturn.value = '';
            } else {
                dateReturn.disabled = false;
            }
        }

        const typeTrip2 = document.getElementById('type-trip');
        const dateReturn2 = document.getElementById('date-return');

        // essa função torna obsoleto o campo de data de retorno quando a viagem for só de ida
        function toggleDateReturn2() {
            if (typeTrip2.value == '2') { 
                dateReturn2.disabled = true;
                dateReturn2.value = '';
            } else {
                dateReturn2.disabled = false;
            }
        }

        typeTrip.addEventListener('change', toggleDateReturn);
        toggleDateReturn();
        
        typeTrip2.addEventListener('change', toggleDateReturn2);
        toggleDateReturn2();

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
});