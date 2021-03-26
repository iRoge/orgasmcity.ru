$(function(){
  $('.from-ul-li').click(function(){
    $('.from-ul-li-ul').toggle(100);
  });

  $('.auth').click(function(){
    $('.auth-div').toggle(100);
  });
  $('.auth2').click(function(){
    $('.auth-div').toggle(100);
  });
  $('.auth-div-desk').parent().hover(
    function() {
	  $(this).find('.auth-div-personal').toggle(100);
	}, function() {
	  $(this).find('.auth-div-personal').toggle(100);
	}
  );

  $('.mail2').click(function(){
    $('.mail-div').toggle(0);
    $('.podlozhka').toggle(0);
    $('.mail-div .popup').show(0);
	sendYandexMetrkiaGoal('feedback-open');
  });

  $('.obr').click(function(){
    $('.mail-div').toggle(0);
    $('.podlozhka').toggle(0);
    $('.mail-div .popup').show(0);
  sendYandexMetrkiaGoal('feedback-open');
  });


  $('.ent').click(function(){
    $('.auth-div-full').toggle(0);
    $('.podlozhka').toggle(0);
  });
  $('.reg').click(function(){
    $('.auth-div-full').toggle(0);
    $('.podlozhka').toggle(0);
  });


  $(document).ready( function() {
    $('form[name=SIMPLE_FORM_1]').submit(function () {
      if($('form[name=SIMPLE_FORM_1] .alert-content').lenght>0){
        console.info('блок с ошибками есть');
      }
    });
  });
/*
$(function() {
  $('.menu-ul-li').hover(function(){
      $(this).next("div.hide-menu").show();
      $(this).toggleClass('active-menu');
  }, function(){
      $(this).toggleClass('active-menu');
      $('.hide-menu').hide();
  });
  $('.menu-ul-li').next('.hide-menu').mouseenter(function(){
      $(this).toggleClass('active-menu');
  }).mouseleave(function(){
      $('.hide-menu').hide();
      $(this).toggleClass('active-menu');
  });
})
*/

$('.grey-first').on('change', function(){
    let $option = $(this);
    if ($option.val() == '0') {
        $option.css('color','#b8b4b4');
    } else {
        $option.css('color','#4e4e4e');
    }
}).change();

/* переписаны методы меню */
$(window).resize(function() {
	$('.hide-menu').css('height', 'auto').each(function() {
        let heightLeftHide = $('.left-hide-menu', this).outerHeight(true);
        let heightRightHide = $('.right-hide-menu', this).outerHeight(true);
		
		if(heightLeftHide > heightRightHide) {
			$(this).css('height', heightLeftHide);
		} else {
			$(this).css('height', heightRightHide);
		}
	});
});
$(function() {
	$('.menu-ul-li.js-has-children').hover(function() {
		$('.menu-ul-li-a', this).addClass('active-menu');
		$('.hide-menu').hide().filter($(this).next('.hide-menu')).show();
	},function(){
    $('.menu-ul-li-a', this).removeClass('active-menu');
  });

  $('.menu').mouseleave(function(){
    $('.hide-menu').hide()
    $('.menu-ul-li-a', this).removeClass('active-menu');
  });
  $('.menu-ul-li:not(.js-has-children)').hover(function(){
    $('.hide-menu').hide()
  });
	
	$('.hide-menu').hover(function() {
		$(this).show();
		$(this).prev('.menu-ul-li').find('.menu-ul-li-a').toggleClass('active-menu');
	},function() {
		$(this).hide();
		$(this).prev('.menu-ul-li').find('.menu-ul-li-a').toggleClass('active-menu');
	});
});

/*$(window).resize(function() {
  $('.hide-menu').css('display', 'block');

  $('#div-new').css('height', 'auto');
  $('#div-womens').css('height', 'auto');
  $('#div-mens').css('height', 'auto');
  $('#div-stocks').css('height', 'auto');
  $('#div-shops').css('height', 'auto');
  $('#div-bonus').css('height', 'auto');
  $('#div-news').css('height', 'auto');
  $('#div-sale').css('height', 'auto');

  var heightLeftHide = $('#div-new .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-new .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-new').css('height', heightLeftHide);
  }
  else
  {
    $('#div-new').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-womens .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-womens .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-womens').css('height', heightLeftHide);
  }
  else
  {
    $('#div-womens').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-mens .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-mens .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-mens').css('height', heightLeftHide);
  }
  else
  {
    $('#div-mens').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-stocks .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-stocks .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-stocks').css('height', heightLeftHide);
  }
  else
  {
    $('#div-stocks').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-shops .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-shops .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-shops').css('height', heightLeftHide);
  }
  else
  {
    $('#div-shops').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-bonus .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-bonus .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-bonus').css('height', heightLeftHide);
  }
  else
  {
    $('#div-bonus').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-news .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-news .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-news').css('height', heightLeftHide);
  }
  else
  {
    $('#div-news').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-sale .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-sale .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-sale').css('height', heightLeftHide);
  }
  else
  {
    $('#div-sale').css('height', heightRightHide);
  }

  $('.hide-menu').css('display', 'none');
});

$(function() {
  $('.hide-menu').css('display', 'block');

  var heightLeftHide = $('#div-new .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-new .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-new').css('height', heightLeftHide);
  }
  else
  {
    $('#div-new').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-womens .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-womens .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-womens').css('height', heightLeftHide);
  }
  else
  {
    $('#div-womens').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-mens .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-mens .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-mens').css('height', heightLeftHide);
  }
  else
  {
    $('#div-mens').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-stocks .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-stocks .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-stocks').css('height', heightLeftHide);
  }
  else
  {
    $('#div-stocks').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-shops .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-shops .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-shops').css('height', heightLeftHide);
  }
  else
  {
    $('#div-shops').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-bonus .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-bonus .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-bonus').css('height', heightLeftHide);
  }
  else
  {
    $('#div-bonus').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-news .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-news .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-news').css('height', heightLeftHide);
  }
  else
  {
    $('#div-news').css('height', heightRightHide);
  }

  var heightLeftHide = $('#div-sale .left-hide-menu').outerHeight(true);
  var heightRightHide = $('#div-sale .right-hide-menu').outerHeight(true);
  if(heightLeftHide > heightRightHide)
  {
    $('#div-sale').css('height', heightLeftHide);
  }
  else
  {
    $('#div-sale').css('height', heightRightHide);
  }

  $('.hide-menu').css('display', 'none');





  $('#new').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').show();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-new').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-new').hover(function() {
    $(this).show();
    $('.new-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.new-a').toggleClass('active-menu');
  });

  $('#womens').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').show();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-womens').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-womens').hover(function() {
    $(this).show();
    $('.womens-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.womens-a').toggleClass('active-menu');
  });

  $('#mens').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').show();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-mens').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-mens').hover(function() {
    $(this).show();
    $('.mens-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.mens-a').toggleClass('active-menu');
  });

  $('#stocks').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').show();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-stocks').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-stocks').hover(function() {
    $(this).show();
    $('.stocks-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.stocks-a').toggleClass('active-menu');
  });

  $('#shops').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').show();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-shops').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-shops').hover(function() {
    $(this).show();
    $('.shops-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.shops-a').toggleClass('active-menu');
  });

  $('#bonus').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').show();
    $('#div-news').hide();
    $('#div-sale').hide();
  },function(){
    $('#div-bonus').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-bonus').hover(function() {
    $(this).show();
    $('.bonus-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.bonus-a').toggleClass('active-menu');
  });

  $('#news').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').show();
    $('#div-sale').hide();
  },function(){
    $('#div-news').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-news').hover(function() {
    $(this).show();
    $('.news-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.news-a').toggleClass('active-menu');
  });

  $('#sale').hover(function() {
    $('.menu-ul-li-a', this).toggleClass('active-menu');
    $('#div-new').hide();
    $('#div-womens').hide();
    $('#div-mens').hide();
    $('#div-stocks').hide();
    $('#div-shops').hide();
    $('#div-bonus').hide();
    $('#div-news').hide();
    $('#div-sale').show();
  },function(){
    $('#div-sale').hide();
    $('.menu-ul-li-a', this).toggleClass('active-menu');
  });

  $('#div-sale').hover(function() {
    $(this).show();
    $('.sale-a').toggleClass('active-menu');
  },function() {
    $(this).hide();
    $('.sale-a').toggleClass('active-menu');
  });


});*/



$(function() {
  setTimeout(function() {
    if($(window).width() > 767)
    {
      let leftb = $('.left-main-two');
      let rightb = $('.right-main-two');
      let imgleft = $('.left-main-two img');

      let hleft = leftb.outerHeight(true);
      let hright = rightb.outerHeight(true);


      if(hleft > hright)
      {
        rightb.css('height', hleft);
      }
      else
      {
        leftb.css('height', hright);
        imgleft.css('height', hright);
      }
    }
    else
    {
      let left_main_two = $('.left-main-two img');
      left_main_two.css('height', 'auto');
      left_main_two.css('width', '100%');
    }
  },300);
});

$(window).resize(function() {
  if($(window).width() > 767)
  {
    $('.left-main-two img').css('height', 'auto');
    $('.left-main-two').css('height', 'auto');
    $('.right-main-two').css('height', 'auto');

    let leftb = $('.left-main-two');
    let rightb = $('.right-main-two');
    let imgleft = $('.left-main-two img');

    let hleft = leftb.outerHeight(true);
    let hright = rightb.outerHeight(true);

    if(hleft > hright)
    {
      rightb.css('height', hleft);
      imgleft.css('height', 'auto');
    }
    else
    {
      leftb.css('height', hright);
      imgleft.css('height', hright);
    }
  }
  else {
    let left_main_two = $('.left-main-two img');
    left_main_two.css('height', 'auto');
    left_main_two.css('width', '100%');
  }
});



$(function() {
  if($(window).width() > 991)
  {
    let leftbb = $('.in-main-top');
    let rightbb = $('.shoes-top');

    let hhleft = leftbb.height();
    let hhright = rightbb.height();

    rightbb.css('height', hhleft);
  }
});

$(window).resize(function() {
  if($(window).width() > 991)
  {
    let leftbb = $('.in-main-top');
    let rightbb = $('.shoes-top');

    let hhleft = leftbb.height();
    let hhright = rightbb.height();

    rightbb.css('height', hhleft);
  }
});



/*
$(function() {
  $('.hide-menu').css('display', 'block');
  var leftbbb = $('.col-md-4.left-hide-menu');
  var rightbbb = $('.right-hide-menu');

  var hhhleft = leftbbb.outerHeight(true);
  var hhhright = rightbbb.outerHeight(true);

  rightbbb.css('height', hhhleft);

  $('.hide-menu').css('display', 'none');

});
*/



$(function() {
  setTimeout(function() {
    $('.bestsel').slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 1,
      lazyLoad: 'ondemand',
      responsive: [
          {
            breakpoint: 5000,
            settings: {
              slidesToShow: 4
            }
          },
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 3
            }
          },
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 2
            }
          },
          {
              breakpoint: 700,
              settings: {
                slidesToShow: 1
            }
          }
      ]
    });
  },300);





  $('.slider-for').slick({
   slidesToShow: 1,
   slidesToScroll: 1,
   arrows: true,
   fade: true,
   asNavFor: '.slider-nav',
   responsive: [
     {
       breakpoint: 991,
       settings: {
         arrows: false
       }
     }
   ]
 });
 $('.slider-nav').slick({
   slidesToShow: 6,
   slidesToScroll: 1,
   asNavFor: '.slider-for',
   dots: false,
   focusOnSelect: true
 });

 $('.slider-for-vert-mob').slick({
   slidesToShow: 1,
   slidesToScroll: 1,
   dots: false,
   arrows: true,
   infinite: true,
   focusOnSelect: true
 });









 $('.slider-for-vert').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: true,
  centerMode: true,
  dots: false,
  infinite: false,
  vertical: true,
  verticalSwiping: true,
  asNavFor: '.slider-nav-vert',
});

