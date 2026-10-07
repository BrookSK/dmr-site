// Painel administrativo — interações mínimas (menu mobile e abas).
(function () {
    'use strict';

    // Menu lateral no mobile.
    var burger = document.getElementById('adminBurger');
    var sidebar = document.getElementById('adminSidebar');
    if (burger && sidebar) {
        burger.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 720 &&
                sidebar.classList.contains('is-open') &&
                !sidebar.contains(e.target) &&
                e.target !== burger) {
                sidebar.classList.remove('is-open');
            }
        });
    }

    // Abas (configurações).
    var tabsWrap = document.querySelector('[data-tabs]');
    if (tabsWrap) {
        var buttons = tabsWrap.querySelectorAll('[data-tab]');
        var panels = tabsWrap.querySelectorAll('[data-panel]');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-tab');
                buttons.forEach(function (b) { b.classList.remove('is-active'); });
                panels.forEach(function (p) { p.classList.remove('is-active'); });
                btn.classList.add('is-active');
                var panel = tabsWrap.querySelector('[data-panel="' + target + '"]');
                if (panel) { panel.classList.add('is-active'); }
            });
        });
    }
})();
