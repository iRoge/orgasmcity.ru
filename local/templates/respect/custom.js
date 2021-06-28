function show_wait ($object) {
	$object.each (function (ob) {
		let that = $(this);
		that.css('position','relative');
		that.append ('<div class="js-waiter" style="position: absolute;width: 100%;height: 100%;left: 0px;top: 0px;opacity: 0.6;background-color: #FFF; z-index: 222"></div>');
	});
}

function hide_wait () {
	$('.js-waiter').remove();
}

function sendYandexMetrkiaGoal(goalName)
{
	let params = arguments[1] || {};

	if ('undefined' != typeof window.yaCounter18950356) {
		yaCounter18950356.reachGoal(goalName, params);
	} else {
		window.respectMetrkiaGoal = window.respectMetrkiaGoal || [];
		window.respectMetrkiaGoal.push({name: goalName, params: params});
	}
}

(function() {
    var StickyButton = function (root, priority, threshold, observe) {
        this.element = {
            root: root,
        }

        this.prop = {
            priority: priority,
        }

		this.state = {
			visible: true
		};
		
		if (threshold) {
			this.prop.threshold = threshold;
			this.state.visible = false;
		}

		if (observe) {
			this.prop.observe = observe;
		}

        if (!StickyButton.instance) {
            StickyButton.instance = [];
        }

        StickyButton.instance.push(this);
	}
	StickyButton.update = function () {
		StickyButton.instance.forEach(function (element) {
			element._updatePosition();
		});
	}

    StickyButton.prototype.init = function () {
		this.element.root.style.transition = 'opacity 0.2s linear 0.2s';

        window.addEventListener('scroll', this._handleScroll.bind(this));
		StickyButton.update();

		if (this.prop.observe) {
			let observer = new MutationObserver(this._observeMutation.bind(this));
			observer.observe(this.element.root, {attributes: true});
		}


    }

    // Приватные методы

	StickyButton.prototype._getClosestPriorityButtons = function (direction) {
		let instances = StickyButton.instance.slice().filter((function (currentElement) {
			return currentElement !== this
		}).bind(this));

		if (instances.length) {
			switch (direction) {
				case 'left': {
					let lefts = instances.filter((function (element) {
						return element.prop.priority > this.prop.priority;
					}).bind(this));

					return lefts;
				}
	
				case 'right': {
					
					let rights = instances.filter((function (element) {
						return element.prop.priority < this.prop.priority;
					}).bind(this));

					return rights;
				}
			}
		} else {
			return false;
		}
    }

    StickyButton.prototype._updatePosition = function () {
		let positionStep = 1;

		this.element.root.style.top = 'auto';
		this.element.root.style.bottom = '1rem';
		this.element.root.style.left = 'auto';

		let rightClosestPriorityButtons = this._getClosestPriorityButtons('right');

		if (rightClosestPriorityButtons.length) {
			positionStep = 6;
			let right = 1;

			rightClosestPriorityButtons.forEach(function (element) {
                if (element.state.visible) {
					right += positionStep;
                }
            });

			this.element.root.style.right = right + 'rem';
		} else {
			this.element.root.style.right = 1 + 'rem';
		}
	}
	
	StickyButton.prototype._observeMutation = function () {
		this._updatePosition();
	}
    
    // Обработчики событий
    
    StickyButton.prototype._handleScroll = function () {
        if (this.prop.threshold) {
			if (document.documentElement.scrollTop > this.prop.threshold) {
				this.state.visible = true;
			} else {
				this.state.visible = false;
			}
		}
		
		this._updatePosition();
	}
	
	window.StickyButton = StickyButton;

	// Инициализация

	$(document).ready(function () {
		// WhatsApp

		if (window.matchMedia('(max-width: 768px)').matches) {
			let whatsAppButtonElement = document.querySelector('.js-whatsapp');
	
			if (whatsAppButtonElement) {
				let whatsAppButton = new StickyButton(whatsAppButtonElement, 2);
	
				whatsAppButton.init();
			}
		}

		// ToTop \local\templates\respect\js\global\to-top.js

		// Jivosite

		if (window.matchMedia('(max-width: 768px)').matches) {
			window.jivo_onLoadCallback = function () {
				let jivositeButtonElement = document.querySelector('.__jivoMobileButton');

				if (jivositeButtonElement) {
					let jivositeButton = new StickyButton(jivositeButtonElement, 3, null, true);

					jivositeButton.init();
				}
			}
			window.jivo_onClose = function () {
				setTimeout(function () {
					let jivositeButtonElement = document.querySelector('.__jivoMobileButton');

					if (jivositeButtonElement) {
						let jivositeButton = new StickyButton(jivositeButtonElement, 3, null, true);

						jivositeButton.init();
					}
				}, 200);
			}
		}


		// // Mango
		// if (window.matchMedia('(max-width: 767px)').matches) {
		// 	let mangoButtonElement = document.querySelector('.mango-false-button');
		// 	if (mangoButtonElement) {
		// 		let mangoButton = new StickyButton(mangoButtonElement, 4, null, true);
		// 		mangoButton.init();
		// 	}
		// }
		window.StickyButton.update()

	});
})();

