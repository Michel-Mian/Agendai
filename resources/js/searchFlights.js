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
});