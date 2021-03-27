$(document).ready(function () {
	$('.tabs-item').on('click', function () {
		$('#metro,#map,#wrap').hide();
		$($(this).data('block')).show();
    });
	$('.from-ul').on('click', function (e) {
	    $('.from-ul-li-ul').hide();
	    $(this).find('.from-ul-li-ul').show();
    });
})

$(function () {
    $('.js-shop-search').each(function () {
        var map_container = $('#map .shop-map'),
            reloadTimer,
            store_name = $('input[name=store_name]'),
            metro_id = $('select[name=metro_id]'),
            map;

        store_name.on('input', function () {
            reloadObjects($('.korpus .tabs-item.active').data('target'));
        });

        metro_id.on('change', function () {
            reloadObjects($('.korpus .tabs-item.active').data('target'));
        });

        setTimeout(function () {
            $('.korpus .tabs-item').on('show', function () {
                reloadObjects($(this).data('target'), 0);
            });
        }, 500);

        var reloadObjects = function (type, timeout) {
            if (timeout == null)
                timeout = 500;

            if (reloadTimer)
                clearTimeout(reloadTimer);

            var data = {
                'store_name': $.trim(store_name.val()),
                'metro_id': $.trim(metro_id.val())
            };
            reloadTimer = setTimeout(function () {
                if (type == '#list') {
                    data['ajax'] = 'y';
                    data['search'] = 'y';
                    $.get(document.location, data, function (data) {
                        $('#wrap .shop-cards').html(data);
                    });

                } else {
                    data['show_map'] = 'y';

                    $.get(document.location, data, function (response) {
                        var shops = [];

                        $.each(response.shops, function (i, shop) {
                            shop.index = i;
                            shops.push(shop);
                        });


                        if (type == '#map') {
                            map = new window.GoogleMapView(map_container, {
                                items: shops
                            });
                        }

                        if (type == '#metro') {
                            BX.message({'RESERVED_STORES_LIST': JSON.stringify(response.shops)});
                            initMetro ();
                        }

                    }, 'json');
                }
            }, timeout);
        };
    });

});

function initMetro () {
    var stores = JSON.parse(BX.message('RESERVED_STORES_LIST'));
    $.ajax({
        method: 'get',
        url: '/local/templates/respect/images/moscow-metro.svg?v3',
        success: function(data) {
            $("#metro").empty();
            $("#metro").append(new XMLSerializer().serializeToString(data.documentElement));

            var len; var shop; var marker;
            for (var i = 0; i < stores.length; i++) {
                shop = stores[i];
                if (shop.subway_trans) {
                    if ($("#metro #" + shop.subway_trans).length > 0) {
                        marker = new SubwayMapMarker(shop, $("#metro #" + shop.subway_trans));
                        marker.appendTo($('#metro'));
                    }
                }
                //results.push(_this._markers[shop.subway_alias] = _this.marker);
            }
        }
    });
}

