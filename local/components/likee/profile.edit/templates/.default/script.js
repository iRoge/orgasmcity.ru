$(function () {
    var select_region = $('.js-profile-regions'),
        select_city = $('.js-profile-cities');


    if ($.trim(select_region.val()) == 'Москва') {
        select_city[0].selectize.isRequired = false;
        select_city[0].selectize.$control_input.prop('required', false).prop('placeholder', 'Город');
    }
    select_region.on('change', function (e) {
        var region_name = $.trim($(this).val()),
            data = {
                action: 'load_cities',
                region_name: region_name
            };

        if (region_name && region_name.length > 0) {
            $.get(document.location, data, function (response) {
                select_city[0].selectize.clearOptions();

                if ($.isEmptyObject(response.CITIES)) {
                    select_city[0].selectize.isRequired = false;
                    select_city[0].selectize.$control_input.prop('required', false).prop('placeholder', 'Город');
                } else {
                    select_city[0].selectize.isRequired = true;
                    select_city[0].selectize.$control_input.prop('required', true).prop('placeholder', '*Город');
                    $.each(response.CITIES, function (city_id, city) {
                        select_city[0].selectize.addOption({value: city, text: city});
                    });
                }

                select_city[0].selectize.refreshOptions();
            }, 'json');
        }
    });
});