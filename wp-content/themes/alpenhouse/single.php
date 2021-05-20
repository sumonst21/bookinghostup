<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package alpenhouse
 */

get_header();
?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main">

            <?php
            while ( have_posts() ) :
                the_post();

                get_template_part( 'template-parts/content', get_post_type() );

                alpenhouse_the_tags();

                if ( is_single() && get_the_author_meta( 'description' ) && 'post' === get_post_type() ) :
                    get_template_part( 'template-parts/biography' );
                endif;

                alpenhouse_related_posts($post);

                alpenhouse_post_navigation();

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;



            endwhile; // End of the loop.
            ?>

            </main><!-- #main -->
        </div><!-- #primary -->


<?php
get_sidebar();
get_footer();
