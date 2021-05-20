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
        <div id="primary" class="content-area boxed">
            <main id="main" class="site-main">

                <?php
                while ( have_posts() ) :
                    the_post();

                    get_template_part( 'template-parts/content', 'activity');

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
get_footer();