$(window).on('pageshow site-ajax', function() {
	if ('undefined' != typeof window.respectMetrkiaGoal) {
		for (var i = 0, total = respectMetrkiaGoal.length; i < total; i ++) {
			var goalName = respectMetrkiaGoal[i];
			var goalParams = {};

			if ('object' === typeof respectMetrkiaGoal[i]) {
				goalName = respectMetrkiaGoal[i].name;
				goalParams = respectMetrkiaGoal[i].params;
			}


			if ('function' === typeof window['respercEvent__'+goalName]) {
				window['respercEvent__'+goalName]();
			}
		}
		respectMetrkiaGoal = [];
	}
});

(function($) {
	$(function() {
		$('.js-feedback').on('click', function(e) {
			e.preventDefault();

			$.get('/local/ajax/feedback.php', function(response) {
                Popup.show($(response), {
					title: 'Обратная связь',
					className: 'popup--feedback',
                    onShow: function (popup) {
						initFileField($('.popup'));
						BX.addCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                    },
                    onClose: function (popup) {
						BX.removeCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                    }
                });
            });
		});

		if ('undefined' !== typeof RESPECT_OPTIONS) {
			// banner popup
			if (RESPECT_OPTIONS.POPUP_BANNER_PATH.length && !BX.getCookie('RESPECT_HIDE_BANNER')) {
				let utmList = !RESPECT_OPTIONS.POPUP_BANNER_UTM ? [] : RESPECT_OPTIONS.POPUP_BANNER_UTM.split(',');
				respectOptionsBannersProcess(RESPECT_OPTIONS.POPUP_BANNER_PATH, utmList);
			}

			// fast order popup
			if (1 == RESPECT_OPTIONS.POPUP_FO_ACTIVE && ! $('body').hasClass('page--cart')) {
				let initPopupTimer = RESPECT_OPTIONS.POPUP_FO_PAGE;
				if ($('body').hasClass('page--list') || $('body').hasClass('page--product')) {
					initPopupTimer = RESPECT_OPTIONS.POPUP_FO_CATALOG;
				}
				let popupOpened = false;
				let popupTimer = initPopupTimer;

				$('body').mousemove(function(event) {
					popupTimer = initPopupTimer;
				});

				setInterval(function() {
					popupTimer --;

					if (0 === popupTimer && false == popupOpened) {
						let basketCount = parseInt($('#basket-small span.shortcut-informer').text());
						
						if (basketCount && (0 == RESPECT_OPTIONS.POPUP_FO_ONCE || !BX.getCookie('RESPECT_POPUP_FO_SHOWN'))) {
							BX.setCookie('RESPECT_POPUP_FO_SHOWN', 'Y', {expires: 43200});

							$.ajax({
								method: 'get',
								url: '/cart/?action=get_one_click',
								success: function (response) {
									response = $(response);
									response.find('.column-5.column-md-2').attr('class', 'column-6 pre-2');
									response.find('.widget__bonus').parent().remove();

									return Popup.show(response, {
										title: 'В вашей корзине находятся товары, вы можете их оформить оставив только номер телефона:',
										className: 'popup--fast-order',
										onShow: function (popup) {
											popupOpened = true;
										},
										onClose: function (popup) {
											popupOpened = false;
										}
									});
								}
							});
						}
					}
				}, 1000);
			}
		}

		$('.shop-card__bonus--f').on('click', function(e) {
			e.preventDefault();
			respercEvent__shop_bonus_click($(this));
		});
		
        $('.js-tender-form').on('click', function(e) {
            e.preventDefault();

			let btn = $(this);
			let tenderId = btn.data('tender-id');


            $.get('/local/ajax/tender_form.php?id='+tenderId, function(response) {
                Popup.show($(response), {
                    title: 'Оформить заявку',
                    className: 'popup--feedback',
                    onShow: function (popup) {
                        BX.addCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                    },
                    onClose: function (popup) {
                        BX.removeCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                    }
                });
            });
		});

		$('.js-popup-open[href]').on('click', function(e) {
			e.preventDefault();
			let $container = $($(this).attr('href'));

            if ($container.length) {
                Popup.show($container.html(), {
					title: ($(this).attr('title') || ''),
					className: ($(this).attr('data-class') || '')
				});
            }
        });
		
		/* metrika */
		checkPageMetrkiaGoals();

		/* page events */
		if ('#feedback' == window.location.hash) {
			$('.js-feedback').trigger('click');
		}
	});

	function reinitPopupWrapper() {
		window.currentPage.init($('.popup'));
		initFileField($('.popup'));
	}

	$(document).on('change', 'input[type="file"]', function(e) {
		$('body').addClass('disable--popup-overlay-close');
		setTimeout(function() {
			$('body').removeClass('disable--popup-overlay-close');
		}, 500);
	});
})(jQuery);

