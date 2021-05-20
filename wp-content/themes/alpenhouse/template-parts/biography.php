<?php
/**
 * The template part for displaying an Author biography
 *
 * @package Alpenhouse
 */

if (function_exists('jetpack_author_bio')) :
    jetpack_author_bio();
else:
    ?>
    <div class="entry-author">
        <div class="author-avatar">
            <?php echo get_avatar(get_the_author_meta('ID'), 80) ?>
        </div>
        <div class="author">
            <span class="name"><?php echo  esc_html__('Author: ', 'alpenhouse').get_the_author_meta('display_name');?></span>
            <p class="description"><?php the_author_meta('description');?></p>
            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo esc_html__('All posts by author','alpenhouse');?></a>
        </div>
    </div>
<?php endif;