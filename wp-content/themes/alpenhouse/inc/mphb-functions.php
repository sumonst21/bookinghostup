<?php

add_filter( 'mphb_single_room_type_gallery_image_size', function ($size){
    return 'alpenhouse-booking-gallery';
});

add_filter( 'mphb_loop_room_type_gallery_nav_slider_image_size', function ($size){
    return 'alpenhouse-booking-gallery';
});

add_filter( 'mphb_loop_room_type_gallery_main_slider_image_size', function ($size){
    return 'alpenhouse-medium';
});

add_filter( 'mphb_loop_room_type_thumbnail_size', function ($size){
    return 'alpenhouse-medium';
});


//add_filter( 'mphb_loop_service_thumbnail_size', function ($size){
//    return 'alpenhouse-post-thumbnail';
//});


add_filter('mphb_loop_room_type_gallery_nav_slider_columns', function (){
    return 5;
});

add_filter('mphb_single_room_type_gallery_columns', function (){
    return 5;
});

remove_action( 'mphb_sc_services_service_details', array( '\MPHB\Views\LoopServiceView', 'renderExcerpt' ), 30 );
remove_action( 'mphb_sc_services_service_details', array( '\MPHB\Views\LoopServiceView', 'renderPrice' ), 40 );

add_action( 'mphb_sc_services_service_details', array( '\MPHB\Views\LoopServiceView', 'renderExcerpt' ), 40 );
add_action( 'mphb_sc_services_service_details', array( '\MPHB\Views\LoopServiceView', 'renderPrice' ), 30 );

if ( ! function_exists( 'alpenhouse_mphb_sc_services_service_read_more' ) ) :
    /**
     * Displays read more btn mphb_sc_services_service
     */
    function alpenhouse_mphb_sc_services_service_read_more() {
        echo '<p><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" class="more-link">' . esc_html__( 'View details', 'alpenhouse' ) . '</a></p>';
    }


endif;

add_action( 'mphb_sc_services_service_details', 'alpenhouse_mphb_sc_services_service_read_more', 70 );

add_action( 'mphb_render_loop_service_before_featured_image', 'alpenhouse_mphb_render_loop_service_before_featured_image' );
function alpenhouse_mphb_render_loop_service_before_featured_image() {
    echo '<a class="post-thumbnail" href="' . esc_url( get_permalink() ) . '">';
}

add_action( 'mphb_render_loop_service_after_featured_image', 'alpenhouse_mphb_render_loop_service_after_featured_image' );
function alpenhouse_mphb_render_loop_service_after_featured_image() {
    echo '</a>';
}

add_action('loop_start', 'alpenhouse_loop_start_services');
if(!function_exists('alpenhouse_loop_start_services')){
    function alpenhouse_loop_start_services(){
        if(is_archive() && get_post_type() == 'mphb_room_service'){
            remove_filter('the_content', array(MPHB()->postTypes()->service(), 'appendMetas'));
        }
    }
}

add_action('post_class', 'alpenhouse_single_mphb_service_class');
if(!function_exists('alpenhouse_single_mphb_service_class')){
    function alpenhouse_single_mphb_service_class($classes){
        if(is_archive() && get_post_type() == 'mphb_room_service'){
            $classes[] = 'hentry';
        }
        return $classes;
    }
}

add_filter('mphb_sc_rooms_wrapper_class', 'alpenhouse_mphb_accommodation_list_type');
if(!function_exists('alpenhouse_mphb_accommodation_list_type')){
    function alpenhouse_mphb_accommodation_list_type($classes){

        $list_layout =  get_theme_mod('alpenhouse_accommodation_list_layout', ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_1);
        if($list_layout){
            $classes.= ' '.$list_layout;
        }


        return $classes;
    }
}


if(get_theme_mod('alpenhouse_accommodation_list_layout', ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_1) == ALPENHOUSE_ACCOMMODATION_LIST_LAYOUT_3){
    add_action('mphb_sc_rooms_item_bottom', 'alpenhouse_mphb_after_room_description');
    add_action('mphb_sc_rooms_render_title', 'alpenhouse_mphb_before_room_description', 5);
    add_action('mphb_sc_rooms_item_top', 'alpenhouse_mphb_before_room_images');
//    add_action('mphb_sc_rooms_render_image', 'alpenhouse_mphb_before_room_images', 15);
}
if(!function_exists('alpenhouse_mphb_before_room_description')){
    function alpenhouse_mphb_before_room_description(){
        ?>
        </div>
        <div class="accommodation-list-room-description">
        <?php
    }
}
if(!function_exists('alpenhouse_mphb_after_room_description')){
    function alpenhouse_mphb_after_room_description(){
        ?>
        </div>
        <?php
    }
}
if(!function_exists('alpenhouse_mphb_before_room_images')){
    function alpenhouse_mphb_before_room_images(){
        ?>
        <div class="accommodation-list-room-images">
        <?php
    }
}

