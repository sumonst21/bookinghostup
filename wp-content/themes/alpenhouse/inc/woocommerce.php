<?php
function alpenhouse_woocommerce_setup() {
    add_theme_support( 'woocommerce' , array(
        'single_image_width' => '405',
        'thumbnail_image_width' => '270',
    ));
    update_option('woocommerce_thumbnail_cropping',
        apply_filters('alpenhouse_woocommerce_thumbnail_cropping', '1:1.3')
    );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

}
add_action( 'after_setup_theme', 'alpenhouse_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function alpenhouse_woocommerce_scripts() {
    wp_enqueue_style( 'alpenhouse-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', array(), alpenhouse_get_theme_version());

    $font_path   = WC()->plugin_url() . '/assets/fonts/';

    $inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

    wp_add_inline_style( 'alpenhouse-woocommerce', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'alpenhouse_woocommerce_scripts');


function alpenhouse_woocommerce_sidebar(){

    register_sidebar(array(
        'name' => esc_html__('WooCommerce Sidebar', 'alpenhouse'),
        'id' => 'woo-sidebar',
        'description' => esc_html__('Sidebar for WooCommerce pages.', 'alpenhouse'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action( 'widgets_init', 'alpenhouse_woocommerce_sidebar' );
/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function alpenhouse_woocommerce_active_body_class( $classes ) {
    $classes[] = 'woocommerce-active';

    return $classes;
}
add_filter( 'body_class', 'alpenhouse_woocommerce_active_body_class' );

/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function alpenhouse_woocommerce_thumbnail_columns() {
    return 3;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'alpenhouse_woocommerce_thumbnail_columns' );

/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */
function alpenhouse_woocommerce_loop_columns() {
    if(get_theme_mod('alpenhouse_shop_type', ALPENHOUSE_SHOP_LAYOUT_1)==ALPENHOUSE_SHOP_LAYOUT_1 &&
        is_active_sidebar('woo-sidebar')){
        return 3;
    }else{
        return 5;
    }
}
add_filter( 'loop_shop_columns', 'alpenhouse_woocommerce_loop_columns' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function alpenhouse_woocommerce_related_products_args( $args ) {
    $defaults = array(
        'posts_per_page' => 3,
        'columns'        => 3,
    );

    $args = wp_parse_args( $defaults, $args );

    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'alpenhouse_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'alpenhouse_woocommerce_wrapper_before' ) ) {
    /**
     * Before Content.
     *
     * Wraps all WooCommerce content in wrappers which match the theme markup.
     *
     * @return void
     */
    function alpenhouse_woocommerce_wrapper_before() {
        ?>
        <div id="primary" class="content-area <?php echo apply_filters('alpenhouse_shop_loop_wrapper_classes', '')?>">
        <main id="main" class="site-main" role="main">
        <?php
    }
}
add_action( 'woocommerce_before_main_content', 'alpenhouse_woocommerce_wrapper_before' );

if ( ! function_exists( 'alpenhouse_woocommerce_wrapper_after' ) ) {
    /**
     * After Content.
     *
     * Closes the wrapping divs.
     *
     * @return void
     */
    function alpenhouse_woocommerce_wrapper_after() {
        ?>
        </main><!-- #main -->
        </div><!-- #primary -->
        <?php
    }
}
add_action( 'woocommerce_after_main_content', 'alpenhouse_woocommerce_wrapper_after' );

remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20);

add_filter( 'woocommerce_show_page_title' , 'woo_hide_page_title' );
/**
 * woo_hide_page_title
 *
 * Removes the "shop" title on the main shop page
 *
 */
function woo_hide_page_title() {

    return false;

}

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 30 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 20 );

add_action('woocommerce_before_shop_loop', 'alpenhouse_before_ordering_wrapper', 15);
function alpenhouse_before_ordering_wrapper(){
    ?>
    <div class="woo-ordering-wrapper">
    <?php
}
add_action('woocommerce_before_shop_loop', 'alpenhouse_after_ordering_wrapper', 35);
function alpenhouse_after_ordering_wrapper(){
    ?>
    </div>
    <?php
}

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 12 );

add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 30 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 20 );
add_action('woocommerce_after_shop_loop', 'alpenhouse_before_ordering_wrapper', 15);
add_action('woocommerce_before_shop_loop', 'alpenhouse_after_ordering_wrapper', 35);



add_action('woocommerce_before_shop_loop_item', 'alpenhouse_before_shop_loop_item', 5);
function alpenhouse_before_shop_loop_item(){
    ?>
    <div class="product-wrapper">
    <?php
}

add_action('woocommerce_shop_loop_item_title', 'alpenhouse_before_shop_item_title', 5);

function alpenhouse_before_shop_item_title(){
    ?>
    <div class="product-title-wrapper">
    <?php
}

add_action('woocommerce_after_shop_loop_item_title', 'alpenhouse_after_shop_loop_item_title', 2);

function alpenhouse_after_shop_loop_item_title(){
    ?>
    </div>
    <?php
}

add_action('woocommerce_after_shop_loop_item', 'alpenhouse_before_loop_add_to_cart', 7);

function alpenhouse_before_loop_add_to_cart(){
    ?>

    <div class="add-to-cart-wrapper">
    <?php
}




add_action('woocommerce_after_shop_loop_item', 'alpenhouse_after_shop_item_title', 15);

function alpenhouse_after_shop_item_title(){

    ?>
    </div>
    </div>
    <?php
}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15);

add_action( 'woocommerce_before_single_product_summary', 'alpenhouse_woo_single_product_wrapper_before', 5 );
function alpenhouse_woo_single_product_wrapper_before(){
    ?>
    <div class="single-product-summary clear">
    <?php
}

add_action( 'woocommerce_after_single_product_summary', 'alpenhouse_woo_single_product_wrapper_after', 5);
function alpenhouse_woo_single_product_wrapper_after(){
    ?>
    </div>
    <?php
}

add_filter('woocommerce_product_tag_cloud_widget_args', 'alpenhouse_woo_tag_cloud');

function alpenhouse_woo_tag_cloud($args){
    $args['largest'] = 0.75;
    $args['smallest'] = 0.75;
    $args['unit'] = 'rem';
    return $args;
}


if (!function_exists('alpenhouse_header_cart')) {
    /**
     * Display Header Cart
     *
     * @since  1.0.0
     * @return void
     */
    function alpenhouse_header_cart()
    {
        if(!get_theme_mod('alpenhouse_display_header_cart','1')){
            return;
        }

        if (class_exists("WooCommerce")) {

            if (is_cart()) {
                $class = 'current-menu-item';
            } else {
                $class = '';
            }
            ?>
            <div class="woo-header-cart-wrapper">
                <ul id="site-header-cart" class="site-header-cart menu">
                    <li class="<?php echo esc_attr($class); ?>">
                        <?php alpenhouse_cart_link();?>
                    </li>
                    <li class="cart-widget">
                        <?php the_widget('WC_Widget_Cart', 'title='); ?>
                    </li>
                </ul>
            </div>
            <?php
        }
    }
}

/**
 * Add cart link if WooCommerce is active
 */

if(class_exists('WooCommerce')){
    add_action('alpenhouse_woo_mini_cart', 'alpenhouse_header_cart');
}

if (!function_exists('alpenhouse_cart_link')) {
    /**
     * Cart Link
     * Displayed a link to the cart including the number of items present and the cart total
     *
     * @return void
     * @since  1.0.0
     */
    function alpenhouse_cart_link()
    {
        $class = 'show-mini-cart';
        if (WC()->cart->get_cart_contents_count() === 0) {
            $class .= ' empty-cart';
        }

        $item_count_text = sprintf(
        /* translators: number of items in the mini cart. */
            _n( '%d items', '%d items', WC()->cart->get_cart_contents_count(), 'alpenhouse' ),
            WC()->cart->get_cart_contents_count()
        );
        ?>
        <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url(wc_get_cart_url());
			?>" title="<?php esc_attr_e('View your shopping cart', 'alpenhouse'); ?>">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
            <span class="products-count"><?php echo $item_count_text; ?></span>
        </a>
        <?php
    }
}

if (!function_exists('alpenhouse_cart_link_fragment')) {
    /**
     * Cart Fragments
     * Ensure cart contents update when products are added to the cart via AJAX
     *
     * @param  array $fragments Fragments to refresh via AJAX.
     * @return array            Fragments to refresh via AJAX
     */
    function alpenhouse_cart_link_fragment($fragments)
    {
        global $woocommerce;
        ob_start();
        alpenhouse_cart_link();
        $fragments['a.show-mini-cart'] = ob_get_clean();
        return $fragments;
    }
}
/**
 * Cart fragment
 *
 */
if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.3', '>=')) {
    add_filter('woocommerce_add_to_cart_fragments', 'alpenhouse_cart_link_fragment');
} else {
    add_filter('add_to_cart_fragments', 'alpenhouse_cart_link_fragment');
}