function updateSmallBasket(diff) {
    diff = parseInt(diff) || 0;
    elCount = $("#basket-small .count");
	let count = parseInt(elCount.html());
    elCount.html(count + diff);
}
function respercEvent__user_auth()
{
	if (0 < parseInt($('#basket-small .shortcut-informer').text())) {
		$(document).trigger('update-basket-small');
	}
}
function respercEvent__new_register()
{
	$.getJSON('/local/ajax/popup_register.php', function(response) {
		if (! response.hasOwnProperty('ITEMS') || ! response.ITEMS.length) {
			return false;
		}

		let popupItem = response.ITEMS[0];
		let popupContent = '';
		let popupClass = 'popup--preorder-success';

		if (popupItem.hasOwnProperty('PREVIEW_PICTURE') && popupItem.PREVIEW_PICTURE) {
			popupContent = '<img src="'+popupItem.PREVIEW_PICTURE.SRC+'" alt="'+popupItem.NAME+'">';

			if (popupItem.hasOwnProperty('DISPLAY_PROPERTIES') && 
				popupItem.DISPLAY_PROPERTIES.hasOwnProperty('URL') && 
			    '' != popupItem.DISPLAY_PROPERTIES.URL.VALUE) {
				popupContent = '<a href="'+popupItem.DISPLAY_PROPERTIES.URL.VALUE+'">'+popupContent+'</a>'
			}
			popupClass = 'popup--banner';
		} else {
			let text = popupItem.PREVIEW_TEXT;
			if (popupItem.hasOwnProperty('PREVIEW_TEXT_TYPE') && 'text' == popupItem.PREVIEW_TEXT_TYPE) {
				text = '<p>'+text+'</p>';
			}

			popupContent = '<div class="product-preorder-success">'+'<header>'+popupItem.NAME+'</header>'+ text+'</div>';
		}

		Popup.show(popupContent, {
			className: popupClass
		});
	});
}
function respercEvent__add_to_cart()
{
	let popupContent = '<div class="product-preorder-success">'
	+ '<header>Товар добавлен в корзину</header>'
	+ '<footer>'
	+ '<button class="js-popup-close button button--xxl button--primary button--outline button--blue">Продолжить покупки</button>'
	+ '<div>&nbsp;</div>'
	+ '<a class="button button--xxl button--primary button--outline button--orange" href="/cart/">Оформить заказ</a>'
	+ '</footer>'
	+ '</div>';

	Popup.show(popupContent, {
		className: 'popup--feedback'
	});
}

// скрипт редиректа по нажатие на кнопку в корзине

$('.js-cart-redirect').on('click', function(e) {
	if ($(this).attr('value') == 'В корзине') {
		e.preventDefault();
		document.location.href = '/cart/';
	}
});

// конец скрипта кнопки

function respercEvent__shop_bonus_click($elem)
{
	var phoneNember = '';

	if ($elem.closest('.shop-card').find('.shop-card__phones p').length) {
		phoneNember = $elem.closest('.shop-card').find('.shop-card__phones p').html().split('<br>')[0];
	} else if ($elem.closest('.shop-header').find('.shop-card__phones').length) {
		phoneNember = $elem.closest('.shop-header').find('.shop-card__phones:eq(0)').text();
	}

	var linkNumber = phoneNember ? '<a href="tel:' + $.trim(phoneNember) + '">' + $.trim(phoneNember).replace(/ /g, '&nbsp;') + '</a>' : '';

	var popupContent = '<div class="product-preorder-success">'
	+ '<h3>Дисконтная программа</h3>'
	+ '<p>В магазине действует ДИСКОНТНАЯ программа, бонусные баллы не принимаются.</p>'
	+ (phoneNember ? '<p>Подробнее об условиях дисконтной программы вы можете узнать по телефону '
			+ linkNumber +'</p>' : '')
	+ '</div>';

	Popup.show(popupContent, {
		className: 'popup--feedback popup--discont'
	});
}
function initFileField($wrapper)
{
	$('.likee-file-upload', $wrapper).each(function() {
		var _this = this;
		
		$('.button', this).on('click', function() {
			$('input[type="file"]', _this).focus().trigger('click');
		});
		
		$('input[type="file"]', this).on('change', function(e) {
			var fileName = e.target.value.split( '\\' ).pop();
			
			if (fileName) {
				$('.js-file-upload-info', _this).html('Файл : '+fileName);
			}
		});
	});
}

