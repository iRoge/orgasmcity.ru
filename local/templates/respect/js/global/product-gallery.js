(function() {
  var ProductGallery;

  ProductGallery = (function() {
    ProductGallery.options = {
      onClickLarge: function() {},
      currentIndex: 0
    };

    ProductGallery.sliderOptions = {
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
    };

    function ProductGallery(container, options) {
      if (options == null) {
        options = {};
      }
      this._options = _.extend(_.clone(ProductGallery.options), options);
      this._container = $(container);
      this._slider = $('.product-gallery__slider', this._container);
      this._links = $('a', this._slider);
      this._currentIndex = this._options.currentIndex;
      this._initLarge();
      this._initSlider();
      this._events();
      this.show(this._currentIndex);
    }

    ProductGallery.prototype._initLarge = function() {
      this._largeContainer = $('<div class="product-gallery__large">');
      this._large = $('<a class="product-gallery__large-link"></a>').appendTo(this._largeContainer);
      return this._container.prepend(this._largeContainer);
    };

    ProductGallery.prototype._initSlider = function() {
      var options;
      options = _.extend(_.clone(ProductGallery.sliderOptions), this._options.sliderOptions);
      return this._slider.slick(options);
    };

    ProductGallery.prototype._events = function() {
      this._links.on('click', _.bind(this._selectImage, this));
      return this._large.on('click', (function(_this) {
        return function() {
          return _this._options.onClickLarge(_this._currentIndex);
        };
      })(this));
    };

    ProductGallery.prototype._selectImage = function(event) {
      event.preventDefault();
      return this.show($(event.currentTarget).index());
    };

    ProductGallery.prototype.show = function(index) {
      this._currentIndex = index;
      this._large.css('backgroundImage', "url(" + (this._getImageSource(index)) + ")");
      this._links.removeClass('selected');
      return $(this._links[index]).addClass('selected');
    };

    ProductGallery.prototype._getImageSource = function(index) {
      return $(this._links[index]).attr('href');
    };

    ProductGallery.prototype["export"] = function() {
      var dom, i, len, link, ref;
      dom = $('<section class="product-gallery">\n  <div class="product-gallery__slider">\n\n  </div>\n</section>');
      ref = this._links;
      for (i = 0, len = ref.length; i < len; i++) {
        link = ref[i];
        $('.product-gallery__slider', dom).append($(link).clone());
      }
      return dom;
    };

    return ProductGallery;

  })();

  window.ProductGallery = ProductGallery;

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicHJvZHVjdC1nYWxsZXJ5LmpzIiwic291cmNlcyI6WyJwcm9kdWN0LWdhbGxlcnkuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUEsTUFBQTs7RUFBTTtJQUNKLGNBQUMsQ0FBQSxPQUFELEdBQ0U7TUFBQSxZQUFBLEVBQWMsU0FBQSxHQUFBLENBQWQ7TUFDQSxZQUFBLEVBQWMsQ0FEZDs7O0lBRUYsY0FBQyxDQUFBLGFBQUQsR0FDRTtNQUFBLE1BQUEsRUFBUSxJQUFSO01BQ0EsSUFBQSxFQUFNLEtBRE47TUFFQSxRQUFBLEVBQVUsS0FGVjtNQUdBLFlBQUEsRUFBYyxDQUhkO01BSUEsVUFBQSxFQUFZO1FBQ1Y7VUFDRSxVQUFBLEVBQVksR0FEZDtVQUVFLFFBQUEsRUFDRTtZQUFBLFlBQUEsRUFBYyxDQUFkO1dBSEo7U0FEVTtPQUpaOzs7SUFXVyx3QkFBQyxTQUFELEVBQVksT0FBWjs7UUFBWSxVQUFVOztNQUNqQyxJQUFDLENBQUEsUUFBRCxHQUFZLENBQUMsQ0FBQyxNQUFGLENBQVMsQ0FBQyxDQUFDLEtBQUYsQ0FBUSxjQUFjLENBQUMsT0FBdkIsQ0FBVCxFQUEwQyxPQUExQztNQUNaLElBQUMsQ0FBQSxVQUFELEdBQWMsQ0FBQSxDQUFFLFNBQUY7TUFDZCxJQUFDLENBQUEsT0FBRCxHQUFXLENBQUEsQ0FBRSwwQkFBRixFQUE4QixJQUFDLENBQUEsVUFBL0I7TUFDWCxJQUFDLENBQUEsTUFBRCxHQUFVLENBQUEsQ0FBRSxHQUFGLEVBQU8sSUFBQyxDQUFBLE9BQVI7TUFFVixJQUFDLENBQUEsYUFBRCxHQUFpQixJQUFDLENBQUEsUUFBUSxDQUFDO01BRTNCLElBQUMsQ0FBQSxVQUFELENBQUE7TUFDQSxJQUFDLENBQUEsV0FBRCxDQUFBO01BQ0EsSUFBQyxDQUFBLE9BQUQsQ0FBQTtNQUVBLElBQUMsQ0FBQSxJQUFELENBQU0sSUFBQyxDQUFBLGFBQVA7SUFaVzs7NkJBY2IsVUFBQSxHQUFZLFNBQUE7TUFDVixJQUFDLENBQUEsZUFBRCxHQUFtQixDQUFBLENBQUUsc0NBQUY7TUFDbkIsSUFBQyxDQUFBLE1BQUQsR0FBVSxDQUFBLENBQUUsNkNBQUYsQ0FBZ0QsQ0FBQyxRQUFqRCxDQUEwRCxJQUFDLENBQUEsZUFBM0Q7YUFFVixJQUFDLENBQUEsVUFBVSxDQUFDLE9BQVosQ0FBb0IsSUFBQyxDQUFBLGVBQXJCO0lBSlU7OzZCQU1aLFdBQUEsR0FBYSxTQUFBO0FBQ1gsVUFBQTtNQUFBLE9BQUEsR0FBVSxDQUFDLENBQUMsTUFBRixDQUFTLENBQUMsQ0FBQyxLQUFGLENBQVEsY0FBYyxDQUFDLGFBQXZCLENBQVQsRUFBZ0QsSUFBQyxDQUFBLFFBQVEsQ0FBQyxhQUExRDthQUNWLElBQUMsQ0FBQSxPQUFPLENBQUMsS0FBVCxDQUFlLE9BQWY7SUFGVzs7NkJBSWIsT0FBQSxHQUFTLFNBQUE7TUFDUCxJQUFDLENBQUEsTUFBTSxDQUFDLEVBQVIsQ0FBVyxPQUFYLEVBQW9CLENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLFlBQVIsRUFBc0IsSUFBdEIsQ0FBcEI7YUFDQSxJQUFDLENBQUEsTUFBTSxDQUFDLEVBQVIsQ0FBVyxPQUFYLEVBQW9CLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQTtpQkFDbEIsS0FBQyxDQUFBLFFBQVEsQ0FBQyxZQUFWLENBQXVCLEtBQUMsQ0FBQSxhQUF4QjtRQURrQjtNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBcEI7SUFGTzs7NkJBS1QsWUFBQSxHQUFjLFNBQUMsS0FBRDtNQUNaLEtBQUssQ0FBQyxjQUFOLENBQUE7YUFDQSxJQUFDLENBQUEsSUFBRCxDQUFNLENBQUEsQ0FBRSxLQUFLLENBQUMsYUFBUixDQUFzQixDQUFDLEtBQXZCLENBQUEsQ0FBTjtJQUZZOzs2QkFJZCxJQUFBLEdBQU0sU0FBQyxLQUFEO01BQ0osSUFBQyxDQUFBLGFBQUQsR0FBaUI7TUFDakIsSUFBQyxDQUFBLE1BQU0sQ0FBQyxHQUFSLENBQVksaUJBQVosRUFBK0IsTUFBQSxHQUFNLENBQUMsSUFBQyxDQUFBLGVBQUQsQ0FBaUIsS0FBakIsQ0FBRCxDQUFOLEdBQStCLEdBQTlEO01BQ0EsSUFBQyxDQUFBLE1BQU0sQ0FBQyxXQUFSLENBQW9CLFVBQXBCO2FBQ0EsQ0FBQSxDQUFFLElBQUMsQ0FBQSxNQUFPLENBQUEsS0FBQSxDQUFWLENBQWlCLENBQUMsUUFBbEIsQ0FBMkIsVUFBM0I7SUFKSTs7NkJBTU4sZUFBQSxHQUFpQixTQUFDLEtBQUQ7YUFDZixDQUFBLENBQUUsSUFBQyxDQUFBLE1BQU8sQ0FBQSxLQUFBLENBQVYsQ0FBaUIsQ0FBQyxJQUFsQixDQUF1QixNQUF2QjtJQURlOzs4QkFHakIsUUFBQSxHQUFRLFNBQUE7QUFDTixVQUFBO01BQUEsR0FBQSxHQUFNLENBQUEsQ0FBRSxvR0FBRjtBQU9OO0FBQUEsV0FBQSxxQ0FBQTs7UUFDRSxDQUFBLENBQUUsMEJBQUYsRUFBOEIsR0FBOUIsQ0FBa0MsQ0FBQyxNQUFuQyxDQUEwQyxDQUFBLENBQUUsSUFBRixDQUFPLENBQUMsS0FBUixDQUFBLENBQTFDO0FBREY7QUFHQSxhQUFPO0lBWEQ7Ozs7OztFQWFWLE1BQU0sQ0FBQyxjQUFQLEdBQXdCO0FBdkV4QiIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIFByb2R1Y3RHYWxsZXJ5XG4gIEBvcHRpb25zOlxuICAgIG9uQ2xpY2tMYXJnZTogKCkgLT4gcmV0dXJuXG4gICAgY3VycmVudEluZGV4OiAwXG4gIEBzbGlkZXJPcHRpb25zOlxuICAgIGFycm93czogdHJ1ZVxuICAgIGRvdHM6IGZhbHNlXG4gICAgaW5maW5pdGU6IGZhbHNlXG4gICAgc2xpZGVzVG9TaG93OiA1XG4gICAgcmVzcG9uc2l2ZTogW1xuICAgICAge1xuICAgICAgICBicmVha3BvaW50OiA2MDBcbiAgICAgICAgc2V0dGluZ3M6XG4gICAgICAgICAgc2xpZGVzVG9TaG93OiAzXG4gICAgICB9XG4gICAgXVxuICBjb25zdHJ1Y3RvcjogKGNvbnRhaW5lciwgb3B0aW9ucyA9IHt9KSAtPlxuICAgIEBfb3B0aW9ucyA9IF8uZXh0ZW5kIF8uY2xvbmUoUHJvZHVjdEdhbGxlcnkub3B0aW9ucyksIG9wdGlvbnNcbiAgICBAX2NvbnRhaW5lciA9ICQoY29udGFpbmVyKVxuICAgIEBfc2xpZGVyID0gJCgnLnByb2R1Y3QtZ2FsbGVyeV9fc2xpZGVyJywgQF9jb250YWluZXIpXG4gICAgQF9saW5rcyA9ICQoJ2EnLCBAX3NsaWRlcilcblxuICAgIEBfY3VycmVudEluZGV4ID0gQF9vcHRpb25zLmN1cnJlbnRJbmRleFxuXG4gICAgQF9pbml0TGFyZ2UoKVxuICAgIEBfaW5pdFNsaWRlcigpXG4gICAgQF9ldmVudHMoKVxuXG4gICAgQHNob3coQF9jdXJyZW50SW5kZXgpXG5cbiAgX2luaXRMYXJnZTogLT5cbiAgICBAX2xhcmdlQ29udGFpbmVyID0gJCgnPGRpdiBjbGFzcz1cInByb2R1Y3QtZ2FsbGVyeV9fbGFyZ2VcIj4nKVxuICAgIEBfbGFyZ2UgPSAkKCc8YSBjbGFzcz1cInByb2R1Y3QtZ2FsbGVyeV9fbGFyZ2UtbGlua1wiPjwvYT4nKS5hcHBlbmRUbyBAX2xhcmdlQ29udGFpbmVyXG5cbiAgICBAX2NvbnRhaW5lci5wcmVwZW5kIEBfbGFyZ2VDb250YWluZXJcblxuICBfaW5pdFNsaWRlcjogLT5cbiAgICBvcHRpb25zID0gXy5leHRlbmQgXy5jbG9uZShQcm9kdWN0R2FsbGVyeS5zbGlkZXJPcHRpb25zKSwgQF9vcHRpb25zLnNsaWRlck9wdGlvbnNcbiAgICBAX3NsaWRlci5zbGljayBvcHRpb25zXG5cbiAgX2V2ZW50czogLT5cbiAgICBAX2xpbmtzLm9uICdjbGljaycsIF8uYmluZCBAX3NlbGVjdEltYWdlLCBAXG4gICAgQF9sYXJnZS5vbiAnY2xpY2snLCA9PlxuICAgICAgQF9vcHRpb25zLm9uQ2xpY2tMYXJnZShAX2N1cnJlbnRJbmRleClcblxuICBfc2VsZWN0SW1hZ2U6IChldmVudCkgLT5cbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpXG4gICAgQHNob3cgJChldmVudC5jdXJyZW50VGFyZ2V0KS5pbmRleCgpXG5cbiAgc2hvdzogKGluZGV4KSAtPlxuICAgIEBfY3VycmVudEluZGV4ID0gaW5kZXhcbiAgICBAX2xhcmdlLmNzcyAnYmFja2dyb3VuZEltYWdlJywgXCJ1cmwoI3tAX2dldEltYWdlU291cmNlKGluZGV4KX0pXCJcbiAgICBAX2xpbmtzLnJlbW92ZUNsYXNzICdzZWxlY3RlZCdcbiAgICAkKEBfbGlua3NbaW5kZXhdKS5hZGRDbGFzcyAnc2VsZWN0ZWQnXG5cbiAgX2dldEltYWdlU291cmNlOiAoaW5kZXgpIC0+XG4gICAgJChAX2xpbmtzW2luZGV4XSkuYXR0cignaHJlZicpXG5cbiAgZXhwb3J0OiAtPlxuICAgIGRvbSA9ICQoJycnXG48c2VjdGlvbiBjbGFzcz1cInByb2R1Y3QtZ2FsbGVyeVwiPlxuICA8ZGl2IGNsYXNzPVwicHJvZHVjdC1nYWxsZXJ5X19zbGlkZXJcIj5cblxuICA8L2Rpdj5cbjwvc2VjdGlvbj5cbicnJylcbiAgICBmb3IgbGluayBpbiBAX2xpbmtzXG4gICAgICAkKCcucHJvZHVjdC1nYWxsZXJ5X19zbGlkZXInLCBkb20pLmFwcGVuZCAkKGxpbmspLmNsb25lKClcblxuICAgIHJldHVybiBkb21cblxud2luZG93LlByb2R1Y3RHYWxsZXJ5ID0gUHJvZHVjdEdhbGxlcnkiXX0=
