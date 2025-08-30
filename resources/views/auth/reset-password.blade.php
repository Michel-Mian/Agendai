@extends('index')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100">
    <div class="w-full max-w-md bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 border border-gray-200">
        <div class="flex flex-col items-center">
            <img src="{{asset('imgs/logoagendaipreto.png')}}" alt="" class="mx-auto w-32 md:w-40 lg:w-48 h-auto">
            <h2 class="text-3xl font-extrabold mb-2 text-gray-800 text-center drop-shadow">Redefinir Senha</h2>
            <p class="text-gray-500 text-sm mb-6 text-center">Digite seu e-mail e nova senha abaixo</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded-lg px-4 py-3 text-sm shadow">
                <ul class="list-disc px-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-gray-700 font-semibold mb-1">E-mail</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
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

            <div>
                <label for="password" class="block text-gray-700 font-semibold mb-1">Nova senha</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2 pl-10 border rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-400 @error('password') border-red-500 @enderror transition">
                    <span class="absolute left-3 top-2.5 text-pink-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 17v1m0-8a4 4 0 00-4 4v4a4 4 0 008 0v-4a4 4 0 00-4-4zm0-2a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </span>
                </div>
                @error('password')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password-confirm" class="block text-gray-700 font-semibold mb-1">Confirme a nova senha</label>
                <div class="relative">
                    <input id="password-confirm" type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 pl-10 border rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
                    <span class="absolute left-3 top-2.5 text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 6a6 6 0 100-12 6 6 0 000 12z" />
                        </svg>
                    </span>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-green-300 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:from-green-300 hover:to-blue-600 transition duration-200 text-lg tracking-wide">
                Redefinir senha
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm font-semibold">
                <svg class="inline w-4 h-4 mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Voltar para o login
            </a>
        </div>
    </div>
</div>
@endsection