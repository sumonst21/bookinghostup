<?php

if ( !class_exists('ACF') ) {

	// Customize ACF path
	add_filter('acf/settings/path', 'alpenhouse_engine_acf_settings_path');

	function alpenhouse_engine_acf_settings_path( $path ) {

		return ALPENHOUSE_ENGINE_INCLUDES_PATH . '/acf/';
	}
	 

	// Customize ACF dir
	add_filter('acf/settings/dir', 'alpenhouse_engine_acf_settings_dir');

	function alpenhouse_engine_acf_settings_dir( $dir ) {

		return ALPENHOUSE_ENGINE_PLUGIN_URL . '/includes/acf/';;

	}
	 

	// Hide ACF field group menu item
	add_filter('acf/settings/show_admin', 'alpenhouse_engine_acf_show_admin');

	function alpenhouse_engine_acf_show_admin() {

		return FALSE;

	}

}

// Include built-in ACF
include_once( ALPENHOUSE_ENGINE_INCLUDES_PATH . '/acf/acf.php' );
