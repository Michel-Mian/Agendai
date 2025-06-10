@extends('index')

@section('content')
    <div class="flex min-h-screen">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
                <div class="max-w-7xl mx-auto w-full py-8">
                    <div class="bg-white rounded-lg shadow-md mb-6">
                            <div class="p-6 flex">
                                <div class="flex items-center mb-2">
                                    <h2 class="text-3xl font-bold mr-3">LATAM</h2>
                                </div>
                                <div class="ml-20">
                                    <p class="text-xl font-bold">LATAM</p>
                                    <p class="text-gray-500">Econômica</p>
                                </div>
                                <div class="ml-20">
                                    <p class="text-2xl font-bold">8:30</p>
                                    <span class="text-gray-500 ">GRU</span>
                                </div>
                            </div>
                    </div>
                </div>
        </div>
    </div>
@endsection