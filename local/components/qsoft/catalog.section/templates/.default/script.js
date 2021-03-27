function SmartFilter() {}

SmartFilter.prototype = {
    filter: '.js-filter-wrapper',
    cards: '.cards__box',
    itemsCount: '.all-items-count',
    navigation: '.after-all-in-right-catalog',
    sort: 'default',
    timerChangedPriceFilter:0,
    newResult: '',
    currentAjax: null,
    nextAjax: [],
    applyFilter: false,
};

SmartFilter.prototype.getFormData = function () {
    let formData = {};
    $('.in-in-left').each(function () {
        let filterName = $(this).data('filter-name');
        if (typeof filterName === 'undefined') {
            return;
        }
        formData[filterName] = [];
        $(this).find(':checkbox:checked').each(function () {
            formData[filterName].push($(this).val());
        });
    });
    let online_try_on = $('#online_try_on');
    let from_default_loc = $('#from_default_loc');
    let max_price = $('#max_price');
    let min_price = $('#min_price');
    if (online_try_on.is("input") && online_try_on.prop("checked")) {
        formData['online_try_on'] = "Y";
    }
    if (from_default_loc.is("input") && from_default_loc.prop("checked")) {
        formData['from_default_loc'] = "N";
    }
    if (max_price.is("input")) {
        formData['max_price'] = max_price.val();
    }
    if (min_price.is("input")) {
        formData['min_price'] = min_price.val();
    }
    // собираем данные из фильтра по типу изделия
    $('.filter__main-list [type="checkbox"]:checked:not(.general-all)').each(function() {
        let name = $(this).data('name');
        let id = $(this).data('id');
        if (formData[name] != undefined && formData[name] != '') {
            formData[name] += ',' + id;
        } else {
            formData[name] = id;
        }
    });
    return formData;
};

SmartFilter.prototype.getQuery = function () {
    var formData = this.getFormData();
    var query = window.location.search;
    var setFilter = false;
    var params = [];
    var newQuery = '';

    if (query !== '') {
        var tempParams = query.replace('?', '').split('&');
        for (var i = 0; i < tempParams.length; i++) {
            var keyval = tempParams[i].split("=");
            if (keyval[0] !== 'set_filter') {
                params[keyval[0]] = decodeURIComponent(keyval[1]);
            }
        }
    }

    for (var filterName in formData) {
        if (typeof formData[filterName] === 'object') {
            formData[filterName] = formData[filterName].join(',');
        }
        if (formData[filterName] !== '') {
            setFilter = true;
        }
        if (params[filterName] !== undefined || formData[filterName] !== '') {
            params[filterName] = formData[filterName];
        }
    }

    setFilter ? newQuery = 'set_filter=Y' : '';

    for (var param in params) {
        if (params[param] !== '') {
            if (newQuery !== '') {
                newQuery += '&' + param + '=' + params[param];
            } else {
                newQuery += param + '=' + params[param];
            }
        }
    }

    if (newQuery.length > 0) {
        newQuery = '?' + newQuery;
    }

    return newQuery;
};

SmartFilter.prototype.getUrl = function (type = 'filter') {
    if (type === 'sort'){
        let params = new URLSearchParams(window.location.search);
        let sign = window.location.search.indexOf('?') > 0 ? '&' : '?';

        if (params.has('sort')) {
            params.set('sort', SmartFilter.prototype.sort);
        } else{
            params.append('sort', SmartFilter.prototype.sort);
        }
        return window.location.pathname + sign + params;
    }
    if (type === 'reset') {
        let sort = '';
        if (window.location.search.indexOf('sort=') > 0) {
            let params = new URLSearchParams(window.location.search);
            sort = '?sort=' + params.get('sort');
        }
        return window.location.pathname + this.getQuery() + sort;
    }
    if (type === 'filter') {
        let sort = '';
        if (window.location.search.indexOf('sort=') > 0) {
            let params = new URLSearchParams(window.location.search);
            sort = '&sort=' + params.get('sort');
        }
        return window.location.pathname + this.getQuery() + sort;
    }
};

