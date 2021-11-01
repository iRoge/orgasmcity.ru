(function() {
  window.Tabs = (function() {
    Tabs.entities = [];

    Tabs.init = function() {
      return $('.js-tabs').each((function(_this) {
        return function(index, element) {
          return _this.entities.push(new Tabs(element));
        };
      })(this));
    };

    function Tabs(element) {
      this._element = $(element);
      this._element.data('tabs', this);
      this._initTabs();
    }
    Tabs.prototype._initTabs = function() {
      this._links = [];
      this._targets = {};
      this._element.children().each((function(_this) {
        return function(index, tab) {
          var targetId;
          _this._links.push($(tab));
          targetId = $(tab).data('target');
          _this._targets[targetId] = $(targetId);
          return $(tab).on('click', function(event) {
            event.preventDefault();
            $(event.currentTarget).trigger('show');
            return _this.show(event.currentTarget);
          });
        };
      })(this));
      return setTimeout((function(_this) {
        return function() {
          return _this._element.children('.active').trigger('show');
        };
      })(this), 500);
    };

    Tabs.prototype.show = function(tab) {
      var id, ref, target, targetId;
      targetId = $(tab).data('target');
      ref = this._targets;
      for (id in ref) {
        target = ref[id];
        target.toggleClass('active', targetId === id);
      }
      $(tab).siblings().removeClass('active');
      return $(tab).addClass('active');
    };

    return Tabs;

  })();

  $(function() {
    return Tabs.init();
  });

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidGFicy5qcyIsInNvdXJjZXMiOlsidGFicy5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFBTSxNQUFNLENBQUM7SUFDWCxJQUFDLENBQUEsUUFBRCxHQUFXOztJQUNYLElBQUMsQ0FBQSxJQUFELEdBQU8sU0FBQTthQUNMLENBQUEsQ0FBRSxVQUFGLENBQWEsQ0FBQyxJQUFkLENBQW1CLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFELEVBQVEsT0FBUjtpQkFDakIsS0FBQyxDQUFBLFFBQVEsQ0FBQyxJQUFWLENBQWUsSUFBSSxJQUFKLENBQVMsT0FBVCxDQUFmO1FBRGlCO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUFuQjtJQURLOztJQUlNLGNBQUMsT0FBRDtNQUNYLElBQUMsQ0FBQSxRQUFELEdBQVksQ0FBQSxDQUFFLE9BQUY7TUFDWixJQUFDLENBQUEsUUFBUSxDQUFDLElBQVYsQ0FBZSxNQUFmLEVBQXVCLElBQXZCO01BQ0EsSUFBQyxDQUFBLFNBQUQsQ0FBQTtJQUhXOzttQkFLYixTQUFBLEdBQVcsU0FBQTtNQUNULElBQUMsQ0FBQSxNQUFELEdBQVU7TUFDVixJQUFDLENBQUEsUUFBRCxHQUFZO01BRVosSUFBQyxDQUFBLFFBQVEsQ0FBQyxRQUFWLENBQUEsQ0FBb0IsQ0FBQyxJQUFyQixDQUEwQixDQUFBLFNBQUEsS0FBQTtlQUFBLFNBQUMsS0FBRCxFQUFRLEdBQVI7QUFDeEIsY0FBQTtVQUFBLEtBQUMsQ0FBQSxNQUFNLENBQUMsSUFBUixDQUFhLENBQUEsQ0FBRSxHQUFGLENBQWI7VUFDQSxRQUFBLEdBQVcsQ0FBQSxDQUFFLEdBQUYsQ0FBTSxDQUFDLElBQVAsQ0FBWSxRQUFaO1VBQ1gsS0FBQyxDQUFBLFFBQVMsQ0FBQSxRQUFBLENBQVYsR0FBc0IsQ0FBQSxDQUFFLFFBQUY7aUJBRXRCLENBQUEsQ0FBRSxHQUFGLENBQU0sQ0FBQyxFQUFQLENBQVUsT0FBVixFQUFtQixTQUFDLEtBQUQ7WUFDakIsS0FBSyxDQUFDLGNBQU4sQ0FBQTtZQUNBLENBQUEsQ0FBRSxLQUFLLENBQUMsYUFBUixDQUFzQixDQUFDLE9BQXZCLENBQStCLE1BQS9CO21CQUNBLEtBQUMsQ0FBQSxJQUFELENBQU0sS0FBSyxDQUFDLGFBQVo7VUFIaUIsQ0FBbkI7UUFMd0I7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQTFCO2FBVUEsVUFBQSxDQUFXLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQTtpQkFDVCxLQUFDLENBQUEsUUFBUSxDQUFDLFFBQVYsQ0FBbUIsU0FBbkIsQ0FBNkIsQ0FBQyxPQUE5QixDQUFzQyxNQUF0QztRQURTO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUFYLEVBRUUsR0FGRjtJQWRTOzttQkFrQlgsSUFBQSxHQUFNLFNBQUMsR0FBRDtBQUNKLFVBQUE7TUFBQSxRQUFBLEdBQVcsQ0FBQSxDQUFFLEdBQUYsQ0FBTSxDQUFDLElBQVAsQ0FBWSxRQUFaO0FBQ1g7QUFBQSxXQUFBLFNBQUE7O1FBQ0UsTUFBTSxDQUFDLFdBQVAsQ0FBbUIsUUFBbkIsRUFBOEIsUUFBQSxLQUFZLEVBQTFDO0FBREY7TUFHQSxDQUFBLENBQUUsR0FBRixDQUFNLENBQUMsUUFBUCxDQUFBLENBQWlCLENBQUMsV0FBbEIsQ0FBOEIsUUFBOUI7YUFDQSxDQUFBLENBQUUsR0FBRixDQUFNLENBQUMsUUFBUCxDQUFnQixRQUFoQjtJQU5JOzs7Ozs7RUFRUixDQUFBLENBQUUsU0FBQTtXQUNBLElBQUksQ0FBQyxJQUFMLENBQUE7RUFEQSxDQUFGO0FBckNBIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3Mgd2luZG93LlRhYnNcbiAgQGVudGl0aWVzOiBbXVxuICBAaW5pdDogLT5cbiAgICAkKCcuanMtdGFicycpLmVhY2ggKGluZGV4LCBlbGVtZW50KSA9PlxuICAgICAgQGVudGl0aWVzLnB1c2ggbmV3IFRhYnMoZWxlbWVudClcblxuICBjb25zdHJ1Y3RvcjogKGVsZW1lbnQpIC0+XG4gICAgQF9lbGVtZW50ID0gJChlbGVtZW50KVxuICAgIEBfZWxlbWVudC5kYXRhICd0YWJzJywgQFxuICAgIEBfaW5pdFRhYnMoKVxuXG4gIF9pbml0VGFiczogLT5cbiAgICBAX2xpbmtzID0gW11cbiAgICBAX3RhcmdldHMgPSB7fVxuXG4gICAgQF9lbGVtZW50LmNoaWxkcmVuKCkuZWFjaCAoaW5kZXgsIHRhYikgPT5cbiAgICAgIEBfbGlua3MucHVzaCAkKHRhYilcbiAgICAgIHRhcmdldElkID0gJCh0YWIpLmRhdGEoJ3RhcmdldCcpXG4gICAgICBAX3RhcmdldHNbdGFyZ2V0SWRdID0gJCh0YXJnZXRJZClcblxuICAgICAgJCh0YWIpLm9uICdjbGljaycsIChldmVudCkgPT5cbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgICAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnRyaWdnZXIgJ3Nob3cnXG4gICAgICAgIEBzaG93KGV2ZW50LmN1cnJlbnRUYXJnZXQpXG5cbiAgICBzZXRUaW1lb3V0ID0+XG4gICAgICBAX2VsZW1lbnQuY2hpbGRyZW4oJy5hY3RpdmUnKS50cmlnZ2VyICdzaG93J1xuICAgICwgNTAwXG5cbiAgc2hvdzogKHRhYikgLT5cbiAgICB0YXJnZXRJZCA9ICQodGFiKS5kYXRhKCd0YXJnZXQnKVxuICAgIGZvciBpZCwgdGFyZ2V0IG9mIEBfdGFyZ2V0c1xuICAgICAgdGFyZ2V0LnRvZ2dsZUNsYXNzICdhY3RpdmUnLCAodGFyZ2V0SWQgaXMgaWQpXG5cbiAgICAkKHRhYikuc2libGluZ3MoKS5yZW1vdmVDbGFzcyAnYWN0aXZlJ1xuICAgICQodGFiKS5hZGRDbGFzcyAnYWN0aXZlJ1xuXG4kIC0+XG4gIFRhYnMuaW5pdCgpIl19
