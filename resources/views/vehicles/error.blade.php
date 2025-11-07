<div class="bg-white rounded-xl shadow-lg p-12 text-center">
    <div class="flex flex-col items-center justify-center space-y-4">
        <i class="fas fa-exclamation-circle text-red-500 text-6xl"></i>
        <h3 class="text-2xl font-bold text-gray-800">Erro na busca</h3>
        <p class="text-gray-600 max-w-md" id="error-message">
            Ocorreu um erro ao buscar veículos. Por favor, tente novamente.
        </p>
        <button 
            onclick="location.reload()" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all"
        >
            <i class="fas fa-redo mr-2"></i>
            Tentar Novamente
        </button>
    </div>
</div>
