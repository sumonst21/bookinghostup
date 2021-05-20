<?php
/**
 * The template for displaying the page.
 *
 * This page template will display any functions hooked into the `fill width page` action.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * Template name: Page full width Custom
 *
 * @package alpenhouse
 */

get_header( 'custom' );
?>
        <div id="primary" class="content-area full-width">
            <main id="main" class="site-main">

                <?php
                while ( have_posts() ) :
                    the_post();

                    get_template_part( 'template-parts/content', 'page' );

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
