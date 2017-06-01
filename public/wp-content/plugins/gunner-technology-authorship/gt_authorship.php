<?php
/*
Plugin Name: Gunner Technology Authorship
Plugin URI: http://gunnertech.com/2012/02/authorship-wordpress-plugin-by-gunner-technology/
Description: A plugin that allows authors to add their social credentials to their articles and author pages
Version: 0.0.3
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_AUTHORSHIP_VERSION', '0.0.3');
define('GT_AUTHORSHIP_URL', plugin_dir_url( __FILE__ ));

class GtAuthorship {
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gfc_db_version", GT_AUTHORSHIP_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gfc_db_version" );
    
    if( $installed_ver != GT_AUTHORSHIP_VERSION ) {
      self::activate();
    }
  }
  
  public static function render_links() { ?>
    <ul class="social-links">
      <?php if($google_profile = get_the_author_meta( 'google_profile' )): ?>
        <li class="google-plus"><a class="g-plus" data-href="<?php echo esc_url($google_profile) ?>" data-size="smallbadge" href="<?php echo esc_url($google_profile) ?>" rel="me">Google Profile</a></li>
      <?php endif; ?>
      <?php if($twitter_handle = get_the_author_meta( 'twitter_handle' )): ?>
        <li class="twitter">
          <a href="https://twitter.com/<?php echo $twitter_handle ?>" class="twitter-follow-button" data-show-count="false">Follow @<?php echo $twitter_handle ?></a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </li>
      <?php endif; ?>
      <?php if($facebook_profile = get_the_author_meta( 'facebook_profile' )): ?>
        <li class="facebook-profile">
          <div class="fb-subscribe" data-href="<?php echo $facebook_profile ?>" data-layout="button_count" data-show-faces="false" data-width="250"></div>
        </li>
      <?php endif; ?>
      <?php if($facebook_page = get_the_author_meta( 'facebook_page' )): ?>
        <li class="facebook-page">
          <div class="fb-like" data-href="<?php echo $facebook_page ?>" data-send="false" data-layout="button_count" data-width="250" data-show-faces="false"></div>
        </li>
      <?php endif; ?>
      <?php if($tungle_me_handle = get_the_author_meta( 'tungle_me_handle' )): ?>
        <li class="tungle"><a class="tungle-me" href="javascript:void(0);" teml="<?php echo trim($tungle_me_handle) ?>">Schedule an Appointment</a></li>
      <?php endif; ?>
    </ul>
  <?php }
  
  private function __construct(){
    add_action( 'wp_loaded', array( $this, 'allow_rel' ) );
    
    add_filter( 'user_contactmethods', array( $this, 'add_google_profile' ), 10, 1);
    add_filter( 'get_the_author_description', array( $this, 'append_social_links' ), 2);
    
    add_action( 'init', array( $this, 'load_dependencies') );
    add_action( 'wp_head', array( $this, 'print_external_requirements') );
  }
  
  public function load_dependencies() {
    if( get_the_author_meta( 'tungle_me_handle' ) ){
      wp_enqueue_script("tunglemeWidget","https://www.tungle.me/portal/js/plugins/tungle.mwmWidget.js",array(),null,true);
    }
  }
  
  public function print_external_requirements() {
    if( $google_profile = get_the_author_meta( 'google_profile' ) ){
      echo '<link href="'.$google_profile.'" rel="publisher" /><script type="text/javascript">
      (function() 
      {var po = document.createElement("script");
      po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(po, s);
      })();</script>';
    }
  }
  
  public function append_social_links($content) {
    ob_start();

    GtAuthorship::render_links();

    return  ob_get_clean() . $content;
  }
  
  public function add_google_profile( $contactmethods ) {
    $contactmethods['google_profile'] = 'Google Profile URL';
    $contactmethods['facebook_profile'] = 'Facebook Profile URL';
    $contactmethods['facebook_page'] = 'Facebook Page URL';
    $contactmethods['twitter_handle'] = 'Twitter Handle';
    $contactmethods['tungle_me_handle'] = 'Tungle.me Handle';
    
    return $contactmethods;
  }
  
  public function allow_rel() {
    global $allowedtags;
    
    $allowedtags['a']['rel'] = array();
  }
  
  public function admin_menu() {
    //add_menu_page( 'Referrer Checker', 'Referrer Checker', 'manage_options', 'referrer-checker', array( 'GoogleReferrerChecker', 'admin_page' ), '' );
  }
  
  public static function setup() {
    self::update_db_check();
    $gt_authorship = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

register_activation_hook( __FILE__, array('GtAuthorship', 'activate') );
register_activation_hook( __FILE__, array('GtAuthorship', 'deactivate') );
register_activation_hook( __FILE__, array('GtAuthorship', 'uninstall') );

add_action('plugins_loaded', array('GtAuthorship', 'setup') );

