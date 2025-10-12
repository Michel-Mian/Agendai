// A função showOnMap precisa estar no escopo global se for chamada via onclick no HTML.
function showOnMap(lat, lng, hotelName) {
  const url = `http://google.com/maps?q=${lat},${lng}`
  window.open(url, "_blank")
}

document.addEventListener("DOMContentLoaded", () => {
  // Verificar se estamos na página de hotéis
  const isHotelsPage = document.getElementById("hotel-search-form") !== null;
  
  if (!isHotelsPage) {
    return;
  }

  const form = document.getElementById("hotel-search-form")
  const searchBtn = document.getElementById("search-btn")
  const resultsContainer = document.getElementById("hotel-results")
  const loadingIndicator = document.getElementById("loading-indicator")
  const errorMessage = document.getElementById("error-message")
  const errorText = document.getElementById("error-text")
  const filtersDiv = document.getElementById("filters")
  const resultsSummary = document.getElementById("results-summary")
  const resultsCount = document.getElementById("results-count")
  const loadMoreBtn = document.getElementById("load-more")

  const tripSelectionModal = document.getElementById("trip-selection-modal")
  const tripSelect = document.getElementById("trip-select")
  const cancelTripBtn = document.getElementById("cancel-trip-btn")
  const confirmTripBtn = document.getElementById("add-to-trip-confirm-btn")

  let allHotels = []
  let filteredHotels = []
  let nextPageToken = null
  let currentQuery = ""
  let currentCheckIn = ""
  let currentCheckOut = ""

  // Centralizando todos os filtros em um único objeto
  let allFilters = {
    // Filtros básicos (já existentes)
    hotelClass: "",
    priceOrder: "",
    rating: "",
    // Filtros avançados (novos)
    minPrice: "",
    maxPrice: "",
    propertyTypes: [],
    amenities: [],
  }

  const trips = window.userTrips || []

  const today = new Date().toISOString().split("T")[0]
  const checkinInput = document.getElementById("check-in-date")
  const checkoutInput = document.getElementById("check-out-date")
  
  if (checkinInput) {
    checkinInput.setAttribute("min", today)
  }
  
  if (checkoutInput) {
    checkoutInput.setAttribute("min", today)
  }

  if (checkinInput) {
    checkinInput.addEventListener("change", function () {
      const checkInDate = this.value
      if (checkoutInput) {
        checkoutInput.setAttribute("min", checkInDate)
      }
    })
  }

  // Listeners para filtros básicos
  const hotelClassFilter = document.getElementById("hotel-class-filter")
  const priceOrderFilter = document.getElementById("price-order")
  const ratingFilter = document.getElementById("rating-filter")
  const clearBasicFiltersBtn = document.getElementById("clear-filters")
  const loadMoreButton = document.getElementById("load-more")

  if (hotelClassFilter) hotelClassFilter.addEventListener("change", applyFilters)
  if (priceOrderFilter) priceOrderFilter.addEventListener("change", applyFilters)
  if (ratingFilter) ratingFilter.addEventListener("change", applyFilters)
  if (clearBasicFiltersBtn) clearBasicFiltersBtn.addEventListener("click", clearAllFilters)
  if (loadMoreButton) loadMoreButton.addEventListener("click", loadMoreResults)

  // Listeners para os filtros AVANÇADOS (aqui unificamos a lógica)
  const advancedMinPrice = document.getElementById('advanced-min-price');
  const advancedMaxPrice = document.getElementById('advanced-max-price');
  const propertyTypeCheckboxes = document.querySelectorAll('.property-type-checkbox');
  const amenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
  
  if (advancedMinPrice) advancedMinPrice.addEventListener('input', syncFiltersAndSearch);
  if (advancedMaxPrice) advancedMaxPrice.addEventListener('input', syncFiltersAndSearch);
  propertyTypeCheckboxes.forEach(checkbox => checkbox.addEventListener('change', syncFiltersAndSearch));
  amenityCheckboxes.forEach(checkbox => checkbox.addEventListener('change', syncFiltersAndSearch));
  
  // Função para sincronizar os filtros e aplicar a busca
  function syncFiltersAndSearch() {
      // Atualiza o objeto allFilters com os valores atuais dos campos
      allFilters.minPrice = advancedMinPrice.value;
      allFilters.maxPrice = advancedMaxPrice.value;
      allFilters.propertyTypes = Array.from(propertyTypeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
      allFilters.amenities = Array.from(amenityCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
      
      // Se já houver uma pesquisa em andamento, aplica os filtros nos resultados
      if (currentQuery && currentCheckIn && currentCheckOut) {
          applyFilters();
      }
      
      // Atualiza o display dos filtros
      updateFilterDisplay('property-types-display', 'Tipos de Propriedade', allFilters.propertyTypes);
      updateFilterDisplay('amenities-display', 'Amenidades', allFilters.amenities);
  }

  // Função auxiliar para atualizar o display de filtros selecionados
  function updateFilterDisplay(displayId, label, selectedItems) {
      const displayElement = document.getElementById(displayId);
      if (displayElement) {
          if (selectedItems.length > 0) {
              const itemsText = selectedItems.length === 1
                  ? selectedItems[0]
                  : `${selectedItems.length} selecionado(s)`;
              displayElement.textContent = `${label}: ${itemsText}`;
              displayElement.className = 'text-gray-800 font-medium';
          } else {
              displayElement.textContent = `Selecione as ${label.toLowerCase()}`;
              displayElement.className = 'text-gray-500';
          }
      }
  }


  resultsContainer.addEventListener("click", (event) => {
    // Listener para o botão "Adicionar a viagem"
    const targetButton = event.target.closest("[data-hotel-json]")
    if (targetButton) {
      try {
        const hotelJsonString = targetButton.dataset.hotelJson
        const hotelData = JSON.parse(hotelJsonString)
        addToTrip(hotelData, currentCheckIn, currentCheckOut)
      } catch (e) {
        showError("Erro ao processar dados do hotel.")
        console.error("hotels.js: Erro ao parsear JSON do hotel:", e)
      }
    }

    // Listener para o botão "Ver no Mapa" usando delegação de eventos
    const mapButton = event.target.closest("[data-map-latitude][data-map-longitude][data-map-hotel-name]")
    if (mapButton) {
      const lat = mapButton.dataset.mapLatitude
      const lng = mapButton.dataset.mapLongitude
      const hotelName = mapButton.dataset.mapHotelName
      showOnMap(lat, lng, hotelName)
    }
  })

  form.addEventListener("submit", (event) => {
    event.preventDefault()

    const query = document.getElementById("hotel-query").value.trim()
    const checkInDate = document.getElementById("check-in-date").value
    const checkOutDate = document.getElementById("check-out-date").value
    
    // Coletar os valores dos filtros avançados antes de fazer a requisição
    syncFiltersAndSearch();
    
    if (!query || !checkInDate || !checkOutDate) {
      showError("Por favor, preencha todos os campos da pesquisa.")
      return
    }

    if (new Date(checkOutDate) <= new Date(checkInDate)) {
      showError("A data de check-out deve ser posterior à data de check-in.")
      return
    }

    currentQuery = query
    currentCheckIn = checkInDate
    currentCheckOut = checkOutDate

    clearResults()
    showLoading(true)
    searchHotels(query, checkInDate, checkOutDate)
  })

  function searchHotels(query, checkInDate, checkOutDate, pageToken = null) {
    const csrfToken = document.querySelector('input[name="_token"]').value

    const formData = new FormData()
    formData.append("q", query)
    formData.append("check_in_date", checkInDate)
    formData.append("check_out_date", checkOutDate)

    // Adicionar filtros avançados se existirem
    if (allFilters.minPrice) {
      formData.append("min_price", allFilters.minPrice)
    }
    if (allFilters.maxPrice) {
      formData.append("max_price", allFilters.maxPrice)
    }
    if (allFilters.propertyTypes.length > 0) {
      formData.append("property_types", allFilters.propertyTypes.join(","))
    }
    if (allFilters.amenities.length > 0) {
      formData.append("amenities", allFilters.amenities.join(","))
    }

    if (pageToken) {
      formData.append("next_page_token", pageToken)
    }

    const url = "/hotels/search"

    fetch(url, {
      method: "POST",
      body: formData,
      headers: {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          return response
            .json()
            .then((err) => {
              throw new Error(err.error || `Erro HTTP: ${response.status}`)
            })
            .catch(() => {
              throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`)
            })
        }
        return response.json()
      })
      .then((data) => {
        showLoading(false)

        if (data.error) {
          throw new Error(data.error)
        }

        const hotels = [...(data.properties || []), ...(data.ads || [])]

        if (pageToken) {
          allHotels = [...allHotels, ...hotels]
        } else {
          allHotels = hotels
        }

        nextPageToken = data.serpapi_pagination?.next_page_token || null

        applyFilters()
        showFilters(true)
      })
      .catch((error) => {
        showLoading(false)
        showError(`Erro na pesquisa: ${error.message}`)
      })
  }

  function displayResults(hotels) {
    if (!hotels || hotels.length === 0) {
      resultsContainer.innerHTML = `
                <div class="col-span-full">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                        <i class="fa-solid fa-info-circle text-blue-500 text-2xl mb-2"></i>
                        <p class="text-blue-800">Nenhum hotel encontrado para sua pesquisa. Tente outros termos ou datas.</p>
                    </div>
                </div>
            `
      showFilters(false)
      return
    }

    let htmlContent = ""

    hotels.forEach((hotel, index) => {
      const hotelName = hotel.name || "Nome não disponível"
      const hotelDescription = hotel.description || ""
      const hotelLink = hotel.link || "#"
      const hotelSponsored = hotel.sponsored || false
      const hotelEcoCertified = hotel.eco_certified || false

      const hotelThumbnail =
        hotel.thumbnail ||
        (hotel.images && hotel.images[0] ? hotel.images[0].thumbnail : null) ||
        "https://placehold.co/300x200"

      const hotelRating = hotel.overall_rating || "N/A"
      const hotelReviews = hotel.reviews || "0"
      const locationRating = hotel.location_rating || null

      let priceInfo = ""
      if (hotel.rate_per_night) {
        priceInfo = hotel.rate_per_night.lowest || hotel.rate_per_night.before_taxes_fees || ""
      } else if (hotel.extracted_price) {
        priceInfo = `R$ ${hotel.extracted_price}`
      } else if (hotel.price) {
        priceInfo = hotel.price
      }

      const hotelClass = hotel.extracted_hotel_class || hotel.hotel_class || ""

      const amenities = hotel.amenities || []
      const topAmenities = amenities.slice(0, 3)

      const checkInTime = hotel.check_in_time || ""
      const checkOutTime = hotel.check_out_time || ""

      const gpsCoords = hotel.gps_coordinates || null

      // Escapa aspas duplas para o atributo data-hotel-json.
const hotelJsonString = JSON.stringify(hotel).replace(/"/g, '&quot;');
      htmlContent += `
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 ${hotelSponsored ? "ring-2 ring-yellow-400" : ""}">
                    <div class="relative">
                        ${hotelSponsored ? '<div class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-medium z-10">Patrocinado</div>' : ""}
                        ${hotelEcoCertified ? '<div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium z-10 flex items-center"><i class="fa-solid fa-leaf mr-1"></i>Eco</div>' : ""}
                        
                        <img src="${hotelThumbnail}" 
                             alt="${hotelName}"
                             class="w-full h-48 object-cover"
                             onerror="this.src='https://placehold.co/300x200'">
                    </div>
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">${hotelName}</h3>
                            ${hotelClass ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium ml-2">${hotelClass}★</span>` : ""}
                        </div>
                        
                        ${hotelDescription ? `<p class="text-gray-600 text-sm mb-3 line-clamp-2">${hotelDescription.substring(0, 100)}${hotelDescription.length > 100 ? "..." : ""}</p>` : ""}
                        
                        <div class="space-y-2 mb-4">
                            ${
                              hotelRating !== "N/A"
                                ? `
                                <div class="flex items-center">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium flex items-center">
                                        <i class="fa-solid fa-star mr-1"></i> ${hotelRating}/5
                                    </span>
                                    <span class="text-gray-500 text-sm ml-2">(${hotelReviews} avaliações)</span>
                                </div>
                            `
                                : ""
                            }
                            
                            ${
                              locationRating
                                ? `
                                <div class="text-gray-600 text-sm flex items-center">
                                    <i class="fa-solid fa-map-marker-alt mr-1"></i>
                                    Localização: ${locationRating}/5
                                </div>
                            `
                                : ""
                            }
                        </div>
                        
                        ${
                          topAmenities.length > 0
                            ? `
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    ${topAmenities.map((amenity) => `<span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">${amenity}</span>`).join("")}
                                    ${amenities.length > 3 ? `<span class="text-gray-500 text-xs">+${amenities.length - 3} mais</span>` : ""}
                                </div>
                            </div>
                        `
                            : ""
                        }
                        
                        ${
                          checkInTime && checkOutTime
                            ? `
                            <div class="mb-4 text-gray-600 text-sm flex items-center">
                                <i class="fa-solid fa-clock mr-1"></i>
                                Check-in: ${checkInTime} | Check-out: ${checkOutTime}
                            </div>
                        `
                            : ""
                        }
                        
                        ${
                          priceInfo
                            ? `
                            <div class="mb-4">
                                <div class="text-2xl font-bold text-green-600">${priceInfo}</div>
                                <div class="text-gray-500 text-sm">por noite</div>
                            </div>
                        `
                            : ""
                        }
                        
                        <div class="space-y-2">
                            <a href="${hotelLink}" 
                               target="_blank" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
                                <i class="fa-solid fa-external-link-alt mr-2"></i> Ver Detalhes
                            </a>
                            ${
                              gpsCoords
                                ? `
                                <button data-map-latitude="${gpsCoords.latitude}" data-map-longitude="${gpsCoords.longitude}" data-map-hotel-name="${hotelName.replace(/"/g, '\\"').replace(/'/g, "\\'")}"
                                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
                                    <i class="fa-solid fa-map mr-2"></i> Ver no Mapa
                                </button>
                            `
                                : ""
                            }
                            <button data-hotel-json="${hotelJsonString}"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
                                <i class="fa fa-bed" aria-hidden="true"></i> Adicionar a viagem
                            </button>
                        </div>
                    </div>
                </div>
            `
    })

    resultsContainer.innerHTML = htmlContent

    if (nextPageToken) {
      loadMoreBtn.classList.remove("hidden")
    } else {
      loadMoreBtn.classList.add("hidden")
    }
  }

  function applyFilters() {
    // Atualizar filtros básicos
    if (hotelClassFilter) allFilters.hotelClass = hotelClassFilter.value || "";
    if (priceOrderFilter) allFilters.priceOrder = priceOrderFilter.value || "";
    if (ratingFilter) allFilters.rating = parseFloat(ratingFilter.value) || 0;

    filteredHotels = allHotels.filter((hotel) => {
      // Filtro por classe do hotel
      if (allFilters.hotelClass && hotel.extracted_hotel_class != allFilters.hotelClass) {
        return false
      }

      // Filtro por avaliação
      if (allFilters.rating && (!hotel.overall_rating || hotel.overall_rating < allFilters.rating)) {
        return false
      }
      
      // Filtro por preço mínimo
      const hotelPrice = hotel.rate_per_night?.extracted_lowest || hotel.extracted_price || 0;
      if (allFilters.minPrice && hotelPrice < allFilters.minPrice) {
        return false;
      }

      // Filtro por preço máximo
      if (allFilters.maxPrice && hotelPrice > allFilters.maxPrice) {
        return false;
      }

      // Filtro por tipo de propriedade
      if (allFilters.propertyTypes.length > 0) {
        const hasPropertyType = allFilters.propertyTypes.some(propType => 
          hotel.property_type?.id === propType
        );
        if (!hasPropertyType) {
          return false;
        }
      }

      // Filtro por amenidades
      if (allFilters.amenities.length > 0) {
        const hasAllAmenities = allFilters.amenities.every(amenityId => 
          (hotel.amenities || []).some(amenity => amenity.id == amenityId)
        );
        if (!hasAllAmenities) {
          return false;
        }
      }

      return true
    })

    // Aplicar ordenação por preço
    if (allFilters.priceOrder) {
      filteredHotels.sort((a, b) => {
        const priceA = a.rate_per_night?.extracted_lowest || a.extracted_price || 0
        const priceB = b.rate_per_night?.extracted_lowest || b.extracted_price || 0

        return allFilters.priceOrder === "asc" ? priceA - priceB : priceB - priceA
      })
    }

    displayResults(filteredHotels)
    updateResultsCount()
  }

  function updateResultsCount() {
    if (resultsCount) {
      resultsCount.textContent = `${filteredHotels.length} hotel(s) encontrado(s)`
      
      // Adicionar informação sobre filtros aplicados
      let filtersInfo = ""
      const activeFilters = []
      
      if (allFilters.minPrice) activeFilters.push(`Min: R$${allFilters.minPrice}`)
      if (allFilters.maxPrice) activeFilters.push(`Max: R$${allFilters.maxPrice}`)
      if (allFilters.propertyTypes.length > 0) activeFilters.push(`${allFilters.propertyTypes.length} tipos`)
      if (allFilters.amenities.length > 0) activeFilters.push(`${allFilters.amenities.length} amenidades`)
      if (allFilters.hotelClass) activeFilters.push(`${allFilters.hotelClass}★`)
      if (allFilters.rating) activeFilters.push(`${allFilters.rating}+ estrelas`)
      
      if (activeFilters.length > 0) {
        filtersInfo = ` - Filtros: ${activeFilters.join(", ")}`
      }
      
      resultsCount.textContent += filtersInfo
    }

    if (resultsSummary) {
      if (filteredHotels.length > 0) {
        resultsSummary.classList.remove("hidden")
      } else {
        resultsSummary.classList.add("hidden")
      }
    }
  }

  function clearAllFilters() {
    // Limpar filtros básicos
    if (hotelClassFilter) hotelClassFilter.value = ""
    if (priceOrderFilter) priceOrderFilter.value = ""
    if (ratingFilter) ratingFilter.value = ""
    
    // Limpar campos do modal de filtros avançados
    const minPriceInput = document.getElementById("advanced-min-price")
    const maxPriceInput = document.getElementById("advanced-max-price")
    const propertyTypeCheckboxes = document.querySelectorAll('.property-type-checkbox')
    const amenityCheckboxes = document.querySelectorAll('.amenity-checkbox')
    
    if (minPriceInput) minPriceInput.value = ""
    if (maxPriceInput) maxPriceInput.value = ""
    
    propertyTypeCheckboxes.forEach(cb => cb.checked = false)
    amenityCheckboxes.forEach(cb => cb.checked = false)
    
    // Atualizar displays do modal
    const propertyTypesDisplay = document.getElementById('property-types-display')
    const amenitiesDisplay = document.getElementById('amenities-display')
    
    if (propertyTypesDisplay) {
      propertyTypesDisplay.textContent = 'Selecione os tipos de propriedade'
      propertyTypesDisplay.className = 'text-gray-500'
    }
    
    if (amenitiesDisplay) {
      amenitiesDisplay.textContent = 'Selecione as amenidades desejadas'
      amenitiesDisplay.className = 'text-gray-500'
    }
    
    // Re-rodar a busca principal sem os filtros
    if (currentQuery && currentCheckIn && currentCheckOut) {
        clearResults();
        showLoading(true);
        searchHotels(currentQuery, currentCheckIn, currentCheckOut);
    } else {
        applyFilters();
    }
  }

  function loadMoreResults() {
    if (nextPageToken) {
      showLoading(true)
      searchHotels(currentQuery, currentCheckIn, currentCheckOut, nextPageToken)
    }
  }

  function showFilters(show) {
    if (filtersDiv) {
      if (show) {
        filtersDiv.classList.remove("hidden")
      } else {
        filtersDiv.classList.add("hidden")
      }
    }
  }

  function showLoading(show) {
    if (loadingIndicator) {
      if (show) {
        loadingIndicator.classList.remove("hidden")
        if (searchBtn) {
          searchBtn.disabled = true
          searchBtn.innerHTML =
            '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Pesquisando...'
        }
      } else {
        loadingIndicator.classList.add("hidden")
        if (searchBtn) {
          searchBtn.disabled = false
          searchBtn.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-2"></i>Pesquisar'
        }
      }
    }
  }

  function showError(message, isSuccess = false) {
    if (errorText && errorMessage) {
      errorText.textContent = message
      errorMessage.classList.remove("hidden")
      if (isSuccess) {
        errorMessage.classList.remove("bg-red-50", "border-red-200")
        errorMessage.classList.add("bg-green-50", "border-green-200")
        const icon = errorMessage.querySelector("i")
        if (icon) {
          icon.className = "fa-solid fa-check-circle text-green-400"
        }
      } else {
        errorMessage.classList.remove("bg-green-50", "border-green-200")
        errorMessage.classList.add("bg-red-50", "border-red-200")
        const icon = errorMessage.querySelector("i")
        if (icon) {
          icon.className = "fa-solid fa-exclamation-triangle text-red-400"
        }
      }
      setTimeout(() => {
        errorMessage.classList.add("hidden")
      }, 5000)
    }
  }

  function clearResults() {
    resultsContainer.innerHTML = ""
    const errorMessage = document.getElementById("error-message")
    if (errorMessage) {
      errorMessage.classList.add("hidden")
    }
    showFilters(false)
    if (resultsSummary) resultsSummary.classList.add("hidden")
    if (loadMoreBtn) loadMoreBtn.classList.add("hidden")
    allHotels = []
    filteredHotels = []
    nextPageToken = null
  }

  function addToTrip(hotelData, checkInDate, checkOutDate) {
    if (!hotelData) {
      showError("Erro: Dados do hotel não disponíveis.")
      return
    }

    if (!trips || trips.length === 0) {
      showError("Para adicionar um hotel, você precisa primeiro criar uma viagem.")
      return
    }

    openTripSelectionModal(hotelData, trips, checkInDate, checkOutDate)
  }

  function openTripSelectionModal(hotelData, trips, checkInDate, checkOutDate) {
    if (!tripSelect || !confirmTripBtn || !tripSelectionModal) {
      console.error("Elementos do modal de seleção de viagem não encontrados")
      return
    }

    // Popula o select com as viagens do usuário
    tripSelect.innerHTML = trips
      .map((trip) => `<option value="${trip.pk_id_viagem}">${trip.destino_viagem}</option>`)
      .join("")

    // Armazena os dados do hotel no botão de confirmação do modal
    confirmTripBtn.dataset.hotel = JSON.stringify(hotelData).replace(/"/g, '"')
    confirmTripBtn.dataset.checkInDate = checkInDate
    confirmTripBtn.dataset.checkOutDate = checkOutDate

    // Remove a classe 'hidden' para exibir o modal
    tripSelectionModal.classList.remove("hidden")
  }

  if (cancelTripBtn) {
    cancelTripBtn.addEventListener("click", () => {
      if (tripSelectionModal) {
        tripSelectionModal.classList.add("hidden")
      }
    })
  }

  if (confirmTripBtn) {
    confirmTripBtn.addEventListener("click", () => {
      if (!tripSelect) return
      
      const selectedTripId = tripSelect.value
      try {
        const hotelData = JSON.parse(confirmTripBtn.dataset.hotel.replace(/"/g, '"'))
        const checkInDate = confirmTripBtn.dataset.checkInDate
        const checkOutDate = confirmTripBtn.dataset.checkOutDate

        if (selectedTripId && checkInDate && checkOutDate) {
          sendHotelToTrip(hotelData, selectedTripId, checkInDate, checkOutDate)
          if (tripSelectionModal) {
            tripSelectionModal.classList.add("hidden")
          }
        } else {
          showError("Por favor, selecione uma viagem e verifique as datas.")
        }
      } catch (e) {
        showError("Erro ao processar os dados do hotel no modal.")
        console.error("hotels.js: Erro ao parsear JSON do modal:", e)
      }
    })
  }

  function sendHotelToTrip(hotel, tripId, checkInDate, checkOutDate) {
    const url = `/hotels/${tripId}`
    const csrfToken = document.querySelector('input[name="_token"]').value

    const requestBody = {
      name: hotel.name,
      link: hotel.link || null,
      check_in_date: checkInDate,
      check_out_date: checkOutDate,
      overall_rating: hotel.overall_rating || null,
      rate_per_night: hotel.rate_per_night || null,
      extracted_price: hotel.extracted_price || null,
      price: hotel.price || null,
      thumbnail: hotel.thumbnail || (hotel.images && hotel.images[0] ? hotel.images[0].thumbnail : null) || null,
      gps_coordinates: hotel.gps_coordinates
        ? {
            latitude: String(hotel.gps_coordinates.latitude),
            longitude: String(hotel.gps_coordinates.longitude),
          }
        : null,
    }

    fetch(url, {
      method: "POST",
      body: JSON.stringify(requestBody),
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((err) => {
            throw new Error(err.message || `Erro ao adicionar hotel à viagem: ${response.status}`)
          })
        }
        return response.json()
      })
      .then((data) => {
        showError(`Hotel "${hotel.name}" adicionado à viagem com sucesso!`, true)
      })
      .catch((error) => {
        console.error("hotels.js: Erro na requisição fetch:", error)
        showError(`Falha ao adicionar hotel à viagem: ${error.message}`)
      })
  }
})