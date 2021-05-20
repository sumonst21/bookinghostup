<?php
/**
 * alpenhouse functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package alpenhouse
 */

if ( ! function_exists( 'alpenhouse_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function alpenhouse_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on alpenhouse, use a find and replace
		 * to change 'alpenhouse' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'alpenhouse', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );


        set_post_thumbnail_size(768);

        add_image_size('alpenhouse-large', 2560);
		add_image_size('alpenhouse-post-thumbnail-cropped', 768, 480, true);
		add_image_size('alpenhouse-medium', 1280, 800, true);
		add_image_size('alpenhouse-booking-gallery', 146, 88, true);



        // This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'alpenhouse' ),
			'menu-2' => esc_html__( 'Header Right (Social)', 'alpenhouse' ),
			'menu-3' => esc_html__( 'Header Left', 'alpenhouse' ),
			'menu-4' => esc_html__( 'Footer Menu', 'alpenhouse' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'alpenhouse_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 50,
			'width'       => 60,
			'flex-width'  => true,
			'flex-height' => true,
		) );


		add_theme_support('cptp-testimonial');
		add_theme_support('cptp-portfolio');
		add_theme_support('cptp-activity');


        add_editor_style(array('css/editor-style.css', alpenhouse_fonts_url()));

	}
endif;
add_action( 'after_setup_theme', 'alpenhouse_setup' );

if (!function_exists('alpenhouse_theme_updater')) :
    /**
     * Theme Updater
     * Easy Digital Downloads.
     *
     * @package EDD Sample Theme
     * Action is used so that child themes can easily disable.
     */

    function alpenhouse_theme_updater()
    {
        if (current_user_can('update_themes')) {
            require get_template_directory() . '/inc/updater/theme-updater.php';
        }
    }
endif;
add_action('after_setup_theme', 'alpenhouse_theme_updater');

/**
 * Get theme version.
 *
 * @access public
 * @return string
 */
function alpenhouse_get_theme_version() {
    $theme_info = wp_get_theme( get_template() );
    return $theme_info->get('Version');
}


/**
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if (!isset($content_width)) {
    $content_width = apply_filters('alpehnouse_content_width', 768);
}
function alpenhouse_adjust_content_width() {
    global $content_width;

    if(is_page_template('template-no-sidebar.php')){
        $content_width = 1280;
    }
}
add_action( 'template_redirect', 'alpenhouse_adjust_content_width');

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function alpenhouse_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'alpenhouse' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'alpenhouse' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    register_sidebar(array(
        'name' => esc_html__('Footer 1', 'alpenhouse'),
        'id' => 'sidebar-2',
        'description' => esc_html__('Appears in the footer section of the site.', 'alpenhouse'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => esc_html__('Footer 2', 'alpenhouse'),
        'id' => 'sidebar-3',
        'description' => esc_html__('Appears in the footer section of the site.', 'alpenhouse'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => esc_html__('Footer 3', 'alpenhouse'),
        'id' => 'sidebar-4',
        'description' => esc_html__('Appears in the footer section of the site.', 'alpenhouse'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => esc_html__('Footer 4', 'alpenhouse'),
        'id' => 'sidebar-5',
        'description' => esc_html__('Appears in the footer section of the site.', 'alpenhouse'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action( 'widgets_init', 'alpenhouse_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function alpenhouse_scripts() {

    wp_enqueue_style( 'alpenhouse-fonts', alpenhouse_fonts_url(), array(), alpenhouse_get_theme_version() );

	wp_enqueue_style( 'alpenhouse', get_stylesheet_uri(), array(), alpenhouse_get_theme_version());

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.7.0' );

    // Slick
    wp_enqueue_style( 'slick', get_template_directory_uri() . '/assets/slick/slick.css', array(), '1.9.0' );
    wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/assets/slick/slick-theme.css', array(), '1.9.0' );
    wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/slick/slick.min.js', array('jquery'), '1.9.0', true );

    if( ( is_home() && alpenhouse_is_masonry_blog() )
        || is_customize_preview() ){
        wp_enqueue_script( 'masonry', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array('jquery'), '4.2.1', true );
    }

    wp_enqueue_script('alpenhouse-script', get_template_directory_uri() . '/js/theme.min.js', array('jquery', 'slick'), alpenhouse_get_theme_version(), true);


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	//doesn't work for shortcode
	//if(is_archive() && 'cptp-portfolio' == get_post_type()){
	    wp_enqueue_style('magnific-popup-style', get_template_directory_uri() . '/assets/magnific-popup/magnific-popup.css', array(), '1.1.0');
	    wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), '1.1.0', true );
    //}
}
add_action( 'wp_enqueue_scripts', 'alpenhouse_scripts' );

/**
 * Include constants
 */
