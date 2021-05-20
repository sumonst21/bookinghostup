
<?php
/**
 * Template part for displaying a portfolio in archive
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

$post_format = get_post_format();
if ( !$post_format ) {
	$post_format = 'image';
}

cptp_get_template_part( 'template-parts/portfolio/content-portfolio-shortcode', $post_format );
