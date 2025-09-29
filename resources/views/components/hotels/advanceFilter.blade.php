<div class="mt-4 md:mt-0">
    <button id="open-filters-btn" type="button" 
            onclick="openFiltersModal()" 
            class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-5 py-4 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-base">
        <i class="fa-solid fa-filter"></i>
    </button>
</div>

<div id="filters-modal" class="fixed inset-0 bg-opacity-40 backdrop-blur-md overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden">
    <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
        <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Filtros Avançados</h3>
            <button type="button" onclick="closeFiltersModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form id="filters-form" onsubmit="submitFilters(event)">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fa-solid fa-dollar-sign text-gray-600 mr-2"></i>
                        Faixa de Preço
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="advanced-min-price" class="block text-sm font-medium text-gray-700 mb-2">
                                Preço Mínimo (R$)
                            </label>
                            <input type="number" 
                                id="advanced-min-price" 
                                name="min_price"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                placeholder="Ex: 100" 
                                min="0">
                        </div>
                        <div>
                            <label for="advanced-max-price" class="block text-sm font-medium text-gray-700 mb-2">
                                Preço Máximo (R$)
                            </label>
                            <input type="number" 
                                id="advanced-max-price" 
                                name="max_price"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                placeholder="Ex: 500" 
                                min="0">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fa-solid fa-building text-gray-600 mr-2"></i>
                        Tipos de Propriedade
                    </h4>
                    <div class="relative">
                        <div id="property-types-selector" 
                             onclick="toggleDropdown('property-types-dropdown')" 
                             class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm cursor-pointer bg-white transition-all duration-200 hover:border-blue-500">
                            <div class="flex justify-between items-center">
                                <span id="property-types-display" class="text-gray-500">Selecione os tipos de propriedade</span>
                                <i class="fa-solid fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <div id="property-types-dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                            <div class="p-2">
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="12" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Beach hotels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="13" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Boutique hotels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="14" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Hostels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="15" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Inns</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="16" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Motels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="17" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Resorts</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="18" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Spa hotels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="19" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Bed and breakfasts</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="20" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Other</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="21" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Apartment hotels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="22" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Minshuku</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="23" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Japanese-style business hotels</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" name="property_types[]" value="24" class="property-type-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updatePropertyTypesDisplay()">
                                    <span class="text-sm text-gray-800">Ryokan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fa-solid fa-star text-gray-600 mr-2"></i>
                    Amenidades
                </h4>
                <div class="relative">
                    <div id="amenities-selector" 
                         onclick="toggleDropdown('amenities-dropdown')" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm cursor-pointer bg-white transition-all duration-200 hover:border-blue-500">
                        <div class="flex justify-between items-center">
                            <span id="amenities-display" class="text-gray-500">Selecione as amenidades desejadas</span>
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <div id="amenities-dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                        <div class="p-2 grid grid-cols-1 md:grid-cols-2 gap-1">
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="1" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Free parking</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="3" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Parking</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="4" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Indoor pool</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="5" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Outdoor pool</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="6" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Pool</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="7" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Fitness center</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="8" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Restaurant</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="9" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Free breakfast</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="10" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Spa</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="11" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Beach access</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="12" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Child-friendly</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="15" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Bar</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="19" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Pet-friendly</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="22" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Room service</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="35" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Free Wi-Fi</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="40" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Air-conditioned</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="52" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">All-inclusive available</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="53" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">Wheelchair accessible</span>
                            </label>
                            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="61" class="amenity-checkbox mr-3 h-4 w-4 text-blue-600 rounded" onchange="updateAmenitiesDisplay()">
                                <span class="text-sm text-gray-800">EV charger</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <button type="button" onclick="clearAdvancedFilters()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition duration-200">
                    <i class="fa-solid fa-broom mr-2"></i>
                    Limpar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function openFiltersModal() {
    const modal = document.getElementById('filters-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('❌ Modal não encontrado!');
    }
}

function closeFiltersModal() {
    const modal = document.getElementById('filters-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
        // Fechar outros dropdowns
        const allDropdowns = ['property-types-dropdown', 'amenities-dropdown'];
        allDropdowns.forEach(id => {
            if (id !== dropdownId) {
                const other = document.getElementById(id);
                if (other) other.classList.add('hidden');
            }
        });
    }
}

function updatePropertyTypesDisplay() {
    const checkboxes = document.querySelectorAll('.property-type-checkbox');
    const display = document.getElementById('property-types-display');
    if (display && checkboxes) {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.querySelector('span').textContent);
        
        if (selected.length === 0) {
            display.textContent = 'Selecione os tipos de propriedade';
            display.className = 'text-gray-500';
        } else {
            display.textContent = selected.length === 1 
                ? selected[0] 
                : `${selected.length} tipos selecionados`;
            display.className = 'text-gray-900';
        }
    }
}

function updateAmenitiesDisplay() {
    const checkboxes = document.querySelectorAll('.amenity-checkbox');
    const display = document.getElementById('amenities-display');
    if (display && checkboxes) {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.querySelector('span').textContent);
        
        if (selected.length === 0) {
            display.textContent = 'Selecione as amenidades desejadas';
            display.className = 'text-gray-500';
        } else {
            display.textContent = selected.length === 1 
                ? selected[0] 
                : `${selected.length} amenidades selecionadas`;
            display.className = 'text-gray-900';
        }
    }
}

function clearAdvancedFilters() {
    const minPriceInput = document.getElementById('advanced-min-price');
    const maxPriceInput = document.getElementById('advanced-max-price');
    const propertyTypeCheckboxes = document.querySelectorAll('.property-type-checkbox');
    const amenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
    
    if (minPriceInput) minPriceInput.value = '';
    if (maxPriceInput) maxPriceInput.value = '';
    
    propertyTypeCheckboxes.forEach(cb => cb.checked = false);
    amenityCheckboxes.forEach(cb => cb.checked = false);
    
    updatePropertyTypesDisplay();
    updateAmenitiesDisplay();
}

function submitFilters(event) {
    event.preventDefault();
    
    const form = document.getElementById('filters-form');
    const formData = new FormData(form);
    
    const selectedFilters = {
        minPrice: formData.get('min_price') || '',
        maxPrice: formData.get('max_price') || '',
        propertyTypes: formData.getAll('property_types[]'),
        amenities: formData.getAll('amenities[]')
    };
    
    
    // Disparar evento para hotels.js
    const event2 = new CustomEvent('advancedFiltersApplied', {
        detail: selectedFilters
    });
    document.dispatchEvent(event2);
    
    closeFiltersModal();
}

// Fechar dropdowns ao clicar fora
document.addEventListener('click', function(event) {
    const propertyDropdown = document.getElementById('property-types-dropdown');
    const amenitiesDropdown = document.getElementById('amenities-dropdown');
    const propertySelector = document.getElementById('property-types-selector');
    const amenitiesSelector = document.getElementById('amenities-selector');
    
    if (propertyDropdown && !propertySelector.contains(event.target) && !propertyDropdown.contains(event.target)) {
        propertyDropdown.classList.add('hidden');
    }
    
    if (amenitiesDropdown && !amenitiesSelector.contains(event.target) && !amenitiesDropdown.contains(event.target)) {
        amenitiesDropdown.classList.add('hidden');
    }
});

// Fechar modal clicando no backdrop
document.addEventListener('click', function(event) {
    const modal = document.getElementById('filters-modal');
    if (event.target === modal) {
        closeFiltersModal();
    }
});
</script>