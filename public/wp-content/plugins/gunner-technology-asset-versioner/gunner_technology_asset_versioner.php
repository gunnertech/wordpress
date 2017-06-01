<?php
/*
Plugin Name: Gunner Technology Asset Versioner
Plugin URI: http://gunnertech.com/2012/02/modernizr-wordpress-plugin
Description: A plugin to version assets for maximum cacheability
Version: 0.0.3
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_ASSET_VERSIONER_VERSION', '0.0.3');
define('GT_ASSET_VERSIONER_URL', plugin_dir_url( __FILE__ ));

class GtAssetVersioner {
  private static $instance;
  public static $is_https_request;
  
  public static function activate() {
    update_option("gt_async_asset_loader_db_version", GT_ASSET_VERSIONER_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_async_asset_loader_db_version" );
    
    if( $installed_ver != GT_ASSET_VERSIONER_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    
    if(defined('GT_ASSET_VERSION')) {
      add_filter( 'style_loader_src', function($src){
        $src = add_query_arg( 'v', GT_ASSET_VERSION, $src );
        return $src;
      }, 9999 );
      
      add_filter( 'script_loader_src', function($src){
        $src = add_query_arg( 'v', GT_ASSET_VERSION, $src );
        return $src;
      }, 9999 );
    }
  }
  
  
  public static function setup() {
    self::update_db_check();
    self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }
}

register_activation_hook( __FILE__, array('GtAssetVersioner', 'activate') );
register_activation_hook( __FILE__, array('GtAssetVersioner', 'deactivate') );
register_activation_hook( __FILE__, array('GtAssetVersioner', 'uninstall') );

add_action('plugins_loaded', array('GtAssetVersioner', 'setup') );