window.SubwayMapMarker = (function() {
    function SubwayMapMarker(data, point) {
        this.data = data;
        this.point = point;
        this.marker = $('<div class="map-marker">');
        this.bubble = $(this._infoWindowTemplate(this.data)).appendTo(this.marker);
        this.marker.on('click', this.show.bind(this));
        $(document).on('mouseup', (function(_this) {
            return function(event) {
                if (!_this.marker.is(event.target) && _this.marker.has(event.target).length === 0) {
                    return _this.hide();
                }
            };
        })(this));
    }

    SubwayMapMarker.prototype.appendTo = function(container) {
        var coordinates, position, svg;
        $(container).append(this.marker);
        svg = $('svg', container);
        coordinates = {
            top: this.point.offset().top - $(container).offset().top,
            left: this.point.offset().left - $(container).offset().left - this.marker.width()
        };
        //костыль, немного правим положение точек, для точности
        var left = ((coordinates.left + this.point.width() / 2) / $(container).width() * 100);
        if($(window).width()>1500){
            if(left<20){left=left+5.5}else
            // if(left>20.1 && left<30){left=left+5}else
            if(left>20.1 && left<30){left=left+4.5}else
            if(left>30.1 && left<40){left=left+4}else
            if(left>40.1 && left<50){left=left+3}else
            if(left>50 && left<60){left=left+2} else
            if(left>60 && left<67){left=left+2} else
            if(left>67 && left<70){left=left+1} else
            if(left>70.1 && left<75){left=left+0.5}
        }else if($(window).width()>1200 && $(window).width()<1499){
            if(left<20){left=left+6.5}else
            if(left>20.1 && left<30){left=left+5.5}else
            if(left>30.1 && left<40){left=left+5}else
            if(left>40.1 && left<50){left=left+3}else
            if(left>50 && left<60){left=left+2} else
            if(left>60 && left<67){left=left+2} else
            if(left>67 && left<70){left=left+1} else
            if(left>70.1 && left<75){left=left+0.5}
        }else if($(window).width()>1100 && $(window).width()<1199){
            if(left<20){left=left+7.5}else
            if(left>20.01 && left<30){left=left+6.5}else
            if(left>30.01 && left<40){left=left+5}else
            if(left>40.01 && left<50){left=left+3}else
            if(left>50 && left<60){left=left+2} else
            if(left>60 && left<67){left=left+2} else
            if(left>67 && left<70){left=left+1} else
            if(left>70.01 && left<75){left=left+0.5}
        }else if($(window).width()>1000 && $(window).width()<1099){
            if(left<20){left=left+8.5}else
            if(left>20.1 && left<30){left=left+6.5}else
            if(left>30.1 && left<40){left=left+6}else
            if(left>40.1 && left<50){left=left+4}else
            if(left>50 && left<60){left=left+3} else
            if(left>60 && left<67){left=left+3} else
            if(left>67 && left<70){left=left+2} else
            if(left>70.1 && left<77){left=left+1.5}
        }else if($(window).width()>900 && $(window).width()<999){
            if(left<20){left=left+9.5}else
            if(left>20.1 && left<77){left=left+9}
        }else if($(window).width()>800 && $(window).width()<899){
            left=left+10.5;
        }else if($(window).width()>700 && $(window).width()<799){
            left=left+11.5;
        }else if($(window).width()>600 && $(window).width()<699){
            left=left+12.5;
        }else if($(window).width()>500 && $(window).width()<599){
            left=left+15.5;
        }else if($(window).width()>400 && $(window).width()<499){
            left=left+19.5;
        }else if($(window).width()>300 && $(window).width()<399){
            left=left+25.5;
        }

        position = {
            left: left + "%",
            top: ((coordinates.top + this.point.height() / 2) / $(container).outerHeight() * 100) + "%"
        };
        return this.marker.css(position);
    };

    SubwayMapMarker.prototype.show = function() {
        store_id=this.data.index;
        return this.marker.addClass('with-bubble');
    };

    SubwayMapMarker.prototype.hide = function() {
        store_id=0;
        return this.marker.removeClass('with-bubble');
    };

    SubwayMapMarker.prototype._infoWindowTemplate = function(data) {
        var template;
        template = _.template('<div class="map-bubble">\n  <div class="map-bubble__title"><%=title%></div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n  </ul>\n</div>');
        return template(data);
    };

    return SubwayMapMarker;

})();


