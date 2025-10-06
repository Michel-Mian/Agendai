@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components/layout/header')
        <div class="max-w-3xl mx-auto w-full py-15">
            <!-- Cards de Viagens do Usuário -->
            @if(isset($viagens) && count($viagens) > 0)
                @foreach($viagens as $viagem)
                    @include('components/myTrips/cardTrips', ['viagem' => $viagem])
                @endforeach
            @else
                <div class="text-center py-10">
                    <i class="fa-solid fa-plane-slash text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Nenhuma viagem encontrada. Que tal começar a planejar sua próxima aventura?</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection