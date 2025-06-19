@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')
        <div class="max-w-3xl mx-auto w-full py-15">
            <!-- Card de Viagem -->
            @include('components/myTrips/cardTrips')
            @include('components/myTrips/cardTrips')
        </div>
    </div>
</div>
@endsection