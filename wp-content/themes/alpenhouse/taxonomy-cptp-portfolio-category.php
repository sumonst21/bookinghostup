<?php
/**
 * The template for displaying portfolio category
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

get_header();
?>
        <div id="primary" class="content-area  <?php echo apply_filters('alpenhouse_content_area_class', '');?>">
            <main id="main" class="site-main">

                <?php if ( have_posts() ) : ?>

                    <header class="page-header hidden">
                        <?php
                        the_archive_title( '<h1 class="page-title">', '</h1>' );
                        the_archive_description( '<div class="archive-description">', '</div>' );
                        ?>
                    </header><!-- .page-header -->

                    <?php

                    alpenhouse_portfolio_menu();

                    do_action('alpenhouse_before_portfolio_loop');
                    /* Start the Loop */
                    while ( have_posts() ) :
                        the_post();

                        /*
                         * Include the Post-Type-specific template for the content.
                         * If you want to override this in a child theme, then include a file
                         * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                         */
                        get_template_part( 'template-parts/content-portfolio', 'shortcode');

                    endwhile;

                    do_action('alpenhouse_after_portfolio_loop');

                    alpenhouse_the_posts_pagination();

                else :

                    get_template_part( 'template-parts/content', 'none' );

                endif;
                ?>

            </main><!-- #main -->
        </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
