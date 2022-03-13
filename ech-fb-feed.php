<?php
/**
 * ECH Facebook Feed
 *
 * @link              https://www.vivideyecentre.com/
 * @since             1.0.0
 * @package           ECH_Facebook_Feed
 * @wordpress-plugin
 * Plugin Name:       ECH Facebook Feed
 * Plugin URI:        https://www.vivideyecentre.com/
 * 
 * Description:       This plugin creates shortcode to show Facebook Feed
 * 
 * 
 *                    
 * Version:           1.0.0
 * Author:            Toby Wong
 * Author URI:        https://www.vivideyecentre.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ech_fb_feed
 * Domain Path:       /languages
 */


if (! defined('ABSPATH')) {
	exit;
}


// loader.php is to load all files in folder "inc"
require_once(dirname(__FILE__). '/inc/loader.php');


// include CSS and JS files
add_action('init', 'register_ech_fb_feed_styles');
add_action('wp_enqueue_scripts', 'enqueue_ech_fb_feed_styles');

//load more posts using ajax
add_action('wp_ajax_nopriv_fb_load_more_feed', 'fb_load_more_feed');
add_action('wp_ajax_fb_load_more_feed', 'fb_load_more_feed');




// Register the shortcode
add_shortcode('ech_fb_feed', 'ech_fb_feed_fun' );


