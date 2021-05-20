<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package alpenhouse
 */

if ( ! is_active_sidebar( 'sidebar-1' ) && ! is_active_sidebar('woo-sidebar') ) {
	return;
}



if ( class_exists( 'WooCommerce' ) ) {
    if(is_woocommerce() && is_archive() &&
        (get_theme_mod('alpenhouse_shop_type') == ALPENHOUSE_SHOP_LAYOUT_2 || ! is_active_sidebar('woo-sidebar'))){
        return;
    }
    if(is_product() &&
        (get_theme_mod('alpenhouse_single_product_layout') == ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_2 || ! is_active_sidebar('woo-sidebar'))){
        return;
    }
}

if(is_home()){
    if(get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_2 ||
        get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1) == ALPENHOUSE_BLOG_LAYOUT_4) {
        return;
    }
}

if(is_archive() && 'cptp-portfolio' == get_post_type()){
    if(alpenhouse_get_portfolio_columns() != 2){
        return;
    }
}

?>

<aside id="secondary" class="widget-area">
	<?php
        if ( function_exists('is_woocommerce') && is_woocommerce() ) {
            dynamic_sidebar( 'woo-sidebar' );
        }  else {
            dynamic_sidebar( 'sidebar-1' );
        }
    ?>
</aside><!-- #secondary -->
