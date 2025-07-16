@extends('index')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-white">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        <div class="w-full max-w-4xl mx-auto mt-12">
            <!-- Barra de progresso -->
            <div class="flex justify-between items-center mb-12 px-4">
                @foreach(['Informações iniciais', 'Detalhes da viagem', 'Preferências', 'Seguros', 'Voos', 'Revisão final'] as $i => $etapa)
                    <div class="flex-1 flex items-center relative">
                        <div class="step-indicator @if($i==0) active @endif" id="step-indicator-{{ $i+1 }}">{{ $i+1 }}</div>
                        <span class="step-label">{{ $etapa }}</span>
                        @if($i < 5)
                            <div class="step-line"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-10 mb-10 animate-fade-in">
                <form id="multiStepForm" method="POST" action="{{ route('formTrip.store') }}">
                    @csrf
                    <!-- Passo 1 -->
                    <div class="form-step active">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Informações iniciais</h2>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual seu destino?</label>
                            <input type="text" id="searchInput" name="searchInput" class="input" placeholder="Digite o destino dos sonhos...">
                        </div>
                        <div class="flex gap-6 mb-6">
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Nº de pessoas:</label>
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
                                <label class="block text-gray-600 font-semibold mb-2">Data de ida:</label>
                                <input type="date" class="input" name="date_departure" id="date_departure">
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Data de volta:</label>
                                <input type="date" class="input" name="date_return" id="date_return">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 2 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Detalhes da viagem</h2>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual seu orçamento total?</label>
                            <input type="number" class="input" placeholder="R$" name="orcamento">
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual será o meio de locomoção?</label>
                            <select class="input">
                                <option>Carro</option>
                                <option>Ônibus</option>
                                <option>Avião</option>
                            </select>
                        </div>
                        <div id="dep_iata_container" class="hidden flex gap-6 mb-8">
                            <div class="mb-8 relative">
                                <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja decolar?</label>
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
                                <label class="block text-gray-600 font-semibold mb-2">Qual cidade/aeroporto deseja pousar?</label>
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
                            <label class="block text-gray-600 font-semibold mb-2">Deseja contratar um seguro?</label>
                            <select class="input" id="seguroViagem" name="seguroViagem">
                                <option value="Não">Não</option>
                                <option value="Sim">Sim</option>
                            </select>
                        </div>
                        <div class="hidden flex gap-6 mb-8" id="insurance-options"> 
                            <div class="mb-8 relative">
                                <label for="motivo" class="block text-gray-600 font-semibold mb-2">Motivo da Viagem:</label>
                                <select name="motivo" id="MainContent_Cotador_ddlMotivoDaViagem" 
                                    class="input">
                                    <option value="">SELECIONE O MOTIVO DA VIAGEM</option>
                                    <option value="1" {{ old('motivo') == '1' ? 'selected' : '' }}>LAZER/NEGÓCIO</option>
                                    <option value="2" {{ old('motivo') == '2' ? 'selected' : '' }}>MULTI-VIAGENS</option>
                                    <option value="3" {{ old('motivo') == '3' ? 'selected' : '' }}>ANUAL</option>
                                    <option value="4" {{ old('motivo') == '4' ? 'selected' : '' }}>ESTUDANTE</option>
                                </select>
                            </div>
                            <div class="mb-8 relative">
                                <label for="destino" class="block text-gray-600 font-semibold mb-2">Destino:</label>
                                <select name="destino" id="MainContent_Cotador_selContinente"
                                    class="input">
                                    <option value="">Selecione o destino</option>
                                    <option value="5" {{ old('destino') == '5' ? 'selected' : '' }}>África</option>
                                    <option value="1" {{ old('destino') == '1' ? 'selected' : '' }}>América do Norte</option>
                                    <option value="4" {{ old('destino') == '4' ? 'selected' : '' }}>América do Sul</option>
                                    <option value="6" {{ old('destino') == '6' ? 'selected' : '' }}>Ásia</option>
                                    <option value="3" {{ old('destino') == '3' ? 'selected' : '' }}>Caribe / México</option>
                                    <option value="2" {{ old('destino') == '2' ? 'selected' : '' }}>Europa</option>
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

                    <!-- Passo 3 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Preferências</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                            @foreach(['Cultura e história' => '🏛️', 'Gastronomia' => '🍽️', 'Natureza' => '🌳', 'Aventura' => '⛰️', 'Praia' => '🏖️', 'Vida noturna' => '🌃', 'Compras' => '🛍️', 'Arte e museus' => '🖼️'] as $pref => $icon)
                                <button type="button" class="pref-btn flex flex-col items-center gap-2">
                                    <span class="text-2xl">{{ $icon }}</span>
                                    <span class="text-gray-700 font-medium">{{ $pref }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 4 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Seguros</h2>
                        <div class="mb-8">
                            @include('trip.form') 
                        </div>
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 5 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Voos</h2>
                        <p class="mb-4 text-gray-600">Escolha sua passagem aérea</p>
                        <div class="space-y-4" id="flights-container">
                        </div>
                        <input type="hidden" name="selected_flight_index" id="selected_flight_index" value="">
                        <input type="hidden" name="selected_flight_data" id="selected_flight_data">
                        <div class="flex justify-between mt-8">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 6: Revisão final -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Revisão final</h2>
                        <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl p-6 text-white mb-6">
                            <h3 class="text-xl font-bold mb-4">Confira seus dados:</h3>
                            <ul class="space-y-2 text-base" id="reviewList">
                                <!-- Os dados preenchidos aparecerão aqui via JS -->
                            </ul>
                        </div>
                        
            @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="submit" class="btn-primary">Finalizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
body { font-family: 'Inter', sans-serif; }
.animate-fade-in { animation: fadeIn 0.7s; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1, transform: none; } }

