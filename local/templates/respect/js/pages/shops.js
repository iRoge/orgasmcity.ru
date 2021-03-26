(function() {
  var ShopsPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ShopsPage = (function(superClass) {
    extend(ShopsPage, superClass);

    function ShopsPage() {
      return ShopsPage.__super__.constructor.apply(this, arguments);
    }

    ShopsPage.prototype.initialize = function() {
      this._mapContainer = $('#map .shop-map');
      return $('.tabs-item[data-target="#map"]').on('show', (function(_this) {
        return function() {
          return _this.showMap();
        };
      })(this));
    };

    ShopsPage.prototype.showMap = function(url) {
      if (url == null) {
        url = '/shops/?show_map=y';
      }
      if (this._map) {
        return;
      }
      return $.ajax({
        method: 'get',
        url: url,
        cache: false,
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

    ShopsPage.prototype._initMap = function() {
      return this._map = new window.GoogleMapView(this._mapContainer, {
        items: this._shops
      });
    };

    return ShopsPage;

  })(_Page);

  window.Pages.register('shops', ShopsPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvc2hvcHMuanMiLCJzb3VyY2VzIjpbInBhZ2VzL3Nob3BzLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBLE1BQUEsU0FBQTtJQUFBOzs7RUFBTTs7Ozs7Ozt3QkFDSixVQUFBLEdBQVksU0FBQTtNQUNWLElBQUMsQ0FBQSxhQUFELEdBQWlCLENBQUEsQ0FBRSxnQkFBRjthQUVqQixDQUFBLENBQUUsZ0NBQUYsQ0FBbUMsQ0FBQyxFQUFwQyxDQUF1QyxNQUF2QyxFQUErQyxDQUFBLFNBQUEsS0FBQTtlQUFBLFNBQUE7aUJBQUcsS0FBQyxDQUFBLE9BQUQsQ0FBQTtRQUFIO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUEvQztJQUhVOzt3QkFLWixPQUFBLEdBQVMsU0FBQyxHQUFEOztRQUFDLE1BQU07O01BQ2QsSUFBVSxJQUFDLENBQUEsSUFBWDtBQUFBLGVBQUE7O2FBRUEsQ0FBQyxDQUFDLElBQUYsQ0FDRTtRQUFBLE1BQUEsRUFBUSxLQUFSO1FBQ0EsR0FBQSxFQUFLLEdBREw7UUFFQSxLQUFBLEVBQU8sS0FGUDtRQUdBLFdBQUEsRUFBYSxNQUhiO1FBSUEsT0FBQSxFQUFTLENBQUEsU0FBQSxLQUFBO2lCQUFBLFNBQUMsUUFBRDtBQUNQLGdCQUFBO1lBQUEsS0FBQyxDQUFBLE1BQUQsR0FBVTtBQUNWO0FBQUEsaUJBQUEscURBQUE7O2NBQ0UsSUFBSSxDQUFDLEtBQUwsR0FBYTtjQUNiLEtBQUMsQ0FBQSxNQUFNLENBQUMsSUFBUixDQUFhLElBQWI7QUFGRjttQkFHQSxLQUFDLENBQUEsUUFBRCxDQUFBO1VBTE87UUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBSlQ7T0FERjtJQUhPOzt3QkFlVCxRQUFBLEdBQVUsU0FBQTthQUNSLElBQUMsQ0FBQSxJQUFELEdBQVEsSUFBSSxNQUFNLENBQUMsYUFBWCxDQUF5QixJQUFDLENBQUEsYUFBMUIsRUFBeUM7UUFDL0MsS0FBQSxFQUFPLElBQUMsQ0FBQSxNQUR1QztPQUF6QztJQURBOzs7O0tBckJZOztFQTBCeEIsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLE9BQXRCLEVBQStCLFNBQS9CO0FBMUJBIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3MgU2hvcHNQYWdlIGV4dGVuZHMgX1BhZ2VcbiAgaW5pdGlhbGl6ZTogLT5cbiAgICBAX21hcENvbnRhaW5lciA9ICQoJyNtYXAgLnNob3AtbWFwJylcblxuICAgICQoJy50YWJzLWl0ZW1bZGF0YS10YXJnZXQ9XCIjbWFwXCJdJykub24gJ3Nob3cnLCA9PiBAc2hvd01hcCgpXG5cbiAgc2hvd01hcDogKHVybCA9ICcvc2hvcHMvP3Nob3dfbWFwPXknKSAtPlxuICAgIHJldHVybiBpZiBAX21hcFxuXG4gICAgJC5hamF4XG4gICAgICBtZXRob2Q6ICdnZXQnXG4gICAgICB1cmw6IHVybFxuICAgICAgY2FjaGU6IGZhbHNlXG4gICAgICBjb250ZW50VHlwZTogJ2pzb24nXG4gICAgICBzdWNjZXNzOiAocmVzcG9uc2UpID0+XG4gICAgICAgIEBfc2hvcHMgPSBbXVxuICAgICAgICBmb3Igc2hvcCwgaW5kZXggaW4gcmVzcG9uc2Uuc2hvcHNcbiAgICAgICAgICBzaG9wLmluZGV4ID0gaW5kZXhcbiAgICAgICAgICBAX3Nob3BzLnB1c2ggc2hvcFxuICAgICAgICBAX2luaXRNYXAoKVxuXG4gIF9pbml0TWFwOiAtPlxuICAgIEBfbWFwID0gbmV3IHdpbmRvdy5Hb29nbGVNYXBWaWV3IEBfbWFwQ29udGFpbmVyLCB7XG4gICAgICBpdGVtczogQF9zaG9wc1xuICAgIH1cblxud2luZG93LlBhZ2VzLnJlZ2lzdGVyICdzaG9wcycsIFNob3BzUGFnZSJdfQ==
