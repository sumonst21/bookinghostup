<?php

add_shortcode('alpenhouse_pricing', 'alpenhouse_pricing_callback');
function alpenhouse_pricing_callback($atts, $content = null){

    $atts = shortcode_atts(array(
        'title' => '',
        'subtitle'  => '',
        'highlighted'  => false,
        'currency' => '$',
        'price' => 1,
        'period' => '',
        'button-text' => '',
        'button-link' => ''
    ), $atts, 'alpenhouse_pricing');
    $highlighted = $atts['highlighted']=='yes'? 'highlighted': '';
    $html = '';
    ob_start();
    do_action('alpenhouse_before_pricing', $highlighted);
    ?>

    <span class="pricing-title"><?php echo $atts['title']?></span>
    <span class="pricing-subtitle"><?php echo $atts['subtitle']?></span>
    <div class="pricing">
        <sup><?php echo $atts['currency'];?></sup>
        <span class="price"><?php echo $atts['price'];?></span>
        <sub><?php echo $atts['period'];?></sub>
    </div>
    <div class="pricing-options">
        <?php
            echo $content;
        ?>
    </div>
    <?php
    if(!empty($atts['button-text'])){
        ?>
        <div class="button-wrapper">
            <a class="<?php echo $atts['highlighted']=='yes'? 'button': 'button secondary'; ?>" href="<?php echo $atts['button-link'];?>"><?php echo $atts['button-text'];?></a>
        </div>
        <?php
    }

    do_action('alpenhouse_after_pricing');

    $html = ob_get_clean();
    return $html;
}

add_action('alpenhouse_before_pricing', 'alpenhouse_before_pricing_callback', 10, 1);

function alpenhouse_before_pricing_callback($highlighted){
    ?>
    <div class="pricing-item <?php echo $highlighted;?>">
    <?php
}

add_action('alpenhouse_after_pricing', 'alpenhouse_after_pricing_callback', 10, 0);

function alpenhouse_after_pricing_callback(){
    ?>
    </div>
    <?php
}

add_shortcode('alpenhouse_icon_text', 'alpenhouse_icon_text_callback');
function alpenhouse_icon_text_callback($atts, $content=null){
    $atts = shortcode_atts(array(
        'class' => '',
    ), $atts, 'alpenhouse_icon_text');

    $html = '';
    ob_start();
    ?>
    <div class="icon-text">
        <i class="<?php echo $atts['class'];?>"></i>
        <div class="text">
            <?php echo $content;?>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}


if(!Alpenhouse_Engine::is_alpenhouseengine_theme()){
    add_filter('the_content', 'alpenhouse_engine_custom_header_default');
}


function alpenhouse_engine_custom_header_default($content){

    $header_block_type = get_field_object('header-block-type');
	$custom_header = '';
    if ( isset( $header_block_type['value'] ) ) {
        $header_block_type_value = $header_block_type['value'];

        if ($header_block_type_value && $header_block_type_value != 'default') {
            ob_start();

            do_action('alpenhouse_header_before_content');
            do_action('alpenhouse_header_content');
            do_action('alpenhouse_header_after_content');

            $custom_header = ob_get_clean();
        }
    }

    return $custom_header . $content;
}

//post-formats
add_filter( 'cptp_portfolio_register_post_type', 'alpenhouse_portfolio_register_post_type', 10, 1);
function alpenhouse_portfolio_register_post_type($args) {

	$args['supports'][] = 'post-formats';
	return $args;
}

add_action( 'load-post.php',     'alpenhouse_post_format_support_filter' );
add_action( 'load-post-new.php', 'alpenhouse_post_format_support_filter' );
add_action( 'load-edit.php',     'alpenhouse_post_format_support_filter' );

function alpenhouse_post_format_support_filter() {

    $screen = get_current_screen();

    // Bail if not on the portfolio screen.
    if ( empty( $screen->post_type ) ||  $screen->post_type !== 'cptp-portfolio' )
        return;

    add_theme_support( 'post-formats', array( 'video' ) );
}
