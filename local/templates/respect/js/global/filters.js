(function() {
  var isOverlapping;

  _.mixin({
    deepExtend: underscoreDeepExtend(_)
  });

  isOverlapping = function(first, second) {
    var rect1, rect2;
    rect1 = first.getBoundingClientRect();
    rect2 = second.getBoundingClientRect();
    return !(rect1.right < rect2.left || rect1.left > rect2.right);
  };
  window.Filters = (function() {
    var from, max, min, to;

    from = window.filterPriceFrom || 0;

    to = window.filterPriceTo || 15000;

    min = window.filterPriceMin || 0;

    max = window.filterPriceMax || 15000;

    Filters.options = {
      cost: {
        start: [from, to],
        connect: true,
        tooltips: true,
        format: wNumb({
          decimals: 0
        }),
        step: 500,
        range: {
          min: min,
          max: max
        }
      }
    };

    function Filters(el, options) {
      if (options == null) {
        options = {};
      }
      this._el = el;
      this.options = _.deepExtend(Filters.options, options);
      this._events();
      this._form();
      this._linkInputs();
    }

    Filters.prototype._events = function() {
      $('.js-filters-toggle').on('click', (function(_this) {
        return function(event) {
          return _this.toggle();
        };
      })(this));
      $('.js-filters-reset').on('click', (function(_this) {
        return function(event) {
          return _this.reset();
        };
      })(this));
      return $('body').on('click', (function(_this) {
        return function(event) {
          if (!event.target.closest('.filters')) {
            return _this.close();
          }
        };
      })(this));
    };

    Filters.prototype._form = function() {
      var range, slider;
      range = $('.range-selector', this._el)[0];
      slider = noUiSlider.create(range, this.options.cost);
      return slider.on('slide', (function(_this) {
        return function() {
          var tooltips;
          tooltips = $('.noUi-tooltip', range);
          return $(range).toggleClass('overlapping', isOverlapping(tooltips[0], tooltips[1]));
        };
      })(this));
    };

    Filters.prototype._linkInputs = function() {
      $('header select', this._el).on('change', (function(_this) {
        return function(event) {
          var checkboxes, filtered, select, selector, targetSelector, values;
          select = $(event.currentTarget);
          selector = select.attr('id').split('-')[1];
          values = select.val();
          targetSelector = _.map(values, function(value) {
            return "#" + (select.attr('name')) + "_" + value;
          }).join(', ');
          checkboxes = $("#checkbox-" + selector + " input", _this._el);
          filtered = checkboxes.filter(targetSelector);
          checkboxes.prop('checked', false);
          return filtered.prop('checked', true);
        };
      })(this));
      $('footer .filters__linked input', this._el).on('change', (function(_this) {
        return function(event) {
          var checkbox, checkedValues, selector, targetName, targetSelect, wrapper;
          checkbox = $(event.currentTarget);
          wrapper = checkbox.closest('.filters__linked');
          selector = wrapper.attr('id').split('-')[1];
          targetSelect = $("#select-" + selector);
          targetName = targetSelect.attr('name');
          checkedValues = _.map($('input:checked', wrapper), function(input) {
            return $(input).attr('name').replace(targetName + '_', '');
          });
          return targetSelect[0].selectize.setValue(checkedValues, true);
        };
      })(this));
      $('.range-selector', this._el)[0].noUiSlider.on('update', (function(_this) {
        return function(values, handle) {
          $('input#cost_from', _this._el).val(values[0]);
          return $('input#cost_to', _this._el).val(values[1]);
        };
      })(this));
      $('.js-f-stores input', this._el).off('change').on('change', (function(_this) {
        return function(event) {
          var checkbox, checkedValue, targetInput, wrapper;
          checkbox = $(event.currentTarget);
          wrapper = checkbox.parent().parent().parent();

          if (!wrapper.find('input:checked').length) {
            checkbox.prop('checked', true);
          } else {
            $('input[data-value="'+checkbox.attr('data-value')+'"]', this._el).prop('checked', checkbox.is(':checked'));

            currentValue = (2 == wrapper.find('input:checked').length) ? 'A' : wrapper.find('input:checked').attr('data-value');
            $('input#f_stores', _this._el)[0].value = currentValue;
            console.log(currentValue);
          }
        };
      })(this));
      return $('input#cost_from, input#cost_to', this._el).on('change', (function(_this) {
        return function(event) {
          return $('.range-selector', _this._el)[0].noUiSlider.set([$('input#cost_from', _this._el).val(), $('input#cost_to', _this._el).val()]);
        };
      })(this));
    };

    Filters.prototype.toggle = function() {
      return this._el.toggleClass('filters--expanded');
    };

    Filters.prototype.close = function() {
      return this._el.removeClass('filters--expanded');
    };

    Filters.prototype.reset = function() {
      $('form', this._el).each(function(index, form) {
        return form.reset();
      });
      $('select.selectize', this._el).each(function(index, select) {
        return select.selectize.clear();
      });
      return $('.range-selector', this._el)[0].noUiSlider.reset();
    };

    return Filters;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZmlsdGVycy5qcyIsInNvdXJjZXMiOlsiZmlsdGVycy5jb2ZmZWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQSxNQUFBOztFQUFBLENBQUMsQ0FBQyxLQUFGLENBQVE7SUFBQyxVQUFBLEVBQVksb0JBQUEsQ0FBcUIsQ0FBckIsQ0FBYjtHQUFSOztFQUVBLGFBQUEsR0FBZ0IsU0FBQyxLQUFELEVBQVEsTUFBUjtBQUNkLFFBQUE7SUFBQSxLQUFBLEdBQVEsS0FBSyxDQUFDLHFCQUFOLENBQUE7SUFDUixLQUFBLEdBQVEsTUFBTSxDQUFDLHFCQUFQLENBQUE7V0FDUixDQUFJLENBQUMsS0FBSyxDQUFDLEtBQU4sR0FBYyxLQUFLLENBQUMsSUFBcEIsSUFBNEIsS0FBSyxDQUFDLElBQU4sR0FBYSxLQUFLLENBQUMsS0FBaEQ7RUFIVTs7RUFLVixNQUFNLENBQUM7QUFDWCxRQUFBOztJQUFBLElBQUEsR0FBTyxNQUFNLENBQUMsZUFBUCxJQUEwQjs7SUFDakMsRUFBQSxHQUFLLE1BQU0sQ0FBQyxhQUFQLElBQXdCOztJQUU3QixHQUFBLEdBQU0sTUFBTSxDQUFDLGNBQVAsSUFBeUI7O0lBQy9CLEdBQUEsR0FBTSxNQUFNLENBQUMsY0FBUCxJQUF5Qjs7SUFFL0IsT0FBQyxDQUFBLE9BQUQsR0FDRTtNQUFBLElBQUEsRUFDRTtRQUFBLEtBQUEsRUFBTyxDQUFDLElBQUQsRUFBTyxFQUFQLENBQVA7UUFDQSxPQUFBLEVBQVMsSUFEVDtRQUVBLFFBQUEsRUFBVSxJQUZWO1FBR0EsTUFBQSxFQUFRLEtBQUEsQ0FBTTtVQUNaLFFBQUEsRUFBVSxDQURFO1NBQU4sQ0FIUjtRQU1BLElBQUEsRUFBTSxHQU5OO1FBT0EsS0FBQSxFQUNFO1VBQUEsR0FBQSxFQUFLLEdBQUw7VUFDQSxHQUFBLEVBQUssR0FETDtTQVJGO09BREY7OztJQVlXLGlCQUFDLEVBQUQsRUFBSyxPQUFMOztRQUFLLFVBQVU7O01BQzFCLElBQUMsQ0FBQSxHQUFELEdBQU87TUFDUCxJQUFD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NUIsQ0FBaUMsU0FBQyxLQUFELEVBQVEsTUFBUjtlQUMvQixNQUFNLENBQUMsU0FBUyxDQUFDLEtBQWpCLENBQUE7TUFEK0IsQ0FBakM7YUFHQSxDQUFBLENBQUUsaUJBQUYsRUFBcUIsSUFBQyxDQUFBLEdBQXRCLENBQTJCLENBQUEsQ0FBQSxDQUFFLENBQUMsVUFBVSxDQUFDLEtBQXpDLENBQUE7SUFQSzs7Ozs7QUF6RlQiLCJzb3VyY2VzQ29udGVudCI6WyJfLm1peGluKHtkZWVwRXh0ZW5kOiB1bmRlcnNjb3JlRGVlcEV4dGVuZChfKX0pXG5cbmlzT3ZlcmxhcHBpbmcgPSAoZmlyc3QsIHNlY29uZCkgLT5cbiAgcmVjdDEgPSBmaXJzdC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKVxuICByZWN0MiA9IHNlY29uZC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKVxuICBub3QgKHJlY3QxLnJpZ2h0IDwgcmVjdDIubGVmdCBvciByZWN0MS5sZWZ0ID4gcmVjdDIucmlnaHQpXG5cbmNsYXNzIHdpbmRvdy5GaWx0ZXJzXG4gIGZyb20gPSB3aW5kb3cuZmlsdGVyUHJpY2VGcm9tIHx8IDBcbiAgdG8gPSB3aW5kb3cuZmlsdGVyUHJpY2VUbyB8fCAxNTAwMFxuXG4gIG1pbiA9IHdpbmRvdy5maWx0ZXJQcmljZU1pbiB8fCAwXG4gIG1heCA9IHdpbmRvdy5maWx0ZXJQcmljZU1heCB8fCAxNTAwMFxuXG4gIEBvcHRpb25zOlxuICAgIGNvc3Q6ICMgQ29zdCByYW5nZSBvcHRpb25zIG9iamVjdCAobm9VaVNsaWRlcilcbiAgICAgIHN0YXJ0OiBbZnJvbSwgdG9dXG4gICAgICBjb25uZWN0OiB0cnVlXG4gICAgICB0b29sdGlwczogdHJ1ZVxuICAgICAgZm9ybWF0OiB3TnVtYih7XG4gICAgICAgIGRlY2ltYWxzOiAwXG4gICAgICB9KVxuICAgICAgc3RlcDogNTAwXG4gICAgICByYW5nZTpcbiAgICAgICAgbWluOiBtaW5cbiAgICAgICAgbWF4OiBtYXhcblxuICBjb25zdHJ1Y3RvcjogKGVsLCBvcHRpb25zID0ge30pIC0+XG4gICAgQF9lbCA9IGVsXG4gICAgQG9wdGlvbnMgPSBfLmRlZXBFeHRlbmQgRmlsdGVycy5vcHRpb25zLCBvcHRpb25zXG5cbiAgICBAX2V2ZW50cygpXG4gICAgQF9mb3JtKClcbiAgICBAX2xpbmtJbnB1dHMoKVxuXG4gIF9ldmVudHM6IC0+XG4gICAgJCgnLmpzLWZpbHRlcnMtdG9nZ2xlJykub24gJ2NsaWNrJywgKGV2ZW50KSA9PlxuICAgICAgQHRvZ2dsZSgpXG4gICAgJCgnLmpzLWZpbHRlcnMtcmVzZXQnKS5vbiAnY2xpY2snLCAoZXZlbnQpID0+XG4gICAgICBAcmVzZXQoKVxuICAgICQoJ2JvZHknKS5vbiAnY2xpY2snLCAoZXZlbnQpID0+XG4gICAgICBpZiAoIWV2ZW50LnRhcmdldC5jbG9zZXN0KCcuZmlsdGVycycpKVxuICAgICAgICBAY2xvc2UoKVxuXG4gIF9mb3JtOiAtPlxuICAgIHJhbmdlID0gJCgnLnJhbmdlLXNlbGVjdG9yJywgQF9lbClbMF1cbiAgICBzbGlkZXIgPSBub1VpU2xpZGVyLmNyZWF0ZSByYW5nZSwgQG9wdGlvbnMuY29zdFxuICAgIHNsaWRlci5vbiAnc2xpZGUnLCAoKSA9PlxuICAgICAgdG9vbHRpcHMgPSAkKCcubm9VaS10b29sdGlwJywgcmFuZ2UpXG4gICAgICAkKHJhbmdlKS50b2dnbGVDbGFzcyAnb3ZlcmxhcHBpbmcnLCBpc092ZXJsYXBwaW5nKHRvb2x0aXBzWzBdLCB0b29sdGlwc1sxXSlcblxuICBfbGlua0lucHV0czogLT5cbiAgICAkKCdoZWFkZXIgc2VsZWN0JywgQF9lbCkub24gJ2NoYW5nZScsIChldmVudCkgPT5cbiAgICAgIHNlbGVjdCA9ICQoZXZlbnQuY3VycmVudFRhcmdldClcbiAgICAgIHNlbGVjdG9yID0gc2VsZWN0LmF0dHIoJ2lkJykuc3BsaXQoJy0nKVsxXVxuXG4gICAgICB2YWx1ZXMgPSBzZWxlY3QudmFsKClcbiAgICAgIHRhcmdldFNlbGVjdG9yID0gXy5tYXAodmFsdWVzLCAodmFsdWUpIC0+IFwiIyN7c2VsZWN0LmF0dHIoJ25hbWUnKX1fI3t2YWx1ZX1cIikuam9pbignLCAnKVxuICAgICAgY2hlY2tib3hlcyA9ICQoXCIjY2hlY2tib3gtI3tzZWxlY3Rvcn0gaW5wdXRcIiwgQF9lbClcbiAgICAgIGZpbHRlcmVkID0gY2hlY2tib3hlcy5maWx0ZXIodGFyZ2V0U2VsZWN0b3IpXG5cbiAgICAgIGNoZWNrYm94ZXMucHJvcCAnY2hlY2tlZCcsIGZhbHNlXG4gICAgICBmaWx0ZXJlZC5wcm9wICdjaGVja2VkJywgdHJ1ZVxuXG4gICAgJCgnZm9vdGVyIC5maWx0ZXJzX19saW5rZWQgaW5wdXQnLCBAX2VsKS5vbiAnY2hhbmdlJywgKGV2ZW50KSA9PlxuICAgICAgY2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG5cbiAgICAgIHdyYXBwZXIgPSBjaGVja2JveC5jbG9zZXN0KCcuZmlsdGVyc19fbGlua2VkJylcbiAgICAgIHNlbGVjdG9yID0gd3JhcHBlci5hdHRyKCdpZCcpLnNwbGl0KCctJylbMV1cbiAgICAgIHRhcmdldFNlbGVjdCA9ICQoXCIjc2VsZWN0LSN7c2VsZWN0b3J9XCIpXG4gICAgICB0YXJnZXROYW1lID0gdGFyZ2V0U2VsZWN0LmF0dHIoJ25hbWUnKVxuXG4gICAgICBjaGVja2VkVmFsdWVzID0gXy5tYXAgJCgnaW5wdXQ6Y2hlY2tlZCcsIHdyYXBwZXIpLCAoaW5wdXQpIC0+XG4gICAgICAgICQoaW5wdXQpLmF0dHIoJ25hbWUnKS5yZXBsYWNlKHRhcmdldE5hbWUgKyAnXycsICcnKVxuXG4gICAgICB0YXJnZXRTZWxlY3RbMF0uc2VsZWN0aXplLnNldFZhbHVlIGNoZWNrZWRWYWx1ZXMsIHRydWVcblxuICAgICQoJy5yYW5nZS1zZWxlY3RvcicsIEBfZWwpWzBdLm5vVWlTbGlkZXIub24gJ3VwZGF0ZScsICh2YWx1ZXMsIGhhbmRsZSkgPT5cbiAgICAgICQoJ2lucHV0I2Nvc3RfZnJvbScsIEBfZWwpLnZhbCB2YWx1ZXNbMF1cbiAgICAgICQoJ2lucHV0I2Nvc3RfdG8nLCBAX2VsKS52YWwgdmFsdWVzWzFdXG4gICAgJCgnaW5wdXQjY29zdF9mcm9tLCBpbnB1dCNjb3N0X3RvJywgQF9lbCkub24gJ2NoYW5nZScsIChldmVudCkgPT5cbiAgICAgICQoJy5yYW5nZS1zZWxlY3RvcicsIEBfZWwpWzBdLm5vVWlTbGlkZXIuc2V0IFskKCdpbnB1dCNjb3N0X2Zyb20nLCBAX2VsKS52YWwoKSwgJCgnaW5wdXQjY29zdF90bycsIEBfZWwpLnZhbCgpXVxuXG4gIHRvZ2dsZTogLT5cbiAgICBAX2VsLnRvZ2dsZUNsYXNzICdmaWx0ZXJzLS1leHBhbmRlZCdcblxuICBjbG9zZTogLT5cbiAgICBAX2VsLnJlbW92ZUNsYXNzICdmaWx0ZXJzLS1leHBhbmRlZCdcblxuICByZXNldDogLT5cbiAgICAkKCdmb3JtJywgQF9lbCkuZWFjaCAoaW5kZXgsIGZvcm0pIC0+XG4gICAgICBmb3JtLnJlc2V0KClcblxuICAgICQoJ3NlbGVjdC5zZWxlY3RpemUnLCBAX2VsKS5lYWNoIChpbmRleCwgc2VsZWN0KSAtPlxuICAgICAgc2VsZWN0LnNlbGVjdGl6ZS5jbGVhcigpXG5cbiAgICAkKCcucmFuZ2Utc2VsZWN0b3InLCBAX2VsKVswXS5ub1VpU2xpZGVyLnJlc2V0KCkiXX0=
