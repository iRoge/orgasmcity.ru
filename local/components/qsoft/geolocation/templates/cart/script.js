$(document).ready(function() {
    let background = $('.podlozhka');
    let close = $('.geoposition__close');
    let popup = $('.geoposition');
    let tooltip = $('.tooltip-window');

    $('.cart-city-input').click(function () {
        popup.css('display', 'flex');
        background.css('display', 'block');
        tooltip.css('display', 'none');
        popup.css('top', $('html').scrollTop() + 'px');
    });
    background.click(function () {
        popup.css('top', '');
    });
    close.click(function () {
        popup.css('top', '');
    });
});