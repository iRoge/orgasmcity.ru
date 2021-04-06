// var timer = 0;
// var q = '';
//
// $(function () {
//
//     $('.search-form').on('keyup', 'input', function() {
//         var q = $(this).val();
//         clearTimeout(timer);
//         timer = setTimeout(getResultSearch(q), 1000);
//     })
// })

// function getResultSearch(q) {
//     var searchSuggestBlock = $('.header__search .search-suggest');
//     //очищаем результаты поиска
//     searchSuggestBlock.html('');
//     //пока не получили результаты поиска - отобразим прелоадер
//     //searchSuggestBlock.append('<li><div class="suggest-item preloader"></li>');
//     $.ajax({
//         type: "POST",
//         url: "/search/ajax.php",
//         data: "q="+q,
//         dataType: 'json',
//         success: function(json){
//             //очистим прелоадер
//             searchSuggestBlock.show();
//             searchSuggestBlock.html('');
//             $.each(json.items, function(index, element) {
//                 searchSuggestBlock.append('<li class="suggest-item"><a href="'+element.url+'">'+element.title+'</a></li>');
//             });
//         }
//     });
// }