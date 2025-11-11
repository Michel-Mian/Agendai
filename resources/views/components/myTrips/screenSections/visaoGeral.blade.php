<div class="space-y-8">
    <!-- Seção de Objetivos e Viajantes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card de Objetivos -->
        @include('components/myTrips/screenSections/themes/objectiveSection')

        <!-- Card de Viajantes -->
        @include('components/myTrips/screenSections/themes/travelerSection')
    </div>

    <!-- Seção de Voos -->
    @include('components/myTrips/screenSections/themes/flightSection')

    <!-- Seção de Hotéis -->
    @include('components/myTrips/screenSections/themes/hotelSection', ['hotel' => $hotel ?? collect()])

    <!-- Seção de Veículos -->
    @include('components/myTrips/screenSections/themes/vehicleSection', ['veiculos' => $veiculos ?? collect()])

    {{-- REMOVIDO: Seções de clima e notícias (movidas para informacoesEstatisticas) --}}
</div>

<style>
    .aspect-square {
        aspect-ratio: 1 / 1;
    }
    
    @media (max-width: 640px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-3 {
            grid-template-columns: repeat(1, 1fr);
        }
    }
    
    @media (min-width: 640px) and (max-width: 1024px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .group:hover .opacity-0 {
        opacity: 1;
    }
    
    .transition-all {
        transition: all 0.3s ease-in-out;
    }

    .hover\:shadow-md:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }
    
    /* Animação para os cards */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .space-y-3 > * {
        animation: slideIn 0.5s ease-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade existente para viajantes
    const addViajanteEmptyBtn = document.getElementById('open-add-viajante-modal-btn-empty');
    if (addViajanteEmptyBtn) {
        addViajanteEmptyBtn.addEventListener('click', function() {
            document.getElementById('open-add-viajante-modal-btn').click();
        });
    }
});
</script>