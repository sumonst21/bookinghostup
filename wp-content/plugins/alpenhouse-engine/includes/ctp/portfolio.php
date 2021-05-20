<?php
final class Alpenhouse_Engine_Portfolio_CPTP {
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
            ($query->get( 'post_type' ) === Custom_Post_Type_Plus_Portfolio::CUSTOM_POST_TYPE ||
                1==2)
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
            get_post_type() === Custom_Post_Type_Plus_Portfolio::CUSTOM_POST_TYPE
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
        $categories = get_the_terms(get_the_ID(), Custom_Post_Type_Plus_Portfolio::CUSTOM_TAXONOMY_TYPE);
        if ( !empty($categories) ) {
            $category = $categories[0]; ?>
            <p><span class="category"><a href="<?php
                    echo esc_url( get_term_link($category, Custom_Post_Type_Plus_Portfolio::CUSTOM_TAXONOMY_TYPE) );?>"><?php
                        echo esc_html($category->name); ?></a></span></p>
            <?php
        }
    }
}
Alpenhouse_Engine_Portfolio_CPTP::instance();