$('.slider-nav-vert').slick({
  slidesToShow: 7,
  slidesToScroll: 1,
  arrows: false,
  infinite: false,
  dots: false,
  asNavFor: '.slider-for-vert',
  focusOnSelect: true,
  vertical: true
});




});


$(function(){

  $('.y').mouseover(function() {
    let that = $(this);
    that.css('min-width', that.width());
    that.width(that.width() + 20);
    /*$(this).css('left', $(this).position().left - 10);*/
  });

  $('.y').mouseout(function() {
    let that = $(this);
    that.width(that.width() - 20);
    /*$(this).css('left', $(this).position().left + 10);*/

  })

})

$(function(){

  $('.y2').mouseover(function() {
    let that = $(this);
    that.css('min-width', that.width());
    that.width(that.width() + 30);
    /*$(this).css('left', $(this).position().left - 10);*/
  });

  $('.y2').mouseout(function() {
    let that = $(this);
    that.width(that.width() - 30);
    /*$(this).css('left', $(this).position().left + 10);*/

  })

})


	 $(function () {
  	  $('.real-show-hint').mouseover(function(e){
        let that = $(this);
        let ypos = that.offset().top+24;
        let xpos = that.offset().left+160;
        let RealHint = that.data('hint');
        let RealHintElem = $(RealHint);
        RealHintElem.css('top',ypos);
        RealHintElem.css('left',xpos);
        RealHintElem.css('display', 'block');
  	  	return;
  	  });
      $('.real-show-hint').mouseout(function(e){
        let that = $(this);
        let ypos = that.offset().top+24;
        let xpos = that.offset().left+160;
        let RealHint =  that.data('hint');
        let RealHintElem = $(RealHint);
        RealHintElem.css('top',ypos);
        RealHintElem.css('left',xpos);
        RealHintElem.css('display', 'none');
  	  	return;
  	  });
	  });


    $(function() {
      $('#close-sale').click(function() {
        $('.full').slideUp('fast');
      })
    });

