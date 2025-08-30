<form action="{{ route('password.change') }}" method="POST" class="w-full px-2 sm:px-0">
    @csrf
    <div class="bg-white/90 shadow-xl rounded-2xl p-4 sm:p-8 max-w-full sm:max-w-xl mx-auto border border-blue-100" id="cardPreferences">
        <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 mb-6 text-center">Configurações</h2>
        <div class="mb-5">
            <label class="block text-base sm:text-lg font-semibold text-blue-900 mb-1">Informe sua senha atual</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-400">
                    <i class="fa-solid fa-lock" style="color:#55aef3;"></i>
                </span>
                <input type="password" id="current_password" name="current_password" class="pl-10 pr-4 py-2 block w-full border border-blue-200 rounded-lg shadow-sm focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition text-sm sm:text-base" required>
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-base sm:text-lg font-semibold text-blue-900 mb-1">Informe sua nova senha</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-400">
                    <i class="fa-solid fa-lock" style="color:#55aef3;"></i>
                </span>
                <input type="password" id="new_password" name="new_password" class="pl-10 pr-4 py-2 block w-full border border-blue-200 rounded-lg shadow-sm focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition text-sm sm:text-base" required>
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-base sm:text-lg font-semibold text-blue-900 mb-1">Confirme sua nova senha</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-400">
                    <i class="fa-solid fa-lock" style="color:#55aef3;"></i>
                </span>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="pl-10 pr-4 py-2 block w-full border border-blue-200 rounded-lg shadow-sm focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition text-sm sm:text-base" required>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between mt-8 gap-4">
            <button type="reset" class="flex-1 px-6 py-2 bg-gray-100 text-blue-700 rounded-lg font-semibold shadow hover:bg-gray-200 transition cursor-pointer text-sm sm:text-base">Limpar</button>
            <button type="submit" class="flex-1 px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-600 transition cursor-pointer text-sm sm:text-base">Salvar Preferências</button>
        </div>
    </div>
</form>