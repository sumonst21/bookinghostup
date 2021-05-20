(function ($) {

    function initMainSlider(){
        $('.main-slider-images').slick({
            asNavFor: '.main-slider-content',
            arrows: false,
            dots: true,
            appendDots: $('#main-slider-dots'),
            loop: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 992,
                    settings:{
                        dots: false
                    }
                }
            ]

        });
        $('.main-slider-content').slick({
            asNavFor: '.main-slider-images',
            arrows: true,
            loop: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1280,
                    settings: {
                        arrows: false
                    }
                },
            ]
        });
        $('.main-slider-images-center').slick({
            arrows: false,
            dots: true,
            appendDots: $('#main-slider-fixed-dots'),
            loop: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 992,
                    settings:{
                        dots: false
                    }
                }
            ]

        });
    }

    $(document).ready(function(){
        if($.fn.slick){
            initMainSlider();
        }
    });
})(jQuery);