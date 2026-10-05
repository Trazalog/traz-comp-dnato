(function () {
    'use strict';

    /**
     * Password strength widget.
     *
     * Como usarlo:
     *   <input type="password" class="form-control js-password-strength"
     *          name="password" id="password"
     *          autocomplete="new-password" minlength="10" required
     *          data-ps-confirm-target="passconf">
     *   <input type="password" class="form-control js-password-confirm"
     *          name="passconf" id="passconf"
     *          autocomplete="new-password" minlength="10" required>
     *
     * Reglas: las mismas que valida el backend (mantener sincronizado con
     * application/libraries/MY_Form_validation->password_strong).
     */

    var RULES = [
        {
            id: 'length',
            label: 'Al menos 10 caracteres',
            test: function (s) { return s.length >= 10; }
        },
        {
            id: 'upper',
            label: 'Al menos una letra mayuscula (A-Z)',
            test: function (s) { return /[A-Z]/.test(s); }
        },
        {
            id: 'lower',
            label: 'Al menos una letra minuscula (a-z)',
            test: function (s) { return /[a-z]/.test(s); }
        },
        {
            id: 'digit',
            label: 'Al menos un numero (0-9)',
            test: function (s) { return /\d/.test(s); }
        },
        {
            id: 'symbol',
            label: 'Al menos un simbolo (@ # $ % & ! ? . , - _ + =)',
            test: function (s) { return /[^A-Za-z0-9]/.test(s); }
        }
    ];

    function createElement(tag, className, html) {
        var el = document.createElement(tag);
        if (className) el.className = className;
        if (html !== undefined) el.innerHTML = html;
        return el;
    }

    function findClosestForm(el) {
        if (el.closest) return el.closest('form');
        while (el) {
            if (el.tagName && el.tagName.toLowerCase() === 'form') return el;
            el = el.parentNode;
        }
        return null;
    }

    function attach(input) {
        if (input.getAttribute('data-ps-attached') === '1') return;
        input.setAttribute('data-ps-attached', '1');

        if (!input.getAttribute('autocomplete')) {
            input.setAttribute('autocomplete', 'new-password');
        }
        if (!input.getAttribute('minlength')) {
            input.setAttribute('minlength', '10');
        }
        if (!input.hasAttribute('required')) {
            input.setAttribute('required', 'required');
        }

        var confirmTarget = input.getAttribute('data-ps-confirm-target');
        var confirmInput = confirmTarget ? document.getElementById(confirmTarget) : null;
        if (confirmInput && !confirmInput.getAttribute('autocomplete')) {
            confirmInput.setAttribute('autocomplete', 'new-password');
        }
        if (confirmInput && !confirmInput.getAttribute('minlength')) {
            confirmInput.setAttribute('minlength', '10');
        }

        var form = findClosestForm(input);

        var inputWrap = createElement('div', 'ps-input-wrap');
        input.parentNode.insertBefore(inputWrap, input);
        inputWrap.appendChild(input);

        var toggle = createElement('button', 'ps-toggle');
        toggle.type = 'button';
        toggle.setAttribute('aria-label', 'Mostrar u ocultar contrasena');
        toggle.setAttribute('tabindex', '-1');
        toggle.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
        toggle.addEventListener('click', function () {
            var nextType = input.type === 'password' ? 'text' : 'password';
            input.type = nextType;
            if (confirmInput) confirmInput.type = nextType;
            toggle.innerHTML = nextType === 'password'
                ? '<i class="fa fa-eye" aria-hidden="true"></i>'
                : '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        });
        inputWrap.appendChild(toggle);

        var feedback = createElement('div', 'ps-feedback');

        var bar = createElement('div', 'ps-bar');
        var fill = createElement('span', 'ps-bar-fill');
        bar.appendChild(fill);
        feedback.appendChild(bar);

        var label = createElement('div', 'ps-bar-label',
            'Fortaleza: <span class="ps-bar-text">--</span>');
        feedback.appendChild(label);

        var ul = createElement('ul', 'ps-checklist');
        RULES.forEach(function (r) {
            var li = createElement('li', 'ps-rule');
            li.setAttribute('data-rule', r.id);
            li.innerHTML = '<span class="ps-rule-icon" aria-hidden="true"></span>' +
                '<span class="ps-rule-label">' + r.label + '</span>';
            ul.appendChild(li);
        });
        if (confirmInput) {
            var liMatch = createElement('li', 'ps-rule');
            liMatch.setAttribute('data-rule', 'match');
            liMatch.innerHTML = '<span class="ps-rule-icon" aria-hidden="true"></span>' +
                '<span class="ps-rule-label">Coincide con la confirmacion</span>';
            ul.appendChild(liMatch);
        }
        feedback.appendChild(ul);

        if (inputWrap.nextSibling) {
            inputWrap.parentNode.insertBefore(feedback, inputWrap.nextSibling);
        } else {
            inputWrap.parentNode.appendChild(feedback);
        }

        function evaluate() {
            var v = input.value || '';
            var passed = 0;

            RULES.forEach(function (r) {
                var ok = r.test(v);
                var li = ul.querySelector('li[data-rule="' + r.id + '"]');
                if (li) li.classList.toggle('ps-ok', ok);
                if (ok) passed++;
            });

            var totalRules = RULES.length;
            if (confirmInput) {
                var matchOk = v.length > 0 && v === (confirmInput.value || '');
                var liMatch = ul.querySelector('li[data-rule="match"]');
                if (liMatch) liMatch.classList.toggle('ps-ok', matchOk);
                if (matchOk) passed++;
                totalRules += 1;
            }

            var pct = Math.round((passed / totalRules) * 100);
            fill.style.width = pct + '%';

            bar.classList.remove('ps-bar-weak', 'ps-bar-medium', 'ps-bar-strong');
            var levelText;
            if (pct >= 100) {
                bar.classList.add('ps-bar-strong');
                levelText = 'Fuerte';
            } else if (pct >= 60) {
                bar.classList.add('ps-bar-medium');
                levelText = 'Media';
            } else {
                bar.classList.add('ps-bar-weak');
                levelText = pct === 0 ? '--' : 'Debil';
            }
            var labelText = label.querySelector('.ps-bar-text');
            if (labelText) labelText.textContent = levelText;

            input.setAttribute('data-ps-valid', passed === totalRules ? '1' : '0');

            if (form) {
                var allValid = true;
                var groupInputs = form.querySelectorAll('input.js-password-strength');
                for (var i = 0; i < groupInputs.length; i++) {
                    if (groupInputs[i].getAttribute('data-ps-valid') !== '1') {
                        allValid = false;
                        break;
                    }
                }
                var btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                for (var j = 0; j < btns.length; j++) {
                    btns[j].disabled = !allValid;
                    btns[j].classList.toggle('ps-disabled', !allValid);
                }
            }
        }

        input.addEventListener('input', evaluate);
        if (confirmInput) confirmInput.addEventListener('input', evaluate);

        if (form) {
            form.addEventListener('submit', function (e) {
                evaluate();
                if (input.getAttribute('data-ps-valid') !== '1') {
                    e.preventDefault();
                    input.focus();
                    var msg = 'La contrasena no cumple los requisitos minimos. ' +
                        'Revisa la lista de requisitos en pantalla.';
                    if (window.alert) window.alert(msg);
                }
            });
        }

        evaluate();
    }

    function init() {
        var inputs = document.querySelectorAll('input.js-password-strength');
        for (var i = 0; i < inputs.length; i++) attach(inputs[i]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
