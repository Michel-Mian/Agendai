document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleSidebarButton = document.getElementById('toggle-sidebar');
    const profileButton = document.getElementById('profile-button');
    const profileDropdown = document.getElementById('profile-dropdown');
    const logo = document.getElementById('logo');
    
    // 1. Seleciona o contêiner do conteúdo principal pelo ID
    const mainContent = document.getElementById('main-content'); 

    // 2. Definição das classes de margem
    const EXPANDED_MARGIN = 'ml-63'; // Use ml-64 (Tailwind padrão) para a largura expandida
    const COLLAPSED_MARGIN = 'ml-18'; // Use ml-20 para a largura recolhida (ajuste se necessário)

    // 3. Verifica se os elementos necessários existem
    // Incluímos mainContent na verificação para evitar erros
    if (!sidebar || !toggleSidebarButton || !mainContent) {
        // console.error("Um ou mais elementos essenciais não foram encontrados.");
        return;
    }
    
    // 4. Função centralizada para aplicar os estados (inclui ajuste de margem)
    function updateLayout(isCollapsed) {
        if (isCollapsed) {
            // Estado Recolhido (Collapsed)
            sidebar.classList.add('collapsed');
            if (logo) logo.classList.add('hidden');
            
            mainContent.classList.remove(EXPANDED_MARGIN);
            mainContent.classList.add(COLLAPSED_MARGIN);
            
            localStorage.setItem('sidebar-collapsed', true);
        } else {
            // Estado Expandido (Expanded)
            sidebar.classList.remove('collapsed');
            if (logo) logo.classList.remove('hidden');
            
            mainContent.classList.remove(COLLAPSED_MARGIN);
            mainContent.classList.add(EXPANDED_MARGIN);
            
            localStorage.setItem('sidebar-collapsed', false);
        }
    }


    toggleSidebarButton.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.contains('collapsed');
        updateLayout(!isCollapsed);
    });

    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function (e) {
            e.stopPropagation();
            
            if (sidebar.classList.contains('collapsed')) {
                updateLayout(false); // Abre a sidebar antes de mostrar o dropdown
                
                setTimeout(function() {
                    profileDropdown.classList.toggle('hidden');
                }, 400); 
            } else {
                profileDropdown.classList.toggle('hidden');
            }
        });
    }

    document.addEventListener('click', function () {
        if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
            profileDropdown.classList.add('hidden');
        }
    });

    if (profileDropdown) {
        profileDropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    // 5. Restaurar estado salvo (AGORA USANDO A FUNÇÃO)
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    updateLayout(isCollapsed);
});