<?php
/**
 * Template part for displaying testimonail post type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
	<?php
		the_post_thumbnail();
	?>
	</a>
	<header class="entry-header">
	<?php
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	?>
	</header><!-- .entry-header -->
	<p class="testimonial-subtitle">
		<?php
		$acf_sub_title_field = get_field_object('sub-title');
		if ($acf_sub_title_field)
			echo esc_html($acf_sub_title_field['value']);
		?>
	</p>
	<div class="entry-content">
		<?php
		the_content();
		?>
	</div><!-- .entry-content -->
</div><!-- #post-<?php the_ID(); ?> -->
