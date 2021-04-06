$(function () {
    function init() {
        $('.products-grid').each(function () {
            var grid = $(this);

            $('.js-add-to-favorites', grid).off('click').on('click', function (e) {
                e.preventDefault();
                LikeeAjax.btnClick($(this));
            });

            $('.js-add-to-basket', grid).off('click').on('click', function (e) {
                e.preventDefault();

                var btn = $(this);

                BX.ajax.get(btn.attr('href'), {action: 'get_buy_modal'}, function (response) {
                    var form = $(response);

                    Popup.show(form, {
                        className: 'popup--size',
                        title: 'Выберите размер',
                        onShow: function () {
                            CountInput.init();

                            form.on('submit', function (e) {
                                e.preventDefault();

                                if (form.find('input:checked').length == 0)
                                    return;

                                var data = form.serialize();

                                BX.ajax.loadJSON(document.location.pathname + '?' + data, function (response) {
                                    if (response.STATUS && response.STATUS == 'OK') {
                                        btn.addClass('shortcut--active');
                                    } else {
                                        btn.removeClass('shortcut--active');
                                    }

                                    $(document).trigger('update-basket-small', response);
                                    Popup.hide();
                                });
                            });
                        }
                    });
                });
            });
        });

        $('.js-catalog-section .js-load-more-btn').on('click', function (e) {
            e.preventDefault();

            var btn = $(this);

            $(".load-more-btn-main").hide();
            $(".load-more-btn-loader").addClass("load-more-btn-loader-visible");

            BX.ajax.get(btn.attr('href'), {'load_more': 'Y'}, function (html) {
                var section = $('.js-catalog-section');
                section.find('.js-show-more-box').remove();
                section.append(html);
                init();
                $(".load-more-btn-main").show();
                $(".load-more-btn-loader").removeClass("load-more-btn-loader-visible");

            });
        });
         $('.js-catalog-section .js-paginate').on('change', function (e) {
            var url = $(this).val();

            history.replaceState( {} , '', url);
            location.reload();
        });

        $('.js-catalog-section').removeClass('catalog-preloader');
    }

    init();
    $(document).on('catalog-init', init);
});