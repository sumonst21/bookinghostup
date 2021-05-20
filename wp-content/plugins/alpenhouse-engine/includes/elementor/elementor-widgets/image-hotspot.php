<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Alpenhouse_Image_Hotspot extends Widget_Base {

    public function get_name() {
        return 'image-hotspot';
    }
    public function get_categories() {
        return [ 'alpenhouse-elements' ];
    }

    public function get_id() {
        return 'alpenhouse-image-hotspot';
    }

    public function get_title() {
        return __( 'Image Hotspot', 'alpenhouse-engine' );
    }

    public function get_script_depends()
    {
        return ['image-hotspot'];
    }

    public function get_style_depends()
    {
        return ['image-hotspot-style'];
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }


    protected function _register_controls() {

        $this->start_controls_section(
            'section_image_hotspot',
            [
                'label' => __( 'Image Hotspot', 'alpenhouse-engine' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Image', 'alpenhouse-engine'),
                'type' => Controls_Manager::MEDIA
            ]
        );
        $this->add_control(
            'hotspot_list',
            [
                'label' => __('List of hotspots', 'alpenhouse-engine'),
                'type'  => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'hotspot_title',
                        'label' => __( 'Title', 'alpenhouse-engine' ),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => __( 'Title' , 'alpenhouse-engine' ),
                        'label_block' => true,
                    ],
                    [
                        'name' => 'hotspot_x',
                        'label' => __( 'Position X', 'alpenhouse-engine' ),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 50,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,

                    ],
                    [
                        'name' => 'hotspot_y',
                        'label' => __( 'Position Y', 'alpenhouse-engine' ),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 50,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    [
                        'name' => 'hotspot_size',
                        'label' => __('Hotspot size', 'alpenhouse-engine'),
                        'type'  => Controls_Manager::SELECT,
                        'default' => 'normal',
                        'options' => [
                            'normal'   => __('Normal', 'alpenhouse-engine'),
                            'small'   => __('Small', 'alpenhouse-engine'),
                            'big'   => __('Big', 'alpenhouse-engine'),
                        ]
                    ],
                    [
                        'name' => 'tip_position',
                        'label' => __('Tooltip position', 'alpenhouse-engine'),
                        'type'  => Controls_Manager::SELECT,
                        'default' => 'top',
                        'options' => [
                            'top'   => __('Top', 'alpenhouse-engine'),
                            'right'   => __('Right', 'alpenhouse-engine'),
                            'left'   => __('Left', 'alpenhouse-engine'),
                            'bottom'   => __('Bottom', 'alpenhouse-engine'),
                        ]
                    ],
                    [
                        'name' => 'tip_show',
                        'label' => __('Display tooltip', 'alpenhouse-engine'),
                        'type'  => Controls_Manager::SELECT,
                        'default' => 'click',
                        'options' => [
                            'click'   => __('On click', 'alpenhouse-engine'),
                            'always'   => __('Always', 'alpenhouse-engine'),
                            'hover'   => __('On hover', 'alpenhouse-engine'),
                        ]
                    ]
                ],
                'title_field' => '{{{ hotspot_title }}}',
            ]
        );

        $this->end_controls_section();


    }

    protected function render( $instance = [] ) {

        $settings = $this->get_settings();
        $hotspot_list = $settings['hotspot_list'];

        $before_hotspots = '[mpce_image_hotspot img="'.$settings['image']['id'].'" common_hotspot_color="custom"
            common_hotspot_custom_color="#000000" common_plus_color="#fff" common_hotspot_size="small" 
            common_tip_theme="custom" common_custom_bg_theme="#344583" common_custom_font_theme="#fff"
            common_tip_position="top" common_tip_show="hover"]';
        $hotspots = '';

        foreach ($hotspot_list as $hotspot){
            $hotspots.='[mpce_hotspot pos_x="'.$hotspot['hotspot_x'].'" pos_y="'.$hotspot['hotspot_y'].'" hotspot_color="inherit" hotspot_custom_color="inherit"
            plus_color="inherit" hotspot_size="'.$hotspot['hotspot_size'].'" tip_theme="inherit" custom_bg_theme="inherit"
            custom_font_theme="inherit" tip_position="'.$hotspot['tip_position'].'" tip_show="'.$hotspot['tip_show'].'"]'.$hotspot['hotspot_title'].'[/mpce_hotspot]';
        }

        $after_hotspots = '[/mpce_image_hotspot]';

        $shortcode = $before_hotspots.$hotspots.$after_hotspots;
        echo do_shortcode($shortcode);


    }

    protected function content_template() {}

    public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Alpenhouse_Image_Hotspot());
