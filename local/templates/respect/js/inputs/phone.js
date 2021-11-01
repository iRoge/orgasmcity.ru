(function() {
  window.PhoneInput = (function() {
    PhoneInput.init = function(container) {
      if (container == null) {
        container = $('body');
      }
      return $('input.phone', container).each(function(index, input) {
        if ($(input).data('phone')) {
          return;
        }
        return new PhoneInput(input);
      });
    };
    function PhoneInput(input) {
      this._input = $(input);
      this._input.data('phone', this);
      this._mask = new Inputmask({
        mask: '+7 (999) 999-99-99',
        onKeyValidation: function (key, result) {
          var char = String.fromCharCode(key)
          if (false === result && char.match(/^[0-9]$/)) {
            $(input).val($(input).val().replace(/\D/g, '').slice(-9)+char);
          }
        },
        onBeforePaste: function (pastedValue, opts) {
          if (10 < pastedValue.length) {
            pastedValue = pastedValue.slice(-10);
          }
          return pastedValue;
        }
      });
      this._mask.mask(this._input);
    }

    return PhoneInput;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5wdXRzL3Bob25lLmpzIiwic291cmNlcyI6WyJpbnB1dHMvcGhvbmUuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ1gsVUFBQyxDQUFBLElBQUQsR0FBTyxTQUFDLFNBQUQ7O1FBQUMsWUFBWSxDQUFBLENBQUUsTUFBRjs7YUFDbEIsQ0FBQSxDQUFFLGFBQUYsRUFBaUIsU0FBakIsQ0FBMkIsQ0FBQyxJQUE1QixDQUFpQyxTQUFDLEtBQUQsRUFBUSxLQUFSO1FBQy9CLElBQVUsQ0FBQSxDQUFFLEtBQUYsQ0FBUSxDQUFDLElBQVQsQ0FBYyxPQUFkLENBQVY7QUFBQSxpQkFBQTs7ZUFDQSxJQUFJLFVBQUosQ0FBZSxLQUFmO01BRitCLENBQWpDO0lBREs7O0lBS00sb0JBQUMsS0FBRDtNQUNYLElBQUMsQ0FBQSxNQUFELEdBQVUsQ0FBQSxDQUFFLEtBQUY7TUFDVixJQUFDLENBQUEsTUFBTSxDQUFDLElBQVIsQ0FBYSxPQUFiLEVBQXNCLElBQXRCO01BRUEsSUFBQyxDQUFBLEtBQUQsR0FBUyxJQUFJLFNBQUosQ0FDUDtRQUFBLElBQUEsRUFBTSxvQkFBTjtPQURPO01BSVQsSUFBQyxDQUFBLEtBQUssQ0FBQyxJQUFQLENBQVksSUFBQyxDQUFBLE1BQWI7SUFSVzs7Ozs7QUFOZiIsInNvdXJjZXNDb250ZW50IjpbImNsYXNzIHdpbmRvdy5QaG9uZUlucHV0XG4gIEBpbml0OiAoY29udGFpbmVyID0gJCgnYm9keScpKS0+XG4gICAgJCgnaW5wdXQucGhvbmUnLCBjb250YWluZXIpLmVhY2ggKGluZGV4LCBpbnB1dCkgLT5cbiAgICAgIHJldHVybiBpZiAkKGlucHV0KS5kYXRhICdwaG9uZSdcbiAgICAgIG5ldyBQaG9uZUlucHV0IGlucHV0XG5cbiAgY29uc3RydWN0b3I6IChpbnB1dCkgLT5cbiAgICBAX2lucHV0ID0gJChpbnB1dClcbiAgICBAX2lucHV0LmRhdGEgJ3Bob25lJywgQFxuXG4gICAgQF9tYXNrID0gbmV3IElucHV0bWFza1xuICAgICAgbWFzazogJys3ICg5OTkpIDk5OS05OS05OSdcbiMgICAgICBjbGVhck1hc2tPbkxvc3RGb2N1czogZmFsc2VcblxuICAgIEBfbWFzay5tYXNrKEBfaW5wdXQpXG5cbiJdfQ==
