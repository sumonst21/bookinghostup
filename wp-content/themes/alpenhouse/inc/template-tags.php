<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package alpenhouse
 */

if ( ! function_exists( 'alpenhouse_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function alpenhouse_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date('j F, Y') ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date('j F, Y') )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( '%s', 'post date', 'alpenhouse' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'alpenhouse_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function alpenhouse_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'alpenhouse' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;


if(!function_exists('alpenhouse_posted_in')){
    function alpenhouse_posted_in(){
        if ( 'post' === get_post_type() ) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list( esc_html_x( ', ', 'list item separator', 'alpenhouse' ) );
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('in %1$s', 'alpenhouse') . '</span>', $categories_list); // WPCS: XSS OK.
            }
        }
    }
}


if(!function_exists('alpenhouse_post_comments')){
    function alpenhouse_post_comments(){
        if ( ! is_single() && ! post_password_required() && get_comments_number() ) {
            $number = get_comments_number( get_the_ID() );
            $more   = wp_kses(_n( '%1$s comment', '%1$s comments', $number, 'alpenhouse' ), array( 'span' => array( 'class' => array() ) ));
            $comments = sprintf( $more, number_format_i18n( $number ));
            echo '<span class="comments-link">';
            comments_popup_link($comments, $comments, $comments);
            echo '</span>';
        }
    }
}