/*
    $(function() {
        $('.name-h3').click(function() {
            if ($(this).parent().hasClass('in-left-catalog--no-toggle')) {
                return;
            }
            $(this).toggleClass('active-name-h3');
            $(this).next('.in-in-left').toggle('fast');
            $(this).find(".plus").toggle(0);
            $(this).find(".minus").toggle(0);
        });
    });

*/

    /*$(document).ready(function()
    {
      var jQuerySliderOptions={orientation:'horizontal',animate:true,range:'min',min:1000,max:10000,value:3000};
      $("#jQuerySlider").slider(jQuerySliderOptions);
      function bookmark_hide_showScroll()
      {
        var $obj=$("#wb_bookmark_hide_show");
        if(!$obj.hasClass("in-viewport")&&$obj.inViewPort(false))
        {
          $obj.addClass("in-viewport");ShowObject('BookmarkMenu',0);
        }
        else if($obj.hasClass("in-viewport")&&!$obj.inViewPort(true))
        {
          $obj.removeClass("in-viewport");ShowObject('BookmarkMenu',1);
        }
      }
      if(!$('#wb_bookmark_hide_show').inViewPort(true))
      {
        $('#wb_bookmark_hide_show').addClass("in-viewport");
      }
      bookmark_hide_showScroll();$(window).scroll(function(event)
      {
          bookmark_hide_showScroll();
        });
      });*/

      $(function(){
        $('#jQuerySlider').slider({step:5,min:1000,max:10000,values:[3000,5000],range:true,change:function(event,ui){$('#Editbox1').val(ui.values[0]);$('#Editbox2').val(ui.values[1]);},slide:function(event,ui){$('#Editbox1').val(ui.values[0]);$('#Editbox2').val(ui.values[1]);}});$('#Editbox1').val($('#jQuerySlider').slider("values",0));$('#Editbox2').val($('#jQuerySlider').slider("values",1));$('#Editbox1').change(function(){var value1=$('#Editbox1').val();var value2=$('#Editbox2').val();if(value1<100){value1=100;$('#Editbox1').val(100)}
        if(parseInt(value1)>parseInt(value2)){
          value1=value2;$('#Editbox1').val(value1);
        }
        $('#jQuerySlider').slider("values",0,value1);});$('#Editbox2').change(function(){var value1=$('#Editbox1').val();var value2=$('#Editbox2').val();if(value2>10000){value2=10000;$('#Editbox2').val(10000)}
        if(parseInt(value1)>parseInt(value2)){
          value2=value1;$('#Editbox2').val(value2);}
        $('#jQuerySlider').slider("values",1,value2);});});function validate(inp){inp.value=inp.value.replace(/[^0-9]/,"");}
        $('#wb_text3').click(function(){$('#Editbox1').focus();});$('#Button').click(function(){$('#Editbox2').focus();});




        $(function() {
          //scrollpane parts
          let scrollPane = $( ".scroll-pane" ),
         scrollContent = $( ".scroll-content" );

          //build slider
          let scrollbar = $( ".scroll-bar" ).slider({
         slide: function( event, ui ) {
         if ( scrollContent.width() > scrollPane.width() ) {
         scrollContent.css( "margin-left", Math.round(
         ui.value / 100 * ( scrollPane.width() - scrollContent.width() )
         ) + "px" );
         //alert(Math.round(
         //ui.value / 100 * ( scrollPane.width() - scrollContent.width())));
         } else {
         scrollContent.css( "margin-left", 0 );
         }
         }
          });
          //рассчитывается ширина ползунка слдайдера в зависимости от ширины
          //блока с классом scroll-content
          //append icon to handle
          let handleHelper = scrollbar.find( ".ui-slider-handle" )
          .mousedown(function() {
         scrollbar.width( handleHelper.width() );
          })
          .mouseup(function() {
         scrollbar.width( "100%" );
          })
          .append( "<span class='ui-icon ui-icon-grip-dotted-vertical'></span>" )
          .wrap( "<div class='ui-handle-helper-parent'></div>" ).parent();

          //change overflow to hidden now that slider handles the scrolling
          scrollPane.css( "overflow", "hidden" );

          //size scrollbar and handle proportionally to scroll distance
          function sizeScrollbar() {
            let remainder = scrollContent.width() - scrollPane.width();
            let proportion = remainder / scrollContent.width();
            let handleSize = scrollPane.width() - ( proportion * scrollPane.width() );
         scrollbar.find( ".ui-slider-handle" ).css({
         width: handleSize,
         "margin-left": -handleSize / 2
         });
         handleHelper.width( "" ).width( scrollbar.width() - handleSize );
          }


          //init scrollbar size
          setTimeout( sizeScrollbar, 10 );//safari wants a timeout
         });

