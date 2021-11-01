$(document).ready(function() {
    updateHasOrderBlock();
    $('.haveOrder-input').on('change', function () {
        updateHasOrderBlock();
    });
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