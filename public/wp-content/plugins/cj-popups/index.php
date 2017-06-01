<?php
/*
Plugin Name: CSSJockey Unlimited Pop-ups
Plugin URI: http://cssjockey.com/wordpress-pop-ups/
Description: With this plugin you can show different kind of pop-ups with any type of HTML contnet or shortcodes on all or certain pages of your WordPress website.
Author: Mohit Aneja (CSSJockey)
Version: 1.3.9
Author URI: http://CSSJockey.com/
Text Domain: cjpopups
*/
ob_start();

if ( !defined('cjpopups_version') )
define('cjpopups_version', '1.3.9');

function cjpopups_load_textdomain() {
	$lang_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	load_plugin_textdomain( 'cjpopups', false, $lang_path );
}
add_action( 'init', 'cjpopups_load_textdomain');

require_once('item_setup.php');
require_once(sprintf('%s/framework/framework.php', dirname(__FILE__)));

do_action('cjpopups_functions');
//set_site_transient('update_themes', null);