@if(isset($showClickableHint) && $showClickableHint)
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center space-x-2">
            <i class="fas fa-info-circle text-blue-500"></i>
            <span class="text-sm text-blue-700">Clique em um objetivo para explorar locais relacionados no mapa</span>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($objetivosExibidos as $index => $objetivo)
        <div class="objetivo-card cursor-pointer group relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 hover:shadow-md transition-all duration-200 aspect-square flex flex-col hover:scale-105 hover:border-purple-300" 
             data-objetivo-nome="{{ $objetivo->nome }}"
             title="Clique para explorar {{ $objetivo->nome }} no mapa">
            
            <div class="absolute top-3 left-3 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ $index + 1 }}
            </div>
            
            <form action="{{ route('objetivos.destroy', ['id' => $objetivo->pk_id_objetivo]) }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="event.stopPropagation();">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 rounded-md transition-colors shadow-lg" title="Remover objetivo" onclick="return confirm('Tem certeza que deseja remover este objetivo?')">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </form>

            <div class="p-4 flex flex-col items-center justify-center h-full text-center">
                @php
                    $imagePath = \App\Helpers\ObjectiveImageHelper::getImagePath($objetivo->nome);
                    $imageExists = \App\Helpers\ObjectiveImageHelper::imageExists($imagePath);
                @endphp
                
                @if($imageExists)
                    <img src="{{ asset($imagePath) }}" alt="{{ $objetivo->nome }}" class="w-16 h-16 mb-2 group-hover:scale-110 transition-transform">
                @else
                    <!-- Fallback icon usando FontAwesome -->
                    <div class="w-16 h-16 mb-2 bg-purple-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-bullseye text-purple-600 text-2xl"></i>
                    </div>
                @endif
                
                <span class="font-medium text-gray-800 text-center leading-relaxed group-hover:text-purple-700 transition-colors">{{ $objetivo->nome }}</span>
                
                <div class="mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-external-link-alt text-purple-500 text-sm"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>