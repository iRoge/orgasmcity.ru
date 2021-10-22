(function() {
  var ListPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ListPage = (function(superClass) {
    extend(ListPage, superClass);

    function ListPage() {
      return ListPage.__super__.constructor.apply(this, arguments);
    }

    ListPage.prototype.initialize = function() {
      return this._initFilters();
    };
    ListPage.prototype._initFilters = function() {
      var from, max, min, to;
      from = window.filterPriceFrom || 0;
      to = window.filterPriceTo || 15000;
      min = window.filterPriceMin || 0;
      max = window.filterPriceMax || 15000;
      if (from === to) {
        to += 1;
      }
      if (min === max) {
        max += 1;
      }
      return this._filters = new Filters($('.filters'), {
        cost: {
          start: [from, to],
          range: {
            min: min,
            max: max
          }
        }
      });
    };

    return ListPage;

  })(_Page);

  window.Pages.register('list', ListPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvbGlzdC5qcyIsInNvdXJjZXMiOlsicGFnZXMvbGlzdC5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBLFFBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7dUJBQ0osVUFBQSxHQUFZLFNBQUE7YUFDVixJQUFDLENBQUEsWUFBRCxDQUFBO0lBRFU7O3VCQUdaLFlBQUEsR0FBYyxTQUFBO0FBQ1osVUFBQTtNQUFBLElBQUEsR0FBTyxNQUFNLENBQUMsZUFBUCxJQUEwQjtNQUNqQyxFQUFBLEdBQUssTUFBTSxDQUFDLGFBQVAsSUFBd0I7TUFFN0IsR0FBQSxHQUFNLE1BQU0sQ0FBQyxjQUFQLElBQXlCO01BQy9CLEdBQUEsR0FBTSxNQUFNLENBQUMsY0FBUCxJQUF5QjtNQUUvQixJQUFJLElBQUEsS0FBUSxFQUFaO1FBQ0UsRUFBQSxJQUFNLEVBRFI7O01BR0EsSUFBSSxHQUFBLEtBQU8sR0FBWDtRQUNFLEdBQUEsSUFBTyxFQURUOzthQUdBLElBQUMsQ0FBQSxRQUFELEdBQVksSUFBSSxPQUFKLENBQVksQ0FBQSxDQUFFLFVBQUYsQ0FBWixFQUEyQjtRQUNyQyxJQUFBLEVBQ0U7VUFBQSxLQUFBLEVBQU8sQ0FBQyxJQUFELEVBQU8sRUFBUCxDQUFQO1VBQ0EsS0FBQSxFQUNFO1lBQUEsR0FBQSxFQUFLLEdBQUw7WUFDQSxHQUFBLEVBQUssR0FETDtXQUZGO1NBRm1DO09BQTNCO0lBYkE7Ozs7S0FKTzs7RUEwQnZCLE1BQU0sQ0FBQyxLQUFLLENBQUMsUUFBYixDQUFzQixNQUF0QixFQUE4QixRQUE5QjtBQTFCQSIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIExpc3RQYWdlIGV4dGVuZHMgX1BhZ2VcbiAgaW5pdGlhbGl6ZTogLT5cbiAgICBAX2luaXRGaWx0ZXJzKClcblxuICBfaW5pdEZpbHRlcnM6IC0+XG4gICAgZnJvbSA9IHdpbmRvdy5maWx0ZXJQcmljZUZyb20gfHwgMFxuICAgIHRvID0gd2luZG93LmZpbHRlclByaWNlVG8gfHwgMTUwMDBcblxuICAgIG1pbiA9IHdpbmRvdy5maWx0ZXJQcmljZU1pbiB8fCAwXG4gICAgbWF4ID0gd2luZG93LmZpbHRlclByaWNlTWF4IHx8IDE1MDAwXG5cbiAgICBpZiAoZnJvbSA9PSB0bylcbiAgICAgIHRvICs9IDE7XG5cbiAgICBpZiAobWluID09IG1heClcbiAgICAgIG1heCArPSAxO1xuXG4gICAgQF9maWx0ZXJzID0gbmV3IEZpbHRlcnMgJCgnLmZpbHRlcnMnKSwge1xuICAgICAgY29zdDpcbiAgICAgICAgc3RhcnQ6IFtmcm9tLCB0b11cbiAgICAgICAgcmFuZ2U6XG4gICAgICAgICAgbWluOiBtaW5cbiAgICAgICAgICBtYXg6IG1heFxuICAgIH1cblxuXG53aW5kb3cuUGFnZXMucmVnaXN0ZXIgJ2xpc3QnLCBMaXN0UGFnZSJdfQ==
