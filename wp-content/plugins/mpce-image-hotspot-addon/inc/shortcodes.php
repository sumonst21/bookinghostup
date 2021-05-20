<?php
if (!defined('ABSPATH')) exit;

$mpceIHSShortcodeFunctions = array(
		'mpce_image_hotspot' => 'mpceIHSShortcode',
		'mpce_hotspot' => 'mpceIHSHotspotShortcode',
);

foreach ($mpceIHSShortcodeFunctions as $sortcode_name => $function_name) {
	add_shortcode($sortcode_name, $function_name);
}

function mpceIHSRunShortcodesBeforeAutop($content) {
	global $shortcode_tags, $mpceIHSShortcodeFunctions;
	// Back up current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	remove_all_shortcodes();
	foreach ($mpceIHSShortcodeFunctions as $sortcode_name => $function_name) {
		add_shortcode($sortcode_name, $function_name);
	}
	// Do the shortcode (only the [motopress shortcodes] are registered)
	$content = do_shortcode($content);
	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	return $content;
}
add_filter('the_content', 'mpceIHSRunShortcodesBeforeAutop', 8);

$mpceIHSCommonSettings = array();

function mpceIHSShortcode($attrs, $content = null, $shortcodeName) {
	global $mpceIHSCommonSettings;
	$mp_style_classes = '';
	$margin = '';
	$output = '';
	$classWrap = '';
	$dataAttrs = '';
	$imgTitle = '';

	$defaultAttrs = array(
			'img' => '',
			'common_plus_color' => '#fff',
			'common_hotspot_color' => '',
			'common_hotspot_custom_color' => '',
			'common_hotspot_size' => 'normal',
			'common_tip_position' => 'top',
			'common_tip_show' => 'hover',
			'common_tip_theme' => 'tooltipster-default',
			'common_custom_font_theme' => '#fff',
			'common_custom_bg_theme' => '#444',
    );
	$mpceActive = is_plugin_active('motopress-content-editor/motopress-content-editor.php') || is_plugin_active('motopress-content-editor-lite/motopress-content-editor.php');
	if ($mpceActive) $defaultAttrs = MPCEShortcode::addStyleAtts($defaultAttrs);
	extract(shortcode_atts($defaultAttrs, $attrs));

	
	wp_enqueue_style('mpce-ihs-style');
	
	wp_enqueue_script('mpce-ihs-script');

	$mpceIHSCommonSettings = shortcode_atts($defaultAttrs, $attrs);

	if (isset($img) && !empty($img)) {
		$img = (int) $img;
		$attachment = get_post($img);
		if (!empty($attachment) && $attachment->post_type === 'attachment') {
			if (wp_attachment_is_image($img)) {
				$imgSrc = wp_get_attachment_image_src( $img, "full" );
				$imgSrc = ($imgSrc && isset($imgSrc[0])) ? $imgSrc[0] : false;
				$imgTitle = $attachment->post_title;
			} else {
				$imgSrc = MPCE_IHS_PLUGIN_DIR_URL . 'assets/images/no-image.png?ver=' . MPCE_IHS_VERSION;
			}
		} else {
			$imgSrc = MPCE_IHS_PLUGIN_DIR_URL . 'assets/images/no-image.png?ver=' . MPCE_IHS_VERSION;
		}
	} else {
		$imgSrc = MPCE_IHS_PLUGIN_DIR_URL . 'assets/images/no-image.png?ver=' . MPCE_IHS_VERSION;
	}

	$img = '<img';
	$img .= ' src="' . $imgSrc  . '" ';
	$img .= ' alt="' . $imgTitle . '" />';
	$mpceClasses = '';
	if ($mpceActive) {
		if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
		$mpceClasses = MPCEShortcode::getBasicClasses('mpce_image_hotspot') . MPCEShortcode::getMarginClasses($margin) . $mp_style_classes;
		if (method_exists('MPCEShortcode', 'handleCustomStyles')) {
			$mpceClasses .= MPCEShortcode::handleCustomStyles($mp_custom_style, $shortcodeName);
		}
	}

    if ( !isContentEditor() ) {
        $classWrap .= ' mpce-hotspot-shortcode-img-wrapprer';
    }
	$output .= '<div class="mpce-hotspot-img-wrapprer ' . $classWrap . ' ' . $mpceClasses . '" ' . $dataAttrs . '>' . $img .
				'	<div class="mpce-hotspot-wrapprer" >' . do_shortcode($content) . '</div>' ;
	if (isContentEditor()) {
		$output .= '<div class="mpce-ihs-coords mpce-ihs-hidden" ></div>';
	}
	$output .= '</div>';

	return $output;
}

