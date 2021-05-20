<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package WordPress
 * @subpackage Alpenhouse
 * @since Alpenhouse 1.0.0
 */

 // Includes the files needed for the theme updater
if ( ! class_exists( 'Alpenhouse_EDD_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

$alpenhouse_info           = wp_get_theme( get_template() );
$alpenhouse_name           = $alpenhouse_info->get( 'Name' );
$alpenhouse_slug           = get_template();
$alpenhouse_version        = $alpenhouse_info->get( 'Version' );
$alpenhouse_author         = $alpenhouse_info->get( 'Author' );
$alpenhouse_remote_api_url = $alpenhouse_info->get( 'AuthorURI' );

// Loads the updater classes
$alpenhouse_updater = new Alpenhouse_EDD_Updater_Admin(

// Config settings
	$alpenhouse_config = array(
		'remote_api_url' => $alpenhouse_remote_api_url, // Site where EDD is hosted
		'item_name'      => $alpenhouse_name, // Name of theme
		'theme_slug'     => $alpenhouse_slug, // Theme slug
		'version'        => $alpenhouse_version, // The current version of this theme
		'author'         => $alpenhouse_author, // The author of this theme
		'download_id'    => '', // Optional, used for generating a license renewal link
		'renew_url'      => '', // Optional, allows for a custom license renewal link
		'beta'           => false, // Optional, set to true to opt into beta versions
	),

	// Strings
	$alpenhouse_strings = array(
		'theme-license'             => esc_html__( 'Theme License', 'alpenhouse' ),
		'enter-key'                 => esc_html__( 'Enter your theme license key.', 'alpenhouse' ),
		'license-key'               => esc_html__( 'License Key', 'alpenhouse' ),
		'license-action'            => esc_html__( 'License Action', 'alpenhouse' ),
		'deactivate-license'        => esc_html__( 'Deactivate License', 'alpenhouse' ),
		'activate-license'          => esc_html__( 'Activate License', 'alpenhouse' ),
		'status-unknown'            => esc_html__( 'License status is unknown.', 'alpenhouse' ),
		'renew'                     => esc_html__( 'Renew?', 'alpenhouse' ),
		'unlimited'                 => esc_html__( 'unlimited', 'alpenhouse' ),
		'license-key-is-active'     => esc_html__( 'License key is active.', 'alpenhouse' ),
		'expires%s'                 => esc_html__( 'Expires %s.', 'alpenhouse' ),
		'expires-never'             => esc_html__( 'Lifetime License.', 'alpenhouse' ),
		'%1$s/%2$-sites'            => esc_html__( 'You have %1$s / %2$s sites activated.', 'alpenhouse' ),
		'license-key-expired-%s'    => esc_html__( 'License key expired %s.', 'alpenhouse' ),
		'license-key-expired'       => esc_html__( 'License key has expired.', 'alpenhouse' ),
		'license-keys-do-not-match' => esc_html__( 'License keys do not match.', 'alpenhouse' ),
		'license-is-inactive'       => esc_html__( 'License is inactive.', 'alpenhouse' ),
		'license-key-is-disabled'   => esc_html__( 'License key is disabled.', 'alpenhouse' ),
		'site-is-inactive'          => esc_html__( 'Site is inactive.', 'alpenhouse' ),
		'license-status-unknown'    => esc_html__( 'License status is unknown.', 'alpenhouse' ),
		'update-notice'             => esc_html__( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'alpenhouse' ),
		'update-available'          => wp_kses(__( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'alpenhouse' ), array( 'strong' => array(), 'a' => array( 'class' => array(),'href' => array(),'title' => array() ) )),
	)

);
