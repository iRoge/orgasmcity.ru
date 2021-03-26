(function() {
  window.Pages = (function() {
    function Pages() {}

    Pages._pages = {};

    Pages.init = function() {
      var alias, matches, options;
      options = window.currentPageOptions || {};
      if (!$('body').attr('class')) {
        window.currentPage = new _Page('default', options);
        return;
      }
      matches = $('body').attr('class').match(/page--([^\s]*)/);
      if (!matches) {
        window.currentPage = new _Page('default', options);
        return;
      }
      alias = matches[1];
      return window.currentPage = Pages._pages[alias] ? new Pages._pages[alias](alias, options) : new _Page(alias, options);
    };

    Pages.register = function(alias, pageClass) {
      return Pages._pages[alias] = pageClass;
    };

    return Pages;

  })();

  window._Page = (function() {
    function _Page(alias, options) {
      if (options == null) {
        options = {};
      }
      this.options = options;
      this._className = "page--" + alias;
      this._defaultEvents();
      this._subscribe();
      this._subscribePopup();
      this._search();
      this._navigation();
      this._gallery();
      this._tooltip();
      this._scroll();
      this._inView();
      this.init();
      if (this.initialize) {
        this.initialize();
      }
    }

    _Page.prototype.init = function(container) {
      this._inputs(container);
      return this._isMobile();
    };

    _Page.prototype._isMobile = function() {
      return bowser.mobile || $(window).width() <= 600;
    };

    _Page.prototype._defaultEvents = function() {
      $('.js-auth-toggle').on('click', function(event) {
        event.preventDefault();
        return $('body').toggleClass('body--auth');
      });
      $(window).on('resize', (function(_this) {
        return function() {
          return _this.resize();
        };
      })(this));
      return $(window).on('load', function() {
        return $(window).trigger('resize');
      });
    };

    _Page.prototype.resize = function() {
      var isMobile;
      isMobile = this._isMobile();
      if (isMobile && !this._isMobileFlag) {
        if (_.isFunction(this._toMobile)) {
          this._toMobile();
        }
      }
      if (!isMobile && this._isMobileFlag) {
        if (_.isFunction(this._fromMobile)) {
          this._fromMobile();
        }
      }
	/* 2018/11/08 by Anatoliy Mitrofanov
      if (!isMobile) {
        this._setSearchPosition();
      } */
      return this._isMobileFlag = isMobile;
    };

    _Page.prototype._inputs = function(container) {
      if (container == null) {
        container = $('body');
      }
      $('select.selectize', container).each((function(_this) {
        return function(index, select) {
          var selectType;
          selectType = $(select).data('selectize');
          if (selectType && _this["_select_" + selectType]) {
            return _this["_select_" + selectType](select);
          } else {
            return _this["_select_default"](select);
          }
        };
      })(this));
      CountInput.init(container);
      ClearableInput.init(container);
      PhoneInput.init(container);
      DatetimeInput.init(container);
      return SkuPopup.init();
    };

    _Page.prototype._setSearchPosition = function() {
      var logotype, logotypeRight, searchBlock, searchLeft;
      searchBlock = $('.header__search');
      logotype = $('.header__logotype');
      logotypeRight = logotype.position().left + logotype.width();
      searchLeft = searchBlock.parent().position().left;
      return searchBlock.css('left', logotypeRight - searchLeft + 10);
    };

    _Page.prototype._select_default = function(select) {
      return $(select).selectize();
    };

    _Page.prototype._select_color = function(select) {
      return $(select).selectize({
        onInitialize: function() {
          return this.$wrapper.addClass('selectize--color');
        },
        render: {
          item: function(data, escape) {
            var className;
            if (!data.color) {
              data.color = data.value;
            }
            className = tinycolor("#" + data.color).isDark() ? 'item--dark' : '';
            return _.template('<div class="item <%= className %>" style="background-color:#<%= data.color %>"></div>')({
              data: data,
              className: className
            });
          },
          option: function(data, escape) {
            var className;
            if (!data.color) {
              data.color = data.value;
            }
            className = tinycolor("#" + data.color).isDark() ? 'item--dark' : '';
            return _.template('<div class="item <%= className %>">\n  <div class="item__color" style="background-color:#<%= data.color %>"></div><%= data.text %>\n</div>')({
              data: data,
              className: className
            });
          }
        }
      });
    };

    _Page.prototype._subscribe = function() {
      return this._subscribe = new window.Subscribe('#subscribe');
    };

    _Page.prototype._subscribePopup = function() {
      return $('.js-subscribe').on('click', function(event) {
        var form;
        event.preventDefault();
        form = $('#subscribe-popup');
        return Popup.show(form.clone().removeClass('hidden'), {
          className: 'popup--subscribe'
        });
      });
    };

    _Page.prototype._search = function() {
      var searchBlock;
      searchBlock = $('.header__search');
      $('.js-search-toggle').on('click', (function(_this) {
        return function() {
          return searchBlock.toggleClass('visible');
        };
      })(this));
      return $(document).on('mouseup', (function(_this) {
        return function(event) {
          if (!searchBlock.is(event.target) && searchBlock.has(event.target).length === 0) {
            return searchBlock.removeClass('visible');
          }
        };
      })(this));
    };

    _Page.prototype._navigation = function() {
      return $('.navigation-toggler').on('click', function(event) {
        var nav;
        nav = $('header .navigation');
        return nav.toggleClass('navigation--expanded');
      });
    };

    _Page.prototype._gallery = function() {
      return $('a.in-popup').on('click', (function(_this) {
        return function(event) {
          var targetImage;
          event.preventDefault();
          targetImage = $('img', event.currentTarget);
          return Popup.show(targetImage.clone(), {
            className: 'popup--gallery'
          });
        };
      })(this));
    };

    _Page.prototype._tooltip = function() {
      $('.js-tooltip').each(function(index, link) {
        var $link;
        $link = $(link);
        return $link.tooltipster({
          theme: 'tooltipster-shadow',
          side: 'bottom',
          debug: true,
          arrow: false,
          content: $($link.data('target')),
          minWidth: $(window).width() <= 414 ? $(window).width() : 335,
          trigger: 'click',
          triggerOpen: {
            mouseenter: true,
            touchstart: true
          },
          triggerClose: {
            mouseleave: true,
            originClick: true,
            touchleave: true
          }
        });
      });
      return $('.tooltipster').each(function(index, link) {
        var $link;
        $link = $(link);
        return $link.tooltipster({
          theme: 'tooltipster-shadow',
          arrow: false
        });
      });
    };

    _Page.prototype._scroll = function() {
      return $('body').on('click', 'a.js-scroll', function(event) {
        var offset, target;
        event.preventDefault();
        target = $(event.currentTarget).attr('href');
        offset = target.length > 1 ? $(target).offset().top : 0;
        return $('html, body').animate({
          scrollTop: offset
        }, 500);
      });
    };

    _Page.prototype._inView = function() {
      return $('.in-view').each(function(index, object) {
        return $(object).on('inview', function() {
          return $(this).addClass('in-view--visible');
        });
      });
    };

    return _Page;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicGFnZXMvX2RlZmF1bHQuanMiLCJzb3VyY2VzIjpbInBhZ2VzL19kZWZhdWx0LmNvZmZlZSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFDQTtFQUFNLE1BQU0sQ0FBQzs7O0lBQ1gsS0FBQyxDQUFBLE1BQUQsR0FBUzs7SUFFVCxLQUFDLENBQUEsSUFBRCxHQUFPLFNBQUE7QUFDTCxVQUFBO01BQUEsT0FBQSxHQUFVLE1BQU0sQ0FBQyxrQkFBUCxJQUE2QjtNQUN2QyxJQUFBLENBQU8sQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLElBQVYsQ0FBZSxPQUFmLENBQVA7UUFDRSxNQUFNLENBQUMsV0FBUCxHQUFxQixJQUFJLEtBQUosQ0FBVSxTQUFWLEVBQXFCLE9BQXJCO0FBQ3JCLGVBRkY7O01BR0EsT0FBQSxHQUFVLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxJQUFWLENBQWUsT0FBZixDQUF1QixDQUFDLEtBQXhCLENBQThCLGdCQUE5QjtNQUNWLElBQUEsQ0FBTyxPQUFQO1FBQ0UsTUFBTSxDQUFDLFdBQVAsR0FBcUIsSUFBSSxLQUFKLENBQVUsU0FBVixFQUFxQixPQUFyQjtBQUNyQixlQUZGOztNQUlBLEtBQUEsR0FBUSxPQUFRLENBQUEsQ0FBQTthQUVoQixNQUFNLENBQUMsV0FBUCxHQUF3QixLQUFLLENBQUMsTUFBTyxDQUFBLEtBQUEsQ0FBaEIsR0FDbkIsSUFBSSxLQUFLLENBQUMsTUFBTyxDQUFBLEtBQUEsQ0FBakIsQ0FBd0IsS0FBeEIsRUFBK0IsT0FBL0IsQ0FEbUIsR0FHbkIsSUFBSSxLQUFKLENBQVUsS0FBVixFQUFpQixPQUFqQjtJQWZHOztJQWlCUCxLQUFDLENBQUEsUUFBRCxHQUFXLFNBQUMsS0FBRCxFQUFRLFNBQVI7YUFDVCxLQUFLLENBQUMsTUFBTyxDQUFBLEtBQUEsQ0FBYixHQUFzQjtJQURiOzs7Ozs7RUFLUCxNQUFNLENBQUM7SUFDRSxlQUFDLEtBQUQsRUFBUSxPQUFSOztRQUFRLFVBQVU7O01BQzdCLElBQUMsQ0FBQSxPQUFELEdBQVc7TUFDWCxJQUFDLENBQUEsVUFBRCxHQUFjLFFBQUEsR0FBUztNQUV2QixJQUFDLENBQUEsY0FBRCxDQUFBO01BQ0EsSUFBQyxDQUFBLFVBQUQsQ0FBQTtNQUNBLElBQUMsQ0FBQSxlQUFELENBQUE7TUFDQSxJQUFDLENBQUEsT0FBRCxDQUFBO01BQ0EsSUFBQyxDQUFBLFdBQUQsQ0FBQTtNQUNBLElBQUMsQ0FBQSxRQUFELENBQUE7TUFDQSxJQUFDLENBQUEsUUFBRCxDQUFBO01BQ0EsSUFBQyxDQUFBLE9BQUQsQ0FBQTtNQUNBLElBQUMsQ0FBQSxPQUFELENBQUE7TUFDQSxJQUFDLENBQUEsSUFBRCxDQUFBO01BRUEsSUFBaUIsSUFBQyxDQUFBLFVBQWxCO1FBQUEsSUFBQyxDQUFBLFVBQUQsQ0FBQSxFQUFBOztJQWZXOztvQkFpQmIsSUFBQSxHQUFNLFNBQUMsU0FBRDtNQUNKLElBQUMsQ0FBQSxPQUFELENBQVMsU0FBVDthQUNBLElBQUMsQ0FBQSxTQUFELENBQUE7SUFGSTs7b0JBSU4sU0FBQSxHQUFXLFNBQUE7YUFDVCxNQUFNLENBQUMsTUFBUCxJQUFpQixDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsS0FBVixDQUFBLENBQUEsSUFBcUI7SUFEN0I7O29CQUdYLGNBQUEsR0FBZ0IsU0FBQTtNQUNkLENBQUEsQ0FBRSxpQkFBRixDQUFvQixDQUFDLEVBQXJCLENBQXdCLE9BQXhCLEVBQWlDLFNBQUMsS0FBRDtRQUMvQixLQUFLLENBQUMsY0FBTixDQUFBO2VBQ0EsQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLFdBQVYsQ0FBc0IsWUFBdEI7TUFGK0IsQ0FBakM7TUFJQSxDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsRUFBVixDQUFhLFFBQWIsRUFBdUIsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFBO2lCQUFHLEtBQUMsQ0FBQSxNQUFELENBQUE7UUFBSDtNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBdkI7YUFDQSxDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsRUFBVixDQUFhLE1BQWIsRUFBcUIsU0FBQTtlQUFHLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxPQUFWLENBQWtCLFFBQWxCO01BQUgsQ0FBckI7SUFOYzs7b0JBUWhCLE1BQUEsR0FBUSxTQUFBO0FBQ04sVUFBQTtNQUFBLFFBQUEsR0FBVyxJQUFDLENBQUEsU0FBRCxDQUFBO01BQ1gsSUFBRyxRQUFBLElBQWEsQ0FBSSxJQUFDLENBQUEsYUFBckI7UUFDRSxJQUFnQixDQUFDLENBQUMsVUFBRixDQUFhLElBQUMsQ0FBQSxTQUFkLENBQWhCO1VBQUEsSUFBQyxDQUFBLFNBQUQsQ0FBQSxFQUFBO1NBREY7O01BR0EsSUFBRyxDQUFJLFFBQUosSUFBaUIsSUFBQyxDQUFBLGFBQXJCO1FBQ0UsSUFBa0IsQ0FBQyxDQUFDLFVBQUYsQ0FBYSxJQUFDLENBQUEsV0FBZCxDQUFsQjtVQUFBLElBQUMsQ0FBQSxXQUFELENBQUEsRUFBQTtTQURGOztNQUdBLElBQUEsQ0FBTyxRQUFQO1FBQ0UsSUFBQyxDQUFBLGtCQUFELENBQUEsRUFERjs7YUFHQSxJQUFDLENBQUEsYUFBRCxHQUFpQjtJQVhYOztvQkFhUixPQUFBLEdBQVMsU0FBQyxTQUFEOztRQUFDLFlBQVksQ0FBQSxDQUFFLE1BQUY7O01BQ3BCLENBQUEsQ0FBRSxrQkFBRixFQUFzQixTQUF0QixDQUFnQyxDQUFDLElBQWpDLENBQXNDLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFELEVBQVEsTUFBUjtBQUNwQyxjQUFBO1VBQUEsVUFBQSxHQUFhLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxJQUFWLENBQWUsV0FBZjtVQUNiLElBQUcsVUFBQSxJQUFlLEtBQUUsQ0FBQSxVQUFBLEdBQVcsVUFBWCxDQUFwQjttQkFDRSxLQUFFLENBQUEsVUFBQSxHQUFXLFVBQVgsQ0FBRixDQUEyQixNQUEzQixFQURGO1dBQUEsTUFBQTttQkFHRSxLQUFFLENBQUEsaUJBQUEsQ0FBRixDQUFxQixNQUFyQixFQUhGOztRQUZvQztNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBdEM7TUFPQSxVQUFVLENBQUMsSUFBWCxDQUFnQixTQUFoQjtNQUNBLGNBQWMsQ0FBQyxJQUFmLENBQW9CLFNBQXBCO01BQ0EsVUFBVSxDQUFDLElBQVgsQ0FBZ0IsU0FBaEI7TUFDQSxhQUFhLENBQUMsSUFBZCxDQUFtQixTQUFuQjthQUNBLFFBQVEsQ0FBQyxJQUFULENBQUE7SUFaTzs7b0JBY1Qsa0JBQUEsR0FBb0IsU0FBQTtBQUNsQixVQUFBO01BQUEsV0FBQSxHQUFjLENBQUEsQ0FBRSxpQkFBRjtNQUNkLFFBQUEsR0FBVyxDQUFBLENBQUUsbUJBQUY7TUFDWCxhQUFBLEdBQWdCLFFBQVEsQ0FBQyxRQUFULENBQUEsQ0FBbUIsQ0FBQyxJQUFwQixHQUEyQixRQUFRLENBQUMsS0FBVCxDQUFBO01BQzNDLFVBQUEsR0FBYSxXQUFXLENBQUMsTUFBWixDQUFBLENBQW9CLENBQUMsUUFBckIsQ0FBQSxDQUErQixDQUFDO2FBQzdDLFdBQVcsQ0FBQyxHQUFaLENBQWdCLE1BQWhCLEVBQXdCLGFBQUEsR0FBZ0IsVUFBaEIsR0FBNkIsRUFBckQ7SUFMa0I7O29CQU9wQixlQUFBLEdBQWlCLFNBQUMsTUFBRDthQUNmLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxTQUFWLENBQUE7SUFEZTs7b0JBR2pCLGFBQUEsR0FBZSxTQUFDLE1BQUQ7YUFDYixDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsU0FBVixDQUNFO1FBQUEsWUFBQSxFQUFjLFNBQUE7aUJBQ1osSUFBQyxDQUFBLFFBQVEsQ0FBQyxRQUFWLENBQW1CLGtCQUFuQjtRQURZLENBQWQ7UUFFQSxNQUFBLEVBQ0U7VUFBQSxJQUFBLEVBQU0sU0FBQyxJQUFELEVBQU8sTUFBUDtBQUNKLGdCQUFBO1lBQUEsSUFBQSxDQUErQixJQUFJLENBQUMsS0FBcEM7Y0FBQSxJQUFJLENBQUMsS0FBTCxHQUFhLElBQUksQ0FBQyxNQUFsQjs7WUFDQSxTQUFBLEdBQWUsU0FBQSxDQUFVLEdBQUEsR0FBSSxJQUFJLENBQUMsS0FBbkIsQ0FBMkIsQ0FBQyxNQUE1QixDQUFBLENBQUgsR0FBNkMsWUFBN0MsR0FBK0Q7bUJBQzNFLENBQUMsQ0FBQyxRQUFGLENBQVcsdUZBQVgsQ0FBQSxDQUVLO2NBQUEsSUFBQSxFQUFNLElBQU47Y0FBWSxTQUFBLEVBQVcsU0FBdkI7YUFGTDtVQUhJLENBQU47VUFNQSxNQUFBLEVBQVEsU0FBQyxJQUFELEVBQU8sTUFBUDtBQUNOLGdCQUFBO1lBQUEsSUFBQSxDQUErQixJQUFJLENBQUMsS0FBcEM7Y0FBQSxJQUFJLENBQUMsS0FBTCxHQUFhLElBQUksQ0FBQyxNQUFsQjs7WUFDQSxTQUFBLEdBQWUsU0FBQSxDQUFVLEdBQUEsR0FBSSxJQUFJLENBQUMsS0FBbkIsQ0FBMkIsQ0FBQyxNQUE1QixDQUFBLENBQUgsR0FBNkMsWUFBN0MsR0FBK0Q7bUJBQzNFLENBQUMsQ0FBQyxRQUFGLENBQVcsNElBQVgsQ0FBQSxDQUlLO2NBQUEsSUFBQSxFQUFNLElBQU47Y0FBWSxTQUFBLEVBQVcsU0FBdkI7YUFKTDtVQUhNLENBTlI7U0FIRjtPQURGO0lBRGE7O29CQW9CZixVQUFBLEdBQVksU0FBQTthQUNWLElBQUMsQ0FBQSxVQUFELEdBQWMsSUFBSSxNQUFNLENBQUMsU0FBWCxDQUFxQixZQUFyQjtJQURKOztvQkFHWixlQUFBLEdBQWlCLFNBQUE7YUFDZixDQUFBLENBQUUsZUFBRixDQUFrQixDQUFDLEVBQW5CLENBQXNCLE9BQXRCLEVBQStCLFNBQUMsS0FBRDtBQUM3QixZQUFBO1FBQUEsS0FBSyxDQUFDLGNBQU4sQ0FBQTtRQUNBLElBQUEsR0FBTyxDQUFBLENBQUUsa0JBQUY7ZUFDUCxLQUFLLENBQUMsSUFBTixDQUFXLElBQUksQ0FBQyxLQUFMLENBQUEsQ0FBWSxDQUFDLFdBQWIsQ0FBeUIsUUFBekIsQ0FBWCxFQUErQztVQUM3QyxTQUFBLEVBQVcsa0JBRGtDO1NBQS9DO01BSDZCLENBQS9CO0lBRGU7O29CQVFqQixPQUFBLEdBQVMsU0FBQTtBQUNQLFVBQUE7TUFBQSxXQUFBLEdBQWMsQ0FBQSxDQUFFLGlCQUFGO01BRWQsQ0FBQSxDQUFFLG1CQUFGLENBQXNCLENBQUMsRUFBdkIsQ0FBMEIsT0FBMUIsRUFBbUMsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFBO2lCQUNqQyxXQUFXLENBQUMsV0FBWixDQUF3QixTQUF4QjtRQURpQztNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBbkM7YUFHQSxDQUFBLENBQUUsUUFBRixDQUFXLENBQUMsRUFBWixDQUFlLFNBQWYsRUFBMEIsQ0FBQSxTQUFBLEtBQUE7ZUFBQSxTQUFDLEtBQUQ7VUFDeEIsSUFBRyxDQUFDLFdBQVcsQ0FBQyxFQUFaLENBQWUsS0FBSyxDQUFDLE1BQXJCLENBQUQsSUFBa0MsV0FBVyxDQUFDLEdBQVosQ0FBZ0IsS0FBSyxDQUFDLE1BQXRCLENBQTZCLENBQUMsTUFBOUIsS0FBd0MsQ0FBN0U7bUJBQ0UsV0FBVyxDQUFDLFdBQVosQ0FBd0IsU0FBeEIsRUFERjs7UUFEd0I7TUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQTFCO0lBTk87O29CQVVULFdBQUEsR0FBYSxTQUFBO2FBQ1gsQ0FBQSxDQUFFLHFCQUFGLENBQXdCLENBQUMsRUFBekIsQ0FBNEIsT0FBNUIsRUFBcUMsU0FBQyxLQUFEO0FBQ25DLFlBQUE7UUFBQSxHQUFBLEdBQU0sQ0FBQSxDQUFFLG9CQUFGO2VBQ04sR0FBRyxDQUFDLFdBQUosQ0FBZ0Isc0JBQWhCO01BRm1DLENBQXJDO0lBRFc7O29CQUtiLFFBQUEsR0FBVSxTQUFBO2FBQ1IsQ0FBQSxDQUFFLFlBQUYsQ0FBZSxDQUFDLEVBQWhCLENBQW1CLE9BQW5CLEVBQTRCLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQyxLQUFEO0FBQzFCLGNBQUE7VUFBQSxLQUFLLENBQUMsY0FBTixDQUFBO1VBQ0EsV0FBQSxHQUFjLENBQUEsQ0FBRSxLQUFGLEVBQVMsS0FBSyxDQUFDLGFBQWY7aUJBQ2QsS0FBSyxDQUFDLElBQU4sQ0FBVyxXQUFXLENBQUMsS0FBWixDQUFBLENBQVgsRUFBZ0M7WUFDOUIsU0FBQSxFQUFXLGdCQURtQjtXQUFoQztRQUgwQjtNQUFBLENBQUEsQ0FBQSxDQUFBLElBQUEsQ0FBNUI7SUFEUTs7b0JBUVYsUUFBQSxHQUFVLFNBQUE7TUFDUixDQUFBLENBQUUsYUFBRixDQUFnQixDQUFDLElBQWpCLENBQXNCLFNBQUMsS0FBRCxFQUFRLElBQVI7QUFDcEIsWUFBQTtRQUFBLEtBQUEsR0FBUSxDQUFBLENBQUUsSUFBRjtlQUNSLEtBQUssQ0FBQyxXQUFOLENBQ0U7VUFBQSxLQUFBLEVBQU8sb0JBQVA7VUFDQSxJQUFBLEVBQU0sUUFETjtVQUVBLEtBQUEsRUFBTyxJQUZQO1VBR0EsS0FBQSxFQUFPLEtBSFA7VUFJQSxPQUFBLEVBQVMsQ0FBQSxDQUFFLEtBQUssQ0FBQyxJQUFOLENBQVcsUUFBWCxDQUFGLENBSlQ7VUFLQSxRQUFBLEVBQWEsQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLEtBQVYsQ0FBQSxDQUFBLElBQXFCLEdBQXhCLEdBQWlDLENBQUEsQ0FBRSxNQUFGLENBQVMsQ0FBQyxLQUFWLENBQUEsQ0FBakMsR0FBd0QsR0FMbEU7VUFNQSxPQUFBLEVBQVMsT0FOVDtVQU9BLFdBQUEsRUFDRTtZQUFBLFVBQUEsRUFBWSxJQUFaO1lBQ0EsVUFBQSxFQUFZLElBRFo7V0FSRjtVQVVBLFlBQUEsRUFDRTtZQUFBLFVBQUEsRUFBWSxJQUFaO1lBQ0EsV0FBQSxFQUFhLElBRGI7WUFFQSxVQUFBLEVBQVksSUFGWjtXQVhGO1NBREY7TUFGb0IsQ0FBdEI7YUFrQkEsQ0FBQSxDQUFFLGNBQUYsQ0FBaUIsQ0FBQyxJQUFsQixDQUF1QixTQUFDLEtBQUQsRUFBUSxJQUFSO0FBQ3JCLFlBQUE7UUFBQSxLQUFBLEdBQVEsQ0FBQSxDQUFFLElBQUY7ZUFDUixLQUFLLENBQUMsV0FBTixDQUNFO1VBQUEsS0FBQSxFQUFPLG9CQUFQO1VBQ0EsS0FBQSxFQUFPLEtBRFA7U0FERjtNQUZxQixDQUF2QjtJQW5CUTs7b0JBeUJWLE9BQUEsR0FBUyxTQUFBO2FBQ1AsQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLEVBQVYsQ0FBYSxPQUFiLEVBQXNCLGFBQXRCLEVBQXFDLFNBQUMsS0FBRDtBQUNuQyxZQUFBO1FBQUEsS0FBSyxDQUFDLGNBQU4sQ0FBQTtRQUNBLE1BQUEsR0FBUyxDQUFBLENBQUUsS0FBSyxDQUFDLGFBQVIsQ0FBc0IsQ0FBQyxJQUF2QixDQUE0QixNQUE1QjtRQUNULE1BQUEsR0FBWSxNQUFNLENBQUMsTUFBUCxHQUFnQixDQUFuQixHQUEwQixDQUFBLENBQUUsTUFBRixDQUFTLENBQUMsTUFBVixDQUFBLENBQWtCLENBQUMsR0FBN0MsR0FBc0Q7ZUFDL0QsQ0FBQSxDQUFFLFlBQUYsQ0FBZSxDQUFDLE9BQWhCLENBQ0U7VUFBQSxTQUFBLEVBQVcsTUFBWDtTQURGLEVBRUUsR0FGRjtNQUptQyxDQUFyQztJQURPOztvQkFTVCxPQUFBLEdBQVMsU0FBQTthQUNQLENBQUEsQ0FBRSxVQUFGLENBQWEsQ0FBQyxJQUFkLENBQW1CLFNBQUMsS0FBRCxFQUFRLE1BQVI7ZUFDakIsQ0FBQSxDQUFFLE1BQUYsQ0FBUyxDQUFDLEVBQVYsQ0FBYSxRQUFiLEVBQXVCLFNBQUE7aUJBQ3JCLENBQUEsQ0FBRSxJQUFGLENBQU8sQ0FBQyxRQUFSLENBQWlCLGtCQUFqQjtRQURxQixDQUF2QjtNQURpQixDQUFuQjtJQURPOzs7OztBQXZMWCIsInNvdXJjZXNDb250ZW50IjpbIiMgR2xvYmFsIHBhZ2UgZmFjdG9yeVxuY2xhc3Mgd2luZG93LlBhZ2VzXG4gIEBfcGFnZXM6IHt9XG5cbiAgQGluaXQ6IC0+XG4gICAgb3B0aW9ucyA9IHdpbmRvdy5jdXJyZW50UGFnZU9wdGlvbnMgb3Ige31cbiAgICB1bmxlc3MgJCgnYm9keScpLmF0dHIoJ2NsYXNzJylcbiAgICAgIHdpbmRvdy5jdXJyZW50UGFnZSA9IG5ldyBfUGFnZSgnZGVmYXVsdCcsIG9wdGlvbnMpXG4gICAgICByZXR1cm5cbiAgICBtYXRjaGVzID0gJCgnYm9keScpLmF0dHIoJ2NsYXNzJykubWF0Y2goL3BhZ2UtLShbXlxcc10qKS8pXG4gICAgdW5sZXNzIG1hdGNoZXNcbiAgICAgIHdpbmRvdy5jdXJyZW50UGFnZSA9IG5ldyBfUGFnZSgnZGVmYXVsdCcsIG9wdGlvbnMpXG4gICAgICByZXR1cm5cblxuICAgIGFsaWFzID0gbWF0Y2hlc1sxXVxuXG4gICAgd2luZG93LmN1cnJlbnRQYWdlID0gaWYgUGFnZXMuX3BhZ2VzW2FsaWFzXVxuICAgICAgbmV3IFBhZ2VzLl9wYWdlc1thbGlhc10oYWxpYXMsIG9wdGlvbnMpICMgVXNlIGN1c3RvbSBwYWdlIGNsYXNzIGJhc2VkIG9uIGRlZmF1bHRcbiAgICBlbHNlXG4gICAgICBuZXcgX1BhZ2UoYWxpYXMsIG9wdGlvbnMpICMgVXNlIGRlZmF1bHQgcGFnZSBjbGFzc1xuXG4gIEByZWdpc3RlcjogKGFsaWFzLCBwYWdlQ2xhc3MpLT5cbiAgICBQYWdlcy5fcGFnZXNbYWxpYXNdID0gcGFnZUNsYXNzXG5cblxuIyBEZWZhdWx0IHBhZ2UgY2xhc3NcbmNsYXNzIHdpbmRvdy5fUGFnZVxuICBjb25zdHJ1Y3RvcjogKGFsaWFzLCBvcHRpb25zID0ge30pIC0+XG4gICAgQG9wdGlvbnMgPSBvcHRpb25zXG4gICAgQF9jbGFzc05hbWUgPSBcInBhZ2UtLSN7YWxpYXN9XCJcblxuICAgIEBfZGVmYXVsdEV2ZW50cygpXG4gICAgQF9zdWJzY3JpYmUoKVxuICAgIEBfc3Vic2NyaWJlUG9wdXAoKVxuICAgIEBfc2VhcmNoKClcbiAgICBAX25hdmlnYXRpb24oKVxuICAgIEBfZ2FsbGVyeSgpXG4gICAgQF90b29sdGlwKClcbiAgICBAX3Njcm9sbCgpXG4gICAgQF9pblZpZXcoKVxuICAgIEBpbml0KClcblxuICAgIEBpbml0aWFsaXplKCkgaWYgQGluaXRpYWxpemVcblxuICBpbml0OiAoY29udGFpbmVyKSAtPlxuICAgIEBfaW5wdXRzKGNvbnRhaW5lcilcbiAgICBAX2lzTW9iaWxlKClcblxuICBfaXNNb2JpbGU6IC0+XG4gICAgYm93c2VyLm1vYmlsZSBvciAkKHdpbmRvdykud2lkdGgoKSA8PSA2MDBcblxuICBfZGVmYXVsdEV2ZW50czogLT5cbiAgICAkKCcuanMtYXV0aC10b2dnbGUnKS5vbiAnY2xpY2snLCAoZXZlbnQpIC0+XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpXG4gICAgICAkKCdib2R5JykudG9nZ2xlQ2xhc3MgJ2JvZHktLWF1dGgnXG5cbiAgICAkKHdpbmRvdykub24gJ3Jlc2l6ZScsID0+IEByZXNpemUoKVxuICAgICQod2luZG93KS5vbiAnbG9hZCcsIC0+ICQod2luZG93KS50cmlnZ2VyICdyZXNpemUnXG5cbiAgcmVzaXplOiAtPlxuICAgIGlzTW9iaWxlID0gQF9pc01vYmlsZSgpXG4gICAgaWYgaXNNb2JpbGUgYW5kIG5vdCBAX2lzTW9iaWxlRmxhZ1xuICAgICAgQF90b01vYmlsZSgpIGlmIF8uaXNGdW5jdGlvbihAX3RvTW9iaWxlKVxuXG4gICAgaWYgbm90IGlzTW9iaWxlIGFuZCBAX2lzTW9iaWxlRmxhZ1xuICAgICAgQF9mcm9tTW9iaWxlKCkgaWYgXy5pc0Z1bmN0aW9uKEBfZnJvbU1vYmlsZSlcblxuICAgIHVubGVzcyBpc01vYmlsZVxuICAgICAgQF9zZXRTZWFyY2hQb3NpdGlvbigpXG5cbiAgICBAX2lzTW9iaWxlRmxhZyA9IGlzTW9iaWxlXG5cbiAgX2lucHV0czogKGNvbnRhaW5lciA9ICQoJ2JvZHknKSktPlxuICAgICQoJ3NlbGVjdC5zZWxlY3RpemUnLCBjb250YWluZXIpLmVhY2ggKGluZGV4LCBzZWxlY3QpID0+XG4gICAgICBzZWxlY3RUeXBlID0gJChzZWxlY3QpLmRhdGEoJ3NlbGVjdGl6ZScpXG4gICAgICBpZiBzZWxlY3RUeXBlIGFuZCBAW1wiX3NlbGVjdF8je3NlbGVjdFR5cGV9XCJdXG4gICAgICAgIEBbXCJfc2VsZWN0XyN7c2VsZWN0VHlwZX1cIl0oc2VsZWN0KVxuICAgICAgZWxzZVxuICAgICAgICBAW1wiX3NlbGVjdF9kZWZhdWx0XCJdKHNlbGVjdClcblxuICAgIENvdW50SW5wdXQuaW5pdChjb250YWluZXIpXG4gICAgQ2xlYXJhYmxlSW5wdXQuaW5pdChjb250YWluZXIpXG4gICAgUGhvbmVJbnB1dC5pbml0KGNvbnRhaW5lcilcbiAgICBEYXRldGltZUlucHV0LmluaXQoY29udGFpbmVyKVxuICAgIFNrdVBvcHVwLmluaXQoKVxuXG4gIF9zZXRTZWFyY2hQb3NpdGlvbjogLT5cbiAgICBzZWFyY2hCbG9jayA9ICQoJy5oZWFkZXJfX3NlYXJjaCcpXG4gICAgbG9nb3R5cGUgPSAkKCcuaGVhZGVyX19sb2dvdHlwZScpXG4gICAgbG9nb3R5cGVSaWdodCA9IGxvZ290eXBlLnBvc2l0aW9uKCkubGVmdCArIGxvZ290eXBlLndpZHRoKClcbiAgICBzZWFyY2hMZWZ0ID0gc2VhcmNoQmxvY2sucGFyZW50KCkucG9zaXRpb24oKS5sZWZ0XG4gICAgc2VhcmNoQmxvY2suY3NzICdsZWZ0JywgbG9nb3R5cGVSaWdodCAtIHNlYXJjaExlZnQgKyAxMFxuXG4gIF9zZWxlY3RfZGVmYXVsdDogKHNlbGVjdCkgLT5cbiAgICAkKHNlbGVjdCkuc2VsZWN0aXplKClcblxuICBfc2VsZWN0X2NvbG9yOiAoc2VsZWN0KSAtPlxuICAgICQoc2VsZWN0KS5zZWxlY3RpemVcbiAgICAgIG9uSW5pdGlhbGl6ZTogLT5cbiAgICAgICAgQCR3cmFwcGVyLmFkZENsYXNzICdzZWxlY3RpemUtLWNvbG9yJ1xuICAgICAgcmVuZGVyOlxuICAgICAgICBpdGVtOiAoZGF0YSwgZXNjYXBlKSAtPlxuICAgICAgICAgIGRhdGEuY29sb3IgPSBkYXRhLnZhbHVlIHVubGVzcyBkYXRhLmNvbG9yXG4gICAgICAgICAgY2xhc3NOYW1lID0gaWYgdGlueWNvbG9yKFwiIyN7ZGF0YS5jb2xvcn1cIikuaXNEYXJrKCkgdGhlbiAnaXRlbS0tZGFyaycgZWxzZSAnJ1xuICAgICAgICAgIF8udGVtcGxhdGUoJycnXG4gICAgICAgICAgICA8ZGl2IGNsYXNzPVwiaXRlbSA8JT0gY2xhc3NOYW1lICU+XCIgc3R5bGU9XCJiYWNrZ3JvdW5kLWNvbG9yOiM8JT0gZGF0YS5jb2xvciAlPlwiPjwvZGl2PlxuICAgICAgICAgICcnJykoZGF0YTogZGF0YSwgY2xhc3NOYW1lOiBjbGFzc05hbWUpXG4gICAgICAgIG9wdGlvbjogKGRhdGEsIGVzY2FwZSkgLT5cbiAgICAgICAgICBkYXRhLmNvbG9yID0gZGF0YS52YWx1ZSB1bmxlc3MgZGF0YS5jb2xvclxuICAgICAgICAgIGNsYXNzTmFtZSA9IGlmIHRpbnljb2xvcihcIiMje2RhdGEuY29sb3J9XCIpLmlzRGFyaygpIHRoZW4gJ2l0ZW0tLWRhcmsnIGVsc2UgJydcbiAgICAgICAgICBfLnRlbXBsYXRlKCcnJ1xuICAgICAgICAgICAgPGRpdiBjbGFzcz1cIml0ZW0gPCU9IGNsYXNzTmFtZSAlPlwiPlxuICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiaXRlbV9fY29sb3JcIiBzdHlsZT1cImJhY2tncm91bmQtY29sb3I6IzwlPSBkYXRhLmNvbG9yICU+XCI+PC9kaXY+PCU9IGRhdGEudGV4dCAlPlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgJycnKShkYXRhOiBkYXRhLCBjbGFzc05hbWU6IGNsYXNzTmFtZSlcblxuICBfc3Vic2NyaWJlOiAtPlxuICAgIEBfc3Vic2NyaWJlID0gbmV3IHdpbmRvdy5TdWJzY3JpYmUoJyNzdWJzY3JpYmUnKVxuXG4gIF9zdWJzY3JpYmVQb3B1cDogLT5cbiAgICAkKCcuanMtc3Vic2NyaWJlJykub24gJ2NsaWNrJywgKGV2ZW50KSAtPlxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgZm9ybSA9ICQoJyNzdWJzY3JpYmUtcG9wdXAnKVxuICAgICAgUG9wdXAuc2hvdyBmb3JtLmNsb25lKCkucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpLCB7XG4gICAgICAgIGNsYXNzTmFtZTogJ3BvcHVwLS1zdWJzY3JpYmUnXG4gICAgICB9XG5cbiAgX3NlYXJjaDogLT5cbiAgICBzZWFyY2hCbG9jayA9ICQoJy5oZWFkZXJfX3NlYXJjaCcpXG5cbiAgICAkKCcuanMtc2VhcmNoLXRvZ2dsZScpLm9uICdjbGljaycsID0+XG4gICAgICBzZWFyY2hCbG9jay50b2dnbGVDbGFzcyAndmlzaWJsZSdcblxuICAgICQoZG9jdW1lbnQpLm9uICdtb3VzZXVwJywgKGV2ZW50KSA9PlxuICAgICAgaWYgIXNlYXJjaEJsb2NrLmlzKGV2ZW50LnRhcmdldCkgYW5kIHNlYXJjaEJsb2NrLmhhcyhldmVudC50YXJnZXQpLmxlbmd0aCA9PSAwXG4gICAgICAgIHNlYXJjaEJsb2NrLnJlbW92ZUNsYXNzICd2aXNpYmxlJ1xuXG4gIF9uYXZpZ2F0aW9uOiAtPlxuICAgICQoJy5uYXZpZ2F0aW9uLXRvZ2dsZXInKS5vbiAnY2xpY2snLCAoZXZlbnQpIC0+XG4gICAgICBuYXYgPSAkKCdoZWFkZXIgLm5hdmlnYXRpb24nKVxuICAgICAgbmF2LnRvZ2dsZUNsYXNzICduYXZpZ2F0aW9uLS1leHBhbmRlZCdcblxuICBfZ2FsbGVyeTogLT5cbiAgICAkKCdhLmluLXBvcHVwJykub24gJ2NsaWNrJywgKGV2ZW50KSA9PlxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgdGFyZ2V0SW1hZ2UgPSAkKCdpbWcnLCBldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgUG9wdXAuc2hvdyB0YXJnZXRJbWFnZS5jbG9uZSgpLCB7XG4gICAgICAgIGNsYXNzTmFtZTogJ3BvcHVwLS1nYWxsZXJ5J1xuICAgICAgfVxuXG4gIF90b29sdGlwOiAtPlxuICAgICQoJy5qcy10b29sdGlwJykuZWFjaCAoaW5kZXgsIGxpbmspIC0+XG4gICAgICAkbGluayA9ICQobGluaylcbiAgICAgICRsaW5rLnRvb2x0aXBzdGVyXG4gICAgICAgIHRoZW1lOiAndG9vbHRpcHN0ZXItc2hhZG93J1xuICAgICAgICBzaWRlOiAnYm90dG9tJ1xuICAgICAgICBkZWJ1ZzogdHJ1ZVxuICAgICAgICBhcnJvdzogZmFsc2VcbiAgICAgICAgY29udGVudDogJCgkbGluay5kYXRhKCd0YXJnZXQnKSlcbiAgICAgICAgbWluV2lkdGg6IGlmICQod2luZG93KS53aWR0aCgpIDw9IDQxNCB0aGVuICQod2luZG93KS53aWR0aCgpIGVsc2UgMzM1XG4gICAgICAgIHRyaWdnZXI6ICdjbGljaydcbiAgICAgICAgdHJpZ2dlck9wZW46XG4gICAgICAgICAgbW91c2VlbnRlcjogdHJ1ZVxuICAgICAgICAgIHRvdWNoc3RhcnQ6IHRydWVcbiAgICAgICAgdHJpZ2dlckNsb3NlOlxuICAgICAgICAgIG1vdXNlbGVhdmU6IHRydWVcbiAgICAgICAgICBvcmlnaW5DbGljazogdHJ1ZVxuICAgICAgICAgIHRvdWNobGVhdmU6IHRydWVcblxuICAgICQoJy50b29sdGlwc3RlcicpLmVhY2ggKGluZGV4LCBsaW5rKSAtPlxuICAgICAgJGxpbmsgPSAkKGxpbmspXG4gICAgICAkbGluay50b29sdGlwc3RlclxuICAgICAgICB0aGVtZTogJ3Rvb2x0aXBzdGVyLXNoYWRvdydcbiAgICAgICAgYXJyb3c6IGZhbHNlXG5cbiAgX3Njcm9sbDogLT5cbiAgICAkKCdib2R5Jykub24gJ2NsaWNrJywgJ2EuanMtc2Nyb2xsJywgKGV2ZW50KSAtPlxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgdGFyZ2V0ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5hdHRyKCdocmVmJylcbiAgICAgIG9mZnNldCA9IGlmIHRhcmdldC5sZW5ndGggPiAxIHRoZW4gJCh0YXJnZXQpLm9mZnNldCgpLnRvcCBlbHNlIDBcbiAgICAgICQoJ2h0bWwsIGJvZHknKS5hbmltYXRlXG4gICAgICAgIHNjcm9sbFRvcDogb2Zmc2V0XG4gICAgICAsIDUwMFxuXG4gIF9pblZpZXc6IC0+XG4gICAgJCgnLmluLXZpZXcnKS5lYWNoIChpbmRleCwgb2JqZWN0KSAtPlxuICAgICAgJChvYmplY3QpLm9uICdpbnZpZXcnLCAtPlxuICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdpbi12aWV3LS12aXNpYmxlJykiXX0=
