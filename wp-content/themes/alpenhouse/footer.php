<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package alpenhouse
 */

?>
            </div>
        </div><!-- #content -->

        <footer id="colophon" class="site-footer">

            <?php
                get_sidebar('footer');
            ?>

            <div class="footer-bottom">
                <div class="wrapper">
                    <div class="wrapper-inside">
                        <div class="site-info">
                            <?php
                            $dateObj = new DateTime;
                            $year    = $dateObj->format( "Y" );
                            printf( '%2$s &copy; %1$s ', $year, bloginfo( 'name' ) );
                            ?>
                        </div><!-- .site-info -->
                        <?php if ( has_nav_menu( 'menu-4' ) ) : ?>
                            <nav class="footer-menu" role="navigation"
                                 aria-label="<?php esc_attr_e( 'Footer Menu', 'alpenhouse' ); ?>">
                                <?php wp_nav_menu(array(
                                    'theme_location' => 'menu-4',
                                    'menu_id'   => 'footer-menu',
                                    'container_class' => 'menu-footer-container',
                                    'menu_class'    => 'theme-social-menu clear',
                                    'depth' => 1,
                                    'link_before'     => '<span class="menu-text">',
                                    'link_after'      => '</span>'
                                )); ?>
                            </nav><!-- .footer-navigation -->
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </footer><!-- #colophon -->
    </div><!-- .site-wrapper -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
