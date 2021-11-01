$(function () {
    $(document).on('click', '.js-animate-scroll', function (event) {
        event.preventDefault();

        var btn = $(this),
            el_animate_to = $(btn.attr('href')),
            speed = btn.data('animate-speed') || 200;

        if (el_animate_to.length > 0) {
            $(document.body).add(document.documentElement).animate({
                scrollTop: el_animate_to.offset().top
            }, speed);
        }
    });
});