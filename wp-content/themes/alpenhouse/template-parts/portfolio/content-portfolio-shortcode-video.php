
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
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="portfolio-thumbnail">
            <a href="<?php echo get_the_content(); ?>" class="mfp-iframe portfolio-popup">
			<?php
                the_post_thumbnail('alpenhouse-post-thumbnail-cropped');
            ?>
				<div class="media-type">
					<i class="fa fa-play" aria-hidden="true"></i>
				</div>
			</a>
			<div class="links">
				<a href="<?php the_permalink(); ?>"><i class="fa fa-share" aria-hidden="true"></i></a>
			</div>
        </div>
		<header class="entry-header hidden">
		<?php
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		?>
		</header><!-- .entry-header -->
    </article><!-- #post-<?php the_ID(); ?> -->
</div>