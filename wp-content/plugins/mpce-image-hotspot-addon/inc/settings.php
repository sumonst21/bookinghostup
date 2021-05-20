<?php
if (!defined('ABSPATH')) exit;


function mpceIHSInitGlobalSettings() {
	static $inited = false;
	if (!$inited ) {
		$inited = true;

		define('MPCE_IHS_PLUGIN_NAME', 'mpce-image-hotspot-addon');
        if(function_exists('get_plugin_data')){
            $pluginData = get_plugin_data(MPCE_IHS_PLUGIN_DIR . MPCE_IHS_PLUGIN_NAME . '.php', false, false);
        } else {
            $pluginData = getPluginData(MPCE_IHS_PLUGIN_DIR . MPCE_IHS_PLUGIN_NAME . '.php', false, false);
        }

		define('MPCE_IHS_PLUGIN_SHORT_NAME', 'mpce-ihs');
		define('MPCE_IHS_PLUGIN_DIR_NAME', basename(dirname(MPCE_IHS_PLUGIN_FILE)));
		define('MPCE_IHS_PLUGIN_DIR_URL', plugin_dir_url(MPCE_IHS_PLUGIN_DIR_NAME . '/' . basename(MPCE_IHS_PLUGIN_FILE)));

		define('MPCE_IHS_VERSION', $pluginData['Version']);
		define('MPCE_IHS_AUTHOR', $pluginData['Author']);

		//define('MPCE_IHS_LICENSE_TYPE', 'Personal');
		define('MPCE_IHS_EDD_STORE_URL', $pluginData['PluginURI']);
		define('MPCE_IHS_EDD_ITEM_NAME', $pluginData['Name']/* . ' ' . MPCE_IHS_LICENSE_TYPE*/);
		define('MPCE_IHS_EDD_ITEM_ID', 211346);
		define('MPCE_IHS_RENEW_URL', $pluginData['PluginURI'] . 'buy/');

        //global $ihsLicense;
        //$ihsLicense = new IHSLicense();
	}
}
mpceIHSInitGlobalSettings();


function getPluginData( $plugin_file, $markup = true, $translate = true ) {
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'TextDomain' => 'Text Domain',
        'DomainPath' => 'Domain Path',
        'Network' => 'Network',
        // Site Wide Only is deprecated in favor of Network.
        '_sitewide' => 'Site Wide Only',
    );
    $plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );
    if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
        _deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The %1$s plugin header is deprecated. Use %2$s instead.' ), '<code>Site Wide Only: true</code>', '<code>Network: true</code>' ) );
        $plugin_data['Network'] = $plugin_data['_sitewide'];
    }
    $plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
    unset( $plugin_data['_sitewide'] );
    if ( $markup || $translate ) {
        $plugin_data = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
    } else {
        $plugin_data['Title']      = $plugin_data['Name'];
        $plugin_data['AuthorName'] = $plugin_data['Author'];
    }

    return $plugin_data;
}
