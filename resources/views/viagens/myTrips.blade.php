@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components/layout/header')
    <div class="max-w-5xl mx-auto w-full py-14 px-5">
            <!-- Filtros (estilo alinhado ao restante do site) -->
            <form method="GET" action="{{ route('myTrips') }}" class="mb-8">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-4 md:px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                        <h2 class="text-base md:text-lg font-semibold text-gray-800">Filtrar viagens</h2>
                        <div class="hidden md:flex items-center gap-4 text-sm text-gray-500">
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-calendar text-indigo-500"></i> Próximas</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-clock text-amber-500"></i> Em andamento</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-circle-check text-emerald-500"></i> Concluídas</span>
                        </div>
                    </div>

                    <div class="p-4 md:p-6 grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-7">
                        <!-- Busca por nome -->
                        <div class="md:col-span-5">
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da viagem</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="nome" id="nome" value="{{ $filtros['nome'] ?? '' }}"
                                       class="w-full h-10 pl-10 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Ex.: Europa 2026" />
                            </div>
                        </div>

                        <!-- Status (segmented) -->
                        <div class="md:col-span-4">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
                            @php($statusSel = $filtros['status'] ?? '')
                                <div class="flex gap-2">
                                    <label class="flex-1 group">
                                        <input type="radio" class="peer hidden" name="status" value="" {{ $statusSel==='' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center h-10 w-full rounded-md border text-xs md:text-sm font-medium transition cursor-pointer
                                            bg-white border-gray-300 text-gray-700
                                            group-hover:bg-indigo-50 group-hover:border-indigo-300 group-hover:text-indigo-700 group-hover:shadow-sm
                                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-sm
                                            peer-checked:group-hover:bg-indigo-700 peer-checked:group-hover:border-indigo-700 peer-checked:group-hover:shadow-md
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">Todas</span>
                                    </label>
                                    <label class="flex-1 group">
                                        <input type="radio" class="peer hidden" name="status" value="planejada" {{ $statusSel==='planejada' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center h-10 w-full rounded-md border text-xs md:text-sm font-medium transition cursor-pointer
                                            bg-white border-gray-300 text-gray-700
                                            group-hover:bg-indigo-50 group-hover:border-indigo-300 group-hover:text-indigo-700 group-hover:shadow-sm
                                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-sm
                                            peer-checked:group-hover:bg-indigo-700 peer-checked:group-hover:border-indigo-700 peer-checked:group-hover:shadow-md
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">Planejada</span>
                                    </label>
                                    <label class="flex-1 group">
                                        <input type="radio" class="peer hidden" name="status" value="andamento" {{ $statusSel==='andamento' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center h-10 w-full rounded-md border text-xs md:text-sm font-medium transition cursor-pointer
                                            bg-white border-gray-300 text-gray-700
                                            group-hover:bg-indigo-50 group-hover:border-indigo-300 group-hover:text-indigo-700 group-hover:shadow-sm
                                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-sm
                                            peer-checked:group-hover:bg-indigo-700 peer-checked:group-hover:border-indigo-700 peer-checked:group-hover:shadow-md
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">Andamento</span>
                                    </label>
                                    <label class="flex-1 group">
                                        <input type="radio" class="peer hidden" name="status" value="concluida" {{ $statusSel==='concluida' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center h-10 w-full rounded-md border text-xs md:text-sm font-medium transition cursor-pointer
                                            bg-white border-gray-300 text-gray-700
                                            group-hover:bg-indigo-50 group-hover:border-indigo-300 group-hover:text-indigo-700 group-hover:shadow-sm
                                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-sm
                                            peer-checked:group-hover:bg-indigo-700 peer-checked:group-hover:border-indigo-700 peer-checked:group-hover:shadow-md
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">Concluída</span>
                                    </label>
                                </div>
                        </div>

                        <!-- Ordenação -->
                        <div class="md:col-span-3">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fa-solid fa-arrow-up-short-wide"></i>
                                </span>
                                <select name="sort" id="sort" class="w-full h-10 pl-10 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" {{ empty(($filtros['sort'] ?? '')) ? 'selected' : '' }}>Mais recentes</option>
                                    <option value="proxima" {{ ($filtros['sort'] ?? '') === 'proxima' ? 'selected' : '' }}>Mais próxima</option>
                                    <option value="longe" {{ ($filtros['sort'] ?? '') === 'longe' ? 'selected' : '' }}>Mais longe</option>
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-12 flex items-center justify-between pt-4">
                            <div class="text-sm text-gray-500">
                                {{ count($viagens) }} {{ count($viagens) === 1 ? 'viagem encontrada' : 'viagens encontradas' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('myTrips') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                                    Limpar
                                </a>
                                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                                    Aplicar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Cards de Viagens do Usuário -->
            @if(isset($viagens) && count($viagens) > 0)
                @foreach($viagens as $viagem)
                    @include('components/myTrips/cardTrips', ['viagem' => $viagem])
                @endforeach
            @else
                <div class="text-center py-10">
                    <i class="fa-solid fa-plane-slash text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Nenhuma viagem encontrada. Que tal começar a planejar sua próxima aventura?</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection