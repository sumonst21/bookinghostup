<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package alpenhouse
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function alpenhouse_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}
	return array_unique($classes);
}
add_filter( 'body_class', 'alpenhouse_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function alpenhouse_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'alpenhouse_pingback_header' );


function alpenhouse_search_form( $form ) {
    $form = '<form role="search" method="get" class="searchform" action="' . home_url( '/' ) . '" >
        <div class="search-controls"><label class="screen-reader-text" for="s">' . __( 'Search for:', 'alpenhouse') . '</label>
        <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="'. __( 'Search &hellip;', 'alpenhouse' ) . '"/>
        <button type="submit" id="searchsubmit"><i class="fa fa-search"></i> </button>
        </div>
        </form>';

	return $form;
}

add_filter( 'get_search_form', 'alpenhouse_search_form' );


add_action('alpenhouse_before_blog_loop', 'alpenhouse_before_blog_loop_action');
if(!function_exists('alpenhouse_before_blog_loop_action')){
    function alpenhouse_before_blog_loop_action(){
        if(alpenhouse_is_masonry_blog()){
            ?>
            <div id="masonry-blog" class="<?php echo get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1);?>">
            <?php
        }
    }
}

add_action('alpenhouse_after_blog_loop', 'alpenhouse_after_blog_loop_action');
if(!function_exists('alpenhouse_after_blog_loop_action')){
    function alpenhouse_after_blog_loop_action(){
        if(alpenhouse_is_masonry_blog()){
            ?>
                <div class="masonry-blog-spacer"></div>
            </div>
            <?php
        }
    }
}

add_action('alpenhouse_before_blog_loop_item','alpenhouse_before_blog_loop_item_action');
if(!function_exists('alpenhouse_before_blog_loop_item_action')){
    function alpenhouse_before_blog_loop_item_action(){
        if(alpenhouse_is_masonry_blog()){
            ?>
            <div class="masonry-blog-item <?php echo get_theme_mod('alpenhouse_blog_type', ALPENHOUSE_BLOG_LAYOUT_1);?>-item">
            <?php
        }
    }
}

add_action('alpenhouse_after_blog_loop_item','alpenhouse_after_blog_loop_item_action');
if(!function_exists('alpenhouse_after_blog_loop_item_action')){
    function alpenhouse_after_blog_loop_item_action(){
        if(alpenhouse_is_masonry_blog()){
            ?>
            </div>
            <?php
        }
    }
}




add_action('alpenhouse_error_page_content', 'alpenhouse_error_page_content_action');
if(!function_exists('alpenhouse_error_page_content_action')){
    function alpenhouse_error_page_content_action(){
    ?>
            <div class="custom-header-content">
                <h1>
                    404
                </h1>
                <h2>
                    <?php
                        echo esc_html__('Sorry, page not found!','alpenhouse');
                    ?>
                </h2>
                <div class="error-page-search">
                    <?php
                        get_search_form();
                    ?>
                </div>
            </div>
    <?php
    }
}


if(!function_exists('alpenhouse_get_portfolio_columns')){
    function alpenhouse_get_portfolio_columns(){
        return get_theme_mod('alpenhouse_portfolio_columns', 2);
    }
}

add_action('alpenhouse_before_portfolio_loop', 'alpenhouse_before_portfolio_loop_action');
if(!function_exists('alpenhouse_before_portfolio_loop_action')){
    function alpenhouse_before_portfolio_loop_action(){

        ?>
        <div class="portfolio-wrapper columns-<?php echo esc_attr(alpenhouse_get_portfolio_columns());?>">
        <?php
    }
}

add_action('alpenhouse_after_portfolio_loop', 'alpenhouse_after_portfolio_loop_action');
if(!function_exists('alpenhouse_after_portfolio_loop_action')){
    function alpenhouse_after_portfolio_loop_action(){
        ?>
        </div>
        <?php
    }
}

add_action('alpenhouse_before_testimonials_loop', 'alpenhouse_before_testimonials_loop_action');
if(!function_exists('alpenhouse_before_testimonials_loop_action')){
    function alpenhouse_before_testimonials_loop_action(){

        ?>
        <div class="testimonials-wrapper columns-1">
        <?php
    }
}

add_action('alpenhouse_after_testimonials_loop', 'alpenhouse_after_testimonials_loop_action');
if(!function_exists('alpenhouse_after_testimonials_loop_action')){
    function alpenhouse_after_testimonials_loop_action(){
        ?>
        </div>
        <?php
    }
}


