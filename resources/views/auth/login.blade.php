@extends('index')

@section('title', 'Login - ExploreMap')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Side - Image & Content (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-blue-600 relative overflow-hidden">
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
        <div class="relative z-10 flex flex-col justify-center px-12 py-1 text-white">
            <div class="max-w-md">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="{{asset('/imgs/logoagendaipreto.png')}}" alt="" srcset="" class="h-40 w-auto overflow-hidden">
                </div>
                
                <!-- Main Content -->
                <h2 class="text-4xl font-bold mb-6 leading-tight">
                    Bem-vindo de volta ao seu mundo de descobertas
                </h2>
                <p class="text-xl text-emerald-100 mb-8 leading-relaxed">
                    Acesse sua conta e continue explorando os melhores lugares ao seu redor com nosso mapa interativo.
                </p>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-32 w-24 h-24 bg-emerald-300/20 rounded-full blur-lg"></div>
        <div class="absolute top-1/2 right-10 w-16 h-16 bg-blue-300/30 rounded-full blur-md"></div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-gray-50">
        <div class="w-full max-w-md">
            <!-- Mobile Logo (Only visible on mobile) -->
            <div class="lg:hidden text-center mb-8">
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <img src="{{asset('/imgs/logoagendaipreto.png')}}" alt="" srcset="" class="h-25 w-auto">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Bem-vindo de volta!</h2>
                <p class="text-gray-600">Faça login para continuar explorando</p>
            </div>

            <!-- Desktop Header -->
            <div class="hidden lg:block text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Fazer Login</h2>
                <p class="text-gray-600">Entre na sua conta para continuar</p>
            </div>

            <!-- Login Form -->
            <form class="space-y-6" action="/login" method="POST">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="seu@email.com"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Lembrar de mim
                        </label>
                    </div>
                    <a href="/forgot-password" class="text-sm text-emerald-600 hover:text-emerald-500 transition-colors">
                        Esqueceu a senha?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-emerald-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                >
                    Entrar
                </button>

                @if ($errors->any())
                    <div class="text-red-500 mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-50 text-gray-500">ou</span>
                    </div>
                </div>
            </form>

            <!-- Sign Up Link -->
            <p class="mt-8 text-center text-sm text-gray-600">
                Não tem uma conta? 
                <a href="/register" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                    Criar conta
                </a>
            </p>
        </div>
    </div>
</div>
@endsection