if(!function_exists('alpenhouse_edit_link')){
    function alpenhouse_edit_link(){
        edit_post_link(
            sprintf(
                wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                    __( 'Edit <span class="screen-reader-text">%s</span>', 'alpenhouse' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
}

if ( ! function_exists( 'alpenhouse_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function alpenhouse_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html_x( ', ', 'list item separator', 'alpenhouse' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'in %1$s', 'alpenhouse' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'alpenhouse' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'alpenhouse' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'alpenhouse' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'alpenhouse' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'alpenhouse_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function alpenhouse_post_thumbnail( $size = 'post-thumbnail' ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail( $size ); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php
			the_post_thumbnail( $size, array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) )
			) );
			?>
            <div class="thumbnail-overlay"></div>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'alpenhouse_the_posts_pagination' ) ) :
    /**
     * Displays the post pagination.
     */
    function alpenhouse_the_posts_pagination() {
        the_posts_pagination( array(
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'alpenhouse' ) . ' </span>',
            'mid_size'           => 1,
        ) );
    }
endif;


if ( ! function_exists( 'alpenhouse_related_posts' ) ) :
    /**
     * Displays related posts
     */
    function alpenhouse_related_posts( $post ) {
        if ( 'post' === get_post_type() ) {
            $orig_post = $post;
            global $post;
            $categories = wp_get_post_categories( $post->ID );
            if ( $categories ) {
                $args     = array(
                    'category__in'        => $categories,
                    'post__not_in'   => array( $post->ID ),
                    'posts_per_page' => 3
                );
                $my_query = new wp_query( $args );
                if ( $my_query->have_posts() ):
                    ?>
                    <div class="related-posts">
                        <h2 class="related-posts-title"><?php esc_html_e( 'Related Posts', 'alpenhouse' ); ?></h2>
                        <!-- .related-posts-title -->
                        <div class="related-posts-wrapper clear">
                            <?php
                            while ( $my_query->have_posts() ) {
                                $my_query->the_post();
                                ?>
                                <div class="related-post">
                                        <a href="<?php the_permalink() ?>" rel="bookmark"
                                           title="<?php the_title(); ?>">
                                            <?php
                                            the_post_thumbnail('alpenhouse-post-thumbnail-cropped');
                                            ?>
                                        </a>

                                        <?php the_title('<a href="'.esc_url(get_permalink()).'" class="entry-title">','</a>'); ?>


                                        <span class="date">
                                            <?php echo esc_html(get_the_date('j F, Y'));?>
                                        </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div><!-- .related-posts -->
                <?php
                endif;
                ?>
                <?php
            }
            $post = $orig_post;
            wp_reset_query();
        }
    }

endif;

if ( ! function_exists( 'alpenhouse_the_tags' ) ) :
    /**
     * Displays post tags.
     */
    function alpenhouse_the_tags() {
        if ( 'post' === get_post_type() ) {
            $tags_list = get_the_tag_list( '', esc_html_x( ' ', 'Used between list items, there is a space.', 'alpenhouse' ), '' );
            if ( $tags_list ) {
                printf( '<div class="single-post-tags"><span class="tags-links">%1$s %2$s</span></div>',
                    esc_html_x( 'Tags:', 'Used before tag names.', 'alpenhouse' ),
                    $tags_list
                );
            }
        }
    }
endif;

if(!function_exists('alpenhouse_breadcrumbs')){
    function alpenhouse_breadcrumbs(){
        if ( function_exists('yoast_breadcrumb') ) {
            yoast_breadcrumb('
                <nav id="breadcrumbs" class="breadcrumbs">','</nav>
            ');
        } elseif ( function_exists('breadcrumb_trail') ) {
            breadcrumb_trail(array(
                'show_browse'     => false,
                'show_on_front'   => false,
                'container'       => 'nav',

            ));
        }
    }
}


if(!function_exists('alpenhouse_header_search')){
    function alpenhouse_header_search(){
        if(!get_theme_mod('alpenhouse_display_header_search','1')){
            return;
        }

        ?>
        <div class="header-search">
            <?php get_search_form(); ?>
        </div><!-- .wrapper -->
        <?php
    }
}

if ( ! function_exists( 'alpenhouse_portfolio_menu' ) ) :
    function alpenhouse_portfolio_menu() {
        if ( class_exists('Custom_Post_Type_Plus_Portfolio') ) {
            $taxonomy = Custom_Post_Type_Plus_Portfolio::CUSTOM_TAXONOMY_TYPE;


            $portfolio_categories = wp_list_categories(array(
                'taxonomy' => $taxonomy,
                'title_li' => '',
                'depth' => 1,
                'echo' => false,
                'show_option_none' => '',
            ));



            if ( !empty($portfolio_categories) ) {

                $archive_class = is_post_type_archive(Custom_Post_Type_Plus_Portfolio::CUSTOM_POST_TYPE) ? 'current-cat' : '';
                ?>
                <div class="portfolio-menu">
                    <ul>
                        <li class="<?php echo esc_attr($archive_class); ?>">
                            <a href="<?php echo esc_url( get_post_type_archive_link(
                                Custom_Post_Type_Plus_Portfolio::CUSTOM_POST_TYPE) ); ?>"><?php echo __('view all', 'alpenhouse');?></a>
                        </li>
                        <?php
                        echo $portfolio_categories;

                        ?>
                    </ul>
                </div>
            <?php }
        }
    }
endif;

if(!function_exists('alpenhouse_post_navigation')){
    function alpenhouse_post_navigation(){

        $closest_posts = array(
            'next' => '',
            'previous' => '');
        if(get_next_post()){
            $closest_posts['next'] = get_next_post()->ID;
        }
        if(get_previous_post()){
            $closest_posts['previous'] = get_previous_post()->ID;
        }

        the_post_navigation(array(
            'next_text' => '<span class="meta-nav" aria-hidden="true"><i class="lnr lnr-chevron-right-circle"></i></span> ' .
                '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'alpenhouse' ) . '</span> ' .
                '<div class="post-thumbnail">'.get_the_post_thumbnail($closest_posts['next'], 'alpenhouse-post-thumbnail-cropped').'</div>' .
                '<span class="nav-text">'.__('Next', 'alpenhouse').'</span>'.
                '<span class="post-title">%title</span>',
            'prev_text' => '<span class="meta-nav" aria-hidden="true"><i class="lnr lnr-chevron-left-circle"></i></span> ' .
                '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'alpenhouse' ) . '</span> ' .
                '<div class="post-thumbnail">'.get_the_post_thumbnail($closest_posts['previous'], 'alpenhouse-post-thumbnail-cropped').'</div>'.
                '<span class="nav-text">'.__('Previous', 'alpenhouse').'</span>'.
                '<span class="post-title">%title</span>'
        ));
    }
}
