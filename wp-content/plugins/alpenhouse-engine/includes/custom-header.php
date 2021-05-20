<?php

add_action('alpenhouse_header_before_content', 'alpenhouse_header_before_content_action');
if(!function_exists('alpenhouse_header_before_content_action')){
    function alpenhouse_header_before_content_action(){
        if(is_404()){
            ?>
            <div class="site-header-wrapper"  style="background-image: url('<?php echo esc_url( get_theme_mod('alpenhouse_not_found_image', get_template_directory_uri().'/img/404_header.jpg') ); ?>')">
                <div class="wrapper">
            <?php
            return;
        }

        if(class_exists('Alpenhouse_Engine')){
            $header_block_type = get_field_object('header-block-type');
            if ($header_block_type) {
                switch ($header_block_type['value']) {
                    case 'default': {
                        ?>
                            <div class="site-header-wrapper"  style="background-image: url('<?php alpenhouse_header_image(); ?>')">
                                <div class="header-overlay"></div>
                        <?php
                        break;
                    }
                    case 'image-block': {
                        $image_block_object = get_field_object('image-block');
                        if ($image_block_object) {
                            $image_block = $image_block_object['value'];
                            if ($image_block) {

                                $background_style = array();
                                $foreground_style = array();
                                $image_block_background_image_src = false;

                                //background layer
                                $image_block_image_obj = $image_block['image'];
                                if ($image_block_image_obj) {
                                    $image_block_image_id = $image_block_image_obj['ID'];
                                    if ($image_block_image_id) {
                                        $image_block_background_image_src = wp_get_attachment_image_src( $image_block_image_id, 'alpenhouse-large' )[0];
                                        $background_style[] = 'background-image:url(' . $image_block_background_image_src . ')';
                                        $background_style['background-repeat'] = 'background-repeat:no-repeat';
                                    }
                                }
                                if ( $image_block['background-color'] ) {
                                    $background_style[] = 'background-color:' . esc_attr( $image_block['background-color'] );
                                }
                                if ( $image_block['image-preset'] && $image_block_background_image_src ) {
                                    switch ( $image_block['image-preset'] ) {
                                        case 'fill':
                                            $background_style[] = 'background-size:cover';
                                            break;
                                        case 'fit':
                                            $background_style[] = 'background-size:contain';
                                            break;
                                        case 'repeat':
                                            $background_style['background-repeat'] = 'background-repeat:repeat';
                                            $background_style[] = 'background-size:unset';
                                            break;
                                    }
                                }
                                if ( empty( $image_block['image-scroll'] ) && $image_block['image-scroll'] === false ) {
                                    $background_style[] = 'background-attachment:fixed';
                                    $background_fixed_image = '<div class="fixed-background" style="'.esc_attr(implode(';', $background_style)).'"></div>';
                                }
                                //foreground layer
                                if ( $image_block['overlay-color'] ) {
                                    $overlay_color = $image_block['overlay-color'];
                                    $overlay_opacity = intval($image_block['overlay-opacity']);

                                    if ( $overlay_opacity ) {
                                        list($r, $g, $b) = sscanf($overlay_color, "#%02x%02x%02x");
                                        $background_rgba = 'rgba('.$r.','.$g.','.$b.','.($overlay_opacity / 100).')';
                                        $foreground_style[] = 'background-color:' . esc_attr( $background_rgba );
                                    } else {
                                        $foreground_style[] = 'background-color:' . esc_attr( $overlay_color );
                                    }
                                }
                            }
                        }
                        ?>
                        <div class="site-header-wrapper custom-image-bg"
                            <?php
                                if (!isset($background_fixed_image)){ ?>
                                    style="<?php echo esc_attr(implode(';', $background_style)); ?>"
                            <?php
                                }
                            ?>
                        >
                        <?php
                            if(isset($background_fixed_image)){
                                echo $background_fixed_image;
                            }

                            if (!empty($foreground_style)) { ?>
                                <div class="header-overlay" style="<?php echo esc_attr(implode(';', $foreground_style)); ?>"></div>
                        <?php }
                        break;
                    }
                    case 'slider-block': {

                        $foreground_style = array();
                        $slider_block_object = get_field_object('slider-block');
                        $slick_atts = array();
                        if ($slider_block_object) {
                            $slider_block = $slider_block_object['value'];
                            if ($slider_block) {
                                //foreground layer
                                if ( $slider_block['overlay-color'] ) {
                                    $overlay_color = $slider_block['overlay-color'];
                                    $overlay_opacity = intval($slider_block['overlay-opacity']);

                                    if ( $overlay_opacity ) {
                                        list($r, $g, $b) = sscanf($overlay_color, "#%02x%02x%02x");
                                        $background_rgba = 'rgba('.$r.','.$g.','.$b.','.($overlay_opacity / 100).')';
                                        $foreground_style[] = 'background-color:' . esc_attr( $background_rgba );
                                    } else {
                                        $foreground_style[] = 'background-color:' . esc_attr( $overlay_color );
                                    }
                                }

                                //slick atts
                                $slick_atts = array(
                                    'autoplaySpeed' => intval($slider_block['slideshow-speed'])*1000,
                                    'autoplay' => $slider_block['automatic-slideshow'],
                                    'speed' => intval($slider_block['slideshow-animation-speed'])*1000,
                                );
                                $slick_slide_effect = $slider_block['slideshow-effect'];
                                if($slick_slide_effect == 'fade'){
                                    $slick_atts['fade'] = true;
                                }
                            }

                        }
                        ?>
                        <div class="site-header-wrapper">
                        <div class="main-slider-images" data-slick='<?php echo esc_attr(json_encode($slick_atts));?>'>
                            <?php
                            if ( have_rows( 'slider-block' ) ) {
                                while( have_rows('slider-block') ):
                                    the_row();
                                    if( have_rows( 'slides' ) ) {
                                        while( have_rows('slides') ):
                                            the_row();
                                            $slide_image_obj = get_sub_field('slide-image');
											if ($slide_image_obj) {
												$slide_image_id = $slide_image_obj['ID'];
												if ($slide_image_id) {
												?>
												<div class="slider-item">
													<?php
														add_filter( 'wp_calculate_image_srcset_meta', 'alpenhouse_engine_wp_calculate_image_srcset_meta', 10, 4 );
                                                        echo wp_get_attachment_image( $slide_image_id, 'alpenhouse-large', false);
                                                        remove_filter( 'wp_calculate_image_srcset_meta', 'alpenhouse_engine_wp_calculate_image_srcset_meta', 10, 4 );
													?>
												</div>
												<?php
												}
                                            }
                                        endwhile;
                                    }
                                endwhile;
                            }
                            ?>
                        </div>
                        <?php if (!empty($foreground_style)) { ?>
                            <div class="header-overlay" style="<?php echo esc_attr(implode(';', $foreground_style)); ?>"></div>
                        <?php }
                        break;
                    }
                    case 'video-block': {
                        $video_block_obj = get_field_object('video-block');
						$video_block = $video_block_obj['value'];

                        $video_block_background_image = isset($video_block['background-image'])?$video_block['background-image']:'';
                        $video_block_video = $video_block['video-source-mp4'];
                        $video_block_video_loop = $video_block['video-loop']?'loop':'';
                        $video_block_video_muted = $video_block['muted']?'muted':'false';
                        $video_block_overlay_color = $video_block['overlay-color'];
                        $video_block_overlay_color_opacity = $video_block['overlay-opacity'];

                        list($r, $g, $b) = sscanf($video_block_overlay_color, "#%02x%02x%02x");
                        $background_rgba = 'rgba('.$r.','.$g.','.$b.','.($video_block_overlay_color_opacity/100).')';
                        ?>
                        <div class="site-header-wrapper full-height video-bg">
                            <?php
                                if($video_block_background_image!=''){
                                    $video_block_background_image_ID = $video_block_background_image['ID'];
                                    if($video_block_background_image_ID){
                                        echo wp_get_attachment_image($video_block_background_image_ID, 'alpenhouse-large', false, array('class'=>'back'));
                                    }
                                }
                            ?>
                            <video autoplay="true" <?php echo esc_attr($video_block_video_loop)." ".esc_attr($video_block_video_muted);?>>
                                <source src="<?php echo esc_url($video_block_video);?>">
                            </video>
                            <div class="header-overlay" style="background: <?php echo esc_attr($background_rgba);?>"></div>
                            <div class="slide-down">
                                <a href="#content"><i class="fa fa-angle-down"></i></a>
                            </div>
                        <?php
                        break;
                    }
                    case 'slider-block-aligned': {

                        $foreground_style = array();
                        $slider_block_object = get_field_object('slider-block-aligned');
                        $slick_atts = array();
                        if ($slider_block_object) {
                            $slider_block = $slider_block_object['value'];
                            if ($slider_block) {
                                //foreground layer

                                if ( $slider_block['background-color'] ) {
                                    $overlay_color = $slider_block['background-color'];
                                    $overlay_opacity = intval($slider_block['background-opacity']);

                                    if ( $overlay_opacity ) {
                                        list($r, $g, $b) = sscanf($overlay_color, "#%02x%02x%02x");
                                        $background_rgba = 'rgba('.$r.','.$g.','.$b.','.($overlay_opacity / 100).')';
                                        $foreground_style[] = 'background-color:' . esc_attr( $background_rgba );
                                    } else {
                                        $foreground_style[] = 'background-color:' . esc_attr( $overlay_color );
                                    }
                                }

                                //slick atts
                                $slick_atts = array(
                                    'autoplaySpeed' => intval($slider_block['slideshow-speed'])*1000,
                                    'autoplay' => $slider_block['automatic-slideshow'],
                                    'speed' => intval($slider_block['slideshow-animation-speed'])*1000,
                                );
                                $slick_slide_effect = $slider_block['slideshow-effect'];
                                if($slick_slide_effect == 'fade'){
                                    $slick_atts['fade'] = true;
                                }
                            }

                        }
                        ?>
                        <?php if (!empty($foreground_style)) { ?>
                            <div class="site-header-wrapper aligned" style="<?php echo esc_attr(implode(';', $foreground_style)); ?>">
                        <?php }else{
                            ?>
                            <div class="site-header-wrapper aligned">
                            <?php
                        }
                        ?>

                        <div class="main-slider-images-center wrapper" data-slick='<?php echo esc_attr(json_encode($slick_atts));?>'>
                            <?php
                            if ( have_rows( 'slider-block-aligned' ) ) {
                                while( have_rows('slider-block-aligned') ):
                                    the_row();
                                    if( have_rows( 'slides' ) ) {
                                        while( have_rows('slides') ):
                                            the_row();
                                            $slide_image_obj = get_sub_field('slide-image');
											if ($slide_image_obj) {
												$slide_image_id = $slide_image_obj['ID'];
												if ($slide_image_id) {
												?>
												<div class="slider-item">
													<?php
														add_filter( 'wp_calculate_image_srcset_meta', 'alpenhouse_engine_wp_calculate_image_srcset_meta', 10, 4 );
                                                        echo wp_get_attachment_image( $slide_image_id, 'alpenhouse-large', false);
                                                        remove_filter( 'wp_calculate_image_srcset_meta', 'alpenhouse_engine_wp_calculate_image_srcset_meta', 10, 4 );
													?>
												</div>
												<?php
												}
                                            }
                                        endwhile;
                                    }
                                endwhile;
                            }
                            ?>
                        </div>
                        <?php
                        break;
                    }
                }
            }else{
                ?>
                <div class="site-header-wrapper" style="background-image: url('<?php alpenhouse_header_image(); ?>')">
                    <div class="header-overlay"></div>
                <?php
            }
        }else{
            ?>
                <div class="site-header-wrapper" style="background-image: url('<?php alpenhouse_header_image(); ?>')">
                <div class="header-overlay"></div>
            <?php
        }
    ?>

        <div class="wrapper">
    <?php


    }
}

