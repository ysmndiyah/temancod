// main.js - Updated for dashboard UI

// Ensure DOM is fully loaded before attaching listeners
document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu'); // legacy menu (if present)
    const sidebar = document.querySelector('.sidebar');
    const avatarWrapper = document.querySelector('.avatar-wrapper');
    const dropdown = document.getElementById('topDropdown');

    // ------------------------------------------------
    // 1. Mobile menu (legacy public pages) – hamburger toggles #mobileMenu
    // ------------------------------------------------
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }

    // ------------------------------------------------
    // 2. Dashboard sidebar toggle (topbar hamburger → .sidebar)
    // ------------------------------------------------
    if (hamburger && sidebar) {
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('active') &&
                !sidebar.contains(e.target) &&
                !hamburger.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }

    // ------------------------------------------------
    // 3. User avatar dropdown in top bar
    // ------------------------------------------------
    if (avatarWrapper && dropdown) {
        avatarWrapper.addEventListener('click', (e) => {
            e.stopPropagation(); // prevent body click from closing immediately
            const isVisible = dropdown.style.display === 'flex';
            dropdown.style.display = isVisible ? 'none' : 'flex';
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdown.style.display = 'none';
        });
    }

    // ------------------------------------------------
    // 4. Auto‑dismiss alerts after a short delay
    // ------------------------------------------------
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // ------------------------------------------------
    // 5. IntersectionObserver for scroll animations
    // ------------------------------------------------
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.step-card, .companion-card, .testi-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

    // ------------------------------------------------
    // 6. Confirmation dialogs for actions with data-confirm attribute
    // ------------------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm(btn.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});