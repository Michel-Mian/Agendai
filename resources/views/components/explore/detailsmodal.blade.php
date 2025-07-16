<div id="placeDetailsModal"
     class="fixed inset-0 flex items-center justify-center p-4 z-50 hidden"
     style="background: rgba(17,24,39,0.3); backdrop-filter: blur(8px);">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto transform scale-95 opacity-0 transition-all duration-300 relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div id="modalContent" class="p-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800" id="detailedPlaceName">Nome do Local</h2>
            <p class="text-gray-600 mb-2" id="detailedPlaceAddress">Endereço do Local</p>
            <p class="text-gray-700 leading-relaxed" id="detailedPlaceDescription">
                Descrição detalhada do local será carregada aqui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <div id="detailedPlacePhotos" class="grid grid-cols-2 gap-4 mt-4">
                </div>
            <p class="text-gray-800 font-semibold mt-4" id="detailedPlaceRating">Avaliação: N/A</p>
            <p class="text-gray-800 font-semibold" id="detailedPlaceType">Tipo: N/A</p>
        </div>

        
    </div>
</div>

<script>
    // --- Funções do Modal ---
async function openPlaceDetailsModal(placeId, fromItinerary = false, databaseId = null, horarioBanco = null) {
    if (typeof infoWindow !== 'undefined' && infoWindow) {
        infoWindow.close();
    }

    const modal = document.getElementById('placeDetailsModal');
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8 ">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-4">Carregando detalhes...</p>
        </div>
    `;

    modal.classList.remove('hidden');
    // Animação de entrada
    setTimeout(() => {
        modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('div').classList.add('scale-100', 'opacity-100');
    }, 50);

    const mapElement = (typeof map !== 'undefined' && map)
        ? map
        : document.createElement('div');
    const service = new google.maps.places.PlacesService(mapElement);
    const request = {
        placeId: placeId,
        fields: ['place_id', 'name', 'formatted_address', 'types', 'rating', 'user_ratings_total', 'photos', 'opening_hours', 'website', 'formatted_phone_number', 'reviews', 'geometry', 'vicinity']
    };

    service.getDetails(request, (placeDetails, status) => {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            currentDetailedPlace = placeDetails; // Armazena o objeto completo para uso posterior

            const photosHtml = placeDetails.photos ?
                `<div class="flex space-x-2 overflow-x-auto pb-2">
                    ${placeDetails.photos.slice(0, 5).map(photo => `<img src="${photo.getUrl({ 'maxWidth': 300, 'maxHeight': 200 })}" class="h-32 w-auto object-cover rounded-md shadow-sm" alt="Foto de ${placeDetails.name}">`).join('')}
                </div>` : '';

            const openingHoursHtml = placeDetails.opening_hours ?
                `<div class="mt-4">
                    <h4 class="font-semibold text-gray-800">Horário de Funcionamento:</h4>
                    <ul class="text-sm text-gray-600 list-disc list-inside">
                        ${placeDetails.opening_hours.weekday_text.map(day => `<li>${day}</li>`).join('')}
                    </ul>
                </div>` : '';

            const reviewsHtml = placeDetails.reviews && placeDetails.reviews.length > 0 ?
                `<div class="mt-4">
                    <h4 class="font-semibold text-gray-800">Avaliações:</h4>
                    <div class="space-y-3 mt-2 max-h-48 overflow-y-auto pr-2">
                        ${placeDetails.reviews.slice(0, 3).map(review => `
                            <div class="border-b border-gray-100 pb-3 last:border-b-0">
                                <div class="flex items-center mb-1">
                                    <span class="font-medium text-gray-700">${review.author_name}</span>
                                    <div class="flex items-center text-xs text-gray-500 ml-2">
                                        ${'⭐'.repeat(review.rating)}
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600">${review.text}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>` : '';

            // --- NOVO FORMULÁRIO GERADO VIA JS ---
            let horarioAtual = horarioBanco || "00:00";
            let alterarHorarioForm = "";
            if (fromItinerary && databaseId) {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                alterarHorarioForm = `
                    <form method="POST" action="/explore/ponto-interesse/${databaseId}/horario" class="mt-4 flex items-center gap-2">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <label for="novo_horario" class="text-sm font-medium text-gray-700">Horário:</label>
                        <input type="time" id="novo_horario" name="novo_horario" value="${horarioAtual}" class="border rounded px-2 py-1">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">Alterar para esse horário</button>
                    </form>
                    <button onclick="removePontoFromItinerary('${databaseId}')" class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-pink-500 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-lg w-full sm:w-auto">🗑️ Remover do Itinerário</button>
                `;
            }
            // ...restante do modal
            modalContent.innerHTML = `
                <div class="bg-white rounded-lg">
                    ${photosHtml}
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">${placeDetails.name}</h2>
                        <p class="text-sm text-gray-600 mb-4">${placeDetails.formatted_address}</p>

                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1 text-xs rounded-full font-medium ${getTypeColorClass(getPlaceType(placeDetails.types))}">
                                ${getTypeLabel(getPlaceType(placeDetails.types))}
                            </span>
                            ${placeDetails.rating ? `
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                ${placeDetails.rating} (${placeDetails.user_ratings_total || 0} avaliações)
                            </div>` : ''}
                            <span class="text-sm text-gray-500 font-medium">
                                ${placeDetails.opening_hours ? placeDetails.opening_hours.weekday_text.join(' | ') : ''}
                            </span>  
                        </div>

                        <p class="text-gray-700 mb-4">${placeDetails.vicinity || placeDetails.formatted_address || ''}</p>

                        ${placeDetails.website ? `<p class="text-blue-600 hover:underline mb-2"><a href="${placeDetails.website}" target="_blank">Site Oficial</a></p>` : ''}
                        ${placeDetails.formatted_phone_number ? `<p class="text-gray-700">Telefone: ${placeDetails.formatted_phone_number}</p>` : ''}

                        ${openingHoursHtml}
                        ${reviewsHtml}
                    </div>
                </div>
                <div class="p-8 border-t border-gray-200 flex flex-col sm:flex-row justify-end items-center gap-4">
                    ${fromItinerary && databaseId
                        ? alterarHorarioForm
                        : (window.hasTrip ? `
                        <div class=\"flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto\">
                            <div class=\"flex items-center gap-2\">
                                <label for=\"itineraryDate\" class=\"text-gray-700 font-medium whitespace-nowrap\">Data da visita:</label>
                                <input type=\"date\" id=\"itineraryDate\" class=\"form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2\">
                            </div>
                            <div class=\"flex items-center gap-2\">
                                <label for=\"itineraryTime\" class=\"text-gray-700 font-medium whitespace-nowrap\">Hora da visita:</label>
                                <input type=\"time\" id=\"itineraryTime\" class=\"form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2\">
                            </div>
                        </div>
                        <button onclick=\"addToItinerary(currentDetailedPlace && currentDetailedPlace.place_id, document.getElementById('itineraryTime').value, document.getElementById('itineraryDate').value); closeModal();\" class=\"px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg w-full sm:w-auto\">
                            ➕ Adicionar ao Itinerário
                        </button>` : `<div class=\"w-full text-center text-gray-400 text-base\">Crie uma viagem para adicionar este local ao itinerário.</div>`)
                    }
                </div>
            `;

            // Step 4: Set modal datepicker value to selected itinerary date or trip start date
            setTimeout(() => {
                if (!fromItinerary) {
                    const modalDatePicker = document.getElementById('itineraryDate');
                    const mainDatePicker = document.getElementById('datePicker');
                    if (modalDatePicker && window.hasTrip && window.dataInicioViagem && window.dataFimViagem) {
                        modalDatePicker.setAttribute('min', window.dataInicioViagem);
                        modalDatePicker.setAttribute('max', window.dataFimViagem);
                        let selectedDate = mainDatePicker && mainDatePicker.value ? mainDatePicker.value : window.dataInicioViagem;
                        modalDatePicker.value = selectedDate;
                    }
                }
            }, 200); // Aguarda renderização do modal
        } else {
            modalContent.innerHTML = `
                <div class="p-8 text-center text-red-500">
                    <p>Não foi possível carregar os detalhes deste lugar.</p>
                    <p class="text-sm text-gray-500">${status}</p>
                </div>
            `;
            console.error('Erro ao carregar detalhes do lugar:', status);
        }
    });

    // Step 4: Set modal datepicker value to selected itinerary date or trip start date
    setTimeout(() => {
        const modalDatePicker = document.getElementById('itineraryDate');
        const mainDatePicker = document.getElementById('datePicker');
        if (modalDatePicker && window.hasTrip && window.dataInicioViagem && window.dataFimViagem) {
            modalDatePicker.setAttribute('min', window.dataInicioViagem);
            modalDatePicker.setAttribute('max', window.dataFimViagem);
            let selectedDate = mainDatePicker && mainDatePicker.value ? mainDatePicker.value : window.dataInicioViagem;
            modalDatePicker.value = selectedDate;
        }
    }, 200); // Aguarda renderização do modal
}

function closeModal() {
    const modal = document.getElementById('placeDetailsModal');
    // Animação de saída
    modal.querySelector('div').classList.remove('scale-100', 'opacity-100');
    modal.querySelector('div').classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        currentDetailedPlace = null; // Limpa o lugar detalhado
    }, 300); // Deve corresponder à duração da transição CSS
}

function getPlaceType(types) {
    if (!types) return 'place';
    if (types.includes('tourist_attraction') || types.includes('museum') || types.includes('park')) return 'attraction';
    if (types.includes('restaurant') || types.includes('food') || types.includes('meal_takeaway')) return 'restaurant';
    if (types.includes('lodging') || types.includes('hotel')) return 'hotel';
    return types.length > 0 ? types[0] : 'place';
}

function getTypeColorClass(type) {
    const colors = {
        attraction: 'bg-purple-100 text-purple-800',
        restaurant: 'bg-orange-100 text-orange-800',
        hotel: 'bg-blue-100 text-blue-800'
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
}

function getTypeLabel(type) {
    const labels = {
        attraction: 'Atração',
        restaurant: 'Restaurante',
        hotel: 'Hotel'
    };
    return labels[type] || type.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
}
</script>
