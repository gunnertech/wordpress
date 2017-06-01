<?php
/*
Plugin Name: Gunner Technology Async Asset Loader
Plugin URI: http://gunnertech.com/2012/02/modernizr-wordpress-plugin
Description: A plugin leverages Modernizr to load JavaScript files asynchronously 
Version: 0.0.3
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_ASYNC_ASSET_LOADER_VERSION', '0.0.3');
define('GT_ASYNC_ASSET_LOADER_URL', plugin_dir_url( __FILE__ ));

class GtAsyncAssetLoader {
  private static $instance;
  public static $is_https_request;
  
  public static function activate() {
    update_option("gt_async_asset_loader_db_version", GT_ASYNC_ASSET_LOADER_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_async_asset_loader_db_version" );
    
    if( $installed_ver != GT_ASYNC_ASSET_LOADER_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    //self::$is_https_request = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
    self::$is_https_request = is_ssl();
    
    add_action("wp_head", function() { ?>
      <script src="<?php echo GT_ASYNC_ASSET_LOADER_URL ?>js/lib/modernizr.js"></script>
    <?php }, 1);
    
    remove_action("wp_head","wp_print_head_scripts",9);
    remove_action('wp_footer', 'wp_print_footer_scripts');
    
    add_action("wp_head", function() {
      global $wp_scripts, $hbgs_scripts, $hbgs_inline_scripts;
      if ( ! did_action('wp_print_scripts') ) {
        do_action('wp_print_scripts');
      }
      
      if ( !is_a($wp_scripts, 'WP_Scripts') ) {
        return array(); // no need to run if nothing is queued
      }
      
      ob_start();
      
      print_head_scripts();
      echo get_option("footer_javascript");
      print_footer_scripts();

      $output = ob_get_contents();
      ob_end_clean();

      $matches = array();
      preg_match_all('|<script.+src=["\'](.+)["\'].+<\/script>|',$output,$matches);
      $output = preg_replace('|<script.+src=["\'](.+)["\'].+<\/script>|',"",$output);

      $hbgs_scripts = implode("','",$matches[1]);
      
      if(defined("GT_CDN") && GT_CDN) {
        $site_domain  = str_replace("https://","",str_replace("http://", "", get_option('siteurl')));
        $hbgs_scripts = preg_replace('|'.$site_domain.'|',GT_CDN,$hbgs_scripts);
        $hbgs_scripts = preg_replace('/'.GT_CDN.'\/index.php/',$site_domain.'/index.php',$hbgs_scripts);
        $hbgs_scripts = str_replace("https://".GT_CDN.":443/index.php?wpsc_user_dynamic_js","https://{$site_domain}/index.php?wpsc_user_dynamic_js",$hbgs_scripts);
        $hbgs_scripts = preg_replace('|\.js\?|',".js.gzip?",$hbgs_scripts);
        $hbgs_scripts = str_replace('.gzip??',".gzip?",$hbgs_scripts);
      }
      
      // if(defined('GT_ASSET_VERSION')) {
      //   $hbgs_scripts = preg_replace('|\.js?|',".js?v=".GT_ASSET_VERSION.'&',$hbgs_scripts);
      // }

      if(GtAsyncAssetLoader::$is_https_request) {
        $hbgs_scripts = preg_replace('/http:\/\//',"https://",$hbgs_scripts);
        
        $hbgs_scripts = str_replace("https://".GT_CDN."/index.php?wpsc_user_dynamic_js","https://{$site_domain}/index.php?wpsc_user_dynamic_js",$hbgs_scripts);
        
        
      }

      if($hbgs_scripts) {
        $allowed_tags = '<b><i><sup><sub><em><strong><u><br><p><div><section><aside><article><h1><h2><h3><h4><h5><h6>';
        $hbgs_inline_scripts = strip_tags($hbgs_inline_scripts,$allowed_tags);
        $output = preg_replace('/<script type=\'text\/javascript\'>/',"",$output);
        $output = preg_replace('/<\/script>/',"",$output);
        $hbgs_scripts = preg_replace('/&#038;/','&amp;',$hbgs_scripts);
        
        $localized_scripts = array();
        preg_match_all( '/var .+ = \{.+\};/' , $output, $localized_scripts);
        
        $pre_load_scripts = isset($localized_scripts[0]) && is_array($localized_scripts[0]) ? implode("\n",$localized_scripts[0]) : "";
        
        $output = preg_replace('/var (.+) = {/',"window.$1 ={",$output);
          
        $hbgs_scripts = preg_replace('|&\?|','&',$hbgs_scripts);

        $new_output = "<script>".$pre_load_scripts."Modernizr.load({test: Modernizr.hbgs_loaded, nope:['$hbgs_scripts'], complete:function(){ Modernizr.hbgs_loaded = true; (function($){ ".$hbgs_inline_scripts.trim($output)."}(jQuery)) }});</script>";
      } else {
        $new_output = "";
      }

      echo $new_output;

      return $wp_scripts->done; 
    },9);
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

register_activation_hook( __FILE__, array('GtAsyncAssetLoader', 'activate') );
register_activation_hook( __FILE__, array('GtAsyncAssetLoader', 'deactivate') );
register_activation_hook( __FILE__, array('GtAsyncAssetLoader', 'uninstall') );

add_action('plugins_loaded', array('GtAsyncAssetLoader', 'setup') );

