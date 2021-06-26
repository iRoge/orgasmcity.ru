$(function () {
    var slider = $('#products-slider');

    slider.on('afterChange', function (event, slick, currentSlide) {
        var $currentSlide = $(slick.$slider).find('.slick-current');

        slider.find('.js-video-youtube').each(function () {
            var video_box = $(this);

            var player = video_box.find('iframe').get(0);
            if (player) {
                player.contentWindow.postMessage(JSON.stringify({
                    'event': 'command',
                    'func': 'pauseVideo'
                }), '*');
            }
        });

        $currentSlide.find('.js-video-youtube').each(function () {
            var video_box = $(this);

            var player = video_box.find('iframe').get(0);
            if (player) {
                player.contentWindow.postMessage(JSON.stringify({
                    'event': 'command',
                    'func': 'playVideo'
                }), '*');
            }
        });
    });


	
	$('.myHTMLvideo').click(function() {
        //this.paused ? this.play() : this.pause();
        var videoBlock = $(this);
        var iconBlock = videoBlock.next('.play-ico');
        this.pause();
        iconBlock.removeClass('stop');
        videoBlock.removeClass('play');
    });



	$('.play-ico').click(function(){
	    var iconBlock = $(this);
        var videoArea = $(this).prev('.myHTMLvideo');
        var videoBlock = videoArea.get(0);
        videoBlock.play();
        iconBlock.addClass('stop');
        videoArea.addClass('play');
    });

    if(device.mobile() === true) {

        $('.comp-ver').fadeOut();
        $('.mob-ver').fadeIn();

	    $('.slider-two').each(function(){

            var slideBlock = $(this);
            var mobLink = slideBlock.data('mob-link');
            var slideImg = slideBlock.children().children('img');
            var mobImg = slideImg.data('mob-img');


            if(mobLink != ''){
                slideBlock.attr('href', mobLink);
            }
            if(mobImg != ''){
                slideImg.attr('src', mobImg);
            }
        });

    }
    else {
        $('.mob-ver').fadeOut();
    }

    if ($('img[usemap]').length) {
        $.getScript('/local/templates/respect/lib/image-map.min.js', function() {
            $('img[usemap]').imageMap();
        });
    }
});