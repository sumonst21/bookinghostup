<?php

add_action('mpce_add_simple_shortcode', 'mpceIHSAddSimpleShortcode');

function mpceIHSAddSimpleShortcode() {
	add_shortcode('mpce_image_hotspot', 'mpceIHSShortcodeSimple');
	add_shortcode('mpce_hotspot', 'mpceIHSHotspotShortcodeSimple');
}

function mpceIHSShortcodeSimple($attrs, $content = null) {
	extract(shortcode_atts(array(
		'img' => '',
    ), $attrs));

	$output = '';

	if (isset($img) && !empty($img)) {
		$img = (int)$img;
		$attachment = get_post($img);
		if (!empty($attachment) && $attachment->post_type === 'attachment') {
			if (wp_attachment_is_image($img)) {
				$imgSrc = wp_get_attachment_image_src($img, "full");
				$imgSrc = ($imgSrc && isset($imgSrc[0])) ? $imgSrc[0] : false;
				$imgTitle = $attachment->post_title;

				$output .= "<img src=\"{$imgSrc}\" alt=\"{$imgTitle}\" />";
			}
		}
	}

	$content = trim(do_shortcode($content));
	if (!empty($content)) {
		$output .= "<ul>{$content}</ul>";
	}

	return $output;
}

function mpceIHSHotspotShortcodeSimple($attrs, $content = null) {
	$output = '';

	$content = trim($content);
	if (!empty($content)) {
		$output = "<li>{$content}</li>";
	}

	return $output;
}