<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Alpenhouse_Testimonials_Carousel extends Widget_Base {

    public function get_name() {
        return 'testimonials-carousel';
    }
    public function get_categories() {
        return [ 'alpenhouse-elements' ];
    }

    public function get_id() {
        return 'alpenhouse-testimonials-carousel';
    }

    public function get_title() {
        return __( 'Testimonials carousel', 'alpenhouse-engine' );
    }
    public function get_script_depends() {
        return ['slick-slider', 'testimonials-carousel'];
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_testimonials_carousel',
            [
                'label' => __( 'Testimonials', 'alpenhouse-engine' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'ids',
            [
                'label'   => __('Testimonial IDs to show', 'alpenhouse-engine'),
                'description' => __('Leave blank to include all items', 'alpenhouse-engine'),
                'type'    => Controls_Manager::TEXT,
                'placeholder' => '1,2,3'
            ]
        );
        $this->add_control(
            'showposts',
            [
                'label'   => __( 'Number of testimonials in carousel', 'alpenhouse-engine' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 1,
                'step'    => 1,
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_testimonials_carousel_options',
            [
                'label' => __('Carousel options', 'alpenhouse-engine'),
                'tab'   => Controls_Manager::TAB_CONTENT
            ]
        );
        $this->add_responsive_control(
            'slides-to-show',
            [
                'label' => __('Slides to show', 'alpenhouse-engine'),
                'type'  => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 4,
                'step' => 1
            ]
        );
        $this->add_control(
            'slide-speed',
            [
                'label' => __('Slide speed', 'alpenhouse-engine'),
                'type'  => Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 100,
                'max' => 2000,
                'step' => 100
            ]
        );
        $this->add_control(
                'autoplay',
                [
                    'label' => __('Autoplay', 'alpenhouse-engine'),
                    'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {

        $settings = $this->get_settings();

        $this->add_render_attribute(
                'carousel',
                [
                    'data-items-to-show-mobile' => $settings['slides-to-show_mobile'],
                    'data-items-to-show-tablet' => $settings['slides-to-show_tablet'],
                    'data-items-to-show-desktop' => $settings['slides-to-show'],
                    'data-slide-speed' => $settings['slide-speed'],
                    'data-autoplay' => $settings['autoplay'],
                ]
        );
        $args = array(
            'post_type' => 'cptp-testimonial',
            'posts_per_page' => $settings['showposts'],

        );
        if ( !empty($atts['ids']) ) {
            $args['post__in'] = array_map('trim', explode(',', $settings['ids']));
        }

        $query = new \WP_Query($args);
        if ($query->have_posts()){
            ?>
            <div class="testimonials-carousel" <?php echo $this->get_render_attribute_string('carousel');?>>
                <?php
                while ($query->have_posts()):
                    $query->the_post();
					if ( function_exists('cptp_get_template_part') ) {
						cptp_get_template_part( 'template-parts/content-testimonial', 'shortcode' );
					}
                endwhile;
                ?>
            </div>
            <?php
        }

        wp_reset_postdata();

    }

    protected function content_template() {}

    public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Alpenhouse_Testimonials_Carousel());

