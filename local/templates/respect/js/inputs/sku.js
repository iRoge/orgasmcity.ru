(function() {
  window.SkuPopup = (function() {
    SkuPopup.init = function(container) {
      if (container == null) {
        container = $('body');
      }
      return $('.js-sku', container).each(function(index, element) {
        if ($(element).data('sku-popup')) {
          return;
        }
        return new SkuPopup(element);
      });
    };

    function SkuPopup(element) {
      this._element = $(element);
      this._element.data('sku-popup', this);
      this._sku = this._element.data('sku') || 0;
      this._element.on('click', _.bind(this.show, this));
    }

    SkuPopup.prototype._render = function() {
      var template;
      template = _.template('<form class="sku-form tooltip-popup" style="display:none">\n  <table>\n    <thead>\n      <tr>\n        <th id="<%= id %>"><%= sku %></th>\n      </tr>\n    </thead>\n    <tfoot>\n      <tr>\n        <td>\n          <a class="button button--primary button--block js-copy js-hide" data-clipboard-target="#<%= id %>">Скопировать</a>\n        </td>\n      </tr>\n    </tr>\n    </tfoot>\n  </table>\n</form>');
      this._element.append(template({
        id: _.uniqueId('sku_'),
        sku: this._sku
      }));
      this._container = $('form.sku-form', this._element);
      new Clipboard($('.js-copy', this._container)[0]);
      $('.js-hide', this._container).on('click', _.bind(this.hide, this));
      return $(document).on('mouseup', (function(_this) {
        return function(event) {
          if (!_this._element.is(event.target) && _this._element.has(event.target).length === 0) {
            return _this.hide();
          }
        };
      })(this));
    };

    SkuPopup.prototype.show = function() {
      if (!this._container) {
        this._render();
      }
      this._element.addClass('expanded');
      return this._container.show();
    };

    SkuPopup.prototype.hide = function() {
      if (!this._container) {
        return;
      }
      return setTimeout((function(_this) {
        return function() {
          _this._element.removeClass('expanded');
          return _this._container.hide();
        };
      })(this), 0);
    };

    return SkuPopup;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL3NrdS5qcyIsInNvdXJjZXMiOlsiaW5wdXRzL3NrdS5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFBTSxNQUFNLENBQUM7SUFDWCxRQUFDLENBQUEsSUFBRCxHQUFPLFNBQUMsU0FBRDs7UUFBQyxZQUFZLENBQUEsQ0FBRSxNQUFGOzthQUNsQixDQUFBLENBQUUsU0FBRixFQUFhLFNBQWIsQ0FBdUIsQ0FBQyxJQUF4QixDQUE2QixTQUFDLEtBQUQsRUFBUSxPQUFSO1FBQzNCLElBQVUsQ0FBQSxDQUFFLE9BQUYsQ0FBVSxDQUFDLElBQVgsQ0FBZ0IsV0FBaEIsQ0FBVjtBQUFBLGlCQUFBOztlQUNBLElBQUksUUFBSixDQUFhLE9BQWI7TUFGMkIsQ0FBN0I7SUFESzs7SUFLTSxrQkFBQyxPQUFEO01BQ1gsSUFBQyxDQUFBLFFBQUQsR0FBWSxDQUFBLENBQUUsT0FBRjtNQUNaLElBQUMsQ0FBQSxRQUFRLENBQUMsSUFBVixDQUFlLFdBQWYsRUFBNEIsSUFBNUI7TUFDQSxJQUFDLENBQUEsSUFBRCxHQUFRLElBQUMsQ0FBQSxRQUFRLENBQUMsSUFBVixDQUFlLEtBQWYsQ0FBQSxJQUF5QjtNQUVqQyxJQUFDLENBQUEsUUFBUSxDQUFDLEVBQVYsQ0FBYSxPQUFiLEVBQXNCLENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLElBQVIsRUFBYyxJQUFkLENBQXRCO0lBTFc7O3VCQU9iLE9BQUEsR0FBUyxTQUFBO0FBQ1AsVUFBQTtNQUFBLFFBQUEsR0FBVyxDQUFDLENBQUMsUUFBRixDQUFXLHNaQUFYO01BbUJYLElBQUMsQ0FBQSxRQUFRLENBQUMsTUFBVixDQUFpQixRQUFBLENBQVM7UUFDeEIsRUFBQSxFQUFJLENBQUMsQ0FBQyxRQUFGLENBQVcsTUFBWCxDQURvQjtRQUV4QixHQUFBLEVBQUssSUFBQyxDQUFBLElBRmtCO09BQVQsQ0FBakI7TUFLQSxJQUFDLENBQUEsVUFBRCxHQUFjLENBQUEsQ0FBRSxlQUFGLEVBQW1CLElBQUMsQ0FBQSxRQUFwQjtNQUVkLElBQUksU0FBSixDQUFjLENBQUEsQ0FBRSxVQUFGLEVBQWMsSUFBQyxDQUFBLFVBQWYsQ0FBMkIsQ0FBQSxDQUFBLENBQXpDO01BQ0EsQ0FBQSxDQUFFLFVBQUYsRUFBYyxJQUFDLENBQUEsVUFBZixDQUEwQixDQUFDLEVBQTNCLENBQThCLE9BQTlCLEVBQXVDLENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLElBQVIsRUFBYyxJQUFkLENBQXZDO2FBRUEsQ0FBQSxDQUFFLFFBQUYsQ0FBVyxDQUFDLEVBQVosQ0FBZSxTQUFmLEVBQTBCLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFEO1VBQ3hCLElBQUcsQ0FBQyxLQUFDLENBQUEsUUFBUSxDQUFDLEVBQVYsQ0FBYSxLQUFLLENBQUMsTUFBbkIsQ0FBRCxJQUFnQyxLQUFDLENBQUEsUUFBUSxDQUFDLEdBQVYsQ0FBYyxLQUFLLENBQUMsTUFBcEIsQ0FBMkIsQ0FBQyxNQUE1QixLQUFzQyxDQUF6RTttQkFDRSxLQUFDLENBQUEsSUFBRCxDQUFBLEVBREY7O1FBRHdCO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUExQjtJQTlCTzs7dUJBa0NULElBQUEsR0FBTSxTQUFBO01BQ0osSUFBQSxDQUFrQixJQUFDLENBQUEsVUFBbkI7UUFBQSxJQUFDLENBQUEsT0FBRCxDQUFBLEVBQUE7O01BQ0EsSUFBQyxDQUFBLFFBQVEsQ0FBQyxRQUFWLENBQW1CLFVBQW5CO2FBQ0EsSUFBQyxDQUFBLFVBQVUsQ0FBQyxJQUFaLENBQUE7SUFISTs7dUJBS04sSUFBQSxHQUFNLFNBQUE7TUFDSixJQUFBLENBQWMsSUFBQyxDQUFBLFVBQWY7QUFBQSxlQUFBOzthQUNBLFVBQUEsQ0FBVyxDQUFBLFNBQUEsS0FBQTtlQUFBLFNBQUE7VUFDVCxLQUFDLENBQUEsUUFBUSxDQUFDLFdBQVYsQ0FBc0IsVUFBdEI7aUJBQ0EsS0FBQyxDQUFBLFVBQVUsQ0FBQyxJQUFaLENBQUE7UUFGUztNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBWCxFQUdFLENBSEY7SUFGSTs7Ozs7QUFwRFIiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuU2t1UG9wdXBcbiAgQGluaXQ6IChjb250YWluZXIgPSAkKCdib2R5JykpLT5cbiAgICAkKCcuanMtc2t1JywgY29udGFpbmVyKS5lYWNoIChpbmRleCwgZWxlbWVudCkgLT5cbiAgICAgIHJldHVybiBpZiAkKGVsZW1lbnQpLmRhdGEgJ3NrdS1wb3B1cCdcbiAgICAgIG5ldyBTa3VQb3B1cCBlbGVtZW50XG5cbiAgY29uc3RydWN0b3I6IChlbGVtZW50KSAtPlxuICAgIEBfZWxlbWVudCA9ICQoZWxlbWVudClcbiAgICBAX2VsZW1lbnQuZGF0YSAnc2t1LXBvcHVwJywgQFxuICAgIEBfc2t1ID0gQF9lbGVtZW50LmRhdGEoJ3NrdScpIHx8IDBcblxuICAgIEBfZWxlbWVudC5vbiAnY2xpY2snLCBfLmJpbmQgQHNob3csIEBcblxuICBfcmVuZGVyOiAtPlxuICAgIHRlbXBsYXRlID0gXy50ZW1wbGF0ZSAnJydcbiAgPGZvcm0gY2xhc3M9XCJza3UtZm9ybSB0b29sdGlwLXBvcHVwXCIgc3R5bGU9XCJkaXNwbGF5Om5vbmVcIj5cbiAgICA8dGFibGU+XG4gICAgICA8dGhlYWQ+XG4gICAgICAgIDx0cj5cbiAgICAgICAgICA8dGggaWQ9XCI8JT0gaWQgJT5cIj48JT0gc2t1ICU+PC90aD5cbiAgICAgICAgPC90cj5cbiAgICAgIDwvdGhlYWQ+XG4gICAgICA8dGZvb3Q+XG4gICAgICAgIDx0cj5cbiAgICAgICAgICA8dGQ+XG4gICAgICAgICAgICA8YSBjbGFzcz1cImJ1dHRvbiBidXR0b24tLXByaW1hcnkgYnV0dG9uLS1ibG9jayBqcy1jb3B5IGpzLWhpZGVcIiBkYXRhLWNsaXBib2FyZC10YXJnZXQ9XCIjPCU9IGlkICU+XCI+0KHQutC+0L/QuNGA0L7QstCw0YLRjDwvYT5cbiAgICAgICAgICA8L3RkPlxuICAgICAgICA8L3RyPlxuICAgICAgPC90cj5cbiAgICAgIDwvdGZvb3Q+XG4gICAgPC90YWJsZT5cbiAgPC9mb3JtPlxuJycnXG4gICAgQF9lbGVtZW50LmFwcGVuZCB0ZW1wbGF0ZSh7XG4gICAgICBpZDogXy51bmlxdWVJZCgnc2t1XycpXG4gICAgICBza3U6IEBfc2t1XG4gICAgfSlcblxuICAgIEBfY29udGFpbmVyID0gJCgnZm9ybS5za3UtZm9ybScsIEBfZWxlbWVudClcblxuICAgIG5ldyBDbGlwYm9hcmQgJCgnLmpzLWNvcHknLCBAX2NvbnRhaW5lcilbMF1cbiAgICAkKCcuanMtaGlkZScsIEBfY29udGFpbmVyKS5vbiAnY2xpY2snLCBfLmJpbmQgQGhpZGUsIEBcblxuICAgICQoZG9jdW1lbnQpLm9uICdtb3VzZXVwJywgKGV2ZW50KSA9PlxuICAgICAgaWYgIUBfZWxlbWVudC5pcyhldmVudC50YXJnZXQpIGFuZCBAX2VsZW1lbnQuaGFzKGV2ZW50LnRhcmdldCkubGVuZ3RoID09IDBcbiAgICAgICAgQGhpZGUoKVxuXG4gIHNob3c6IC0+XG4gICAgQF9yZW5kZXIoKSB1bmxlc3MgQF9jb250YWluZXJcbiAgICBAX2VsZW1lbnQuYWRkQ2xhc3MgJ2V4cGFuZGVkJ1xuICAgIEBfY29udGFpbmVyLnNob3coKVxuXG4gIGhpZGU6IC0+XG4gICAgcmV0dXJuIHVubGVzcyBAX2NvbnRhaW5lclxuICAgIHNldFRpbWVvdXQgPT5cbiAgICAgIEBfZWxlbWVudC5yZW1vdmVDbGFzcyAnZXhwYW5kZWQnXG4gICAgICBAX2NvbnRhaW5lci5oaWRlKClcbiAgICAsIDBcblxuXG5cblxuIl19
