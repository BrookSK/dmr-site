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

    // Conteúdo do site: listas repetíveis (adicionar, remover, reordenar).
    var uidSeq = 0;
    document.querySelectorAll('[data-repeater]').forEach(function (rep) {
        var list = rep.querySelector('[data-rep-list]');
        var tpl = rep.querySelector('[data-rep-template]');
        var addBtn = rep.querySelector('[data-rep-add]');
        var max = parseInt(rep.getAttribute('data-max') || '50', 10);

        function renumber() {
            var items = list.querySelectorAll('[data-rep-item]');
            items.forEach(function (item, i) {
                var n = item.querySelector('[data-rep-n]');
                if (n) { n.textContent = String(i + 1); }
            });
            if (addBtn) { addBtn.hidden = items.length >= max; }
        }

        if (addBtn && tpl) {
            addBtn.addEventListener('click', function () {
                uidSeq += 1;
                var uid = 'n' + Date.now() + uidSeq;
                var wrap = document.createElement('div');
                wrap.innerHTML = tpl.innerHTML.split('__UID__').join(uid);
                var item = wrap.firstElementChild;
                list.appendChild(item);
                renumber();
                var first = item.querySelector('input[type="text"], textarea');
                if (first) { first.focus(); }
            });
        }

        list.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn) { return; }
            var item = btn.closest('[data-rep-item]');
            if (!item) { return; }
            if (btn.hasAttribute('data-rep-remove')) {
                if (window.confirm('Remover este item? A remoção vale quando você clicar em "Salvar seção".')) {
                    item.remove();
                    renumber();
                }
            } else if (btn.hasAttribute('data-rep-up') && item.previousElementSibling) {
                list.insertBefore(item, item.previousElementSibling);
                renumber();
            } else if (btn.hasAttribute('data-rep-down') && item.nextElementSibling) {
                list.insertBefore(item.nextElementSibling, item);
                renumber();
            }
        });

        renumber();
    });

    // Prévia da imagem escolhida antes de salvar.
    document.addEventListener('change', function (e) {
        var input = e.target;
        if (!input.matches || !input.matches('[data-image-input]') || !input.files || !input.files[0]) { return; }
        var preview = input.closest('.img-field').querySelector('[data-preview]');
        if (!preview) { return; }
        var file = input.files[0];
        if (file.size > 8 * 1024 * 1024) {
            window.alert('Esta imagem tem mais de 8 MB. Reduza o tamanho antes de enviar.');
            input.value = '';
            return;
        }
        var img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(file);
        preview.innerHTML = '';
        preview.appendChild(img);
    });

    // Confirmação antes de ações destrutivas (ex.: restaurar padrão).
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) { e.preventDefault(); }
        });
    });

    // Abre a seção indicada no endereço (#sec-...).
    if (location.hash.indexOf('#sec-') === 0) {
        var target = document.getElementById(location.hash.slice(1));
        if (target && target.tagName === 'DETAILS') { target.open = true; }
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
