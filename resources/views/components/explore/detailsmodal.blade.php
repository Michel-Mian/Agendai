<div id="placeDetailsModal"
     class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 hidden"
     style="background: rgba(17,24,39,0.3); backdrop-filter: blur(8px);">
    <div class="explore-details-modal-base bg-white rounded-lg shadow-xl relative flex flex-col max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div id="modalContent" class="p-4 overflow-y-auto flex-1 w-full"> <!-- max-h-[70vh] para garantir rolagem -->
            <h2 class="text-xl sm:text-2xl font-bold mb-4 text-gray-800" id="detailedPlaceName">Nome do Local</h2>
            <p class="text-gray-600 mb-2" id="detailedPlaceAddress">Endereço do Local</p>
            <p class="text-gray-700 leading-relaxed" id="detailedPlaceDescription">
                Descrição detalhada do local será carregada aqui.
            </p>
            <div id="detailedPlacePhotos" class="grid grid-cols-2 gap-4 mt-4"></div>
            <p class="text-gray-800 font-semibold mt-4" id="detailedPlaceRating">Avaliação: N/A</p>
            <p class="text-gray-800 font-semibold" id="detailedPlaceType">Tipo: N/A</p>
        </div>
        <!-- O rodapé pode ser fixo na base do modal -->
        <!-- <div class="p-4 border-t bg-white"> ...botões... </div> -->
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

            // --- NOVO FORMULÁRIO GERADO VIA JS   ---
            let horarioAtual = horarioBanco || "00:00";
            let alterarHorarioForm = "";
            if (fromItinerary && databaseId) {
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                alterarHorarioForm = `
                    <div class="flex flex-col gap-3">
                        <form method="POST" action="/explore/ponto-interesse/${databaseId}/horario" class="flex items-center gap-2" onsubmit="handleHorarioSubmit(event, ${databaseId})">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="POST">
                            <label for="novo_horario" class="text-sm font-medium text-gray-700">Horário:</label>
                            <input type="time" id="novo_horario" name="novo_horario" value="${horarioAtual}" class="border rounded px-2 py-1" required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded transition-colors">
                                Alterar para esse horário
                            </button>
                        </form>
                        <button onclick="removePontoFromItinerary('${databaseId}')" class="mt-2 px-6 py-3 text-sm font-medium text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-lg transition-all duration-200 shadow-lg w-full sm:w-auto border-0" style="background-color: #ef4444 !important; color: white !important;">
                            🗑️ Remover do Itinerário
                        </button>
                    </div>
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

/**
 * Handle form submission for changing time
 */
function handleHorarioSubmit(event, pontoId) {
    event.preventDefault();
    console.log('=== INICIANDO ALTERAÇÃO DE HORÁRIO ===');
    console.log('Ponto ID:', pontoId);
    
    const form = event.target;
    const formData = new FormData(form);
    const novoHorario = formData.get('novo_horario');
    
    console.log('Novo horário:', novoHorario);
    console.log('Action URL:', form.action);
    
    if (!novoHorario) {
        console.log('Erro: Horário não fornecido');
        if (typeof showNotification === 'function') {
            showNotification('Por favor, selecione um horário válido', 'error');
        } else {
            alert('Por favor, selecione um horário válido');
        }
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Alterando...';
    
    console.log('Enviando requisição...');
    
    // Preparar dados para envio
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value ||
                      window.Laravel?.csrfToken;
                      
    console.log('CSRF Token:', csrfToken);
    
    if (!csrfToken) {
        console.error('CSRF Token não encontrado!');
        if (typeof showNotification === 'function') {
            showNotification('Erro: Token de segurança não encontrado', 'error');
        } else {
            alert('Erro: Token de segurança não encontrado');
        }
        return;
    }
    
    // Validar formato do horário
    const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
    if (!timeRegex.test(novoHorario)) {
        console.error('Formato de horário inválido:', novoHorario);
        if (typeof showNotification === 'function') {
            showNotification('Formato de horário inválido. Use HH:MM', 'error');
        } else {
            alert('Formato de horário inválido. Use HH:MM');
        }
        return;
    }
    
    // Criar dados do formulário manualmente para garantir formato correto
    const bodyData = new URLSearchParams();
    bodyData.append('novo_horario', novoHorario);
    bodyData.append('_token', csrfToken);
    
    console.log('Dados a serem enviados:', Object.fromEntries(bodyData));
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: bodyData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Verificar se a resposta é ok
        if (!response.ok) {
            // Para erros 422, vamos tentar ler a resposta JSON mesmo assim
            if (response.status === 422) {
                return response.json().then(errorData => {
                    throw new Error(`Validation Error: ${JSON.stringify(errorData)}`);
                });
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Verificar se a resposta é JSON
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
            // Se não é JSON, vamos ler como texto para debug
            return response.text().then(text => {
                console.error('Resposta não é JSON:', text);
                throw new Error('A resposta do servidor não é JSON válido: ' + text.substring(0, 100));
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Resposta do servidor:', data);
        
        if (data.success) {
            console.log('Horário alterado com sucesso!');
            if (typeof showNotification === 'function') {
                showNotification('Horário alterado com sucesso!', 'success');
            } else {
                alert('Horário alterado com sucesso!');
            }
            
            // Atualizar o horário no DOM sem recarregar a página
            updateHorarioInInterface(pontoId, novoHorario);
            
            // Fechar modal após sucesso
            closeModal();
        } else {
            console.log('Erro retornado pelo servidor:', data.error);
            if (typeof showNotification === 'function') {
                showNotification(data.error || 'Erro ao alterar horário', 'error');
            } else {
                alert(data.error || 'Erro ao alterar horário');
            }
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        if (typeof showNotification === 'function') {
            showNotification('Erro ao alterar horário: ' + error.message, 'error');
        } else {
            alert('Erro ao alterar horário: ' + error.message);
        }
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.log('=== FIM DA ALTERAÇÃO DE HORÁRIO ===');
    });
}

/**
 * Atualiza o horário na interface após alteração
 * @param {number} pontoId - ID do ponto de interesse
 * @param {string} novoHorario - Novo horário no formato HH:MM
 */
function updateHorarioInInterface(pontoId, novoHorario) {
    const formattedTime = formatHorario(novoHorario);
    console.log(`Atualizando horário do ponto ${pontoId} para ${formattedTime}`);
    
    // Atualizar no painel lateral (aba Suas Rotas)
    const pontosContainer = document.getElementById('pontos-container');
    if (pontosContainer) {
        const pontoElements = pontosContainer.querySelectorAll('.group');
        pontoElements.forEach((element, index) => {
            const onclickAttr = element.getAttribute('onclick');
            // Verificar se o onclick contém o índice do ponto que corresponde ao ID
            if (onclickAttr) {
                // Extrair o primeiro parâmetro (index) do focusOnPoint
                const match = onclickAttr.match(/focusOnPoint\((\d+)/);
                if (match) {
                    const pointIndex = parseInt(match[1]);
                    // Verificar se esse índice corresponde ao ponto que estamos atualizando
                    // Para isso, vamos usar uma abordagem diferente - buscar pelo texto/conteúdo
                    const clockElement = element.querySelector('.fa-clock');
                    if (clockElement) {
                        const timeSpan = clockElement.parentElement.querySelector('span');
                        if (timeSpan) {
                            timeSpan.textContent = formattedTime;
                            console.log(`Horário atualizado no elemento ${index}`);
                        }
                    } else {
                        // Se não há elemento de horário, criar um
                        const dateElement = element.querySelector('.fa-calendar').parentElement;
                        if (dateElement && dateElement.parentElement) {
                            const timeHtml = `
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-clock text-blue-900"></i>
                                    <span>${formattedTime}</span>
                                </div>
                            `;
                            dateElement.insertAdjacentHTML('afterend', timeHtml);
                            console.log(`Elemento de horário criado para ponto ${index}`);
                        }
                    }
                }
            }
        });
    }

    // Atualizar marcadores do mapa se existirem
    if (typeof pontosInteresseMarkers !== 'undefined') {
        pontosInteresseMarkers.forEach((markerObj, index) => {
            // Verificar se o marcador corresponde ao ponto atualizado
            if (markerObj && markerObj.infoWindow) {
                const infoContent = markerObj.infoWindow.getContent();
                if (infoContent && typeof infoContent === 'string' && infoContent.includes('Horário:')) {
                    const updatedContent = infoContent.replace(
                        /Horário: \d{2}:\d{2}/,
                        `Horário: ${formattedTime}`
                    );
                    markerObj.infoWindow.setContent(updatedContent);
                    console.log(`InfoWindow atualizado para marcador ${index}`);
                }
            }
        });
    }

    console.log(`Atualização do horário concluída para ponto ${pontoId}`);
}

/**
 * Formata horário para exibição
 * @param {string} horario - Horário no formato HH:MM
 * @returns {string} Horário formatado
 */
function formatHorario(horario) {
    if (!horario) return '';
    
    // Se já está no formato correto, retorna como está
    if (horario.includes(':')) {
        return horario;
    }
    
    // Caso contrário, tenta formatar
    const time = new Date(`2000-01-01 ${horario}`);
    if (!isNaN(time.getTime())) {
        return time.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }
    
    return horario;
}
</script>
