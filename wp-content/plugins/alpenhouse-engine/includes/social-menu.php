<?php
class Social_Menu_Widget extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'alpenhouse-social-menu',  // Base ID
            __('Social Menu','alpenhouse-engine')   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'Social_Menu_Widget' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>'
    );
 
    public function widget( $args, $instance ) {

        $nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

        if ( ! $nav_menu ) {
            return;
        }

        $nav_menu_args = array(
            'fallback_cb' => '',
            'menu_class' => 'theme-social-menu',
            'link_before'     => '<span class="menu-text">',
            'link_after'      => '</span>',
            'depth'         => 1,
            'menu'        => $nav_menu
        );

        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

 
        echo '<div class="alpenhouse-social-menu">';

        wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );
 
        echo '</div>';
 
        echo $args['after_widget'];
 
    }
 
    public function form( $instance ) {
 
        $title = ! empty( $instance['title'] ) ? $instance['title'] : "";
        $nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

        // Get menus
        $menus = wp_get_nav_menus();
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>" name="<?php echo $this->get_field_name( 'nav_menu' ); ?>">
                <option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
                <?php foreach ( $menus as $menu ) : ?>
                    <option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
                        <?php echo esc_html( $menu->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        if ( ! empty( $new_instance['nav_menu'] ) ) {
            $instance['nav_menu'] = (int) $new_instance['nav_menu'];
        }
 
        return $instance;
    }
 
}
$my_widget = new Social_Menu_Widget();