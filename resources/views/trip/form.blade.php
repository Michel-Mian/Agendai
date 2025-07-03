@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')

    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        <main class="flex-1 p-8">
            <meta charset="utf-8" />

            <div class="container mx-auto py-5 max-w-4xl">
                <!-- Título do formulário -->
                <h2 class="mb-6 text-2xl font-semibold text-gray-800">Formulário de Raspagem</h2>

                <!-- Formulário principal -->
                <form action="{{ route('run.Scraping') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow">
                    @csrf

                    <!-- Motivo da viagem -->
                    <div>
                        <label for="motivo" class="block mb-1 font-medium text-gray-700">Motivo da Viagem:</label>
                        <select name="motivo" id="MainContent_Cotador_ddlMotivoDaViagem" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">SELECIONE O MOTIVO DA VIAGEM</option>
                            <option value="1" {{ old('motivo') == '1' ? 'selected' : '' }}>LAZER/NEGÓCIO</option>
                            <option value="2" {{ old('motivo') == '2' ? 'selected' : '' }}>MULTI-VIAGENS</option>
                            <option value="3" {{ old('motivo') == '3' ? 'selected' : '' }}>ANUAL</option>
                            <option value="4" {{ old('motivo') == '4' ? 'selected' : '' }}>ESTUDANTE</option>
                        </select>
                    </div>

                    <!-- Destino -->
                    <div>
                        <label for="destino" class="block mb-1 font-medium text-gray-700">Destino:</label>
                        <select name="destino" id="MainContent_Cotador_selContinente" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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

                    <!-- Datas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="data_ida" class="block mb-1 font-medium text-gray-700">Data de Ida:</label>
                            <input type="date" name="data_ida" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label for="data_volta" class="block mb-1 font-medium text-gray-700">Data de Volta:</label>
                            <input type="date" name="data_volta" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>

                    <!-- Quantidade de Passageiros -->
                    <div>
                        <label for="qtd_passageiros" class="block mb-1 font-medium text-gray-700">Quantidade de Passageiros:</label>
                        <select name="qtd_passageiros" id="MainContent_Cotador_selQtdCliente" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for ($i = 0; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('qtd_passageiros') == "$i" ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Idades dos Passageiros -->
                    <div id="divIdadeDoCliente">
                        <label class="block mb-2 font-medium text-gray-700">Idade dos Passageiros:</label>

                        @for ($i = 1; $i <= 8; $i++)
                            <div class="mb-3 {{ $i > 1 ? 'hidden' : '' }}" id="bloco_idade_{{ $i }}">
                                <label for="txtIdadePassageiro{{ $i }}" class="block mb-1 text-gray-600">
                                    Idade do passageiro {{ $i }}
                                </label>
                                <input type="number" min="0" max="120" id="txtIdadePassageiro{{ $i }}" name="idade{{ $i }}"
                                    placeholder="Idade" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    {{ $i == 1 ? 'required' : '' }}>
                            </div>
                        @endfor
                    </div>

                    <!-- Botão Enviar -->
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-md transition duration-300">
                        Mostrar seguros
                    </button>
                </form>

                <!-- Resultados dos seguros - cards pequenos modernos -->
@if (!empty($frases) && count($frases) > 0)
    <h3 class="mt-10 mb-6 text-center text-blue-600 font-bold text-xl">Resultados dos Seguros</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
@foreach ($frases as $seguro)
    @if (!empty($seguro['dados']) && count(array_filter($seguro['dados'])) > 0)
        <div class="bg-white rounded-xl shadow-lg border border-blue-300 p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
            <!-- Nome do site -->
            <div class="text-sm font-semibold text-blue-700 mb-3">
                {{ $seguro['site'] ?? 'Site Desconhecido' }}
            </div>

            <!-- Conteúdo filtrado -->
            <div class="text-gray-700 text-sm space-y-1 flex-grow">
                @foreach ($seguro['dados'] as $linha)
                    @php $linhaLimpando = trim($linha); @endphp
                    @if (!empty($linhaLimpando) && stripos($linhaLimpando, 'VEJA OS DETALHES DA COBERTURA') === false)
                        <p>{{ $linhaLimpando }}</p>
                    @endif
                @endforeach
            </div>

            @if (!empty($seguro['link']))
                <div class="mt-4">
                    <a href="{{ $seguro['link'] }}" target="_blank" rel="noopener noreferrer"
                        class="inline-block w-full text-center text-blue-600 border border-blue-600 rounded-md py-2 hover:bg-blue-600 hover:text-white transition">
                        Ir para o site
                    </a>
                </div>
            @endif
        </div>
    @endif
@endforeach

    </div>
@elseif(isset($frases)) {{-- Verifica se houve tentativa de consulta --}}
    <p class="mt-10 text-center text-red-500 font-semibold text-lg">Nenhum seguro encontrado.</p>
@endif
            </div>
            
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const qtdPassageirosInput = document.getElementById("MainContent_Cotador_selQtdCliente");

                    function atualizarCamposIdade() {
                        const qtd = parseInt(qtdPassageirosInput.value) || 1;

                        for (let i = 1; i <= 8; i++) {
                            const bloco = document.getElementById("bloco_idade_" + i);
                            const input = document.getElementById("txtIdadePassageiro" + i);

                            if (bloco && input) {
                                bloco.classList.toggle("hidden", i > qtd);
                                input.required = i <= qtd;
                            }
                        }
                    }

                    qtdPassageirosInput.addEventListener("change", atualizarCamposIdade);
                    atualizarCamposIdade();
                });
            </script>
        </main>
    </div>
</div>
@endsection
