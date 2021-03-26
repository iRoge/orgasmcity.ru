(function () {
	window.CountInput = (function () {
		CountInput.init = function (container) {
			if (container == null) {
				container = $('body');
			}
			return $('input.counter', container).each(function (index, input) {
				if ($(input).data('counter')) {
					return;
				}
				return new CountInput(input);
			});
		};

		function CountInput(input) {
			this._input = $(input);
			this._input.data('counter', this);
			this._render();
		}

		CountInput.prototype._render = function () {
			var decrease, increase;
			_.bindAll(this, 'increase', 'decrease','fixNumber');
			this._wrapper = $('<div class="counter-wrapper">').appendTo(this._input.parent());
			this._wrapper.append(this._input);
			decrease = $('<a class="counter__decrease"><i class="icon icon-arrow-bottom"></i></a>');
			decrease.on('click', (function (_this) {
				return function () {
					_this.decrease();
					return _this.fixNumber();
				};
			})(this));
			this._wrapper.prepend(decrease);
			increase = $('<a class="counter__increase"><i class="icon icon-arrow-up"></i></a>');
			increase.on('click', (function (_this) {
				return function () {
					_this.increase();
					return _this.fixNumber();
				};
			})(this));

			this._input.on('change', (function (_this) {
				return function () {
					return _this.fixNumber();
				};
			})(this));

			return this._wrapper.append(increase);
		};

		CountInput.prototype.increase = function () {
			var value;
			value = parseInt(this._input.val());
			return this._input.val(value + 1);
		};

		CountInput.prototype.decrease = function () {
			var newValue, value;
			value = parseInt(this._input.val());
			newValue = value - 1;
			if (newValue < 0) {
				newValue = 0;
			}
			return this._input.val(newValue);
		};

		CountInput.prototype.fixNumber = function () {

			if (!this._input.data('maximum'))
				return false;

			var maxValue, value;
			value = parseInt(this._input.val());
			maxValue=parseInt(this._input.data('maximum'));

			if (value>maxValue)
				return this._input.val(maxValue);
		};

		return CountInput;

	})();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL2NvdW50ZXIuanMiLCJzb3VyY2VzIjpbImlucHV0cy9jb3VudGVyLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUFNLE1BQU0sQ0FBQztJQUNYLFVBQUMsQ0FBQSxJQUFELEdBQU8sU0FBQyxTQUFEOztRQUFDLFlBQVksQ0FBQSxDQUFFLE1BQUY7O2FBQ2xCLENBQUEsQ0FBRSxlQUFGLEVBQW1CLFNBQW5CLENBQTZCLENBQUMsSUFBOUIsQ0FBbUMsU0FBQyxLQUFELEVBQVEsS0FBUjtRQUNqQyxJQUFVLENBQUEsQ0FBRSxLQUFGLENBQVEsQ0FBQyxJQUFULENBQWMsU0FBZCxDQUFWO0FBQUEsaUJBQUE7O2VBQ0EsSUFBSSxVQUFKLENBQWUsS0FBZjtNQUZpQyxDQUFuQztJQURLOztJQUtNLG9CQUFDLEtBQUQ7TUFDWCxJQUFDLENBQUEsTUFBRCxHQUFVLENBQUEsQ0FBRSxLQUFGO01BQ1YsSUFBQyxDQUFBLE1BQU0sQ0FBQyxJQUFSLENBQWEsU0FBYixFQUF3QixJQUF4QjtNQUVBLElBQUMsQ0FBQSxPQUFELENBQUE7SUFKVzs7eUJBTWIsT0FBQSxHQUFTLFNBQUE7QUFDUCxVQUFBO01BQUEsQ0FBQyxDQUFDLE9BQUYsQ0FBVSxJQUFWLEVBQWEsVUFBYixFQUF5QixVQUF6QjtNQUVBLElBQUMsQ0FBQSxRQUFELEdBQVksQ0FBQSxDQUFFLCtCQUFGLENBQWtDLENBQUMsUUFBbkMsQ0FBNEMsSUFBQyxDQUFBLE1BQU0sQ0FBQyxNQUFSLENBQUEsQ0FBNUM7TUFDWixJQUFDLENBQUEsUUFBUSxDQUFDLE1BQVYsQ0FBaUIsSUFBQyxDQUFBLE1BQWxCO01BRUEsUUFBQSxHQUFXLENBQUEsQ0FBRSx5RUFBRjtNQUNYLFFBQVEsQ0FBQyxFQUFULENBQVksT0FBWixFQUFzQixDQUFBLFNBQUEsS0FBQTtlQUFBLFNBQUE7aUJBQUcsS0FBQyxDQUFBLFFBQUQsQ0FBQTtRQUFIO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUF0QjtNQUNBLElBQUMsQ0FBQSxRQUFRLENBQUMsT0FBVixDQUFrQixRQUFsQjtNQUVBLFFBQUEsR0FBVyxDQUFBLENBQUUscUVBQUY7TUFDWCxRQUFRLENBQUMsRUFBVCxDQUFZLE9BQVosRUFBc0IsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFBO2lCQUFHLEtBQUMsQ0FBQSxRQUFELENBQUE7UUFBSDtNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBdEI7YUFDQSxJQUFDLENBQUEsUUFBUSxDQUFDLE1BQVYsQ0FBaUIsUUFBakI7SUFaTzs7eUJBY1QsUUFBQSxHQUFVLFNBQUE7QUFDUixVQUFBO01BQUEsS0FBQSxHQUFRLFFBQUEsQ0FBUyxJQUFDLENBQUEsTUFBTSxDQUFDLEdBQVIsQ0FBQSxDQUFUO2FBQ1IsSUFBQyxDQUFBLE1BQU0sQ0FBQyxHQUFSLENBQVksS0FBQSxHQUFRLENBQXBCO0lBRlE7O3lCQUlWLFFBQUEsR0FBVSxTQUFBO0FBQ1IsVUFBQTtNQUFBLEtBQUEsR0FBUSxRQUFBLENBQVMsSUFBQyxDQUFBLE1BQU0sQ0FBQyxHQUFSLENBQUEsQ0FBVDtNQUNSLFFBQUEsR0FBVyxLQUFBLEdBQVE7TUFDbkIsSUFBZ0IsUUFBQSxHQUFXLENBQTNCO1FBQUEsUUFBQSxHQUFXLEVBQVg7O2FBQ0EsSUFBQyxDQUFBLE1BQU0sQ0FBQyxHQUFSLENBQVksUUFBWjtJQUpROzs7OztBQTlCWiIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIHdpbmRvdy5Db3VudElucHV0XG4gIEBpbml0OiAoY29udGFpbmVyID0gJCgnYm9keScpKS0+XG4gICAgJCgnaW5wdXQuY291bnRlcicsIGNvbnRhaW5lcikuZWFjaCAoaW5kZXgsIGlucHV0KSAtPlxuICAgICAgcmV0dXJuIGlmICQoaW5wdXQpLmRhdGEgJ2NvdW50ZXInXG4gICAgICBuZXcgQ291bnRJbnB1dCBpbnB1dFxuXG4gIGNvbnN0cnVjdG9yOiAoaW5wdXQpIC0+XG4gICAgQF9pbnB1dCA9ICQoaW5wdXQpXG4gICAgQF9pbnB1dC5kYXRhICdjb3VudGVyJywgQFxuXG4gICAgQF9yZW5kZXIoKVxuXG4gIF9yZW5kZXI6IC0+XG4gICAgXy5iaW5kQWxsIEAsICdpbmNyZWFzZScsICdkZWNyZWFzZSdcblxuICAgIEBfd3JhcHBlciA9ICQoJzxkaXYgY2xhc3M9XCJjb3VudGVyLXdyYXBwZXJcIj4nKS5hcHBlbmRUbyBAX2lucHV0LnBhcmVudCgpXG4gICAgQF93cmFwcGVyLmFwcGVuZCBAX2lucHV0XG5cbiAgICBkZWNyZWFzZSA9ICQoJzxhIGNsYXNzPVwiY291bnRlcl9fZGVjcmVhc2VcIj48aSBjbGFzcz1cImljb24gaWNvbi1hcnJvdy1ib3R0b21cIj48L2k+PC9hPicpXG4gICAgZGVjcmVhc2Uub24gJ2NsaWNrJywgID0+IEBkZWNyZWFzZSgpXG4gICAgQF93cmFwcGVyLnByZXBlbmQgZGVjcmVhc2VcblxuICAgIGluY3JlYXNlID0gJCgnPGEgY2xhc3M9XCJjb3VudGVyX19pbmNyZWFzZVwiPjxpIGNsYXNzPVwiaWNvbiBpY29uLWFycm93LXVwXCI+PC9pPjwvYT4nKVxuICAgIGluY3JlYXNlLm9uICdjbGljaycsICA9PiBAaW5jcmVhc2UoKVxuICAgIEBfd3JhcHBlci5hcHBlbmQgaW5jcmVhc2VcblxuICBpbmNyZWFzZTogLT5cbiAgICB2YWx1ZSA9IHBhcnNlSW50IEBfaW5wdXQudmFsKClcbiAgICBAX2lucHV0LnZhbCB2YWx1ZSArIDFcblxuICBkZWNyZWFzZTogLT5cbiAgICB2YWx1ZSA9IHBhcnNlSW50IEBfaW5wdXQudmFsKClcbiAgICBuZXdWYWx1ZSA9IHZhbHVlIC0gMVxuICAgIG5ld1ZhbHVlID0gMCBpZiBuZXdWYWx1ZSA8IDBcbiAgICBAX2lucHV0LnZhbCBuZXdWYWx1ZVxuXG5cbiJdfQ==
