(function() {
  var CartPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  CartPage = (function(superClass) {
    extend(CartPage, superClass);

    function CartPage() {
      return CartPage.__super__.constructor.apply(this, arguments);
    }

    CartPage.prototype.initialize = function() {
      this._initInputs();
      return this._initOneClick();
    };
    CartPage.prototype._initInputs = function() {
      var b_slider_init, bonusInput, cartCurBonuses, cartUserBonuses;
      bonusInput = $('#bonus-input');
      cartUserBonuses = window.cartUserBonuses || 0;
      cartCurBonuses = window.cartCurBonuses || 0;
      if (cartUserBonuses === 0) {
        return;
      }
      b_slider_init = true;
      setTimeout(function() {
        return b_slider_init = false;
      }, 150);
      bonusInput.on('change', function() {
        return range.noUiSlider.set(bonusInput.val());
      });
      return range.noUiSlider.on('set', function(values, handler) {
        bonusInput.val(values[0]);
        if (!b_slider_init) {
          return $(document).trigger('set-bonuses');
        }
      });
    };

    CartPage.prototype._initOneClick = function() {
      return $('.js-one-click-short').on('click', function(event) {
        event.preventDefault();
        return $.ajax({
          method: 'get',
          url: window.application.getUrl('oneClick'),
          success: function(response) {
            return Popup.show($(response), {
              title: 'Быстрый заказ'
            });
          }
        });
      });
    };

    return CartPage;

  })(_Page);

  window.Pages.register('cart', CartPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvY2FydC5qcyIsInNvdXJjZXMiOlsicGFnZXMvY2FydC5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBLFFBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7dUJBQ0osVUFBQSxHQUFZLFNBQUE7TUFDVixJQUFDLENBQUEsV0FBRCxDQUFBO2FBQ0EsSUFBQyxDQUFBLGFBQUQsQ0FBQTtJQUZVOzt1QkFJWixXQUFBLEdBQWEsU0FBQTtBQUNYLFVBQUE7TUFBQSxVQUFBLEdBQWEsQ0FBQSxDQUFFLGNBQUY7TUFDYixLQUFBLEdBQVEsQ0FBQSxDQUFFLGVBQUYsQ0FBbUIsQ0FBQSxDQUFBO01BRTNCLGVBQUEsR0FBa0IsTUFBTSxDQUFDLGVBQVAsSUFBMEI7TUFDNUMsY0FBQSxHQUFpQixNQUFNLENBQUMsY0FBUCxJQUF5QjtNQUUxQyxJQUFJLGVBQUEsS0FBbUIsQ0FBdkI7QUFDRSxlQURGOztNQUdBLFVBQVUsQ0FBQyxNQUFYLENBQWtCLEtBQWxCLEVBQ0U7UUFBQSxLQUFBLEVBQU8sY0FBUDtRQUNBLE9BQUEsRUFBUyxDQUFDLElBQUQsRUFBTyxLQUFQLENBRFQ7UUFFQSxRQUFBLEVBQVUsSUFGVjtRQUdBLE1BQUEsRUFBUSxLQUFBLENBQU07VUFDWixRQUFBLEVBQVUsQ0FERTtTQUFOLENBSFI7UUFNQSxJQUFBLEVBQU0sQ0FOTjtRQU9BLEtBQUEsRUFDRTtVQUFBLEdBQUEsRUFBSyxDQUFMO1VBQ0EsR0FBQSxFQUFLLGVBREw7U0FSRjtPQURGO01BWUEsYUFBQSxHQUFnQjtNQUNoQixVQUFBLENBQVcsU0FBQTtlQUNULGFBQUEsR0FBZ0I7TUFEUCxDQUFYLEVBRUUsR0FGRjtNQUlBLFVBQVUsQ0FBQyxFQUFYLENBQWMsUUFBZCxFQUF3QixTQUFBO2VBQ3RCLEtBQUssQ0FBQyxVQUFVLENBQUMsR0FBakIsQ0FBcUIsVUFBVSxDQUFDLEdBQVgsQ0FBQSxDQUFyQjtNQURzQixDQUF4QjthQUdBLEtBQUssQ0FBQyxVQUFVLENBQUMsRUFBakIsQ0FBb0IsS0FBcEIsRUFBMkIsU0FBQyxNQUFELEVBQVMsT0FBVDtRQUN6QixVQUFVLENBQUMsR0FBWCxDQUFlLE1BQU8sQ0FBQSxDQUFBLENBQXRCO1FBQ0EsSUFBSSxDQUFDLGFBQUw7aUJBQ0UsQ0FBQSxDQUFFLFFBQUYsQ0FBVyxDQUFDLE9BQVosQ0FBb0IsYUFBcEIsRUFERjs7TUFGeUIsQ0FBM0I7SUE5Qlc7O3VCQW1DYixhQUFBLEdBQWUsU0FBQTthQUNiLENBQUEsQ0FBRSxxQkFBRixDQUF3QixDQUFDLEVBQXpCLENBQTRCLE9BQTVCLEVBQXFDLFNBQUMsS0FBRDtRQUNuQyxLQUFLLENBQUMsY0FBTixDQUFBO2VBQ0EsQ0FBQyxDQUFDLElBQUYsQ0FDRTtVQUFBLE1BQUEsRUFBUSxLQUFSO1VBQ0EsR0FBQSxFQUFLLE1BQU0sQ0FBQyxXQUFXLENBQUMsTUFBbkIsQ0FBMEIsVUFBMUIsQ0FETDtVQUVBLE9BQUEsRUFBUyxTQUFDLFFBQUQ7bUJBQ1AsS0FBSyxDQUFDLElBQU4sQ0FBVyxDQUFBLENBQUUsUUFBRixDQUFYLEVBQ0U7Y0FBQSxLQUFBLEVBQU8sZUFBUDthQURGO1VBRE8sQ0FGVDtTQURGO01BRm1DLENBQXJDO0lBRGE7Ozs7S0F4Q007O0VBbUR2QixNQUFNLENBQUMsS0FBSyxDQUFDLFFBQWIsQ0FBc0IsTUFBdEIsRUFBOEIsUUFBOUI7QUFuREEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyBDYXJ0UGFnZSBleHRlbmRzIF9QYWdlXG4gIGluaXRpYWxpemU6IC0+XG4gICAgQF9pbml0SW5wdXRzKClcbiAgICBAX2luaXRPbmVDbGljaygpXG5cbiAgX2luaXRJbnB1dHM6IC0+XG4gICAgYm9udXNJbnB1dCA9ICQoJyNib251cy1pbnB1dCcpXG4gICAgcmFuZ2UgPSAkKCcjYm9udXMtc2xpZGVyJylbMF1cblxuICAgIGNhcnRVc2VyQm9udXNlcyA9IHdpbmRvdy5jYXJ0VXNlckJvbnVzZXMgfHwgMFxuICAgIGNhcnRDdXJCb251c2VzID0gd2luZG93LmNhcnRDdXJCb251c2VzIHx8IDBcblxuICAgIGlmIChjYXJ0VXNlckJvbnVzZXMgPT0gMClcbiAgICAgIHJldHVybjtcblxuICAgIG5vVWlTbGlkZXIuY3JlYXRlIHJhbmdlLFxuICAgICAgc3RhcnQ6IGNhcnRDdXJCb251c2VzXG4gICAgICBjb25uZWN0OiBbdHJ1ZSwgZmFsc2VdXG4gICAgICB0b29sdGlwczogdHJ1ZVxuICAgICAgZm9ybWF0OiB3TnVtYih7XG4gICAgICAgIGRlY2ltYWxzOiAwXG4gICAgICB9KVxuICAgICAgc3RlcDogMVxuICAgICAgcmFuZ2U6XG4gICAgICAgIG1pbjogMFxuICAgICAgICBtYXg6IGNhcnRVc2VyQm9udXNlc1xuXG4gICAgYl9zbGlkZXJfaW5pdCA9IHRydWU7XG4gICAgc2V0VGltZW91dCAtPlxuICAgICAgYl9zbGlkZXJfaW5pdCA9IGZhbHNlXG4gICAgLCAxNTBcblxuICAgIGJvbnVzSW5wdXQub24gJ2NoYW5nZScsIC0+XG4gICAgICByYW5nZS5ub1VpU2xpZGVyLnNldChib251c0lucHV0LnZhbCgpKVxuXG4gICAgcmFuZ2Uubm9VaVNsaWRlci5vbiAnc2V0JywgKHZhbHVlcywgaGFuZGxlcikgLT5cbiAgICAgIGJvbnVzSW5wdXQudmFsIHZhbHVlc1swXVxuICAgICAgaWYgKCFiX3NsaWRlcl9pbml0KVxuICAgICAgICAkKGRvY3VtZW50KS50cmlnZ2VyKCdzZXQtYm9udXNlcycpXG5cbiAgX2luaXRPbmVDbGljazogLT5cbiAgICAkKCcuanMtb25lLWNsaWNrLXNob3J0Jykub24gJ2NsaWNrJywgKGV2ZW50KSAtPlxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgJC5hamF4XG4gICAgICAgIG1ldGhvZDogJ2dldCdcbiAgICAgICAgdXJsOiB3aW5kb3cuYXBwbGljYXRpb24uZ2V0VXJsKCdvbmVDbGljaycpXG4gICAgICAgIHN1Y2Nlc3M6IChyZXNwb25zZSkgLT5cbiAgICAgICAgICBQb3B1cC5zaG93ICQocmVzcG9uc2UpLFxuICAgICAgICAgICAgdGl0bGU6ICfQkdGL0YHRgtGA0YvQuSDQt9Cw0LrQsNC3J1xuXG5cbndpbmRvdy5QYWdlcy5yZWdpc3RlciAnY2FydCcsIENhcnRQYWdlIl19
