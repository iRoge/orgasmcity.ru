(function() {
  window.Subscribe = (function() {
    function Subscribe(container) {
      this._container = $(container);
      this._form = $('form', this._container);
      this._successMessage = $('.subscribe-message', this._container);
      this._form.validate({
        submitHandler: _.bind(this._submit, this),
        invalidHandler: _.bind(this._invalid, this),
        errorLabelContainer: $('.subscribe-errors', this._container),
        wrapper: 'li',
        messages: {
          email: 'Поле Email обязательно для заполнения',
          agreement: {
            required: 'Требуется согласиться с политикой конфиденциальности'
          }
        }
      });
    }

    Subscribe.prototype._submit = function(form) {
      this._container.addClass('subscribe--success');
      return true;
    };

    Subscribe.prototype._invalid = function(event, validator) {
      return console.log('error');
    };

    return Subscribe;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoic3Vic2NyaWJlLmpzIiwic291cmNlcyI6WyJzdWJzY3JpYmUuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ0UsbUJBQUMsU0FBRDtNQUNYLElBQUMsQ0FBQSxVQUFELEdBQWMsQ0FBQSxDQUFFLFNBQUY7TUFDZCxJQUFDLENBQUEsS0FBRCxHQUFTLENBQUEsQ0FBRSxNQUFGLEVBQVUsSUFBQyxDQUFBLFVBQVg7TUFDVCxJQUFDLENBQUEsZUFBRCxHQUFtQixDQUFBLENBQUUsb0JBQUYsRUFBd0IsSUFBQyxDQUFBLFVBQXpCO01BRW5CLElBQUMsQ0FBQSxLQUFLLENBQUMsUUFBUCxDQUNFO1FBQUEsYUFBQSxFQUFlLENBQUMsQ0FBQyxJQUFGLENBQU8sSUFBQyxDQUFBLE9BQVIsRUFBaUIsSUFBakIsQ0FBZjtRQUNBLGNBQUEsRUFBZ0IsQ0FBQyxDQUFDLElBQUYsQ0FBTyxJQUFDLENBQUEsUUFBUixFQUFrQixJQUFsQixDQURoQjtRQUVBLG1CQUFBLEVBQXFCLENBQUEsQ0FBRSxtQkFBRixFQUF1QixJQUFDLENBQUEsVUFBeEIsQ0FGckI7UUFHQSxPQUFBLEVBQVMsSUFIVDtRQUlBLFFBQUEsRUFDRTtVQUFBLEtBQUEsRUFBTyx1Q0FBUDtVQUNBLFNBQUEsRUFDRTtZQUFBLFFBQUEsRUFBVSxzREFBVjtXQUZGO1NBTEY7T0FERjtJQUxXOzt3QkFlYixPQUFBLEdBQVMsU0FBQyxJQUFEO01BQ1AsSUFBQyxDQUFBLFVBQVUsQ0FBQyxRQUFaLENBQXFCLG9CQUFyQjtBQUVBLGFBQU87SUFIQTs7d0JBS1QsUUFBQSxHQUFVLFNBQUMsS0FBRCxFQUFRLFNBQVI7YUFDUixPQUFPLENBQUMsR0FBUixDQUFZLE9BQVo7SUFEUTs7Ozs7QUFyQloiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuU3Vic2NyaWJlXG4gIGNvbnN0cnVjdG9yOiAoY29udGFpbmVyKSAtPlxuICAgIEBfY29udGFpbmVyID0gJChjb250YWluZXIpXG4gICAgQF9mb3JtID0gJCgnZm9ybScsIEBfY29udGFpbmVyKVxuICAgIEBfc3VjY2Vzc01lc3NhZ2UgPSAkKCcuc3Vic2NyaWJlLW1lc3NhZ2UnLCBAX2NvbnRhaW5lcilcblxuICAgIEBfZm9ybS52YWxpZGF0ZVxuICAgICAgc3VibWl0SGFuZGxlcjogXy5iaW5kKEBfc3VibWl0LCBAKVxuICAgICAgaW52YWxpZEhhbmRsZXI6IF8uYmluZChAX2ludmFsaWQsIEApXG4gICAgICBlcnJvckxhYmVsQ29udGFpbmVyOiAkKCcuc3Vic2NyaWJlLWVycm9ycycsIEBfY29udGFpbmVyKSxcbiAgICAgIHdyYXBwZXI6ICdsaScsXG4gICAgICBtZXNzYWdlczpcbiAgICAgICAgZW1haWw6ICfQn9C+0LvQtSBFbWFpbCDQvtCx0Y/Qt9Cw0YLQtdC70YzQvdC+INC00LvRjyDQt9Cw0L/QvtC70L3QtdC90LjRjydcbiAgICAgICAgYWdyZWVtZW50OlxuICAgICAgICAgIHJlcXVpcmVkOiAn0KLRgNC10LHRg9C10YLRgdGPINGB0L7Qs9C70LDRgdC40YLRjNGB0Y8g0YEg0L/QvtC70LjRgtC40LrQvtC5INC60L7QvdGE0LjQtNC10L3RhtC40LDQu9GM0L3QvtGB0YLQuCdcblxuICBfc3VibWl0OiAoZm9ybSkgLT5cbiAgICBAX2NvbnRhaW5lci5hZGRDbGFzcyAnc3Vic2NyaWJlLS1zdWNjZXNzJ1xuXG4gICAgcmV0dXJuIHRydWVcblxuICBfaW52YWxpZDogKGV2ZW50LCB2YWxpZGF0b3IpIC0+XG4gICAgY29uc29sZS5sb2cgJ2Vycm9yJ1xuIl19
