var timer = 0;
var q = '';

$(function () {
    let mainSearchForm = $('.main-search-form');
    mainSearchForm.on('blur', 'input', function() {
        setTimeout(function () {
                var searchSuggestBlock = $('.header__search .search-suggest');
                searchSuggestBlock.hide()
            },
            500);
    });
    mainSearchForm.on('focus', 'input', function() {
        var q = $(this).val();
        var searchSuggestBlock = $('.header__search .search-suggest');
        searchSuggestBlock.show();
        if (q.length === 0) {
            searchSuggestBlock.html('Введите минимум 3 символа');
        }

    });
    mainSearchForm.on('keyup', 'input', function() {
        var q = $(this).val();
        clearTimeout(timer);
        var searchSuggestBlock = $('.header__search .search-suggest');
        if (q.length > 2) {
            $(this).removeClass('search-btn-disable');
            searchSuggestBlock.show();
            //очищаем результаты поиска
            searchSuggestBlock.html('');
            //пока не получили результаты поиска - отобразим прелоадер
            searchSuggestBlock.append('<div style="width:30px;height:30px;" class="lds-ring lds-ring--button"><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div><div style="width:30px;height:30px;"></div></div>');
            timer = setTimeout(() => getResultSearch(q, searchSuggestBlock), 500);
        } else {
            $(this).addClass('search-btn-disable');
            searchSuggestBlock.show();
            searchSuggestBlock.html('Введите минимум 3 символа');
        }
    });
    mainSearchForm.on('submit', function (e) {
        let searchText = $('#main-search-input').val();
        if (searchText.length >= 3) {
            let form = $('#main-search-form');
            form.attr('action', '/catalog/search/?q=' + searchText);
            form.submit();
        } else {
            $('.header__search .search-suggest').show();
            $('.header__search .search-suggest').html('Введите минимум 3 символа');
            return false;
        }
    });
});

function getResultSearch(q, searchSuggestBlock) {
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
                searchSuggestBlock.append('<li class="suggest-item"><a href="' + element.url + '">' + element.title + '</a></li>');
                searchSuggestBlock.append('<input type="hidden" name="sections[' + element.url1 + ']" value="' + element.title1 + '">');
                empty = false;
            });
            $.each(json.properties, function(index, element) {
                for (let i in element) {
                    searchSuggestBlock.append('<li class="suggest-item"><a href="' + element[i].url + '">' + element[i].title + '</a></li>');
                    searchSuggestBlock.append('<input type="hidden" name="properties[' + element[i].url1 + ']" value="' + element[i].title1 + '">');
                    empty = false;
                }
            });
            $.each(json.items, function(index, element) {
                searchSuggestBlock.append('<li class="suggest-item"><a href="' + element.url + '">' + element.title + '</a></li>');
                searchSuggestBlock.append('<input type="hidden" name="items[' + element.url1 +']" value="' + element.title1 + '">');
                empty = false;
            });
            if (empty) {
                searchSuggestBlock.append('<p>Ничего не найдено</p>');
            }
        }
    });
}
