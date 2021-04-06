let vacanciesText = '';

$(document).ready(function(){
    $('.vacancies-button-arrow').click(function(){
        $(this).parents('.vacancy').toggleClass('vacancy--expanded');
    });
});

$('.button-vacancies').click(function () {
    vacanciesText = '\n\nГород вакансии\n*******************************\n' + $('.selectize-input').text();
    vacanciesText += '\n\nНазвание вакансии\n*******************************\n' + $('#' + $(this).data('id')).children('header').text().trim();
    vacanciesText += '\n\nСодержание вакансии\n*******************************\n' + $('#' + $(this).data('id')).children('article').text().trim();

    $("html, body").animate({scrollTop: 0},500);

    $('.mail-div').toggle(0);
    $('.podlozhka').toggle(0);
    $('.mail-div .popup').show(0);

    $('.feedback-selectize').attr('onmousedown', 'return false;');
    $('.feedback-selectize').attr('onkeydown', 'return false;');
    $('.feedback-selectize').val('По вакансиям');
});
$(document).on('click', '.js-feedback-btn', function () {
    $("html, body").animate({scrollTop: 0},500);
    $('[name="SIMPLE_FORM_1"]').append('<textarea name="form_textarea_2" style="display: none;" class="required">' + $('[name="form_textarea_2"]').val() + vacanciesText.replace(/ +/g, ' ') + '</textarea>');
});

$('.cls-mail-div').click(function () {
    $('.feedback-selectize').attr('onmousedown');
    $('.feedback-selectize').attr('onkeydown');
    $('.feedback-selectize').val('');
});

$('.podlozhka').click(function () {
    $('.feedback-selectize').removeAttr('onmousedown');
    $('.feedback-selectize').removeAttr('onkeydown');
    $('.feedback-selectize').val('');
});