// Custom form validation — replaces native browser bubbles with inline messages.
(function () {
    function messageFor(el) {
        var v = el.validity;
        if (v.valueMissing) {
            if (el.type === 'checkbox') return 'Toto pole je potrebné potvrdiť.';
            if (el.type === 'radio') return 'Vyberte jednu z možností.';
            if (el.tagName === 'SELECT') return 'Vyberte jednu z možností.';
            return 'Toto pole je povinné.';
        }
        if (v.typeMismatch && el.type === 'email') return 'Zadajte platnú e-mailovú adresu.';
        if (v.tooShort) return 'Zadajte aspoň ' + el.minLength + ' znakov.';
        if (v.rangeUnderflow) return 'Minimálna hodnota je ' + el.min + '.';
        if (v.rangeOverflow) return 'Maximálna hodnota je ' + el.max + '.';
        if (v.patternMismatch || v.typeMismatch) return 'Zadajte hodnotu v správnom tvare.';
        return 'Skontrolujte toto pole.';
    }

    function anchorFor(el) {
        // Radios/checkboxes sit next to their label — attach the message after the label instead.
        if ((el.type === 'radio' || el.type === 'checkbox') && el.nextElementSibling && el.nextElementSibling.tagName === 'LABEL') {
            return el.nextElementSibling;
        }
        return el;
    }

    function clearError(el) {
        el.classList.remove('is-invalid');
        var next = anchorFor(el).nextElementSibling;
        if (next && next.classList.contains('field-err')) next.remove();
    }

    function showError(el) {
        clearError(el);
        el.classList.add('is-invalid');
        var err = document.createElement('small');
        err.className = 'field-err';
        err.textContent = messageFor(el);
        anchorFor(el).insertAdjacentElement('afterend', err);
    }

    document.querySelectorAll('form').forEach(function (form) {
        if (form.hasAttribute('data-native-validate')) return;
        form.setAttribute('novalidate', '');

        form.addEventListener('submit', function (e) {
            form.querySelectorAll('.field-err').forEach(function (n) { n.remove(); });
            form.querySelectorAll('.is-invalid').forEach(function (n) { n.classList.remove('is-invalid'); });

            var seenRadio = {};
            var invalid = Array.prototype.filter.call(form.elements, function (el) {
                if (!el.willValidate || el.checkValidity()) return false;
                if (el.type === 'radio') {
                    if (seenRadio[el.name]) return false;
                    seenRadio[el.name] = true;
                }
                return true;
            });
            if (!invalid.length) return;

            e.preventDefault();
            invalid.forEach(showError);
            invalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            invalid[0].focus({ preventScroll: true });
        });

        ['input', 'change'].forEach(function (evt) {
            form.addEventListener(evt, function (e) {
                var el = e.target;
                if (!el.classList) return;
                // Radio-group errors sit on the group's first invalid element —
                // picking ANY option in the group must clear it.
                if (el.type === 'radio' && el.name) {
                    var marked = form.querySelector('input[type="radio"].is-invalid[name="' + (window.CSS && CSS.escape ? CSS.escape(el.name) : el.name) + '"]');
                    if (marked && marked.checkValidity()) clearError(marked);
                    return;
                }
                if (el.classList.contains('is-invalid') && el.checkValidity()) clearError(el);
            }, true);
        });
    });
})();
