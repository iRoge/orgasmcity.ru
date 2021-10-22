$(document).on('click', '.js-load-more-btn', function (e) {
    e.preventDefault();
    var btn = $(this);

    btn.addClass('preloader');

    BX.ajax.get(btn.attr('href'), {'load_more': 'Y'}, function (html) {
        var section = $('.js-news-section');
        section.find('.js-show-more-box').remove();
        section.find('.show-more-news-area').remove();
        section.append(html);
        section.find('.news-container').removeClass('in-view');
    });
    $('.js-news-section').removeClass('catalog-preloader');
});
