(function() {
  var Application;

  Application = (function() {
    Application.GoogleMaps = function() {};

    function Application() {
      this.api = {};
    }

    Application.prototype.init = function() {
      return this._currentPage = window.Pages.init();
    };

    Application.prototype.addUrl = function(urls) {
      if (urls == null) {
        urls = {};
      }
      return this.api = _.extend(this.api, urls);
    };

    Application.prototype.getUrl = function(alias) {
      return this.api[alias];
    };

    return Application;

  })();

  window.initMap = Application.GoogleMaps;

  $(function() {
    window.application = new Application();
    window.application.addUrl({
      shopList: '/assets/data/product-shop-list.json',
      shopListPage: '/product-shop-list.html',
      product: '/product-popup.html',
      oneClick: '/product-popup-short.html'
    });
    return window.application.init();
  });

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwbGljYXRpb24uanMiLCJzb3VyY2VzIjpbImFwcGxpY2F0aW9uLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBLE1BQUE7O0VBQU07SUFDSixXQUFDLENBQUEsVUFBRCxHQUFhLFNBQUEsR0FBQTs7SUFDQSxxQkFBQTtNQUNYLElBQUMsQ0FBQSxHQUFELEdBQU87SUFESTs7MEJBR2IsSUFBQSxHQUFNLFNBQUE7YUFDSixJQUFDLENBQUEsWUFBRCxHQUFnQixNQUFNLENBQUMsS0FBSyxDQUFDLElBQWIsQ0FBQTtJQURaOzswQkFHTixNQUFBLEdBQVEsU0FBQyxJQUFEOztRQUFDLE9BQU87O2FBQ2QsSUFBQyxDQUFBLEdBQUQsR0FBTyxDQUFDLENBQUMsTUFBRixDQUFTLElBQUMsQ0FBQSxHQUFWLEVBQWUsSUFBZjtJQUREOzswQkFHUixNQUFBLEdBQVEsU0FBQyxLQUFEO0FBQ04sYUFBTyxJQUFDLENBQUEsR0FBSSxDQUFBLEtBQUE7SUFETjs7Ozs7O0VBR1YsTUFBTSxDQUFDLE9BQVAsR0FBaUIsV0FBVyxDQUFDOztFQUU3QixDQUFBLENBQUUsU0FBQTtJQUNBLE1BQU0sQ0FBQyxXQUFQLEdBQXFCLElBQUksV0FBSixDQUFBO0lBQ3JCLE1BQU0sQ0FBQyxXQUFXLENBQUMsTUFBbkIsQ0FBMEI7TUFDeEIsUUFBQSxFQUFVLHFDQURjO01BRXhCLFlBQUEsRUFBYyx5QkFGVTtNQUd4QixPQUFBLEVBQVMscUJBSGU7TUFJeEIsUUFBQSxFQUFVLDJCQUpjO0tBQTFCO1dBTUEsTUFBTSxDQUFDLFdBQVcsQ0FBQyxJQUFuQixDQUFBO0VBUkEsQ0FBRjtBQWhCQSIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIEFwcGxpY2F0aW9uXG4gIEBHb29nbGVNYXBzOiAtPiByZXR1cm5cbiAgY29uc3RydWN0b3I6ICgpIC0+XG4gICAgQGFwaSA9IHt9XG5cbiAgaW5pdDogKCkgLT5cbiAgICBAX2N1cnJlbnRQYWdlID0gd2luZG93LlBhZ2VzLmluaXQoKVxuXG4gIGFkZFVybDogKHVybHMgPSB7fSkgLT5cbiAgICBAYXBpID0gXy5leHRlbmQgQGFwaSwgdXJsc1xuXG4gIGdldFVybDogKGFsaWFzKSAtPlxuICAgIHJldHVybiBAYXBpW2FsaWFzXVxuXG53aW5kb3cuaW5pdE1hcCA9IEFwcGxpY2F0aW9uLkdvb2dsZU1hcHNcblxuJCAtPlxuICB3aW5kb3cuYXBwbGljYXRpb24gPSBuZXcgQXBwbGljYXRpb24oKVxuICB3aW5kb3cuYXBwbGljYXRpb24uYWRkVXJsIHtcbiAgICBzaG9wTGlzdDogJy9hc3NldHMvZGF0YS9wcm9kdWN0LXNob3AtbGlzdC5qc29uJ1xuICAgIHNob3BMaXN0UGFnZTogJy9wcm9kdWN0LXNob3AtbGlzdC5odG1sJ1xuICAgIHByb2R1Y3Q6ICcvcHJvZHVjdC1wb3B1cC5odG1sJ1xuICAgIG9uZUNsaWNrOiAnL3Byb2R1Y3QtcG9wdXAtc2hvcnQuaHRtbCdcbiAgfVxuICB3aW5kb3cuYXBwbGljYXRpb24uaW5pdCgpXG4iXX0=
