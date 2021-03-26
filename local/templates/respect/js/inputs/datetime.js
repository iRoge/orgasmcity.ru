(function() {
  $.datetimepicker.setLocale('ru');

  window.DatetimeInput = (function() {
    DatetimeInput.init = function(container) {
      if (container == null) {
        container = $('body');
      }
      return $('input.datepicker', container).each(function(index, input) {
        return new DatetimeInput(input);
      });
    };

    function DatetimeInput(input) {
      this._input = $(input);
      this._input.datetimepicker({
        timepicker: false,
        format: 'd.m.Y'
      });
    }

    return DatetimeInput;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL2RhdGV0aW1lLmpzIiwic291cmNlcyI6WyJpbnB1dHMvZGF0ZXRpbWUuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQUEsQ0FBQyxDQUFDLGNBQWMsQ0FBQyxTQUFqQixDQUEyQixJQUEzQjs7RUFFTSxNQUFNLENBQUM7SUFDWCxhQUFDLENBQUEsSUFBRCxHQUFPLFNBQUMsU0FBRDs7UUFBQyxZQUFZLENBQUEsQ0FBRSxNQUFGOzthQUNsQixDQUFBLENBQUUsa0JBQUYsRUFBc0IsU0FBdEIsQ0FBZ0MsQ0FBQyxJQUFqQyxDQUFzQyxTQUFDLEtBQUQsRUFBUSxLQUFSO2VBQ3BDLElBQUksYUFBSixDQUFrQixLQUFsQjtNQURvQyxDQUF0QztJQURLOztJQUlNLHVCQUFDLEtBQUQ7TUFDWCxJQUFDLENBQUEsTUFBRCxHQUFVLENBQUEsQ0FBRSxLQUFGO01BRVYsSUFBQyxDQUFBLE1BQU0sQ0FBQyxjQUFSLENBQ0U7UUFBQSxVQUFBLEVBQVksS0FBWjtRQUNBLE1BQUEsRUFBUSxPQURSO09BREY7SUFIVzs7Ozs7QUFQZiIsInNvdXJjZXNDb250ZW50IjpbIiQuZGF0ZXRpbWVwaWNrZXIuc2V0TG9jYWxlKCdydScpXG5cbmNsYXNzIHdpbmRvdy5EYXRldGltZUlucHV0XG4gIEBpbml0OiAoY29udGFpbmVyID0gJCgnYm9keScpKS0+XG4gICAgJCgnaW5wdXQuZGF0ZXBpY2tlcicsIGNvbnRhaW5lcikuZWFjaCAoaW5kZXgsIGlucHV0KSAtPlxuICAgICAgbmV3IERhdGV0aW1lSW5wdXQgaW5wdXRcblxuICBjb25zdHJ1Y3RvcjogKGlucHV0KSAtPlxuICAgIEBfaW5wdXQgPSAkKGlucHV0KVxuXG4gICAgQF9pbnB1dC5kYXRldGltZXBpY2tlclxuICAgICAgdGltZXBpY2tlcjogZmFsc2VcbiAgICAgIGZvcm1hdDogJ2QubS5ZJ1xuXG5cbiJdfQ==
