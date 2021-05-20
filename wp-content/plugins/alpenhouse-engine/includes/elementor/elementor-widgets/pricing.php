<?php
namespace Elementor;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Alpenhouse_Pricing extends Widget_Base {

    public function get_name() {
        return 'pricing';
    }
    public function get_categories() {
        return [ 'alpenhouse-elements' ];
    }

    public function get_id() {
        return 'alpenhouse-pricing';
    }

    public function get_title() {
        return __( 'Pricing Package', 'alpenhouse-engine' );
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_pricing',
            [
                'label' => __( 'Pricing Package', 'alpenhouse-engine' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Title', 'alpenhouse-engine'),
                'type'    => Controls_Manager::TEXT,

            ]
        );

        $this->add_control(
            'highlighted',
            [
                'label'   => __('Highlight this item', 'alpenhouse-engine'),
                'type'    => Controls_Manager::SWITCHER,
            ]
        );
        $this->add_control(
            'subtitle',
            [
                'label'   => __('Sub-title', 'alpenhouse-engine'),
                'description' => __('Visible only when highlighted', 'alpenhouse-engine'),
                'type'    => Controls_Manager::TEXT,
                'condition' =>[
                    'highlighted' => 'yes'
                ]
            ]

        );
        $this->add_control(
            'currency',
            [
                'label' => __('Currency', 'alpenhouse-engine'),
                'type'  => Controls_Manager::TEXT,
                'default'   => '$'
            ]
        );
        $this->add_control(
            'price',
            [
                'label' => __('Price', 'alpenhouse-engine'),
                'type'  => Controls_Manager::NUMBER,
                'default'   => 50,
                'min'   => 1,
                'step'  => 1,
            ]
        );
        $this->add_control(
            'period',
            [
                'label' => __('Period', 'alpenhouse-engine'),
                'type'  => Controls_Manager::TEXT,
                'default'   => '/mo',
            ]
        );
        $this->add_control(
            'options',
            [
                'label' => __('Content', 'alpenhouse-engine'),
                'type'  => Controls_Manager::WYSIWYG
            ]
        );
        $this->add_control(
            'button-text',
            [
                'label' => __('Button text', 'alpenhouse-engine'),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'button-link',
            [
                'label' => __('Button link', 'alpenhouse-engine'),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {

        $settings = $this->get_settings();
        echo do_shortcode('[alpenhouse_pricing highlighted="'.$settings['highlighted'].'" title="'.$settings['title'].'" 
        subtitle="'.$settings['subtitle'].'" currency="'.$settings['currency'].'" price="'.$settings['price'].'" 
        period="'.$settings['period'].'" button-text="'.$settings['button-text'].'" 
        button-link="'.$settings['button-link'].'"]'.$settings['options'].'[/alpenhouse_pricing]');


    }

    protected function content_template() {}

    public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Alpenhouse_Pricing());
