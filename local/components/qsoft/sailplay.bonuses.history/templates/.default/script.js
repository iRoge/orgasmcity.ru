$(function () {
    $('.js-btn-history-update').on('click', function () {
        var $div = $('#update-error');
        var request = BX.ajax.runComponentAction('qsoft:sailplay.bonuses.history', 'updateHistory', {mode: 'class'});

        request.then(function (response) {
            window.location.reload();
        }).catch(function () {
            $div.html('<p>Не удалось обновить</p>');
            // TODO добавить верстку сообщения об ошибке
        })
    })
});

$('#purchase-show').click(function(){
    $(".bonus-check").toggle();
});  