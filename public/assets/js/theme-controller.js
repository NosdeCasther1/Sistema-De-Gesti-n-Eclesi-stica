/**
 * Theme Controller
 * Handles theme switching, system preferences, and persistence.
 */

class ThemeController {
    constructor() {
        this.themeKey = 'preferred-theme';
        this.htmlTag = document.documentElement;
        console.log('ThemeController: Initializing...');
        this.init();
    }

    init() {
        // 1. Check for saved preference
        const savedTheme = localStorage.getItem(this.themeKey);
        
        // 2. Check for system preference
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme) {
            console.log('ThemeController: Using saved theme:', savedTheme);
            this.setTheme(savedTheme);
        } else if (systemPrefersDark) {
            console.log('ThemeController: Using system dark preference');
            this.setTheme('dark');
        } else {
            console.log('ThemeController: Defaulting to light theme');
            this.setTheme('light');
        }

        // Listen for system changes if no manual preference is set
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem(this.themeKey)) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    setTheme(theme) {
        console.log('ThemeController: Setting theme to', theme);
        this.htmlTag.setAttribute('data-theme', theme);
        
        // Force redraw to ensure variables are applied
        this.htmlTag.style.display = 'none';
        this.htmlTag.offsetHeight; 
        this.htmlTag.style.display = '';
        
        this.updateIcons(theme);

        // Dispatch global event for other components (like Chart.js)
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    toggleTheme() {
        const currentTheme = this.htmlTag.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        console.log('ThemeController: Toggling from', currentTheme, 'to', newTheme);
        this.setTheme(newTheme);
        localStorage.setItem(this.themeKey, newTheme);
    }

    updateIcons(theme) {
        const moonIcon = document.querySelector('.theme-icon-moon');
        const sunIcon = document.querySelector('.theme-icon-sun');
        
        if (moonIcon && sunIcon) {
            if (theme === 'dark') {
                moonIcon.classList.add('d-none');
                sunIcon.classList.remove('d-none');
            } else {
                sunIcon.classList.add('d-none');
                moonIcon.classList.remove('d-none');
            }
        }
    }
}

/**
 * Initialization and Global Event Listeners
 */

// Initialize Helper
const initThemeController = () => {
    if (!window.themeController) {
        window.themeController = new ThemeController();
    }
};

// Start logic immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeController);
} else {
    initThemeController();
}

// Global Theme Toggle Listener (Ultra-Resilient)
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('.theme-toggle');
    if (toggle) {
        e.preventDefault();
        console.log('Theme Toggle Clicked');
        if (window.themeController) {
            window.themeController.toggleTheme();
        } else {
            // Fallback if controller failed to init
            const html = document.documentElement;
            const current = html.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('preferred-theme', next);
            location.reload(); // Hard fix if all else fails
        }
    }
});

/**
 * Mobile Sidebar Touch Controls (Swipe to Close)
 */
let touchstartX = 0;
let touchendX = 0;

document.addEventListener('touchstart', e => {
    touchstartX = e.changedTouches[0].screenX;
}, { passive: true });

document.addEventListener('touchend', e => {
    touchendX = e.changedTouches[0].screenX;
    
    const sidebar = document.querySelector('.sidebar');
    if (window.innerWidth < 991 && (touchstartX - touchendX) > 50) {
        if (sidebar && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) overlay.classList.add('d-none');
            document.body.style.overflow = '';
        }
    }
}, { passive: true });

/**
 * UI Helpers: Skeletons & CSRF
 */
window.mostrarSkeleton = function (contenedorId, count = 3) {
    const container = document.getElementById(contenedorId);
    if (!container) return;
    const skeletonHTML = `
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm skeleton-card">
                <div class="skeleton-box skeleton-avatar"></div>
                <div class="skeleton-box skeleton-title"></div>
                <div class="skeleton-box skeleton-text"></div>
                <div class="skeleton-box skeleton-text" style="width: 50%"></div>
            </div>
        </div>
    `.repeat(count);
    container.innerHTML = skeletonHTML;
};

window.ocultarSkeleton = function (contenedorId) {
    const container = document.getElementById(contenedorId);
    if (container) container.innerHTML = '';
};

// CSRF Fetch Interceptor
(function () {
    const originalFetch = window.fetch;
    window.fetch = async (...args) => {
        let [resource, config] = args;
        config = config || {};
        config.headers = config.headers || {};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            if (config.headers instanceof Headers) {
                config.headers.set('X-CSRF-TOKEN', csrfToken);
                config.headers.set('X-Requested-With', 'XMLHttpRequest');
            } else {
                config.headers['X-CSRF-TOKEN'] = csrfToken;
                config.headers['X-Requested-With'] = 'XMLHttpRequest';
            }
        }
        return originalFetch(resource, config);
    };
})();
