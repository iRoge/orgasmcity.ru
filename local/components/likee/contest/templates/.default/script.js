(function($) {
    var contestItems;

    $(function() {
        contestItems = ('undefined' !== typeof RESPECT_CONTEST) ? RESPECT_CONTEST : [];

        if ('undefined' !== typeof RESPECT_CONTEST_START && true === RESPECT_CONTEST_START) {
            $('.js-contest-start')
                .addClass('container text--center')
                .append('<div class="column-8 column-center"><button type="button" class="button button--primary button--xl button--bigger js-contest-go">Приступить</button></div>');
        }

        $('.js-contest-go').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', 'disabled');
            if (contestItems.length) {
                $('.js-contest-start').remove();
                $('#contest-rules').addClass('hidden');
                initContest();
            }
        });

        if ($('.contest').is('.contest--autostart')) {
            $('.js-contest-go').trigger('click');
        }
    });

    function initContest()
    {
        var item = contestItems.shift();
        if (! item) {
            Popup.hide();
            return initStatistics();
        }

        var responseClose = false;
        var $content = $('<div class="contest-content" />');
        $content.append('<div class="contest-content__image" />').find('.contest-content__image').css('background-image', 'url('+item.SRC+')').attr('title', item.NAME);
        $content.append('<div class="contest-content__actions" />')
            .find('.contest-content__actions')
            .append('<button class="contest-content__button js-content-like js-status-0">Не нравится</button>')
            .append('<button class="contest-content__button js-content-like js-status-1">Нравится</button>');

        Popup.show($content, {
            className: 'popup--contest',
            onClose: function (popup) {
                if (! responseClose) {
                    contestItems.unshift(item);
                    $('.js-contest-go').removeAttr('disabled');

                    initStatistics();
                }
            }
        });
        $('body').addClass('disable--popup-overlay-close');

        $('.js-content-like', $content).on('click', function(e) {

            responseClose = true;
            $('.js-content-like', $content).attr('disabled', 'disabled');

            var artStatus = {
                action: 'add_contest_response',
                art: item.PROPERTY_ARTICLE_VALUE,
                status: ($(this).is('.js-status-1') ? 'Y' : 'N')
            };

            $.ajax({
                type: "POST",
                data: artStatus,
                dataType: "json",
                success: function(data) {
                    initContest();
                }
            });
        });
    }
    function initStatistics()
    {
        $('body').removeClass('disable--popup-overlay-close');
        
        var $result = $('.contest__stat');

        $.getJSON('', { action: 'get_contest_result' }, function(data) {
            if (! data.status) {
                return false;
            }

            var liked = 0;
            var total = data.results.length;
            for (var i = 0; i < total; i ++) {
                liked += ('Y' == data.results[i].STATUS ? 1 : 0);
            }

            $result.find('.js-contest-total').text(total);
            $result.find('.js-contest-liked').text(liked);
            $result.find('.js-contest-disliked').text((total - liked));
            $result.find('.js-contest-remains').text(contestItems.length);

            if (! contestItems.length) {
                $result.find('.js-contest-go').remove();
            }
            $result.fadeIn();

            $('.container.container--action').hide();
            $('.container.container--contest-end').fadeIn();
        });
    }
})(jQuery);