SmartFilter.prototype.processSearch = function (params = {}) {
    var search = new URLSearchParams(window.location.search);
    if (search.has('q')) {
        params.q = search.get('q');
    }

    return params;
};

SmartFilter.prototype.click = function (checkbox) {
    let jqCheckbox = $(checkbox);
    var $filterSection = jqCheckbox.closest('.in-left-catalog');
    $('.filter-btn-loader').show();
    if ($filterSection.hasClass('in-left-catalog--delivery')) {
        if ($filterSection.find('input[type="checkbox"]').not(':checked').length > 0) {
            $filterSection.find('input[type="checkbox"]:checked').prop('disabled', true);
        } else {
            $filterSection.find('input[type="checkbox"]').each(function () {
                $(this).prop('disabled', false);
            });
        }
    }
    this.setFilterSectionStyle($filterSection);
    if (jqCheckbox.attr('id') === 'from_default_loc') {
        saveSettingsInCookie();
    }
    SmartFilter.prototype.doClick(this.getUrl(),'filter');
};

SmartFilter.prototype.doClick = function (url,request) {
    let params = this.processSearch();
    if(request !== 'sort'){
        params.getFilters = 'Y';
    }

    SmartFilter.prototype.setFilterButtonsStyle(request);
    if (SmartFilter.prototype.currentAjax){//если какой-то запрос выполняется, ставим в очередь
        SmartFilter.prototype.nextAjax[0] = url;
        SmartFilter.prototype.nextAjax[1] = request;
    } else {
        SmartFilter.prototype.currentAjax = BX.ajax.post(url, params, function (data) {
            SmartFilter.prototype.currentAjax = null;
            //переходим к следующему в очереди, если есть
            if (SmartFilter.prototype.nextAjax[0] != null) {
                SmartFilter.prototype.doClick(SmartFilter.prototype.nextAjax[0],SmartFilter.prototype.nextAjax[1]);
                SmartFilter.prototype.nextAjax = [];
            } else {
                let newFilter = $(data).find(SmartFilter.prototype.filter);
                if (request !== 'sort') {
                    SmartFilter.prototype.newResult = data;
                    $('label').each(function () {
                        let $this = $(this);
                        if (!$this.prop('for')) {
                            return;
                        }
                        let newInput = newFilter.find('#' + $this.prop('for'));
                        let currentInput = $(SmartFilter.prototype.filter).find('#' + $this.prop('for'));
                        if (newInput.prop('disabled')){
                            $this.addClass('mydisabled');
                            currentInput.prop('disabled', true);
                        } else {
                            $this.removeClass('mydisabled');
                            currentInput.prop('disabled', false);
                        }
                    });
                    $('#min_price').attr('placeholder', newFilter.find('#min_price').attr('placeholder'));
                    $('#max_price').attr('placeholder', newFilter.find('#max_price').attr('placeholder'));

                    SmartFilter.prototype.sortFilterListByActive();

                }
                $('.items-count').text(newFilter.find('.all-items-count').val());
                if(SmartFilter.prototype.applyFilter){
                    SmartFilter.prototype.applyFilter = false;
                    SmartFilter.prototype.updateCatalog(url,params, request, data);
                }
                $('.filter-btn-loader').hide();
            }
        });
    }
};

SmartFilter.prototype.updateCatalog = function (url, params = {}, request,data) {
    let lds_ring = $('.lds-ring--settings');
    lds_ring.css('visibility', 'visible');
    params = this.processSearch();
    if (request === 'sort'){
        params.sort = SmartFilter.prototype.sort;
    }
    if(request !== 'sort') {
        data = SmartFilter.prototype.newResult;
    }
    if(request === 'filter') {
        $('.js-retail-rocket-recommendation').html('');
    }
    var $catalog = $(SmartFilter.prototype.cards);
    var $navigation = $(SmartFilter.prototype.navigation);
    var sign = url.indexOf('?') > 0 ? '&' : '?';
    if (params.q !=null){
        window.history.pushState({ "html": $catalog.html() }, url, url + sign + 'q=' + params.q);
    }else{
        window.history.pushState({ "html": $catalog.html() }, url, url);
    }
    $catalog.html($(data).find(SmartFilter.prototype.cards).html());
    var $navString = $(data).find(SmartFilter.prototype.navigation);
    if ($navString.length > 0) {
        $navigation.html($navString.html());
    } else {
        $navigation.empty();
    }
    SmartFilter.prototype.setFilterButtonsStyle('new');
    truncateItemTitle();
    lds_ring.css('visibility', 'hidden');
    $('.lds-ring-container').hide();
    $('.filter-btn-loader').hide();
};

