<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Preferências</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        @foreach(['Cultura e história' => asset('imgs/objectives/open-book.png'), 'Gastronomia' => asset('imgs/objectives/restaurant.png'), 'Natureza' => asset('imgs/objectives/landscape.png'), 'Aventura' => asset('imgs/objectives/hiking.png'), 'Praia' => asset('imgs/objectives/beach-umbrella.png'), 'Vida noturna' => asset('imgs/objectives/moon.png'), 'Compras' => asset('imgs/objectives/shopping-cart.png'), 'Arte e museus' => asset('imgs/objectives/museum.png')] as $pref => $icon)
            <button type="button" class="pref-btn flex flex-col items-center gap-2" data-preference="{{ $pref }}">
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

<style>
.pref-btn {
    border: 2px solid #e5e7eb !important; 
    border-radius: 16px !important; 
    padding: 22px 12px !important;
    background: #f9fafb !important; 
    cursor: pointer !important; 
    transition: all 0.3s ease !important;
    min-width: 120px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
}

.pref-btn.selected {
    border-color: #22c55e !important; 
    background: linear-gradient(135deg, #dcfce7, #bbf7d0) !important;
    transform: scale(1.05) !important;
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.25) !important;
}

.pref-btn:hover:not(.selected) {
    border-color: #3b82f6 !important; 
    background: #eff6ff !important;
    transform: scale(1.02) !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15) !important;
}

.pref-btn img {
    filter: grayscale(0.3);
    transition: filter 0.3s ease;
}

.pref-btn.selected img {
    filter: grayscale(0) saturate(1.2);
}

.pref-btn span {
    transition: color 0.3s ease, font-weight 0.3s ease;
}

.pref-btn.selected span {
    color: #166534 !important;
    font-weight: 600 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    
    let selectedPrefs = [];
    const preferencesInput = document.getElementById('preferences');
    
    document.querySelectorAll('.pref-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const prefText = this.getAttribute('data-preference');

            
            // Toggle visual imediato
            this.classList.toggle('selected');
            
            // Atualizar array
            if (selectedPrefs.includes(prefText)) {
                selectedPrefs = selectedPrefs.filter(p => p !== prefText);
            } else {
                selectedPrefs.push(prefText);
            }
            
            // Atualizar input
            if (preferencesInput) {
                preferencesInput.value = selectedPrefs.join(',');
            }
            

        });
    });
});
</script>