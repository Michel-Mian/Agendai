<div class="space-y-8">
    @include('components/myTrips/screenSections/themes/statisticSection')

    <!-- Seção de Previsão do Tempo -->
    @include('components/myTrips/screenSections/themes/wetherSection')

    <!-- Seção de Notícias da Região -->
    @include('components/myTrips/screenSections/themes/newsSection')
</div>

<style>
    /* Animações suaves para hover */
    .hover\:shadow-md:hover {
        transition: box-shadow 0.3s ease-in-out;
    }
    
    /* Gradientes personalizados */
    .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }
    
    /* Efeitos de transição */
    .transition-shadow {
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .transition-colors {
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
</style>
