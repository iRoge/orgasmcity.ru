$(document).on('click', '.close-popup-btn', function (e) {
    $('.popup').hide(0);
    $('body').removeClass('with--popup');
});

$(document).on('click', '.contest-rules-popup-open', function (e) {
    e.preventDefault();
    var $container = $($(this).attr('href'));

    if ($container.length) {
        $('.popup').hide(0);
        $('body').removeClass('with--popup');

        Popup.show($container.html(), {
            title: ($(this).attr('title') || ''),
            className: ($(this).attr('data-class') || '')
        });
    }
});