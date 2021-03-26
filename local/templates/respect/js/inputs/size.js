(function() {
  window.SizeInput = (function() {
    SizeInput.init = function(container) {
      if (container == null) {
        container = $('body');
      }
      return $('.size-selector a', container).each(function(index, element) {
        if ($(element).data('size-input')) {
          return;
        }
        return new SizeInput(element);
      });
    };

    function SizeInput(element) {
      this._element = $(element);
      this._element.data('size-input', this);
      this._size = this._element.data('size') || 0;
      this._count = parseInt($('.size-selector__count', this._element).text());
      if (_.isNaN(this._count)) {
        this._count = 0;
      }
      this._element.on('click', _.bind(this.show, this));
    }

    SizeInput.prototype._render = function() {
      var template;
      template = _.template('<form class="size-form tooltip-popup" style="display:none">\n  <table>\n    <thead>\n      <tr>\n        <th>Размер</th>\n        <th>Количество</th>\n      </tr>\n    </thead>\n    <tbody>\n      <tr>\n        <td class="text--primary"><%= size %></td>\n        <td>\n          <input type="text" class="counter" value="<%= count %>">\n        </td>\n      </tr>\n    </tbody>\n    <tfoot>\n      <tr>\n        <td>\n          <a class="button button--outline button--block js-hide">Отмена</a>\n        </td>\n        <td>\n          <a class="button button--primary button--block js-save">Сохранить</a>\n        </td>\n      </tr>\n    </tr>\n    </tfoot>\n  </table>\n</form>');
      this._element.append(template({
        size: this._size,
        count: this._count
      }));
      this._container = $('form.size-form', this._element);
      $('.js-save', this._container).on('click', _.bind(this.save, this));
      $('.js-hide', this._container).on('click', _.bind(this.clear, this));
      $(document).on('mouseup', (function(_this) {
        return function(event) {
          if (!_this._element.is(event.target) && _this._element.has(event.target).length === 0) {
            return _this.clear();
          }
        };
      })(this));
      return CountInput.init(this._container);
    };

    SizeInput.prototype.save = function() {
      var sizeIdentity;
      this._count = parseInt($('.counter', this._element).val());
      sizeIdentity = $('.size-selector__count', this._element);
      if (!sizeIdentity.length) {
        sizeIdentity = $('<div class="size-selector__count">');
        this._element.prepend(sizeIdentity);
      }
      sizeIdentity.text(this._count);
      if (this._count === 0) {
        sizeIdentity.remove();
      }
      this._element.toggleClass('selected', this._count > 0);
      return this.hide();
    };

    SizeInput.prototype.show = function() {
      if (!this._container) {
        this._render();
      }
      return this._container.show();
    };

    SizeInput.prototype.clear = function() {
      $('.counter', this._container).val(this._count);
      return this.hide();
    };

    SizeInput.prototype.hide = function() {
      if (!this._container) {
        return;
      }
      return setTimeout((function(_this) {
        return function() {
          return _this._container.hide();
        };
      })(this), 0);
    };

    return SizeInput;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL3NpemUuanMiLCJzb3VyY2VzIjpbImlucHV0cy9zaXplLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUFNLE1BQU0sQ0FBQztJQUNYLFNBQUMsQ0FBQSxJQUFELEdBQU8sU0FBQyxTQUFEOztRQUFDLFlBQVksQ0FBQSxDQUFFLE1BQUY7O2FBQ2xCLENBQUEsQ0FBRSxrQkFBRixFQUFzQixTQUF0QixDQUFnQyxDQUFDLElBQWpDLENBQXNDLFNBQUMsS0FBRCxFQUFRLE9BQVI7UUFDcEMsSUFBVSxDQUFBLENBQUUsT0FBRixDQUFVLENBQUMsSUFBWCxDQUFnQixZQUFoQixDQUFWO0FBQUEsaUJBQUE7O2VBQ0EsSUFBSSxTQUFKLENBQWMsT0FBZDtNQUZvQyxDQUF0QztJQURLOztJQUtNLG1CQUFDLE9BQUQ7TUFDWCxJQUFDLENBQUEsUUFBRCxHQUFZLENBQUEsQ0FBRSxPQUFGO01BQ1osSUFBQyxDQUFBLFFBQVEsQ0FBQyxJQUFWLENBQWUsWUFBZixFQUE2QixJQUE3QjtNQUNBLElBQUMsQ0FBQSxLQUFELEdBQVMsSUFBQyxDQUFBLFFBQVEsQ0FBQyxJQUFWLENBQWUsTUFBZixDQUFBLElBQTBCO01BQ25DLElBQUMsQ0FBQSxNQUFELEdBQVUsUUFBQSxDQUFTLENBQUEsQ0FBRSx1QkFBRixFQUEyQixJQUFDLENBQUEsUUFBNUIsQ0FBcUMsQ0FBQyxJQUF0QyxDQUFBLENBQVQ7TUFDVixJQUFlLENBQUMsQ0FBQyxLQUFGLENBQVEsSUFBQyxDQUFBLE1BQVQsQ0FBZjtRQUFBLElBQUMsQ0FBQSxNQUFELEdBQVUsRUFBVjs7TUFFQSxJQUFDLENBQUEsUUFBUSxDQUFDLEVBQVYsQ0FBYSxPQUFiLEVBQXNCLENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLElBQVIsRUFBYyxJQUFkLENBQXRCO0lBUFc7O3dCQVNiLE9BQUEsR0FBUyxTQUFBO0FBQ1AsVUFBQTtNQUFBLFFBQUEsR0FBVyxDQUFDLENBQUMsUUFBRixDQUFXLHdxQkFB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b250YWluZXIgPSAkKCdib2R5JykpLT5cbiAgICAkKCcuc2l6ZS1zZWxlY3RvciBhJywgY29udGFpbmVyKS5lYWNoIChpbmRleCwgZWxlbWVudCkgLT5cbiAgICAgIHJldHVybiBpZiAkKGVsZW1lbnQpLmRhdGEgJ3NpemUtaW5wdXQnXG4gICAgICBuZXcgU2l6ZUlucHV0IGVsZW1lbnRcblxuICBjb25zdHJ1Y3RvcjogKGVsZW1lbnQpIC0+XG4gICAgQF9lbGVtZW50ID0gJChlbGVtZW50KVxuICAgIEBfZWxlbWVudC5kYXRhICdzaXplLWlucHV0JywgQFxuICAgIEBfc2l6ZSA9IEBfZWxlbWVudC5kYXRhKCdzaXplJykgfHwgMFxuICAgIEBfY291bnQgPSBwYXJzZUludCAkKCcuc2l6ZS1zZWxlY3Rvcl9fY291bnQnLCBAX2VsZW1lbnQpLnRleHQoKVxuICAgIEBfY291bnQgPSAwIGlmIF8uaXNOYU4gQF9jb3VudFxuXG4gICAgQF9lbGVtZW50Lm9uICdjbGljaycsIF8uYmluZCBAc2hvdywgQFxuXG4gIF9yZW5kZXI6IC0+XG4gICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlICcnJ1xuICA8Zm9ybSBjbGFzcz1cInNpemUtZm9ybSB0b29sdGlwLXBvcHVwXCIgc3R5bGU9XCJkaXNwbGF5Om5vbmVcIj5cbiAgICA8dGFibGU+XG4gICAgICA8dGhlYWQ+XG4gICAgICAgIDx0cj5cbiAgICAgICAgICA8dGg+0KDQsNC30LzQtdGAPC90aD5cbiAgICAgICAgICA8dGg+0JrQvtC70LjRh9C10YHRgtCy0L48L3RoPlxuICAgICAgICA8L3RyPlxuICAgICAgPC90aGVhZD5cbiAgICAgIDx0Ym9keT5cbiAgICAgICAgPHRyPlxuICAgICAgICAgIDx0ZCBjbGFzcz1cInRleHQtLXByaW1hcnlcIj48JT0gc2l6ZSAlPjwvdGQ+XG4gICAgICAgICAgPHRkPlxuICAgICAgICAgICAgPGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJjb3VudGVyXCIgdmFsdWU9XCI8JT0gY291bnQgJT5cIj5cbiAgICAgICAgICA8L3RkPlxuICAgICAgICA8L3RyPlxuICAgICAgPC90Ym9keT5cbiAgICAgIDx0Zm9vdD5cbiAgICAgICAgPHRyPlxuICAgICAgICAgIDx0ZD5cbiAgICAgICAgICAgIDxhIGNsYXNzPVwiYnV0dG9uIGJ1dHRvbi0tb3V0bGluZSBidXR0b24tLWJsb2NrIGpzLWhpZGVcIj7QntGC0LzQtdC90LA8L2E+XG4gICAgICAgICAgPC90ZD5cbiAgICAgICAgICA8dGQ+XG4gICAgICAgICAgICA8YSBjbGFzcz1cImJ1dHRvbiBidXR0b24tLXByaW1hcnkgYnV0dG9uLS1ibG9jayBqcy1zYXZlXCI+0KHQvtGF0YDQsNC90LjRgtGMPC9hPlxuICAgICAgICAgIDwvdGQ+XG4gICAgICAgIDwvdHI+XG4gICAgICA8L3RyPlxuICAgICAgPC90Zm9vdD5cbiAgICA8L3RhYmxlPlxuICA8L2Zvcm0+XG4nJydcbiAgICBAX2VsZW1lbnQuYXBwZW5kIHRlbXBsYXRlKHtcbiAgICAgIHNpemU6IEBfc2l6ZVxuICAgICAgY291bnQ6IEBfY291bnRcbiAgICB9KVxuXG4gICAgQF9jb250YWluZXIgPSAkKCdmb3JtLnNpemUtZm9ybScsIEBfZWxlbWVudClcblxuICAgICQoJy5qcy1zYXZlJywgQF9jb250YWluZXIpLm9uICdjbGljaycsIF8uYmluZCBAc2F2ZSwgQFxuXG4gICAgJCgnLmpzLWhpZGUnLCBAX2NvbnRhaW5lcikub24gJ2NsaWNrJywgXy5iaW5kIEBjbGVhciwgQFxuXG4gICAgJChkb2N1bWVudCkub24gJ21vdXNldXAnLCAoZXZlbnQpID0+XG4gICAgICBpZiAhQF9lbGVtZW50LmlzKGV2ZW50LnRhcmdldCkgYW5kIEBfZWxlbWVudC5oYXMoZXZlbnQudGFyZ2V0KS5sZW5ndGggPT0gMFxuICAgICAgICBAY2xlYXIoKVxuXG4gICAgQ291bnRJbnB1dC5pbml0KEBfY29udGFpbmVyKVxuXG4gIHNhdmU6IC0+XG4gICAgQF9jb3VudCA9IHBhcnNlSW50KCQoJy5jb3VudGVyJywgQF9lbGVtZW50KS52YWwoKSlcbiAgICBzaXplSWRlbnRpdHkgPSAkKCcuc2l6ZS1zZWxlY3Rvcl9fY291bnQnLCBAX2VsZW1lbnQpXG5cbiAgICB1bmxlc3Mgc2l6ZUlkZW50aXR5Lmxlbmd0aFxuICAgICAgc2l6ZUlkZW50aXR5ID0gJCgnPGRpdiBjbGFzcz1cInNpemUtc2VsZWN0b3JfX2NvdW50XCI+JylcbiAgICAgIEBfZWxlbWVudC5wcmVwZW5kIHNpemVJZGVudGl0eVxuXG4gICAgc2l6ZUlkZW50aXR5LnRleHQgQF9jb3VudFxuICAgIGlmIEBfY291bnQgPT0gMFxuICAgICAgc2l6ZUlkZW50aXR5LnJlbW92ZSgpXG4gICAgQF9lbGVtZW50LnRvZ2dsZUNsYXNzICdzZWxlY3RlZCcsIEBfY291bnQgPiAwXG4gICAgQGhpZGUoKVxuXG4gIHNob3c6IC0+XG4gICAgQF9yZW5kZXIoKSB1bmxlc3MgQF9jb250YWluZXJcbiAgICBAX2NvbnRhaW5lci5zaG93KClcblxuICBjbGVhcjogLT5cbiAgICAkKCcuY291bnRlcicsIEBfY29udGFpbmVyKS52YWwoQF9jb3VudClcbiAgICBAaGlkZSgpXG5cbiAgaGlkZTogLT5cbiAgICByZXR1cm4gdW5sZXNzIEBfY29udGFpbmVyXG4gICAgc2V0VGltZW91dCA9PlxuICAgICAgQF9jb250YWluZXIuaGlkZSgpXG4gICAgLCAwXG5cblxuXG5cbiJdfQ==
