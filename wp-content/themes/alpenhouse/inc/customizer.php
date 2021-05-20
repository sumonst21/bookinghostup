<?php
/**
 * alpenhouse Theme Customizer
 *
 * @package alpenhouse
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function alpenhouse_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'alpenhouse_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'alpenhouse_customize_partial_blogdescription',
		) );
	}
}
add_action( 'customize_register', 'alpenhouse_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function alpenhouse_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function alpenhouse_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function alpenhouse_customize_preview_js() {
	wp_enqueue_script( 'alpenhouse-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), alpenhouse_get_theme_version(), true );
}
add_action( 'customize_preview_init', 'alpenhouse_customize_preview_js' );

/**
 * Adds custom panels to customizer.
 */

function alpenhouse_customizer_add_settings($wp_customize)
{

    /**
     * Header settings
     */
    $wp_customize->add_section('alpenhouse_header', array(
        'title' => __('Header Options', 'alpenhouse'),
        'priority' => 30,
    ));
    $wp_customize->add_setting('alpenhouse_display_header_search', array(
        'default' => '1',
        'sanitize_callback' => 'alpenhouse_sanitize_checkbox'
    ));
    $wp_customize->add_control('alpenhouse_display_header_search', array(
        'label' => __('Display site search form in header.', 'alpenhouse'),
        'section' => 'alpenhouse_header',
        'type' => 'checkbox'
    ));

    if ( class_exists( 'WooCommerce' ) ) {
        $wp_customize->add_setting('alpenhouse_display_header_cart', array(
            'default' => '1',
            'sanitize_callback' => 'alpenhouse_sanitize_checkbox'
        ));
        $wp_customize->add_control('alpenhouse_display_header_cart', array(
            'label' => __('Display WooCommerce cart in header.', 'alpenhouse'),
            'section' => 'alpenhouse_header',
            'type' => 'checkbox'
        ));
    }


    /**
     * Blog settings
     */
    $wp_customize->add_section( 'alpenhouse_blog' , array(
        'title'      => __( 'Blog Options', 'alpenhouse' ),
        'priority'   => 31,
    ) );
    $wp_customize->add_setting('alpenhouse_blog_type', array(
        'default' => ALPENHOUSE_BLOG_LAYOUT_1,
        'transport' => 'refresh',
        'type' => 'theme_mod',
        'sanitize_callback' => 'alpenhouse_sanitize_select'

    ));
    $wp_customize->add_control('alpenhouse_blog_type', array(
        'label' => __('Blog Layout', 'alpenhouse' ),
        'type'      => 'select',
        'section'   => 'alpenhouse_blog',
        'choices'   => array(
            ALPENHOUSE_BLOG_LAYOUT_1 => __('Classic with sidebar', 'alpenhouse'),
            ALPENHOUSE_BLOG_LAYOUT_2 => __('Classic no sidebar', 'alpenhouse'),
            ALPENHOUSE_BLOG_LAYOUT_3 => __('Masonry 2 columns', 'alpenhouse'),
            ALPENHOUSE_BLOG_LAYOUT_4 => __('Masonry 3 columns', 'alpenhouse'),
        ),
        'settings'  => 'alpenhouse_blog_type'

    ));
    /**
     * Portfolio settings
     */
    $wp_customize->add_section( 'alpenhouse_portfolio' , array(
        'title'      => __( 'Gallery Options', 'alpenhouse' ),
        'priority'   => 31,
    ) );
    $wp_customize->add_setting('alpenhouse_portfolio_columns', array(
        'default' => 2,
        'transport' => 'refresh',
        'type' => 'theme_mod',
        'sanitize_callback' => 'alpenhouse_sanitize_select'

    ));
    $wp_customize->add_control('alpenhouse_portfolio_columns', array(
        'label' => __('Gallery Layout', 'alpenhouse' ),
        'type'      => 'select',
        'section'   => 'alpenhouse_portfolio',
        'choices'   => array(
            2 => __('Default', 'alpenhouse'),
            3 => __('3 columns', 'alpenhouse'),
            4 => __('4 columns', 'alpenhouse'),
        ),
        'settings'  => 'alpenhouse_portfolio_columns'

    ));

    /**
     * Booking settings
     */
    if(class_exists('HotelBookingPlugin')) {

        $wp_customize->add_section('alpenhouse_booking', array(
            'title' => __('Booking Options', 'alpenhouse'),
            'priority' => 31,
        ));
        $wp_customize->add_setting('alpenhouse_accommodation_list_layout', array(
            'default' => ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_1,
            'transport' => 'refresh',
            'type' => 'theme_mod',
            'sanitize_callback' => 'alpenhouse_sanitize_select'

        ));
        $wp_customize->add_control('alpenhouse_accommodation_list_layout', array(
            'label' => __('Accommodations Layout', 'alpenhouse'),
            'type' => 'select',
            'section' => 'alpenhouse_booking',
            'choices' => array(
                ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_1 => __('Default', 'alpenhouse'),
                ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_2 => __('Grid', 'alpenhouse'),
                ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_3 => __('List', 'alpenhouse'),
            ),
            'settings' => 'alpenhouse_accommodation_list_layout'

        ));
    }

    /**
     *  404 Page settings
     */
    $wp_customize->add_section( 'alpenhouse_not_found' , array(
        'title'      => __( '404 Page', 'alpenhouse' ),
        'priority'   => 35,
    ) );
    $wp_customize->add_setting('alpenhouse_not_found_image', array(
        'default' => get_template_directory_uri().'/img/404_header.jpg',
        'type' => 'theme_mod',
        'transport' => 'refresh',
        'sanitize_callback' => 'alpenhouse_sanitize_image'
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize,
            'alpenhouse_not_found_image',
            array(
                'label'      => __( 'Background Image', 'alpenhouse' ),
                'section'    => 'alpenhouse_not_found',
                'settings'   => 'alpenhouse_not_found_image',
            )
        )
    );
    /**
     * Check if WooCommerce is active
     **/
    if ( class_exists( 'WooCommerce' ) ) {
        $wp_customize->add_panel('alpenhouse_shop_panel',array(
            'title'      => __('WooCommerce Options', 'alpenhouse'),
            'priority'   => 32
        ));
        $wp_customize->add_section('alpenhouse_shop', array(
            'title'     => __('Shop Layout', 'alpenhouse'),
            'priority'  => 1,
            'panel'     => 'alpenhouse_shop_panel',
        ));
        $wp_customize->add_setting('alpenhouse_shop_type', array(
            'default'   => ALPENHOUSE_SHOP_LAYOUT_1,
            'transport' => 'refresh',
            'type'      => 'theme_mod',
            'sanitize_callback' => 'alpenhouse_sanitize_select'
        ));
        $wp_customize->add_control('alpenhouse_shop_type', array(
            'label'     => __('Shop Layout', 'alpenhouse'),
            'type'      => 'select',
            'section'   => 'alpenhouse_shop',
            'choices'   => array(
                ALPENHOUSE_SHOP_LAYOUT_1  => __('Classic with sidebar', 'alpenhouse'),
                ALPENHOUSE_SHOP_LAYOUT_2  => __('Without sidebar', 'alpenhouse'),
            ),
            'settings'  => 'alpenhouse_shop_type'
        ));
        $wp_customize->add_setting('alpenhouse_shop_products_per_page', array(
            'default'   => 9,
            'transport' => 'refresh',
            'type'      => 'theme_mod',
            'sanitize_callback' => 'absint'
        ));
        $wp_customize->add_control('alpenhouse_shop_products_per_page', array(
            'label'     => __('Products per page', 'alpenhouse'),
            'type'      => 'number',
            'section'   => 'alpenhouse_shop',
            'settings'  => 'alpenhouse_shop_products_per_page'
        ));



        $wp_customize->add_section('alpenhouse_single_product', array(
            'title'     => __('Single Product Layout', 'alpenhouse'),
            'priority'  => 2,
            'panel'     => 'alpenhouse_shop_panel',
        ));
        $wp_customize->add_setting('alpenhouse_single_product_layout', array(
            'default'   => ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_1,
            'transport' => 'refresh',
            'type'      => 'theme_mod',
            'sanitize_callback' => 'alpenhouse_sanitize_select'
        ));
        $wp_customize->add_control('alpenhouse_single_product_layout', array(
            'label'     => __('Single Product Layout', 'alpenhouse'),
            'type'      => 'select',
            'section'   => 'alpenhouse_single_product',
            'choices'   => array(
                ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_1 => __('Classic with sidebar', 'alpenhouse'),
                ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_2  => __('Without sidebar', 'alpenhouse'),
            ),
            'settings'  => 'alpenhouse_single_product_layout'

        ));
    }
}
add_action( 'customize_register', 'alpenhouse_customizer_add_settings' );

function alpenhouse_sanitize_checkbox( $input ){
    if ($input == 1) {
        return 1;
    } else {
        return '';
    }
}

function alpenhouse_sanitize_select( $input, $setting ){

    //input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
    $input = sanitize_key($input);

    //get the list of possible select options
    $choices = $setting->manager->get_control( $setting->id )->choices;

    //return input if valid or return default option
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

}

function alpenhouse_sanitize_image( $image, $setting ) {
    /*
     * Array of valid image file types.
     *
     * The array includes image mime types that are included in wp_get_mime_types()
     */
    $mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'bmp'          => 'image/bmp',
        'tif|tiff'     => 'image/tiff',
    );
    // Return an array with file extension and mime_type.
    $file = wp_check_filetype( $image, $mimes );
    // If $image has a valid mime_type, return it; otherwise, return the default.
    return ( $file['ext'] ? $image : $setting->default );
}
