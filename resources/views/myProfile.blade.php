@extends('index')

@section('content')
<div class="flex min-h-screen">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        @include('components/myProfile/formProfile')
    </div>
</div>

@endsection



