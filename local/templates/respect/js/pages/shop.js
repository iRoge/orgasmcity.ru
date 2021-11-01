(function() {
  var ShopPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ShopPage = (function(superClass) {
    extend(ShopPage, superClass);

    function ShopPage() {
      return ShopPage.__super__.constructor.apply(this, arguments);
    }
    ShopPage.prototype.initialize = function() {
      $('#shop-photo-slider').slick({
        arrows: true,
        dots: false,
        infinite: true,
        slidesToShow: 2,
        centerMode: true,
        variableWidth: true,
        focusOnSelect: true,
        responsive: [
          {
            breakpoint: 375,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });
      $('#shop-photo-slider-1').slick({
        arrows: false,
        dots: false,
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        focusOnSelect: true,
        centerMode: true,
        responsive: [
          {
            breakpoint: 640,
            settings: {
              arrows: true,
            }
          },
        ]
      });
      $('#shop-photo-slider-2').slick({
        arrows: false,
        dots: false,
        infinite: false,
        slidesToShow: 2,
        centerMode: false,
        variableWidth: true,
        focusOnSelect: false,
        responsive: [
          {
            breakpoint: 1280,
            settings: {
              arrows: true,
              slidesToShow: 1,
              infinite: true,
              focusOnSelect: true,
              centerMode: true,
            }
          },
        ]
      });
      return setTimeout((function(_this) {
        return function() {
          return _this._showMap();
        };
      })(this), 500);
    };

    ShopPage.prototype._showMap = function(url) {
      if (url == null) {
        url = window.application.getUrl('shopList');
      }
      return $.ajax({
        method: 'get',
        url: url,
        contentType: 'json',
        success: (function(_this) {
          return function(response) {
            var i, index, len, ref, shop;
            _this._shops = [];
            ref = response.shops;
            for (index = i = 0, len = ref.length; i < len; index = ++i) {
              shop = ref[index];
              shop.index = index;
              _this._shops.push(shop);
            }
            return _this._initMap();
          };
        })(this)
      });
    };

    ShopPage.prototype._initMap = function() {
      return this._map = new window.GoogleMapView($('#shop-map'), {
        items: _.sample(this._shops, 1),
        google: {
          zoom: 15
        }
      });
    };

    return ShopPage;

  })(_Page);

  window.Pages.register('shop', ShopPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvc2hvcC5qcyIsInNvdXJjZXMiOlsicGFnZXMvc2hvcC5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBLFFBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7dUJBQ0osVUFBQSxHQUFZLFNBQUE7TUFDVixDQUFBLENBQUUsb0JBQUYsQ0FBdUIsQ0FBQyxLQUF4QixDQUNFO1FBQUEsTUFBQSxFQUFRLElBQVI7UUFDQSxJQUFBLEVBQU0sS0FETjtRQUVBLFFBQUEsRUFBVSxJQUZWO1FBR0EsWUFBQSxFQUFjLENBSGQ7UUFJQSxVQUFBLEVBQVksSUFKWjtRQUtBLGFBQUEsRUFBZSxJQUxmO1FBTUEsYUFBQSxFQUFlLElBTmY7UUFPQSxVQUFBLEVBQVk7VUFDVjtZQUNFLFVBQUEsRUFBWSxHQURkO1lBRUUsUUFBQSxFQUNFO2NBQUEsWUFBQSxFQUFjLENBQWQ7YUFISjtXQURVO1NBUFo7T0FERjthQWdCQSxVQUFBLENBQVcsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFBO2lCQUNULEtBQUMsQ0FBQSxRQUFELENBQUE7UUFEUztNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBWCxFQUVFLEdBRkY7SUFqQlU7O3VCQXFCWixRQUFBLEdBQVUsU0FBQyxHQUFEOztRQUFDLE1BQU0sTUFBTSxDQUFDLFdBQVcsQ0FBQyxNQUFuQixDQUEwQixVQUExQjs7YUFDZixDQUFDLENBQUMsSUFBRixDQUNFO1FBQUEsTUFBQSxFQUFRLEtBQVI7UUFDQSxHQUFBLEVBQUssR0FETDtRQUVBLFdBQUEsRUFBYSxNQUZiO1FBR0EsT0FBQSxFQUFTLENBQUEsU0FBQSxLQUFBO2lCQUFBLFNBQUMsUUFBRDtBQUNQLGdCQUFBO1lBQUEsS0FBQyxDQUFBLE1BQUQsR0FBVTtBQUNWO0FBQUEsaUJBQUEscURBQUE7O2NBQ0UsSUFBSSxDQUFDLEtBQUwsR0FBYTtjQUNiLEtBQUMsQ0FBQSxNQUFNLENBQUMsSUFBUixDQUFhLElBQWI7QUFGRjttQkFHQSxLQUFDLENBQUEsUUFBRCxDQUFBO1VBTE87UUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBSFQ7T0FERjtJQURROzt1QkFZVixRQUFBLEdBQVUsU0FBQTthQUNSLElBQUMsQ0FBQSxJQUFELEdBQVEsSUFBSSxNQUFNLENBQUMsYUFBWCxDQUF5QixDQUFBLENBQUUsV0FBRixDQUF6QixFQUF5QztRQUMvQyxLQUFBLEVBQU8sQ0FBQyxDQUFDLE1BQUYsQ0FBUyxJQUFDLENBQUEsTUFBVixFQUFrQixDQUFsQixDQUR3QztRQUUvQyxNQUFBLEVBQ0U7VUFBQSxJQUFBLEVBQU0sRUFBTjtTQUg2QztPQUF6QztJQURBOzs7O0tBbENXOztFQTBDdkIsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLE1BQXRCLEVBQThCLFFBQTlCO0FBMUNBIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3MgU2hvcFBhZ2UgZXh0ZW5kcyBfUGFnZVxuICBpbml0aWFsaXplOiAtPlxuICAgICQoJyNzaG9wLXBob3RvLXNsaWRlcicpLnNsaWNrXG4gICAgICBhcnJvd3M6IHRydWVcbiAgICAgIGRvdHM6IGZhbHNlXG4gICAgICBpbmZpbml0ZTogdHJ1ZVxuICAgICAgc2xpZGVzVG9TaG93OiAyXG4gICAgICBjZW50ZXJNb2RlOiB0cnVlXG4gICAgICB2YXJpYWJsZVdpZHRoOiB0cnVlXG4gICAgICBmb2N1c09uU2VsZWN0OiB0cnVlXG4gICAgICByZXNwb25zaXZlOiBbXG4gICAgICAgIHtcbiAgICAgICAgICBicmVha3BvaW50OiAzNzVcbiAgICAgICAgICBzZXR0aW5nczpcbiAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogMVxuICAgICAgICB9XG4gICAgICBdXG5cbiAgICBzZXRUaW1lb3V0ID0+XG4gICAgICBAX3Nob3dNYXAoKVxuICAgICwgNTAwXG5cbiAgX3Nob3dNYXA6ICh1cmwgPSB3aW5kb3cuYXBwbGljYXRpb24uZ2V0VXJsKCdzaG9wTGlzdCcpKSAtPlxuICAgICQuYWpheFxuICAgICAgbWV0aG9kOiAnZ2V0J1xuICAgICAgdXJsOiB1cmxcbiAgICAgIGNvbnRlbnRUeXBlOiAnanNvbidcbiAgICAgIHN1Y2Nlc3M6IChyZXNwb25zZSkgPT5cbiAgICAgICAgQF9zaG9wcyA9IFtdXG4gICAgICAgIGZvciBzaG9wLCBpbmRleCBpbiByZXNwb25zZS5zaG9wc1xuICAgICAgICAgIHNob3AuaW5kZXggPSBpbmRleFxuICAgICAgICAgIEBfc2hvcHMucHVzaCBzaG9wXG4gICAgICAgIEBfaW5pdE1hcCgpXG5cbiAgX2luaXRNYXA6IC0+XG4gICAgQF9tYXAgPSBuZXcgd2luZG93Lkdvb2dsZU1hcFZpZXcgJCgnI3Nob3AtbWFwJyksIHtcbiAgICAgIGl0ZW1zOiBfLnNhbXBsZSBAX3Nob3BzLCAxXG4gICAgICBnb29nbGU6XG4gICAgICAgIHpvb206IDE1XG4gICAgfVxuXG5cbndpbmRvdy5QYWdlcy5yZWdpc3RlciAnc2hvcCcsIFNob3BQYWdlIl19
