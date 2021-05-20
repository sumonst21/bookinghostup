<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php alpenhouse_post_thumbnail(); ?>

	<header class="entry-header">
		<?php
        if(is_sticky()){
            ?>
            <span class="featured-post"><?php
                echo esc_html__('Featured', 'alpenhouse');
                ?></span>
            <?php
        }
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php

                alpenhouse_posted_on();
				alpenhouse_posted_in();
                if(!alpenhouse_is_masonry_blog()){
                    alpenhouse_posted_by();
                    alpenhouse_post_comments();
                }
				alpenhouse_edit_link();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->


	<div class="entry-content">
		<?php
        if(is_home() && 'post' == get_post_type() && alpenhouse_is_masonry_blog()){
            the_excerpt();
        }else{
            the_content( sprintf(
                wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                    __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'alpenhouse' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ) );

            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'alpenhouse' ),
                'after'  => '</div>',
            ) );
        }

		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
