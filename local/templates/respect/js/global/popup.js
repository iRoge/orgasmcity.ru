(function() {
  window.Popup = (function() {
    Popup.current = null;

    Popup.options = {
      container: 'body'
    };

    Popup.timeout = 0;

    Popup.init = function() {
      $(document).on('mouseup', (function(_this) {
        return function(event) {
          var currentWrapper;
          if (!Popup.hasPopup()) {
            return;
          }
          currentWrapper = Popup.current._wrapper;
          if (!$(currentWrapper).is(event.target) && $(currentWrapper).has(event.target).length === 0) {
            return Popup.hide();
          }
        };
      })(this));
      return $(window).on('resize', function(event) {
        if (!Popup.hasPopup()) {
          return;
        }
        clearTimeout(Popup.timeout);
        return Popup.timeout = setTimeout(function() {
          return Popup.current.resize();
        }, 150);
      });
    };

    Popup.show = function(content, options) {
      if (Popup.hasPopup()) {
        Popup.hide(true);
        return setTimeout(function() {
          let popup = Popup["new"](content, options);
          $('.cls-mail-div').off('click');
          $('.cls-mail-div').click(function() {
            $('.podlozhka').hide(0);
            $('.mail-div').hide(0);
            $('.auth-div-full').hide(0);
            $('.popup').hide(0);
            $('body').removeClass('with--popup');
          })
          return popup;
        }, 600);
      } else {
        let popup = Popup["new"](content, options);
        $('.cls-mail-div').off('click');
        $('.cls-mail-div').click(function() {
          $('.podlozhka').hide(0);
          $('.mail-div').hide(0);
          $('.auth-div-full').hide(0);
          $('.popup').hide(0);
          $('body').removeClass('with--popup');
        })
        return popup;
      }
    };

    Popup["new"] = function(content, options) {
      var _options;
      _options = _.extend(_.clone(Popup.options), options);
      Popup.current = new Popup(content, _options);
      return Popup.current.show();
    };

    Popup.hide = function(withNext) {
      if (withNext == null) {
        withNext = false;
      }
      Popup.current.destroy(withNext);
      return Popup.current = null;
    };

    Popup.hasPopup = function() {
      return !_.isNull(Popup.current);
    };

    function Popup(content, options) {
      this._options = options;
      this._wrapper = $(this._template()());
      this._container = $('.popup__container', this._wrapper);
      if (this._options.title) {
        this._title = $('<header class="popup__header">').appendTo(this._container).text(this._options.title);
      }
      if (this._options.className) {
        this._wrapper.addClass(this._options.className);
      }
      this._content = $('<article class="popup__content">').appendTo(this._container).html(content);
      this._events();
    }

    Popup.prototype._events = function() {
      return this._wrapper.on('click', '.js-popup-close', function() {
        return Popup.hide();
      });
    };

    Popup.prototype._template = function() {
      return _.template(`<div class="popup">
        <div class="popup__wrapper !auth-div-full">
          <div class="cls-mail-div"></div>
          <div class="popup__container"></div>
        </div>
      </div>`);
    };

    Popup.prototype.show = function() {
      var container;
      container = $(this._options.container);
      container.addClass('with--popup');
      this._wrapper.css('opacity', 0).prependTo(container);
      this.resize();
      return this._wrapper.css('opacity', 1).hide().fadeIn('slow', (function(_this) {
        return function() {
          _this.resize();
          window.currentPage.init(_this._wrapper);
          if (_.isFunction(_this._options.onShow)) {
            return _this._options.onShow(_this);
          }
        };
      })(this));
    };

    Popup.prototype.destroy = function(withNext) {
      if (withNext == null) {
        withNext = false;
      }
      return this._wrapper.fadeOut('slow', (function(_this) {
        return function() {
          if (!withNext) {
            $(_this._options.container).removeClass('with--popup');
          }
          if (_.isFunction(_this._options.onClose)) {
            _this._options.onClose(_this);
          }
          return _this._wrapper.remove();
        };
      })(this));
    };

    Popup.prototype.resize = function() {
      var ch, wh;
      wh = $(window).height();
      ch = this._content.outerHeight() + 150;
      return this._wrapper.toggleClass('popup--overflow', wh < ch);
    };

    return Popup;

  })();

  Popup.init();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicG9wdXAuanMiLCJzb3VyY2VzIjpbInBvcHVwLmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUFNLE1BQU0sQ0FBQztJQUNYLEtBQUMsQ0FBQSxPQUFELEdBQVU7O0lBQ1YsS0FBQyxDQUFBLE9BQUQsR0FDRTtNQUFBLFNBQUEsRUFBVyxNQUFYOzs7SUFDRixLQUFDLENBQUEsT0FBRCxHQUFVOztJQUNWLEtBQUMsQ0FBQSxJQUFELEdBQU8sU0FBQTtNQUNMLENBQUEsQ0FBRSxRQUFGLENBQVcsQ0FBQyxFQUFaLENBQWUsU0FBZixFQUEwQixDQUFBLFNBQUEsS0FBQTtlQUFBLFNBQUMsS0FBRDtBQUN4QixjQUFBO1VBQUEsSUFBQSxDQUFjLEtBQUssQ0FBQyxRQUFOLENBQUEsQ0FBZDtBQUFBLG1CQUFBOztVQUNBLGNBQUEsR0FBaUIsS0FBSyxDQUFDLE9BQU8sQ0FBQztVQUMvQixJQUFHLENBQUMsQ0FBQSxDQUFFLGNBQUYsQ0FBaUIsQ0FBQyxFQUFsQixDQUFxQixLQUFLLENBQUMsTUFBM0IsQ0FBRCxJQUF3QyxDQUFBLENBQUUsY0FBRixDQUFpQixDQUFDLEdBQWxCLENBQXNCLEtBQUssQ0FBQyxNQUE1QixDQUFtQyxDQUFDLE1BQXBDLEtBQThDLENBQXpGO21CQUNFLEtBQUssQ0FBQyxJQUFOLENBQUEsRUFERjs7UUFId0I7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQTFCO2FBTUEsQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLEVBQVYsQ0FBYSxRQUFiLEVBQXVCLFNBQUMsS0FBRDtRQUNyQixJQUFBLENBQWMsS0FBSyxDQUFDLFFBQU4sQ0FBQSxDQUFkO0FBQUEsaUJBQUE7O1FBQ0EsWUFBQSxDQUFhLEtBQUssQ0FBQyxPQUFuQjtlQUNBLEtBQUssQ0FBQyxPQUFOLEdBQWdCLFVBQUEsQ0FBVyxTQUFBO2lCQUN6QixLQUFLLENBQUMsT0FBTyxDQUFDLE1BQWQsQ0FBQTtRQUR5QixDQUFYLEVBRWQsR0FGYztNQUhLLENBQXZCO0lB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Q0FBQyxJQUFOLENBQUE7QUFqR0EiLCJzb3VyY2VzQ29udGVudCI6WyJjbGFzcyB3aW5kb3cuUG9wdXBcbiAgQGN1cnJlbnQ6IG51bGxcbiAgQG9wdGlvbnM6XG4gICAgY29udGFpbmVyOiAnYm9keSdcbiAgQHRpbWVvdXQ6IDBcbiAgQGluaXQ6IC0+XG4gICAgJChkb2N1bWVudCkub24gJ21vdXNldXAnLCAoZXZlbnQpID0+XG4gICAgICByZXR1cm4gdW5sZXNzIFBvcHVwLmhhc1BvcHVwKClcbiAgICAgIGN1cnJlbnRXcmFwcGVyID0gUG9wdXAuY3VycmVudC5fd3JhcHBlclxuICAgICAgaWYgISQoY3VycmVudFdyYXBwZXIpLmlzKGV2ZW50LnRhcmdldCkgYW5kICQoY3VycmVudFdyYXBwZXIpLmhhcyhldmVudC50YXJnZXQpLmxlbmd0aCA9PSAwXG4gICAgICAgIFBvcHVwLmhpZGUoKVxuXG4gICAgJCh3aW5kb3cpLm9uICdyZXNpemUnLCAoZXZlbnQpIC0+XG4gICAgICByZXR1cm4gdW5sZXNzIFBvcHVwLmhhc1BvcHVwKClcbiAgICAgIGNsZWFyVGltZW91dCBQb3B1cC50aW1lb3V0XG4gICAgICBQb3B1cC50aW1lb3V0ID0gc2V0VGltZW91dCAtPlxuICAgICAgICBQb3B1cC5jdXJyZW50LnJlc2l6ZSgpXG4gICAgICAsIDE1MFxuXG5cbiAgQHNob3c6IChjb250ZW50LCBvcHRpb25zKSAtPlxuICAgIGlmIFBvcHVwLmhhc1BvcHVwKClcbiAgICAgIFBvcHVwLmhpZGUodHJ1ZSlcbiAgICAgIHNldFRpbWVvdXQgLT5cbiAgICAgICAgUG9wdXAubmV3KGNvbnRlbnQsIG9wdGlvbnMpXG4gICAgICAsIDYwMFxuICAgIGVsc2VcbiAgICAgIFBvcHVwLm5ldyhjb250ZW50LCBvcHRpb25zKVxuXG4gIEBuZXc6IChjb250ZW50LCBvcHRpb25zKSAtPlxuICAgIF9vcHRpb25zID0gXy5leHRlbmQgXy5jbG9uZShQb3B1cC5vcHRpb25zKSwgb3B0aW9uc1xuICAgIFBvcHVwLmN1cnJlbnQgPSBuZXcgUG9wdXAoY29udGVudCwgX29wdGlvbnMpXG4gICAgUG9wdXAuY3VycmVudC5zaG93KClcblxuICBAaGlkZTogKHdpdGhOZXh0ID0gZmFsc2UpIC0+XG4gICAgUG9wdXAuY3VycmVudC5kZXN0cm95KHdpdGhOZXh0KVxuICAgIFBvcHVwLmN1cnJlbnQgPSBudWxsXG5cbiAgQGhhc1BvcHVwOiAtPlxuICAgIG5vdCBfLmlzTnVsbCBQb3B1cC5jdXJyZW50XG5cbiAgY29uc3RydWN0b3I6IChjb250ZW50LCBvcHRpb25zKSAtPlxuICAgIEBfb3B0aW9ucyA9IG9wdGlvbnNcbiAgICBAX3dyYXBwZXIgPSAkKEBfdGVtcGxhdGUoKSgpKVxuICAgIEBfY29udGFpbmVyID0gJCgnLnBvcHVwX19jb250YWluZXInLCBAX3dyYXBwZXIpXG4gICAgaWYgQF9vcHRpb25zLnRpdGxlXG4gICAgICBAX3RpdGxlID0gJCgnPGhlYWRlciBjbGFzcz1cInBvcHVwX19oZWFkZXJcIj4nKVxuICAgICAgICAuYXBwZW5kVG8gQF9jb250YWluZXJcbiAgICAgICAgLnRleHQgQF9vcHRpb25zLnRpdGxlXG5cbiAgICBpZiBAX29wdGlvbnMuY2xhc3NOYW1lXG4gICAgICBAX3dyYXBwZXIuYWRkQ2xhc3MgQF9vcHRpb25zLmNsYXNzTmFtZVxuICAgIEBfY29udGVudCA9ICQoJzxhcnRpY2xlIGNsYXNzPVwicG9wdXBfX2NvbnRlbnRcIj4nKVxuICAgICAgLmFwcGVuZFRvIEBfY29udGFpbmVyXG4gICAgICAuaHRtbCBjb250ZW50XG5cbiAgICBAX2V2ZW50cygpXG5cbiAgX2V2ZW50czogLT5cbiAgICBAX3dyYXBwZXIub24gJ2NsaWNrJywgJy5qcy1wb3B1cC1jbG9zZScsICgpIC0+XG4gICAgICBQb3B1cC5oaWRlKClcblxuICBfdGVtcGxhdGU6IC0+XG4gICAgXy50ZW1wbGF0ZSAnJydcbiAgICAgIDxkaXYgY2xhc3M9XCJwb3B1cFwiPlxuICAgICAgICA8ZGl2IGNsYXNzPVwicG9wdXBfX3dyYXBwZXJcIj5cbiAgICAgICAgICA8YSBjbGFzcz1cInBvcHVwX190b2dnbGUganMtcG9wdXAtY2xvc2VcIj48aSBjbGFzcz1cImljb24gaWNvbi10aW1lcy10aGluXCI+PC9pPjwvYT5cbiAgICAgICAgICA8ZGl2IGNsYXNzPVwicG9wdXBfX2NvbnRhaW5lclwiPlxuICAgICAgICAgIDwvZGl2PlxuICAgICAgICA8L2Rpdj5cbiAgICAgIDwvZGl2PlxuJycnXG5cbiAgc2hvdzogLT5cbiAgICBjb250YWluZXIgPSAkKEBfb3B0aW9ucy5jb250YWluZXIpXG4gICAgY29udGFpbmVyLmFkZENsYXNzICd3aXRoLS1wb3B1cCdcbiAgICBAX3dyYXBwZXIuY3NzKCdvcGFjaXR5JywgMCkucHJlcGVuZFRvIGNvbnRhaW5lclxuICAgIEByZXNpemUoKVxuICAgIEBfd3JhcHBlci5jc3MoJ29wYWNpdHknLCAxKS5oaWRlKCkuZmFkZUluICdzbG93JywgKCkgPT5cbiAgICAgIEByZXNpemUoKVxuICAgICAgd2luZG93LmN1cnJlbnRQYWdlLmluaXQoQF93cmFwcGVyKVxuICAgICAgQF9vcHRpb25zLm9uU2hvdyhAKSBpZiBfLmlzRnVuY3Rpb24gQF9vcHRpb25zLm9uU2hvd1xuXG4gIGRlc3Ryb3k6ICh3aXRoTmV4dCA9IGZhbHNlKSAtPlxuICAgIEBfd3JhcHBlci5mYWRlT3V0ICdzbG93JywgPT5cbiAgICAgIHVubGVzcyB3aXRoTmV4dFxuICAgICAgICAkKEBfb3B0aW9ucy5jb250YWluZXIpLnJlbW92ZUNsYXNzICd3aXRoLS1wb3B1cCdcbiAgICAgIEBfb3B0aW9ucy5vbkNsb3NlKEApIGlmIF8uaXNGdW5jdGlvbiBAX29wdGlvbnMub25DbG9zZVxuICAgICAgQF93cmFwcGVyLnJlbW92ZSgpXG5cbiAgcmVzaXplOiAtPlxuICAgIHdoID0gJCh3aW5kb3cpLmhlaWdodCgpXG4gICAgY2ggPSBAX2NvbnRlbnQub3V0ZXJIZWlnaHQoKSArIDE1MFxuXG4gICAgQF93cmFwcGVyLnRvZ2dsZUNsYXNzICdwb3B1cC0tb3ZlcmZsb3cnLCB3aCA8IGNoXG5cblxuUG9wdXAuaW5pdCgpIl19
