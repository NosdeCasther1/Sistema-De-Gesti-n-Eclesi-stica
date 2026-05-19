document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Manejo de submenús
    const submenuToggles = document.querySelectorAll('.has-submenu');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            this.querySelector('.submenu-icon').style.transform =
                this.getAttribute('aria-expanded') === 'true' ? 'rotate(90deg)' : 'rotate(0)';
        });
    });
});