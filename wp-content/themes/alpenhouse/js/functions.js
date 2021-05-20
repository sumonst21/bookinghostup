/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

(function ($) {

    "use strict";

    var body, masthead, menuToggle, siteNavigation, socialNavigation, siteHeaderMenu, resizeTimer;

    function initMainNavigation(container) {
        // Add dropdown toggle that displays child menu items.
        var dropdownToggle = $('<button />', {
            'class': 'dropdown-toggle',
            'aria-expanded': false
        });

        container.find('.menu-item-has-children > a').after(dropdownToggle);

        // Toggle buttons and submenu items with active children menu items.
        container.find('.current-menu-ancestor > button').addClass('toggled-on');
        container.find('.current-menu-ancestor > .sub-menu').addClass('toggled-on');

        // Add menu items with submenus to aria-haspopup="true".
        container.find('.menu-item-has-children').attr('aria-haspopup', 'true');

        container.find('.dropdown-toggle').click(function (e) {
            var _this = $(this);
            // screenReaderSpan = _this.find('.screen-reader-text');

            e.preventDefault();

            _this.toggleClass('toggled-on');
            _this.next('.children, .sub-menu').toggleClass('toggled-on');

            // jscs:disable
            _this.attr('aria-expanded', _this.attr('aria-expanded') === 'false' ? 'true' : 'false');
            // jscs:enable
            // screenReaderSpan.text(screenReaderSpan.text() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand);
        });
    }

    $(document).ready(function (e) {
        initMainNavigation($('.main-navigation'));
    });

    $(document).ready(function (e) {
        masthead = $('#masthead');
        menuToggle = masthead.find('.menu-toggle');
        // Return early if menuToggle is missing.
        // console.log(1);
        if (!menuToggle.length) {
            return;
        }

        // console.log(1);
        // Add an initial values for the attribute.
        menuToggle.add(siteNavigation).add(socialNavigation).attr('aria-expanded', 'false');

        menuToggle.on('click', function () {
            // console.log('test');
            $(this).add(siteHeaderMenu).toggleClass('toggled-on');
            $('.header-menu').toggleClass('menu-opened');
            //
            // // jscs:disable
            $(this).add(siteNavigation).add(socialNavigation).attr('aria-expanded', $(this).add(siteNavigation).add(socialNavigation).attr('aria-expanded') === 'false' ? 'true' : 'false');
            // // jscs:enable
        });
    })



    function subMenuPosition() {
        $('.sub-menu').each(function () {
            $(this).removeClass('toleft');
            if (($(this).parent().offset().left + $(this).parent().width() - $(window).width() + 178) > 0) {
                $(this).addClass('toleft');
            }
        });
    }



    subMenuPosition();
    /*
     *Search modal
     */

    $(window).resize(function () {
        subMenuPosition();
    });



    (function () {
        $('.alpenhouse-slider').each(function (e) {
            $(this).slick();
        })
    })();
    function initMainSlider(){
        $('.main-slider-images').slick({
            asNavFor: '.main-slider-content',
            arrows: false,
            dots: true,
            appendDots: $('#main-slider-dots'),
            loop: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            rows: 0,
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
            rows: 0,
            responsive: [
                {
                    breakpoint: 992,
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
            rows: 0,
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

    (function () {
        $(window).load(function (e) {
            if($.fn.masonry){
                $('#masonry-blog').masonry({
                    itemSelector: '.masonry-blog-item',
                    gutter: '.masonry-blog-spacer',
                });
            }

        })

    })();

    (function () {
        $(document).ready(function (e) {
            $('.slide-down a').click(function (e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $( $.attr(this, 'href') ).offset().top
                }, 700);
            });
        })

    })();


    (function () {
        $(document).ready(function (e) {
            if($.fn.magnificPopup) {
                $('.portfolio-wrapper').magnificPopup({
					delegate: 'a.portfolio-popup',
					gallery: {
					  enabled: true
					},
				});
            }
        });

    })();

    (function () {
        $(document).ready(function (e) {

            var $header = $('#masthead .site-header-wrapper.full-height'),
                $top_menus = $('#masthead .header-top-menus'),
                $main_menu = $('#masthead .header-menu'),
                $window_height = $(window).height(),
                $top_menus_height = 0,
                $main_menu_height = 0;

            if($(window).width()>991 && $top_menus.length != 0){
                $top_menus_height = $top_menus.outerHeight();
            }
            if($main_menu.length != 0){
                $main_menu_height = $main_menu.outerHeight();
            }

            if($header.length != 0){
                $header.height($window_height - $top_menus_height - $main_menu_height);
            }

        });
    })()

})
(jQuery);
