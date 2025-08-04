@extends('index')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-white">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        <div class="w-full max-w-4xl mx-auto mt-12">
            <!-- Barra de progresso -->
            @include('trip.progressBar')
            <div class="bg-white rounded-2xl shadow-xl p-10 mb-10 animate-fade-in">
                <form id="multiStepForm" method="POST" action="{{ route('formTrip.store') }}">
                    @csrf
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

                    <!-- Passo 6: Revisão final -->
                    @include('trip.step6')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Campos de idade dinâmicos
    const idadesContainer = document.getElementById("idades-container");
    const numPessoasSelect = document.getElementById("num_pessoas");

    function renderIdadesInputs() {
        const qtd = parseInt(numPessoasSelect.value) || 1;
        idadesContainer.innerHTML = '';
        for (let i = 1; i <= qtd; i++) {
            idadesContainer.innerHTML += `
                <div class="flex-1">
                    <label class="block text-gray-600 font-semibold mb-2">Idade do passageiro ${i}<label class="text-red-600 text-base font-thin">*</label></label>
                    <input type="number" min="0" max="120" name="idades[]" class="input" id="idades" required>
                </div>
            `;
        }
    }

    if (numPessoasSelect && idadesContainer) {
        numPessoasSelect.addEventListener("change", renderIdadesInputs);
        renderIdadesInputs();
    }

    // AJAX para buscar seguros
    const btnBuscar = document.getElementById('buscar-seguros');
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            const motivo = document.getElementById('MainContent_Cotador_ddlMotivoDaViagem').value;
            const destino = document.getElementById('MainContent_Cotador_selContinente').value;
            const data_ida = document.querySelector('input[name="date_departure"]').value;
            const data_volta = document.querySelector('input[name="date_return"]').value;
            const qtd = document.getElementById('num_pessoas').value;
            let idades = [];
    document.querySelectorAll('input[name="idades[]"]').forEach(input => {
        idades.push(input.value);
    });
            const token = document.querySelector('input[name="_token"]').value;

            const resultado = document.getElementById('resultado-seguros');
            resultado.innerHTML = '<div class="text-gray-500">Buscando seguros...</div>';

            fetch('{{ route('run.Scraping.ajax') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    motivo, destino, data_ida, data_volta, qtd_passageiros: qtd, idades
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.frases && data.frases.length) {
                    let html = '<h3 class="mt-10 mb-8 text-center text-blue-700 font-extrabold text-2xl tracking-tight">Resultados dos Seguros</h3>';
                    html += '<div class="flex flex-col gap-6">';
                    data.frases.forEach(seguro => {
                        html += `
                            <div class="flex flex-col md:flex-row items-stretch bg-white/90 border border-blue-100 rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <div class="flex items-center justify-center md:w-40 bg-gradient-to-br from-blue-100 to-green-100 p-6">
                                    <span class="text-5xl text-blue-400">🛡️</span>
                                </div>
                                <div class="flex-1 flex flex-col justify-between p-6">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
                                        <span class="text-lg font-semibold text-blue-700">${seguro.site || 'Site Desconhecido'}</span>
                                        ${seguro.preco ? `<span class="text-green-600 font-bold text-lg bg-green-50 px-3 py-1 rounded-full">${seguro.preco}</span>` : ''}}
                                    </div>
                                    <div class="text-gray-700 text-sm flex flex-wrap gap-x-6 gap-y-1 mb-4">
                                        ${(seguro.dados || []).map(linha => `<span class="inline-block">${linha}</span>`).join('')}
                                    </div>
                                    ${seguro.link ? `
                                        <div class="flex justify-end">
                                            <a href="${seguro.link}" target="_blank" rel="noopener noreferrer"
                                                class="inline-block text-blue-600 font-semibold border border-blue-200 rounded-lg px-5 py-2 hover:bg-blue-50 hover:scale-105 transition">
                                                Ver detalhes &rarr;
                                            </a>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    resultado.innerHTML = html;
                } else {
                    resultado.innerHTML = '<div class="text-red-500">Nenhum seguro encontrado.</div>';
                }
            })
            .catch(() => {
                resultado.innerHTML = '<div class="text-red-500">Erro ao buscar seguros.</div>';
            });
        });
    }
});
</script>