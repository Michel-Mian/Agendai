<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Detalhes da viagem</h2>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual seu orçamento total?</label>
        <input type="number" class="input" placeholder="R$" name="orcamento" id="orcamento" min="0">
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Qual será o meio de locomoção?<label class="text-red-600 text-base font-thin">*</label></label>
        <select class="input">
            <option>Carro (próprio)</option>
            <option>Carro (alugado)</option>
            <option>Ônibus</option>
            <option>Avião</option>
        </select>
    </div>
    <div id="cars-rent" class="hidden flex gap-6 mb-8">
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual hora deseja retirar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="datetime-local" 
                name="car_pickup_datetime" 
                id="car_pickup_datetime"
                class="input"
<<<<<<< HEAD
=======
                step="1800"
>>>>>>> d643e774296f46c453f341bc72b8ad752d734306
            >
            <label class="block text-gray-600 font-semibold mb-2">Qual hora deseja devolver?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="datetime-local" 
                name="car_return_datetime" 
<<<<<<< HEAD
                id="car_pickup_datetime"
                class="input"
=======
                id="car_return_datetime"
                class="input"
                step="1800"
>>>>>>> d643e774296f46c453f341bc72b8ad752d734306
            >
        </div>
    </div>
    <div id="dep_iata_container" class="hidden flex gap-6 mb-8">
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja decolar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="text" 
                name="dep_iata" 
                id="dep_iata"
                placeholder="ex: Guarulhos"
                class="input airport-autocomplete"
                autocomplete="off"
            >
            <div id="dep_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow"></div>
        </div>
        <div class="mb-8 relative">
            <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja pousar?<label class="text-red-600 text-base font-thin">*</label></label>
            <input 
                type="text" 
                name="arr_iata" 
                id="arr_iata"
                placeholder="ex: John F. Kennedy"
                class="input airport-autocomplete"
                autocomplete="off"
            >
            <div id="arr_iata_suggestions" class="absolute left-0 top-full w-full bg-white border border-gray-200 rounded max-h-40 overflow-y-auto shadow"></div>
        </div>
    </div>
    <div class="mb-6">
        <label class="block text-gray-600 font-semibold mb-2">Deseja contratar um seguro?<label class="text-red-600 text-base font-thin">*</label></label>
        <select class="input" id="seguroViagem" name="seguroViagem">
            <option value="Não">Não</option>
            <option value="Sim">Sim</option>
        </select>
    </div>
    <div class="hidden flex gap-6 mb-8" id="insurance-options"> 
        <div class="mb-8 relative">
            <label for="destino" class="block text-gray-600 font-semibold mb-2">Destino:<label class="text-red-600 text-base font-thin">*</label></label>
            <select name="destino" id="MainContent_Cotador_selContinente" class="input">
                <option value="">Selecione o destino</option>
                <option value="5" {{ old('destino') == '5' ? 'selected' : '' }}>África</option>
                <option value="14" {{ old('destino') == '14' ? 'selected' : '' }}>América Central</option>
                <option value="1" {{ old('destino') == '1' ? 'selected' : '' }}>América do Norte</option>
                <option value="4" {{ old('destino') == '4' ? 'selected' : '' }}>América do Sul</option>
                <option value="12" {{ old('destino') == '12' ? 'selected' : '' }}>Argentina</option>
                <option value="6" {{ old('destino') == '6' ? 'selected' : '' }}>Ásia</option>
                <option value="2" {{ old('destino') == '2' ? 'selected' : '' }}>Europa</option>
                <option value="13" {{ old('destino') == '13' ? 'selected' : '' }}>Internacional</option>
                <option value="7" {{ old('destino') == '7' ? 'selected' : '' }}>Oceania</option>
                <option value="11" {{ old('destino') == '11' ? 'selected' : '' }}>Oriente Médio</option>
            </select>
        </div>
    </div>
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>