<?php

/**
 *
 * Demo data
 *
 **/

function alpenhouse_ocdi_import_files() {
    return array(
        array(
            'import_file_name'             => 'Demo Import 1',
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'assets/demo-data/alpenhouse.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'assets/demo-data/alpenhouse-widgets.wie',
            'import_preview_image_url'     => '',
            'import_notice'                => __( 'Data import is generally not immediate and can take up to 10-15 minutes.<br/>After you import this demo, you will have to configure Google Maps API key separately.', 'alpenhouse' ),
            'preview_url'                  => 'https://themes.getmotopress.com/alpenhouse/',
        ),
    );
}
add_filter( 'pt-ocdi/import_files', 'alpenhouse_ocdi_import_files' );

function alpenhouse_ocdi_after_import_setup() {

    // Assign menus to their locations.
    $menu1 = get_term_by( 'slug', 'primary-menu', 'nav_menu' );
    $menu2 = get_term_by( 'slug', 'socials-menu', 'nav_menu' );
    $menu3 = get_term_by( 'slug', 'contacts-menu', 'nav_menu' );
    $menu4 = get_term_by( 'slug', 'footer', 'nav_menu' );


    set_theme_mod( 'nav_menu_locations', array(
            'menu-1' => $menu1->term_id,
            'menu-2' => $menu2->term_id,
            'menu-3' => $menu3->term_id,
            'menu-4' => $menu4->term_id
        )
    );

    // Assign menu to widget
    $menu5 = get_term_by( 'name', 'Widget menu', 'nav_menu' );
    $nav_menu_widget = get_option('widget_nav_menu');

    if($menu5 && $nav_menu_widget && !empty($nav_menu_widget[2])){
        $nav_menu_widget[2]['nav_menu'] = $menu5->term_id;
        update_option('widget_nav_menu', $nav_menu_widget);
    }

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home 1' );
    $blog_page_id  = get_page_by_title( 'News' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );


    // Assign Hotel Booking default pages.
    $search_results_page = get_page_by_title('Search Results');
    $booking_confirmation_page = get_page_by_title('Booking Confirmation');
    $terms_conditions_page = get_page_by_title('Terms & Conditions');
    $booking_confirmed_page = get_page_by_title('Booking Confirmed');
    $booking_cancelled_page = get_page_by_title('Booking Cancelled');

    update_option('mphb_search_results_page', $search_results_page->ID);
    update_option('mphb_checkout_page', $booking_confirmation_page->ID);
    update_option('mphb_terms_and_conditions_page', $terms_conditions_page->ID);
    update_option('mphb_booking_confirmation_page',$booking_confirmed_page->ID);
    update_option('mphb_user_cancel_redirect_page', $booking_cancelled_page->ID);


    // Disable elementor lightbox by default

    if(!get_option('elementor_global_image_lightbox')){
        update_option('elementor_global_image_lightbox', '');
    }


     // elementor_scheme_color

    $elementor_color_pallet = array (
        1 => '#344583',
        2 => '#54595f',
        3 => '#221f1f',
        4 => '#61ce70',
    );
    update_option( 'elementor_scheme_color', $elementor_color_pallet );

    /*
     * elementor_scheme_color-picker
     */
    $elementor_scheme_color_picker = array(
        '#3f7bfe',
        '#344583',
        '#221f1f',
        '#77818c',
        '#e4e4e4',
        '#eff2f6',
        '#dfeeff',
        '#ffffff'
    );
    update_option('elementor_scheme_color-picker', $elementor_scheme_color_picker);

    /*
     * elementor_container_width
     */
    update_option('elementor_container_width', 1400);

    $elementor_scheme_typography = array(
        1 => array(
            'font_family' => 'Roboto Slab',
            'font_weight' => '700'
        ),
        2 => array(
            'font_family' => 'Roboto Slab',
            'font_weight' => '400'
        ),
        3 => array(
            'font_family' => 'Noto Sans',
            'font_weight' => '400'
        ),
        4 => array(
            'font_family' => 'Noto Sans',
            'font_weight' => '400'
        ),
    );
    update_option('elementor_scheme_typography', $elementor_scheme_typography);

    //update taxonomies
    $update_taxonomies = array(
        'cptp-portfolio-category',
        'product_cat',
        'product_tag',
        'post_tag',
        'category'
    );
    foreach ($update_taxonomies as $taxonomy ) {
        alpenhouse_ocdi_update_taxonomy( $taxonomy );
    }

    // skip hotel booking wizard
    update_option( 'mphb_wizard_passed', true);

}
add_action( 'pt-ocdi/after_import', 'alpenhouse_ocdi_after_import_setup' );

// Disable generation of smaller images (thumbnails) during the content import
//add_filter( 'pt-ocdi/regenerate_thumbnails_in_content_import', '__return_false' );

// Disable the branding notice
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

function alpenhouse_ocdi_update_taxonomy( $taxonomy ) {
    $get_terms_args = array(
        'taxonomy' => $taxonomy,
        'fields' => 'ids',
        'hide_empty' => false,
    );

    $update_terms = get_terms($get_terms_args);
    if ( $taxonomy && $update_terms ) {
        wp_update_term_count_now($update_terms, $taxonomy);
    }
}

add_filter( 'pt-ocdi/importer_options', 'alpenhouse_ocdi_importer_options' );
function alpenhouse_ocdi_importer_options( $options ) {
    $options['aggressive_url_search'] = true;

    return $options;
}
