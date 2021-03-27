$(function () {
    window.onload = function () {
        if ($(window).width() < 991 && currentSlideNum > 1) {
            $('html, body').scrollTop($('[data-page-num=' + currentSlideNum + ']').offset().top - 50);
        }
    };

    $('.lookbook-desktop').on('afterChange', function (e, slider, currentSlide) {
        let currentUrl = $(location).attr('href').split('/');
        currentUrl[currentUrl.length - 2] = (currentSlide + 2) / 2;
        history.replaceState({}, '', currentUrl.join('/'));
    }).on('beforeChange', function (e, slider, currentSlide, nextSlide) {
        if ($(window).width() > 991) {
            $.ajax({
                type: 'POST',
                dataType: "json",
                data: {
                    num: (nextSlide + 2) / 2
                },
                success: function (data) {
                    $('[data-slide-text-num=' + nextSlide + ']').text(data.text);
                    $('[data-slide-title-num=' + nextSlide + ']').text(data.seo['ELEMENT_PAGE_TITLE']);
                },
                error: function (jqXHR, exception) {
                    ajaxError(jqXHR, exception);
                },
            });
        }
    }).slick({
        initialSlide: currentSlideNum,
        prevArrow: '.slider-prev',
        nextArrow: '.slider-next',
        slidesToShow: 2,
        slidesToScroll: 2,
        infinite: true,
        arrows: true,
        asNavFor: '.js-add-slider',
        responsive: [{
            breakpoint: 991,
            settings: {
                slidesToShow: countSlide,
                slidesToScroll: 2,
                vertical: true,
                arrows: false
            }
        }],
    });

    $('.lookbook-nav').slick({
        initialSlide: currentSlideNum,
        infinite: true,
        slidesToShow: 12,
        slidesToScroll: 2,
        asNavFor: '.js-add-slider',
        dots: false,
        focusOnSelect: true,
        arrows: true,
    });
    $('.lookbook-text').slick({
        infinite: true,
        initialSlide: currentSlideNum,
        slidesToShow: 2,
        slidesToScroll: 2,
        asNavFor: '.js-add-slider',
        arrows: false,
        draggable: false,
    });
    $('.lookbook-title').slick({
        infinite: true,
        initialSlide: currentSlideNum,
        slidesToShow: 2,
        slidesToScroll: 2,
        asNavFor: '.js-add-slider',
        arrows: false,
        draggable: false,
        responsive: [{
            breakpoint: 991,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            }
        }],
    });

    function isShown(target) {
        let wt = $(window).scrollTop();
        let wh = $(window).height();
        let eh = $(target).outerHeight();
        let et = $(target).offset().top;
        return wt + wh / 2 + 50 >= et && wt + wh / 2 + 50 <= et + eh;
    }

    let blockFindCurrentPage = false;

    $(window).on("scroll", function () {
        if (!blockFindCurrentPage && $(window).width() < 991) {
            setTimeout(function () {
                $('[data-page-num]').each(function () {
                    let pageNum = $(this).data('page-num');
                    if (isShown('[data-page-num=' + pageNum + ']')) {
                        let currentUrl = $(location).attr('href').split('/');
                        pageNum = Math.floor(pageNum / 2) + 1;
                        if (pageNum !== currentUrl[currentUrl.length - 2]) {
                            currentUrl[currentUrl.length - 2] = pageNum;
                            history.replaceState({}, '', currentUrl.join('/'));
                        }
                        return false;
                    }
                });
                blockFindCurrentPage = false;
            }, 1000);
            blockFindCurrentPage = true;
        }
    });
});
