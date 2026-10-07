// DMR — interações do site (leve, sem dependências).
(function () {
    'use strict';

    var header = document.getElementById('siteHeader');
    var nav = document.getElementById('siteNav');
    var toggle = document.getElementById('navToggle');

    // Header muda de estilo ao rolar.
    function onScroll() {
        if (!header) return;
        if (window.scrollY > 40) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Menu mobile.
    if (toggle && nav) {
        function openMenu() {
            nav.classList.add('is-open');
            toggle.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('nav-open');
        }
        function closeMenu() {
            nav.classList.remove('is-open');
            toggle.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-open');
        }

        toggle.addEventListener('click', function () {
            if (nav.classList.contains('is-open')) { closeMenu(); } else { openMenu(); }
        });
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
        // Fecha com Esc.
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) { closeMenu(); }
        });
        // Clique no overlay (fora do drawer) fecha o menu.
        document.addEventListener('click', function (e) {
            if (nav.classList.contains('is-open') &&
                !nav.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                closeMenu();
            }
        });
        // Garante estado limpo ao voltar para desktop.
        window.addEventListener('resize', function () {
            if (window.innerWidth > 720 && nav.classList.contains('is-open')) { closeMenu(); }
        });
    }

    // Animações de entrada ao aparecer no viewport.
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && reveals.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }
})();
