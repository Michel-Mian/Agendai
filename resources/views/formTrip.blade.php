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
{{-- Removido o bloco de cards de seguros estáticos abaixo --}}
{{-- 
<div id="insuranceCards" class="row">
    @foreach ($insurances as $insurance)
        <div class="col-md-4 mb-3">
            <div class="insurance-card" data-id="{{ $insurance['id'] }}">
                <h5>{{ $insurance['company'] }} - {{ $insurance['plan'] }}</h5>
                <div class="insurance-data">
                    <div><strong>Despesa médica hospitalar:</strong><br>{{ $insurance['medical_coverage'] }}</div>
                    <div><strong>Seguro bagagem:</strong><br>{{ $insurance['baggage_coverage'] }}</div>
                    <div><strong>Preço PIX:</strong><br>{{ $insurance['price_pix'] }}</div>
                    <div><strong>Preço Cartão:</strong><br>{{ $insurance['price_card'] }}</div>
                </div>
                <a href="{{ $insurance['link'] }}" target="_blank">Ver detalhes</a>
            </div>
        </div>
    @endforeach
</div>
--}}
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

            function buscarSegurosTentativa(tentativa = 0) {
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
                        html += '<div id="seguros-list" class="flex flex-col gap-6">';
                        data.frases.forEach((seguro, idx) => {
                            html += `
                                <div class="seguro-card flex flex-col md:flex-row items-stretch bg-white/90 border border-blue-100 rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden cursor-pointer" data-idx="${idx}" data-seguro='${JSON.stringify(seguro)}'>
                                    <div class="flex items-center justify-center md:w-40 bg-gradient-to-br from-blue-100 to-green-100 p-6">
                                        <span class="text-5xl text-blue-400">🛡️</span>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between p-6">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
                                            <span class="text-lg font-semibold text-blue-700">${seguro.site || 'Site Desconhecido'}</span>
                                            ${seguro.preco ? `<span class="text-green-600 font-bold text-lg bg-green-50 px-3 py-1 rounded-full">${seguro.preco}</span>` : ''}
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

                        // Seleção de seguro
                        // Do NOT preselect any insurance
                        sessionStorage.removeItem('selectedSeguroIdx');
                        sessionStorage.removeItem('selectedSeguroName');
                        const segurosCards = document.querySelectorAll('.seguro-card');
                        segurosCards.forEach(card => {
                            card.classList.remove('border-green-500', 'border-blue-600', 'ring-2', 'ring-purple-200', 'shadow-md');
                            card.addEventListener('click', function() {
                                segurosCards.forEach(c => c.classList.remove('border-green-500', 'border-blue-600', 'ring-2', 'ring-blue-200', 'shadow-md'));
                                card.classList.add('border-blue-600', 'ring-2', 'ring-blue-200', 'shadow-md');
                                sessionStorage.setItem('selectedSeguroIdx', card.getAttribute('data-idx'));
                                // Salva nome completo do seguro selecionado (site + nome do seguro)
                                const seguroData = JSON.parse(card.getAttribute('data-seguro'));
                                let fullName = seguroData.site || '';
                                if (seguroData.dados && seguroData.dados.length > 0) {
                                    fullName += ' ' + seguroData.dados[0];
                                }
                                sessionStorage.setItem('selectedSeguroName', fullName.trim());
                                // Salva no banco via AJAX, incluindo nome completo
                                seguroData.site = fullName.trim();
                                fetch("/trip/salvar-seguro", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": token
                                    },
                                    body: JSON.stringify(seguroData)
                                });
                            });
                        });
                    } else if (data.status === 'carregando' && tentativa < 10) {
                        resultado.innerHTML = '<div class="text-gray-500">Carregando seguros, aguarde...</div>';
                        setTimeout(() => buscarSegurosTentativa(tentativa + 1), 2000);
                    } else {
                        resultado.innerHTML = '<div class="text-red-500">Nenhum seguro encontrado.</div>';
                    }
                })
                .catch(() => {
                    resultado.innerHTML = '<div class="text-red-500">Erro ao buscar seguros.</div>';
                });
            }

            buscarSegurosTentativa();
        });
    }

    // Seleção de seguro para cards dinâmicos (.seguro-card)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.seguro-card')) {
            document.querySelectorAll('.seguro-card').forEach(c => c.classList.remove('selected'));
            const card = e.target.closest('.seguro-card');
            card.classList.add('selected');
            // Salva nome completo do seguro selecionado (site + nome do seguro)
            const seguroData = JSON.parse(card.getAttribute('data-seguro'));
            let fullName = seguroData.site || '';
            if (seguroData.dados && seguroData.dados.length > 0) {
                fullName += ' ' + seguroData.dados[0];
            }
            sessionStorage.setItem('selectedSeguroName', fullName.trim());
        }
        // Seleção para cards estáticos
        if (e.target.closest('.insurance-card')) {
            document.querySelectorAll('.insurance-card').forEach(c => c.classList.remove('selected'));
            e.target.closest('.insurance-card').classList.add('selected');
        }
    });
});
</script>