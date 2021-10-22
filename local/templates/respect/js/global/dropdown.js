(function() {
  window.Dropdown = (function() {
    Dropdown.entities = [];

    Dropdown.init = function() {
      return $('.dropdown-toggle').each((function(_this) {
        return function(index, element) {
          return _this.entities.push(new Dropdown(element));
        };
      })(this));
    };
    function Dropdown(element) {
      this._element = $(element);
      this._target = $(this._element.attr('href'));
      this._element.on('click', (function(_this) {
        return function(event) {
          event.preventDefault();
          _this._element.toggleClass('dropdown-toggle--expanded');
          return _this._target.toggle();
        };
      })(this));
    }

    return Dropdown;

  })();

  $(function() {
    return Dropdown.init();
  });

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZHJvcGRvd24uanMiLCJzb3VyY2VzIjpbImRyb3Bkb3duLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUFNLE1BQU0sQ0FBQztJQUNYLFFBQUMsQ0FBQSxRQUFELEdBQVc7O0lBQ1gsUUFBQyxDQUFBLElBQUQsR0FBTyxTQUFBO2FBQ0wsQ0FBQSxDQUFFLGtCQUFGLENBQXFCLENBQUMsSUFBdEIsQ0FBMkIsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFDLEtBQUQsRUFBUSxPQUFSO2lCQUN6QixLQUFDLENBQUEsUUFBUSxDQUFDLElBQVYsQ0FBZSxJQUFJLFFBQUosQ0FBYSxPQUFiLENBQWY7UUFEeUI7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQTNCO0lBREs7O0lBSU0sa0JBQUMsT0FBRDtNQUNYLElBQUMsQ0FBQSxRQUFELEdBQVksQ0FBQSxDQUFFLE9BQUY7TUFDWixJQUFDLENBQUEsT0FBRCxHQUFXLENBQUEsQ0FBRSxJQUFDLENBQUEsUUFBUSxDQUFDLElBQVYsQ0FBZSxNQUFmLENBQUY7TUFFWCxJQUFDLENBQUEsUUFBUSxDQUFDLEVBQVYsQ0FBYSxPQUFiLEVBQXNCLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFEO1VBQ3BCLEtBQUssQ0FBQyxjQUFOLENBQUE7VUFDQSxLQUFDLENBQUEsUUFBUSxDQUFDLFdBQVYsQ0FBc0IsMkJBQXRCO2lCQUNBLEtBQUMsQ0FBQSxPQUFPLENBQUMsTUFBVCxDQUFBO1FBSG9CO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUF0QjtJQUpXOzs7Ozs7RUFTZixDQUFBLENBQUUsU0FBQTtXQUNBLFFBQVEsQ0FBQyxJQUFULENBQUE7RUFEQSxDQUFGO0FBZkEiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuRHJvcGRvd25cbiAgQGVudGl0aWVzOiBbXVxuICBAaW5pdDogLT5cbiAgICAkKCcuZHJvcGRvd24tdG9nZ2xlJykuZWFjaCAoaW5kZXgsIGVsZW1lbnQpID0+XG4gICAgICBAZW50aXRpZXMucHVzaCBuZXcgRHJvcGRvd24oZWxlbWVudClcblxuICBjb25zdHJ1Y3RvcjogKGVsZW1lbnQpIC0+XG4gICAgQF9lbGVtZW50ID0gJChlbGVtZW50KVxuICAgIEBfdGFyZ2V0ID0gJChAX2VsZW1lbnQuYXR0cignaHJlZicpKVxuXG4gICAgQF9lbGVtZW50Lm9uICdjbGljaycsIChldmVudCkgPT5cbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KClcbiAgICAgIEBfZWxlbWVudC50b2dnbGVDbGFzcyAnZHJvcGRvd24tdG9nZ2xlLS1leHBhbmRlZCdcbiAgICAgIEBfdGFyZ2V0LnRvZ2dsZSgpXG5cbiQgLT5cbiAgRHJvcGRvd24uaW5pdCgpIl19