$(function() {
  $('.sectionEvent').click(function() {
    let that = $(this);
    that.toggleClass('active-blue');
    that.toggleClass('blue');
    that.find('.arr-up').toggle();
    that.find('.arr-down').toggle();
    that.next('.after-blue').toggle();
  })
});

$(function() {
  $('.hide-filter').click(function() {
    event.preventDefault();
    $(this).toggle();
    $('.show-filter').toggle();
    $('.in-left-catalog').toggle('fast');
    $('.filters__bottom').toggle('fast');
    $('.left-catalog').css('width', '17%');
    $('.left-catalog').css('padding-right', '0');
    $('.right-catalog').css('width', '83%');
  });
  $('.show-filter').click(function() {
    event.preventDefault();
    $(this).toggle();
    $('.hide-filter').toggle();
    $('.in-left-catalog').toggle('fast');
      $('.filters__bottom').toggle('fast');
    $('.left-catalog').css('width', '28%');
    $('.right-catalog').css('width', '70%');
    $('.left-catalog').css('padding-right', '2%');
  })
});




  $(function() {
    $('.blue-menu').click(function() {
      $('.blue-menu-div').animate({"margin-left": "0px"}, 300);
      $('.podlozhka').toggle(0);
      $('.cls-blue-menu').css('display', 'inline-block');
      $('.blue-menu').css('display', 'none');
    });

    $('.podlozhka').click(function() {
      $('.blue-menu-div').animate({"margin-left": "-320px"}, 300);
      $('.podlozhka').toggle(0);
      $('.cls-blue-menu').css('display', 'none');
      $('.blue-menu').css('display', 'inline-block');
      $('.vou2').hide();
      $('.cls-blue-menu2').hide();
      $('.mail-div').hide();
      $('.auth-div-full').hide(0);
    });



    $('.cls-mail-div').click(function() {
      $('.podlozhka').hide(0);
      $('.mail-div').hide(0);
      $('.auth-div-full').hide(0);
      $('.popup').hide(0);
      $('body').removeClass('with--popup');
    })

    $('.cls-blue-menu').click(function() {
      $('.blue-menu-div').animate({"margin-left": "-320px"}, 300);
      $('.podlozhka').hide(0);
      $('.cls-blue-menu').css('display', 'none');
      $('.blue-menu').css('display', 'inline-block');
    });
  });

  $('.sex-span').on('click', function(e) {
    let that = $(this);
    that.parent().addClass('sex-btn--non-active');
    that.parent().siblings().addClass('sex-btn--active');
    let name = that.data('name');
    $('.sex-list').each(function(index) {
      let that = $(this);
      if (that.data('name') == name) {
        that.slideToggle();
      } else {
        that.hide();
      }
    });
  });

  $('.sex-span').on('click', function(e) {
    let that = $(this);
    that.parent().toggleClass('sex-btn--active sex-btn--non-active');
    that.parent().siblings().toggleClass('sex-btn--active sex-btn--non-active');
  });

  $('.submenu-item').click(function(e) {
    let e_target = $(e.target);
    if (e_target.is('.submenu-item')) {
      let that = $(this);
      (e_target).siblings().slideToggle();
      that.toggleClass('arrow-down');
      that.toggleClass('arrow-up');
    }
  });

  $(function() {
    $('.more-span').click(function(e) {
      let that = $(this);
      e.preventDefault();
      that.next('.blue-menu-div-div ul').toggle('300');
      that.find('span').toggleClass('open-ul');
    });

  });

  $(function() {
    let that = $(this);
    $('.one-zkz').click(function() {

      if(that.css('background-color') != 'rgb(243, 243, 243)')
      {
        //$(this).css('background-color', '#f3f3f3');
        //$(this).css('background-image', 'url("img/up-arrow.png")');
        that.css('background-position', 'calc(100% + 1px) 24px');
        that.removeClass('opn');
      }
      else{
        //$(this).css('background-color', '#fff');
        //$(this).css('background-image', 'url("img/down-arrow.png")');
        that.css('background-position', 'calc(100% + 1px) 45%');
        that.addClass('opn');
      }

      that.find('.in-one-zkz').toggle();
    });



  });

});

