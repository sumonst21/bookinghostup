<?php

// enqueue parent stylesheet
add_action( 'wp_enqueue_scripts', 'alpenhouse_child_wp_enqueue_scripts' );
function alpenhouse_child_wp_enqueue_scripts() {

	$parent_theme = wp_get_theme( get_template() );
	$child_theme = wp_get_theme();

	// Enqueue the parent stylesheet
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), $parent_theme['Version'] );
	wp_enqueue_style( 'alpenhouse', get_stylesheet_uri(), array('parent-style'), $child_theme['Version'] );

	// Enqueue the parent rtl stylesheet
	if ( is_rtl() ) {
		wp_enqueue_style( 'parent-style-rtl', get_template_directory_uri() . '/rtl.css', array(), $parent_theme['Version'] );
	}
}


	// Enqueue my child-javascript files

	function my_scripts_method() {
    wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/custom_script.js',
        array( 'jquery' )
    );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


// Increase media file - Murillo Costa

@ini_set( 'upload_max_size' , '10M' );
@ini_set( 'post_max_size', '10M');
@ini_set( 'max_execution_time', '300' );
