<div id="confirmation-modal" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 transition-all duration-300 hidden">
    <div class="w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden animate-fadeIn mt-10">
        <form action="{{ route('flights.saveFlights') }}" method="POST">
            @csrf
            <input type="hidden" name="flight_data" id="flight_data">
            <input type="hidden" name="viagem_id" id="viagem_id">
            <div class="bg-gradient-to-r from-blue-600 to-blue-400 px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-white mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-300 text-3xl"></i>
                        Aviso!
                    </h1>
                    <p class="text-blue-100 text-base">Confira as informações antes de confirmar o voo.</p>
                </div>
                <button type="button" onclick="document.getElementById('confirmation-modal').classList.add('hidden')" class="text-white text-2xl hover:text-blue-200 transition-colors">&times;</button>
            </div>
            <div class="gap-4 p-9">
                <div id="aviso-data" class="flex items-center gap-2 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-400 px-4 py-2 rounded mb-4 font-semibold text-base" style="display:none">
                    <i class="fa-solid fa-calendar-exclamation text-yellow-500"></i>
                    <span></span>
                </div>
                <div id="span-confirm-orc" class="flex items-center gap-2 text-red-700 bg-red-100 border-l-4 border-red-400 px-4 py-2 rounded mb-2 font-semibold text-base hidden">
                    <i class="fa-solid fa-wallet text-red-500"></i>
                    <span id="confirm-orcamento"></span> {{ $user->currency ?? 'BRL' }}
                </div>
                <div id="span-confirm-preco" class="flex items-center gap-2 text-red-700 bg-red-100 border-l-4 border-red-400 px-4 py-2 rounded mb-2 font-semibold text-base hidden">
                    <i class="fa-solid fa-money-bill-wave text-red-500"></i>
                    <span id="confirm-preco"></span> {{ $user->currency ?? 'BRL' }}
                </div>
            </div>
            <div class="p-6 flex justify-center gap-4">
                <button type="button" id="btn-confirmar" class="bg-blue-600 group-hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition-all duration-150 cursor-pointer">Confirmar</button>
                <button type="button" class="select-trip-btn bg-red-600 group-hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition-all duration-150 cursor-pointer" id="cancel">Cancelar</button>
            </div>
        </form>
    </div>
</div>