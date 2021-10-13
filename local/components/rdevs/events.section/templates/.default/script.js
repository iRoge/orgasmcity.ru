function goToPageNum(btn) {
    $("html, body").animate({scrollTop: 0}, 500);
    $.ajax({
        method: "POST",
        url: $(btn).data('url'),
        data: data,
        dataType: "json",
        success: function (data) {
            $('.js-event-container').html($(data));
            trText();
            history.pushState(null, null, $(btn).data('url'));
        },
        error: function (data) {
            $('.js-event-container').html($(data));
            trText();
            history.pushState(null, null, $(btn).data('url'));
        }
    });
}

function change(select) {
    history.replaceState({}, '', $(select).val());
    location.reload();
}

let blockResizeText = false;

(function ($) {
    let truncate = function (el) {
        let text = el.text(),
            height = el.height(),
            width = el.width(),
            clone = el.clone();

        clone.css({
            position: 'absolute',
            visibility: 'hidden',
            height: 'auto',
            padding: '0px 10px 0px 0px'
        });
        el.after(clone);

        let l = 2 * width - 250;

        for (; l >= 0 && clone.height() > height; --l) {
            clone.text(text.substring(0, l) + '...');
        }
        el.text(clone.text());
        clone.remove();
    };

    $.fn.truncateText = function () {
        return this.each(function () {
            truncate($(this));
        });
    };
}(jQuery));

function trText() {
    $('.event-bottom-container').each(function () {
        let tmpHeight = 0;
        $(this).children('span').each(function () {
            tmpHeight = tmpHeight + $(this).height();
        });

        let textHeight = $(this).height() - tmpHeight + 5;
        $('.js-event-text-box-wp', $(this)).css('height', textHeight);
        $('.js-event-text-box', $(this)).html($('.js-event-text-box', $(this)).data('full-text'));
    });

    $('.js-event-text-box').truncateText();
    $('.js-event-text-box-wp').css('visibility', 'visible');
    blockResizeText = false;
}

trText();

$(window).resize(function () {
    if (!blockResizeText) {
        setTimeout(function () {
            trText();
        }, 100);
        blockResizeText = true;
    }
});
