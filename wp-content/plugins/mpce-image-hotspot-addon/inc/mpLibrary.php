<?php
if (!defined('ABSPATH')) exit;

function mpceHotspoterAddonLibrary($mpceLibrary) {
    $positions = array(
        'inherit' => __('Inherit', 'mpce-ihs'),
        'top' => __('Top', 'mpce-ihs'),
        'right' => __('Right', 'mpce-ihs'),
        'bottom' => __('Bottom', 'mpce-ihs'),
        'left' => __('Left', 'mpce-ihs'),
    );

    $positionCommon = array(
        'top' => __('Top', 'mpce-ihs'),
        'right' => __('Right', 'mpce-ihs'),
        'bottom' => __('Bottom', 'mpce-ihs'),
        'left' => __('Left', 'mpce-ihs'),
    );

    $themeCommon = array(
        'tooltipster-default' => __('Dark', 'mpce-ihs'),
        'tooltipster-light' => __('Silver', 'mpce-ihs'),
        'tooltipster-noir' => __('Noir', 'mpce-ihs'),
        'tooltipster-shadow' => __('Shadow', 'mpce-ihs'),
        'tooltipster-dark' => __('Black', 'mpce-ihs'),
        'custom' => __('Custom', 'mpce-ihs'),
    );

    $theme = array(
        'inherit' => __('Inherit', 'mpce-ihs'),
        'tooltipster-default' => __('Dark', 'mpce-ihs'),
        'tooltipster-light' => __('Silver', 'mpce-ihs'),
        'tooltipster-noir' => __('Noir', 'mpce-ihs'),
        'tooltipster-shadow' => __('Shadow', 'mpce-ihs'),
        'tooltipster-dark' => __('Black', 'mpce-ihs'),
        'custom' => __('Custom', 'mpce-ihs'),
    );

    $showCommon = array(
        'hover' => __('On Hover', 'mpce-ihs'),
        'always' => __('Always', 'mpce-ihs'),
        'click' => __('On Click', 'mpce-ihs'),
    );

    $show = array(
        'inherit' => __('Inherit', 'mpce-ihs'),
        'hover' => __('On Hover', 'mpce-ihs'),
        'always' => __('Always', 'mpce-ihs'),
        'click' => __('On Click', 'mpce-ihs'),
    );

    $hotspotImgObj = new MPCEObject('mpce_image_hotspot', __('Image Hotspot', 'mpce-ihs'),  'plugins/' . MPCE_IHS_PLUGIN_NAME . '/assets/images/image-hotspot.png', array(
	    'elements' => array(
            'type' => 'group',
            'contains' => 'mpce_hotspot',
            'items' => array(
                'label' => array(
                    'default' => __('Hotspot', 'mpce-ihs'),
                ),
                'count' => 0
            ),
            'text' => __('Add New Hotspot', 'mpce-ihs'),
            'disabled' => 'false',
            'rules' => array(
                    'rootSelector' => '.mpce-tooltip', // css selector of the internal object
                    'activeSelector' => '.mpce-hotspot', // css selector of the active element
                    'activeClass' => 'active' // css class name of the active element
                    ),
            'events' => array(
                    'onActive' => array( // javascript event when item is activated
                            'selector' => '.mpce-hotspot', // css selector of the element
                            'event' => 'click' // event name
                            ),
                    'onInactive' => array( // javascript event when item is de-activated
                                    'selector' => '.mpce-hotspot', // css selector of the element
                                    'event' => 'click' // event name
                                )
                    )
        ),
		'img' => array(
				'type' => 'image',
				'label' => __('Main Image', 'mpce-ihs'),
				'default' => '',
				'description' => __('Choose an image from Media Library', 'mpce-ihs'),
				'autoOpen' => 'true'
		),
        'common_hotspot_color' => array(
            'type' => 'color-select',
            'label' => __('Hotspot Theme', 'mpce-ihs'),
            'default' => 'mp-text-color-red',
            'list' => array(
                'mp-text-color-red' => __('Red', 'mpce-ihs'),
                'mp-text-color-dark-grey' => __('Grey', 'mpce-ihs'),
                'mp-text-color-black' => __('Black', 'mpce-ihs'),
                'custom' => __('Custom', 'mpce-ihs'),
            ),
        ),
        'common_hotspot_custom_color' => array(
            'type' => 'color-picker',
            'label' => __('Hotspot Color', 'mpce-ihs'),
            'default' => '#e25441',
            'dependency' => array(
                'parameter' => 'common_hotspot_color',
                'value' => 'custom'
            )
        ),
        'common_plus_color' => array(
            'type' => 'color-picker',
            'label' => __('Hotspot Icon Color', 'mpce-ihs'),
            'default' => '#ffffff',
            'dependency' => array(
                'parameter' => 'common_hotspot_color',
                'value' => 'custom'
            )
        ),
        'common_hotspot_size' => array(
            'type' => 'radio-buttons',
            'label' => __('Hotspot Size', 'mpce-ihs'),
            'default' => 'normal',
            'list' => array(
                'small' => __('Small', 'mpce-ihs'),
                'normal' => __('Middle', 'mpce-ihs'),
                'big' => __('Large', 'mpce-ihs'),
            )
        ),
        'common_tip_theme' => array(
            'type' => 'select',
            'label' => __('Tooltip Theme', 'mpce-ihs'),
            'default' => 'tooltipster-shadow',
            'list' => $themeCommon,
        ),
        'common_custom_bg_theme' => array(
            'type' => 'color-picker',
            'label' => __('Tooltip Background Color', 'mpce-ihs'),
            'default' => '#eb002c',
            'dependency' => array(
                'parameter' => 'common_tip_theme',
                'value' => 'custom'
            )
        ),
        'common_custom_font_theme' => array(
            'type' => 'color-picker',
            'label' => __('Tooltip Font Color', 'mpce-ihs'),
            'default' => '#ffffff',
            'dependency' => array(
                'parameter' => 'common_tip_theme',
                'value' => 'custom'
            )
        ),
        'common_tip_position' => array(
            'type' => 'select',
            'label' => 'Tooltip Position',
            'default' => 'top',
            'list' => $positionCommon
        ),
        'common_tip_show' => array(
            'type' => 'select',
            'label' => 'Display',
            'default' => 'hover',
            'list' => $showCommon
        ),

    ), 50, MPCEObject::ENCLOSED, MPCEObject::RESIZE_HORIZONTAL);

    $markerObj = new MPCEObject('mpce_hotspot', __('Hotspot', 'mpce-ihs'), null, array(
        'tooltip' => array(
            'type' => 'longtext-tinymce',
            'label' => __('Description', 'mpce-ihs'),
            'default' => '<p>Lorem ipsum</p>',
            'description' => '',
            'text' => __('Edit', 'mpce-ihs'),
            'saveInContent' => 'true'
        ),
        'pos_x' => array(
            'type' => 'text',
            'label' => __('X Position, %', 'mpce-ihs'),
            'description' => __('Hover an image to display coordinates', 'mpce-ihs'),
            'default' => '50',
        ),
        'pos_y' => array(
            'type' => 'text',
            'label' => __('Y Position, %', 'mpce-ihs'),
            'description' => __('Hover an image to display coordinates', 'mpce-ihs'),
            'default' => '50',
        ),

        'hotspot_color' => array(
            'type' => 'color-select',
            'label' => __('Hotspot Theme', 'mpce-ihs'),
            'default' => 'inherit',
            'list' => array(
                'inherit' => __('Inherit', 'mpce-ihs'),
                'mp-text-color-red' => __('Red', 'mpce-ihs'),
                'mp-text-color-dark-grey' => __('Grey', 'mpce-ihs'),
                'mp-text-color-black' => __('Black', 'mpce-ihs'),
                'custom' => __('Custom', 'mpce-ihs'),
            ),
        ),
        'hotspot_custom_color' => array(
            'type' => 'color-picker',
            'label' => __('Hotspot Color', 'mpce-ihs'),
            'default' => '#e25441',
            'dependency' => array(
                'parameter' => 'hotspot_color',
                'value' => 'custom'
            )
        ),
        'plus_color' => array(
            'type' => 'color-picker',
            'label' => __('Hotspot Icon Color', 'mpce-ihs'),
            'default' => '#ffffff',
            'dependency' => array(
                'parameter' => 'hotspot_color',
                'value' => 'custom'
            )
        ),
        'hotspot_size' => array(
            'type' => 'radio-buttons',
            'label' => __('Hotspot Size', 'mpce-ihs'),
            'default' => 'inherit',
            'list' => array(
                'inherit' => __('Inherit', 'mpce-ihs'),
                'small' => __('Small', 'mpce-ihs'),
                'normal' => __('Middle', 'mpce-ihs'),
                'big' => __('Large', 'mpce-ihs'),
            )
        ),
        'tip_theme' => array(
            'type' => 'select',
            'label' => __('Tooltip Theme', 'mpce-ihs'),
            'default' => 'inherit',
            'list' => $theme,
        ),
        'custom_bg_theme' => array(
            'type' => 'color-picker',
            'label' => __('Tooltip Background Color', 'mpce-ihs'),
            'default' => '#eb002c',
            'dependency' => array(
                'parameter' => 'tip_theme',
                'value' => 'custom'
            )
        ),
        'custom_font_theme' => array(
            'type' => 'color-picker',
            'label' => __('Tooltip Font Color', 'mpce-ihs'),
            'default' => '#ffffff',
            'dependency' => array(
                'parameter' => 'tip_theme',
                'value' => 'custom'
            )
        ),
        'tip_position' => array(
            'type' => 'select',
            'label' => 'Tooltip Position',
            'default' => 'inherit',
            'list' => $positions
        ),
        'tip_show' => array(
            'type' => 'select',
            'label' => 'Display',
            'default' => 'inherit',
            'list' => $show
        ),

    ), null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE, false);

    $mpceLibrary->addObject($hotspotImgObj, MPCEShortcode::PREFIX . 'image');
    $mpceLibrary->addObject($markerObj, MPCEShortcode::PREFIX . 'image');
}
add_action('mp_library', 'mpceHotspoterAddonLibrary', 11, 1);