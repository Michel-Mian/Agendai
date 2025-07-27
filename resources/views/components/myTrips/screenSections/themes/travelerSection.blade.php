<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green-800">Viajantes</h2>
                    <p class="text-green-600 text-sm">{{ $viajantes->count() }} {{ $viajantes->count() == 1 ? 'viajante' : 'viajantes' }}</p>
                </div>
            </div>
            <button type="button" id="open-add-viajante-modal-btn" class="bg-white hover:bg-green-100 cursor-pointer text-green-500 border-2 border-green-400 p-2 rounded-lg transition-colors" title="Adicionar viajante">
                <i class="fas fa-user-plus text-lg"></i> Adicionar Viajantes
            </button>
        </div>
    </div>
    
    <div class="p-6">
        @if($viajantes->count())
            <div class="space-y-3">
                @php
                    $viajantesExibidos = ($viajantes->count() > 4) ? $viajantes->take(4) : $viajantes;
                @endphp
                @foreach($viajantesExibidos as $viajante)
                    <div class="group flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($viajante->nome, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $viajante->nome }}</div>
                                <div class="text-sm text-gray-600">{{ $viajante->idade }} anos</div>
                            </div>
                        </div>
                        <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors" title="Remover viajante">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
            
            @if($viajantes->count() > 3)
                <div class="mt-4">
                    <button id="open-viajantes-modal-btn" class="w-full bg-gradient-to-r from-green-100 to-green-200 hover:from-green-200 hover:to-green-300 text-green-700 font-medium py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-eye"></i>
                        <span>Ver todos os viajantes ({{ $viajantes->count() }})</span>
                    </button>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-green-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 mb-4">Nenhum viajante cadastrado</p>
                <button type="button" id="open-add-viajante-modal-btn-empty" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Adicionar primeiro viajante
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        
        const addViajanteEmptyBtn = document.getElementById('open-add-viajante-modal-btn-empty');
        if (addViajanteEmptyBtn) {
            addViajanteEmptyBtn.addEventListener('click', function() {
                document.getElementById('open-add-viajante-modal-btn').click();
            });
        }
    });
</script>