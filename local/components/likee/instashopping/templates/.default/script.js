(function($) {
    $(function() {
        $('.lis').on('click', '.lis__media', function() {
            var $media = $(this);
            var html = '<div class="lis-full container">'
                + '<div class="column-5 column-md-2">'
                + '<div class="lis-full__img" style="background-image:url('+$media.data('img')+')"></div>'
                + '</div>'
                + '<div class="column-5 column-md-2">' + $media.find('.lis__media-full').html() + '</div>'
                + '</div>';

            Popup.show(html);
            sendYandexMetrkiaGoal('instashopping_popup');
        });
        $('.js-lis-section').on('click', ' .js-load-more-btn', function (e) {
            e.preventDefault();

            var btn = $(this);
            var section = $('.js-lis-section');

            btn.addClass('preloader');

            BX.ajax.get(btn.attr('href'), {'load_more': 'Y'}, function (html) {
                section.find('.js-show-more-box').remove();
                section.append(html);
            });
        });
        $(document).on('click', '.lis-full__button', function() {
            sendYandexMetrkiaGoal('instashopping2product');
        });
    });
})(jQuery);