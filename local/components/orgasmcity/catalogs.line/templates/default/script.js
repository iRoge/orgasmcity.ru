$(document).ready(function() {
    $('.js-catalog-list-slider').slick({
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
        infinite: true,
        responsive: [
            // {
            //     breakpoint: 1500,
            //     settings: {
            //         slidesToShow: 10,
            //         slidesToScroll : 10,
            //     }
            // },
            {
                breakpoint: 1440,
                settings: {
                    slidesToShow: 9,
                    slidesToScroll : 9,
                }
            },
            {
                breakpoint: 1350,
                settings: {
                    slidesToShow: 8,
                    slidesToScroll : 8,
                }
            },
            {
                breakpoint: 1150,
                settings: {
                    slidesToShow: 7,
                    slidesToScroll : 7,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll : 6,
                }
            },
            {
                breakpoint: 750,
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
                breakpoint: 450,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
                }
            },
            {
                breakpoint: 300,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll : 2,
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