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
        infinite: false,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll : 5,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll : 3,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
                }
            },
            {
                breakpoint: 400,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll : 1,
                }
            },
            {
                breakpoint: 1,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll : 1,
                }
            },
        ]
    });
});