<?php
/**
 * Fired when the plugin is being uninstalled.
 *
 * @link       https://primecare.com.hk/
 * @since      1.0.0
 *
 * @package     Primecare_Dr_Slick
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once(dirname(__FILE__). '/inc/loader.php');
