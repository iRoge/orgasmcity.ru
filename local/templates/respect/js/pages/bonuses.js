(function() {
  var BonusesPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  BonusesPage = (function(superClass) {
    extend(BonusesPage, superClass);

    function BonusesPage() {
      return BonusesPage.__super__.constructor.apply(this, arguments);
    }

    BonusesPage.prototype._toMobile = function() {
      return $('.bonuses-items').slick({
        arrows: false,
        dots: true,
        infinite: true,
        slidesToShow: 1
      });
    };
    BonusesPage.prototype._fromMobile = function() {
      return $('.bonuses-items').slick('unslick');
    };

    return BonusesPage;

  })(_Page);

  window.Pages.register('bonuses', BonusesPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvYm9udXNlcy5qcyIsInNvdXJjZXMiOlsicGFnZXMvYm9udXNlcy5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBLFdBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7MEJBQ0osU0FBQSxHQUFXLFNBQUE7YUFDVCxDQUFBLENBQUUsZ0JBQUYsQ0FBbUIsQ0FBQyxLQUFwQixDQUNFO1FBQUEsTUFBQSxFQUFRLEtBQVI7UUFDQSxJQUFBLEVBQU0sSUFETjtRQUVBLFFBQUEsRUFBVSxJQUZWO1FBR0EsWUFBQSxFQUFjLENBSGQ7T0FERjtJQURTOzswQkFPWCxXQUFBLEdBQWEsU0FBQTthQUNYLENBQUEsQ0FBRSxnQkFBRixDQUFtQixDQUFDLEtBQXBCLENBQTBCLFNBQTFCO0lBRFc7Ozs7S0FSVzs7RUFXMUIsTUFBTSxDQUFDLEtBQUssQ0FBQyxRQUFiLENBQXNCLFNBQXRCLEVBQWlDLFdBQWpDO0FBWEEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyBCb251c2VzUGFnZSBleHRlbmRzIF9QYWdlXG4gIF90b01vYmlsZTogLT5cbiAgICAkKCcuYm9udXNlcy1pdGVtcycpLnNsaWNrXG4gICAgICBhcnJvd3M6IGZhbHNlXG4gICAgICBkb3RzOiB0cnVlXG4gICAgICBpbmZpbml0ZTogdHJ1ZVxuICAgICAgc2xpZGVzVG9TaG93OiAxXG5cbiAgX2Zyb21Nb2JpbGU6IC0+XG4gICAgJCgnLmJvbnVzZXMtaXRlbXMnKS5zbGljaygndW5zbGljaycpXG5cbndpbmRvdy5QYWdlcy5yZWdpc3RlciAnYm9udXNlcycsIEJvbnVzZXNQYWdlIl19
