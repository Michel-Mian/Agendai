<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($objetivosExibidos as $index => $objetivo)
    <div class="cursor-pointer group relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 hover:shadow-md transition-all duration-200 aspect-square flex flex-col">
        <div class="absolute top-3 left-3 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
            {{ $index + 1 }}
        </div>
        
        <form action="{{ route('objetivos.destroy', ['id' => $objetivo->pk_id_objetivo]) }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 rounded-md transition-colors shadow-lg" title="Remover objetivo">
                <i class="fas fa-times text-xs"></i>
            </button>
        </form>
        
        <div class="flex-1 flex flex-col items-center justify-center p-4 pt-12">
            @php
                $imagePath = '';
                switch ($objetivo->nome) {
                    case 'Cultura e história':
                        $imagePath = 'imgs/objectives/open-book.png';
                        break;
                    case 'Gastronomia':
                        $imagePath = 'imgs/objectives/restaurant.png';
                        break;
                    case 'Natureza':
                        $imagePath = 'imgs/objectives/landscape.png';
                        break;
                    case 'Aventura':
                        $imagePath = 'imgs/objectives/hiking.png';
                        break;
                    case 'Praia':
                        $imagePath = 'imgs/objectives/beach-umbrella.png';
                        break;
                    case 'Vida noturna':
                        $imagePath = 'imgs/objectives/moon.png';
                        break;
                    case 'Compras':
                        $imagePath = 'imgs/objectives/shopping-cart.png';
                        break;
                    case 'Arte e museus':
                        $imagePath = 'imgs/objectives/museum.png';
                        break;
                    default:
                        $imagePath = 'imgs/objectives/default.png'; // Fallback image if objective name doesn't match
                        break;
                }
            @endphp
            @if($imagePath)
                <img src="{{ asset($imagePath) }}" alt="{{ $objetivo->nome }}" class="w-16 h-16 mb-2">
            @endif
            <span class="font-medium text-gray-800 text-center leading-relaxed">{{ $objetivo->nome }}</span>
        </div>
    </div>
@endforeach
</div>