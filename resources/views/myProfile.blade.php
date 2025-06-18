@extends('index')

@section('content')
<div class="flex min-h-screen">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        <div class="flex-100 p-6 bg-gray-50">
            <div class="flex bg-blue-50 rounded-lg shadow mb-6 max-w-xl mx-auto border border-blue-100">
                <button type="button" class="flex-1 px-6 py-2 bg-white text-blue-900 rounded-lg font-semibold shadow transition cursor-pointer focus:outline-none" style="box-shadow: 0 2px 8px 0 #e0e7ef;" id="btnProfile">
                    Perfil
                </button>
                <button type="button" class="flex-1 px-6 py-2 bg-transparent text-blue-700 rounded-lg font-semibold transition cursor-pointer hover:bg-gray-100 hover:text-blue-900" id="btnPreferences">
                    Preferências
                </button>
            </div>
            <div class="bg-white/90 shadow-xl rounded-2xl p-8 max-w-xl mx-auto border border-blue-100" id="cardProfile">
                @if ($errors->any())
                    <div class="mb-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <!-- Formulário de perfil -->
                <form action="{{ route('user.updateProfile', $user->id) }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')

                    <!-- Foto de Perfil -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative group">
                            <img 
                                src="{{ $user->profile_photo_url ? asset($user->profile_photo_url) : asset('images/default-profile.png') }}" 
                                alt="Foto de Perfil"
                                class="w-36 h-36 rounded-full object-cover shadow-lg transition group-hover:brightness-75"
                                id="profileImagePreview"
                            >
                            <label for="profile_photo" class="absolute bottom-2 right-2 bg-blue-600 text-white rounded-full p-2 cursor-pointer hover:bg-blue-700 shadow-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2a2.828 2.828 0 11-4-4 2.828 2.828 0 014 4z" />
                                </svg>
                                <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" onchange="previewProfileImage(event)">
                            </label>
                        </div>
                        <span class="text-gray-500 text-sm mt-3">Clique no ícone para alterar a foto</span>
                    </div>

                    <!-- Campos do Perfil -->
                    <div class="mb-5">
                        <label for="name" class="block text-lg font-semibold text-blue-900 mb-1">Nome</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-400">
                                <i class="fa-solid fa-user" style="color:#55aef3;"></i>
                            </span>
                            <input type="text" id="name" name="name" value="{{ $user->name ?? '' }}" class="pl-10 pr-4 py-2 block w-full border border-blue-200 rounded-lg shadow-sm focus focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition" placeholder="Seu nome">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block text-lg font-semibold text-blue-900 mb-1">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-400">
                                <i class="fa-solid fa-envelope" style="color:#55aef3;"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ $user->email ?? '' }}" class="pl-10 pr-4 py-2 block w-full border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition" placeholder="Seu email">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-between mt-8 gap-4">
                        <button type="button" onclick="clearProfileForm()" class="flex-1 px-6 py-2 bg-gray-100 text-blue-700 rounded-lg font-semibold shadow hover:bg-gray-200 transition cursor-pointer">Limpar</button>
                        <button type="submit" class="flex-1 px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-600 transition cursor-pointer">Salvar Alterações</button>
                    </div>
                </form>
            </div>
            <div class="bg-white/90 shadow-xl rounded-2xl p-8 max-w-xl mx-auto border border-blue-100 hidden" id="cardPreferences">
                <h2 class="text-2xl font-semibold text-blue-900 mb-6">Preferências</h2>
                <!-- Formulário de preferências -->
                <form action="{{ route('user.updatePreferences', $user->id) }}" method="POST" id="preferencesForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-5">
                        <label for="language" class="block text-lg font-semibold text-blue-900 mb-1">Idioma</label>
                        <select id="language" name="language" class="block w-full border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition py-2 px-3">
                            <option value="en">Inglês</option>
                            <option value="pt">Português</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="theme" class="block text-lg font-semibold text-blue-900 mb-1">Tema</label>
                        <select id="theme" name="theme" class="block w-full border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition py-2 px-3">
                            <option value="light">Claro</option>
                            <option value="dark">Escuro</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="currency" class="block text-lg font-semibold text-blue-900 mb-1">Moeda</label>
                        <select id="currency" name="currency" class="block w-full border border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-blue-900 placeholder-blue-300 transition py-2 px-3">
                            @foreach($currencies as $code => $name)
                                <option value="{{ $code }}" {{ $user->currency == $code ? 'selected' : '' }}>
                                    {{ $name }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-between mt-8 gap-4">
                        <button type="button" onclick="clearPreferencesForm()" class="flex-1 px-6 py-2 bg-gray-100 text-blue-700 rounded-lg font-semibold shadow hover:bg-gray-200 transition cursor-pointer">Limpar</button>
                        <button type="submit" class="flex-1 px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-600 transition cursor-pointer">Salvar Preferências
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection



