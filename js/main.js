// main.js - Initialisation générale, menu mobile, thème sombre, micro-animations, scroll

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================
    // 1. BURGER MENU (Mobile Navigation)
    // ==========================================================
    const burgerBtn = document.getElementById('burgerToggle');
    const navMenu   = document.getElementById('navMenu');

    if (burgerBtn && navMenu) {
        burgerBtn.addEventListener('click', function () {
            const isOpen = navMenu.classList.toggle('nav-open');
            burgerBtn.innerHTML = isOpen
                ? '<i class="fa-solid fa-xmark"></i>'
                : '<i class="fa-solid fa-bars"></i>';
            burgerBtn.setAttribute('aria-expanded', isOpen);
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function (e) {
            if (navMenu.classList.contains('nav-open') &&
                !navMenu.contains(e.target) &&
                !burgerBtn.contains(e.target)) {
                navMenu.classList.remove('nav-open');
                burgerBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
                burgerBtn.setAttribute('aria-expanded', false);
            }
        });

        // Fermer le menu au clic sur un lien
        navMenu.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('nav-open');
                burgerBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
            });
        });
    }

    // ==========================================================
    // 2. SCROLL HEADER EFFECT — rétrécissement au scroll
    // ==========================================================
    const mainHeader = document.querySelector('.main-header');
    if (mainHeader) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 60) {
                mainHeader.classList.add('header-scrolled');
            } else {
                mainHeader.classList.remove('header-scrolled');
            }
        }, { passive: true });
    }

    // ==========================================================
    // 3. TOGGLE THÈME SOMBRE (persistance en localStorage)
    // ==========================================================
    const themeToggle = document.getElementById('themeToggle');
    const htmlEl      = document.documentElement;

    // Récupérer le thème sauvegardé
    const savedTheme = localStorage.getItem('airmaken_theme') || 'light';
    htmlEl.setAttribute('data-theme', savedTheme);
    if (themeToggle) {
        themeToggle.innerHTML = savedTheme === 'dark'
            ? '<i class="fa-solid fa-sun"></i>'
            : '<i class="fa-solid fa-moon"></i>';

        themeToggle.addEventListener('click', function () {
            const current = htmlEl.getAttribute('data-theme');
            const next    = current === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-theme', next);
            localStorage.setItem('airmaken_theme', next);
            themeToggle.innerHTML = next === 'dark'
                ? '<i class="fa-solid fa-sun"></i>'
                : '<i class="fa-solid fa-moon"></i>';
        });
    }

    // ==========================================================
    // 4. MICRO-ANIMATIONS AU SCROLL — Intersection Observer
    // ==========================================================
    const animatedEls = document.querySelectorAll('.card, .stat-card, .service-card, .reassurance-card, .advantage-item, .mv-card');

    if (animatedEls.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        animatedEls.forEach(function (el) {
            el.classList.add('animate-on-scroll');
            observer.observe(el);
        });
    }

    // ==========================================================
    // 5. AUTO-DISMISS DES ALERTES FLASH (après 5 secondes)
    // ==========================================================
    const flashAlerts = document.querySelectorAll('.alert');
    flashAlerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease, max-height 0.5s ease';
            alert.style.opacity = '0';
            alert.style.maxHeight = '0';
            alert.style.overflow = 'hidden';
            alert.style.padding = '0';
            alert.style.margin = '0';
        }, 5000);
    });

    // ==========================================================
    // 6. SIDEBAR ADMIN MOBILE (bouton hamburger admin)
    // ==========================================================
    const adminSidebarToggle = document.getElementById('adminSidebarToggle');
    const adminSidebar       = document.querySelector('.admin-sidebar');

    if (adminSidebarToggle && adminSidebar) {
        adminSidebarToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('sidebar-open');
        });

        // Fermer la sidebar en cliquant en dehors
        document.addEventListener('click', function (e) {
            if (adminSidebar.classList.contains('sidebar-open') &&
                !adminSidebar.contains(e.target) &&
                !adminSidebarToggle.contains(e.target)) {
                adminSidebar.classList.remove('sidebar-open');
            }
        });
    }

    // ==========================================================
    // 7. BOUTONS RETOUR EN HAUT (scroll to top)
    // ==========================================================
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }, { passive: true });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ==========================================================
    // 8. ACTIVE NAV LINK — marquer le lien actif selon l'URL
    // ==========================================================
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });

});
