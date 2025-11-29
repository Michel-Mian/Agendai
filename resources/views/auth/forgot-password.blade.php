@extends('index')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-purple-100 to-pink-1000">
    <div class="w-full max-w-md bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 border border-gray-200">
        <div class="flex flex-col items-center mb-4">
            <img src="{{ asset('imgs/logoagendaipreto.png') }}" alt="Logo Agendai" class="mx-auto w-32 md:w-40 lg:w-48 h-auto" />
            <h2 class="text-3xl font-extrabold mt-4 mb-2 text-gray-800 text-center drop-shadow">Esqueci minha senha</h2>
            <p class="text-gray-500 text-sm mb-4 text-center">Informe seu e-mail para receber o link de redefinição</p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded-lg px-4 py-3 text-sm shadow">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-gray-700 font-semibold mb-1">E-mail</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 pl-10 border rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror transition">
                    <span class="absolute left-3 top-2.5 text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 12V4a2 2 0 00-2-2H6a2 2 0 00-2 2v16a2 2 0 002 2h8a2 2 0 002-2v-8m-6 4l4-4-4-4" />
                        </svg>
                    </span>
                </div>
                @error('email')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-green-400 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:from-blue-700 hover:to-green-500 transition duration-200 text-lg tracking-wide">
                Enviar link de redefinição
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-700 hover:underline text-sm font-semibold">
                <svg class="inline w-4 h-4 mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Voltar para o login
            </a>
        </div>
    </div>
</div>
@endsection