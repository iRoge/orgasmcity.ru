(function() {
  $(function() {
    return $('[data-toggle]').on('click', function(event) {
      var $target, $toggle, hiddenText, targetId, visibleText;
      $toggle = $(event.currentTarget);
      targetId = $toggle.data('target');
      $target = $(document.getElementById(targetId));
      $target.toggle();
      hiddenText = $toggle.data('hidden-text');
      visibleText = $toggle.data('visible-text');
      if (hiddenText && visibleText) {
        if ($target.is(':visible')) {
          return $toggle.text(visibleText);
        } else {
          return $toggle.text(hiddenText);
        }
      }
    });
  });
}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidG9nZ2xlLmpzIiwic291cmNlcyI6WyJ0b2dnbGUuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQUEsQ0FBQSxDQUFFLFNBQUE7V0FDQSxDQUFBLENBQUUsZUFBRixDQUFrQixDQUFDLEVBQW5CLENBQXNCLE9BQXRCLEVBQStCLFNBQUMsS0FBRDtBQUM3QixVQUFBO01BQUEsT0FBQSxHQUFVLENBQUEsQ0FBRSxLQUFLLENBQUMsYUFBUjtNQUNWLFFBQUEsR0FBVyxPQUFPLENBQUMsSUFBUixDQUFhLFFBQWI7TUFDWCxPQUFBLEdBQVUsQ0FBQSxDQUFFLFFBQVEsQ0FBQyxjQUFULENBQXdCLFFBQXhCLENBQUY7TUFDVixPQUFPLENBQUMsTUFBUixDQUFBO01BRUEsVUFBQSxHQUFhLE9BQU8sQ0FBQyxJQUFSLENBQWEsYUFBYjtNQUNiLFdBQUEsR0FBYyxPQUFPLENBQUMsSUFBUixDQUFhLGNBQWI7TUFDZCxJQUFHLFVBQUEsSUFBZSxXQUFsQjtRQUNFLElBQUcsT0FBTyxDQUFDLEVBQVIsQ0FBVyxVQUFYLENBQUg7aUJBQ0UsT0FBTyxDQUFDLElBQVIsQ0FBYSxXQUFiLEVBREY7U0FBQSxNQUFBO2lCQUdFLE9BQU8sQ0FBQyxJQUFSLENBQWEsVUFBYixFQUhGO1NBREY7O0lBUjZCLENBQS9CO0VBREEsQ0FBRjtBQUFBIiwic291cmNlc0NvbnRlbnQiOlsiJCAtPlxuICAkKCdbZGF0YS10b2dnbGVdJykub24gJ2NsaWNrJywgKGV2ZW50KSAtPlxuICAgICR0b2dnbGUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG4gICAgdGFyZ2V0SWQgPSAkdG9nZ2xlLmRhdGEoJ3RhcmdldCcpXG4gICAgJHRhcmdldCA9ICQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQodGFyZ2V0SWQpKVxuICAgICR0YXJnZXQudG9nZ2xlKClcblxuICAgIGhpZGRlblRleHQgPSAkdG9nZ2xlLmRhdGEoJ2hpZGRlbi10ZXh0JylcbiAgICB2aXNpYmxlVGV4dCA9ICR0b2dnbGUuZGF0YSgndmlzaWJsZS10ZXh0JylcbiAgICBpZiBoaWRkZW5UZXh0IGFuZCB2aXNpYmxlVGV4dFxuICAgICAgaWYgJHRhcmdldC5pcygnOnZpc2libGUnKVxuICAgICAgICAkdG9nZ2xlLnRleHQodmlzaWJsZVRleHQpXG4gICAgICBlbHNlXG4gICAgICAgICR0b2dnbGUudGV4dChoaWRkZW5UZXh0KVxuXG5cbiJdfQ==