SmartFilter.prototype.clearFilter = function () {
    let in_left_catalog = $('.in-left-catalog');
    in_left_catalog.each(function () {
        let $this = $(this);
        $this.find('label').removeClass('mydisabled');
        $this.find(':checkbox').each(function () {
            $(this).prop('checked', false);
            $(this).prop('disabled', false);
        });
        $this.find('input[type=text]').val('');
    });
    $('.plus').show();
    $('.minus').hide();
    $('.name-h3').removeClass('active-name-h3');
    $('.in-in-left').removeAttr('style');
    in_left_catalog.removeClass('in-left-catalog--checked');
};

SmartFilter.prototype.nextPage = function (btn) {
    $("html, body").animate({scrollTop: 0}, 500);
    btn = $(btn);
    let lds_ring = $('.lds-ring--settings');
    lds_ring.css('visibility', 'visible');
    $(".load-more-btn-main").hide();
    $(".load-more-btn-loader:not(.filter-btn-loader)").addClass("load-more-btn-loader-visible");
    BX.ajax.get(btn.data('href'), { 'load_more': 'Y' }, function (data) {
        let $catalog = $(SmartFilter.prototype.cards);
        history.pushState(null, null, btn.data('href'));
        $catalog.html('');
        $catalog.append($(data).find(SmartFilter.prototype.cards).html());
        let $navigation = $(SmartFilter.prototype.navigation);
        let $navString = $(data).find(SmartFilter.prototype.navigation);
        if ($navString.length > 0) {
            $navigation.html($navString.html());
        } else {
            $navigation.empty();
        }
        lds_ring.css('visibility', 'hidden');
    });
};

SmartFilter.prototype.goToPageNum = function (btn) {
    $("html, body").animate({scrollTop: 0}, 500);
    btn = $(btn);
    let lds_ring = $('.lds-ring--settings');
    lds_ring.css('visibility', 'visible');
    BX.ajax.post(btn.data('url'), { 'load_more': 'Y' }, function (data) {
        let $catalog = $(SmartFilter.prototype.cards);
        history.pushState(null, null, btn.data('url'));
        $catalog.html($(data).find(SmartFilter.prototype.cards).html());
        let $navigation = $(SmartFilter.prototype.navigation);
        let $navString = $(data).find(SmartFilter.prototype.navigation);
        if ($navString.length > 0) {
            $navigation.html($navString.html());
        } else {
            $navigation.empty();
        }
        lds_ring.css('visibility', 'hidden');
    });
};

SmartFilter.prototype.resetFilterSection = function ($filterSection) {
    let $checkboxes = $filterSection.find('input[type="checkbox"]');
    let $inputs = $filterSection.find('input[type="text"]');

    if ($filterSection.hasClass('in-left-catalog--price')) {

        $inputs.val('');
        $filterSection.find('.in-in-left').hide();
        $filterSection.find(".plus").show();
        $filterSection.find(".minus").hide();
        $filterSection.find(".name-h3").removeClass('active-name-h3');

    } else {

        $checkboxes.prop('checked', false);
        $filterSection.find('.in-in-left').hide();
        $filterSection.find(".plus").show();
        $filterSection.find(".minus").hide();
        $filterSection.find(".name-h3").removeClass('active-name-h3');

    }
    this.setFilterSectionStyle($filterSection);
    SmartFilter.prototype.doClick(SmartFilter.prototype.getUrl(),'reset');
};

