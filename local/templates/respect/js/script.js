$(document).ready(function () {
  let regForm   = $('#reg-form-popup'),
      authForm  = $('#auth-form'),
      regInput  = $('#vkl20'),
      authInput = $('#vkl10');

  $('.reg').click(function() {
    regInput.not(':checked').prop("checked", true);
    regForm.show();
    $('.auth-div-full').toggle(0);
    $('.podlozhka').toggle(0);
  });

  $('.ent').click(function() {
    authInput.not(':checked').prop("checked", true);
    authForm.show();
    $('.auth-div-full').toggle(0);
    $('.podlozhka').toggle(0);
  });

  $('.cls-mail-div, .podlozhka').click(function() {
    regForm.hide();
    authForm.hide();
  })

  regInput.click(function() {
    authForm.hide();
    regForm.show();
  });

  authInput.click(function() {
    authForm.show();
    regForm.hide();
  });

  $('.from-ul-li').click(function(){
    $('.from-ul-li-ul').toggle(100);
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
  });

  $('.obr').click(function(){
    $('.mail-div').toggle(0);
    $('.podlozhka').toggle(0);
    $('.mail-div .popup').show(0);
  });

  $(document).ready( function() {
    $('form[name=SIMPLE_FORM_1]').submit(function () {
      if($('form[name=SIMPLE_FORM_1] .alert-content').lenght>0){
        console.info('блок с ошибками есть');
      }
    });
  });

  $('.grey-first').on('change', function(){
      let $option = $(this);
      if ($option.val() == '0') {
          $option.css('color','#b8b4b4');
      } else {
          $option.css('color','#4e4e4e');
      }
  }).change();

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

  $('.banner_item').on('click', function () {
      let bannerElem = $(this);
      let oGTMPush = {
        'event': 'MTRENDO',
        'eventCategory': 'EEC',
        'eventAction': 'view_promotion',
        'eventLabel': bannerElem.data('rblockName'),  // название баннера/акции
        'ecommerce': {
          'promoView': {
            'promotions': [{
              'name': bannerElem.data('rblockName'),  // название баннера/акции
              'id': bannerElem.data('rblockId'),   // id баннера, если есть
              'creative': bannerElem.data('prodCreative'),  // место размещения баннера
              'position': bannerElem.data('prodPosition') //позиция в блоке
            }]
          }
        }
      };
      window.dataLayer = window.dataLayer || [];
      dataLayer.push(oGTMPush);
    });

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

  $(function() {

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

  $(function() {

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

  $(function() {
    $('#jQuerySlider').slider({step:5,min:1000,max:10000,values:[3000,5000],range:true,change:function(event,ui){$('#Editbox1').val(ui.values[0]);$('#Editbox2').val(ui.values[1]);},slide:function(event,ui){$('#Editbox1').val(ui.values[0]);$('#Editbox2').val(ui.values[1]);}});$('#Editbox1').val($('#jQuerySlider').slider("values",0));$('#Editbox2').val($('#jQuerySlider').slider("values",1));$('#Editbox1').change(function(){var value1=$('#Editbox1').val();var value2=$('#Editbox2').val();if(value1<100){value1=100;$('#Editbox1').val(100)}
    if(parseInt(value1)>parseInt(value2)){
      value1=value2;$('#Editbox1').val(value1);
    }
    $('#jQuerySlider').slider("values",0,value1);});$('#Editbox2').change(function(){var value1=$('#Editbox1').val();var value2=$('#Editbox2').val();if(value2>10000){value2=10000;$('#Editbox2').val(10000)}
    if(parseInt(value1)>parseInt(value2)){
      value2=value1;$('#Editbox2').val(value2);}
    $('#jQuerySlider').slider("values",1,value2);});});

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

  function onlinePayment(elem) {
    if (!elem.hasClass('isDisabled')) {
      elem.addClass('isDisabled');
      elem.text('Перенаправление...');

      $.ajax({
        method: "POST",
        url: "/local/ajax/tinkoff_payment.php",
        data: {'orderId': elem.attr('data-order-id')},
        success: function (data) {
          elem.text('Переход');
          window.location.replace(data);
        },
        error: function (data) {
          console.log('Ошибка перехода к онлайн оплате');
        }
      });
    }
  }

  $('button.pay-button').on('click', function (){
    onlinePayment($(this));
  })

  $('.blue-menu').click(function() {
    $('body').css('overflow', 'hidden');
    $('.blue-menu-div').animate({"margin-left": "0px"}, 300);
    $('.podlozhka').fadeIn(600);
    $('.cls-blue-menu').css('display', 'inline-block');
    $('.blue-menu').css('display', 'none');
  });

  $('.podlozhka').click(function() {
    if ($('.menu-div').css('display') === 'none') {
      let menuAnimateWidth;
      let windowsWidth = $(window).width();
      if (windowsWidth > 767) {
        menuAnimateWidth = '-320px';
      } else {
        menuAnimateWidth = '-100%';
      }
      $('.blue-menu-div').animate({"margin-left": menuAnimateWidth}, 300);
      $('.podlozhka').fadeOut(600);
      $('.cls-blue-menu').css('display', 'none');
      $('.blue-menu').css('display', 'inline-block');
      $('.vou2').hide();
      $('.cls-blue-menu2').hide();
      $('.mail-div').hide();
      $('.auth-div-full').hide(0);
    }
  });
  
  $('.cls-mail-div').click(function() {
    $('.podlozhka').hide(0);
    $('.mail-div').hide(0);
    $('.auth-div-full').hide(0);
    $('.popup').hide(0);
    $('body').removeClass('with--popup');
  })

  $('.cls-blue-menu').click(function() {
    let menuAnimateWidth;
    let windowsWidth = $(window).width();
    if (windowsWidth > 767) {
      menuAnimateWidth = '-320px';
    } else {
      menuAnimateWidth = '-100%';
    }
    $('.blue-menu-div').animate({"margin-left": menuAnimateWidth}, 300);
    $('.podlozhka').fadeOut(600);
    $('.cls-blue-menu').css('display', 'none');
    $('.blue-menu').css('display', 'inline-block');
    $('body').css('overflow', 'auto');
  });

  $('.sex-span').on('click', function(e) {
    let that = $(this);
    let sections = $('.sex-span');
    let isActive = that.parent().hasClass('sex-btn--active');
    sections.each(function (index) {
      $(this).parent().removeClass('sex-btn--active');
      $(this).parent().addClass('sex-btn--non-active');
    });
    if (isActive) {
      that.parent().removeClass('sex-btn--active');
      that.parent().addClass('sex-btn--non-active');
    } else {
      that.parent().removeClass('sex-btn--non-active');
      that.parent().addClass('sex-btn--active');
    }
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
    $('.order-info-grid').click(function() {
      let that = $(this);
      let basketBlock = that.next('.order-basket-items');
      let oneZkzBlock = that.parent('.one-zkz');
      if (that.css('background-color') != 'rgb(243, 243, 243)') {
        oneZkzBlock.css('border-color', 'gray');
        that.css('background-color', '#f3f3f3');
        that.css('background-image', 'url("/img/up-arrow.png")');
        that.css('background-position', 'calc(100% - 20px) 37px');
        basketBlock.slideDown();
        that.removeClass('opn');
      } else {
        that.css('background-color', '#fff');
        that.css('background-image', 'url("/img/down-arrow.png")');
        that.css('background-position', 'calc(100% - 20px) 37px');
        basketBlock.slideUp({complete: function(){ // callback
            oneZkzBlock.css('border-color', 'rgba(200,200,200, .5)');
          }
        });
        that.addClass('opn');
      }
    });

    $('.pay-lk-button').on('click', function (event) {
      event.stopPropagation();
      onlinePayment($(this));
    })
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
        $('body').css('overflow', 'hidden');
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
    BX.ajax.post('/catalog/favorites/', 'changeFavourite=Y&ID=' + $(this).data('id'), function (response) {
      response = JSON.parse(response);
      if (response.res == 'error') {
        button.toggleClass('active');
        Popup.show('<div style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">' + response.text + '</article></div>');
      } else {
        let count = Number($('.count--heart.in-full').text());
        if (response.res == 'add') {
          $('.count--heart').text(++count);
        } else {
          $('.count--heart').text(--count);
        }
      }
    });
  });
});

//Прикрепление маски на поле ввода телефона
function phoneMaskCreate(phoneInput, needStar = true) {
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
        let phoneString = (needStar ? '*' : '') + 'Телефон';
        $(this).attr('placeholder', phoneString);
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
}
