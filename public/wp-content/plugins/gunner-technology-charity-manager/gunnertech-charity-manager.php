<?php  

  /*
  Plugin Name: Gunnertech Charity Manager
  Plugin URI: http://gunnertech.com
  Description: Provides a customizeable system for managing charitable donations.
  Version: 0.1.1
  Author: Gunnertech
  Author URI: http://gunnertech.com
  */
  
  global $wpdb;
  
  define('GTCM_FILE_PATH', dirname(__FILE__));
  define('GTCM_URL', WP_CONTENT_URL.'/plugins/'.dirname(plugin_basename(__FILE__)));
  define('GTCM_ACHIEVEMENTS_TABLE', "{$wpdb->prefix}gtcm_achievements");
  define('GTCM_CAMPAIGNS_TABLE', "{$wpdb->prefix}gtcm_campaigns");
  define('GTCM_DONATIONS_TABLE', "{$wpdb->prefix}gtcm_donations");
  
  if (!defined('GTCM_VERSION_KEY'))
    define('GTCM_VERSION_KEY', 'gtcm_version');
  if (!defined('GTCM_VERSION_NUM'))
    define('GTCM_VERSION_NUM', '0.1.1');
  add_option(GTCM_VERSION_KEY, GTCM_VERSION_NUM);
  
  include_once(GTCM_FILE_PATH."/admin.php");
  include_once(GTCM_FILE_PATH."/functions.php");
  
  include_once(GTCM_FILE_PATH."/install.php");
  include_once(GTCM_FILE_PATH."/update.php");
  
  register_activation_hook(__FILE__,'gtcm_install');
  
  add_action('widgets_init', 'gtcm_widgets_init');
?>