document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');

    menuToggle.addEventListener('click', () => {
        wrapper.classList.toggle('toggled');
    });
});
