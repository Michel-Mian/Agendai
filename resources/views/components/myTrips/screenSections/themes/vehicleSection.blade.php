<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
    {{-- CABEÇALHO --}}
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-car text-gray-400 text-2xl"></i>
                <h2 class="text-lg font-semibold text-gray-800">Veículos Alugados</h2>
            </div>
            <a href="{{ route('vehicles.index') }}" class="ml-auto bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Buscar Veículos
            </a>
        </div>
    </div>
    
    <div class="p-6">
        @php
            $veiculosSelecionados = $veiculos ?? collect();
        @endphp

        @if($veiculosSelecionados->count() > 0)
            {{-- VEÍCULOS SELECIONADOS --}}
            <div id="selected-vehicles-session" class="space-y-4">
                @foreach($veiculosSelecionados as $veiculo)
                    <div class="group border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300 ease-in-out relative" data-vehicle-id="{{ $veiculo->pk_id_veiculo }}">
                        <button onclick="deleteVehicle({{ $veiculo->pk_id_veiculo }})" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                        <div class="flex gap-4">
                            {{-- Imagem do Veículo --}}
                            @if($veiculo->imagem_url)
                                <div class="flex-shrink-0">
                                    <img src="{{ $veiculo->imagem_url }}" alt="{{ $veiculo->nome_veiculo }}" class="w-32 h-24 object-cover rounded-lg">
                                </div>
                            @endif

                            {{-- Informações Principais --}}
                            <div class="flex-grow space-y-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-gray-800">{{ $veiculo->nome_veiculo }}</h3>
                                        @if($veiculo->categoria)
                                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $veiculo->categoria }}</span>
                                        @endif
                                    </div>
                                    @if($veiculo->locadora_nome)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-building text-gray-400 mr-1"></i>
                                            {{ $veiculo->locadora_nome }}
                                            @if($veiculo->avaliacao_locadora)
                                                <span class="ml-2 text-yellow-500">
                                                    <i class="fas fa-star"></i> {{ number_format($veiculo->avaliacao_locadora, 1) }}
                                                </span>
                                            @endif
                                        </p>
                                    @endif
                                </div>

                                {{-- Especificações do Veículo --}}
                                <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                                    @if($veiculo->passageiros)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-user w-4 text-center text-gray-400"></i>
                                            <span>{{ $veiculo->passageiros }} passageiros</span>
                                        </div>
                                    @endif
                                    @if($veiculo->malas)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-suitcase w-4 text-center text-gray-400"></i>
                                            <span>{{ $veiculo->malas }}</span>
                                        </div>
                                    @endif
                                    @if($veiculo->ar_condicionado)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-snowflake w-4 text-center text-blue-400"></i>
                                            <span>Ar Condicionado</span>
                                        </div>
                                    @endif
                                    @if($veiculo->cambio)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-cog w-4 text-center text-gray-400"></i>
                                            <span>{{ $veiculo->cambio }}</span>
                                        </div>
                                    @endif
                                    @if($veiculo->quilometragem)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-road w-4 text-center text-gray-400"></i>
                                            <span>{{ $veiculo->quilometragem }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Diferenciais --}}
                                @if($veiculo->diferenciais)
                                    @php
                                        $diferenciais = is_string($veiculo->diferenciais) ? json_decode($veiculo->diferenciais, true) : $veiculo->diferenciais;
                                    @endphp
                                    @if(is_array($diferenciais) && count($diferenciais) > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($diferenciais as $diferencial)
                                                <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-full">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ $diferencial }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                {{-- Tags --}}
                                @if($veiculo->tags)
                                    @php
                                        $tags = is_string($veiculo->tags) ? json_decode($veiculo->tags, true) : $veiculo->tags;
                                    @endphp
                                    @if(is_array($tags) && count($tags) > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($tags as $tag)
                                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                {{-- Local de Retirada --}}
                                @if($veiculo->endereco_retirada)
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                        {{ $veiculo->endereco_retirada }}
                                        @if($veiculo->nome_local)
                                            <span class="text-gray-500">- {{ $veiculo->nome_local }}</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Preço --}}
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($veiculo->preco_total)
                                            <p class="text-lg font-bold text-gray-900">
                                                R$ {{ number_format($veiculo->preco_total, 2, ',', '.') }}
                                                <span class="text-sm font-normal text-gray-500">total</span>
                                            </p>
                                        @endif
                                        @if($veiculo->preco_diaria)
                                            <p class="text-sm text-gray-600">
                                                R$ {{ number_format($veiculo->preco_diaria, 2, ',', '.') }}/dia
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Link de Reserva --}}
                                    @if($veiculo->link_reserva)
                                        <a href="{{ $veiculo->link_reserva }}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-500 hover:underline">
                                            Ver detalhes →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- ESTADO VAZIO --}}
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-car text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhum carro alugado selecionado</h3>
                <p class="text-gray-500 mb-6 max-w-xs mx-auto">Pesquise e adicione um veículo para facilitar seu deslocamento durante a viagem.</p>
                <a href="{{ route('vehicles.index') }}" class="inline-block bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg transition-colors font-semibold">
                    <i class="fas fa-search mr-2"></i>
                    Buscar Veículos
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('vehicleChanged', function(e) {
            const veiculo = e.detail && e.detail.veiculo;
            if (!veiculo) return;
            
            const vehicleHtml = buildVehicleCard(veiculo);
            const vehicleId = veiculo.pk_id_veiculo || veiculo.id;
            addOrUpdateVehicle(vehicleId, vehicleHtml);
        });

        function buildVehicleCard(veiculo) {
            const precoTotalFmt = formatPrice(veiculo.preco_total);
            const precoDiariaFmt = formatPrice(veiculo.preco_diaria);
            const diferenciais = parseJsonField(veiculo.diferenciais);
            const tags = parseJsonField(veiculo.tags);
            const vehicleId = veiculo.pk_id_veiculo || veiculo.id;

            return `
                <div class="group border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300 ease-in-out relative" data-vehicle-id="${vehicleId}">
                    <button onclick="deleteVehicle(${vehicleId})" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                    <div class="flex gap-4">
                        ${veiculo.imagem_url ? `
                            <div class="flex-shrink-0">
                                <img src="${veiculo.imagem_url}" alt="${veiculo.nome_veiculo}" class="w-32 h-24 object-cover rounded-lg">
                            </div>
                        ` : ''}
                        <div class="flex-grow space-y-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-800">${veiculo.nome_veiculo}</h3>
                                    ${veiculo.categoria ? `<span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">${veiculo.categoria}</span>` : ''}
                                </div>
                                ${veiculo.locadora_nome ? `
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-building text-gray-400 mr-1"></i>
                                        ${veiculo.locadora_nome}
                                        ${veiculo.avaliacao_locadora ? `<span class="ml-2 text-yellow-500"><i class="fas fa-star"></i> ${parseFloat(veiculo.avaliacao_locadora).toFixed(1)}</span>` : ''}
                                    </p>
                                ` : ''}
                            </div>
                            <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                                ${veiculo.passageiros ? `<div class="flex items-center gap-1.5"><i class="fas fa-user w-4 text-center text-gray-400"></i><span>${veiculo.passageiros} passageiros</span></div>` : ''}
                                ${veiculo.malas ? `<div class="flex items-center gap-1.5"><i class="fas fa-suitcase w-4 text-center text-gray-400"></i><span>${veiculo.malas}</span></div>` : ''}
                                ${veiculo.ar_condicionado ? `<div class="flex items-center gap-1.5"><i class="fas fa-snowflake w-4 text-center text-blue-400"></i><span>Ar Condicionado</span></div>` : ''}
                                ${veiculo.cambio ? `<div class="flex items-center gap-1.5"><i class="fas fa-cog w-4 text-center text-gray-400"></i><span>${veiculo.cambio}</span></div>` : ''}
                                ${veiculo.quilometragem ? `<div class="flex items-center gap-1.5"><i class="fas fa-road w-4 text-center text-gray-400"></i><span>${veiculo.quilometragem}</span></div>` : ''}
                            </div>
                            ${diferenciais.length > 0 ? `
                                <div class="flex flex-wrap gap-2">
                                    ${diferenciais.map(d => `<span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-full"><i class="fas fa-check-circle mr-1"></i>${d}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${tags.length > 0 ? `
                                <div class="flex flex-wrap gap-2">
                                    ${tags.map(t => `<span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">${t}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${veiculo.endereco_retirada ? `
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    ${veiculo.endereco_retirada}
                                    ${veiculo.nome_local ? `<span class="text-gray-500">- ${veiculo.nome_local}</span>` : ''}
                                </div>
                            ` : ''}
                            <div class="flex items-center justify-between">
                                <div>
                                    ${precoTotalFmt ? `<p class="text-lg font-bold text-gray-900">R$ ${precoTotalFmt} <span class="text-sm font-normal text-gray-500">total</span></p>` : ''}
                                    ${precoDiariaFmt ? `<p class="text-sm text-gray-600">R$ ${precoDiariaFmt}/dia</p>` : ''}
                                </div>
                                ${veiculo.link_reserva ? `<a href="${veiculo.link_reserva}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-500 hover:underline">Ver detalhes →</a>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function addOrUpdateVehicle(vehicleId, html) {
            const container = document.getElementById('selected-vehicles-session');
            
            if (container) {
                const existingVehicle = container.querySelector(`[data-vehicle-id="${vehicleId}"]`);
                
                if (existingVehicle) {
                    existingVehicle.outerHTML = html;
                } else {
                    container.insertAdjacentHTML('beforeend', html);
                }
            } else {
                replaceEmptyState(html);
            }
        }

        function replaceEmptyState(html) {
            const parent = document.querySelector('.p-6');
            const emptyState = parent?.querySelector('.text-center.py-12');
            
            if (emptyState) {
                emptyState.outerHTML = `<div id="selected-vehicles-session" class="space-y-4">${html}</div>`;
            }
        }

        function formatPrice(price) {
            return price 
                ? parseFloat(price).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '';
        }

        function parseJsonField(field) {
            if (Array.isArray(field)) return field;
            if (typeof field === 'string') {
                try {
                    return JSON.parse(field);
                } catch {
                    return [];
                }
            }
            return [];
        }

        window.deleteVehicle = function(vehicleId) {
            if (!confirm('Tem certeza que deseja remover este veículo da viagem?')) {
                return;
            }

            fetch(`/vehicles/${vehicleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const vehicleCard = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
                    if (vehicleCard) {
                        vehicleCard.remove();
                    }

                    const container = document.getElementById('selected-vehicles-session');
                    if (container && container.children.length === 0) {
                        showEmptyState();
                    }
                } else {
                    alert(data.message || 'Erro ao remover veículo');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao remover veículo');
            });
        }

        function showEmptyState() {
            const parent = document.querySelector('.p-6');
            if (parent) {
                const emptyStateHtml = `
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhum carro alugado selecionado</h3>
                        <p class="text-gray-500 mb-6 max-w-xs mx-auto">Pesquise e adicione um veículo para facilitar seu deslocamento durante a viagem.</p>
                        <a href="{{ route('vehicles.index') }}" class="inline-block bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg transition-colors font-semibold">
                            <i class="fas fa-search mr-2"></i>
                            Buscar Veículos
                        </a>
                    </div>
                `;
                parent.innerHTML = emptyStateHtml;
            }
        }
    });
</script>
