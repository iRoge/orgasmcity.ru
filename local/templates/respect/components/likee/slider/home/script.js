(function($) {
    $(function() {


        if(device.mobile() !== true) {
            $('.slides-item__video').on('click', function () {
                stopVideo($(this));
            });

            $('.slides-item__video-play').on('click', function () {
                playVideo($(this).prev('video'));
            });


            $('[data-play="yes"]').each(function () {
                var videoBlock = $(this);
                var iconBlock = videoBlock.next('.play-ico');
                this.play();
                playVideo(videoBlock);
                iconBlock.addClass('stop');
                videoBlock.addClass('play');

            });
        }
        else {
            $('.comp-ver').fadeOut();
        }


        $('.slider-one').each(function(){
            var slideBlock = $(this);
            var mobBg = slideBlock.data('mob-bg');
            var mobLink = slideBlock.data('mob-link');
            var slideImg = slideBlock.children('img');
            var mobImg = slideImg.data('mob-img');

            if(device.mobile() === true) {
                if(mobLink != ''){
                    slideBlock.attr('href', mobLink);
                }
                if(mobBg != ''){
                    slideBlock.css('background-image', mobBg);
                }
                if(mobImg != ''){
                    slideImg.attr('src', mobImg);
                }
            }
        });

        $('a.slides-item[data-title]').on('click', function () {
            var slideTitle = $(this).attr('data-title');

            if ('undefined' != typeof window.yaCounter18950356) {
                yaCounter18950356.reachGoal('main_banner', {title: slideTitle});
            }
        });
    });

    function playVideo($video)
    {
        $video[0].play();
        $video.next('[data-videoicon]').addClass('stop');
    }
    function stopVideo($video)
    {
        $video[0].pause();
        $video.next('[data-videoicon]').removeClass('stop');
    }

})(jQuery);
