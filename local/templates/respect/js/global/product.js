(function() {
  window.ProductItem = (function() {
    function ProductItem(element) {
      this._el = $(element);
      this._el.data('control', this);
      $('.js-cart-button').on('click', this._buttonClickHandler.bind(this));
      this._colors();
    }
    ProductItem.prototype._colors = function() {
      return $('.products-item__colors a', this._el).on('click', _.bind(this._colorChangeHandler, this));
    };

    ProductItem.prototype._colorChangeHandler = function(event) {
      var colorLink, index, targetImage;
      colorLink = $(event.currentTarget);
      colorLink.siblings().removeClass('selected');
      colorLink.addClass('selected');
      index = colorLink.index();
      targetImage = $($('.products-item__image', this._el)[index]);
      targetImage.siblings().addClass('hidden');
      return targetImage.removeClass('hidden');
    };

    ProductItem.prototype._buttonClickHandler = function(event) {
      var button;
      button = $(event.currentTarget);
      button.text('Добавлено в корзину').removeClass('button--outline');
      return button.trigger('product:add-to-cart');
    };

    return ProductItem;

  })();

  $(function() {
    return $('.products-item').each(function(index, element) {
      return new window.ProductItem(element);
    });
  });

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicHJvZHVjdC5qcyIsInNvdXJjZXMiOlsicHJvZHVjdC5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQ0E7RUFBTSxNQUFNLENBQUM7SUFDRSxxQkFBQyxPQUFEO01BQ1gsSUFBQyxDQUFBLEdBQUQsR0FBTyxDQUFBLENBQUUsT0FBRjtNQUNQLElBQUMsQ0FBQSxHQUFHLENBQUMsSUFBTCxDQUFVLFNBQVYsRUFBcUIsSUFBckI7TUFDQSxDQUFBLENBQUUsaUJBQUYsQ0FBb0IsQ0FBQyxFQUFyQixDQUF3QixPQUF4QixFQUFpQyxJQUFDLENBQUEsbUJBQW1CLENBQUMsSUFBckIsQ0FBMEIsSUFBMUIsQ0FBakM7TUFFQSxJQUFDLENBQUEsT0FBRCxDQUFBO0lBTFc7OzBCQU9iLE9BQUEsR0FBUyxTQUFBO2FBQ1AsQ0FBQSxDQUFFLDBCQUFGLEVBQThCLElBQUMsQ0FBQSxHQUEvQixDQUFtQyxDQUFDLEVBQXBDLENBQXVDLE9BQXZDLEVBQWdELENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLG1CQUFSLEVBQTZCLElBQTdCLENBQWhEO0lBRE87OzBCQUdULG1CQUFBLEdBQXFCLFNBQUMsS0FBRDtBQUNuQixVQUFBO01BQUEsU0FBQSxHQUFZLENBQUEsQ0FBRSxLQUFLLENBQUMsYUFBUjtNQUNaLFNBQVMsQ0FBQyxRQUFWLENBQUEsQ0FBb0IsQ0FBQyxXQUFyQixDQUFpQyxVQUFqQztNQUNBLFNBQVMsQ0FBQyxRQUFWLENBQW1CLFVBQW5CO01BQ0EsS0FBQSxHQUFRLFNBQVMsQ0FBQyxLQUFWLENBQUE7TUFFUixXQUFBLEdBQWMsQ0FBQSxDQUFFLENBQUEsQ0FBRSx1QkFBRixFQUEyQixJQUFDLENBQUEsR0FBNUIsQ0FBaUMsQ0FBQSxLQUFBLENBQW5DO01BQ2QsV0FBVyxDQUFDLFFBQVosQ0FBQSxDQUFzQixDQUFDLFFBQXZCLENBQWdDLFFBQWhDO2FBQ0EsV0FBVyxDQUFDLFdBQVosQ0FBd0IsUUFBeEI7SUFSbUI7OzBCQVVyQixtQkFBQSxHQUFxQixTQUFDLEtBQUQ7QUFDbkIsVUFBQTtNQUFBLE1BQUEsR0FBUyxDQUFBLENBQUUsS0FBSyxDQUFDLGFBQVI7TUFDVCxNQUFNLENBQUMsSUFBUCxDQUFZLHFCQUFaLENBQWtDLENBQUMsV0FBbkMsQ0FBK0MsaUJBQS9DO2FBQ0EsTUFBTSxDQUFDLE9BQVAsQ0FBZSxxQkFBZjtJQUhtQjs7Ozs7O0VBS3ZCLENBQUEsQ0FBRSxTQUFBO1dBQ0EsQ0FBQSxDQUFFLGdCQUFGLENBQW1CLENBQUMsSUFBcEIsQ0FBeUIsU0FBQyxLQUFELEVBQVEsT0FBUjthQUN2QixJQUFJLE1BQU0sQ0FBQyxXQUFYLENBQXVCLE9BQXZCO0lBRHVCLENBQXpCO0VBREEsQ0FBRjtBQTFCQSIsInNvdXJjZXNDb250ZW50IjpbIlxuY2xhc3Mgd2luZG93LlByb2R1Y3RJdGVtXG4gIGNvbnN0cnVjdG9yOiAoZWxlbWVudCkgLT5cbiAgICBAX2VsID0gJChlbGVtZW50KVxuICAgIEBfZWwuZGF0YSAnY29udHJvbCcsIEBcbiAgICAkKCcuanMtY2FydC1idXR0b24nKS5vbiAnY2xpY2snLCBAX2J1dHRvbkNsaWNrSGFuZGxlci5iaW5kKEApXG5cbiAgICBAX2NvbG9ycygpXG5cbiAgX2NvbG9yczogLT5cbiAgICAkKCcucHJvZHVjdHMtaXRlbV9fY29sb3JzIGEnLCBAX2VsKS5vbiAnY2xpY2snLCBfLmJpbmQgQF9jb2xvckNoYW5nZUhhbmRsZXIsIEBcblxuICBfY29sb3JDaGFuZ2VIYW5kbGVyOiAoZXZlbnQpIC0+XG4gICAgY29sb3JMaW5rID0gJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgIGNvbG9yTGluay5zaWJsaW5ncygpLnJlbW92ZUNsYXNzICdzZWxlY3RlZCdcbiAgICBjb2xvckxpbmsuYWRkQ2xhc3MgJ3NlbGVjdGVkJ1xuICAgIGluZGV4ID0gY29sb3JMaW5rLmluZGV4KClcblxuICAgIHRhcmdldEltYWdlID0gJCAkKCcucHJvZHVjdHMtaXRlbV9faW1hZ2UnLCBAX2VsKVtpbmRleF1cbiAgICB0YXJnZXRJbWFnZS5zaWJsaW5ncygpLmFkZENsYXNzICdoaWRkZW4nXG4gICAgdGFyZ2V0SW1hZ2UucmVtb3ZlQ2xhc3MgJ2hpZGRlbidcblxuICBfYnV0dG9uQ2xpY2tIYW5kbGVyOiAoZXZlbnQpIC0+XG4gICAgYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgIGJ1dHRvbi50ZXh0KCfQlNC+0LHQsNCy0LvQtdC90L4g0LIg0LrQvtGA0LfQuNC90YMnKS5yZW1vdmVDbGFzcyAnYnV0dG9uLS1vdXRsaW5lJ1xuICAgIGJ1dHRvbi50cmlnZ2VyICdwcm9kdWN0OmFkZC10by1jYXJ0J1xuXG4kIC0+XG4gICQoJy5wcm9kdWN0cy1pdGVtJykuZWFjaCAoaW5kZXgsIGVsZW1lbnQpIC0+XG4gICAgbmV3IHdpbmRvdy5Qcm9kdWN0SXRlbSBlbGVtZW50Il19
