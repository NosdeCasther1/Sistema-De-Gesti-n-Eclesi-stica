document.addEventListener('DOMContentLoaded', function () {
    // Toggle Sidebar
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.hoe-sidebar');

    sidebarToggle?.addEventListener('click', function () {
        sidebar.classList.toggle('show');
    });

    // Active Link Handling
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    // Submenu Handling
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const submenuId = this.getAttribute('data-bs-target');
            const submenu = document.querySelector(submenuId);

            // Close other submenus
            document.querySelectorAll('.submenu.show').forEach(menu => {
                if (menu !== submenu) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                }
            });

            // Toggle current submenu
            submenu.classList.toggle('show');
            this.setAttribute('aria-expanded', submenu.classList.contains('show'));
        });
    });

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function (event) {
        if (window.innerWidth <= 768 &&
            !sidebar.contains(event.target) &&
            !event.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('show');
        }
    });
});