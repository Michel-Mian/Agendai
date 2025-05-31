// const sidebar = document.getElementById('sidebar');
//     const toggleSidebarButton = document.getElementById('toggleSidebar');
 
//     function toggleSidebar() {
//         sidebar.classList.toggle('sidebar-expanded');
//         sidebar.classList.toggle('sidebar-collapsed');
//         const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
//         localStorage.setItem('sidebar-collapsed', isCollapsed);
//     }
 
//     toggleSidebarButton.addEventListener('click', toggleSidebar);
 
//     document.addEventListener('DOMContentLoaded', function () {
//         const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
//         if (isCollapsed) {
//             sidebar.classList.add('sidebar-collapsed');
//             sidebar.classList.remove('sidebar-expanded');
//         } else {
//             sidebar.classList.add('sidebar-expanded');
//             sidebar.classList.remove('sidebar-collapsed');
//         }
//     });

        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const profileButton = document.getElementById('profile-button');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        let isCollapsed = false;
        let isMobileOpen = false;

        // Toggle sidebar collapse (desktop)
        toggleButton.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            
            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                
                // Hide text elements
                document.querySelectorAll('.nav-text, #logo-text, .profile-info').forEach(el => {
                    el.classList.add('hidden');
                });
            } else {
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                
                // Show text elements
                document.querySelectorAll('.nav-text, #logo-text, .profile-info').forEach(el => {
                    el.classList.remove('hidden');
                });
            }
        });

        // Mobile menu toggle
        mobileMenuButton.addEventListener('click', () => {
            isMobileOpen = !isMobileOpen;
            
            if (isMobileOpen) {
                sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-50');
                sidebar.classList.remove('hidden');
                mobileOverlay.classList.remove('hidden');
            } else {
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50');
                mobileOverlay.classList.add('hidden');
            }
        });

        // Close mobile menu when clicking overlay
        mobileOverlay.addEventListener('click', () => {
            isMobileOpen = false;
            sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50');
            mobileOverlay.classList.add('hidden');
        });

        // Profile dropdown toggle
        profileButton.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth >= 1024) {
                // Desktop
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50', 'hidden');
                mobileOverlay.classList.add('hidden');
                isMobileOpen = false;
            } else {
                // Mobile
                if (!isMobileOpen) {
                    sidebar.classList.add('hidden');
                }
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call

        // Só adiciona evento se o elemento existir
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                sidebar.classList.toggle('w-16');
                sidebar.classList.toggle('w-64');
                document.querySelectorAll('.nav-text, #logo-text, .profile-info').forEach(el => {
                    el.classList.toggle('hidden');
                });
                // Se quiser ocultar a seta do perfil também:
                // document.querySelector('.profile-chevron').classList.toggle('hidden');
            });
        }

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('toggle-sidebar');
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');

            let isCollapsed = false;

            // Toggle sidebar collapse (desktop)
            if (toggleButton) {
                toggleButton.addEventListener('click', () => {
                    isCollapsed = !isCollapsed;
                    if (isCollapsed) {
                        sidebar.classList.remove('w-64');
                        sidebar.classList.add('w-16');
                        document.querySelectorAll('.nav-text, #logo-text, .profile-info').forEach(el => {
                            el.classList.add('hidden');
                        });
                    } else {
                        sidebar.classList.remove('w-16');
                        sidebar.classList.add('w-64');
                        document.querySelectorAll('.nav-text, #logo-text, .profile-info').forEach(el => {
                            el.classList.remove('hidden');
                        });
                    }
                });
            }

            // Profile dropdown toggle
            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
        });