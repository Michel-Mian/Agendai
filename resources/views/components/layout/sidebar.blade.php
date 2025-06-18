<div id="sidebar" class="bg-white shadow-lg min-h-screen relative flex flex-col sidebar-expanded">
    <!-- Header com Logo e Toggle -->
    <div id="sidebar-header" class="flex items-center justify-between p-2 border-b border-gray-200 transition-all duration-300" style="background-color: #f7faf8;">
        <div id="logo-container" class="flex items-center justify-center w-full space-x-3">
            <div class="w-24 h-24 rounded-lg flex items-center justify-center p-0" id="logo">
                <a href="/dashboard">
                    <img src="{{ asset('imgs/logoagendaipreto.png') }}" alt="" class="cursor-pointer">
                </a>
            </div>
        </div>
        <button id="toggle-sidebar" class="p-1 rounded-lg hover:none transition-colors cursor-pointer">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>


    <!-- Navigation Menu -->
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <li>
                <a href="/dashboard" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-book"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/myTrips" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-calendar-week"></i>
                    <span class="nav-text">Minhas Viagens</span>
                </a>
            </li>
            <li>
                <a href="/flights" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span class="nav-text">Voos</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span class="nav-text">Criar Viagem</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-earth-americas"></i>
                    <span class="nav-text">Explorar</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Profile Section -->
    <div class="border-t border-gray-200 p-4">
        <div class="relative">
            <button id="profile-button" class="flex items-center space-x-3 w-full rounded-lg hover:bg-gray-50 transition-colors profile-toggle">
                <img src="{{ Auth::user()?->profile_photo_url ? asset(Auth::user()->profile_photo_url) : asset('imgs/default-profile.png') }}"
                     alt="Foto do perfil" 
                     class="w-8 h-8 rounded-full object-cover">
                <div class="flex-1 text-left profile-info">
                    @if(Auth::check())
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    @else
                        <p class="text-xs text-gray-400">Usuário não autenticado</p>
                    @endif
                </div>
                <svg class="w-4 h-4 text-gray-400 profile-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <!-- Dropdown Menu -->
            <div id="profile-dropdown" class="absolute bottom-full left-1 min-w-[200px] w-[260px] mb-2 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                <div class="py-2">
                    @if(Auth::check())
                        <a href="/myProfile/{{  Auth::user()->id }}/edit" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Meu Perfil</span>
                    </a>
                    @else
                        <p class="text-xs text-gray-400">Usuário não autenticado</p>
                    @endif
                    <a href="/config/{{  Auth::user()->id }}/edit" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Configurações</span>
                    </a>
                    <hr class="my-2 border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
