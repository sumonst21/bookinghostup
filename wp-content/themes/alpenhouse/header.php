<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package alpenhouse
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
    <div class="site-wrapper">
        <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'alpenhouse' ); ?></a>

        <header id="masthead" class="site-header">
            <?php
            if(has_nav_menu('menu-2') ||
                has_nav_menu('menu-3') ||
                class_exists('WooCommerce')){
                ?>
                <div class="header-top-menus">
                    <div class="header-container wrapper">
                        <div class="header-wrapper-inner">
                            <?php
                            if(has_nav_menu('menu-3')){
                                wp_nav_menu(array(
                                    'theme_location' => 'menu-3',
                                    'menu_id'   => 'header-contacts',
                                    'container_class' => 'menu-contacts-container',
                                    'menu_class' => 'theme-social-menu clear',
                                    'link_before'     => '<span class="menu-text">',
                                    'link_after'      => '</span>',
                                    'depth' => 1
                                ));
                            }
                            ?>
                            <div class="top-right-menus">
                                <?php
                                if(has_nav_menu('menu-2')){
                                    wp_nav_menu(array(
                                        'theme_location' => 'menu-2',
                                        'menu_id'   => 'header-socials',
                                        'container_class' => 'menu-socials-container',
                                        'menu_class'    => 'theme-social-menu clear',
                                        'link_before'     => '<span class="menu-text">',
                                        'link_after'      => '</span>',
                                        'depth'         => 1
                                    ));
                                }

                                    do_action('alpenhouse_woo_mini_cart');
                                    do_action('alpenhouse_wpml_list');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="wrapper header-menu">
                <div class="site-branding">
                    <?php
                    the_custom_logo();
                    if ( is_front_page() && is_home() ) :
                        ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                        <?php
                    else :
                        ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                        <?php
                    endif;
                    $alpenhouse_description = get_bloginfo( 'description', 'display' );
                    if ( $alpenhouse_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $alpenhouse_description; /* WPCS: xss ok. */ ?></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->
                <div class="menu-toggle-wrapper">
                    <button class="menu-toggle" aria-controls="primary-menu" id="mobile-menu-toggle" aria-expanded="false"><i class="fa fa-bars" aria-hidden="true"></i></button>
                </div>
                <div class="menu-wrapper" id="menu-wrapper">
                    <?php
                         if(has_nav_menu('menu-1')) {

                             ?>
                             <nav id="site-navigation" class="main-navigation">
                                 <?php
                                 wp_nav_menu(array(
                                     'theme_location' => 'menu-1',
                                     'menu_id' => 'primary-menu',
                                     'container_class' => 'menu-primary-container',
                                     'menu_class' => 'menu nav-menu clear'
                                 ));
                                 ?>
                             </nav><!-- #site-navigation -->
                             <?php
                         }
                    ?>


                    <div class="mobile-social-menus">
                        <?php
                        if(has_nav_menu('menu-3')) {
                            wp_nav_menu(array(
                                'theme_location' => 'menu-3',
                                'menu_id' => 'header-contacts-mobile',
                                'container_class' => 'menu-contacts-container',
                                'menu_class' => 'theme-social-menu clear',
                                'link_before' => '<span class="menu-text">',
                                'link_after' => '</span>',
                                'depth' => 1
                            ));
                        }
                        if(has_nav_menu('menu-2')) {
                            wp_nav_menu(array(
                                'theme_location' => 'menu-2',
                                'menu_id' => 'header-socials-mobile',
                                'container_class' => 'menu-socials-container',
                                'menu_class' => 'theme-social-menu clear',
                                'link_before' => '<span class="menu-text">',
                                'link_after' => '</span>',
                                'depth'     => 1
                            ));
                        }

                            do_action('alpenhouse_wpml_list');
                        ?>
                    </div>

                    <?php
                        alpenhouse_header_search();
                    ?>

                </div>
            </div>
            <?php
                do_action('alpenhouse_header_before_content');
                do_action('alpenhouse_header_content');
                do_action('alpenhouse_header_after_content');
            ?>
        </header><!-- #masthead -->

        <div id="content" class="site-content">
            <div class="wrapper clear">
