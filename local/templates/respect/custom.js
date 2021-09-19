function show_wait ($object) {
	$object.each (function (ob) {
		let that = $(this);
		that.css('position','relative');
		that.append ('<div class="js-waiter" style="position: absolute;width: 100%;height: 100%;left: 0;top: 0;opacity: 0.6;background-color: #FFF; z-index: 222"></div>');
	});
}

function hide_wait () {
	$('.js-waiter').remove();
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
			this.element.root.style.right = positionStep + 'rem';
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

		if (window.matchMedia('(max-width: 767px)').matches) {
			let whatsAppButtonElement = document.querySelector('.js-whatsapp');
	
			if (whatsAppButtonElement) {
				let whatsAppButton = new StickyButton(whatsAppButtonElement, 2);
	
				whatsAppButton.init();
			}
		}

		// ToTop \local\templates\respect\js\global\to-top.js

		// Jivosite

		if (window.matchMedia('(max-width: 767px)').matches) {
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

function reinitPopupWrapper()
{
	window.currentPage.init($('.popup'));
	initFileField($('.popup'));
}

function updateSmallBasket(diff)
{
	diff = parseInt(diff) || 0;
	let elCount = $("#basket-small .count");
	let count = parseInt(elCount.html());
	elCount.html(count + diff);
}

function respercEvent__add_to_cart()
{
	let popupContent = '<div class="product-preorder-success">'
		+ '<header>Товар добавлен в корзину</header>'
		+ '<div class="popup-footer">'
		+ '<button class="js-popup-close button button--xxl button--primary button--outline button--blue">Продолжить покупки</button>'
		+ '<div>&nbsp;</div>'
		+ '<a class="button button--xxl button--primary button--outline button--purple" href="/cart/">Оформить заказ</a>'
		+ '</div>'
		+ '</div>';

	Popup.show(popupContent, {
		className: 'popup--feedback'
	});
}

$(document).ready(function () {
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

	/* page events */
	if ('#feedback' == window.location.hash) {
		$('.js-feedback').trigger('click');
	}

	$(document).on('change', 'input[type="file"]', function(e) {
		$('body').addClass('disable--popup-overlay-close');
		setTimeout(function() {
			$('body').removeClass('disable--popup-overlay-close');
		}, 500);
	});

	$('.js-cart-redirect').on('click', function(e) {
		if ($(this).attr('value') == 'В корзине') {
			e.preventDefault();
			document.location.href = '/cart/';
		}
	});
});
