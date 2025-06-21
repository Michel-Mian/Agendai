document.addEventListener('DOMContentLoaded', function() {
        const typeTrip = document.getElementById('type_trip');
        const dateReturn = document.getElementById('date_return');

        function toggleDateReturn() {
            if (typeTrip.value == '2') { // Só ida
                dateReturn.disabled = true;
                dateReturn.value = '';
            } else {
                dateReturn.disabled = false;
            }
        }

        typeTrip.addEventListener('change', toggleDateReturn);
        toggleDateReturn(); // Executa ao carregar a página
});