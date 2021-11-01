(function() {
  var IndexPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  IndexPage = (function(superClass) {
    extend(IndexPage, superClass);

    function IndexPage() {
      return IndexPage.__super__.constructor.apply(this, arguments);
    }

    IndexPage.prototype.initialize = function() {
      return this._initSliders();
    };
    IndexPage.prototype._initSliders = function() {
      let mainSlider = $('#main-slider');
      if (mainSlider.length) {
        mainSlider.slick({
          arrows: true,
          dots: true,
          mobileFirst: true,
          lazyLoad: 'ondemand',
          autoplay: true,
          autoplaySpeed: 10000,
        });
      }
      let mainSlider2 =$('#main-slider2');
      if (mainSlider2.length) {
        mainSlider2.slick({
          arrows: true,
          dots: true,
          mobileFirst: true
        });
      }
      let mainSliderMob = $('#main-slider-mob1');
      if (mainSliderMob.length) {
        mainSliderMob.slick({
          arrows: true,
          dots: true,
          mobileFirst: true
        });
      }

      return true;
    };

    return IndexPage;

  })(_Page);

  window.Pages.register('index', IndexPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvaW5kZXguanMiLCJzb3VyY2VzIjpbInBhZ2VzL2luZGV4LmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBLE1BQUEsU0FBQTtJQUFBOzs7RUFBTTs7Ozs7Ozt3QkFDSixVQUFBLEdBQVksU0FBQTthQUNWLElBQUMsQ0FBQSxZQUFELENBQUE7SUFEVTs7d0JBR1osWUFBQSxHQUFjLFNBQUE7TUFDWixDQUFBLENBQUUsY0FBRixDQUFpQixDQUFDLEtBQWxCLENBQ0U7UUFBQSxNQUFBLEVBQVEsS0FBUjtRQUNBLElBQUEsRUFBTSxJQUROO1FBRUEsV0FBQSxFQUFhLElBRmI7T0FERjtNQUtBLENBQUEsQ0FBRSxrQkFBRixDQUFxQixDQUFDLEtBQXRCLENBQ0U7UUFBQSxNQUFBLEVBQVEsS0FBUjtRQUNBLElBQUEsRUFBTSxJQUROO1FBRUEsV0FBQSxFQUFhLElBRmI7UUFHQSxRQUFBLEVBQVUsS0FIVjtRQUlBLFNBQUEsRUFBVyxRQUpYO1FBS0EsVUFBQSxFQUFZLENBQUEsQ0FBRSw2QkFBRixDQUxaO09BREY7YUFRQSxDQUFBLENBQUUsa0JBQUYsQ0FBcUIsQ0FBQyxLQUF0QixDQUNFO1FBQUEsSUFBQSxFQUFNLElBQU47UUFDQSxTQUFBLEVBQVcsQ0FBQSxDQUFFLHVCQUFGLENBRFg7UUFFQSxTQUFBLEVBQVcsQ0FBQSxDQUFFLHVCQUFGLENBRlg7UUFHQSxVQUFBLEVBQVksQ0FBQSxDQUFFLDZCQUFGLENBSFo7T0FERjtJQWRZOzs7O0tBSlE7O0VBd0J4QixNQUFNLENBQUMsS0FBSyxDQUFDLFFBQWIsQ0FBc0IsT0FBdEIsRUFBK0IsU0FBL0I7QUF4QkEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyBJbmRleFBhZ2UgZXh0ZW5kcyBfUGFnZVxuICBpbml0aWFsaXplOiAtPlxuICAgIEBfaW5pdFNsaWRlcnMoKVxuXG4gIF9pbml0U2xpZGVyczogLT5cbiAgICAkKCcjbWFpbi1zbGlkZXInKS5zbGlja1xuICAgICAgYXJyb3dzOiBmYWxzZVxuICAgICAgZG90czogdHJ1ZVxuICAgICAgbW9iaWxlRmlyc3Q6IHRydWVcblxuICAgICQoJyNwcm9kdWN0cy1zbGlkZXInKS5zbGlja1xuICAgICAgYXJyb3dzOiBmYWxzZVxuICAgICAgZG90czogdHJ1ZVxuICAgICAgbW9iaWxlRmlyc3Q6IHRydWVcbiAgICAgIGluZmluaXRlOiBmYWxzZVxuICAgICAgcmVzcG9uZFRvOiAnd2luZG93J1xuICAgICAgYXBwZW5kRG90czogJCgnI3Byb2R1Y3RzLXNsaWRlci1wYWdpbmF0aW9uJylcblxuICAgICQoJyN2ZXJ0aWNhbC1zbGlkZXInKS5zbGlja1xuICAgICAgZG90czogdHJ1ZVxuICAgICAgcHJldkFycm93OiAkKCcjdmVydGljYWwtc2xpZGVyLXByZXYnKVxuICAgICAgbmV4dEFycm93OiAkKCcjdmVydGljYWwtc2xpZGVyLW5leHQnKVxuICAgICAgYXBwZW5kRG90czogJCgnI3ZlcnRpY2FsLXNsaWRlci1wYWdpbmF0aW9uJylcblxud2luZG93LlBhZ2VzLnJlZ2lzdGVyICdpbmRleCcsIEluZGV4UGFnZSJdfQ==
