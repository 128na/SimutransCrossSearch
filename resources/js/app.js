try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) { }

function register(selector, event_name, event_category) {
    [...document.querySelectorAll(selector)].map(el => el.addEventListener('click', e => {
        gtag('event', event_name, {
            event_category,
            event_action: 'click',
            event_label: e.currentTarget.href
        })
    }));
}

register('.link-site', 'click site link', 'Site Link');
register('.link-page', 'click page link', 'Page Link');
register('.link-article', 'click article link', 'Article Link');
register('.link-media', 'click media link', 'Media Link');
