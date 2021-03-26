(function() {
  var OrderPickupPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  OrderPickupPage = (function(superClass) {
    extend(OrderPickupPage, superClass);

    function OrderPickupPage() {
      return OrderPickupPage.__super__.constructor.apply(this, arguments);
    }

    OrderPickupPage.prototype.initialize = function() {
      this._mapContainer = $('#pickup-map .shop-map');
      return $('.tabs-item[data-target="#pickup-map"]').on('show', (function(_this) {
        return function() {
          return _this.showMap();
        };
      })(this));
    };

    OrderPickupPage.prototype.showMap = function(url) {
      if (url == null) {
        url = window.application.getUrl('product');
      }
      if (this._map) {
        return;
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

    OrderPickupPage.prototype._initMap = function() {
      return this._map = new window.GoogleMapView(this._mapContainer, {
        items: this._shops
      });
    };

    return OrderPickupPage;

  })(_Page);

  window.Pages.register('order-pickup', OrderPickupPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvb3JkZXItcGlja3VwLmpzIiwic291cmNlcyI6WyJwYWdlcy9vcmRlci1waWNrdXAuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUEsTUFBQSxlQUFBO0lBQUE7OztFQUFNOzs7Ozs7OzhCQUNKLFVBQUEsR0FBWSxTQUFBO01BQ1YsSUFBQyxDQUFBLGFBQUQsR0FBaUIsQ0FBQSxDQUFFLHVCQUFGO2FBQ2pCLENBQUEsQ0FBRSx1Q0FBRixDQUEwQyxDQUFDLEVBQTNDLENBQThDLE1BQTlDLEVBQXNELENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQTtpQkFBRyxLQUFDLENBQUEsT0FBRCxDQUFBO1FBQUg7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQXREO0lBRlU7OzhCQUlaLE9BQUEsR0FBUyxTQUFDLEdBQUQ7O1FBQUMsTUFBTSxNQUFNLENBQUMsV0FBVyxDQUFDLE1BQW5CLENBQTBCLFNBQTFCOztNQUNkLElBQVUsSUFBQyxDQUFBLElBQVg7QUFBQSxlQUFBOzthQUVBLENBQUMsQ0FBQyxJQUFGLENBQ0U7UUFBQSxNQUFBLEVBQVEsS0FBUjtRQUNBLEdBQUEsRUFBSyxHQURMO1FBRUEsV0FBQSxFQUFhLE1BRmI7UUFHQSxPQUFBLEVBQVMsQ0FBQSxTQUFBLEtBQUE7aUJBQUEsU0FBQyxRQUFEO0FBQ1AsZ0JBQUE7WUFBQSxLQUFDLENBQUEsTUFBRCxHQUFVO0FBQ1Y7QUFBQSxpQkFBQSxxREFBQTs7Y0FDRSxJQUFJLENBQUMsS0FBTCxHQUFhO2NBQ2IsS0FBQyxDQUFBLE1BQU0sQ0FBQyxJQUFSLENBQWEsSUFBYjtBQUZGO21CQUdBLEtBQUMsQ0FBQSxRQUFELENBQUE7VUFMTztRQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FIVDtPQURGO0lBSE87OzhCQWNULFFBQUEsR0FBVSxTQUFBO2FBQ1IsSUFBQyxDQUFBLElBQUQsR0FBUSxJQUFJLE1BQU0sQ0FBQyxhQUFYLENBQXlCLElBQUMsQ0FBQSxhQUExQixFQUF5QztRQUMvQyxLQUFBLEVBQU8sSUFBQyxDQUFBLE1BRHVDO09BQXpDO0lBREE7Ozs7S0FuQmtCOztFQXdCOUIsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLGNBQXRCLEVBQXNDLGVBQXRDO0FBeEJBIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3MgT3JkZXJQaWNrdXBQYWdlIGV4dGVuZHMgX1BhZ2VcbiAgaW5pdGlhbGl6ZTogLT5cbiAgICBAX21hcENvbnRhaW5lciA9ICQoJyNwaWNrdXAtbWFwIC5zaG9wLW1hcCcpXG4gICAgJCgnLnRhYnMtaXRlbVtkYXRhLXRhcmdldD1cIiNwaWNrdXAtbWFwXCJdJykub24gJ3Nob3cnLCA9PiBAc2hvd01hcCgpXG5cbiAgc2hvd01hcDogKHVybCA9IHdpbmRvdy5hcHBsaWNhdGlvbi5nZXRVcmwoJ3Byb2R1Y3QnKSkgLT5cbiAgICByZXR1cm4gaWYgQF9tYXBcblxuICAgICQuYWpheFxuICAgICAgbWV0aG9kOiAnZ2V0J1xuICAgICAgdXJsOiB1cmxcbiAgICAgIGNvbnRlbnRUeXBlOiAnanNvbidcbiAgICAgIHN1Y2Nlc3M6IChyZXNwb25zZSkgPT5cbiAgICAgICAgQF9zaG9wcyA9IFtdXG4gICAgICAgIGZvciBzaG9wLCBpbmRleCBpbiByZXNwb25zZS5zaG9wc1xuICAgICAgICAgIHNob3AuaW5kZXggPSBpbmRleFxuICAgICAgICAgIEBfc2hvcHMucHVzaCBzaG9wXG4gICAgICAgIEBfaW5pdE1hcCgpXG5cbiAgX2luaXRNYXA6IC0+XG4gICAgQF9tYXAgPSBuZXcgd2luZG93Lkdvb2dsZU1hcFZpZXcgQF9tYXBDb250YWluZXIsIHtcbiAgICAgIGl0ZW1zOiBAX3Nob3BzXG4gICAgfVxuXG53aW5kb3cuUGFnZXMucmVnaXN0ZXIgJ29yZGVyLXBpY2t1cCcsIE9yZGVyUGlja3VwUGFnZSJdfQ==