add_filter('alpenhouse_shop_loop_wrapper_classes', 'alpenhouse_shop_loop_wrapper_classes_filter');
if(!function_exists('alpenhouse_shop_loop_wrapper_classes_filter')){
    function alpenhouse_shop_loop_wrapper_classes_filter($classes){

        if(function_exists('is_woocommerce') && is_woocommerce()){

            if( (is_product_taxonomy() || is_shop())
                && (get_theme_mod('alpenhouse_shop_type', ALPENHOUSE_SHOP_LAYOUT_1) == ALPENHOUSE_SHOP_LAYOUT_2 || !is_active_sidebar('woo-sidebar'))){
                $classes .= ' no-sidebar';
            }
            if(is_product() &&
                (get_theme_mod('alpenhouse_single_product_layout', ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_1) == ALPENHOUSE_SINGLE_PRODUCT_LAYOUT_2 || !is_active_sidebar('woo-sidebar'))){
                $classes .= ' boxed';
            }
        }
        return $classes;
    }
}


add_action( 'woocommerce_product_query', 'alpenhouse_woocommerce_product_query' );
function alpenhouse_woocommerce_product_query( $query ) {
    if ( $query->is_main_query() && ( $query->get( 'wc_query' ) === 'product_query' ) ) {
        $products = get_theme_mod('alpenhouse_shop_products_per_page', 9);

        $query->set( 'posts_per_page', $products);
    }
}
