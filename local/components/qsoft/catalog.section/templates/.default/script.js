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

    // Собираем обычные фильтры
    $('.in-in-left').each(function () {
        let filterName = $(this).data('filter-name');
        if (typeof filterName === 'undefined') {
            return;
        }
        formData[filterName] = [];
        $(this).find(':checkbox:checked').each(function () {
            formData[filterName].push(encodeURIComponent($(this).val()));
        });
    });

    // Собираем циферные фильтры
    $('input.js-number-filter').each(function () {
        let filterName = $(this).prop('id');
        if (typeof filterName === 'undefined') {
            return;
        }
        formData[filterName] = $(this).val();
    });
    return formData;
};

SmartFilter.prototype.getQuery = function () {
    let formData = this.getFormData();
    let query = window.location.search;
    let setFilter = false;
    let params = [];
    let newQuery = '';

    if (query !== '') {
        let tempParams = query.replace('?', '').split('&');
        for (let i = 0; i < tempParams.length; i++) {
            let keyval = tempParams[i].split("=");
            if (keyval[0] !== 'set_filter') {
                params[keyval[0]] = decodeURIComponent(keyval[1]);
            }
        }
    }

    for (let filterName in formData) {
        if (typeof formData[filterName] === 'object') {
            formData[filterName] = formData[filterName].join(';');
        }
        if (formData[filterName] !== '') {
            setFilter = true;
        }
        if (params[filterName] !== undefined || formData[filterName] !== '') {
            params[filterName] = formData[filterName];
        }
    }

    setFilter ? newQuery = 'set_filter=Y' : '';

    for (let param in params) {
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
        } else {
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
    this.setFilterSectionStyle($filterSection);
    SmartFilter.prototype.doClick(this.getUrl(),'filter');
};

SmartFilter.prototype.doClick = function (url,request) {
    let params = this.processSearch();
    if (request !== 'sort'){
        params.getFilters = 'Y';
    }

    SmartFilter.prototype.setFilterButtonsStyle(request);
    if (SmartFilter.prototype.currentAjax){//если какой-то запрос выполняется, ставим в очередь
        SmartFilter.prototype.nextAjax[0] = url;
        SmartFilter.prototype.nextAjax[1] = request;
    } else {
        $.ajax({
            method: 'get',
            url: url,
            data: params,
            success: function (data) {
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
                        $('#min_diameter').attr('placeholder', newFilter.find('#min_diameter').attr('placeholder'));
                        $('#max_diameter').attr('placeholder', newFilter.find('#max_diameter').attr('placeholder'));
                        $('#min_length').attr('placeholder', newFilter.find('#min_length').attr('placeholder'));
                        $('#max_length').attr('placeholder', newFilter.find('#max_length').attr('placeholder'));

                        SmartFilter.prototype.sortFilterListByActive();

                    }
                    $('.items-count').text(newFilter.find('.all-items-count').val());
                    if(SmartFilter.prototype.applyFilter){
                        SmartFilter.prototype.applyFilter = false;
                        SmartFilter.prototype.updateCatalog(url,params, request, data);
                    }
                    $('.filter-btn-loader').hide();
                }
                $('.lazy-img').lazyLoadXT();

            },
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
    $('.lazy-img').lazyLoadXT();
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
    btn = $(btn);
    let lds_ring = $('.lds-ring--settings');
    lds_ring.css('visibility', 'visible');
    $(".load-more-btn-main").hide();
    $(".load-more-btn-loader:not(.filter-btn-loader)").addClass("load-more-btn-loader-visible");

    $.ajax({
        method: 'get',
        url: btn.data('href'),
        data: { 'load_more': 'Y' },
        success: function (data) {
            $("html, body").animate({scrollTop: $('.catalog__main').offset().top - 55}, 500);
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
            $('.lazy-img').lazyLoadXT();
        },
    });
};

SmartFilter.prototype.goToPageNum = function (btn) {
    btn = $(btn);
    let lds_ring = $('.lds-ring--settings');
    lds_ring.css('visibility', 'visible');

    $.ajax({
        method: 'get',
        url: btn.data('url'),
        data: { 'load_more': 'Y' },
        success: function (data) {
            $("html, body").animate({scrollTop: $('.catalog__main').offset().top - 55}, 500);
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
            $('.lazy-img').lazyLoadXT();
        },
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
        filters_btn_reset.prop('disabled', false);
        filters_status_text_btn.prop('disabled', false);
        filters_status_text_btn.addClass('filter__status-text-btn-active');
        filters_btn_submit.removeClass('filters__btn--disabled').prop('disabled', false);
    }

    if (request === 'new') {
        if ($('.js-filter-wrapper').find('.in-left-catalog--checked').length == 0) {
            filter_status_area.text('показано')
            filter_reset_btn.addClass('filter__disabled-reset-btn').prop('disabled', true);
            filters_btn_reset.prop('disabled', true);
            filters_status_text_btn.prop('disabled', true);
            filters_status_text_btn.removeClass('filter__status-text-btn-active');
            filters_btn_submit.addClass('filters__btn--disabled').prop('disabled', true);
        } else {
            filter_status_area.text('показано')
            filter_reset_btn.removeClass('filter__disabled-reset-btn').prop('disabled', false);
            filters_btn_reset.prop('disabled', false);
            filters_status_text_btn.prop('disabled', true);
            filters_status_text_btn.removeClass('filter__status-text-btn-active');
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
        scrollTop: $('.catalog__main').offset().top - 55
    }, 800);
};

//Функция сортировки для поднятия активных элементов
SmartFilter.prototype.sortFilterListUp = function(a, b) {
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

function saveSettingsInCookie(sortMobile){
    let sort = $('html').find('.sort__items').find('.sort__text--active').parent().data('sort');
    if (sortMobile){
        sort = sortMobile;
    }
    let grid = $('html').find('.view__item--active').data('viewType');
    let locationFilter = $('#from_default_loc').prop('checked');
    document.cookie = 'user_settings=' + sort + '~' + grid + '~' + locationFilter + ';domain=' + currentHost + ';path=/;max-age=2592000;';
}

function resetHandlers() {
    $('.clear-section').on('click', function (e) {
        e.stopImmediatePropagation();
        let $filterSection = $(e.target).closest('.in-left-catalog');
        $('.filter-btn-loader').show();
        smartFilter.resetFilterSection($filterSection);
    });

    $('.name-h3').click(function() {
        smartFilter.clickOnSectionFilter(this);
    });

    // $('.scrollbar-inner').scrollbar({
    //     disableBodyScroll: false
    // });
}

$(document).ready(function() {
    $(document).on('click', '.js-filter-toggle', function() {
        let $filterToggle = $(this);
        let $filterCol = $('.js-filter-col');
        $filterToggle.toggleClass('filter-toggle--hidden');
        $filterCol.toggleClass('catalog__content-col--hidden');
    });
    $(document).on('click', '.js-filter-toggle-mobile', function() {
        $('.podlozhka').show();
        $('body').css('overflow', 'hidden');
        $('.js-filter-mobile-close').show();
        $(".js-filter-col").removeClass("catalog__content-col--sidebar");
    });
    $(document).on('click', '.js-filter-mobile-close', function() {
        hideFilter();
        $('body').css('overflow-y', '');
    });
    $(document).on('click', '.podlozhka', function() {
        hideFilter();
        $('body').css('overflow-y', '');
    });
    function hideFilter() {
        $(".js-filter-col").addClass("catalog__content-col--sidebar");
        $('.podlozhka').hide();
        $('body').css('overflow-y', '');
    }
    $(document).on('click', '.js-view-item', function() {
        let $viewItem = $(this);
        let isCurrentActive = $viewItem.hasClass('view__item--active');
        if (isCurrentActive) {
            return;
        }
        let $viewItemBox = $viewItem.closest('.js-view');
        let $viewItems = $viewItemBox.find('.js-view-item');
        let $cards = $('.js-cards');
        let viewType = $viewItem.data('view-type');
        $viewItems.removeClass('view__item--active');
        $viewItem.addClass('view__item--active');
        if (viewType === 'big') {
            $cards.find('.product-card').each(function() {
                $(this).removeClass('col-lg-3').removeClass('col-sm-4').removeClass('col-md-4').removeClass('col-xs-6');
                $(this).addClass('col-lg-4').addClass('col-sm-6').addClass('col-md-6').addClass('col-xs-12');
            });
        } else {
            $cards.find('.product-card').each(function() {
                $(this).removeClass('col-lg-4').removeClass('col-sm-6').removeClass('col-md-6').removeClass('col-xs-12');
                $(this).addClass('col-lg-3').addClass('col-sm-4').addClass('col-md-4').addClass('col-xs-6');
            });
        }
        saveSettingsInCookie();
    });

    let params = SmartFilter.prototype.processSearch();
    params.getFilters = 'Y';

    $.ajax({
        method: 'post',
        url: $(location).attr('href'),
        data: params,
        success: function (response) {
            $(SmartFilter.prototype.filter).html($(response).find(SmartFilter.prototype.filter).html());
            resetHandlers();
            smartFilter.sortFilterListByActive();
            smartFilter.setFilterButtonsStyle('new');
            $('.name-h3').each(function () {
                let sectionInFilter = $(this);
                if (sectionInFilter.hasClass('active-name-h3') && !sectionInFilter.parent().hasClass('subsections-block')) {
                    sectionInFilter.next('.in-in-left').toggle('fast');
                    sectionInFilter.find(".plus").toggle(0);
                    sectionInFilter.find(".minus").toggle(0);
                    if ($('.podlozhka').css('display') === 'block' && sectionInFilter.hasClass('active-name-h3')){
                        $('.js-filter-col').animate({
                            scrollTop: sectionInFilter.offset().top - 45
                        }, 800);
                    }
                    console.log(sectionInFilter.find('.filter-name').html());
                    console.log(sectionInFilter.next('.in-in-left').html());
                }
            });
            $('.lds-ring-container-first').css('display', 'none');
        },
    });

    if (targetDeviceType === 'mobile') {
        $('.props-icon-img').on('click', function () {
            var dd = $(this).next().html();
            Popup.show('</br>' + '<div style="text-align:center">' + dd + '</div>');
        });
    }

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

    $(window).on('resize', truncateItemTitle);

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
});