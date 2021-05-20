<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php alpenhouse_post_thumbnail(); ?>
    <header class="entry-header">
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;


        if(class_exists('Alpenhouse_Engine')){
            $activity_price = get_field('activity-price');
            $activity_period = get_field('activity-period');
            if($activity_price){
                ?>
                <div class="pricing">
                    <span><?php echo __('Price:', 'alpenhouse');?></span>
                    <span class="price"><?php echo esc_html($activity_price);?></span>
                    <?php
                    if($activity_period){
                        ?>
                        <span class="period"><?php echo esc_html(' / '.$activity_period);?></span>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }

        ?>
    </header><!-- .entry-header -->
    <div class="entry-content">
        <?php
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
        ?>
    </div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->