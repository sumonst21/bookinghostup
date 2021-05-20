<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Alpenhouse_Blog_Posts extends Widget_Base {

    public function get_name() {
        return 'blog-posts';
    }
    public function get_categories() {
        return [ 'alpenhouse-elements' ];
    }

    public function get_id() {
        return 'alpenhouse-blog-posts';
    }

    public function get_title() {
        return __( 'Blog posts', 'alpenhouse-engine' );
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_blog_posts',
            [
                'label' => __( 'Blog posts', 'alpenhouse-engine' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'ids',
            [
                'label'   => __('Post IDs to show', 'alpenhouse-engine'),
                'description' => __('Leave blank to include all posts', 'alpenhouse-engine'),
                'type'    => Controls_Manager::TEXT,
                'placeholder' => '1,2,3'
            ]
        );
        $this->add_control(
            'order',
            [
                'label'       => __( 'Posts ordering', 'alpenhouse-engine' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => 'DESC',
                    'ASC' => 'ASC',
                ],
            ]
        );
        $this->add_control(
            'order-by',
            [
                'label'       => __( 'Order posts by', 'alpenhouse-engine' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'title',
                'options' => [
                    'title' => 'Title',
                    'date' => 'Date',
                    'modified' => 'Modified',
                    'rand' => 'Random',
                ]
            ]
        );
        $this->add_control(
            'showposts',
            [
                'label'   => __( 'Number of posts to show', 'alpenhouse-engine' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 3,
                'min'     => 1,
                'max'     => 5,
                'step'    => 1,
            ]
        );


        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {

        $settings = $this->get_settings();



        $args = array(
            'post_type'   => 'post',
            'posts_per_page' => $settings['showposts'],
            'post_status'   => 'publish',
            'order' => $settings['order'],
            'orderby' => $settings['order-by'],
            'ignore_sticky_posts' => true
        );

        if(!empty($settings['ids'])){
            $args['post__in'] = array_map( 'trim', explode( ',', $settings['ids'] ) );
        }

        $query = new \WP_Query($args);
        if($query->have_posts()){
            ?>
            <div class="blog-posts-widget-wrapper">
            <?php
            while ($query->have_posts()):
                $query->the_post();
                ?>
                <div class="post has-post-thumbnail">
                    <?php
                    if(has_post_thumbnail()){
                    ?>
                    <div class="post-thumbnail-wrapper">
                        <a href="<?php the_permalink();?>" class="post-thumbnail">
                            <?php
                            the_post_thumbnail('alpenhouse-post-thumbnail-cropped')
                            ?>
                            <div class="thumbnail-overlay"></div>
                        </a>

                    </div>
                    <?php
                    }
                    ?>
                    <header class="entry-header">
                        <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
                    </header>
                    <div class="entry-meta">
                        <?php
                            if(function_exists('alpenhouse_posted_on')){
                                alpenhouse_posted_on();
                            }
                        ?>
                    </div>
                    <div class="entry-content">
                        <?php
                            the_excerpt();
                        ?>
                    </div>
                </div>
                <?php
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

Plugin::instance()->widgets_manager->register_widget_type( new Alpenhouse_Blog_Posts());
