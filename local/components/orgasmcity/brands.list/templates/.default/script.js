$(document).ready(function() {
    $('#brands-list').slick({
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
                    slidesToShow: 6,
                    slidesToScroll : 6,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll : 4,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll : 3,
                }
            },
            {
                breakpoint: 400,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
                }
            },
            {
                breakpoint: 1,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
                }
            },
        ]
    });
});