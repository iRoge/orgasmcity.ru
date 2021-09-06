$(document).ready(function() {
    updateHasOrderBlock();
    $('.haveOrder-input').on('change', function () {
        updateHasOrderBlock();
    });
    $('.feedback-form').submit(function (event) {
        event.preventDefault();
        let form = $(this);
        let data = form.serialize();
        BX.ajax.loadJSON('/local/ajax/sendFeedback.php?' + data, function (response) {
            let errorsWrapper = $('.feedback-errors-wrapper');
            let successWrapper = $('.feedback-success-wrapper');
            let errorMessageBlock = $('.js-error-message');
            let successMessageBlock = $('.js-success-message');
            if (response['SUCCESS']) {
                errorsWrapper.hide();
                successWrapper.show();
                successMessageBlock.html('Ваш отзыв успешно отправлен на модерацию и скоро будет опубликован!');
                form.hide();
            } else {
                let errors = response['ERRORS'];
                errorsWrapper.show();
                errorMessageBlock.html(errors.join('<br>'));
            }
        });
        return false;
    })
});

function updateHasOrderBlock() {
    let elem = $('.haveOrder-input');
    let hasOrderBlock = elem.parent().find('.haveOrder-closed');
    if (elem.prop('checked') === true) {
        hasOrderBlock.slideDown().show();
    } else {
        hasOrderBlock.slideUp().hide();
    }
}