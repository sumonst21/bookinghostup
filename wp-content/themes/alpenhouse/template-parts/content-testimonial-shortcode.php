<?php
/**
 * Template part for displaying testimonail post type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package alpenhouse
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="testimonial-wrapper">
        <div class="testimonial-thumbnail">
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
                <?php
                the_post_thumbnail('thumbnail');
                ?>
            </a>
        </div>
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
		<header class="entry-header">
		<?php
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		?>
        </header><!-- .entry-header -->
        <span class="testimonial-subtitle">
            <?php
            $acf_sub_title_field = get_field_object('sub-title');
			if ($acf_sub_title_field)
				echo esc_html($acf_sub_title_field['value']);
            ?>
        </span>

    </div>
</article><!-- #post-<?php the_ID(); ?> -->
