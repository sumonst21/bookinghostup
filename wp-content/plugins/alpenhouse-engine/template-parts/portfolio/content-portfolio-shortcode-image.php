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
        <div class="portfolio-thumbnail">
			<a href="<?php the_permalink() ?>" class="mfp-image portfolio-popup">
            <?php
                the_post_thumbnail();
            ?>
			</a>
		</div>
    </div><!-- #post-<?php the_ID(); ?> -->
</div>