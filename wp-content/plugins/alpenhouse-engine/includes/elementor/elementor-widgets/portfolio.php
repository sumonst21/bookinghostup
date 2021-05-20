<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Alpenhouse_Portfolio extends Widget_Base {

    public function get_name() {
        return 'portfolio';
    }
    public function get_categories() {
        return [ 'alpenhouse-elements' ];
    }

    public function get_id() {
        return 'alpenhouse-portfolio';
    }

    public function get_title() {
        return __( 'Portfolio', 'alpenhouse-engine' );
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_portfolio',
            [
                'label' => __( 'Portfolio', 'alpenhouse-engine' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'ids',
            [
                'label'   => __('Portfolio IDs to show', 'alpenhouse-engine'),
                'description' => __('Leave blank to include all items', 'alpenhouse-engine'),
                'type'    => Controls_Manager::TEXT,
                'placeholder' => '1,2,3'
            ]
        );
        $this->add_control(
            'columns',
            [
                'label'   => __( 'Columns', 'alpenhouse-engine' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 2,
                'min'     => 1,
                'max'     => 4,
                'step'    => 1,
            ]
        );
        $this->add_control(
            'showposts',
            [
                'label'   => __( 'Number of posts to show', 'alpenhouse-engine' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 1,
                'step'    => 1,
            ]
        );
        $this->add_control(
            'disable_pagination',
            [
                'label'   => __( 'Show/Hide posts pagination', 'alpenhouse-engine' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Show', 'alpenhouse-engine' ),
                'label_off' => __( 'Hide', 'alpenhouse-engine' ),
                'return_value' => 'yes',
            ]
        );


        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {

		$settings = $this->get_settings();

		if( class_exists('Custom_Post_Type_Plus_Portfolio') && $settings['disable_pagination'] != 'yes' ){
			remove_action('cptp_portfolio_shortcode_pagination', array( \Custom_Post_Type_Plus_Portfolio::instance(), 'shortcode_pagination'));
		}

		echo '<div class="portfolio-shortcode-wrapper">';
		echo do_shortcode('[portfolio columns="'.$settings['columns'].'" ids="'.$settings['ids'].'" showposts="'.$settings['showposts'].'"] ');
		echo '</div>';
    }

    protected function content_template() {}

    public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Alpenhouse_Portfolio());

