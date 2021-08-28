$(document).ready(function() {
    $('.haveOrder-input').on('change', function () {
        let elem = $(this);
        let hasOrderBlock = elem.parent().find('.haveOrder-closed');
        if ($(this).prop('checked') === true) {
            hasOrderBlock.slideDown().show();
        } else {
            hasOrderBlock.slideUp().hide();
        }
    });


});