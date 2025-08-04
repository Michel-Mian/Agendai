<div class="form-step active">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Informações iniciais</h2>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual seu destino?<label class="text-red-600 text-base font-thin">*</label></label>
        <input type="text" id="searchInput" name="searchInput" class="input" placeholder="Digite o destino dos sonhos...">
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual sua origem?<label class="text-red-600 text-base font-thin">*</label></label>
        <input type="text" id="origem" name="origem" class="input" placeholder="Digite o destino dos sonhos...">
    </div>
    <div class="flex gap-6 mb-6">
        <div class="flex-1">
            <label class="block text-gray-600 font-semibold mb-2">Nº de pessoas:<label class="text-red-600 text-base font-thin">*</label></label>
            <select class="input" name="num_pessoas" id="num_pessoas">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
            </select>
        </div>
    </div>
    <div id="idades-container" class="flex gap-4 mb-6"></div>
    <div class="flex gap-6 mb-8">
        <div class="flex-1">
            <label class="block text-gray-600 font-semibold mb-2">Data de ida:<label class="text-red-600 text-base font-thin">*</label></label>
            <input type="date" class="input" name="date_departure" id="date_departure">
        </div>
        <div class="flex-1">
            <label class="block text-gray-600 font-semibold mb-2">Data de volta:<label class="text-red-600 text-base font-thin">*</label></label>
            <input type="date" class="input" name="date_return" id="date_return">
        </div>
    </div>
    <div class="flex justify-end">
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>