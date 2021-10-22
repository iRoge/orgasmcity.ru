$(function () {
    const MAX_MOBILE_WIDTH = 768;
    const ANIMATION_DURATION = 300;
    const HIDE_SEARCH_BREAK_POINT = 130;

    let $menuBlock = $('.menu');
    let $searchBlock = $('.poisk-div');
    let $searchBlockForm = $('.poisk-div form');

    let offsetTop = $menuBlock.offset().top + 20;
    let searchBlockHeight = $('.poisk-div').height();
    let windowWidth = $(window).width();

    let searchFirstActive = false;

    let scrollTop;

    $(window).on('scroll', function () {
        windowWidth = $(window).width();
        scrollTop = $(window).scrollTop();

        if (windowWidth < MAX_MOBILE_WIDTH) {
            if ($searchBlock.hasClass('is-fixed') && $searchBlock.hasClass('active') && scrollTop <= HIDE_SEARCH_BREAK_POINT) {
                $searchBlock.animate({'top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlock.removeClass('active');
                $('.search-suggest').empty();
                $menuBlock.animate({'top': '0px'}, ANIMATION_DURATION);
            } else if (!$searchBlock.hasClass('is-fixed') && $searchBlock.hasClass('active')) {
                $searchBlock.removeClass('active');
                $('.search-suggest').empty();
                $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
            }

            let menuScrollTop = offsetTop - scrollTop;

            if ($searchBlock.hasClass('active')) {
                menuScrollTop = menuScrollTop + searchBlockHeight;
                searchFirstActive = true;
            } else if (searchFirstActive) {
                menuScrollTop = menuScrollTop - searchBlockHeight;
                searchFirstActive = false;
            }

            if (menuScrollTop <= 0) {
                $menuBlock.addClass('is-fixed');
            } else {
                $menuBlock.removeClass('is-fixed');
            }
        }
    });
    let currentScroll;
    if (windowWidth < MAX_MOBILE_WIDTH) {
        $('.reg, .ent, .mail.obr').on('click', function () {
            currentScroll = scrollTop;
            $(window).scrollTop(0);
        });
        $('.cls-mail-div, .podlozhka').on('click', function () {
            $(window).scrollTop(currentScroll);
        });
    }

    $('.touch-for-poisk').click(function () {
        let isFixed = $menuBlock.hasClass('is-fixed');

        $searchBlock.toggleClass('active');

        if ($searchBlock.hasClass('active')) {
            if (isFixed) {
                $searchBlock.addClass('is-fixed');
                $searchBlock.css({'margin-top': 0});
                $searchBlockForm.css({'margin-top': 0});
                $searchBlock.animate({'top': '0px'}, ANIMATION_DURATION);
                $menuBlock.animate({'top': searchBlockHeight}, ANIMATION_DURATION);
            } else {
                $searchBlock.removeClass('is-fixed');
                $searchBlock.animate({'margin-top': '0px'}, ANIMATION_DURATION);
                $searchBlockForm.animate({'margin-top': '0px'}, ANIMATION_DURATION);
            }
        } else {
            if (isFixed) {
                $searchBlock.addClass('is-fixed');
                $searchBlock.animate({'top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $menuBlock.animate({'top': '0px'}, ANIMATION_DURATION);
            } else {
                $searchBlock.removeClass('is-fixed');
                $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
                $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
            }
        }
    });
});

$(document).ready(function () {
    let getUrlParameter = function getUrlParameter(sParam) {
        let sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
    let showFeedbackForm = getUrlParameter('WEB_FORM_ID') && getUrlParameter('RESULT_ID');
    if (showFeedbackForm) {
        $('.mail-div').toggle(0);
        $('.podlozhka').toggle(0);
        $('.mail-div .popup').show(0);
    }
    $('.lazy-img').lazyLoadXT();

    let regForm = $('#reg-form-popup'),
        authForm = $('#auth-form'),
        regInput = $('#vkl20'),
        authInput = $('#vkl10');

    $('.reg').click(function () {
        regInput.not(':checked').prop("checked", true);
        regForm.show();
        $('.auth-div-full').toggle(0);
        $('.podlozhka').toggle(0);
    });

    $('.auth2').click(function() {
        $('.auth-div').toggle(100);
    });

    $('.ent').click(function () {
        authInput.not(':checked').prop("checked", true);
        authForm.show();
        $('.auth-div-full').toggle(0);
        $('.podlozhka').toggle(0);
    });

    $('.cls-mail-div, .podlozhka').click(function () {
        regForm.hide();
        authForm.hide();
    })

    regInput.click(function () {
        authForm.hide();
        regForm.show();
    });

    authInput.click(function () {
        authForm.show();
        regForm.hide();
    });

    $('.from-ul-li').click(function () {
        $('.from-ul-li-ul').toggle(100);
    });

    $('.auth-div-desk').parent().hover(
        function () {
            $(this).find('.auth-div-personal').toggle(100);
        }, function () {
            $(this).find('.auth-div-personal').toggle(100);
        }
    );

    $('.mail2').click(function () {
        $('.mail-div').toggle(0);
        $('.podlozhka').toggle(0);
        $('.mail-div .popup').show(0);
    });

    $(window).resize(function () {
        $('.hide-menu').css('height', 'auto').each(function () {
            let heightLeftHide = $('.left-hide-menu', this).outerHeight(true);
            let heightRightHide = $('.right-hide-menu', this).outerHeight(true);

            if (heightLeftHide > heightRightHide) {
                $(this).css('height', heightLeftHide);
            } else {
                $(this).css('height', heightRightHide);
            }
        });
    });

    $('.menu-ul-li.js-has-children').hover(function () {
        $('.menu-ul-li-a', this).addClass('active-menu');
        $('.hide-menu').hide().filter($(this).next('.hide-menu')).show();
    }, function () {
        $('.menu-ul-li-a', this).removeClass('active-menu');
    });

    $('.menu').mouseleave(function () {
        $('.hide-menu').hide()
        $('.menu-ul-li-a', this).removeClass('active-menu');
    });

    $('.menu-ul-li:not(.js-has-children)').hover(function () {
        $('.hide-menu').hide()
    });

    $('.hide-menu').hover(function () {
        $(this).show();
        $(this).prev('.menu-ul-li').find('.menu-ul-li-a').toggleClass('active-menu');
    }, function () {
        $(this).hide();
        $(this).prev('.menu-ul-li').find('.menu-ul-li-a').toggleClass('active-menu');
    });

    if ($(window).width() > 767) {
        $('.left-main-two img').css('height', 'auto');
        $('.left-main-two').css('height', 'auto');
        $('.right-main-two').css('height', 'auto');

        let leftb = $('.left-main-two');
        let rightb = $('.right-main-two');
        let imgleft = $('.left-main-two img');

        let hleft = leftb.outerHeight(true);
        let hright = rightb.outerHeight(true);

        if (hleft > hright) {
            rightb.css('height', hleft);
            imgleft.css('height', 'auto');
        } else {
            leftb.css('height', hright);
            imgleft.css('height', hright);
        }
    } else {
        let left_main_two = $('.left-main-two img');
        left_main_two.css('height', 'auto');
        left_main_two.css('width', '100%');
    }

    if ($(window).width() > 991) {
        let leftbb = $('.in-main-top');
        let rightbb = $('.shoes-top');

        let hhleft = leftbb.height();
        let hhright = rightbb.height();

        rightbb.css('height', hhleft);
    }

    $(window).resize(function () {
        if ($(window).width() > 991) {
            let leftbb = $('.in-main-top');
            let rightbb = $('.shoes-top');

            let hhleft = leftbb.height();
            let hhright = rightbb.height();

            rightbb.css('height', hhleft);
        }
    });

    $('.sectionEvent').click(function () {
        let that = $(this);
        that.toggleClass('active-blue');
        that.toggleClass('blue');
        that.find('.arr-up').toggle();
        that.find('.arr-down').toggle();
        that.next('.after-blue').toggle();
    })

    $('.hide-filter').click(function () {
        event.preventDefault();
        $(this).toggle();
        $('.show-filter').toggle();
        $('.in-left-catalog').toggle('fast');
        $('.filters__bottom').toggle('fast');
        $('.left-catalog').css('width', '17%');
        $('.left-catalog').css('padding-right', '0');
        $('.right-catalog').css('width', '83%');
    });

    $('.show-filter').click(function () {
        event.preventDefault();
        $(this).toggle();
        $('.hide-filter').toggle();
        $('.in-left-catalog').toggle('fast');
        $('.filters__bottom').toggle('fast');
        $('.left-catalog').css('width', '28%');
        $('.right-catalog').css('width', '70%');
        $('.left-catalog').css('padding-right', '2%');
    })

    function onlinePayment(elem) {
        if (!elem.hasClass('isDisabled')) {
            elem.addClass('isDisabled');
            elem.text('Перенаправление...');

            $.ajax({
                method: "POST",
                url: "/local/ajax/tinkoff_payment.php",
                data: {'orderId': elem.attr('data-order-id')},
                success: function (data) {
                    elem.text('Переход');
                    window.location.replace(data);
                },
                error: function (data) {
                    console.log('Ошибка перехода к онлайн оплате');
                }
            });
        }
    }

    $('button.pay-button').on('click', function () {
        onlinePayment($(this));
    })

    $('.blue-menu').click(function () {
        $('body').css('overflow', 'hidden');
        $('.blue-menu-div').animate({"margin-left": "0px"}, 300);
        $('.podlozhka').fadeIn(600);
    });

    $('.podlozhka').click(function () {
        if ($('.menu-div').css('display') === 'none') {
            let menuAnimateWidth;
            let windowsWidth = $(window).width();
            if (windowsWidth > 767) {
                menuAnimateWidth = '-320px';
            } else {
                menuAnimateWidth = '-100%';
            }
            $('.blue-menu-div').animate({"margin-left": menuAnimateWidth}, 300);
            $('.podlozhka').fadeOut(600);
            $('.vou2').hide();
            $('.mail-div').hide();
            $('.auth-div-full').hide(0);
        }
    });

    $('.cls-mail-div').click(function () {
        $('.podlozhka').hide(0);
        $('.mail-div').hide(0);
        $('.auth-div-full').hide(0);
        $('.popup').hide(0);
        $('body').removeClass('with--popup');
    });

    $('.cls-blue-menu').click(function () {
        let menuAnimateWidth;
        let windowsWidth = $(window).width();
        if (windowsWidth > 767) {
            menuAnimateWidth = '-320px';
        } else {
            menuAnimateWidth = '-100%';
        }
        $('.blue-menu-div').animate({"margin-left": menuAnimateWidth}, 300);
        $('.podlozhka').fadeOut(600);
        $('.blue-menu').css('display', 'flex');
        $('body').css('overflow', 'auto');
    });

    $('.sex-span').on('click', function (e) {
        let that = $(this);
        let sections = $('.sex-span');
        let isActive = that.parent().hasClass('sex-btn--active');
        sections.each(function (index) {
            $(this).parent().removeClass('sex-btn--active');
            $(this).parent().addClass('sex-btn--non-active');
        });
        if (isActive) {
            that.parent().removeClass('sex-btn--active');
            that.parent().addClass('sex-btn--non-active');
        } else {
            that.parent().removeClass('sex-btn--non-active');
            that.parent().addClass('sex-btn--active');
        }
        let name = that.data('name');
        $('.sex-list').each(function (index) {
            let that = $(this);
            if (that.data('name') == name) {
                that.slideToggle();
            } else {
                that.hide();
            }
        });
    });

    $('.submenu-level2-item').click(function (e) {
        let e_target = $(e.target);
        if (e_target.is('.submenu-level2-item')) {
            let that = $(this);
            (e_target).siblings().slideToggle();
            that.toggleClass('arrow-down');
            that.toggleClass('arrow-up');
        }
    });

    $('.more-span').click(function (e) {
        let that = $(this);
        e.preventDefault();
        that.next('.blue-menu-div-div ul').toggle('200');
        that.toggleClass('open-ul');
        setTimeout(function () {
            that.parent().find('.lazy-img-menu').lazyLoadXT({forceLoad: 1, visibleOnly: 0, throttle: 0})
        }, 500);
    });

    $('.order-info-grid').click(function () {
        let that = $(this);
        let basketBlock = that.next('.order-basket-items');
        let oneZkzBlock = that.parent('.one-zkz');
        if (that.css('background-color') != 'rgb(243, 243, 243)') {
            oneZkzBlock.css('border-color', 'gray');
            that.css('background-color', '#f3f3f3');
            that.css('background-image', 'url("/img/up-arrow.png")');
            that.css('background-position', 'calc(100% - 20px) 37px');
            basketBlock.slideDown();
            that.removeClass('opn');
        } else {
            that.css('background-color', '#fff');
            that.css('background-image', 'url("/img/down-arrow.png")');
            that.css('background-position', 'calc(100% - 20px) 37px');
            basketBlock.slideUp({
                complete: function () { // callback
                    oneZkzBlock.css('border-color', 'rgba(200,200,200, .5)');
                }
            });
            that.addClass('opn');
        }
    });

    $('.pay-lk-button').on('click', function (event) {
        event.stopPropagation();
        onlinePayment($(this));
    });

    $(document).on('click', '.js-favour-heart', function () {
        let button = $(this);
        button.toggleClass('active');
        $.ajax({
            method: 'post',
            url: '/catalog/favorites/',
            data: {
                changeFavourite: 'Y',
                ID: $(this).data('id'),
            },
            success: function (response) {
                if (response['res'] === 'error') {
                    button.toggleClass('active');
                    Popup.show('<div style="text-align: center; padding: 0 40px;"><article style="font-size: 1.4em;">' + response.text + '</article></div>');
                } else {
                    let count = Number($('.count--heart.in-full').text());
                    if (response['res'] == 'add') {
                        $('.count--heart').text(++count);
                        ym(82799680, 'reachGoal', 'favourite_add');
                    } else {
                        $('.count--heart').text(--count);
                    }
                }
            },
        });
    });
});

//Прикрепление маски на поле ввода телефона
function phoneMaskCreate(phoneInput, needStar = true) {
    phoneInput.mask('+7 (999) 999-99-99', {
        autoclear: false
    }).click(function () {
        let that = $(this);
        if (that.val() == '+7 (___) ___-__-__') {
            that[0].selectionStart = 4;
            that[0].selectionEnd = 4;
        }
    }).mouseover(function () {
        $(this).attr('placeholder', '+7 (___) ___-__-__');
    }).mouseout(function () {
        let phoneString = (needStar ? '*' : '') + 'Телефон';
        $(this).attr('placeholder', phoneString);
    }).keydown(function (e) {
        let that = $(this);
        if (that.val() == '+7 (___) ___-__-__') {
            if (e.key == 8 || e.key == 7) {
                that.val('+7 (___) ___-__-__');
                that[0].selectionStart = 4;
                that[0].selectionEnd = 4;
                e.preventDefault();
                e.stopPropagation();
            }
        }
    }).on('input keyup', function (e) {
        let that = $(this);
        if (String(Number(that.val().replace(/\D+/g, ""))).substr(0, 2) == '77' ||
            String(Number(that.val().replace(/\D+/g, ""))).substr(0, 2) == '78' ||
            that.val().indexOf('+7 (8') + 1 ||
            that.val().indexOf('+7 (7') + 1 ||
            (that.val().indexOf('+7 ' == -1) && (that.val()[0] == 8 || that.val()[0] == 7))) {
            that.val('+7 (___) ___-__-__');
            that.mask('+7 (999) 999-99-99', {autoclear: false});
            that.val('+7 (___) ___-__-__');
            that[0].selectionStart = 4;
            that[0].selectionEnd = 4;
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

//функция для клика на кнопку "Добавить в корзину"
function addToCartHandler(offerId, quantity, productData) {
    let data = {
        action: "basketAdd",
        offerId: offerId,
        quantity: quantity,
    };
    $.ajax({
        method: "POST",
        url: "/cart/",
        data: data,
        dataType: "json",
        success: function (data) {
            if (data.status == "ok") {
                updateSmallBasket(quantity);
                respercEvent__add_to_cart();
                ym(82799680,'reachGoal','add_in_cart');
                if (productData) {
                    productData['quantity'] = quantity;
                    window.metrikaData.push({
                        "ecommerce": {
                            "add": {
                                "products": [
                                    productData
                                ]
                            }
                        },
                    });
                }
                return;
            }
            let error_text = '<div class="product-preorder-success">'
                + '<h2>Ошибка</h2>'
                + '<div class="popup-footer" style="justify-content: center">'
                + '<div class="js-size-popup popup-error text-danger">'
                + (Array.isArray(data.text) ? data.text.join("<br>") : 'Возникла ошибка при добавлении товара в корзину. Обратитесь в поддержку')
                + '</div>'
                + '</div>'
                + '</div>';
            Popup.show(error_text, {});
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function addItemToCartOrOpenDetail(buttonElem) {
    let btn = $(buttonElem);
    let offerId = btn.data('id');
    if (offerId) {
        let productData = $(buttonElem).data();
        addToCartHandler(offerId, 1, productData);
    } else {
        console.log(btn.data());
        window.open(btn.data('url'), '_blank');
    }
}
