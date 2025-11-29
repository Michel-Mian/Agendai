// Este script processa respostas da IA, identifica locais sugeridos ([[nome]]), transforma em links clicáveis e abre o modal de detalhes usando Google Places

function processIaResponse(text) {
    // Substitui [[nome do lugar]] por links clicáveis
    return text.replace(/\[\[(.*?)\]\]/g, (match, placeName) => {
        return `<a href="#" class="ia-place-link" data-place-name="${placeName}">${placeName}</a>`;
    });
}

// Após renderizar a resposta, adicione o evento de clique global
if (!window._iaPlaceLinkHandlerAdded) {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('ia-place-link')) {
            e.preventDefault();
            const placeName = e.target.getAttribute('data-place-name');
            console.log('Clicou no link IA:', placeName);
            buscarPlaceIdEExibirModal(placeName);
        }
    });
    window._iaPlaceLinkHandlerAdded = true;
}

function buscarPlaceIdEExibirModal(placeName) {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
        alert('Google Maps API não está disponível!');
        return;
    }
    const service = new google.maps.places.PlacesService(document.createElement('div'));
    service.findPlaceFromQuery({
        query: placeName,
        fields: ['place_id']
    }, function(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK && results.length > 0) {
            const placeId = results[0].place_id;
            service.getDetails({
                placeId: placeId,
                fields: ['place_id', 'name', 'formatted_address', 'types', 'rating', 'user_ratings_total', 'photos', 'opening_hours', 'website', 'formatted_phone_number', 'reviews', 'geometry', 'vicinity']
            }, function(place, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    showIaPlaceDetailsModal(place);
                } else {
                    alert('Detalhes do local não encontrados!');
                }
            });
        } else {
            alert('Local não encontrado!');
        }
    });
}

