<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
            alpenhouse_posted_on();
            alpenhouse_posted_by();
            alpenhouse_posted_in();
            alpenhouse_post_comments();
            alpenhouse_edit_link();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

    <a href="<?php the_permalink();?>" class="button"><?php echo esc_html__('Continue reading', 'alpenhouse')?></a>
</article><!-- #post-<?php the_ID(); ?> -->