SmartFilter.prototype.setFilterSectionStyle = function ($filterSection) {

    let isChanged = false;

    if ($filterSection.hasClass('in-left-catalog--price')) {
        let $inputs = $filterSection.find('input[type="text"]');
        for (let i = 0; i < $inputs.length; i++) {
            if ($inputs[i].value !== '') {
                isChanged = true;
                break;
            }
        }
    } else {
        if ($filterSection.find('input[type="checkbox"]').filter(':checked').length > 0) {
            isChanged = true;
        }
    }

    if (isChanged) {
        $filterSection.removeClass('in-left-catalog--unchecked');
        $filterSection.addClass('in-left-catalog--checked')
    } else {
        $filterSection.addClass('in-left-catalog--unchecked');
        $filterSection.removeClass('in-left-catalog--checked');
    }
};

SmartFilter.prototype.setFilterButtonsStyle = function (request) {
    let filter_status_area = $('.filter-status-area');
    let filter_reset_btn = $('.filter-reset-btn');
    let filters_btn_reset = $('.filters__btn--reset');
    let filters_status_text_btn = $('.filter__status-text-btn');
    let filters_btn_submit = $('.filters__btn--submit');
    if (request === 'filter' || request === 'reset') {
        filter_status_area.text('показать');
        filter_reset_btn.removeClass('filter__disabled-reset-btn').prop('disabled', false);
        filters_btn_reset.removeClass('filters__btn--disabled').prop('disabled', false);
        filters_status_text_btn.prop('disabled', false);
        filters_btn_submit.removeClass('filters__btn--disabled').prop('disabled', false);
    }

    if (request === 'new') {
        if ($('.js-filter-wrapper').find('.in-left-catalog--checked').length == 0) {
            filter_status_area.text('показано')
            filter_reset_btn.addClass('filter__disabled-reset-btn').prop('disabled', true);
            filters_btn_reset.addClass('filters__btn--disabled').prop('disabled', true);
            filters_status_text_btn.prop('disabled', true);
            filters_btn_submit.addClass('filters__btn--disabled').prop('disabled', true);
        } else {
            filter_status_area.text('показано')
            filter_reset_btn.removeClass('filter__disabled-reset-btn').prop('disabled', false);
            filters_btn_reset.removeClass('filters__btn--disabled').prop('disabled', false);
            filters_status_text_btn.prop('disabled', true);
            filters_btn_submit.addClass('filters__btn--disabled').prop('disabled', true);
        }
    }
};

SmartFilter.prototype.setSort = function (sort) {
    $('.lds-ring--settings').css('visibility', 'visible');
    SmartFilter.prototype.sort = sort;
    SmartFilter.prototype.applyFilter = true;
    SmartFilter.prototype.doClick(SmartFilter.prototype.getUrl('sort'),'sort');
};

SmartFilter.prototype.hideFilter = function () {
    $(".js-filter-col").addClass("catalog__content-col--sidebar");
    $(".js-filter-toggle-mobile").css("display","inline-flex");
    $('.podlozhka').hide();
    $('body').css('overflow-y', '');
    $('body,html').animate({
        scrollTop: $('.zagolovok').offset().top
    }, 800);
};

//Функция сортировки для поднятия активных элементов
SmartFilter.prototype.sortFilterListUp = function(a, b) {
    if ($(a).text().trim() ===  'Respect') {
        return -1;
    }
    if ($(b).text().trim() ===  'Respect') {
        return 1;
    }
    if ($(a).text().trim() > $(b).text().trim()) {
        return 1;
    } else if ($(a).text().trim() < $(b).text().trim()) {
        return -1;
    } else {
        return 0;
    }
};

//Функция сортировки для опускания неактивных элементов
SmartFilter.prototype.sortFilterListDown = function(a, b) {
    return $(a).find('label').hasClass('mydisabled') - $(b).find('label').hasClass('mydisabled');
};

