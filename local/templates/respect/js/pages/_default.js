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
      this._scroll();
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
      return $(window).width() <= 600;
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
      // DatetimeInput.init(container);
      return SkuPopup.init();
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

    return _Page;

  })();

}).call(this);