add_action('alpenhouse_header_content', 'alpenhouse_header_content_action');
if(!function_exists('alpenhouse_header_content_action')){
    function alpenhouse_header_content_action(){

        if(class_exists('Alpenhouse_Engine')){
            $header_block_type = get_field_object('header-block-type');
            if($header_block_type){
                switch ($header_block_type['value']){
                case 'default':{
                    alpenhouse_header_content_default();
                    break;
                }
                case 'slider-block':{
                    if ( have_rows( 'slider-block' ) ) {
                        while( have_rows('slider-block') ):
                            the_row();
                            $slick_atts = array(
                                'autoplaySpeed' => intval(get_sub_field('slideshow-speed'))*1000,
                                'autoplay' => get_sub_field('automatic-slideshow'),
                                'speed' => intval(get_sub_field('slideshow-animation-speed'))*1000,
                            );
                            $slick_slide_effect = get_sub_field('slideshow-effect');
                            if($slick_slide_effect == 'fade'){
                                $slick_atts['fade'] = true;
                            }
                    ?>
                    <div id="main-slider-dots"></div>
                        <div class="main-slider-content"
                            data-slick='<?php echo esc_attr(json_encode($slick_atts));?>'>
                        <?php

                            if( have_rows( 'slides' ) ) {
                                while( have_rows('slides') ):
                                    the_row();

                                    $slide_text = get_sub_field('slide-text');
                                    if ($slide_text) {
                                    ?>
                                     <div class="slider-item">
                                        <div class="page-header-custom content-wrapper boxed">
                                            <div class="custom-header-content">
                                                <?php
                                                    echo $slide_text;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                endwhile;
                            }

                        ?>
                        </div>
                    <?php
                        endwhile;
                    }
                    break;
                }
                case 'image-block':{
                    $image_block = get_field_object('image-block');
                    ?>
                    <div class="page-header-custom content-wrapper boxed">
                        <div class="custom-header-content">
                        <?php
                            echo $image_block['value']['text'];
                        ?>
                        </div>
                    </div>
                    <?php
                    break;
                }
                case 'video-block':{
                    $video_block_obj = get_field_object('video-block');
                    $video_block = $video_block_obj['value'];
                    ?>
                    <div class="page-header-custom content-wrapper boxed">
                        <div class="custom-header-content">
                        <?php
                            echo $video_block['text'];
                        ?>
                        </div>
                    </div>
                    <?php
                    break;
                }
                case 'slider-block-aligned':{
                     $slider_block = get_field_object('slider-block-aligned')['value'];
                    if ( have_rows( 'slider-block-aligned' ) ) {
                        while( have_rows('slider-block-aligned') ):
                            the_row();
                            $overlay_style = array();
                            if ( $slider_block['overlay-color'] ) {
                                    $overlay_color = $slider_block['overlay-color'];
                                    $overlay_opacity = intval($slider_block['overlay-opacity']);

                                    if ( $overlay_opacity ) {
                                        list($r, $g, $b) = sscanf($overlay_color, "#%02x%02x%02x");
                                        $background_rgba = 'rgba('.$r.','.$g.','.$b.','.($overlay_opacity / 100).')';
                                        $overlay_style[] = 'background-color:' . esc_attr( $background_rgba );
                                    } else {
                                        $overlay_style[] = 'background-color:' . esc_attr( $overlay_color );
                                    }
                            }

                    ?>
                        <div id="main-slider-fixed-dots"></div>
                        <div class="main-slider-fixed-content" <?php if (!empty($overlay_style)){
                            ?>
                            style="<?php echo esc_attr(implode(';', $overlay_style)); ?>"
                            <?php
                        }?>>
                        <?php
                                    $slide_text = get_sub_field('slider-block-content');
                                    if ($slide_text) {
                                    ?>
                                        <div class="custom-header-content">
                                            <?php
                                                echo $slide_text;
                                            ?>
                                        </div>
                                    <?php
                                    }


                        ?>
                        </div>
                    <?php
                        endwhile;
                    }
                    break;
                }
            }
            }else{
                alpenhouse_header_content_default();
            }
        }else{
            alpenhouse_header_content_default();
        }


    }
}

function alpenhouse_header_content_default() {

	$page_template = '';

	$header_content_classes = array('page-header-custom', 'content-wrapper', 'boxed');
//	$header_content_classes = apply_filters('alpenhouse_custom_header_content_classes', $header_content_classes );
//	$header_content_classes = array_unique($header_content_classes);

	?>
	<div class="<?php echo esc_attr( implode(' ', $header_content_classes) ); ?>">
		<?php
		if ( is_home() && ! is_front_page() ) {
		?>
			<header class="page-header">
				<p class="page-title"><?php echo apply_filters( 'the_title', get_the_title( get_option( 'page_for_posts' ) ) ); ?></p>
			</header><!-- .page-header -->
		<?php
		} elseif ( is_single() || is_page()) {
		?>
			<header class="page-header">
				<?php the_title( '<p class="page-title">', '</p>' ); ?>
			</header><!-- .page-header -->
		<?php
		} elseif ( is_search() ) {
		?>
			<header class="page-header">
				<p class="page-title">
					<?php
					printf( esc_html__( 'Search Results for: %s', 'alpenhouse-engine' ), '<span>"' . get_search_query() . '"</span>' );
					?>
				</p>
			</header>
		<?php
		}  elseif ( is_archive() ) {
		    if(function_exists('is_woocommerce') && is_woocommerce()){
		        ?>
		        <header class="page-header">
		            <p class="page-title">
				        <?php woocommerce_page_title(); ?>
				    </p>
                </header><!-- .page-header -->
		        <?php
		    }else{
		        ?>
		        <header class="page-header">
                    <?php
                        the_archive_title('<p class="page-title">', '</p>');
                        the_archive_description( '<div class="archive-description">', '</div>' );
                    ?>
                </header>
		        <?php
		    }
		} elseif ( is_404() ) {

		    do_action('alpenhouse_error_page_content');
		}
//		if(!is_404()){
//		    alpenhouse_breadcrumbs();
//		}
		?>
	</div><!-- .page-header-custom -->
	<?php
}

add_action('alpenhouse_header_after_content', 'alpenhouse_header_after_content_action');
if(!function_exists('alpenhouse_header_after_content_action')){
    function alpenhouse_header_after_content_action(){
        ?>
                </div><!-- .wrapper -->
            </div><!-- .site-header-wrapper -->
        <?php
    }
}

function alpenhouse_engine_wp_calculate_image_srcset_meta($image_meta, $size_array, $image_src, $attachment_id) {
    return false;
}
