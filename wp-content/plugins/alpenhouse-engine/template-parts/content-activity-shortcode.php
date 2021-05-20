<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
        <?php
			the_post_thumbnail();
        ?>
    </a>
	<header class="entry-header">
	<?php
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	?>
	</header><!-- .entry-header -->
	<?php
		if(class_exists('Alpenhouse_Engine')){
			$activity_price = get_field('activity-price');
			$activity_period = get_field('activity-period');
			if($activity_price){
				?>
				<p class="pricing">
					<span><?php echo __('Price:', 'alpenhouse-engine');?></span>
					<span class="price"><?php echo esc_html($activity_price);?></span>
					<?php
					if($activity_period){
						?>
						<span class="period"><?php echo esc_html(' / '.$activity_period);?></span>
						<?php
					}
					?>
				</p>
				<?php
			}
		}
	?>
	<div class="entry-content">
	<?php
	the_content( sprintf(
		wp_kses(
		/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'alpenhouse-engine' ),
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
</div><!-- #post-<?php the_ID(); ?> -->