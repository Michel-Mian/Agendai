<footer id="contato" class="w-full bg-gray-900 text-white py-8 md:py-12">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8 max-w-7xl mx-auto">
            <!-- AgendAí
             : ocupa todas as 3 colunas -->
            <div class="col-span-3 lg:col-span-1">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="flex items-center space-x-2 h-20 overflow-hidden">
                        <img src="{{asset('/imgs/logoagendaibranco.png')}}" alt="" class="h-20 w-auto object-cover">
                    </div>
                    <br>
                    <span class="text-lg md:text-xl font-bold">AgendAí

                    </span>
                </div>
                <p class="text-sm md:text-base text-gray-400">
                    Sua plataforma completa para explorar o mundo através de mapas interativos inteligentes.
                </p>
            </div>

            <!-- Explorar -->
            <div>
                <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Explorar</h4>
                <ul class="space-y-2 text-sm md:text-base text-gray-400">
                    <li><p class="hover:text-emerald-400 transition-colors">Hospedagens</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Restaurantes</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Pontos Turísticos</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Casas de Câmbio</p></li>
                </ul>
            </div>

            <!-- Recursos -->
            <div>
                <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Recursos</h4>
                <ul class="space-y-2 text-sm md:text-base text-gray-400">
                    <li><p class="hover:text-emerald-400 transition-colors">Mapa Interativo</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Filtros Avançados</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Rotas e Direções</p></li>
                    <li><p class="hover:text-emerald-400 transition-colors">Avaliações</p></li>
                </ul>
            </div>

            <!-- Suporte -->
            <div>
                <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Membros</h4>
                <div class="space-y-2 text-sm md:text-base text-gray-400">
                    <p class="hover:text-emerald-400 transition-colors">Matheus Porcaro</p>
                    <p class="hover:text-emerald-400 transition-colors">Michel Mian</p>
                    <p class="hover:text-emerald-400 transition-colors">Nara Stachetti</p>
                    <p class="hover:text-emerald-400 transition-colors">Rafael Fante</p>
                    <p class="hover:text-emerald-400 transition-colors">Samy Maiorini</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-6 md:mt-8 pt-6 md:pt-8 text-center text-sm md:text-base text-gray-400">
            <p>&copy; {{ date('Y') }} AgendAí
                . Todos os direitos reservados. Agenda aí a sua viagem.</p>
        </div>
    </div>
</footer>