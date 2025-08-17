<div id="filters" class="hidden mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm border">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                <select id="hotel-class-filter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas as categorias</option>
                    <option value="5">5 estrelas</option>
                    <option value="4">4 estrelas</option>
                    <option value="3">3 estrelas</option>
                    <option value="2">2 estrelas</option>
                    <option value="1">1 estrela</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por preço</label>
                <select id="price-order" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Padrão</option>
                    <option value="asc">Menor preço</option>
                    <option value="desc">Maior preço</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Avaliação mínima</label>
                <select id="rating-filter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas as avaliações</option>
                    <option value="4.5">4.5+ estrelas</option>
                    <option value="4.0">4.0+ estrelas</option>
                    <option value="3.5">3.5+ estrelas</option>
                    <option value="3.0">3.0+ estrelas</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="clear-filters" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
                    <i class="fa-solid fa-filter-circle-xmark mr-2"></i>
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
