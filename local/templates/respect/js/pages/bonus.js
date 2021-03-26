(function() {
  var BonusPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  BonusPage = (function(superClass) {
    extend(BonusPage, superClass);

    function BonusPage() {
      return BonusPage.__super__.constructor.apply(this, arguments);
    }

    BonusPage.prototype._toMobile = function() {
      return $('.bonus-slider').slick({
        arrows: false,
        dots: true,
        infinite: true,
        slidesToShow: 1
      });
    };

    BonusPage.prototype._fromMobile = function() {
      return $('.bonus-slider').slick('unslick');
    };

    return BonusPage;

  })(_Page);

  window.Pages.register('bonus', BonusPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvYm9udXMuanMiLCJzb3VyY2VzIjpbInBhZ2VzL2JvbnVzLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBLE1BQUEsU0FBQTtJQUFBOzs7RUFBTTs7Ozs7Ozt3QkFDSixTQUFBLEdBQVcsU0FBQTthQUNULENBQUEsQ0FBRSxlQUFGLENBQWtCLENBQUMsS0FBbkIsQ0FDRTtRQUFBLE1BQUEsRUFBUSxLQUFSO1FBQ0EsSUFBQSxFQUFNLElBRE47UUFFQSxRQUFBLEVBQVUsSUFGVjtRQUdBLFlBQUEsRUFBYyxDQUhkO09BREY7SUFEUzs7d0JBT1gsV0FBQSxHQUFhLFNBQUE7YUFDWCxDQUFBLENBQUUsZUFBRixDQUFrQixDQUFDLEtBQW5CLENBQXlCLFNBQXpCO0lBRFc7Ozs7S0FSUzs7RUFXeEIsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLE9BQXRCLEVBQStCLFNBQS9CO0FBWEEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyBCb251c1BhZ2UgZXh0ZW5kcyBfUGFnZVxuICBfdG9Nb2JpbGU6IC0+XG4gICAgJCgnLmJvbnVzLXNsaWRlcicpLnNsaWNrXG4gICAgICBhcnJvd3M6IGZhbHNlXG4gICAgICBkb3RzOiB0cnVlXG4gICAgICBpbmZpbml0ZTogdHJ1ZVxuICAgICAgc2xpZGVzVG9TaG93OiAxXG5cbiAgX2Zyb21Nb2JpbGU6IC0+XG4gICAgJCgnLmJvbnVzLXNsaWRlcicpLnNsaWNrKCd1bnNsaWNrJylcblxud2luZG93LlBhZ2VzLnJlZ2lzdGVyICdib251cycsIEJvbnVzUGFnZSJdfQ==
