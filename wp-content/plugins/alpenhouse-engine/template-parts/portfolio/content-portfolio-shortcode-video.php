
<?php
/**
 * Template part for displaying a portfolio in archive
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

?>
<div class="portfolio-post-wrapper">
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
		<?php
			the_content();
		?>
		</div><!-- .entry-content -->
    </div><!-- #post-<?php the_ID(); ?> -->
</div>