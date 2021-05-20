<?php


add_action( 'elementor/elements/categories_registered', function() {
    \Elementor\Plugin::$instance->elements_manager->add_category(
        'alpenhouse-elements',
        [
            'title' => __( 'Theme Elements', 'alpenhouse-engine' ),
            'icon' => 'fa fa-plug', //default icon
        ]
    );
} );

add_action( 'elementor/widgets/widgets_registered', 'alpenhouse_elementor_widgets_registered' );
function alpenhouse_elementor_widgets_registered() {
    $widgets = array(
        'testimonials-carousel/testimonials-carousel',
        'portfolio',
        'blog-posts',
        'image-hotspot',
        'activities',
        'pricing'
    );
    foreach ($widgets as $widget){
        require_once __DIR__.'/elementor-widgets/'.$widget.'.php';
    }

}


add_action( 'elementor/frontend/after_register_scripts', function() {
    wp_register_script( 'slick', ALPENHOUSE_ENGINE_PLUGIN_URL . 'assets/slick/slick.js', [ 'jquery' ], '1.9.0', true );
    wp_register_script( 'testimonials-carousel', ALPENHOUSE_ENGINE_PLUGIN_URL . 'includes/elementor/elementor-widgets/testimonials-carousel/testimonials-carousel.js', [ 'jquery' ], false, true );
    wp_register_script( 'image-hotspot', plugins_url('mpce-image-hotspot-addon/assets/js/engine.min.js'), [ 'jquery' ], false, true );

    wp_register_style( 'image-hotspot-style', plugins_url('mpce-image-hotspot-addon/assets/css/style.min.css'), array(), '1.3.1' );
} );

add_action('elementor/init', function (){
    wp_enqueue_style( 'mpce-ihs-style', plugins_url('mpce-image-hotspot-addon/assets/css/style.min.css'), array(), '1.3.1' );
});


add_action( 'elementor/element/before_section_end', function( $element, $section_id, $args ) {
    /** @var \Elementor\Element_Base $element */
    if ( 'mphbe-rooms' === $element->get_name() && 'section_parameters' === $section_id ) {

        $element->add_control(
            'list_type',
            [
                'label' => __( 'List layout', 'alpenhouse-engine' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __( 'Default', 'alpenhouse-engine' ),
                    'horizontal-list' => __( 'Horizontal list', 'alpenhouse-engine' ),
                    'vertical-list' => __( 'Vertical list', 'alpenhouse-engine' ),
                ],
                'prefix_class' => '',
            ]
        );
    }
}, 10, 3 );


function alpenhouse_mphb_accommodation_widget_list_type($classes, $type){
    return $classes.= ' '.$type;
}
function alpenhouse_modify_mphb_widget_list($widget){
    if($widget->get_name()=='mphbe-rooms'){
        $type = $widget->get_settings()['list_type'];
        add_filter('mphb_sc_rooms_wrapper_class', function($classes) use ($type){
            return alpenhouse_mphb_accommodation_widget_list_type($classes, $type);
        });
        if($type == 'vertical-list'){
            add_action('mphb_sc_rooms_item_bottom', 'alpenhouse_engine_mphb_after_room_description');
            add_action('mphb_sc_rooms_render_title', 'alpenhouse_engine_mphb_before_room_description', 5);
            add_action('mphb_sc_rooms_item_top', 'alpenhouse_engine_mphb_before_room_images');
        }
    }
}
add_action('elementor/widget/before_render_content', 'alpenhouse_modify_mphb_widget_list');


if(!function_exists('alpenhouse_engine_mphb_before_room_images')){
    function alpenhouse_engine_mphb_before_room_images(){
        ?>
        <div class="accommodation-list-room-images">
        <?php
    }
}
if(!function_exists('alpenhouse_engine_mphb_before_room_description')){
    function alpenhouse_engine_mphb_before_room_description(){
        ?>
        </div>
        <div class="accommodation-list-room-description">
        <?php
    }
}
if(!function_exists('alpenhouse_engine_mphb_after_room_description')){
    function alpenhouse_engine_mphb_after_room_description(){
        ?>
        </div>
        <?php
    }
}


/**
 * Remove controls from image carousel widget
 * @param $section
 * @param $section_id
 * @param $args
 */
function alpenhouse_remove_controls($section, $section_id, $args){
    if( $section->get_name() == 'image-carousel' && $section_id == 'section_style_navigation' )
    {
        $section->remove_control('dots_color');
        $section->remove_control('dots_size');
        $section->remove_control('arrows_color');
    }
}

add_action('elementor/element/before_section_end', 'alpenhouse_remove_controls', 10, 3);