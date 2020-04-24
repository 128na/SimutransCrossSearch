
/*
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) { }
 */

[...document.querySelectorAll('.link-site')].map(el => el.addEventListener('click', e => {
    gtag('event', 'click site link', {
        event_category: 'Site Link',
        event_action: 'click',
        event_label: e.currentTarget.href
    })
}));

[...document.querySelectorAll('.link-page')].map(el => el.addEventListener('click', e => {
    gtag('event', 'click page link', {
        event_category: 'Page Link',
        event_action: 'click',
        event_label: e.currentTarget.href
    })
}));
