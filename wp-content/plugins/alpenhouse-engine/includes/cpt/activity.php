<?php
final class Alpenhouse_Engine_Activity_CPTP {
    protected static $_instance = null;
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct()
    {
        if ( ! Alpenhouse_Engine::is_alpenhouseengine_theme() ) {
            add_action( 'loop_start', array( $this, 'setupPseudoTemplate' ) );
        }
    }

    /**
     *
     * @param \WP_Query $query
     * @return null
     */
    public function setupPseudoTemplate( $query ){
        // append meta to single service page's query & service listing queries except our shortcodes output
        if ( $query->is_main_query() &&
            ($query->get( 'post_type' ) === Custom_Post_Type_Plus_Activity::CUSTOM_POST_TYPE )
        ) {
            $query->set( 'alpenhouse_engine_append_meta', true );
            add_filter( 'the_content', array( $this, 'appendMetas' ) );
            remove_action( 'loop_start', array( $this, 'setupPseudoTemplate' ) );
            add_action( 'loop_end', array( $this, 'stopAppendMetas' ) );
        }
    }

    /**
     * Append metas to service content.
     *
     * @param string $content
     * @return string
     */
    public function appendMetas( $content ){
        if ( is_main_query() &&
            get_query_var( 'alpenhouse_engine_append_meta' ) &&
            get_post_type() === Custom_Post_Type_Plus_Activity::CUSTOM_POST_TYPE
        ) {
            ob_start();
            self::renderMetas();
            $content = ob_get_clean() . $content;
        }
        return $content;
    }
    public function stopAppendMetas( $query ){
        if ( $query->is_main_query() &&
            $query->get( 'alpenhouse_engine_append_meta' )
        ) {
            remove_filter( 'the_content', array( $this, 'appendMetas' ) );
            remove_filter( 'loop_end', array( $this, 'stopAppendMetas' ) );
        }
    }
    public static function renderMetas() {
        $activity_price = get_field('activity-price');
        $activity_period = get_field('activity-period');
        if($activity_price){
            ?>
            <p class="pricing">
                <span><?php echo __('Price:', 'alpenhouse-engine');?></span>
                <span class="price"><?php echo esc_html($activity_price);?></span>
                <?php
                if($activity_period){
                    ?>
                    <span class="period"><?php echo esc_html(' / '.$activity_period);?></span>
                    <?php
                }
                ?>
            </p>
            <?php
        }

    }
}
Alpenhouse_Engine_Activity_CPTP::instance();