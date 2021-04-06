$(function () {
    $('.js-rubrics-agree').on('change', function () {
        $('.js-rubrics-fieldset').prop('disabled', !$(this).is(':checked'));
    }).trigger('change');
});