// Сортирует элементы фильтра
SmartFilter.prototype.sortFilterListByActive = function () {
    let scrollPosition = [];
    let scroll_content = $('.scrollbar-inner');
    scroll_content.each(function () {
        scrollPosition.push($(this).scrollTop())
    });
    scroll_content.each(function(index) {
        let wrapper = $(this);
        $(this).children('label').sort(SmartFilter.prototype.sortFilterListUp).sort(function(a, b) {
            return $(a).hasClass('mydisabled') - $(b).hasClass('mydisabled');
        }).each(function() {
            wrapper.append(wrapper.find('#'+$(this).attr('for')));
            wrapper.append(this);
        });
    });

    $('.in-in-left').children('.outer-color').sort(this.sortFilterListUp).sort(this.sortFilterListDown).each(function() {
        $(this).parent().append($(this));
    });

    $('.storages-list').children('li').sort(this.sortFilterListUp).sort(this.sortFilterListDown).each(function() {
        $('.storages-list').append($(this));
    });
    scroll_content.each(function (index) {
        $(this).scrollTop(scrollPosition[index]);
    });
};

SmartFilter.prototype.changedPriceFilter = function (input) {
    clearTimeout(this.timerChangedPriceFilter);
    let that = this;
    this.timerChangedPriceFilter = setTimeout(function () {
        $('.filter-btn-loader').show();
        smartFilter.click(input);
        smartFilter.doClick(that.getUrl(), 'filter');
    }, 1000);
};

SmartFilter.prototype.clickOnSectionFilter = function (sectionInFilter) {
    if ($(sectionInFilter).parent().hasClass('in-left-catalog--no-toggle')) {
        return;
    }
    $(sectionInFilter).toggleClass('active-name-h3');
    $(sectionInFilter).next('.in-in-left').toggle('fast');
    $(sectionInFilter).find(".plus").toggle(0);
    $(sectionInFilter).find(".minus").toggle(0);
    if ($('.podlozhka').css('display') === 'block' && $(sectionInFilter).hasClass('active-name-h3')){
        $('.js-filter-col').animate({
            scrollTop: sectionInFilter.offsetTop - 45
        }, 800);
    }
}

var smartFilter = new SmartFilter();


function JsPaginate() {}

JsPaginate.prototype.change = function (select) {
    var url = $(select).val();
    history.replaceState({}, '', url);
    location.reload();
};

var jsPaginate = new JsPaginate();


var swiperContainer = document.querySelector('.swiper-container');

if (swiperContainer) {
    var mySwiper = new Swiper(swiperContainer, {
        speed: 100,
        spaceBetween: 15,
        freeMode: true,
        mousewheel: {
            releaseOnEdges: true,
        },
        slidesPerView: 'auto',
        scrollbar: {
            el: '.swiper-scrollbar',
            hide: false,
            draggable: true
        },
        navigation: {
            nextEl: '.tags-arrow--next',
            prevEl: '.tags-arrow--prev',
            disabledClass: 'tags-arrow--disabled'
        },
    });
}

function truncateItemTitle () {
    if ($(window).width() < 768) {
        $('.card__title').ellipsis({
            lines: 3,
            responsive: true
        });
    } else {
        $('.card__title').ellipsis({
            lines: 2,
            responsive: true
        });
    }
}

//хендлеры статичной части витрины
truncateItemTitle();
$(window).on('resize', truncateItemTitle);

$('.js-change-sort').on('change', function () {
    let sort = $(this).val();
    smartFilter.setSort(sort);
    saveSettingsInCookie(sort);
});

$('.sort__item').on('click', function () {
    $('.sort__text').removeClass('sort__text--active');
    let sort = $(this).data('sort');
    smartFilter.setSort(sort);
    $(this).find('.sort__text').addClass('sort__text--active');
    saveSettingsInCookie();
});

$('.catalog-sort--mobile-icon').on('click', function () {
    $(this).toggleClass('active');
    $('.catalog__sort--mobile').toggleClass('catalog-sort--show');
});

