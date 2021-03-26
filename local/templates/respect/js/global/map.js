(function() {
  window.GoogleMapView = (function() {
    GoogleMapView.options = {
      markersImage: '/local/templates/respect/images/map-marker.png',
      "default": {
        center: {
          lat: 55.7494733,
          lng: 37.35232
        },
        scrollwheel: false,
        zoom: 8
      }
    };

    function GoogleMapView(wrapper, options) {
      var bounds, i, item, len, marker, page, ref;
      if (options == null) {
        options = {};
      }
      this.options = _.extend(GoogleMapView.options, options);
      this._markers = {};
      this._mapOptions = this.options.google ? _.extend(this.options["default"], this.options.google) : this.options["default"];
      this._map = new google.maps.Map(wrapper[0], this._mapOptions);
      bounds = new google.maps.LatLngBounds();
      ref = this.options.items;
      for (i = 0, len = ref.length; i < len; i++) {
        item = ref[i];
        marker = new google.maps.Marker({
          map: this._map,
          position: item.coordinates,
          title: item.title,
          icon: this._markerImage(),
          zIndex: item.index
        });
        bounds.extend(item.coordinates);
        marker.data = item;
        page = this;
        marker.addListener('click', function(a, b, c) {
          return page._showInfoWindow(this);
        });
        this._markers[item.index] = marker;
      }
      if (!$.isEmptyObject(this._markers)) {
        this._map.fitBounds(bounds);
      } else {
        this._map.setCenter(this._mapOptions.center);
      }
      if (this.options.items.length === 1) {
        setTimeout((function(_this) {
          return function() {
            return _this._map.setZoom(_this._mapOptions.zoom);
          };
        })(this), 500);
      }
      google.maps.event.addListener(this._map, 'click', (function(_this) {
        return function() {
          if (_this._infoWindow) {
            return _this._infoWindow.close();
          }
        };
      })(this));
    }

    GoogleMapView.prototype.select = function(index) {
      if (index) {
        return this._showInfoWindow(this._markers[index]);
      }
    };

    GoogleMapView.prototype._showInfoWindow = function(marker) {
      if (this._infoWindow) {
        this._infoWindow.close();
      }
      this._infoWindow = new google.maps.InfoWindow({
        content: this._infoWindowTemplate(marker.data),
        maxWidth: 350,
        maxHeight: 220
      });
      google.maps.event.addListener(this._infoWindow, 'domready', function() {
        var iwBackground, iwCloseBtn, iwOuter;
        iwOuter = $('.gm-style-iw');
        iwOuter.css('width', 'auto');
        iwBackground = iwOuter.prev();
        $(iwBackground).addClass('bubble-background');
        iwBackground.children(':nth-child(1)').hide();
        iwBackground.children(':nth-child(2)').hide();
        iwBackground.children(':nth-child(3)').hide();
        iwBackground.children(':nth-child(4)').hide();
        iwCloseBtn = iwOuter.next();
        return iwCloseBtn.hide();
      });
      this._infoWindow.open(this._map, marker);
      if (_.isFunction(this.options.onSelect)) {
        return this.options.onSelect(marker.zIndex);
      }
    };

    GoogleMapView.prototype._infoWindowTemplate = function(data) {
      var template;
      template = _.template('<div class="map-bubble">\n  <div class="map-bubble__title"><%=title%></div>\n  <% if (subway) { %>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <% } %>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <% if (worktime) { %>\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <% } %>\n    <% if (phone) { %>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n    <% } %>\n  </ul>\n</div>');
      return template(data);
    };

    GoogleMapView.prototype._markerImage = function() {
      return {
        url: this.options.markersImage,
        size: new google.maps.Size(47, 61),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(23, 61)
      };
    };

    return GoogleMapView;

  })();

}).call(this);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoibWFwLmpzIiwic291cmNlcyI6WyJtYXAuY29mZmVlIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQU0sTUFBTSxDQUFDO0lBQ1gsYUFBQyxDQUFBLE9BQUQsR0FDRTtNQUFBLFlBQUEsRUFBYyxnREFBZDtNQUNBLENBQUEsT0FBQSxDQUFBLEVBQ0U7UUFBQSxNQUFBLEVBQVE7VUFBQyxHQUFBLEVBQUssVUFBTjtVQUFrQixHQUFBLEVBQUssUUFBdkI7U0FBUjtRQUNBLFdBQUEsRUFBYSxLQURiO1FBRUEsSUFBQSxFQUFNLENBRk47T0FGRjs7O0lBS1csdUJBQUMsT0FBRCxFQUFVLE9BQVY7QUFDWCxVQUFBOztRQURxQixVQUFVOztNQUMvQixJQUFDLENBQUEsT0FBRCxHQUFXLENBQUMsQ0FBQyxNQUFGLENBQVMsYUFBYSxDQUFDLE9BQXZCLEVBQWdDLE9BQWhDO01BQ1gsSUFBQyxDQUFBLFFBQUQsR0FBWTtNQUVaLElBQUMsQ0FBQSxXQUFELEdBQWtCLElBQUMsQ0FBQSxPQUFPLENBQUMsTUFBWixHQUNiLENBQUMsQ0FBQyxNQUFGLENBQVMsSUFBQyxDQUFBLE9BQU8sRUFBQyxPQUFELEVBQWpCLEVBQTJCLElBQUMsQ0FBQSxPQUFPLENBQUMsTUFBcEMsQ0FEYSxHQUdiLElBQUMsQ0FBQSxPQUFPLEVBQUMsT0FBRDtNQUVWLElBQUMsQ0FBQSxJQUFELEdBQVEsSUFBSSxNQUFNLENBQUMsSUFBSSxDQUFDLEdBQWhCLENBQW9CLE9BQVEsQ0FBQSxDQUFBLENBQTVCLEVBQWdDLElBQUMsQ0FBQSxXQUFqQztNQUVSLE1BQUEsR0FBUyxJQUFJLE1BQU0sQ0FBQyxJQUFJLENBQUMsWUFBaEIsQ0FBQTtBQUVUO0FBQUEsV0FBQSxxQ0FBQTs7UUFDRSxNQUFBLEdBQVMsSUFBSSxNQUFNLENBQUMsSUFBSSxDQUFDLE1BQWhCLENBQXVCO1VBQzlCLEdBQUEsRUFBSyxJQUFDLENBQUEsSUFEd0I7VUFFOUIsUUFBQSxFQUFVLElBQUksQ0FBQyxXQUZlO1VBRzlCLEtBQUEsRUFBTyxJQUFJLENBQUMsS0FIa0I7VUFJOUIsSUFBQSxFQUFNLElBQUMsQ0FBQSxZQUFELENBQUEsQ0FKd0I7VUFLOUIsTUFBQSxFQUFRLElBQUksQ0FBQyxLQUxpQjtTQUF2QjtRQVFULE1BQU0sQ0FBQyxNQUFQLENBQWMsSUFBSSxDQUFDLFdBQW5CO1FBRUEsTUFBTSxDQUFDLElBQVAsR0FBYztRQUVkLElBQUEsR0FBTztRQUNQLE1BQU0sQ0FBQyxXQUFQLENBQW1CLE9BQW5CLEVBQTRCLFNBQUMsQ0FBRCxFQUFJLENBQUosRUFBTyxDQUFQO2lCQUMxQixJQUFJLENBQUMsZUFBTCxDQUFxQixJQUFyQjtRQUQwQixDQUE1QjtRQUdBLElBQUMsQ0FBQSxRQUFTLENBQUEsSUFBSSxDQUFDLEtBQUwsQ0FBVixHQUF3QjtBQWpCMUI7TUFtQkEsSUFBRyxDQUFDLENBQUMsQ0FBQyxhQUFGLENBQWdCLElBQUMsQ0FBQSxRQUFqQixDQUFKO1FBQ0UsSUFBQyxDQUFBLElBQUksQ0FBQyxTQUFOLENBQWdCLE1BQWhCLEVBREY7T0FBQSxNQUFBO1FBR0UsSUFBQyxDQUFBLElBQUksQ0FBQyxTQUFOLENBQWdCLElBQUMsQ0FBQSxXQUFXLENBQUMsTUFBN0IsRUFIRjs7TUFLQSxJQUFHLElBQUMsQ0FBQSxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQWYsS0FBeUIsQ0FBNUI7UUFDRSxVQUFBLENBQVcsQ0FBQSxTQUFBLEtBQUE7aUJBQUEsU0FBQTttQkFDVCxLQUFDLENBQUEsSUFBSSxDQUFDLE9BQU4sQ0FBYyxLQUFDLENBQUEsV0FBVyxDQUFDLElBQTNCO1VBRFM7UUFBQSxDQUFBLENBQUEsQ0FBQSxJQUFBLENBQVgsRUFFRSxHQUZGLEVBREY7O01BS0EsTUFBTSxDQUFDLElBQUksQ0FBQyxLQUFLLENBQUMsV0FBbEIsQ0FBOEIsSUFBQyxDQUFBLElBQS9CLEVBQXFDLE9BQXJDLEVBQThDLENBQUEsU0FBQSxLQUFBO2VBQUEsU0FBQTtVQUM1QyxJQUFHLEtBQUMsQ0FBQSxXQUFKO21CQUNFLEtBQUMsQ0FBQSxXQUFXLENBQUMsS0FBYixDQUFBLEVBREY7O1FBRDRDO01BQUEsQ0FBQSxDQUFBLENBQUEsSUFBQSxDQUE5QztJQTFDVzs7NEJBOENiLE1BQUEsR0FBUSxTQUFDLEtBQUQ7TUFDTixJQUFHLEtBQUg7ZUFDRSxJQUFDLENBQUEsZUFBRCxDQUFpQixJQUFDLENBQUEsUUFBUyxDQUFBLEtBQUEsQ0FBM0IsRUFERjs7SUFETTs7NEJBSVIsZUFBQSxHQUFpQixTQUFDLE1BQUQ7TUFDZixJQUF3QixJQUFDLENBQUEsV0FBekI7UUFBQSxJQUFDLENBQUEsV0FBVyxDQUFDLEtBQWIsQ0FBQSxFQUFBOztNQUNBLElBQUMsQ0FBQSxXQUFELEdBQWUsSUFBSSxNQUFNLENBQUMsSUFBSSxDQUFDLFVBQWhCLENBQTJCO1FBQ3hDLE9BQUEsRUFBUyxJQUFDLENBQUEsbUJBQUQsQ0FBcUIsTUFBTSxDQUFDLElBQTVCLENBRCtCO1FBRXhDLFFBQUEsRUFBVSxHQUY4QjtRQUd4QyxTQUFBLEVBQVcsR0FINkI7T0FBM0I7TUFNZixNQUFNLENBQUMsSUFBSSxDQUFDLEtBQUssQ0FBQyxXQUFsQixDQUE4QixJQUFDLENBQUEsV0FBL0IsRUFBNEMsVUFBNUMsRUFBd0QsU0FBQTtBQUN0RCxZQUFBO1FBQUEsT0FBQSxHQUFVLENBQUEsQ0FBRSxjQUFGO1FBQ1YsT0FBTyxDQUFDLEdBQVIsQ0FBWSxPQUFaLEVBQXFCLE1BQXJCO1FBQ0EsWUFBQSxHQUFlLE9BQU8sQ0FBQyxJQUFSLENBQUE7UUFDZixDQUFBLENBQUUsWUFBRixDQUFlLENBQUMsUUFBaEIsQ0FBeUIsbUJBQXpCO1FBR0EsWUFBWSxDQUFDLFFBQWIsQ0FBc0IsZUFBdEIsQ0FBc0MsQ0FBQyxJQUF2QyxDQUFBO1FBQ0EsWUFBWSxDQUFDLFFBQWIsQ0FBc0IsZUFBdEIsQ0FBc0MsQ0FBQyxJQUF2QyxDQUFBO1FBQ0EsWUFBWSxDQUFDLFFBQWIsQ0FBc0IsZUFBdEIsQ0FBc0MsQ0FBQyxJQUF2QyxDQUFBO1FBQ0EsWUFBWSxDQUFDLFFBQWIsQ0FBc0IsZUFBdEIsQ0FBc0MsQ0FBQyxJQUF2QyxDQUFBO1FBQ0EsVUFBQSxHQUFhLE9BQU8sQ0FBQyxJQUFSLENBQUE7ZUFDYixVQUFVLENBQUMsSUFBWCxDQUFBO01BWnNELENBQXhEO01BY0EsSUFBQyxDQUFBLFdBQVcsQ0FBQyxJQUFiLENBQWtCLElBQUMsQ0FBQSxJQUFuQixFQUF5QixNQUF6QjtNQUNBLElBQUcsQ0FBQyxDQUFDLFVBQUYsQ0FBYSxJQUFDLENBQUEsT0FBTyxDQUFDLFFBQXRCLENBQUg7ZUFDRSxJQUFDLENBQUEsT0FBTyxDQUFDLFFBQVQsQ0FBa0IsTUFBTSxDQUFDLE1BQXpCLEVBREY7O0lBdkJlOzs0QkEwQmpCLG1CQUFBLEdBQXFCLFNBQUMsSUFBRDtBQUNuQixVQUFBO01BQUEsUUFBQSxHQUFXLENBQUMsQ0FBQyxRQUFGLENBQVcsaWtCQUFYO2FBMEJYLFFBQUEsQ0FBUyxJQUFUO0lBM0JtQjs7NEJBNkJyQixZQUFBLEdBQWMsU0FBQTthQUNaO1FBQUEsR0FBQSxFQUFLLElBQUMsQ0FBQSxPQUFPLENBQUMsWUFBZDtRQUNBLElBQUEsRUFBTSxJQUFJLE1BQU0sQ0FBQyxJQUFJLENBQUMsSUFBaEIsQ0FBcUIsRUFBckIsRUFBeUIsRUFBekIsQ0FETjtRQUVBLE1BQUEsRUFBUSxJQUFJLE1BQU0sQ0FBQyxJQUFJLENBQUMsS0FBaEIsQ0FBc0IsQ0FBdEIsRUFBeUIsQ0FBekIsQ0FGUjtRQUdBLE1BQUEsRUFBUSxJQUFJLE1BQU0sQ0FBQyxJQUFJLENBQUMsS0FBaEIsQ0FBc0IsRUFBdEIsRUFBMEIsRUFBMUIsQ0FIUjs7SUFEWTs7Ozs7QUFoSGhCIiwic291cmNlc0NvbnRlbnQiOlsiY2xhc3Mgd2luZG93Lkdvb2dsZU1hcFZpZXdcbiAgQG9wdGlvbnM6XG4gICAgbWFya2Vyc0ltYWdlOiAnL2xvY2FsL3RlbXBsYXRlcy9yZXNwZWN0L2ltYWdlcy9tYXAtbWFya2VyLnBuZydcbiAgICBkZWZhdWx0OlxuICAgICAgY2VudGVyOiB7bGF0OiA1NS43NDk0NzMzLCBsbmc6IDM3LjM1MjMyfSxcbiAgICAgIHNjcm9sbHdoZWVsOiBmYWxzZSxcbiAgICAgIHpvb206IDhcbiAgY29uc3RydWN0b3I6ICh3cmFwcGVyLCBvcHRpb25zID0ge30pIC0+XG4gICAgQG9wdGlvbnMgPSBfLmV4dGVuZCBHb29nbGVNYXBWaWV3Lm9wdGlvbnMsIG9wdGlvbnNcbiAgICBAX21hcmtlcnMgPSB7fVxuXG4gICAgQF9tYXBPcHRpb25zID0gaWYgQG9wdGlvbnMuZ29vZ2xlXG4gICAgICBfLmV4dGVuZCBAb3B0aW9ucy5kZWZhdWx0LCBAb3B0aW9ucy5nb29nbGVcbiAgICBlbHNlXG4gICAgICBAb3B0aW9ucy5kZWZhdWx0XG5cbiAgICBAX21hcCA9IG5ldyBnb29nbGUubWFwcy5NYXAod3JhcHBlclswXSwgQF9tYXBPcHRpb25zKVxuXG4gICAgYm91bmRzID0gbmV3IGdvb2dsZS5tYXBzLkxhdExuZ0JvdW5kcygpXG5cbiAgICBmb3IgaXRlbSBpbiBAb3B0aW9ucy5pdGVtc1xuICAgICAgbWFya2VyID0gbmV3IGdvb2dsZS5tYXBzLk1hcmtlcih7XG4gICAgICAgIG1hcDogQF9tYXAsXG4gICAgICAgIHBvc2l0aW9uOiBpdGVtLmNvb3JkaW5hdGVzLFxuICAgICAgICB0aXRsZTogaXRlbS50aXRsZVxuICAgICAgICBpY29uOiBAX21hcmtlckltYWdlKClcbiAgICAgICAgekluZGV4OiBpdGVtLmluZGV4XG4gICAgICB9KVxuXG4gICAgICBib3VuZHMuZXh0ZW5kIGl0ZW0uY29vcmRpbmF0ZXNcblxuICAgICAgbWFya2VyLmRhdGEgPSBpdGVtXG5cbiAgICAgIHBhZ2UgPSBAXG4gICAgICBtYXJrZXIuYWRkTGlzdGVuZXIgJ2NsaWNrJywgKGEsIGIsIGMpIC0+XG4gICAgICAgIHBhZ2UuX3Nob3dJbmZvV2luZG93KHRoaXMpXG5cbiAgICAgIEBfbWFya2Vyc1tpdGVtLmluZGV4XSA9IG1hcmtlclxuXG4gICAgaWYgISQuaXNFbXB0eU9iamVjdChAX21hcmtlcnMpXG4gICAgICBAX21hcC5maXRCb3VuZHMoYm91bmRzKVxuICAgIGVsc2VcbiAgICAgIEBfbWFwLnNldENlbnRlcihAX21hcE9wdGlvbnMuY2VudGVyKVxuXG4gICAgaWYgQG9wdGlvbnMuaXRlbXMubGVuZ3RoIGlzIDFcbiAgICAgIHNldFRpbWVvdXQgPT5cbiAgICAgICAgQF9tYXAuc2V0Wm9vbSBAX21hcE9wdGlvbnMuem9vbVxuICAgICAgLCA1MDBcblxuICAgIGdvb2dsZS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyIEBfbWFwLCAnY2xpY2snLCAoKSA9PlxuICAgICAgaWYgQF9pbmZvV2luZG93XG4gICAgICAgIEBfaW5mb1dpbmRvdy5jbG9zZSgpO1xuXG4gIHNlbGVjdDogKGluZGV4KSAtPlxuICAgIGlmIGluZGV4XG4gICAgICBAX3Nob3dJbmZvV2luZG93KEBfbWFya2Vyc1tpbmRleF0pXG5cbiAgX3Nob3dJbmZvV2luZG93OiAobWFya2VyKSAtPlxuICAgIEBfaW5mb1dpbmRvdy5jbG9zZSgpIGlmIEBfaW5mb1dpbmRvd1xuICAgIEBfaW5mb1dpbmRvdyA9IG5ldyBnb29nbGUubWFwcy5JbmZvV2luZG93KHtcbiAgICAgIGNvbnRlbnQ6IEBfaW5mb1dpbmRvd1RlbXBsYXRlKG1hcmtlci5kYXRhKVxuICAgICAgbWF4V2lkdGg6IDM1MFxuICAgICAgbWF4SGVpZ2h0OiAyMjBcbiAgICB9KVxuXG4gICAgZ29vZ2xlLm1hcHMuZXZlbnQuYWRkTGlzdGVuZXIgQF9pbmZvV2luZG93LCAnZG9tcmVhZHknLCAtPlxuICAgICAgaXdPdXRlciA9ICQoJy5nbS1zdHlsZS1pdycpXG4gICAgICBpd091dGVyLmNzcyAnd2lkdGgnLCAnYXV0bydcbiAgICAgIGl3QmFja2dyb3VuZCA9IGl3T3V0ZXIucHJldigpXG4gICAgICAkKGl3QmFja2dyb3VuZCkuYWRkQ2xhc3MgJ2J1YmJsZS1iYWNrZ3JvdW5kJ1xuXG4gICAgICAjIFJlbW92ZXMgYmFja2dyb3VuZCBhcnJvd3MgYW5kIHNoYWRvd3NcbiAgICAgIGl3QmFja2dyb3VuZC5jaGlsZHJlbignOm50aC1jaGlsZCgxKScpLmhpZGUoKVxuICAgICAgaXdCYWNrZ3JvdW5kLmNoaWxkcmVuKCc6bnRoLWNoaWxkKDIpJykuaGlkZSgpXG4gICAgICBpd0JhY2tncm91bmQuY2hpbGRyZW4oJzpudGgtY2hpbGQoMyknKS5oaWRlKClcbiAgICAgIGl3QmFja2dyb3VuZC5jaGlsZHJlbignOm50aC1jaGlsZCg0KScpLmhpZGUoKVxuICAgICAgaXdDbG9zZUJ0biA9IGl3T3V0ZXIubmV4dCgpXG4gICAgICBpd0Nsb3NlQnRuLmhpZGUoKVxuXG4gICAgQF9pbmZvV2luZG93Lm9wZW4oQF9tYXAsIG1hcmtlcilcbiAgICBpZiBfLmlzRnVuY3Rpb24gQG9wdGlvbnMub25TZWxlY3RcbiAgICAgIEBvcHRpb25zLm9uU2VsZWN0KG1hcmtlci56SW5kZXgpXG5cbiAgX2luZm9XaW5kb3dUZW1wbGF0ZTogKGRhdGEpIC0+XG4gICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlICcnJ1xuICAgICAgPGRpdiBjbGFzcz1cIm1hcC1idWJibGVcIj5cbiAgICAgICAgPGRpdiBjbGFzcz1cIm1hcC1idWJibGVfX3RpdGxlXCI+PCU9dGl0bGUlPjwvZGl2PlxuICAgICAgICA8JSBpZiAoc3Vid2F5KSB7ICU+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJtYXAtYnViYmxlX19tZXRyb1wiPlxuICAgICAgICAgIDxpIGNsYXNzPVwiaWNvbiBpY29uLW1ldHJvXCI+PC9pPlxuICAgICAgICAgIDwlPXN1YndheSU+XG4gICAgICAgIDwvZGl2PlxuICAgICAgICA8JSB9ICU+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJtYXAtYnViYmxlX19hZGRyZXNzXCI+PCU9YWRkcmVzcyU+PC9kaXY+XG4gICAgICAgIDx1bCBjbGFzcz1cIm1hcC1idWJibGVfX2luZm9cIj5cbiAgICAgICAgICA8JSBpZiAod29ya3RpbWUpIHsgJT5cbiAgICAgICAgICA8bGk+XG4gICAgICAgICAgICA8aSBjbGFzcz1cImljb24gaWNvbi1jbG9ja1wiPjwvaT5cbiAgICAgICAgICAgIDxzcGFuPjwlPXdvcmt0aW1lJT48L3NwYW4+XG4gICAgICAgICAgPC9saT5cbiAgICAgICAgICA8JSB9ICU+XG4gICAgICAgICAgPCUgaWYgKHBob25lKSB7ICU+XG4gICAgICAgICAgPGxpPlxuICAgICAgICAgICAgPGkgY2xhc3M9XCJpY29uIGljb24tcGhvbmVcIj48L2k+XG4gICAgICAgICAgICA8c3Bhbj48JT1waG9uZSU+PC9zcGFuPlxuICAgICAgICAgIDwvbGk+XG4gICAgICAgICAgPCUgfSAlPlxuICAgICAgICA8L3VsPlxuICAgICAgPC9kaXY+XG4nJydcbiAgICB0ZW1wbGF0ZShkYXRhKVxuXG4gIF9tYXJrZXJJbWFnZTogLT5cbiAgICB1cmw6IEBvcHRpb25zLm1hcmtlcnNJbWFnZVxuICAgIHNpemU6IG5ldyBnb29nbGUubWFwcy5TaXplKDQ3LCA2MSlcbiAgICBvcmlnaW46IG5ldyBnb29nbGUubWFwcy5Qb2ludCgwLCAwKVxuICAgIGFuY2hvcjogbmV3IGdvb2dsZS5tYXBzLlBvaW50KDIzLCA2MSkiXX0=
