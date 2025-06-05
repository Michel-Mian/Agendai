@extends('index')

@section('content')
<div class="flex min-h-screen">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')
        <div class="flex-100 p-6">
            <div class="bg-white/90 shadow-xl rounded-2xl p-8 max-w-xl mx-auto border border-blue-100">
                <form action="" method="POST" enctype="multipart/form-data" id="profileForm">
                    @isset($user)
                        @method('put')
                    @endisset
                    @csrf

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
                        <button type="button" onclick="clearProfileForm()" class="flex-1 px-6 py-2 bg-gray-100 text-blue-700 rounded-lg font-semibold shadow hover:bg-gray-200 transition">Limpar</button>
                        <button type="submit" class="flex-1 px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-600 transition">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection