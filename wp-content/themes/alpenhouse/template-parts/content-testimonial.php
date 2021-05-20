<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="testimonial-wrapper">
        <div class="testimonial-thumbnail">
            <?php
            the_post_thumbnail('thumbnail');
            ?>
        </div>

        <header class="entry-header">
            <?php
            the_title( '<h1 class="entry-title">', '</h1>' );
            ?>
        </header><!-- .entry-header -->
        <span class="testimonial-subtitle">
            <?php
            $acf_sub_title_field = get_field_object('sub-title');
            if ($acf_sub_title_field)
                echo esc_html($acf_sub_title_field['value']);
            ?>
        </span>
        <div class="rating">
            <?php
            $rating = get_field('rating');
            for($i = 0; $i<$rating; $i++){
                echo '<span class="star"><i class="fa fa-star"></i></span>';
            }
            ?>
        </div>

        <div class="entry-content">
            <?php
            the_content();
            ?>
        </div><!-- .entry-content -->

    </div>
</article><!-- #post-<?php the_ID(); ?> -->