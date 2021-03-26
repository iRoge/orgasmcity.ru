(function() {
  window.ToTop = (function() {
    ToTop.threshold = 100;

    function ToTop() {
      this._toggle = $('<a href="#" class="to-top-toggle js-scroll">').appendTo($('body'));
      this._toggle.append($('<i class="icon icon-arrow-up">'));
      $(window).on('scroll', _.bind(this._scrollHandler, this));
      this._scrollHandler();

      this.stickyButton = new StickyButton(this._toggle.get(0), 1, 100, true);
      this.stickyButton.init();
    }

    ToTop.prototype._scrollHandler = function(event) {
      if ($(window).scrollTop() >= ToTop.threshold) {
        return this._toggle.addClass('to-top-toggle--visible');
      } else {
        return this._toggle.removeClass('to-top-toggle--visible');
      }
    };

    return ToTop;

  })();

  $(function() {
    return window.toTop = new ToTop();
  });

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidG8tdG9wLmpzIiwic291cmNlcyI6WyJ0by10b3AuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ1gsS0FBQyxDQUFBLFNBQUQsR0FBWTs7SUFDQyxlQUFBO01BQ1gsSUFBQyxDQUFBLE9BQUQsR0FBVyxDQUFBLENBQUUsOENBQUYsQ0FBaUQsQ0FBQyxRQUFsRCxDQUEyRCxDQUFBLENBQUUsTUFBRixDQUEzRDtNQUNYLElBQUMsQ0FBQSxPQUFPLENBQUMsTUFBVCxDQUFnQixDQUFBLENBQUUsZ0NBQUYsQ0FBaEI7TUFFQSxDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsRUFBVixDQUFhLFFBQWIsRUFBdUIsQ0FBQyxDQUFDLElBQUYsQ0FBTyxJQUFDLENBQUEsY0FBUixFQUF3QixJQUF4QixDQUF2QjtNQUNBLElBQUMsQ0FBQSxjQUFELENBQUE7SUFMVzs7b0JBT2IsY0FBQSxHQUFnQixTQUFDLEtBQUQ7TUFDZCxJQUFHLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxTQUFWLENBQUEsQ0FBQSxJQUF5QixLQUFLLENBQUMsU0FBbEM7ZUFDRSxJQUFDLENBQUEsT0FBTyxDQUFDLFFBQVQsQ0FBa0Isd0JBQWxCLEVBREY7T0FBQSxNQUFBO2VBR0UsSUFBQyxDQUFBLE9BQU8sQ0FBQyxXQUFULENBQXFCLHdCQUFyQixFQUhGOztJQURjOzs7Ozs7RUFPbEIsQ0FBQSxDQUFFLFNBQUE7V0FDQSxNQUFNLENBQUMsS0FBUCxHQUFlLElBQUksS0FBSixDQUFBO0VBRGYsQ0FBRjtBQWhCQSIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIHdpbmRvdy5Ub1RvcFxuICBAdGhyZXNob2xkOiAxMDBcbiAgY29uc3RydWN0b3I6IC0+XG4gICAgQF90b2dnbGUgPSAkKCc8YSBocmVmPVwiI1wiIGNsYXNzPVwidG8tdG9wLXRvZ2dsZSBqcy1zY3JvbGxcIj4nKS5hcHBlbmRUbyAkKCdib2R5JylcbiAgICBAX3RvZ2dsZS5hcHBlbmQgJCgnPGkgY2xhc3M9XCJpY29uIGljb24tYXJyb3ctdXBcIj4nKVxuXG4gICAgJCh3aW5kb3cpLm9uICdzY3JvbGwnLCBfLmJpbmQgQF9zY3JvbGxIYW5kbGVyLCBAXG4gICAgQF9zY3JvbGxIYW5kbGVyKClcblxuICBfc2Nyb2xsSGFuZGxlcjogKGV2ZW50KSAtPlxuICAgIGlmICQod2luZG93KS5zY3JvbGxUb3AoKSA+PSBUb1RvcC50aHJlc2hvbGRcbiAgICAgIEBfdG9nZ2xlLmFkZENsYXNzICd0by10b3AtdG9nZ2xlLS12aXNpYmxlJ1xuICAgIGVsc2VcbiAgICAgIEBfdG9nZ2xlLnJlbW92ZUNsYXNzICd0by10b3AtdG9nZ2xlLS12aXNpYmxlJ1xuXG5cbiQgLT5cbiAgd2luZG93LnRvVG9wID0gbmV3IFRvVG9wKCkiXX0=
