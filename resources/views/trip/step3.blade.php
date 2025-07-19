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
    <input type="hidden" name="preferences[]" id="preferences" value="">
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="button" class="next-btn btn-primary">Próximo →</button>
    </div>
</div>