<section id="sobre" class="w-full py-12 md:py-20 bg-gray-50">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Sobre nós</h2>
            <p class="text-lg md:text-xl text-gray-600">Nossos fundadores</p>
        </div>

        <!-- Grid 3+2 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <!-- Primeira linha: 3 cards -->
            <div class="text-center">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-emerald-100 flex items-center justify-center mx-auto mb-4 md:mb-6 overflow-hidden">
                    <img src="{{ asset('imgs/team/matheus.png') }}" alt="Matheus Porcaro" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Matheus Porcaro</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">Desenvolvedor front-end</p>
                <div class="flex justify-center">
                                <a href="https://www.linkedin.com/in/matheus-henrique-porcaro-91a7482a6" target="_blank" rel="noopener"
                                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 rounded-full p-3 flex items-center justify-center transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 448 512" class="w-6 h-6">
                            <path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="text-center">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-emerald-100 flex items-center justify-center mx-auto mb-4 md:mb-6 overflow-hidden">
                    <img src="{{ asset('imgs/team/michel.jpeg') }}" alt="Michel Mian" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Michel Mian</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">Desenvolvedor back-end</p>
                <div class="flex justify-center">
                    <a href="https://www.linkedin.com/in/michel-mian-56ab16324" target="_blank" rel="noopener"
                        class="bg-blue-600 hover:bg-blue-700 rounded-full p-3 flex items-center justify-center transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 448 512" class="w-6 h-6">
                            <path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="text-center">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-emerald-100 flex items-center justify-center mx-auto mb-4 md:mb-6 overflow-hidden">
                    <img src="{{ asset('imgs/team/nara.png') }}" alt="Nara Stachetti" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Nara Stachetti</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">Documentadora</p>
                <div class="flex justify-center">
                    <a href="https://mail.google.com/mail/u/0/?fs=1&tf=cm&source=mailto&to=narastachetti@gmail.com" target="_blank" rel="noopener"
                        class="bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-full p-3 flex items-center justify-center transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="size-6">
                            <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                            <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto mt-8">
            <!-- Segunda linha: 2 cards centralizados -->
            <div class="text-center md:col-start-1 md:col-end-2">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-emerald-100 flex items-center justify-center mx-auto mb-4 md:mb-6 overflow-hidden">
                    <img src="{{ asset('imgs/team/rafael.jpeg') }}" alt="Rafael Fante" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Rafael Fante</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">Desenvolvedor back-end</p>
                <div class="flex justify-center">
                                <a href="https://www.linkedin.com/in/rafael-fante-713111370?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app" target="_blank" rel="noopener"
                                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 rounded-full p-3 flex items-center justify-center transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 448 512" class="w-6 h-6">
                            <path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="text-center md:col-start-2 md:col-end-3">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-emerald-100 flex items-center justify-center mx-auto mb-4 md:mb-6 overflow-hidden">
                    <img src="{{ asset('imgs/team/samy.jpeg') }}" alt="Samy Maiorini" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Samy Maiorini</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">Desenvolvedor front-end</p>
                <div class="flex justify-center">
                    <a href="https://www.linkedin.com/in/samy-fabr%C3%ADcio-maiorini-pav%C3%A3o-654376307?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" rel="noopener"
                        class="bg-blue-600 hover:bg-blue-700 rounded-full p-3 flex items-center justify-center transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 448 512" class="w-6 h-6">
                            <path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Fim do grid 3+2 -->
</section>