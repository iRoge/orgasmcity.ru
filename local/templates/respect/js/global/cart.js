(function() {
    window.Cart = (function() {
      Cart.threshold = 100;
  
      function Cart() {
        this._element = $('<a class="cart-button" href="/cart">').appendTo($('body'));
        this._element.append($('<img class="cart-button__icon" src="/local/templates/respect/img/svg/cartWhite.svg" alt="Корзина" width="26" height="auto">'));
        $('<span class="cart-button__counter-outer"></span>').appendTo(this._element).append($('<span class="cart-button__counter"></span>'));
        $(window).on('scroll', _.bind(this._scrollHandler, this));
        $('.shortcut-informer.count').bind("DOMSubtreeModified",function(){
          if(($(window).scrollTop() >= Cart.threshold) && $('.shortcut-informer.count').html() == 1) {
            $('.cart-button').addClass('cart-button--visible');
          }
          $('.cart-button__counter').text($('.shortcut-informer.count').html());
        });
        this._scrollHandler();
      }
      Cart.prototype._scrollHandler = function(event) {
        if ($(window).scrollTop() >= Cart.threshold && $('.shortcut-informer.count').html() !== '0') {
          return this._element.addClass('cart-button--visible'), this._element.find('.cart-button__counter').text($('.shortcut-informer.count').html());
        } else {
          return this._element.removeClass('cart-button--visible');
        }
      };
  
      return Cart;
  
    })();
  
    $(function() {
      return window.Cart = new Cart();
    });
  
  }).call(this);