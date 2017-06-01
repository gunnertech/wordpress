<?php
/*
Plugin Name: Gunner Technology Multisite
Plugin URI: http://gunnertech.com/2012/02/pinboard-a-pinterest-wordpress-plugin/
Description: A plugin that allows authors to add their Packageboard via widgets
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/

class GtMultisite {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_multisite";
    self::$URL = plugin_dir_url( __FILE__ );
    self::$PATH = plugin_dir_path( __FILE__ );
    
    
    self::update_db_check();
    $me = self::singleton();
  }
  
  public static function getConst($const) {
    return self::$$const;
  }
  
  public static function activate() {
    global $wpdb;
    
    update_option(self::$PREFIX."_db_version", self::$VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    $installed_ver = get_option( self::$PREFIX."_db_version" );
    
    if( $installed_ver != self::$VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    $_this = $this;
            
    add_action('admin_init', function() {
      global $wp_query;
            
      wp_enqueue_script( GtMultisite::getConst('PREFIX'), GtMultisite::getConst('URL').'js/script.js', array('jquery') );
      
    });
    

  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }
}


register_activation_hook( __FILE__, array('GtMultisite', 'activate') );
register_activation_hook( __FILE__, array('GtMultisite', 'deactivate') );
register_activation_hook( __FILE__, array('GtMultisite', 'uninstall') );

add_action('plugins_loaded', array('GtMultisite', 'setup') );