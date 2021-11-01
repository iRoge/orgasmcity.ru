(function() {
  window.Vacancy = (function() {
    Vacancy.entities = [];

    Vacancy.init = function() {
      return $('.vacancy--dropdown').each((function(_this) {
        return function(index, element) {
          return _this.entities.push(new Vacancy(element));
        };
      })(this));
    };

    function Vacancy(element) {
      this._element = $(element);
      this._header = $('header', this._element);
      console.log(this._header);
      this._header.on('click', (function(_this) {
        return function() {
          return _this._element.toggleClass('vacancy--expanded');
        };
      })(this));
    }

    return Vacancy;

  })();

  $(function() {
    return Vacancy.init();
  });
}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidmFjYW5jeS5qcyIsInNvdXJjZXMiOlsidmFjYW5jeS5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFBTSxNQUFNLENBQUM7SUFDWCxPQUFDLENBQUEsUUFBRCxHQUFXOztJQUNYLE9BQUMsQ0FBQSxJQUFELEdBQU8sU0FBQTthQUNMLENBQUEsQ0FBRSxvQkFBRixDQUF1QixDQUFDLElBQXhCLENBQTZCLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFELEVBQVEsT0FBUjtpQkFDM0IsS0FBQyxDQUFBLFFBQVEsQ0FBQyxJQUFWLENBQWUsSUFBSSxPQUFKLENBQVksT0FBWixDQUFmO1FBRDJCO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUE3QjtJQURLOztJQUlNLGlCQUFDLE9BQUQ7TUFDWCxJQUFDLENBQUEsUUFBRCxHQUFZLENBQUEsQ0FBRSxPQUFGO01BQ1osSUFBQyxDQUFBLE9BQUQsR0FBVyxDQUFBLENBQUUsUUFBRixFQUFZLElBQUMsQ0FBQSxRQUFiO01BQ1gsT0FBTyxDQUFDLEdBQVIsQ0FBWSxJQUFDLENBQUEsT0FBYjtNQUVBLElBQUMsQ0FBQSxPQUFPLENBQUMsRUFBVCxDQUFZLE9BQVosRUFBcUIsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFBO2lCQUNuQixLQUFDLENBQUEsUUFBUSxDQUFDLFdBQVYsQ0FBc0IsbUJBQXRCO1FBRG1CO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUFyQjtJQUxXOzs7Ozs7RUFRZixDQUFBLENBQUUsU0FBQTtXQUNBLE9BQU8sQ0FBQyxJQUFSLENBQUE7RUFEQSxDQUFGO0FBZEEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuVmFjYW5jeVxuICBAZW50aXRpZXM6IFtdXG4gIEBpbml0OiAtPlxuICAgICQoJy52YWNhbmN5LS1kcm9wZG93bicpLmVhY2ggKGluZGV4LCBlbGVtZW50KSA9PlxuICAgICAgQGVudGl0aWVzLnB1c2ggbmV3IFZhY2FuY3koZWxlbWVudClcblxuICBjb25zdHJ1Y3RvcjogKGVsZW1lbnQpIC0+XG4gICAgQF9lbGVtZW50ID0gJChlbGVtZW50KVxuICAgIEBfaGVhZGVyID0gJCgnaGVhZGVyJywgQF9lbGVtZW50KVxuICAgIGNvbnNvbGUubG9nIEBfaGVhZGVyXG5cbiAgICBAX2hlYWRlci5vbiAnY2xpY2snLCA9PlxuICAgICAgQF9lbGVtZW50LnRvZ2dsZUNsYXNzICd2YWNhbmN5LS1leHBhbmRlZCdcblxuJCAtPlxuICBWYWNhbmN5LmluaXQoKSJdfQ==