$('.catalog-change-image').on('click', function () {
    let card_img_pic = $('.card__img-pic:not(.pic-one)');
    $(this).toggleClass('active');
    card_img_pic.toggleClass('pic-active');
    card_img_pic.toggleClass('pic-hide');
    saveSettingsInCookie();
});

$('.js-filter-button-reset').on('click', function (e) {
    e.preventDefault();
    smartFilter.clearFilter();
    saveSettingsInCookie();
    // Показываем лоадеры для фильтров
    $('.lds-ring-container').show();
    $('.filter-btn-loader').show();

    $('.filters__btn')
        .addClass('filters__btn--disabled')
        .prop('disabled', true);
    $('.filter-status-area')
        .text('показано');
    $('.filter-reset-btn')
        .addClass('filter__disabled-reset-btn')
        .prop('disabled', true);
    $('.filter__status-text-btn')
        .prop('disabled', true);

    SmartFilter.prototype.applyFilter = true;
    SmartFilter.prototype.doClick(SmartFilter.prototype.getUrl('reset'),'reset');
});

$('.js-filter-button-submit').on('click', function (e) {
    e.preventDefault();
    $('.lds-ring-container').show();
    $('.filter-btn-loader').show();
    $('.filters__btn')
        .addClass('filters__btn--disabled')
        .prop('disabled', true);
    $('.filter-reset-btn')
        .addClass('filter__disabled-reset-btn')
        .prop('disabled', true);
    if(SmartFilter.prototype.currentAjax){
        SmartFilter.prototype.applyFilter = true;
    }else {
        smartFilter.updateCatalog(smartFilter.getUrl(), {},'filter');
    }
    if ($(this).hasClass('js-filter-button-mobile') || $('.podlozhka').css('display') === 'block') {
        smartFilter.hideFilter();
    }
});



function saveSettingsInCookie(sortMobile){
    let sort = $('html').find('.sort__items').find('.sort__text--active').parent().data('sort');
    if (sortMobile){
        sort = sortMobile;
    }
    let grid = $('html').find('.view__item--active').data('viewType');
    let view = $('html').find('.catalog-change-image').hasClass('active');
    let locationFilter = $('#from_default_loc').prop('checked');
    document.cookie = 'user_settings=' + sort + '~' + view + '~' + grid + '~' + locationFilter + ';domain=' + currentHost + ';path=/;max-age=2592000;';
}

$('.in-left-catalog').last().addClass('in-left-catalog--last');

$(window).on('scroll', function () {
    if ( $(window).scrollTop() > $('div.cards').offset().top - 50){
        $('.catalog__filter-toggle--device').css('padding','13px');
        $('.filter-toggle__text--mobile').css('display','none');
        $('.js-filter-toggle-mobile').css('position','fixed');
        $('.js-filter-toggle-mobile').css('bottom','1rem');
        $('.js-filter-toggle-mobile').css('z-index','100');
    } else {
        $('.catalog__filter-toggle--device').css('padding','8px');
        $('.filter-toggle__text--mobile').css('display','inline-flex');
        $('.js-filter-toggle-mobile').css('position','relative');
        $('.js-filter-toggle-mobile').css('bottom','');
        $('.js-filter-toggle-mobile').css('z-index','');
    }
});