.form-step { display: none; }
.form-step.active { display: block; animation: fadeIn 0.5s; }
.step-indicator {
    width: 38px; height: 38px; border-radius: 50%; background: #fff;
    border: 3px solid #22c55e; color: #22c55e;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.2rem; box-shadow: 0 2px 8px #22c55e22;
    transition: all 0.3s;
    z-index: 1;
}
.step-indicator:not(.active) {
    border-color: #d1d5db; color: #d1d5db; background: #f3f4f6;
    box-shadow: none;
}
.step-indicator.active {
    background: linear-gradient(135deg, #22c55e 60%, #0ea5e9 100%);
    color: #fff;
    border-color: #0ea5e9;
    box-shadow: 0 4px 16px #0ea5e955;
}
.step-label {
    margin-left: 10px; margin-right: 10px;
    font-weight: 600; color: #22c55e;
    font-size: 1rem;
}
.step-line {
    flex: 1; height: 3px; background: linear-gradient(90deg, #22c55e, #0ea5e9);
    margin-left: 10px; margin-right: 10px; border-radius: 2px;
}
.input {
    border: 1.5px solid #d1d5db; border-radius: 8px; padding: 10px 14px; width: 100%;
    font-size: 1rem; background: #f9fafb; transition: border 0.2s, box-shadow 0.2s;
    outline: none;
}
.input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 2px #0ea5e955;
    background: #fff;
}
.btn-primary {
    background: linear-gradient(90deg, #2563eb, #2563eb, #3b82f6, #2563eb);
    background-image: linear-gradient(to right, #2563eb, #3b82f6);
    color: #fff; padding: 10px 32px; border-radius: 8px;
    font-weight: 700; font-size: 1rem; border: none;
    box-shadow: 0 2px 8px #2563eb33;
    transition: background 0.2s, transform 0.2s;
    cursor: pointer;
}
.btn-primary:hover {
    background-image: linear-gradient(to right, #3b82f6, #2563eb);
    transform: translateY(-2px) scale(1.03);
}
.btn-secondary {
    background: #fff; border: 1.5px solid #d1d5db; color: #222;
    padding: 10px 32px; border-radius: 8px; font-weight: 600;
    transition: border 0.2s, background 0.2s, color 0.2s;
    cursor: pointer;
}
.btn-secondary:hover {
    border-color: #0ea5e9; color: #0ea5e9; background: #f0f9ff;
}
.pref-btn, .insurance-btn {
    border: 2px solid #e5e7eb; border-radius: 16px; padding: 22px 0;
    background: #f9fafb; cursor: pointer; font-size: 1rem;
    font-weight: 500; box-shadow: 0 2px 8px #0001;
    transition: border 0.2s, background 0.2s, box-shadow 0.2s, transform 0.2s;
    outline: none;
    min-width: 120px;
}
.pref-btn.selected, .insurance-btn.selected {
    border-color: #22c55e; background: #e6f9f0;
    box-shadow: 0 4px 16px #22c55e33;
    transform: scale(1.04);
}
.pref-btn:hover, .insurance-btn:hover {
    border-color: #0ea5e9; background: #e0f2fe;
    box-shadow: 0 4px 16px #0ea5e933;
    transform: scale(1.03);
}
</style>

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
                    <label class="block text-gray-600 font-semibold mb-2">Idade do passageiro ${i}</label>
                    <input type="number" min="0" max="120" name="idades[]" class="input" required>
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