<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Preferências</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        @foreach(['Cultura e história' => asset('imgs/objectives/open-book.png'), 'Gastronomia' => asset('imgs/objectives/restaurant.png'), 'Natureza' => asset('imgs/objectives/landscape.png'), 'Aventura' => asset('imgs/objectives/hiking.png'), 'Praia' => asset('imgs/objectives/beach-umbrella.png'), 'Vida noturna' => asset('imgs/objectives/moon.png'), 'Compras' => asset('imgs/objectives/shopping-cart.png'), 'Arte e museus' => asset('imgs/objectives/museum.png')] as $pref => $icon)
            <button type="button" class="pref-btn flex flex-col items-center gap-2">
                <img src="{{ $icon }}" alt="icone" class="w-10 h-10">
                <span class="text-gray-700 font-medium">{{ $pref }}</span>
            </button>
        @endforeach
    </div>
    <input type="hidden" name="preferences[]" id="preferences" value="">
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>