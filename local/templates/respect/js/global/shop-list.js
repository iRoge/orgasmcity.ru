(function() {
  window.ShopList = (function() {
    ShopList.options = {
      autoload: true
    };

    function ShopList(element, options) {
      if (options == null) {
        options = {};
      }
      this.options = _.extend(ShopList.options, options);
      this._el = $(element);
      this._list = $('.shop-list', this._el);
      this._mapContainer = $('.js-shop-list-map', this._el);
      if (this.options.autoload) {
        this.loadList();
      }
    }

    ShopList.prototype.loadList = function(url) {
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
            _this._render();
            _this._initMap();
            return $('#size').on('change', function(event) {
              _this._render();
              return _this._initMap();
            });
          };
        })(this)
      });
    };

    ShopList.prototype._render = function() {
      var filter_shops, i, len, li, shop, shop_mobile_select, size, template;
      this._list.children().remove();
      size = $('#size').val();
      shop_mobile_select = $('#shop');
      filter_shops = [];
      $.each(this._shops, (function(_this) {
        return function(j, shop) {
          if ($.inArray(size, shop.sizes) !== -1) {
            return filter_shops.push(shop);
          }
        };
      })(this));
      shop_mobile_select[0].selectize.clearOptions();
      for (i = 0, len = filter_shops.length; i < len; i++) {
        shop = filter_shops[i];
        template = _.template('<li class="shop-list-item" data-index="<%=index%>">\n  <% if (title) { %>\n    <div class="shop-list-item__title"><%=title%></div>\n  <% } %>\n  <% if (address) { %>\n    <div class="shop-list-item__address"><%=address%></div>\n  <% } %>\n  <% if (distance) { %>\n    <div class="shop-list-item__distance">От центра <%=distance%> км</div>\n  <% } %>\n</li>');
        li = $(template(shop));
        this._list.append(li);
        li.on('click', this._itemClickHandler.bind(this));
        shop_mobile_select[0].selectize.addOption({
          value: shop.index,
          text: shop.title
        });
      }
      shop_mobile_select[0].selectize.refreshOptions();
      return shop_mobile_select.on('change', (function(_this) {
        return function(event) {
          return _this._map.select($(event.currentTarget).val());
        };
      })(this));
    };

    ShopList.prototype._initMap = function() {
      var $map, center, filter_shops, size;
      size = $('#size').val();
      filter_shops = [];
      $.each(this._shops, (function(_this) {
        return function(j, shop) {
          if ($.inArray(size, shop.sizes) !== -1) {
            return filter_shops.push(shop);
          }
        };
      })(this));
      $map = $(this._mapContainer);
      center = {
        lat: parseFloat($map.data('lat')) || 55.7494733,
        lng: parseFloat($map.data('lon')) || 37.35232
      };
      return this._map = new window.GoogleMapView(this._mapContainer, {
        items: filter_shops,
        google: {
          center: center
        },
        onSelect: (function(_this) {
          return function(index) {
            var item;
            $('#shop')[0].selectize.setValue(index, true);
            item = $("li[data-index=" + index + "]", _this._list);
            item.siblings().removeClass('selected');
            return item.addClass('selected');
          };
        })(this)
      });
    };

    ShopList.prototype._itemClickHandler = function(event) {

      /*$('#shop')[0].selectize.setValue(index, true); */
      var index, item;
      item = $(event.currentTarget);
      item.siblings().removeClass('selected');
      item.addClass('selected');
      index = item.data('index');
      return this._map.select(index);
    };

    return ShopList;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoic2hvcC1saXN0LmpzIiwic291cmNlcyI6WyJzaG9wLWxpc3QuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ1gsUUFBQyxDQUFBLE9BQUQsR0FDRTtNQUFBLFFBQUEsRUFBVSxJQUFWOzs7SUFFVyxrQkFBQyxPQUFELEVBQVUsT0FBVjs7UUFBVSxVQUFVOztNQUMvQixJQUFDLENBQUEsT0FBRCxHQUFXLENBQUMsQ0FBQyxNQUFGLENBQVMsUUFBUSxDQUFDLE9BQWxCLEVBQTJCLE9BQTNCO01BQ1gsSUFBQyxDQUFBLEdBQUQsR0FBTyxDQUFBLENBQUUsT0FBRjtNQUNQLElBQUMsQ0FBQSxLQUFELEdBQVMsQ0FBQSxDQUFFLFlBQUYsRUFBZ0IsSUFBQyxDQUFBLEdBQWpCO01BQ1QsSUFBQyxDQUFBLGFBQUQsR0FBaUIsQ0FBQSxDQUFFLG1CQUFGLEVBQXVCLElBQUMsQ0FBQSxHQUF4QjtNQUVqQixJQUFlLElBQUMsQ0FBQSxPQUFPLENBQUMsUUFBeEI7UUFBQSxJQUFDLENBQUEsUUFBRCxDQUFBLEVBQUE7O0lBTlc7O3VCQVFiLFFBQUEsR0FBVSxTQUFDLEdBQUQ7O1FBQUMsTUFBTSxNQUFNLENBQUMsV0FBVyxDQUFDLE1BQW5CLENBQTBCLFVBQTFCOzthQUNmLENBQUMsQ0FBQyxJQUFGLENBQ0U7UUFBQSxNQUFBLEVBQVEsS0FBUjtRQUNBLEdBQUEsRUFBSyxHQURMO1FBRUEsV0FBQSxFQUFhLE1BRmI7UUFHQSxPQUFBLEVBQVMsQ0FBQSxTQUFBLEtBQUE7aUJBQUEsU0FBQyxRQUFEO0FBQ1AsZ0JBQUE7WUFBQSxLQUFDLENBQUEsTUFBRCxHQUFVO0FBQ1Y7QUFBQSxpQkFBQSxxREFBQTs7Y0FDRSxJQUFJLENBQUMsS0FBTCxHQUFhO2NBQ2IsS0FBQyxDQUFBLE1BQU0sQ0FBQyxJQUFSLENBQWEsSUFBYjtBQUZGO1lBR0EsS0FBQyxDQUFBLE9BQUQsQ0FBQTtZQUNBLEtBQUMsQ0FBQSxRQUFELENBQUE7bUJBQ0Es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YUFLVCxJQUFDLENBQUEsSUFBRCxHQUFRLElBQUksTUFBTSxDQUFDLGFBQVgsQ0FBeUIsSUFBQyxDQUFBLGFBQTFCLEVBQXlDO1FBQy9DLEtBQUEsRUFBTyxZQUR3QztRQUUvQyxNQUFBLEVBQVE7VUFDTixNQUFBLEVBQVEsTUFERjtTQUZ1QztRQUsvQyxRQUFBLEVBQVUsQ0FBQSxTQUFBLEtBQUE7aUJBQUEsU0FBQyxLQUFEO0FBQ1IsZ0JBQUE7WUFBQSxDQUFBLENBQUUsT0FBRixDQUFXLENBQUEsQ0FBQSxDQUFFLENBQUMsU0FBUyxDQUFDLFFBQXhCLENBQWlDLEtBQWpDLEVBQXdDLElBQXhDO1lBQ0EsSUFBQSxHQUFPLENBQUEsQ0FBRSxnQkFBQSxHQUFpQixLQUFqQixHQUF1QixHQUF6QixFQUE2QixLQUFDLENBQUEsS0FBOUI7WUFDUCxJQUFJLENBQUMsUUFBTCxDQUFBLENBQWUsQ0FBQyxXQUFoQixDQUE0QixVQUE1QjttQkFDQSxJQUFJLENBQUMsUUFBTCxDQUFjLFVBQWQ7VUFKUTtRQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FMcUM7T0FBekM7SUFmQTs7dUJBMkJWLGlCQUFBLEdBQW1CLFNBQUMsS0FBRDs7QUFDakI7QUFBQSxVQUFBO01BQ0EsSUFBQSxHQUFPLENBQUEsQ0FBRSxLQUFLLENBQUMsYUFBUjtNQUNQLElBQUksQ0FBQyxRQUFMLENBQUEsQ0FBZSxDQUFDLFdBQWhCLENBQTRCLFVBQTVCO01BQ0EsSUFBSSxDQUFDLFFBQUwsQ0FBYyxVQUFkO01BQ0EsS0FBQSxHQUFRLElBQUksQ0FBQyxJQUFMLENBQVUsT0FBVjthQUNSLElBQUMsQ0FBQSxJQUFJLENBQUMsTUFBTixDQUFhLEtBQWI7SUFOaUI7Ozs7O0FBakdyQiIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIHdpbmRvdy5TaG9wTGlzdFxuICBAb3B0aW9uczpcbiAgICBhdXRvbG9hZDogdHJ1ZVxuXG4gIGNvbnN0cnVjdG9yOiAoZWxlbWVudCwgb3B0aW9ucyA9IHt9KSAtPlxuICAgIEBvcHRpb25zID0gXy5leHRlbmQgU2hvcExpc3Qub3B0aW9ucywgb3B0aW9uc1xuICAgIEBfZWwgPSAkKGVsZW1lbnQpXG4gICAgQF9saXN0ID0gJCgnLnNob3AtbGlzdCcsIEBfZWwpXG4gICAgQF9tYXBDb250YWluZXIgPSAkKCcuanMtc2hvcC1saXN0LW1hcCcsIEBfZWwpXG5cbiAgICBAbG9hZExpc3QoKSBpZiBAb3B0aW9ucy5hdXRvbG9hZFxuXG4gIGxvYWRMaXN0OiAodXJsID0gd2luZG93LmFwcGxpY2F0aW9uLmdldFVybCgnc2hvcExpc3QnKSktPlxuICAgICQuYWpheFxuICAgICAgbWV0aG9kOiAnZ2V0J1xuICAgICAgdXJsOiB1cmxcbiAgICAgIGNvbnRlbnRUeXBlOiAnanNvbidcbiAgICAgIHN1Y2Nlc3M6IChyZXNwb25zZSkgPT5cbiAgICAgICAgQF9zaG9wcyA9IFtdXG4gICAgICAgIGZvciBzaG9wLCBpbmRleCBpbiByZXNwb25zZS5zaG9wc1xuICAgICAgICAgIHNob3AuaW5kZXggPSBpbmRleFxuICAgICAgICAgIEBfc2hvcHMucHVzaCBzaG9wXG4gICAgICAgIEBfcmVuZGVyKClcbiAgICAgICAgQF9pbml0TWFwKClcbiAgICAgICAgJCgnI3NpemUnKS5vbiAnY2hhbmdlJywgKGV2ZW50KSA9PlxuICAgICAgICAgIEBfcmVuZGVyKClcbiAgICAgICAgICByZXR1cm4gQF9pbml0TWFwKClcblxuICBfcmVuZGVyOiAtPlxuICAgIEBfbGlzdC5jaGlsZHJlbigpLnJlbW92ZSgpXG5cbiAgICBzaXplID0gJCgnI3NpemUnKS52YWwoKVxuXG4gICAgc2hvcF9tb2JpbGVfc2VsZWN0ID0gJCgnI3Nob3AnKTtcblxuICAgIGZpbHRlcl9zaG9wcyA9IFtdXG5cbiAgICAkLmVhY2ggQF9zaG9wcywgKGosIHNob3ApID0+XG4gICAgICBpZiAkLmluQXJyYXkoc2l6ZSwgc2hvcC5zaXplcykgIT0gLTFcbiAgICAgICAgZmlsdGVyX3Nob3BzLnB1c2ggc2hvcFxuXG4gICAgc2hvcF9tb2JpbGVfc2VsZWN0WzBdLnNlbGVjdGl6ZS5jbGVhck9wdGlvbnMoKTtcbiAgICBmb3Igc2hvcCBpbiBmaWx0ZXJfc2hvcHNcbiAgICAgIHRlbXBsYXRlID0gXy50ZW1wbGF0ZSAnJydcbjxsaSBjbGFzcz1cInNob3AtbGlzdC1pdGVtXCIgZGF0YS1pbmRleD1cIjwlPWluZGV4JT5cIj5cbiAgPCUgaWYgKHRpdGxlKSB7ICU+XG4gICAgPGRpdiBjbGFzcz1cInNob3AtbGlzdC1pdGVtX190aXRsZVwiPjwlPXRpdGxlJT48L2Rpdj5cbiAgPCUgfSAlPlxuICA8JSBpZiAoYWRkcmVzcykgeyAlPlxuICAgIDxkaXYgY2xhc3M9XCJzaG9wLWxpc3QtaXRlbV9fYWRkcmVzc1wiPjwlPWFkZHJlc3MlPjwvZGl2PlxuICA8JSB9ICU+XG4gIDwlIGlmIChkaXN0YW5jZSkgeyAlPlxuICAgIDxkaXYgY2xhc3M9XCJzaG9wLWxpc3QtaXRlbV9fZGlzdGFuY2VcIj7QntGCINGG0LXQvdGC0YDQsCA8JT1kaXN0YW5jZSU+INC60Lw8L2Rpdj5cbiAgPCUgfSAlPlxuPC9saT5cbicnJ1xuICAgICAgbGkgPSAkKHRlbXBsYXRlKHNob3ApKVxuICAgICAgQF9saXN0LmFwcGVuZCBsaVxuXG4gICAgICBsaS5vbiAnY2xpY2snLCBAX2l0ZW1DbGlja0hhbmRsZXIuYmluZChAKVxuXG4gICAgICBzaG9wX21vYmlsZV9zZWxlY3RbMF0uc2VsZWN0aXplLmFkZE9wdGlvbih7XG4gICAgICAgIHZhbHVlOiBzaG9wLmluZGV4LFxuICAgICAgICB0ZXh0OiBzaG9wLnRpdGxlXG4gICAgICB9KVxuXG4gICAgc2hvcF9tb2JpbGVfc2VsZWN0WzBdLnNlbGVjdGl6ZS5yZWZyZXNoT3B0aW9ucygpO1xuICAgIHNob3BfbW9iaWxlX3NlbGVjdC5vbiAnY2hhbmdlJywoZXZlbnQpID0+XG4gICAgICB0aGlzLl9tYXAuc2VsZWN0KCQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCkpO1xuXG4gIF9pbml0TWFwOiAtPlxuICAgIHNpemUgPSAkKCcjc2l6ZScpLnZhbCgpXG5cbiAgICBmaWx0ZXJfc2hvcHMgPSBbXVxuXG4gICAgJC5lYWNoIEBfc2hvcHMsIChqLCBzaG9wKSA9PlxuICAgICAgaWYgJC5pbkFycmF5KHNpemUsIHNob3Auc2l6ZXMpICE9IC0xXG4gICAgICAgIGZpbHRlcl9zaG9wcy5wdXNoIHNob3BcblxuICAgICRtYXAgPSAkKEBfbWFwQ29udGFpbmVyKTtcbiAgICBjZW50ZXIgPSB7XG4gICAgICBsYXQ6IHBhcnNlRmxvYXQoJG1hcC5kYXRhKCdsYXQnKSkgfHwgNTUuNzQ5NDczMyxcbiAgICAgIGxuZzogcGFyc2VGbG9hdCgkbWFwLmRhdGEoJ2xvbicpKSB8fCAzNy4zNTIzMlxuICAgIH1cblxuICAgIEBfbWFwID0gbmV3IHdpbmRvdy5Hb29nbGVNYXBWaWV3IEBfbWFwQ29udGFpbmVyLCB7XG4gICAgICBpdGVtczogZmlsdGVyX3Nob3BzXG4gICAgICBnb29nbGU6IHtcbiAgICAgICAgY2VudGVyOiBjZW50ZXJcbiAgICAgIH1cbiAgICAgIG9uU2VsZWN0OiAoaW5kZXgpID0+XG4gICAgICAgICQoJyNzaG9wJylbMF0uc2VsZWN0aXplLnNldFZhbHVlKGluZGV4LCB0cnVlKTtcbiAgICAgICAgaXRlbSA9ICQoXCJsaVtkYXRhLWluZGV4PSN7aW5kZXh9XVwiLCBAX2xpc3QpXG4gICAgICAgIGl0ZW0uc2libGluZ3MoKS5yZW1vdmVDbGFzcygnc2VsZWN0ZWQnKVxuICAgICAgICBpdGVtLmFkZENsYXNzICdzZWxlY3RlZCdcbiAgICB9XG5cbiAgX2l0ZW1DbGlja0hhbmRsZXI6IChldmVudCkgLT5cbiAgICAjIyMkKCcjc2hvcCcpWzBdLnNlbGVjdGl6ZS5zZXRWYWx1ZShpbmRleCwgdHJ1ZSk7IyMjXG4gICAgaXRlbSA9ICQoZXZlbnQuY3VycmVudFRhcmdldClcbiAgICBpdGVtLnNpYmxpbmdzKCkucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJylcbiAgICBpdGVtLmFkZENsYXNzICdzZWxlY3RlZCdcbiAgICBpbmRleCA9IGl0ZW0uZGF0YSgnaW5kZXgnKVxuICAgIEBfbWFwLnNlbGVjdChpbmRleClcbiJdfQ==