(function ($) {


    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetTestimonialsCarousel = function( $scope, $ ) {
        $scope.find('.testimonials-carousel').each(function (e) {
            var autoplay = $(this).attr('data-autoplay')==='yes',
                slidesMobile = parseInt($(this).attr('data-items-to-show-mobile')),
                slidesTablet = parseInt($(this).attr('data-items-to-show-tablet')),
                slides = parseInt($(this).attr('data-items-to-show-desktop')),
                speed = $(this).attr('data-slide-speed');
            $(this).slick({
                dots: true,
                infinite: true,
                speed: speed,
                autoplay: autoplay,
                arrows: false,
                mobileFirst: true,
                rows: 0,
                responsive:[{
                    breakpoint: 991,
                    settings:{
                        slidesToShow: slides
                    }
                },{
                    breakpoint: 767,
                    settings:{
                        slidesToShow: slidesTablet
                    }
                },{
                    breakpoint: 0,
                    settings:{
                        slidesToShow: slidesMobile
                    }
                }]
            })
        });
    };

    // Make sure you run this code under Elementor.
    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/testimonials-carousel.default', WidgetTestimonialsCarousel );
    } );
})(jQuery);