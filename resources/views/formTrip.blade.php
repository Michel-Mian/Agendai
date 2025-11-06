@extends('index')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-white">
    @include('components/layout/sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components/layout/header')

        <div class="w-full max-w-4xl mx-auto mt-12">
            <!-- Barra de progresso -->
            @include('trip.progressBar')
            <div class="bg-white rounded-2xl shadow-xl p-10 mb-10 animate-fade-in">
                <form id="multiStepForm" method="POST" action="{{ route('formTrip.store') }}">
                    @csrf
                    <input type="hidden" name="seguroSelecionadoData" id="seguroSelecionadoData">
                    <input type="hidden" name="viajantesData" id="viajantesData">
                    <input type="hidden" name="segurosViajantesData" id="segurosViajantesData">
                    <!-- Passo 1 -->
                    @include('trip.step1')

                    <!-- Passo 2 -->
                    @include('trip.step2')

                    <!-- Passo 3 -->
                    @include('trip.step3')

                    <!-- Passo 4 -->
                    @include('trip.step4')

                    <!-- Passo 5 -->
                    @include('trip.step5')

                    <!-- Passo 6: Aluguel de carros -->
                    @include('trip.step6')

                    <!-- Passo 7: Revisão final -->
                    @include('trip.step7')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


<style>
    .insurance-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        background: #fff;
        min-height: 260px;
        max-height: 260px;
        height: 260px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        transition: border-color 0.2s, background 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        cursor: pointer;
        padding: 18px 16px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .insurance-card.selected {
        border: 2.5px solid #2ecc40 !important;
        background: #eafaf1 !important;
        box-shadow: 0 0 0 2px #2ecc4033 !important;
    }
    .insurance-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .insurance-card .insurance-data {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 10px;
    }
    .insurance-card .insurance-data > div {
        padding: 2px 0;
        border-bottom: 1px solid #f0f0f0;
        word-break: break-word;
    }
    .insurance-card a {
        margin-top: 8px;
        color: #2ecc40;
        font-weight: 500;
        text-decoration: underline;
    }
    .seguro-card.selected, .insurance-card.selected {
        border: 2.5px solid #2ecc40 !important;
        background: #eafaf1 !important;
        box-shadow: 0 0 0 2px #2ecc4033 !important;
    }
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Lógica para campos de idade dinâmicos
    const idadesContainer = document.getElementById("idades-container");
    const numPessoasSelect = document.getElementById("num_pessoas");

    function renderIdadesInputs() {
        if (!numPessoasSelect || !idadesContainer) return; // Checagem de segurança
        
        const qtd = parseInt(numPessoasSelect.value) || 1;
        idadesContainer.innerHTML = ''; // Limpa os campos existentes
        for (let i = 1; i <= qtd; i++) {
            idadesContainer.innerHTML += `
                <div class="flex-1">
                    <label class="block text-gray-600 font-semibold mb-2">Idade do passageiro ${i}<label class="text-red-600 text-base font-thin">*</label></label>
                    <input type="number" min="0" max="120" name="idades[]" class="input" required>
                </div>
            `;
        }
    }

    if (numPessoasSelect && idadesContainer) {
        numPessoasSelect.addEventListener("change", renderIdadesInputs);
        // Garante que os campos de idade sejam renderizados na carga inicial da página
        renderIdadesInputs(); 
    }
});
</script>

<style>
/* Garante altura fixa e quebra de linha para todos os cards de seguro */
#seguros-list .seguro-card {
    min-height: 320px !important;
    max-height: 320px !important;
    height: 320px !important;
    display: flex;
    flex-direction: row;
}
#seguros-list .seguro-card .flex-1 {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
#seguros-list .seguro-card .text-gray-700 {
    white-space: pre-line;
    word-break: break-word;
}
</style></style>
<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&callback=initTripFormMap" async defer></script>

<script>
    // Rotas e dados da viagem para o formulário (usado por resources/js/formTrip.js)
    window.APP_ROUTES = {
        searchVehicles: "{{ route('vehicles.search.ajax') }}",
        saveVehicle: "{{ route('vehicles.save') }}"
    };

    // Expor dados da viagem atual (se fornecido pelo controller).
    // Não depender da sessão aqui para evitar associar o formulário a uma viagem anterior.
    window.VIAGEM_DATA = <?php echo isset($viagem) ? json_encode($viagem, 15, 512) : 'null'; ?>;
</script>
