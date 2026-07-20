(function () {
    var SELECTOR = 'h2, .section-head, .j-feat, .frag-note, .howto-step, .line-it, .b2b-li, .b2b-stat';

    function revealAll() {
        document.querySelectorAll(SELECTOR).forEach(function (el) {
            el.classList.add('in');
        });
    }

    function init() {
        if (!('IntersectionObserver' in window)) {
            revealAll();
            return;
        }
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            revealAll();
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

        document.querySelectorAll(SELECTOR).forEach(function (el) {
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
