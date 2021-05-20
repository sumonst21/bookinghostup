<?php
/**
 * The template for displaying the post.
 *
 * This page template will display any functions hooked into the `boxed post` action.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * Template name: Post boxed
 * Template post type: post, mphb_room_type
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

                    do_action('alpenhouse_post_navigation_links');

                endwhile; // End of the loop.
                ?>

            </main><!-- #main -->
        </div><!-- #primary -->

<?php
get_footer();
