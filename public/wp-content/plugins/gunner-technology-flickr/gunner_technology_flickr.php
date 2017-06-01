<?php
/*
Plugin Name: Gunner Technology Flickr
Plugin URI: http://gunnertech.com/gunner-technology-flickr
Description: A plugin that allows authors to add their Flickr photos via widgets
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_FLICKR_VERSION', '0.0.1');
define('GT_FLICKR_URL', plugin_dir_url( __FILE__ ));

class GtFlickr {
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_flickr_db_version", GT_FLICKR_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_flickr_db_version" );
    
    if( $installed_ver != GT_FLICKR_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function(){
      return register_widget('GtFlickr_Widget');
    });
    
    wp_enqueue_style( 'flightbox', GT_FLICKR_URL.'css/jquery.flightbox.css');
    
    wp_enqueue_script( 'flightbox', GT_FLICKR_URL.'js/lib/jquery.flightbox.js', array('jquery'));
    wp_enqueue_script( 'flickr_stream', GT_FLICKR_URL.'js/lib/jquery.flickrStream.js', array('flightbox'));
    wp_enqueue_script( 'gunner_technology_flickr', GT_FLICKR_URL.'js/script.js', array('flickr_stream'));
  }
  
  public static function setup() {
    self::update_db_check();
    $gunner_technology_flickr = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

class GtFlickr_Widget extends WP_Widget {
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-flickr', 'description' => 'A widget that displays Flickr photos.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-flickr' );
    
    parent::__construct( 
      'gunner_technology_flickr', 
      'Flickr Widget', 
      array( 'description' => 'A Flickr Like Box', 'classname' => 'gunner-technology-flickr' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $more_url = isset($instance['more_url']) ? $instance['more_url'] : false;


     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     <div 
      class="flickr-drop"
      data-api_key="<?php echo $instance['api_key'] ?>"
      data-user_id="<?php echo $instance['user_id'] ?>"
      data-max_photos="<?php echo $instance['max_photos'] ?>"
      data-photo_width="<?php echo $instance['photo_width'] ?>"
      data-photo_height="<?php echo $instance['photo_height'] ?>"
      data-of_user="<?php echo $instance['of_user'] ?>"
      >
     </div>
     <?php if($more_url): ?>
       <a class="call-to-action" href="<?php echo $more_url ?>"><?php echo $instance['more_slug'] ?></a>
     <?php endif; ?>
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['api_key'] = strip_tags( $new_instance['api_key'] );
    $instance['user_id'] = strip_tags( $new_instance['user_id'] );
    $instance['more_slug'] = $new_instance['more_slug'];
    $instance['more_url'] = $new_instance['more_url'];
    $instance['max_photos'] = intval( $new_instance['max_photos'] );
    $instance['photo_width'] = intval( $new_instance['photo_width'] );
    $instance['photo_height'] = intval( $new_instance['photo_height'] );
    $instance['of_user'] = (isset($new_instance['of_user']) && $new_instance['of_user'] ? 1 : 0);

    return $instance;
  }

  /** @see WP_Widget::form */
  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'more_url' => '', 
      'more_slug' => '&#187; View All', 
      'title' => 'Latest Photos', 
      'max_photos' => '12', 
      'user_id' => '', 
      'api_key' => '', 
      'photo_height' => 75, 
      'photo_width' => 75, 
      'of_user' => false)
    );
    ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'user_id' ); ?>">User ID:</label><br />
      <input id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" value="<?php echo $instance['user_id']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'max_photos' ); ?>">Number of Photos to Show:</label><br />
      <input id="<?php echo $this->get_field_id( 'max_photos' ); ?>" name="<?php echo $this->get_field_name( 'max_photos' ); ?>" value="<?php echo $instance['max_photos']; ?>" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'api_key' ); ?>">API Key:</label><br />
      <input id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" value="<?php echo $instance['api_key']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'photo_width' ); ?>">Photo Width:</label><br />
      <input id="<?php echo $this->get_field_id( 'photo_width' ); ?>" name="<?php echo $this->get_field_name( 'photo_width' ); ?>" value="<?php echo $instance['photo_width']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'photo_height' ); ?>">Photo Height:</label><br />
      <input id="<?php echo $this->get_field_id( 'photo_height' ); ?>" name="<?php echo $this->get_field_name( 'photo_height' ); ?>" value="<?php echo $instance['photo_height']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'more_url' ); ?>">More URL:</label><br />
      <input id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo $instance['more_url']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'more_slug' ); ?>">More Slug:</label><br />
      <input id="<?php echo $this->get_field_id( 'more_slug' ); ?>" name="<?php echo $this->get_field_name( 'more_slug' ); ?>" value="<?php echo $instance['more_slug']; ?>" />
    </p>

    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['of_user'], true) ?> id="<?php echo $this->get_field_id('of_user'); ?>" name="<?php echo $this->get_field_name('of_user'); ?>" />
      <label for="<?php echo $this->get_field_id('of_user'); ?>"><?php _e('Limit to Photos user is tagged in?'); ?></label>
    </p>
    
    <?php 
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtFlickr', 'activate') );
register_activation_hook( __FILE__, array('GtFlickr', 'deactivate') );
register_activation_hook( __FILE__, array('GtFlickr', 'uninstall') );

add_action('plugins_loaded', array('GtFlickr', 'setup') );