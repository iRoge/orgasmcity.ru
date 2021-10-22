(function() {
  var ProductPage,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ProductPage = (function(superClass) {
    extend(ProductPage, superClass);

    function ProductPage() {
      return ProductPage.__super__.constructor.apply(this, arguments);
    }

    ProductPage.prototype.initialize = function() {
      this._initGallery();
      this._initSlider();
      return this._initShopList();
    };
    ProductPage.prototype._initGallery = function() {
      this._gallery = new ProductGallery('.product-page__left .product-gallery', {
        onClickLarge: (function(_this) {
          return function(index) {
            return Popup.show(_this._gallery["export"](), {
              className: 'popup--product-gallery',
              title: false,
              onShow: function(popup) {
                return new ProductGallery($('.product-gallery', popup._container), {
                  onClickLarge: function(index) {
                    return window.Popup.hide();
                  },
                  currentIndex: index,
                  sliderOptions: {
                    slidesToShow: 7
                  }
                });
              }
            });
          };
        })(this)
      });
      return new ProductGallery('.phone--only .product-gallery');
    };

    ProductPage.prototype._initSlider = function() {
      return $('.js-slider').slick({
        arrows: true,
        dots: false,
        infinite: false,
        slidesToShow: 5,
        responsive: [
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 3
            }
          }
        ]
      });
    };

    ProductPage.prototype._toMobile = function() {
      return $('.js-products-slider').slick({
        arrows: true,
        dots: false,
        infinite: false,
        slidesToShow: 2,
        responsive: [
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2
            }
          }, {
            breakpoint: 375,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });
    };

    ProductPage.prototype._fromMobile = function() {
      return $('.js-products-slider').slick('unslick');
    };

    ProductPage.prototype._initShopList = function() {
      return $('.js-shop-list').on('click', function(event) {
        event.preventDefault();
        return $.ajax({
          method: 'get',
          url: window.application.getUrl('shopListPage'),
          success: function(response) {
            return Popup.show($(response), {
              className: 'popup--shop-list',
              title: 'Наличие товара в магазинах',
              onShow: (function(_this) {
                return function(popup) {
                  return new ShopList(popup._wrapper);
                };
              })(this)
            });
          }
        });
      });
    };

    return ProductPage;

  })(_Page);

  window.Pages.register('product', ProductPage);

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvcHJvZHVjdC5qcyIsInNvdXJjZXMiOlsicGFnZXMvcHJvZHVjdC5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBLFdBQUE7SUFBQTs7O0VBQU07Ozs7Ozs7MEJBQ0osVUFBQSxHQUFZLFNBQUE7TUFDVixJQUFDLENBQUEsWUFBRCxDQUFBO01BQ0EsSUFBQyxDQUFBLFdBQUQsQ0FBQTtNQUNBLElBQUMsQ0FBQSxhQUFELENBQUE7YUFDQSxJQUFDLENBQUEsYUFBRCxDQUFBO0lBSlU7OzBCQU1aLFlBQUEsR0FBYyxTQUFBO01BQ1osSUFBQyxDQUFBLFFBQUQsR0FBWSxJQUFJLGNBQUosQ0FBbUIsc0NBQW5CLEVBQ1Y7UUFBQSxZQUFBLEVBQWMsQ0FBQSxTQUFBLEtBQUE7aUJBQUEsU0FBQyxLQUFEO21CQUNaLEtBQUssQ0FBQyxJQUFOLENBQVcsS0FBQyxDQUFBLFFBQVEsRUFBQyxNQUFELEVBQVQsQ0FBQSxDQUFYLEVBQ0U7Y0FBQSxTQUFBLEVBQVcsd0JBQVg7Y0FDQSxLQUFBLEVBQU8sS0FEUDtjQUVBLE1BQUEsRUFBUSxTQUFDLEtBQUQ7dUJBQ04sSUFBSSxjQUFKLENBQW1CLENBQUEsQ0FBRSxrQkFBRixFQUFzQixLQUFLLENBQUMsVUFBNUIsQ0FBbkIsRUFBNEQ7a0JBQzFELFlBQUEsRUFBYyxTQUFDLEtBQUQ7MkJBQ1osTUFBTSxDQUFDLEtBQUssQ0FBQyxJQUFiLENBQUE7a0JBRFksQ0FENEM7a0JBRzFELFlBQUEsRUFBYyxLQUg0QztrQkFJMUQsYUFBQSxFQUNFO29CQUFBLFlBQUEsRUFBYyxDQUFkO21CQUx3RDtpQkFBNUQ7Y0FETSxDQUZSO2FBREY7VUFEWTtRQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBZDtPQURVO2FBYVosSUFBSSxjQUFKLENBQW1CLCtCQUFuQjtJQWRZOzswQkFnQmQsV0FBQSxHQUFhLFNBQUE7YUFDWCxDQUFBLENBQUUsWUFBRixDQUFlLENBQUMsS0FBaEIsQ0FDRTtRQUFBLE1BQUEsRUFBUSxJQUFSO1FBQ0EsSUFBQSxFQUFNLEtBRE47UUFFQSxRQUFBLEVBQVUsS0FGVjtRQUdBLFlBQUEsRUFBYyxDQUhkO1FBSUEsVUFBQSxFQUFZO1VBQ1Y7WUFDRSxVQUFBLEVBQVksR0FEZDtZQUVFLFFBQUEsRUFDRTtjQUFBLFlBQUEsRUFBYyxDQUFkO2FBSEo7V0FEVTtTQUpaO09BREY7SUFEVzs7MEJBY2IsU0FBQSxHQUFXLFNBQUE7YUFDVCxDQUFBLENBQUUscUJBQUYsQ0FBd0IsQ0FBQyxLQUF6QixDQUNFO1FBQUEsTUFBQSxFQUFRLElBQVI7UUFDQSxJQUFBLEVBQU0sS0FETjtRQUVBLFFBQUEsRUFBVSxLQUZWO1FBR0EsWUFBQSxFQUFjLENBSGQ7UUFJQSxVQUFBLEVBQVk7VUFDVjtZQUNFLFVBQUEsRUFBWSxHQURkO1lBRUUsUUFBQSxFQUNFO2NBQUEsWUFBQSxFQUFjLENBQWQ7YUFISjtXQURVLEVBTVY7WUFDRSxVQUFBLEVBQVksR0FEZDtZQUVFLFFBQUEsRUFDRTtjQUFBLFlBQUEsRUFBYyxDQUFkO2FBSEo7V0FOVTtTQUpaO09BREY7SUFEUzs7MEJBbUJYLFdBQUEsR0FBYSxTQUFBO2FBQ1gsQ0FBQSxDQUFFLHFCQUFGLENBQXdCLENBQUMsS0FBekIsQ0FBK0IsU0FBL0I7SUFEVzs7MEJBR2IsYUFBQSxHQUFlLFNBQUE7YUFDYixDQUFBLENBQUUsZUFBRixDQUFrQixDQUFDLEVBQW5CLENBQXNCLE9BQXRCLEVBQStCLFNBQUMsS0FBRDtRQUM3QixLQUFLLENBQUMsY0FBTixDQUFBO2VBQ0EsQ0FBQyxDQUFDLElBQUYsQ0FDRTtVQUFBLE1BQUEsRUFBUSxLQUFSO1VBQ0EsR0FBQSxFQUFLLE1BQU0sQ0FBQyxXQUFXLENBQUMsTUFBbkIsQ0FBMEIsU0FBMUIsQ0FETDtVQUVBLE9BQUEsRUFBUyxTQUFDLFFBQUQ7bUJBQ1AsS0FBSyxDQUFDLElBQU4sQ0FBVyxDQUFBLENBQUUsUUFBRixDQUFYLEVBQ0U7Y0FBQSxLQUFBLEVBQU8sZUFBUDtjQUNBLE1BQUEsRUFBUSxDQUFBLFNBQUEsS0FBQTt1QkFBQSxTQUFDLEtBQUQ7a0JBQ04sbUJBQUEsQ0FBQTt5QkFDQSxVQUFVLENBQUMsSUFBWCxDQUFBO2dCQUZNO2NBQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQURSO2FBREY7VUFETyxDQUZUO1NBREY7TUFGNkIsQ0FBL0I7SUFEYTs7MEJBYWYsYUFBQSxHQUFlLFNBQUE7YUFDYixDQUFBLENBQUUsZUFBRixDQUFrQixDQUFDLEVBQW5CLENBQXNCLE9BQXRCLEVBQStCLFNBQUMsS0FBRDtRQUM3QixLQUFLLENBQUMsY0FBTixDQUFBO2VBQ0EsQ0FBQyxDQUFDLElBQUYsQ0FDRTtVQUFBLE1BQUEsRUFBUSxLQUFSO1VBQ0EsR0FBQSxFQUFLLE1BQU0sQ0FBQyxXQUFXLENBQUMsTUFBbkIsQ0FBMEIsY0FBMUIsQ0FETDtVQUVBLE9BQUEsRUFBUyxTQUFDLFFBQUQ7bUJBQ1AsS0FBSyxDQUFDLElBQU4sQ0FBVyxDQUFBLENBQUUsUUFBRixDQUFYLEVBQ0U7Y0FBQSxTQUFBLEVBQVcsa0JBQVg7Y0FDQSxLQUFBLEVBQU8sNEJBRFA7Y0FFQSxNQUFBLEVBQVEsQ0FBQSxTQUFBLEtBQUE7dUJBQUEsU0FBQyxLQUFEO3lCQUNOLElBQUksUUFBSixDQUFhLEtBQUssQ0FBQyxRQUFuQjtnQkFETTtjQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FGUjthQURGO1VBRE8sQ0FGVDtTQURGO01BRjZCLENBQS9CO0lBRGE7Ozs7S0F4RVM7O0VBcUYxQixNQUFNLENBQUMsS0FBSyxDQUFDLFFBQWIsQ0FBc0IsU0FBdEIsRUFBaUMsV0FBakM7QUFyRkEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyBQcm9kdWN0UGFnZSBleHRlbmRzIF9QYWdlXG4gIGluaXRpYWxpemU6IC0+XG4gICAgQF9pbml0R2FsbGVyeSgpXG4gICAgQF9pbml0U2xpZGVyKClcbiAgICBAX2luaXRPbmVDbGljaygpXG4gICAgQF9pbml0U2hvcExpc3QoKVxuXG4gIF9pbml0R2FsbGVyeTogLT5cbiAgICBAX2dhbGxlcnkgPSBuZXcgUHJvZHVjdEdhbGxlcnkgJy5wcm9kdWN0LXBhZ2VfX2xlZnQgLnByb2R1Y3QtZ2FsbGVyeScsXG4gICAgICBvbkNsaWNrTGFyZ2U6IChpbmRleCkgPT5cbiAgICAgICAgUG9wdXAuc2hvdyBAX2dhbGxlcnkuZXhwb3J0KCksXG4gICAgICAgICAgY2xhc3NOYW1lOiAncG9wdXAtLXByb2R1Y3QtZ2FsbGVyeSdcbiAgICAgICAgICB0aXRsZTogZmFsc2VcbiAgICAgICAgICBvblNob3c6IChwb3B1cCkgPT5cbiAgICAgICAgICAgIG5ldyBQcm9kdWN0R2FsbGVyeSAkKCcucHJvZHVjdC1nYWxsZXJ5JywgcG9wdXAuX2NvbnRhaW5lciksIHtcbiAgICAgICAgICAgICAgb25DbGlja0xhcmdlOiAoaW5kZXgpIC0+XG4gICAgICAgICAgICAgICAgd2luZG93LlBvcHVwLmhpZGUoKVxuICAgICAgICAgICAgICBjdXJyZW50SW5kZXg6IGluZGV4XG4gICAgICAgICAgICAgIHNsaWRlck9wdGlvbnM6XG4gICAgICAgICAgICAgICAgc2xpZGVzVG9TaG93OiA3XG4gICAgICAgICAgICB9XG4gICAgbmV3IFByb2R1Y3RHYWxsZXJ5ICcucGhvbmUtLW9ubHkgLnByb2R1Y3QtZ2FsbGVyeSdcblxuICBfaW5pdFNsaWRlcjogLT5cbiAgICAkKCcuanMtc2xpZGVyJykuc2xpY2tcbiAgICAgIGFycm93czogdHJ1ZVxuICAgICAgZG90czogZmFsc2VcbiAgICAgIGluZmluaXRlOiBmYWxzZVxuICAgICAgc2xpZGVzVG9TaG93OiA1XG4gICAgICByZXNwb25zaXZlOiBbXG4gICAgICAgIHtcbiAgICAgICAgICBicmVha3BvaW50OiA2MDBcbiAgICAgICAgICBzZXR0aW5nczpcbiAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogM1xuICAgICAgICB9XG4gICAgICBdXG5cbiAgX3RvTW9iaWxlOiAtPlxuICAgICQoJy5qcy1wcm9kdWN0cy1zbGlkZXInKS5zbGlja1xuICAgICAgYXJyb3dzOiB0cnVlXG4gICAgICBkb3RzOiBmYWxzZVxuICAgICAgaW5maW5pdGU6IGZhbHNlXG4gICAgICBzbGlkZXNUb1Nob3c6IDJcbiAgICAgIHJlc3BvbnNpdmU6IFtcbiAgICAgICAge1xuICAgICAgICAgIGJyZWFrcG9pbnQ6IDYwMFxuICAgICAgICAgIHNldHRpbmdzOlxuICAgICAgICAgICAgc2xpZGVzVG9TaG93OiAyXG4gICAgICAgIH0sXG4gICAgICAgIHtcbiAgICAgICAgICBicmVha3BvaW50OiAzNzVcbiAgICAgICAgICBzZXR0aW5nczpcbiAgICAgICAgICAgIHNsaWRlc1RvU2hvdzogMVxuICAgICAgICB9XG4gICAgICBdXG5cbiAgX2Zyb21Nb2JpbGU6IC0+XG4gICAgJCgnLmpzLXByb2R1Y3RzLXNsaWRlcicpLnNsaWNrKCd1bnNsaWNrJylcblxuICBfaW5pdE9uZUNsaWNrOiAtPlxuICAgICQoJy5qcy1vbmUtY2xpY2snKS5vbiAnY2xpY2snLCAoZXZlbnQpIC0+XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpXG4gICAgICAkLmFqYXhcbiAgICAgICAgbWV0aG9kOiAnZ2V0J1xuICAgICAgICB1cmw6IHdpbmRvdy5hcHBsaWNhdGlvbi5nZXRVcmwoJ3Byb2R1Y3QnKVxuICAgICAgICBzdWNjZXNzOiAocmVzcG9uc2UpIC0+XG4gICAgICAgICAgUG9wdXAuc2hvdyAkKHJlc3BvbnNlKSxcbiAgICAgICAgICAgIHRpdGxlOiAn0JHRi9GB0YLRgNGL0Lkg0LfQsNC60LDQtydcbiAgICAgICAgICAgIG9uU2hvdzogKHBvcHVwKSA9PlxuICAgICAgICAgICAgICBvbk9wZW5Nb2RhbE9uZUNsaWNrKClcbiAgICAgICAgICAgICAgQ291bnRJbnB1dC5pbml0KClcblxuICBfaW5pdFNob3BMaXN0OiAtPlxuICAgICQoJy5qcy1zaG9wLWxpc3QnKS5vbiAnY2xpY2snLCAoZXZlbnQpIC0+XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpXG4gICAgICAkLmFqYXhcbiAgICAgICAgbWV0aG9kOiAnZ2V0J1xuICAgICAgICB1cmw6IHdpbmRvdy5hcHBsaWNhdGlvbi5nZXRVcmwoJ3Nob3BMaXN0UGFnZScpXG4gICAgICAgIHN1Y2Nlc3M6IChyZXNwb25zZSkgLT5cbiAgICAgICAgICBQb3B1cC5zaG93ICQocmVzcG9uc2UpLFxuICAgICAgICAgICAgY2xhc3NOYW1lOiAncG9wdXAtLXNob3AtbGlzdCdcbiAgICAgICAgICAgIHRpdGxlOiAn0J3QsNC70LjRh9C40LUg0YLQvtCy0LDRgNCwINCyINC80LDQs9Cw0LfQuNC90LDRhSdcbiAgICAgICAgICAgIG9uU2hvdzogKHBvcHVwKSA9PlxuICAgICAgICAgICAgICBuZXcgU2hvcExpc3QgcG9wdXAuX3dyYXBwZXJcblxud2luZG93LlBhZ2VzLnJlZ2lzdGVyICdwcm9kdWN0JywgUHJvZHVjdFBhZ2UiXX0=
