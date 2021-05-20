<?php
/**
 * Plugin Name: AlpenHouse Engine
 * Plugin URI:  https://motopress.com
 * Description: AlpenHouse Engine
 * Version:     0.1.1
 * Author:      MotoPress
 * Author URI:  https://motopress.com
 * Text Domain: alpenhouse-engine
 * Domain Path: /languages
 */

if ( !class_exists('Alpenhouse_Engine') ) :

    final class Alpenhouse_Engine
    {

        /**
         * The single instance of the class.
         */
        protected static $_instance = null;
        private $prefix;
        private $theme_prefix;

        /**
         * Main Alpenhouse_Engine Instance.
         *
         * Ensures only one instance of WooCommerce is loaded or can be loaded.
         *
         * @since
         * @static
         * @see AlpenhouseEngine_Instance()
         * @return Alpenhouse_Engine - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function __construct()
        {
			$this->prefix = 'alpenhouse_engine';
			$this->theme_prefix = 'alpenhouse';
			/*
			 *  Path to classes folder in Plugin
			 */
			
			define('ALPENHOUSE_ENGINE_PATH', plugin_dir_path(__FILE__) );
			define('ALPENHOUSE_ENGINE_INCLUDES_PATH', plugin_dir_path(__FILE__) . 'includes/');
			define('ALPENHOUSE_ENGINE_PLUGIN_URL', plugin_dir_url(__FILE__));

			$this->include_files();

            add_action('plugins_loaded', array($this, 'alpenhouse_engine_plugins_loaded'));
            add_action( 'wp_enqueue_scripts', array($this, 'alpenhouse_engine_scripts' ));

			add_action( 'export_wp', array($this, 'alpenhouse_engine_register_post_formats') );
        }

        /**
         * Load plugin textdomain.
         *
         * @access public
         * @return void
         */
        function alpenhouse_engine_plugins_loaded()
        {
            load_plugin_textdomain('alpenhouse-engine', false, basename(dirname(__FILE__)) . '/languages/');

			//custom-post-type-plus metas
			if ( class_exists('Custom_Post_Type_Plus') ) {
                include_once  ALPENHOUSE_ENGINE_INCLUDES_PATH . 'cpt/activity.php';
                include_once  ALPENHOUSE_ENGINE_INCLUDES_PATH . 'cpt/testimonial.php';
                include_once  ALPENHOUSE_ENGINE_INCLUDES_PATH . 'cpt/portfolio.php';
            }
        }

        /**
         * Get prefix.
         *
         * @access public
         * @return sting
         */
        public function get_prefix()
        {
            return $this->prefix . '_';
        }

        /**
         * Get theme prefix.
         *
         * @access public
         * @return sting
         */
        public function get_theme_prefix()
        {
            return $this->theme_prefix . '_';
        }

        /**
         * Is theme alpenhouseengine.
         *
         * @access public
         * @return sting
         */
        public static function is_alpenhouseengine_theme()
        {

            if (get_template() === 'alpenhouse') {
                return true;
            }

            return false;
        }

        public function include_files()
        {
			/*
            * Include Advanced Custom Fields
            */
            include_once ALPENHOUSE_ENGINE_INCLUDES_PATH . '/acf-config.php';

            include_once ALPENHOUSE_ENGINE_INCLUDES_PATH . '/acf-data.php';

			include_once ALPENHOUSE_ENGINE_PATH . 'functions.php';

            include_once  ALPENHOUSE_ENGINE_INCLUDES_PATH . '/social-menu.php';

            /**
             * Include custom header
             */
            include_once  ALPENHOUSE_ENGINE_INCLUDES_PATH . '/custom-header.php';

            /**
             * Include Elementor widgets
             */

            include_once ALPENHOUSE_ENGINE_INCLUDES_PATH . '/elementor/elementor.php';

        }

        /**
         *  Enqueue scripts/styles
         */
        public function alpenhouse_engine_scripts(){

            if( !self::is_alpenhouseengine_theme() ){
                wp_enqueue_style( 'font-awesome',  plugins_url('assets/css/font-awesome.css', __FILE__) , array(), '4.7.0' );

                wp_enqueue_style( 'slick',  plugins_url('assets/slick/slick.css', __FILE__) , array(), '1.9.0' );
                wp_enqueue_style( 'slick-theme', plugins_url('assets/slick/slick-theme.css', __FILE__) , array(), '1.9.0' );
                wp_enqueue_script( 'slick', plugins_url('assets/slick/slick.min.js', __FILE__) , array('jquery'), '1.9.0', true );

                wp_enqueue_style( 'fallback-css', plugins_url('assets/fallback.css', __FILE__));
                wp_enqueue_script( 'fallback-js', plugins_url('assets/fallback.js', __FILE__) , array('jquery', 'slick'), '1.0', true );

                if ( class_exists('Custom_Post_Type_Plus') ) {
                    add_filter('cptp_locate_template', array($this, 'cptp_locate_template'), 10, 3);
                    add_filter('cptp_get_template_part_templates', array($this, 'cptp_get_template_part_templates'), 10, 3);
                }
            }

        }

        public function cptp_get_template_part_templates($templates, $slug, $name = null ) {

            $default_template = Custom_Post_Type_Plus::get_default_template();
            if (($key = array_search($default_template, $templates)) !== false) {
                unset($templates[$key]);
            }
            return $templates;
        }

        public function cptp_locate_template($located, $slug, $name = null) {
            if ( '' === $located) {
                $templates = array();
                $name = (string) $name;
                if ( '' !== $name )
                    $templates[] = "{$slug}-{$name}.php";
                $templates[] = "{$slug}.php";
                foreach ( (array) $templates as $template_name ) {
                    if ( !$template_name )
                        continue;
                    if ( file_exists( ALPENHOUSE_ENGINE_PATH . $template_name ) ) {
                        $located = ALPENHOUSE_ENGINE_PATH . $template_name;
                        break;
                    }
                }
            }
            return $located;
        }

        public function alpenhouse_engine_register_post_formats() {
            register_taxonomy_for_object_type( 'post_format', 'cptp-portfolio' );
        }

    }

    /**
     * Main instance of Alpenhouse_Engine_Instance.
     *
     * Returns the main instance of WC to prevent the need to use globals.
     *
     * @since
     * @return
     */
    function alpenhouse_engine_instance()
    {
        return Alpenhouse_Engine::instance();
    }

    /*
     * Global for backwards compatibility.
     */
    $GLOBALS['alpenhouse_engine_instance'] = alpenhouse_engine_instance();

endif;
