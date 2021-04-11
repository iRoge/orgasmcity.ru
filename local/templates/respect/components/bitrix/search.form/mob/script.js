var timer = 0;
var q = '';

$(function () {
    let mobSearchForm = $('.mob-search-form');
    mobSearchForm.on('blur', 'input', function() {
        setTimeout(function (){
                var searchSuggestBlock = $('.poisk-div .search-suggest');
                searchSuggestBlock.hide();
            },
            500);
    });
    mobSearchForm.on('focus', 'input', function() {
        var q = $(this).val();
        var searchSuggestBlock = $('.poisk-div .search-suggest');
        searchSuggestBlock.show();
        if (q.length === 0) {
            searchSuggestBlock.html('Введите минимум 3 символа');
        }

    });
    mobSearchForm.on('keyup', 'input', function() {
        var q = $(this).val();
        clearTimeout(timer);
        var searchSuggestBlock = $('.poisk-div .search-suggest');
        if (q.length > 2) {
            $(this).removeClass('search-btn-disable');
            searchSuggestBlock.show();
            //очищаем результаты поиска
            searchSuggestBlock.html('');
            //пока не получили результаты поиска - отобразим прелоадер
            searchSuggestBlock.append('<div style="padding-top:10px;width:30px;height:40px;" class="lds-ring lds-ring--button"><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div></div>');
            timer = setTimeout(() => getResultMobSearch(q, searchSuggestBlock), 1000);
        } else {
            $(this).addClass('search-btn-disable');
            searchSuggestBlock.show();
            searchSuggestBlock.html('Введите минимум 3 символа');
        }
    })
    mobSearchForm.on('submit', function () {
        let searchText = $('#mob-search-input').val();
        if (searchText.length >= 3) {
            let form = $('#mob-search-form');
            form.attr('action', '/catalog/search/?q=' + searchText);
            form.submit();
        } else {
            $('.poisk-div .search-suggest').show();
            $('.poisk-div .search-suggest').html('Введите минимум 3 символа');
            return false;
        }
    });
});

function getResultMobSearch(q, searchSuggestBlock) {
   $.ajax({
        type: "POST",
        url: "/search/ajax.php",
        data: "q="+q,
        dataType: 'json',
        success: function(json){
            //очистим прелоадер
            searchSuggestBlock.show();
            searchSuggestBlock.html('');
            let empty = true;
            $.each(json.sections, function(index, element) {
                searchSuggestBlock.append('<li style="list-style-type: none;line-height: 40px;" class="suggest-item"><a style="text-decoration: none; color: black;" href="'+element.url+'">'+element.title+'</a></li>');
                empty = false;
            });
            $.each(json.items, function(index, element) {
                searchSuggestBlock.append('<li style="list-style-type: none;line-height: 40px;" class="suggest-item"><a style="text-decoration: none; color: black;" href="'+ element.url + '">' + element.title + '</a></li>');
                empty = false;
            });
            if (empty) {
                searchSuggestBlock.append('<p>Ничего не найдено</p>');
            }
        }
    });
}