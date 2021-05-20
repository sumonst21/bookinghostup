<?php
/*
Plugin Name: MotoPress Image Hotspot Addon
Plugin URI: https://motopress.com/
Description: Create an image with interactive hotspots.
Version: 1.3.1.1
Author: MotoPress
Author URI: https://motopress.com/
Text Domain: mpce-ihs
License: GPL2 or later
*/


global $wp_version;
if (version_compare($wp_version, '3.9', '<') && isset($network_plugin)) {
	define('MPCE_IHS_PLUGIN_FILE', $network_plugin);
} else {
	define('MPCE_IHS_PLUGIN_FILE', __FILE__);
}
define('MPCE_IHS_PLUGIN_DIR', trailingslashit(plugin_dir_path(MPCE_IHS_PLUGIN_FILE)));

if( !function_exists('isContentEditor')){
    function isContentEditor() {
        return method_exists('MPCEShortcode', 'isContentEditor') && MPCEShortcode::isContentEditor();
    }
}

//require_once MPCE_IHS_PLUGIN_DIR . 'inc/license.php';
require_once MPCE_IHS_PLUGIN_DIR . 'inc/settings.php';
require_once MPCE_IHS_PLUGIN_DIR . 'inc/mpLibrary.php';
require_once MPCE_IHS_PLUGIN_DIR . 'inc/shortcodes.php';
require_once MPCE_IHS_PLUGIN_DIR . 'inc/simpleShortcodes.php';
require_once MPCE_IHS_PLUGIN_DIR . 'inc/settingsPage.php';
//require_once MPCE_IHS_PLUGIN_DIR . 'inc/EDD_MPCE_IHS_Plugin_Updater.php';

function mpceIHSLoadTextdomain() {
    load_plugin_textdomain('mpce-ihs', FALSE, MPCE_IHS_PLUGIN_NAME . '/lang/');
}
add_action('plugins_loaded', 'mpceIHSLoadTextdomain');

function mpceIHSAdminInit() {
	new EDD_MPCE_IHS_Plugin_Updater(MPCE_IHS_EDD_STORE_URL, __FILE__, array(
		'version' => MPCE_IHS_VERSION,                       // current version number
		'license' => get_option('edd_mpce_ihs_license_key'), // license key (used get_option above to retrieve from DB)
		'item_id' => MPCE_IHS_EDD_ITEM_ID,                   // id of this plugin
		'author'  => MPCE_IHS_AUTHOR                         // author of this plugin
    ));
}
//add_action('admin_init', 'mpceIHSAdminInit');

function mpceIHSEnqueueScripts() {
    wp_register_style('mpce-ihs-style', MPCE_IHS_PLUGIN_DIR_URL . 'assets/css/style.min.css', array(), MPCE_IHS_VERSION);
    

    wp_register_script('mpce-ihs-script', MPCE_IHS_PLUGIN_DIR_URL . 'assets/js/engine.min.js', array('jquery'), MPCE_IHS_VERSION, true);
    wp_register_script('mpce-ihs-admin-script', MPCE_IHS_PLUGIN_DIR_URL . 'assets/js/engine-admin.min.js', array('jquery'), MPCE_IHS_VERSION, true);
    
    if( isContentEditor() ){
        wp_enqueue_script('mpce-ihs-admin-script');

        
        wp_enqueue_style('mpce-ihs-style');
        
        wp_enqueue_script('mpce-ihs-script');
    }
}
add_action('wp_enqueue_scripts', 'mpceIHSEnqueueScripts');


function mpceIHSLicenseInit($hookSuffix) {
    global $ihsLicense;
    add_filter('admin_mpce_license_tabs', 'mpceIHSLicenseTab');
    add_action('admin_mpce_license_save-' . MPCE_IHS_PLUGIN_SHORT_NAME, array(&$ihsLicense, 'save'));
}
//add_action('admin_mpce_license_init', 'mpceIHSLicenseInit');

// Plugin Activation
function motopressIHSInstall($network_wide) {
    $autoLicenseKey = apply_filters('ihs_auto_license_key', false);
    if ($autoLicenseKey) {
        IHSLicense::setAndActivateLicenseKey($autoLicenseKey);
    }
}
//register_activation_hook(__FILE__, 'motopressIHSInstall');