// $(function () {
//     $('.js-shop-search').each(function () {
//
//         var map_container = $('#map .shop-map'),
//             reloadTimer,
//             store_name = $('input.store_name'),
//             metro_id = $('select.js-metro-id'),
//             map;
//
//         store_name.on('input', function () {
//             reloadObjects($('.korpus .tab-item:checked').data('target'));
//         });
//
//         metro_id.on('change', function () {
//             reloadObjects($('.korpus .tab-item:checked').data('target'));
//         });
//
//         setTimeout(function () {
//             $('.korpus .tab-item').on('show', function () {
//                 reloadObjects($(this).data('target'), 0);
//             });
//         }, 500);
//
//         var reloadObjects = function (type, timeout) {
//             console.info('reloadObjects');
//             if (timeout == null)
//                 timeout = 500;
//
//             /*if (reloadTimer)
//                 clearTimeout(reloadTimer);*/
//
//             var data = {
//                 'store_name': $.trim(store_name.val()),
//                 'metro_id': $.trim(metro_id.val())
//             };
//
//             reloadTimer = setTimeout(function () {
//                 console.info('list');
//                 if (type == 'list') {
//                     data['ajax'] = 'y';
//                     data['search'] = 'y';
//
//                     $.get(document.location, data, function (data) {
//                         $('#wrap .shop-cards').html(data);
//                     });
//
//                 } else {
//                 	console.info('show_map');
//                     data['show_map'] = 'y';
//
//                     $.get(document.location, data, function (response) {
//                         var shops = [];
//
//                         $.each(response.shops, function (i, shop) {
//                             shop.index = i;
//                             shops.push(shop);
//                         });
//
//
// 						if (type == 'map') {
// 							map = new window.GoogleMapView(map_container, {
// 								items: shops
// 							});
// 						}
//
// 						if (type == 'metro') {
// 							BX.message({'RESERVED_STORES_LIST': JSON.stringify(response.shops)});
// 							initMetro ();
// 						}
//
//
//                     }, 'json');
//                 }
//             }, timeout);
//         };
//     });
//
// });
//
//
//
// function initMetro () {
// 	var stores = JSON.parse(BX.message('RESERVED_STORES_LIST'));
//
// 	//console.log(stores);
// 	$.ajax({
// 		method: 'get',
// 		url: '/local/templates/respect/images/moscow-metro.svg?v3',
// 		success: function(data) {
// 			$("#metro").empty();
// 			$("#metro").append(new XMLSerializer().serializeToString(data.documentElement));
//
// 			var len; var shop; var marker;
// 			for (var i = 0; i < stores.length; i++) {
// 				shop = stores[i];
//
// 				if (shop.subway_trans) {
// 					if ($("#metro #" + shop.subway_trans).length>0) {
// 						marker = new SubwayMapMarker(shop, $("#metro #" + shop.subway_trans));
// 						marker.appendTo($('#metro'));
// 					}
// 				}
//
// 				//results.push(_this._markers[shop.subway_alias] = _this.marker);
// 			}
// 		}
// 	});
// }
//
//
//
// window.SubwayMapMarker = (function() {
// 	function SubwayMapMarker(data, point) {
// 		this.data = data;
// 		this.point = point;
// 		this.marker = $('<div class="map-marker">');
// 		this.bubble = $(this._infoWindowTemplate(this.data)).appendTo(this.marker);
// 		this.marker.on('click', this.show.bind(this));
// 		$(document).on('mouseup', (function(_this) {
// 		return function(event) {
// 		if (!_this.marker.is(event.target) && _this.marker.has(event.target).length === 0) {
// 		return _this.hide();
// 		}
// 		};
// 		})(this));
// 	}
//
// 	SubwayMapMarker.prototype.appendTo = function(container) {
// 		var coordinates, position, svg;
// 		$(container).append(this.marker);
// 		svg = $('svg', container);
// 		coordinates = {
// 		top: this.point.offset().top - $(container).offset().top,
// 		left: this.point.offset().left - $(container).offset().left - this.marker.width()
// 		};
// 		console.log(coordinates);
// 		position = {
// 		left: ((coordinates.left + this.point.width() / 2) / $(container).width() * 100) + "%",
// 		top: ((coordinates.top + this.point.height() / 2) / $(container).outerHeight() * 100) + "%"
// 		};
// 		return this.marker.css(position);
// 	};
//
// 	SubwayMapMarker.prototype.show = function() {
// 		store_id=this.data.index;
// 		return this.marker.addClass('with-bubble');
// 	};
//
// 	SubwayMapMarker.prototype.hide = function() {
// 		store_id=0;
// 		return this.marker.removeClass('with-bubble');
// 	};
//
// 	SubwayMapMarker.prototype._infoWindowTemplate = function(data) {
// 		var template;
// 		template = _.template('<div class="map-bubble">\n  <div class="map-bubble__title"><%=title%></div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n  </ul>\n</div>');
// 		return template(data);
// 	};
//
// 	return SubwayMapMarker;
//
// })();