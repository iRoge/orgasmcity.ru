$(document).ready(function() {
    $('#recommendeds-slider').slick({
        arrows: true,
        mobileFirst: true,
        init: false,
        updateOnWindowResize: true,
        watchOverflow: true,
        observer: true,
        spaceBetween: 0,
        lazy: true,
        preloadImages: true,
        touchReleaseOnEdges: true,
        watchSlidesVisibility: true,
        loop: true,
        autoplay: false,
        speed: 800,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll : 5,
                    infinite: true
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll : 3,
                    infinite: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
                    infinite: true
                }
            },
            {
                breakpoint: 400,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll : 1,
                    infinite: true
                }
            },
            {
                breakpoint: 1,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll : 1,
                    infinite: true
                }
            },
        ]
    });
});