// Função para preencher e exibir o modal de detalhes do local
function showIaPlaceDetailsModal(placeDetails) {
    const modal = document.getElementById('ia-place-details-modal');
    const panel = document.getElementById('ia-place-details-modal-panel');
    
    // Mostrar modal com loading
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
        document.body.style.overflow = 'hidden';
    }, 10);

    // Fotos
    const photosContainer = document.getElementById('ia-place-details-photos-container');
    photosContainer.innerHTML = '';
    if (placeDetails.photos && placeDetails.photos.length > 0) {
        photosContainer.innerHTML = `<div class="flex space-x-2 overflow-x-auto pb-2">
            ${placeDetails.photos.slice(0, 5).map(photo => 
                `<img src="${photo.getUrl({ maxWidth: 300, maxHeight: 200 })}" 
                     class="h-32 w-auto object-cover rounded-md shadow-sm" 
                     alt="Foto de ${placeDetails.name}">`
            ).join('')}
        </div>`;
    }

    // Nome e Endereço
    document.getElementById('ia-place-details-name').textContent = placeDetails.name || 'Nome não disponível';
    document.getElementById('ia-place-details-address').textContent = placeDetails.formatted_address || 'Endereço não disponível';

    // Tipo
    const placeType = getPlaceType(placeDetails.types);
    const typeEl = document.getElementById('ia-place-details-type');
    typeEl.textContent = getTypeLabel(placeType);
    typeEl.className = `px-3 py-1 text-xs rounded-full font-medium ${getTypeColorClass(placeType)}`;

    // Avaliação
    const ratingContainer = document.getElementById('ia-place-details-rating-container');
    if (placeDetails.rating) {
        ratingContainer.innerHTML = `
            <svg class="w-4 h-4 mr-1 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            ${placeDetails.rating} (${placeDetails.user_ratings_total || 0} avaliações)
        `;
    } else {
        ratingContainer.innerHTML = '';
    }

    // Vicinity
    const vicinityEl = document.getElementById('ia-place-details-vicinity');
    vicinityEl.textContent = placeDetails.vicinity || '';

    // Website
    const websiteEl = document.getElementById('ia-place-details-website');
    if (placeDetails.website) {
        websiteEl.innerHTML = `<p class="text-blue-600 hover:underline mb-2"><a href="${placeDetails.website}" target="_blank">Site Oficial</a></p>`;
    } else {
        websiteEl.innerHTML = '';
    }

    // Telefone
    const phoneEl = document.getElementById('ia-place-details-phone');
    if (placeDetails.formatted_phone_number) {
        phoneEl.textContent = `Telefone: ${placeDetails.formatted_phone_number}`;
    } else {
        phoneEl.textContent = '';
    }

    // Horário de Funcionamento
    const openingHoursEl = document.getElementById('ia-place-details-opening-hours');
    if (placeDetails.opening_hours && placeDetails.opening_hours.weekday_text) {
        openingHoursEl.innerHTML = `
            <div class="mt-4">
                <h4 class="font-semibold text-gray-800">Horário de Funcionamento:</h4>
                <ul class="text-sm text-gray-600 list-disc list-inside">
                    ${placeDetails.opening_hours.weekday_text.map(day => `<li>${day}</li>`).join('')}
                </ul>
            </div>
        `;
    } else {
        openingHoursEl.innerHTML = '';
    }

    // Reviews
    const reviewsEl = document.getElementById('ia-place-details-reviews');
    if (placeDetails.reviews && placeDetails.reviews.length > 0) {
        reviewsEl.innerHTML = `
            <div class="mt-4">
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
            </div>
        `;
    } else {
        reviewsEl.innerHTML = '';
    }

    // Configurar date pickers
    setTimeout(() => {
        const dateInput = document.getElementById('ia-itineraryDate');
        const timeSelect = document.getElementById('ia-itineraryTime');
        // Prefer using shared setItineraryDateLimits if available (it already supports trip_id fallback)
        if (typeof setItineraryDateLimits === 'function') {
            try { setItineraryDateLimits(); } catch (e) { console.error('setItineraryDateLimits error', e); }
        } else {
            if (dateInput && window.hasTrip && window.dataInicioViagem && window.dataFimViagem) {
                const dataInicio = normalizarData(window.dataInicioViagem);
                const dataFim = normalizarData(window.dataFimViagem);
                dateInput.setAttribute('min', dataInicio);
                dateInput.setAttribute('max', dataFim);
                dateInput.value = dataInicio;
            }
        }
        
        if (timeSelect && !timeSelect.innerHTML) {
            timeSelect.innerHTML = generateTimeOptions();
        }
    }, 100);

    // Salvar dados para uso posterior
    modal.dataset.placeId = placeDetails.place_id;
    modal.dataset.placeName = placeDetails.name;
    modal.dataset.placeAddress = placeDetails.formatted_address;
    modal.dataset.placePhoto = (placeDetails.photos && placeDetails.photos.length > 0) 
        ? placeDetails.photos[0].getUrl({maxWidth: 300, maxHeight: 300}) 
        : '';
    modal.dataset.latitude = placeDetails.geometry?.location?.lat() || '';
    modal.dataset.longitude = placeDetails.geometry?.location?.lng() || '';
    // Associate modal with current trip id if available so date limits can be fetched per-trip
    if (window.currentTripId) modal.dataset.tripId = window.currentTripId;

    // Configurar botão de salvar
    setupSaveButton(placeDetails);
    
    // Configurar botão de fechar
    setupCloseButton();
}