function resetHandlers() {
    $('.clear-section').on('click', function (e) {
        e.stopImmediatePropagation();
        let $filterSection = $(e.target).closest('.in-left-catalog');
        $('.filter-btn-loader').show();
        smartFilter.resetFilterSection($filterSection);
    });

    $('.storage_search').on('keyup', function () {
        let that = $(this);
        let text = that.val().toLowerCase();
        let $list = that.siblings('.storages-list');

        $list.find('li').hide();
        $list.find('label').each(function() {
            if ($(this).find('.storage-name').text().toLowerCase().indexOf(text) > -1 || $(this).find('.storage-address').text().toLowerCase().indexOf(text) > -1) {
                $(this).closest('li').show();
            }
        });
    });

    // событие для чекбоксов типа изделия
    $(".all-type").on("change", function () {
        let that = $(this);
        that.prop("checked", this.checked);
        that.siblings('.filter__product-list').find('.type').prop("checked", this.checked);
        smartFilter.click(this)
    });

    // событие для чекбоксов вида изделия
    $(".type").on("change", function () {
        let that = $(this);
        let sectionId = that.data('section');
        that.closest('.filter__type-list').find('.all-type[data-id="' + sectionId + '"]').prop("checked", $('input[data-section="' + sectionId + '"]:checked').length > 0);
        if($('.type.checkbox_size[data-section="' + sectionId + '"]:checked:not([data-id="NONAME"])').length == $('.type.checkbox_size[data-section="' + sectionId + '"]:not([data-id="NONAME"])').length){
            $('.type.checkbox_size[data-section="' + sectionId + '"][data-id="NONAME"]').prop("checked", true);
        } else {
            $('.type.checkbox_size[data-section="' + sectionId + '"][data-id="NONAME"]').prop("checked", false);
        }
        smartFilter.click(this)
    });

    //выбор вообще всех чекбоксов в блоке
    $(".general-all").on("change", function () {
        let that = $(this);
        that.closest('.js-filter-box').find('.type').prop("checked", this.checked);
        that.closest('.js-filter-box').find('.all-type').prop("checked", this.checked);
        smartFilter.click(this)
    });

    //активирует главный чекбокс если все чекбоксы в блоке отмечены, и снимает, если сняли один из вложенных
    $('.js-filter-box').on("change", function () {
        let that = $(this);
        let allChecked = that.find('input:not(.general-all):not(:checked)').length === 0;
        that.find('.general-all').prop("checked", allChecked);
    });

    $('.name-h3').click(function() {
        smartFilter.clickOnSectionFilter(this);
    });

    // $('.scrollbar-inner').scrollbar({
    //     disableBodyScroll: false
    // });
}

$(document).ready(function() {
    let params = SmartFilter.prototype.processSearch();
    params.getFilters = 'Y';
    BX.ajax.post($(location).attr('href'), params, function (data) {
        $(SmartFilter.prototype.filter).html($(data).find(SmartFilter.prototype.filter).html());
        resetHandlers();
        smartFilter.sortFilterListByActive();
        smartFilter.setFilterButtonsStyle('new');
        $('.name-h3').each(function () {
            if ($(this).hasClass('active-name-h3')) {
                smartFilter.clickOnSectionFilter(this);
            }
        });
        $('.in-left-catalog').last().addClass('in-left-catalog--last');
        $('.lds-ring-container-first').css('display', 'none');
    });
});

$('a[class="card__img"]').on('click', function () {
    let parent = $(this).parents('.cards__item');

    if (parent) {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'event':'MTRENDO',
            'eventCategory': 'EEC',
            'eventAction':'impressionClick',
            'eventLabel': parent.data('prod-name'),  // data-prod-name
            'product-list': parent.data('prod-list'), // data-prod-list
            'products': [{
                'name': parent.data('prod-name'),  // data-prod-name
                'id': parent.data('prod-id'),   // data-prod-id
                'articul': parent.data('prod-articul'), // data-prod-articul
                'price': parent.data('prod-price'),  // data-prod-price
                'category': parent.data('prod-category'), // data-prod-category
                'list': parent.data('prod-list'), // data-prod-list
                'variant':  parent.data('prod-variant'), // data-prod-variant
                'brand': parent.data('prod-brand'),  //  Бренд товара data-prod-brand
                'top-material': parent.data('prod-top-material'),  //  Материал верха data-prod-top-material
                'lining-material': parent.data('prod-lining-material'), //Материал подкладки data-prod-lining-material
                'season': parent.data('prod-season'),  //  Сезон data-prod-season
                'collection': parent.data('prod-collection'),  //  Коллекция data-prod-collection
                'position': parent.data('prod-position') // data-prod-position
            }]
        });
    }
})

$(document).ready(function () {
    $('.props_icon_img').on('click', function () {
        var dd = $(this).next().html();
        Popup.show('</br>' + '<div style="text-align:center">' + dd + '</div>');
    });
});