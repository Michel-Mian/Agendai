@extends('index')

@section('content')
<div class="flex min-h-screen">
    @include('components/layout/sidebar')
    <div id="main-content" class="flex-1 flex flex-col bg-[var(--color-neutral-50)]">
        @include('components/layout/header')

        @include('components/myProfile/formProfile')
    </div>
</div>

@endsection



