@extends('index')

@section('content')
    <!-- Header/Navigation -->
    @include('components.home.header')

    <!-- Hero Section -->
    @include('components.home.hero-section')

    <!-- Stats -->
    @include('components.home.stats')

    <!-- Main Features Section -->
    @include('components.home.main-features')

    <!-- Categories Section -->
    @include('components.home.categories')

    <!-- How it Works Section -->
    @include('components.home.about-us')

    <!-- CTA Section -->
    @include('components.home.cta')

    <!-- Footer -->
    @include('components.home.footer')

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
    </style>
@endsection