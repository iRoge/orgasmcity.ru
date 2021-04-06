$(function () {
    var change_template_btns = $('.js-change-template');
    var two_column_change = $('.tiles-big-a');
    var one_column_change = $('.tiles-square-a');
    var use_filter = false;

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.hasOwnProperty('url') && -1 !== settings.url.indexOf('set_filter=Y')) {
            use_filter = true;
        } else if (settings.hasOwnProperty('data') && -1 !== settings.data.indexOf('del_filter=Y')) {
            use_filter = false;
        }
        window.changeNameSel();
    });
    $(document).ready(function (){
        window.changeNameSel();
    });
    $('.tiles-icons').on('click', function(){
        two_column_change.toggleClass('active');
        one_column_change.toggleClass('active');
        two_column_change.toggle();
        one_column_change.toggle();
        window.changeNameSel();
    });
    window.changeNameSel = function changeNameSel() {
        if (two_column_change.hasClass('active')) {
            $('.name-sel').addClass("name-sel-hidden");
            $('.mobile-change-columns').removeClass('change-col-sm-12');
            $('.mobile-change-columns').addClass("change-col-sm-6");
        } else if (one_column_change.hasClass('active')) {
            $('.name-sel').removeClass("name-sel-hidden");
            $('.mobile-change-columns').removeClass('change-col-sm-6');
            $('.mobile-change-columns').addClass("change-col-sm-12");
        }
    }

    change_template_btns.on('click', function (e) {
        e.preventDefault();

        var btn = $(this);
        if (btn.hasClass('active')) {
            return false;
        }

        change_template_btns.removeClass('active');
        btn.addClass('active');

        $("div.js-catalog-section div.col-sm-6").toggleClass("col-12 col-md-4 col-lg-3");
    });

    $(document).on('catalog-load', function () {
        $('.js-catalog-section').addClass('catalog-preloader');
    });
	
	$(function() {
		$('.right-catalog').on('click', '[data-listid] a', function() {
			history.replaceState({listid:$(this).closest('[data-listid]').attr('data-listid')}, document.title, window.location.href);
		});

		if (history.state && history.state.hasOwnProperty('listid')) {
			var $listItem = $('[data-listid="'+history.state.listid+'"]');
			if ($listItem.length) {
				setTimeout(function() {
					$('html, body').animate({ scrollTop: $listItem.offset().top}, 500);
				}, 1000);
			}
		}
	});
});