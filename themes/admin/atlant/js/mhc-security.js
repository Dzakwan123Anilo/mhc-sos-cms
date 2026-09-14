(function (window, document) {
    'use strict';

    window.MHC = window.MHC || {};
    window.MHC.escapeHtml = function (value) {
        var replacements = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (value == null ? '' : String(value)).replace(/[&<>"']/g, function (character) {
            return replacements[character];
        });
    };

    window.MHC.sameOriginUrl = function (value) {
        if (typeof value !== 'string' || value === '') {
            return null;
        }

        try {
            var target = new URL(value, window.location.href);
            if ((target.protocol !== 'http:' && target.protocol !== 'https:')
                || target.origin !== window.location.origin
                || target.username
                || target.password) {
                return null;
            }
            return target.href;
        } catch (error) {
            return null;
        }
    };

    window.MHC.navigate = function (value) {
        var target = window.MHC.sameOriginUrl(value);
        if (!target) {
            return false;
        }
        window.location.assign(target);
        return true;
    };

    document.addEventListener('click', function (event) {
        var link = event.target.closest ? event.target.closest('a[data-page-offset]') : null;
        if (!link || typeof window.page !== 'function') {
            return;
        }

        event.preventDefault();
        var offset = link.getAttribute('data-page-offset');
        window.page(offset === '0' ? '' : offset);
    });
}(window, document));
