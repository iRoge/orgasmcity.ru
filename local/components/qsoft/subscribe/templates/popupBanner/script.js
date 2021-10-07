$(function () {
    $(document).on('submit', '#subscribe-form_mobile', function (e) {
        e.preventDefault();
        var errCount = 0;
        $('.error').remove();
        var $email = $(this).find('.js-footer-email');
        if ($email.val().trim() != "") {
            if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test($email.val().trim())) {
                $email.before('<p class="error">Неверный Email.</p>');
                $email.addClass("red_border_sub");
                errCount += 1;
            }
        } else {
            $email.before('<p class="error">Поле Email обязательно для заполнения.</p>');
            $email.addClass("red_border_sub");
            errCount += 1;
        }
        if (errCount == 0) {
            var form = $(this),
                data = form.serializeArray();
            var $subscribe = form.closest('.js-subscribe-new');
            $.post('/local/ajax/generateCoupon.php', data, function (html) {
                $subscribe.replaceWith(html);
                $subscribe = form.closest('.js-subscribe-new');
                ym(82799680,'reachGoal','subscribe');
                if ($subscribe.find('#subscribe-message').length > 0) {
                    $subscribe.addClass('subscribe--success');
                }
            });
        }
    });

    $(document).ready(function () {
        if (window.localStorage['surpriseReceived'] !== 'true' && !isAuth) {
            setInterval(function () {
                if (!window.localStorage['onlineTime']) {
                    window.localStorage['onlineTime'] = 0;
                }
                window.localStorage['onlineTime'] = Number(window.localStorage['onlineTime']) + 5;
                if (Number(window.localStorage['onlineTime']) === 60) {
                    let element = $('.js-popup-banner');
                    element.prepend('<img src="/img/surprise.webp" alt="Surprise" width="100%" height="100%">')
                    element.show();
                    Popup.show(element, {
                        onShow: function () {
                            element.closest('.cls-mail-div').hide();
                        },
                        className: 'surprise-banner'
                    });
                    window.localStorage['surpriseReceived'] = true;
                }
            }, 5000);
        }
    })
});