function mpceIHSHotspotShortcode($attrs, $content = null) {
	global $mpceIHSCommonSettings;
	$options = array();
    $defaultAttrs = array(
        'tooltip' => '',
		'pos_x' => '50',
		'pos_y' => '50',
        'plus_color' => '',
        'hotspot_color' => '',
        'hotspot_custom_color' => '',
        'hotspot_size' => '',
        'tip_position' => 'inherit',
        'tip_show' => 'inherit',
		'tip_theme' => 'inherit',
		'custom_font_theme' => '#fff',
		'custom_bg_theme' => '#444',
    );

	$customAttrs = shortcode_atts($defaultAttrs, $attrs);

	/*Tip settings*/
	$options['tooltip'] = $customAttrs['tooltip'];
	$options['pos_x'] = $customAttrs['pos_x'];
	$options['pos_y'] = $customAttrs['pos_y'];

	/*Hotspot*/
	$options['hotspot_class'] = '';
	if( $customAttrs['hotspot_color'] === '' || $customAttrs['hotspot_color'] === 'inherit' ){
		if( $mpceIHSCommonSettings['common_hotspot_color'] !== 'custom' && $mpceIHSCommonSettings['common_hotspot_color'] !== '' ){
			$options['hotspot_class'] = $mpceIHSCommonSettings['common_hotspot_color'];
			$options['hotspot_color'] = '';
			$options['plus_color'] = '';
		} else {
			$options['hotspot_class'] = '';
			$options['hotspot_color'] = $mpceIHSCommonSettings['common_hotspot_custom_color'];
			$options['plus_color'] = $mpceIHSCommonSettings['common_plus_color'];
		}
	} else if( $customAttrs['hotspot_color'] === 'custom' ){
		$options['hotspot_class'] = '';
		$options['hotspot_color'] = $customAttrs['hotspot_custom_color'];
		$options['plus_color'] = $customAttrs['plus_color'];
	} else {
		$options['hotspot_class'] = $customAttrs['hotspot_color'];
		$options['hotspot_color'] = '';
		$options['plus_color'] = '';
	}

	if( $customAttrs['hotspot_size'] === '' || $customAttrs['hotspot_size'] === 'inherit' ){
		$options['hotspot_size'] = $mpceIHSCommonSettings['common_hotspot_size'];
	} else {
		$options['hotspot_size'] = $customAttrs['hotspot_size'];
	}

	/*Tips*/
	if( $customAttrs['tip_position'] === '' || $customAttrs['tip_position'] === 'inherit' ){
		$options['tip_position'] = $mpceIHSCommonSettings['common_tip_position'];
	} else {
		$options['tip_position'] = $customAttrs['tip_position'];
	}

	if( $customAttrs['tip_theme'] === '' || $customAttrs['tip_theme'] === 'inherit' ){
		$options['tip_theme'] = $mpceIHSCommonSettings['common_tip_theme'];
		if( $mpceIHSCommonSettings['common_tip_theme'] === 'custom'){
			$options['custom_font_theme'] = $mpceIHSCommonSettings['common_custom_font_theme'];
			$options['custom_bg_theme'] = $mpceIHSCommonSettings['common_custom_bg_theme'];
		}
	} else {
		$options['tip_theme'] = $customAttrs['tip_theme'];
		if( $customAttrs['tip_theme'] === 'custom'){
			$options['custom_font_theme'] = $customAttrs['custom_font_theme'];
			$options['custom_bg_theme'] = $customAttrs['custom_bg_theme'];
		}
	}

	if( $customAttrs['tip_show'] === '' || $customAttrs['tip_show'] === 'inherit' ){
		$options['tip_show'] = $mpceIHSCommonSettings['common_tip_show'];
	} else {
		$options['tip_show'] = $customAttrs['tip_show'];
	}
	/*end Tips*/

	$dataAttr = '';
	$stylePoint = '';
	$styleTooltip = '';
	$output = '';

	$dataAttr .= " data-position='" . $options['tip_position'] . "' ";

	if( $options['tip_theme'] === 'custom' ){
		$pref =  preg_replace( '/#|,|\(|\)|\s|\./','', $options['custom_bg_theme'] . $options['custom_font_theme']);
		$className = 'mpce-custom' . $pref;
		$dataAttr .= " data-class='" . $className . "' ";
		$dataAttr .= " data-color='" . $options['custom_font_theme'] . "' ";
		$dataAttr .= " data-color_bg='" . $options['custom_bg_theme'] . "' ";
	} else {
		$dataAttr .= " data-theme='" . $options['tip_theme'] . "' ";
	}

	$dataAttr .= " data-show='" . $options['tip_show'] . "' ";

	$hotspot_color = $options['hotspot_color'];

	$hotspotClass = "hotspot-" . $options['hotspot_size'];

	$hotspot_size = mpceIHSGetItemSize( $options['hotspot_size'] );

	$stylePulse = '';
	$styleTooltip .= " top: calc(" . $options['pos_y'] . "% - " . ($hotspot_size / 2 + 10) . "px);"
				. " left:  calc(" . $options['pos_x'] . "% - " . ($hotspot_size / 2 + 10) . "px);"
				. " width: " . ($hotspot_size + 20) . "px;"
				. " height:" . ($hotspot_size + 20) . "px;";

	if( $options['hotspot_class'] ==='' ){
		$stylePoint .= " border-radius: 50%;"
				. " color: " . $options['plus_color'] . ";"
				. " background: " . $hotspot_color . ";";

		$stylePulse = " border: 3px solid " . $hotspot_color . ";"
				." -webkit-box-shadow: inset 0px 0px 15px 10px " . $hotspot_color . ";"
				." -ms-box-shadow: inset 0px 0px 15px 10px " . $hotspot_color . ";"
				." -moz-box-shadow: inset 0px 0px 15px 10px " . $hotspot_color . ";"
				." box-shadow: inset 0px 0px 15px 10px " . $hotspot_color . ";";
	}

	if (isContentEditor()) {
		$output .= "<div class='mpce-tooltip' style='" . $styleTooltip . "'>"
				. "		<div class='mpce-hotspot'  >"
				. "			<div class='" . $hotspotClass . " hotspot-tip " . $options['hotspot_class'] . "'  " . $dataAttr . " style='" . $stylePoint . "' >+<div class='mpce-tooltip-text' style='display:none'>" . $content . "</div></div>"
				. "			<div class='pulse-class ' data-color='" . $hotspot_color . "' data-class='" . $options['hotspot_class'] . "' >&#32;</div>" //add here pulse
				. "</div></div>";
	} else {
		$output .= "<div class='mpce-tooltip' style='" . $styleTooltip . "'>"
				."		<div class='mpce-hotspot'>"
				."			<div class='" . $hotspotClass . " hotspot-tip " . $options['hotspot_class'] . "'  " . $dataAttr . " style='" .$stylePoint . "' >+<div class='mpce-tooltip-text' style='display:none'>" . $content . "</div></div>"
				."			<div class='pulse " . $options['hotspot_class'] . "' style='" . $stylePulse . "'>&#32;</div>"
				."</div></div>";
	}
    return $output;
}

function mpceIHSGetItemSize( $val ){

	switch ($val) {
		case  'small':
			$size = 20;
			break;
		case 'normal':
			$size = 30;
			break;
		case 'big':
			$size = 40;
			break;
		default:
			$size = 30;
			break;
	}

	return $size;
}

