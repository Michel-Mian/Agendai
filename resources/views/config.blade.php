@extends('index')

@section('content')
<div id="main-content" class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col bg-[var(--color-neutral-50)]">
        @include('components/layout/header')
        <main class="flex-1 p-8">
                @include('components/config/formConfig')
        </main>
    </div>
@endsection