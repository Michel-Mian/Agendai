<div class="flex min-h-screen bg-gray-50">


    <div class="flex-1 flex flex-col">


        <main class="flex-1 p-8">
            <meta charset="utf-8" />

            <div class="container mx-auto py-5 max-w-4xl">
                    <!-- Removido o botão "Mostrar seguros" -->
            </div>
                    
                <!-- Resultados dos seguros - cards pequenos modernos -->
                <div id="resultado-seguros" class="mt-6"></div>
        </main>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&callback=initTripFormMap" async defer></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ...existing code para campos de idade...

    // Busca seguros automaticamente ao carregar o step4
    function buscarSegurosTentativa(tentativa = 0) {
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
                // ...existing code para seleção de seguro...
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

    // Chama automaticamente ao carregar o step4
    buscarSegurosTentativa();
});
</script>
