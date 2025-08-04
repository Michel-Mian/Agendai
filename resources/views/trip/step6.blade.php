<div class="form-step">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Revisão final</h2>
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl p-6 text-white mb-6">
        <h3 class="text-xl font-bold mb-4">Confira seus dados:</h3>
        <ul class="space-y-2 text-base" id="reviewList">
            <!-- Os dados preenchidos aparecerão aqui via JS -->
        </ul>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="flex justify-between">
        <button type="button" class="prev-btn btn-secondary">← Voltar</button>
        <button type="submit" class="btn-primary">Finalizar</button>
    </div>
</div>