require get_template_directory() . '/inc/define.php';


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

if(class_exists('HotelBookingPlugin')){
    require get_template_directory() . '/inc/mphb-functions.php';
}

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


/**
 * Load TGM plugin activation.
 */

require_once get_template_directory() . '/inc/tgmpa-init.php';


/**
 * Load demo-data import settings
 */

require_once get_template_directory() . '/inc/demo-import.php';



if (!function_exists('alpenhouse_fonts_url')) :
    /**
     * Register Google fonts for Alpenhouse.
     *
     * Create your own alpenhouse_fonts_url() function to override in a child theme.
     *
     * @return string Google fonts URL for the theme.
     */
    function alpenhouse_fonts_url()
    {
        $fonts_url = '';
        $font_families = array();

        /**
         * Translators: If there are characters in your language that are not
         * supported by Lato, translate this to 'off'. Do not translate
         * into your own language.
         */
        if ('off' !== esc_html_x('on', 'Roboto Slab font: on or off', 'alpenhouse')) {
            $font_families[] = 'Roboto Slab:300,400,700';
        }
        if ('off' !== esc_html_x('on', 'Noto Sans font: on or off', 'alpenhouse')) {
            $font_families[] = 'Noto Sans:400,700';
        }
        if ($font_families) {
            $query_args = array(
                'family' => urlencode(implode('|', $font_families)),
                'subset' => urlencode('latin,latin-ext'),
            );
            $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
        }

        return esc_url_raw($fonts_url);

        $fonts_url = '';
    }
endif;

if (!function_exists('alpenhouse_header_image')) :
    function alpenhouse_header_image() {

		$image = get_header_image();
		$post_types = apply_filters( 'alpenhouse_header_image_post_types' , array(
			'mphb_room_type','cptp-activity','post','cptp-portfolio')
		);

		if(is_page() && has_post_thumbnail()){
			$image = esc_url( get_the_post_thumbnail_url(get_the_ID(), 'alphenhouse-large') );
		}
		if(is_single() && has_post_thumbnail() && in_array(get_post_type(), $post_types )){
			$image = esc_url( get_the_post_thumbnail_url(get_the_ID(), 'alphenhouse-large') );
		}

		if ($image) {
			echo $image;
		}
    }
endif;

if (!function_exists('alpenhouse_widget_tag_cloud_args')) :
    /**
     * Modifies tag cloud widget arguments to have all tags in the widget same font size.
     *
     * @since Alpenhouse 1.0.0
     *
     * @param array $args Arguments for tag cloud widget.
     *
     * @return array A new modified arguments.
     */
    function alpenhouse_widget_tag_cloud_args($args)
    {
        $args['largest'] = 0.75;
        $args['smallest'] = 0.75;
        $args['unit'] = 'rem';

        return $args;
    }
endif;
add_filter('widget_tag_cloud_args', 'alpenhouse_widget_tag_cloud_args');



add_filter('default_page_template_title', function() {
    return __('Page with sidebar', 'alpenhouse');
});

add_action('alpenhouse_wpml_list', 'alpenhouse_wpml_list_action');
if(!function_exists('alpenhouse_wpml_list_action')){
    function alpenhouse_wpml_list_action(){
        if(!defined('ICL_SITEPRESS_VERSION')){
            return;
        }
        ?>
        <div class="wpml-list-container">
            <?php
                do_action('wpml_add_language_selector');
            ?>
        </div>
        <?php
    }
}


add_filter('alpenhouse_content_area_class', 'alpenhouse_content_area_class_filter');
if(!function_exists('alpenhouse_content_area_class_filter')){
    function alpenhouse_content_area_class_filter($classes){
        if(is_home()){
            if(get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_2){
                $classes.= 'boxed';
            }

            if(get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_4){
                $classes.= 'no-sidebar';
            }
        }

        if(is_archive() && 'cptp-portfolio' == get_post_type()){
            if(alpenhouse_get_portfolio_columns() != 2){
                $classes.= 'no-sidebar';
            }
        }

        return $classes;
    }
}

if( !function_exists('alpenhouse_is_masonry_blog')){
    function alpenhouse_is_masonry_blog(){
        if(get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_3 ||
            get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_4 ){
            return true;
        }else{
            return false;
        }
    }

}
