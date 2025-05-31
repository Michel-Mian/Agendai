@extends('index')

@section('title', 'Registro - ExploreMap')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Side - Image & Content (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-purple-600 to-emerald-600 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 flex flex-col justify-center px-12 py-12 text-white">
            <div class="max-w-md">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="{{asset('/imgs/logoagendaibranco.png')}}" alt="" srcset="" class="h-40 w-auto">
                </div>
                
                <!-- Main Content -->
                <h2 class="text-4xl font-bold mb-6 leading-tight">
                    Comece sua jornada de descobertas hoje
                </h2>
                <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                    Crie sua conta gratuita e tenha acesso a milhares de lugares incríveis ao redor do mundo.
                </p>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-32 w-24 h-24 bg-purple-300/20 rounded-full blur-lg"></div>
        <div class="absolute top-1/2 right-10 w-16 h-16 bg-emerald-300/30 rounded-full blur-md"></div>
    </div>

    <!-- Right Side - Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-3 lg:p-6 bg-gray-50">
        <div class="w-full max-w-md">
            <!-- Mobile Logo (Only visible on mobile) -->
            <div class="lg:hidden text-center mb-4">
                <div class="flex items-center justify-center space-x-2 mb-3">
                    <img src="{{asset('/imgs/logoagendaipreto.png')}}" alt="" srcset="" class="h-25 w-auto">
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Criar Conta</h2>
                <p class="text-sm text-gray-600">Comece sua jornada de descobertas</p>
            </div>

            <!-- Desktop Header -->
            <div class="hidden lg:block text-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Criar Conta</h2>
                <p class="text-sm text-gray-600">Preencha os dados para começar</p>
            </div>

            <!-- Register Form -->
            <form class="space-y-3" action="/register" method="POST">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nome Completo
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            required 
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="Seu nome completo"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="seu@email.com"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Terms & Privacy -->
                <div class="flex items-start pt-1">
                    <div class="flex items-center h-5">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required
                            class="h-3.5 w-3.5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        >
                    </div>
                    <div class="ml-2 text-xs">
                        <label for="terms" class="text-gray-700">
                            Eu concordo com os 
                            <a href="/terms" class="text-emerald-600 hover:text-emerald-500 transition-colors">Termos de Uso</a>
                            e 
                            <a href="/privacy" class="text-emerald-600 hover:text-emerald-500 transition-colors">Política de Privacidade</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                >
                    Criar Conta
                </button>

                 
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-red-800">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Divider -->
                <div class="relative my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-gray-50 text-gray-500">ou</span>
                    </div>
                </div>
            </form>

            <!-- Login Link -->
            <p class="mt-4 text-center text-xs text-gray-600">
                Já tem uma conta? 
                <a href="/login" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                    Fazer login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection