<?php
if (!defined('ABSPATH')) exit;

function mpceIHSSettingsTab($tabs) {
	$tabs[MPCE_IHS_PLUGIN_SHORT_NAME] = array(
		'label' => __('Countdown Pro', 'mpce-ihs'),
		'priority' => 10,
		'callback' => 'mpceIHSSettingsTabContent'
	);
	return $tabs;
}

function mpceIHSSettingsTabContent() {
	if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        add_settings_error(
            'mpceIHSSettings',
            esc_attr('settings_updated'),
            __('Settings saved.', 'mpce-ihs'),
            'updated'
        );
    }
	settings_errors('mpceIHSSettings', false);
	echo '<form actoin="options.php" method="POST">';
//    settings_fields('mpceIHSOptionsFields');
	do_settings_sections('mpce_ihs_options');
	echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="' . __('Save', 'mpce-ihs') . '" /></p>';
	echo '</form>';
}

function mpceIHSSettingsSave() {
	if (!empty($_POST)) {
		$settings = get_option('mpce-ihs-settings', array());
		if (isset($_POST['server_api_key'])) {
			$settings['server_api_key'] = $_POST['server_api_key'];
		}
		if (isset($_POST['browser_api_key'])) {
			$settings['browser_api_key'] = $_POST['browser_api_key'];
		}
		update_option('mpce-ihs-settings', $settings);
		wp_redirect(add_query_arg(array('page' => $_GET['page'], 'plugin' => $_GET['plugin'], 'settings-updated' => 'true'), admin_url('admin.php')));
	}
}

function mpceIHSLicenseTab($tabs) {
    global $ihsLicense;
	$tabs[MPCE_IHS_PLUGIN_SHORT_NAME] = array(
		'label' => __('Hotspot Addon', 'mpce-ihs'),
		'priority' => 10,
        'callback' => array(&$ihsLicense, 'renderPage')
	);
	return $tabs;
}