function respectOptionsBannersProcess(banners, utmList) {
	var isGlobal = false;
	var canRequest = false;

	if (-1 !== $.inArray(window.location.pathname, banners)) {
		canRequest = true;
	} else if (-1 !== $.inArray('', banners) && !BX.getCookie('RESPECT_GLOBAL_BANNER_HIDE')) {
		isGlobal = true;
		canRequest = true;
	}

	if (isGlobal && utmList.length && window.location.search) {
		for (var i = 0; i < utmList.length; i ++) {
			var utmString = $.trim(utmList[i]);
			if (utmString && -1 !== window.location.search.indexOf(utmString)) {
				canRequest = false;
			}
		}
	}

	if (canRequest) {
		$.getJSON('/local/ajax/popup_banner.php?url=' + window.location.pathname, function (response) {
			if (!$(response.html).length) {
				return;
			}

			setTimeout(function () {
				Popup.show($(response.html), {
					className: 'popup--banner' + (response.use_subscribe ? ' popup--banner-with-subscribe' : '')
				});

				if (response.use_subscribe) {
					var subscribe = new window.Subscribe('#subscribe-popup');
				}

				if (response.js) {
					eval(response.js);
				}

				if (isGlobal) {
					var globalExpires = response.interval;

					BX.setCookie('RESPECT_GLOBAL_BANNER_HIDE', 'Y', {
						expires: globalExpires,
						path: '/'
					});

					var bannersQueue = BX.getCookie('RESPECT_GLOBAL_BANNER_QUEUE') ? BX.getCookie('RESPECT_GLOBAL_BANNER_QUEUE').split('|') : [];
					bannersQueue = bannersQueue.filter(function (item) {
						return item != response.id
					});
					bannersQueue.push(response.id);

					BX.setCookie('RESPECT_GLOBAL_BANNER_QUEUE', bannersQueue.join('|'), {
						expires: 2592000,
						path: '/'
					});
				} else {
					BX.setCookie('RESPECT_HIDE_BANNER', 'Y', {
						expires: 2592000,
						path: window.location.pathname
					});
				}
			}, response.duration * 1000);
		});
	}
}

function checkPageMetrkiaGoals()
{
	var goalTitle = $('.js-goal-name').text() || '';
	if ('' == goalTitle) {
		goalTitle = $('h1').clone().find('span').remove().end().text();
	}
	goalTitle = $.trim(goalTitle);

	if ($('body').is('.page--group')) {
		goalTitle += ' - '+ window.location.pathname.split('/')[2];
	}
}

$(document).ready(function () {
	$('.order-info__btn').on('click', function () {
        $.fancybox.open([$('.order-info__modal')]);
    })
    $('.order-info__submit').on('click', function () {
    	//Получаем номер заказа и номер телефона из формы
        var parent_section = $(this).parent();
        var order_number = parent_section.find($('.order-info__input[name=order_number]')).val();
		var order_phone = parent_section.find($('.order-info__input[name=order_phone]')).val();
        var captcha_word = parent_section.find($('.static_input[name=captcha_word]')).val();
        var captcha_code = parent_section.find($('.static_input[name=captcha_code]')).val();

		//Создаем массив с ошибками
		var arerror="";
		if(!order_phone){
            arerror = arerror+'Заполните поле "Номер телефона"';
		}
		if(!order_number){
            arerror = arerror+'<br />Заполните поле "Номер заказа"';
		}
		if(!captcha_word){
            arerror = arerror+'<br />Заполните поле "Текст с картинки"';
		}

		//Проверяю заполнили ли поля в форме и отправляем ajax запрос в файл
		if(order_phone && order_number && captcha_word){
			$.post("/local/templates/respect/ajax/order_status.php",
				{
					order_number: order_number,
					order_phone: order_phone,
					captcha_code: captcha_code,
					captcha_word: captcha_word,
				},
				//выводим результат запроса
				function(data){
					parent_section.find($(".order-info__result")).html(data);
					//Если заказ не найден, то для следующей попытки выводим поле с капчей
					if(data.search('не найден')!=-1 || data.search('картинки')!=-1 || data.search('не соответствует')!=-1){
						parent_section.find($('.order-info__captcha')).show();
						$.getJSON('/local/templates/respect/ajax/reload_captcha.php', function(data) {
							parent_section.find($('.order-info_captcha-img')).attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
							parent_section.find($('.static_input[name=captcha_code]')).val(data);
						});
						return false;
					}
				}
			);
		}else{
			//выводим массив с ошибками
			parent_section.find($(".order-info__result")).html(arerror);
		}

    });
});