$(function () {
  const MAX_MOBILE_WIDTH        = 768;
  const ANIMATION_DURATION      = 300;
  const HIDE_SEARCH_BREAK_POINT = 130;

  let $menuBlock        = $('.menu');
  let $searchBlock      = $('.poisk-div');
  let $searchBlockForm  = $('.poisk-div form');

  let offsetTop         = $menuBlock.offset().top + 20;
  let searchBlockHeight = $('.poisk-div').height();
  let windowWidth       = $(window).width();

  let searchFirstActive = false;

  let scrollTop;

  $(window).on('scroll', function () {
      windowWidth          = $(window).width();
      scrollTop            = $(window).scrollTop();

      if (windowWidth < MAX_MOBILE_WIDTH) {
        if ($searchBlock.hasClass('is-fixed') && $searchBlock.hasClass('active') && scrollTop <= HIDE_SEARCH_BREAK_POINT) {
          $searchBlock.animate({'top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlock.removeClass('active');
          $('.search-suggest').empty();
          $menuBlock.animate({'top': '0px'}, ANIMATION_DURATION);
        } else if (!$searchBlock.hasClass('is-fixed') && $searchBlock.hasClass('active')) {
          $searchBlock.removeClass('active');
          $('.search-suggest').empty();
          $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
        }

        let menuScrollTop = offsetTop - scrollTop;

        if ($searchBlock.hasClass('active')) {
          menuScrollTop = menuScrollTop + searchBlockHeight;
          searchFirstActive = true;
        } else if (searchFirstActive) {
          menuScrollTop = menuScrollTop - searchBlockHeight;
          searchFirstActive = false;
        }

        if (menuScrollTop <= 0) {
          $menuBlock.addClass('is-fixed');
        } else {
          $menuBlock.removeClass('is-fixed');
        }
      }
  });
  let currentScroll;
  if (windowWidth < MAX_MOBILE_WIDTH) {
    $('.reg, .ent, .mail.obr').on('click', function () {
        currentScroll = scrollTop;
        $(window).scrollTop(0);
    });
    $('.cls-mail-div, .podlozhka').on('click', function () {
        $(window).scrollTop(currentScroll);
    });
  }

  $('.touch-for-poisk').click(function() {
    let isFixed = $menuBlock.hasClass('is-fixed');

    $searchBlock.toggleClass('active');

    if($searchBlock.hasClass('active')) {
      if (isFixed) {
          $searchBlock.addClass('is-fixed');
          $searchBlock.css({'margin-top': 0});
          $searchBlockForm.css({'margin-top': 0});
          $searchBlock.animate({'top': '0px'}, ANIMATION_DURATION);
          $menuBlock.animate({'top': searchBlockHeight}, ANIMATION_DURATION);
      } else {
          $searchBlock.removeClass('is-fixed');
          $searchBlock.animate({'margin-top': '0px'}, ANIMATION_DURATION);
          $searchBlockForm.animate({'margin-top': '0px'}, ANIMATION_DURATION);
      }
    }
    else {
      if (isFixed) {
          $searchBlock.addClass('is-fixed');
          $searchBlock.animate({'top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $menuBlock.animate({'top': '0px'}, ANIMATION_DURATION);
      } else {
          $searchBlock.removeClass('is-fixed');
          $searchBlock.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
          $searchBlockForm.animate({'margin-top': -searchBlockHeight}, ANIMATION_DURATION);
      }
    }
  });
});

$(function() {
    $(document).on('click', '.js-filter-toggle', function() {
        let $filterToggle = $(this);
        let $filterCol = $('.js-filter-col');
        $filterToggle.toggleClass('filter-toggle--hidden');
        $filterCol.toggleClass('catalog__content-col--hidden');
    });
    $(document).on('click', '.js-filter-toggle-mobile', function() {
        $('.podlozhka').show();
        $('body').css('overflow-y', 'hidden');
        $('.js-filter-mobile-close').show();
        $(".js-filter-col").removeClass("catalog__content-col--sidebar");
    });
    $(document).on('click', '.js-filter-mobile-close', function() {
        hideFilter();
        $('body').css('overflow-y', '');
    });
    $(document).on('click', '.podlozhka', function() {
        hideFilter();
        $('body').css('overflow-y', '');
    });
    function hideFilter() {
        $(".js-filter-col").addClass("catalog__content-col--sidebar");
        $('.podlozhka').hide();
        $('body').css('overflow-y', '');
    }
    $(document).on('click', '.js-view-item', function() {
        let $viewItem = $(this);
        let isCurrentActive = $viewItem.hasClass('view__item--active');
        if (isCurrentActive) {
          return;
        }
        let $viewItemBox = $viewItem.closest('.js-view');
        let $viewItems = $viewItemBox.find('.js-view-item');
        let $cards = $('.js-cards');
        let viewType = $viewItem.data('view-type');
        $viewItems.removeClass('view__item--active');
        $viewItem.addClass('view__item--active');
        if (viewType === 'big') {
            $cards.addClass('cards--big');
            $('.card__img-pic').each(function() {
                let that = $(this);
              that.attr('data-src-small', that.attr('src'));
                if (that.attr('src') != null){
                  that.attr('src', that.data('src-big'));
                } else {
                  that.attr('data-src', that.data('src-big'));
                }
            });
        } else {
            $cards.removeClass('cards--big');
            $('.card__img-pic').each(function() {
              let that = $(this);
              that.attr('data-src-big', that.attr('src'));
                if (that.attr('src') != null){
                  that.attr('src', that.data('src-small'));
                } else {
                  that.attr('data-src', that.data('src-small'));
                }
            });
        }
        saveSettingsInCookie();
    });

    $('.js-check-input').on('click' , function(){
      let that = $(this);
      let $curentInputState = that
        .find("input")
        .prop("checked");

      let $curentInput = that
        .find("input");

      if ($curentInputState == false) {
        $curentInput.prop("checked", true);
      } else {
        $curentInput.prop("checked", false);
      }
    });
});

$(document).ready(function () {
  $(document).on('click', '.heart__btn', function () {
    let button = $(this);
    button.toggleClass('active');
    if (button.hasClass('rr-heart__btn')) {
      return;
    }
    BX.ajax.post('catalog/favorites/', 'favorites=Y&ID=' + $(this).data('id'), function (response) {
      response = JSON.parse(response);
      if (response.res == 'error') {
        button.toggleClass('active');
        Popup.show('<div style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">' + response.text + '</article></div>');
      } else {
        let count = Number($('.count--heart.in-full').text());
        if (response.res == 'add') {
          $('.count--heart').text(++count);
          sendYandexMetrkiaGoal('add_favorites');
        } else {
          $('.count--heart').text(--count);
          sendYandexMetrkiaGoal('del_favorites');
        }
      }
    });
  });
  $(document).on('click', '.favorites_header', function () {
    sendYandexMetrkiaGoal('open_favorites');
  });
});

//кнопка обратного звонка Mango
$(document).on('click', '.mango-false-button', function () {
  // Функция обратного вызова при загрузке скрипта Mango
  let mangoCallback = function() {
    $('.mango-false-button').css('display', 'none');
    $('.button-widget-open').click();
    if (window.matchMedia('(max-width: 767px)').matches) {
      let mangoButtonElement = document.querySelector('.mng-wgt');
      if (mangoButtonElement) {
        let mangoButton = new StickyButton(mangoButtonElement, 4, null, true);
        mangoButton.init();
      }
    }
    window.StickyButton.update();
    if (window.matchMedia('(max-width: 767px)').matches) {
      $('.widget-wrapper__center').attr('style', 'top: 150px');
      $('.close-popup').attr('style', 'top: 175px');
      $('.currentCountry').attr('style', 'margin: 0px!important;margin-bottom: 20px !important;');
      $('.text-widget').attr('style', 'font-size: 20px!important');
      $('.title-widget').attr('style', 'font-size: 35px!important');
    }

    mangoWatchClick.disconnect();
  };
  // Следим за кликом и загрузкой кнопки обратного звонка
  let mangoWatchClick = new MutationObserver(mangoCallback);
  mangoWatchClick.observe(document.getElementById('mango-callback'), {childList: true});
  // Скрипт Mango
  !function(t) {function e(){i=document.querySelectorAll(".button-widget-open");for(var e=0;e<i.length;e++) "true"!=i[e].getAttribute("init")&&(options=JSON.parse(i[e].closest('.'+t).getAttribute("data-settings")),i[e].setAttribute("onclick","alert('"+options.errorMessage+"(0000)'); return false;"))}function o(t,e,o,n,i,r){var s=document.createElement(t);for(var a in e)s.setAttribute(a,e[a]);s.readyState?s.onreadystatechange=o:(s.onload=n,s.onerror=i),r(s)}function n(){for(var t=0;t<i.length;t++){var e=i[t];if("true"!=e.getAttribute("init")){options=JSON.parse(e.getAttribute("data-settings"));var o=new MangoWidget({host:window.location.protocol+'//'+options.host,id:options.id,elem:e,message:options.errorMessage});o.initWidget(),e.setAttribute("init","true"),i[t].setAttribute("onclick","")}}}host=window.location.protocol+"//widgets.mango-office.ru/";var i=document.getElementsByClassName(t);o("link",{rel:"stylesheet",type:"text/css",href:host+"css/widget-button.css"},function(){},function(){},e,function(t){var headTag=document.querySelector('head');headTag.insertBefore(t,headTag.firstChild)}),o("script",{type:"text/javascript",src:host+"widgets/mango-callback.js"},function(){("complete"==this.readyState||"loaded"==this.readyState)&&n()},n,e,function(t){document.documentElement.appendChild(t);})}("mango-callback");
});

//Прикрепление маски на поле ввода телефона
function phoneMaskCreate(phoneInput) {
    phoneInput.mask('+7 (999) 999-99-99', {autoclear: false
    }).click(function() {
        let that = $(this);
        if (that.val() == '+7 (___) ___-__-__') {
            that[0].selectionStart = 4;
            that[0].selectionEnd = 4;
        }
    }).mouseover(function () {
        $(this).attr('placeholder', '+7 (___) ___-__-__');
    }).mouseout(function () {
        $(this).attr('placeholder', '*Телефон');
    }).keydown(function (e) {
      let that = $(this);
      if(that.val() == '+7 (___) ___-__-__'){
        if(e.key == 8 || e.key == 7){
          that.val('+7 (___) ___-__-__');
          that[0].selectionStart = 4;
          that[0].selectionEnd = 4;
          e.preventDefault();
          e.stopPropagation();
        }
      }
    }).on('input keyup', function (e) {
      let that = $(this);
      if( String(Number(that.val().replace(/\D+/g,""))).substr(0, 2) == '77' ||
          String(Number(that.val().replace(/\D+/g,""))).substr(0, 2) == '78' ||
          that.val().indexOf('+7 (8') + 1 ||
          that.val().indexOf('+7 (7') + 1 ||
          (that.val().indexOf('+7 ' == -1) && (that.val()[0] == 8 || that.val()[0] == 7))){
            that.val('+7 (___) ___-__-__');
            that.mask('+7 (999) 999-99-99', {autoclear: false});
            that.val('+7 (___) ___-__-__');
            that[0].selectionStart = 4;
            that[0].selectionEnd = 4;
            e.preventDefault();
            e.stopPropagation();
        }
    });
};

//Retail Rocket
// $('.form--subscribe').submit(function (e) {
//     if ($('#email').prop("checked")) {
//         let email = $(this).data('user-email');
//         (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
//             rrApi.setEmail(
//                 email,
//                 {
//                     "stockId": userShowcase
//                 }
//             );
//         });
//     }
// });

function rrAddToFavourite(productId) {
  let elem = document.querySelector('div[data-offer-id="' + productId + '"]');
  let realProdId = elem.getAttribute('data-group-id');
  BX.ajax.post('catalog/favorites/', 'favorites=Y&ID=' + realProdId, function (response) {
    response = JSON.parse(response);
    if (response.res == 'error') {
      Popup.show('<div style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">' + response.text + '</article></div>');
    } else{
      let count = Number($('.count--heart.in-full').text());
      if (response.res == 'add') {
        $('.count--heart').text(++count);
        sendYandexMetrkiaGoal('add_favorites');
      } else {
        $('.count--heart').text(--count);
        sendYandexMetrkiaGoal('del_favorites');
      }
    }
  });
};

function rrAddToBasket(productId) {
  let data = {
    action: "basketAdd",
    offerId: productId,
    isLocal: 'Y',
    quantity: 1,
  };
  $.ajax({
    method: "POST",
    url: "/cart/",
    data: data,
    dataType: "json",
    success: function (data) {
      if (data.status == "ok") {
        fbq('track', 'AddToCart');
        updateSmallBasket(data.text);
        let _rutarget = window._rutarget || [];
        _rutarget.push({'event': 'addToCart'});
      } else {
        console.log('Ошибка добавления в корзину RR');
      }
    },
    error: function (data) {
      console.log('Ошибка добавления в корзину RR');
    }
  });
  sendYandexMetrkiaGoal('click_product_cart');
};
