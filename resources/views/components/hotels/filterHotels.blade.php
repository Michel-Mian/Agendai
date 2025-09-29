<div id="filters" class="hidden mb-8">
    <div class="bg-white p-6 rounded-2xl shadow border border-blue-200 mt-2">
        <h3 class="text-xl font-bold text-blue-700 mb-6">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-base font-semibold text-blue-600 mb-2">Categoria</label>
                <select id="hotel-class-filter"
                        class="w-full px-4 py-2 border border-blue-200 rounded-xl shadow focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 text-base">
                    <option value="">Todas as categorias</option>
                    <option value="5">5 estrelas</option>
                    <option value="4">4 estrelas</option>
                    <option value="3">3 estrelas</option>
                    <option value="2">2 estrelas</option>
                    <option value="1">1 estrela</option>
                </select>
            </div>
            <div>
                <label class="block text-base font-semibold text-blue-600 mb-2">Ordenar por preço</label>
                <select id="price-order"
                        class="w-full px-4 py-2 border border-blue-200 rounded-xl shadow focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 text-base">
                    <option value="">Padrão</option>
                    <option value="asc">Menor preço</option>
                    <option value="desc">Maior preço</option>
                </select>
            </div>
            <div>
                <label class="block text-base font-semibold text-blue-600 mb-2">Avaliação mínima</label>
                <select id="rating-filter"
                        class="w-full px-4 py-2 border border-blue-200 rounded-xl shadow focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 text-base">
                    <option value="">Todas as avaliações</option>
                    <option value="4.5">4.5+ estrelas</option>
                    <option value="4.0">4.0+ estrelas</option>
                    <option value="3.5">3.5+ estrelas</option>
                    <option value="3.0">3.0+ estrelas</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="clear-filters"
                        class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-xl transition duration-200 flex items-center justify-center shadow">
                    <i class="fa-solid fa-filter-circle-xmark mr-2"></i>
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
