$(function () {

    var items = $('.navigation-row .js-has-children');

    items.each(function () {
        var item = $(this),
            item_link = item.find('.js-top-menu-item'),
            child = item.children('.navigation-submenu'),
            timeout;

        item_link.on('click', function (e) {
            if ($(window).width() < 600)
                e.preventDefault();
        });
        item.on('mouseover', function () {
            child.addClass('open');
            if (timeout)
                clearTimeout(timeout);
        });

        item.on('mouseout', function () {
            timeout = setTimeout(function () {
                child.removeClass('open');
            }, 300);
        });

        child.on('mouseover', function () {
            if (timeout)
                clearTimeout(timeout);
        });

        child.on('mouseout', function () {
            timeout = setTimeout(function () {
                child.removeClass('open');
            }, 100);
        });
    });
});