(function() {
  window.CartItem = (function() {
    function CartItem(element) {
      this._el = $(element);
      this._el.data('control', this);
      this._toggle();
    }

    CartItem.prototype._toggle = function() {
      $('.cart-item__toggle', this._el).on('click', _.bind(this._toggleClickHandler, this));
      return $('.js-params-toggle', this._el).on('click', _.bind(this._paramClickHandler, this));
    };
    CartItem.prototype._toggleClickHandler = function() {
      return this._el.toggleClass('cart-item--expanded');
    };

    CartItem.prototype._paramClickHandler = function(event) {
      var colorRow, params;
      colorRow = $(event.currentTarget);
      params = colorRow.closest('.cart-item__param');
      params.toggleClass('cart-item__param--expanded');
      return $('.color-selector a', params).toggleClass('selected');
    };

    return CartItem;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiY2FydC1pdGVtLmpzIiwic291cmNlcyI6WyJjYXJ0LWl0ZW0uY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ0Usa0JBQUMsT0FBRDtNQUNYLElBQUMsQ0FBQSxHQUFELEdBQU8sQ0FBQSxDQUFFLE9BQUY7TUFDUCxJQUFDLENBQUEsR0FBRyxDQUFDLElBQUwsQ0FBVSxTQUFWLEVBQXFCLElBQXJCO01BRUEsSUFBQyxDQUFBLE9BQUQsQ0FBQTtJQUpXOzt1QkFNYixPQUFBLEdBQVMsU0FBQTtNQUNQLENBQUEsQ0FBRSxvQkFBRixFQUF3QixJQUFDLENBQUEsR0FBekIsQ0FBNkIsQ0FBQyxFQUE5QixDQUFpQyxPQUFqQyxFQUEwQyxDQUFDLENBQUMsSUFBRixDQUFPLElBQUMsQ0FBQSxtQkFBUixFQUE2QixJQUE3QixDQUExQzthQUNBLENBQUEsQ0FBRSxtQkFBRixFQUF1QixJQUFDLENBQUEsR0FBeEIsQ0FBNEIsQ0FBQyxFQUE3QixDQUFnQyxPQUFoQyxFQUF5QyxDQUFDLENBQUMsSUFBRixDQUFPLElBQUMsQ0FBQSxrQkFBUixFQUE0QixJQUE1QixDQUF6QztJQUZPOzt1QkFJVCxtQkFBQSxHQUFxQixTQUFBO2FBQ25CLElBQUMsQ0FBQSxHQUFHLENBQUMsV0FBTCxDQUFpQixxQkFBakI7SUFEbUI7O3VCQUdyQixrQkFBQSxHQUFvQixTQUFDLEtBQUQ7QUFDbEIsVUFBQTtNQUFBLFFBQUEsR0FBVyxDQUFBLENBQUUsS0FBSyxDQUFDLGFBQVI7TUFDWCxNQUFBLEdBQVMsUUFBUSxDQUFDLE9BQVQsQ0FBaUIsbUJBQWpCO01BQ1QsTUFBTSxDQUFDLFdBQVAsQ0FBbUIsNEJBQW5CO2FBQ0EsQ0FBQSxDQUFFLG1CQUFGLEVBQXVCLE1BQXZCLENBQThCLENBQUMsV0FBL0IsQ0FBMkMsVUFBM0M7SUFKa0I7Ozs7O0FBZHRCIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3Mgd2luZG93LkNhcnRJdGVtXG4gIGNvbnN0cnVjdG9yOiAoZWxlbWVudCkgLT5cbiAgICBAX2VsID0gJChlbGVtZW50KVxuICAgIEBfZWwuZGF0YSAnY29udHJvbCcsIEBcblxuICAgIEBfdG9nZ2xlKClcblxuICBfdG9nZ2xlOiAtPlxuICAgICQoJy5jYXJ0LWl0ZW1fX3RvZ2dsZScsIEBfZWwpLm9uICdjbGljaycsIF8uYmluZCBAX3RvZ2dsZUNsaWNrSGFuZGxlciwgQFxuICAgICQoJy5qcy1wYXJhbXMtdG9nZ2xlJywgQF9lbCkub24gJ2NsaWNrJywgXy5iaW5kIEBfcGFyYW1DbGlja0hhbmRsZXIsIEBcblxuICBfdG9nZ2xlQ2xpY2tIYW5kbGVyOiAtPlxuICAgIEBfZWwudG9nZ2xlQ2xhc3MgJ2NhcnQtaXRlbS0tZXhwYW5kZWQnXG5cbiAgX3BhcmFtQ2xpY2tIYW5kbGVyOiAoZXZlbnQpIC0+XG4gICAgY29sb3JSb3cgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG4gICAgcGFyYW1zID0gY29sb3JSb3cuY2xvc2VzdCgnLmNhcnQtaXRlbV9fcGFyYW0nKVxuICAgIHBhcmFtcy50b2dnbGVDbGFzcyAnY2FydC1pdGVtX19wYXJhbS0tZXhwYW5kZWQnXG4gICAgJCgnLmNvbG9yLXNlbGVjdG9yIGEnLCBwYXJhbXMpLnRvZ2dsZUNsYXNzICdzZWxlY3RlZCciXX0=
