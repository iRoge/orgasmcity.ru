var component = $('.user-region, .location-icon, .current-locality');
var background = $('.podlozhka');
var close = $('.geoposition__close');
var popup = $('.geoposition');
var tooltip = $('.tooltip-window');
var tooltipAccept = $('.tooltip-window__button--ok');
var tooltipReject = $('.tooltip-window__button--no');
var autodetect = $('.geoposition__set-city-auto');
var locality = $('.geoposition__city');
var geoForm = $(".geoposition__form");
var okButton = $('.geoposition__button--ok, .geoposition__button-mobile');

component.click(function() {
    popup.css('display', 'flex');
    background.css('display', 'block');
    tooltip.css('display', 'none');
});

background.click(function() {
    popup.css('display', 'none');
});

close.click(function() {
    popup.css('display', 'none');
    background.css('display', 'none');
});

tooltip.click(function() {
    tooltip.css('display', 'none');
});

tooltipAccept.click(function() {
    tooltip.css('display', 'none');
});

tooltipReject.click(function() {
    tooltip.css('display', 'none');
    popup.css('display', 'flex');
    background.css('display', 'block');
});

autodetect.click(function() {
    updateUserLocality('auto');
});

locality.click(function() {
    updateUserLocality($(this).attr('id'));
});

geoForm.submit(function() {
    return false;
})

okButton.click(function() {
    $("#geo_location_search").next("span").find(".select2-selection").removeClass("red_border");
    code = $("#geo_location_code").val();
    if (code) {
        updateUserLocality(code);
    } else {
        $("#geo_location_search").next("span").find(".select2-selection").addClass("red_border");
    }
});

function updateUserLocality(localityCode) {
    $('#geoposition_error').remove();
    $.ajax({
        type: 'post',
        async: false,
        url: '/local/ajax/update_user_locality.php',
        data: {
            'location_code': localityCode,
            'pathname': document.location.pathname
        },
        success:function(data) {
            var parsed = JSON.parse(data);
            switch (parsed.status) {
                case 'success':
                    if (localityCode == "auto") {
                        $("#select2-geo_location_search-container").html(parsed.NAME_RU);
                        $('#geo_location_code').val(parsed.CODE);
                    } else {
                        let oPush = {
                            'event':'MTRENDO',
                            'eventCategory': 'changeCity',
                            'cityName': parsed.cityName,              // город, выбранный пользователем
                        };

                        //Если пользователь авторизован:
                        if (parsed.user) {
                            oPush['userId'] = parsed.user.id;              // уникальный идентификатор пользователя
                            oPush['userAuth'] = parsed.user.userAuth; 		     // признак авторизации пользователя
                            oPush['userStatus'] = parsed.user.userStatus;  // Статус клиента в программе лояльности
                        }

                        window.dataLayer = window.dataLayer || [];
                        dataLayer.push(oPush);

                        $(window).scrollTop(0);
                        popup.css('top', '');
                        document.location.href = parsed.url;
                    }
                    break;
                case 'fail':
                    $('.geoposition__heading').after('<p id="geoposition_error">Не удалось изменить регион доставки</p>');
                    break;
            }
        },
        error:function() {
            $('.geoposition__heading').after('<p id="geoposition_error">Не удалось изменить регион доставки</p>');
        }
    });
}

$(function() {
    $('#geo_location_search').select2({
        minimumInputLength: 3,
        placeholder: "Населенный пункт",
        ajax: {
            url: "/local/ajax/location_search.php",
            dataType: "json",
            type: "GET",
            contentType: "application/json;charset=utf-8",
            delay: 300,
            data: function (params) {
                return {
                    q: params.term || 'Москва',
                };
            },
        },
    }).on('select2:select select2:unselect', function(data) {
        if(typeof data.params.data.code !== 'undefined') {
            $('#geo_location_code').val(data.params.data.code);
        } else {
            $('#geo_location_code').val('');
        }
    });
});
