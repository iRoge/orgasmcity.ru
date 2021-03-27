$(document).ready(function () {
    $('.tenders__link').on('click', function (e) {
        var id = $(this).attr('href');
        if($(this).hasClass('dropdown-toggle--expanded')){
            $(id).hide();
            $(this).removeClass('dropdown-toggle--expanded');
        }else{
            $(id).show();
            $(this).addClass('dropdown-toggle--expanded');
        }
        e.preventDefault();
    })
})