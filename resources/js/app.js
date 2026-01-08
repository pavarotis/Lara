// Base JavaScript - Conditional Alpine.js loading
// Only load Alpine if needed (zero-JS default policy)

// Check if Alpine is needed (elements with x-data attribute)
if (document.querySelector('[x-data]')) {
    import('alpinejs').then(Alpine => {
        window.Alpine = Alpine.default;
        Alpine.default.start();
    });
}

// Base functionality (no Alpine dependency)
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle (if not using Alpine)
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