function setupSaveButton(placeDetails) {
    const saveBtn = document.getElementById('save-place-to-trip-btn');
    if (!saveBtn) return;

    // Use addEventListener and preventDefault to ensure no form submission happens
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const dateInput = document.getElementById('ia-itineraryDate');
        const returnDateInput = document.getElementById('ia-itineraryReturnDate');
        const timeSelect = document.getElementById('ia-itineraryTime');

        const selectedDate = dateInput?.value;
        const selectedReturnDate = returnDateInput?.value || null;
        const selectedTime = timeSelect?.value;

        if (!selectedDate || !selectedTime) {
            safeNotify('Por favor, selecione data e horário da visita!', 'error');
            return;
        }

        saveBtn.disabled = true;
        const originalText = saveBtn.textContent;
        saveBtn.textContent = 'Salvando...';

        const modal = document.getElementById('ia-place-details-modal');
        // Preparar payload conforme ExploreController@store espera
        const lat = modal.dataset.latitude ? parseFloat(modal.dataset.latitude) : null;
        const lng = modal.dataset.longitude ? parseFloat(modal.dataset.longitude) : null;

        const data = {
            placeid_ponto_interesse: modal.dataset.placeId,
            nome_ponto_interesse: modal.dataset.placeName,
            categoria: (function(){
                const t = getPlaceType(placeDetails.types);
                return (t === 'hotel' ? 'hotel' : t);
            })(),
            latitude: lat,
            longitude: lng,
            data_ponto_interesse: selectedDate,
            data_retorno_ponto_interesse: selectedReturnDate,
            hora_ponto_interesse: selectedTime,
            desc_ponto_interesse: placeDetails.formatted_address || ''
        };

        fetch('/explore', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
            let result = {};
            try {
                result = await response.json();
            } catch (err) {
                console.error('Erro ao parsear JSON:', err);
                safeNotify('Resposta inválida do servidor', 'error');
                return;
            }

            if (result.success) {
                safeNotify('Ponto de interesse adicionado com sucesso!', 'success');
                if (typeof window.closeIaPlaceDetailsModal === 'function') window.closeIaPlaceDetailsModal();
                else if (typeof closeIaPlaceDetailsModal === 'function') closeIaPlaceDetailsModal();

                // Atualiza o itinerário via AJAX se possível; caso contrário, apenas notifica sucesso sem reload
                if (typeof updateItineraryDisplay === 'function') {
                    try { updateItineraryDisplay(); } catch (e) { console.error('updateItineraryDisplay error', e); safeNotify('Ponto salvo — atualize a página para ver o itinerário.', 'info'); }
                } else if (typeof reloadItinerary === 'function') {
                    try { reloadItinerary(); } catch (e) { console.error('reloadItinerary error', e); safeNotify('Ponto salvo — atualize a página para ver o itinerário.', 'info'); }
                } else {
                    safeNotify('Ponto salvo com sucesso. Atualize a página para ver as mudanças.', 'info');
                }
            } else {
                safeNotify((result.error && typeof result.error === 'string') ? result.error : (result.error?.message || 'Erro ao adicionar ponto de interesse!'), 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
            safeNotify('Erro ao conectar com o servidor!', 'error');
        });
    });
}

function setupCloseButton() {
    const modal = document.getElementById('ia-place-details-modal');
    const panel = document.getElementById('ia-place-details-modal-panel');
    const closeBtn = document.getElementById('close-ia-place-details-btn');
    
    function closeIaPlaceDetailsModal() {
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    
    if (closeBtn) {
        closeBtn.onclick = closeIaPlaceDetailsModal;
    }
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeIaPlaceDetailsModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeIaPlaceDetailsModal();
        }
    });
    
    window.closeIaPlaceDetailsModal = closeIaPlaceDetailsModal;
}

// Funções auxiliares
function normalizarData(data) {
    if (!data) return null;
    if (/^\d{4}-\d{2}-\d{2}$/.test(data)) return data;
    if (data.includes('T')) return data.split('T')[0];
    if (data.includes('/')) {
        const [d, m, y] = data.split('/');
        return `${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
    }
    return data;
}

// Notificação segura: usa showNotification se disponível, senão alert/console
function safeNotify(message, type = 'info') {
    if (typeof showNotification === 'function') {
        try { showNotification(message, type); } catch (e) { console.error('showNotification error', e); }
    } else if (typeof window?.toastr !== 'undefined') {
        // se existir toastr global
        if (type === 'success') window.toastr.success(message);
        else if (type === 'error') window.toastr.error(message);
        else window.toastr.info(message);
    } else {
        if (type === 'error') alert(message); else console.log(type.toUpperCase() + ': ' + message);
    }
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

function generateTimeOptions() {
    let options = '<option value="">Selecione um horário</option>';
    for (let hour = 0; hour < 24; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const hourStr = hour.toString().padStart(2, '0');
            const minuteStr = minute.toString().padStart(2, '0');
            const timeValue = `${hourStr}:${minuteStr}`;
            options += `<option value="${timeValue}">${timeValue}</option>`;
        }
    }
    return options;
}

// Exportar para uso global
window.processIaResponse = processIaResponse;
window.buscarPlaceIdEExibirModal = buscarPlaceIdEExibirModal;
window.showIaPlaceDetailsModal = showIaPlaceDetailsModal;