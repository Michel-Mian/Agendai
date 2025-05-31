@extends('index')

@section('content')
    <!-- Header/Navigation -->
    @include('components.home.header')

    <!-- Hero Section -->
    @include('components.home.hero-section')

    <!-- Stats -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 w-full px-4">
        <div class="flex flex-wrap justify-center gap-4 text-white text-center max-w-4xl mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 min-w-[120px]">
                <div class="text-2xl font-bold">50K+</div>
                <div class="text-sm">Locais Mapeados</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 min-w-[120px]">
                <div class="text-2xl font-bold">200+</div>
                <div class="text-sm">Cidades</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 min-w-[120px]">
                <div class="text-2xl font-bold">1M+</div>
                <div class="text-sm">Exploradores</div>
            </div>
        </div>
    </div>
    </section>

    <!-- Main Features Section -->
    <section id="explorar" class="w-full py-12 md:py-20 bg-white">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Explore Tudo em Um Só Lugar</h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa plataforma oferece um mapa interativo completo para você descobrir e explorar qualquer destino com facilidade
                </p>
            </div>

            
        </div>
    </section>

    <!-- Categories Section -->
    <section id="funcionalidades" class="w-full py-12 md:py-20 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 max-w-7xl mx-auto">
                <!-- Hospedagens -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Hospedagens</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Hotéis, pousadas, hostels e acomodações de todos os tipos com avaliações e preços atualizados
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Hotéis de luxo e econômicos</li>
                        <li>• Pousadas e hostels</li>
                        <li>• Apartamentos e casas</li>
                        <li>• Avaliações e fotos</li>
                    </ul>
                </div>

                <!-- Restaurantes -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Restaurantes</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Descubra a gastronomia local com restaurantes, bares, cafés e lanchonetes próximos
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Culinária local e internacional</li>
                        <li>• Bares e cafeterias</li>
                        <li>• Cardápios e preços</li>
                        <li>• Horários de funcionamento</li>
                    </ul>
                </div>

                <!-- Pontos Turísticos -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Pontos Turísticos</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Explore museus, monumentos, parques e atrações imperdíveis de cada destino
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Museus e galerias</li>
                        <li>• Monumentos históricos</li>
                        <li>• Parques e praças</li>
                        <li>• Ingressos e horários</li>
                    </ul>
                </div>

                <!-- Casas de Câmbio -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Casas de Câmbio</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Encontre as melhores cotações e locais para trocar sua moeda com segurança
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Cotações em tempo real</li>
                        <li>• Bancos e casas de câmbio</li>
                        <li>• ATMs e caixas eletrônicos</li>
                        <li>• Taxas e comissões</li>
                    </ul>
                </div>

                <!-- Transporte -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Transporte</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Metrô, ônibus, táxis, aluguel de carros e todas as opções de mobilidade urbana
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Transporte público</li>
                        <li>• Táxis e ride-sharing</li>
                        <li>• Aluguel de veículos</li>
                        <li>• Rotas e horários</li>
                    </ul>
                </div>

                <!-- Serviços Essenciais -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 text-center">Serviços Essenciais</h3>
                    <p class="text-sm md:text-base text-gray-600 text-center mb-3 md:mb-4">
                        Farmácias, hospitais, delegacias e outros serviços importantes para sua segurança
                    </p>
                    <ul class="text-xs md:text-sm text-gray-500 space-y-1">
                        <li>• Farmácias e hospitais</li>
                        <li>• Delegacias e bombeiros</li>
                        <li>• Embaixadas e consulados</li>
                        <li>• Emergências 24h</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section class="w-full py-12 md:py-20 bg-white">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Como Funciona</h2>
                <p class="text-lg md:text-xl text-gray-600">Simples, rápido e intuitivo</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <span class="text-xl md:text-2xl font-bold text-emerald-600">1</span>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Abra o Mapa</h3>
                    <p class="text-sm md:text-base text-gray-600">
                        Acesse nossa plataforma e permita a localização para ver o que está ao seu redor
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <span class="text-xl md:text-2xl font-bold text-emerald-600">2</span>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Filtre e Busque</h3>
                    <p class="text-sm md:text-base text-gray-600">
                        Use os filtros para encontrar exatamente o que procura: restaurantes, hotéis, pontos turísticos
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <span class="text-xl md:text-2xl font-bold text-emerald-600">3</span>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Explore e Descubra</h3>
                    <p class="text-sm md:text-base text-gray-600">
                        Veja informações detalhadas, avaliações, fotos e trace rotas para seus destinos favoritos
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="w-full py-12 md:py-20 bg-emerald-600">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 md:mb-6">
                    Comece Sua Exploração Agora
                </h2>
                <p class="text-lg md:text-xl text-emerald-100 mb-6 md:mb-8">
                    Descubra tudo o que o mundo tem a oferecer com nosso mapa interativo. É gratuito e fácil de usar!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/mapa" class="bg-white text-emerald-600 px-6 md:px-8 py-3 md:py-4 rounded-lg text-base md:text-lg font-semibold hover:border-2 hover:border-white hover:bg-emerald-600 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="#funcionalidades" class="border-2 border-white text-white hover:bg-white hover:text-emerald-600 px-6 md:px-8 py-3 md:py-4 rounded-lg text-base md:text-lg font-semibold transition-colors">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.home.footer')

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
    </style>
@endsection