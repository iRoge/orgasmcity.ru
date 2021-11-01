(function() {
  window.ClearableInput = (function() {
    ClearableInput.init = function(container) {
      if (container == null) {
        container = $('body');
      }
      return $('input.clearable', container).each(function(index, input) {
        if ($(input).data('clearable')) {
          return;
        }
        return new ClearableInput(input);
      });
    };
    function ClearableInput(input) {
      this._input = $(input);
      this._input.data('clearable', this);
      this._render();
    }

    ClearableInput.prototype._render = function() {
      var clear;
      _.bindAll(this, 'clear', 'toggle');
      this._wrapper = $('<div class="clearable-wrapper">');
      this._input.before(this._wrapper);
      this._wrapper.append(this._input);
      clear = $('<a class="clearable__clear"><i class="icon icon-times"></i></a>');
      this._wrapper.append(clear);
      clear.on('click', (function(_this) {
        return function() {
          return _this.clear();
        };
      })(this));
      this._input.on('change keyup', this.toggle);
      return this.toggle();
    };

    ClearableInput.prototype.toggle = function() {
      return this._wrapper.toggleClass('with-times', !_.isEmpty(this._input.val()));
    };

    ClearableInput.prototype.clear = function() {
      this._input.val('');
      return this.toggle();
    };

    return ClearableInput;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL2NsZWFyYWJsZS5qcyIsInNvdXJjZXMiOlsiaW5wdXRzL2NsZWFyYWJsZS5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFBTSxNQUFNLENBQUM7SUFDWCxjQUFDLENBQUEsSUFBRCxHQUFPLFNBQUMsU0FBRDs7UUFBQyxZQUFZLENBQUEsQ0FBRSxNQUFGOzthQUNsQixDQUFBLENBQUUsaUJBQUYsRUFBcUIsU0FBckIsQ0FBK0IsQ0FBQyxJQUFoQyxDQUFxQyxTQUFDLEtBQUQsRUFBUSxLQUFSO1FBQ25DLElBQVUsQ0FBQSxDQUFFLEtBQUYsQ0FBUSxDQUFDLElBQVQsQ0FBYyxXQUFkLENBQVY7QUFBQSxpQkFBQTs7ZUFDQSxJQUFJLGNBQUosQ0FBbUIsS0FBbkI7TUFGbUMsQ0FBckM7SUFESzs7SUFLTSx3QkFBQyxLQUFEO01BQ1gsSUFBQyxDQUFBLE1BQUQsR0FBVSxDQUFBLENBQUUsS0FBRjtNQUNWLElBQUMsQ0FBQSxNQUFNLENBQUMsSUFBUixDQUFhLFdBQWIsRUFBMEIsSUFBMUI7TUFFQSxJQUFDLENBQUEsT0FBRCxDQUFBO0lBSlc7OzZCQU1iLE9BQUEsR0FBUyxTQUFBO0FBQ1AsVUFBQTtNQUFBLENBQUMsQ0FBQyxPQUFGLENBQVUsSUFBVixFQUFhLE9BQWIsRUFBc0IsUUFBdEI7TUFFQSxJQUFDLENBQUEsUUFBRCxHQUFZLENBQUEsQ0FBRSxpQ0FBRjtNQUNaLElBQUMsQ0FBQSxNQUFNLENBQUMsTUFBUixDQUFlLElBQUMsQ0FBQSxRQUFoQjtNQUNBLElBQUMsQ0FBQSxRQUFRLENBQUMsTUFBVixDQUFpQixJQUFDLENBQUEsTUFBbEI7TUFFQSxLQUFBLEdBQVEsQ0FBQSxDQUFFLGlFQUFGO01BQ1IsSUFBQyxDQUFBLFFBQVEsQ0FBQyxNQUFWLENBQWlCLEtBQWpCO01BQ0EsS0FBSyxDQUFDLEVBQU4sQ0FBUyxPQUFULEVBQW1CLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQTtpQkFBRyxLQUFDLENBQUEsS0FBRCxDQUFBO1FBQUg7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQW5CO01BRUEsSUFBQyxDQUFBLE1BQU0sQ0FBQyxFQUFSLENBQVcsY0FBWCxFQUEyQixJQUFDLENBQUEsTUFBNUI7YUFDQSxJQUFDLENBQUEsTUFBRCxDQUFBO0lBWk87OzZCQWNULE1BQUEsR0FBUSxTQUFBO2FBQ04sSUFBQyxDQUFBLFFBQVEsQ0FBQyxXQUFWLENBQXNCLFlBQXRCLEVBQW9DLENBQUksQ0FBQyxDQUFDLE9BQUYsQ0FBVSxJQUFDLENBQUEsTUFBTSxDQUFDLEdBQVIsQ0FBQSxDQUFWLENBQXhDO0lBRE07OzZCQUdSLEtBQUEsR0FBTyxTQUFBO01BQ0wsSUFBQyxDQUFBLE1BQU0sQ0FBQyxHQUFSLENBQVksRUFBWjthQUNBLElBQUMsQ0FBQSxNQUFELENBQUE7SUFGSzs7Ozs7QUE3QlQiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuQ2xlYXJhYmxlSW5wdXRcbiAgQGluaXQ6IChjb250YWluZXIgPSAkKCdib2R5JykpLT5cbiAgICAkKCdpbnB1dC5jbGVhcmFibGUnLCBjb250YWluZXIpLmVhY2ggKGluZGV4LCBpbnB1dCkgLT5cbiAgICAgIHJldHVybiBpZiAkKGlucHV0KS5kYXRhICdjbGVhcmFibGUnXG4gICAgICBuZXcgQ2xlYXJhYmxlSW5wdXQgaW5wdXRcblxuICBjb25zdHJ1Y3RvcjogKGlucHV0KSAtPlxuICAgIEBfaW5wdXQgPSAkKGlucHV0KVxuICAgIEBfaW5wdXQuZGF0YSAnY2xlYXJhYmxlJywgQFxuXG4gICAgQF9yZW5kZXIoKVxuXG4gIF9yZW5kZXI6IC0+XG4gICAgXy5iaW5kQWxsIEAsICdjbGVhcicsICd0b2dnbGUnXG5cbiAgICBAX3dyYXBwZXIgPSAkKCc8ZGl2IGNsYXNzPVwiY2xlYXJhYmxlLXdyYXBwZXJcIj4nKVxuICAgIEBfaW5wdXQuYmVmb3JlIEBfd3JhcHBlclxuICAgIEBfd3JhcHBlci5hcHBlbmQgQF9pbnB1dFxuXG4gICAgY2xlYXIgPSAkKCc8YSBjbGFzcz1cImNsZWFyYWJsZV9fY2xlYXJcIj48aSBjbGFzcz1cImljb24gaWNvbi10aW1lc1wiPjwvaT48L2E+JylcbiAgICBAX3dyYXBwZXIuYXBwZW5kIGNsZWFyXG4gICAgY2xlYXIub24gJ2NsaWNrJywgID0+IEBjbGVhcigpXG5cbiAgICBAX2lucHV0Lm9uICdjaGFuZ2Uga2V5dXAnLCBAdG9nZ2xlXG4gICAgQHRvZ2dsZSgpXG5cbiAgdG9nZ2xlOiAtPlxuICAgIEBfd3JhcHBlci50b2dnbGVDbGFzcyAnd2l0aC10aW1lcycsIG5vdCBfLmlzRW1wdHkoQF9pbnB1dC52YWwoKSlcblxuICBjbGVhcjogLT5cbiAgICBAX2lucHV0LnZhbCAnJ1xuICAgIEB0b2dnbGUoKVxuXG5cbiJdfQ==
