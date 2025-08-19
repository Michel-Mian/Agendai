document.addEventListener('DOMContentLoaded', function () {
    console.log('Sidebar JS carregado');

    const sidebar = document.getElementById('sidebar');
    const toggleSidebarButton = document.getElementById('toggle-sidebar');
    const profileButton = document.getElementById('profile-button');
    const profileDropdown = document.getElementById('profile-dropdown');
    const logo = document.getElementById('logo');

    toggleSidebarButton.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.contains('collapsed');
        if (isCollapsed) {
            // Abrindo: mostra o logo
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('body-sidebar-collapsed');
            if (logo) logo.classList.remove('hidden');
            localStorage.setItem('sidebar-collapsed', false);
        } else {
            // Fechando: esconde o logo
            sidebar.classList.add('collapsed');
            document.body.classList.add('body-sidebar-collapsed');
            if (logo) logo.classList.add('hidden');
            localStorage.setItem('sidebar-collapsed', true);
        }
    });

    profileButton.addEventListener('click', function (e) {
        e.stopPropagation();
        // Se a sidebar estiver fechada, abra antes de mostrar o dropdown
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('body-sidebar-collapsed');
            if (logo) logo.classList.remove('hidden');
            localStorage.setItem('sidebar-collapsed', false);
            // Aguarda animação antes de mostrar o dropdown 
            setTimeout(function() {
                profileDropdown.classList.toggle('hidden');
            }, 400); // tempo igual ao transition do CSS
        } else {
            profileDropdown.classList.toggle('hidden');
        }
    });

    document.addEventListener('click', function () {
        if (!profileDropdown.classList.contains('hidden')) {
            profileDropdown.classList.add('hidden');
        }
    });

    profileDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Restaurar estado salvo
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        document.body.classList.add('body-sidebar-collapsed');
        if (logo) logo.classList.add('hidden');
    } else {
        sidebar.classList.remove('collapsed');
        document.body.classList.remove('body-sidebar-collapsed');
        if (logo) logo.classList.remove('hidden');
    }
});
