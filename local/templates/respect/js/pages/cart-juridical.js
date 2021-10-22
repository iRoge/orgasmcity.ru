(function() {
  var CartJuridicalPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  CartJuridicalPage = (function(superClass) {
    extend(CartJuridicalPage, superClass);

    function CartJuridicalPage() {
      return CartJuridicalPage.__super__.constructor.apply(this, arguments);
    }

    CartJuridicalPage.prototype.initialize = function() {
      SizeInput.init();
      return this._initCartItems();
    };
    CartJuridicalPage.prototype._initCartItems = function() {
      return $('.cart-item').each(function(index, cartItem) {
        return new window.CartItem(cartItem);
      });
    };

    return CartJuridicalPage;

  })(_Page);

  window.Pages.register('cart-juridical', CartJuridicalPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvY2FydC1qdXJpZGljYWwuanMiLCJzb3VyY2VzIjpbInBhZ2VzL2NhcnQtanVyaWRpY2FsLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBLE1BQUEsaUJBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7Z0NBQ0osVUFBQSxHQUFZLFNBQUE7TUFDVixTQUFTLENBQUMsSUFBVixDQUFBO2FBQ0EsSUFBQyxDQUFBLGNBQUQsQ0FBQTtJQUZVOztnQ0FJWixjQUFBLEdBQWdCLFNBQUE7YUFDZCxDQUFBLENBQUUsWUFBRixDQUFlLENBQUMsSUFBaEIsQ0FBcUIsU0FBQyxLQUFELEVBQVEsUUFBUjtlQUNuQixJQUFJLE1BQU0sQ0FBQyxRQUFYLENBQW9CLFFBQXBCO01BRG1CLENBQXJCO0lBRGM7Ozs7S0FMYzs7RUFVaEMsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLGdCQUF0QixFQUF3QyxpQkFBeEM7QUFWQSIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIENhcnRKdXJpZGljYWxQYWdlIGV4dGVuZHMgX1BhZ2VcbiAgaW5pdGlhbGl6ZTogLT5cbiAgICBTaXplSW5wdXQuaW5pdCgpXG4gICAgQF9pbml0Q2FydEl0ZW1zKClcblxuICBfaW5pdENhcnRJdGVtczogLT5cbiAgICAkKCcuY2FydC1pdGVtJykuZWFjaCAoaW5kZXgsIGNhcnRJdGVtKSAtPlxuICAgICAgbmV3IHdpbmRvdy5DYXJ0SXRlbShjYXJ0SXRlbSlcblxuXG53aW5kb3cuUGFnZXMucmVnaXN0ZXIgJ2NhcnQtanVyaWRpY2FsJywgQ2FydEp1cmlkaWNhbFBhZ2UiXX0=
