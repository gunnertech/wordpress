<?php
/*
Plugin Name: Gunner Technology Facebook
Plugin URI: http://gunnertech.com/gunner-technology-facebook
Description: A plugin that allows authors to add their social credentials to their articles and author pages
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_FACEBOOK_VERSION', '0.0.1');
define('GT_FACEBOOK_URL', plugin_dir_url( __FILE__ ));

class GtFacebook {
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_facebook_widget_db_version", GT_FACEBOOK_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_facebook_db_version" );
    
    if( $installed_ver != GT_FACEBOOK_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function(){
      return register_widget('GtFacebook_Widget');
    });
  }
  
  public static function setup() {
    self::update_db_check();
    $gunner_technology_facebook = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

class GtFacebook_Widget extends WP_Widget {
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-facebook', 'description' => 'A widget that displays a Facebook like box.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-facebook' );
    
    // parent::__construct( 'gunner_technology_facebook_widget', 'Facebook Like Widget', $widget_ops, $control_ops );
    
    parent::__construct( 
      'gunner_technology_facebook', 
      'Facebook Widget', 
      array( 'description' => 'A Facebook Like Box', 'classname' => 'gunner-technology-facebook' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    extract( $args );
    
    $title = apply_filters('widget_title', $instance['title'] );
    $name = $instance['username'];
    $color_scheme = $instance['color_scheme'];
    $border_color = $instance['border_color'];
    $description = $instance['description'];
    $url = $instance['url'];
    $version = isset($instance['version']) && trim($instance['version']) != '' ? $instance['version'] : 'fb:like-box';
    $width = isset($instance['width']) ? $instance['width'] : 250;
    $height = isset($instance['height']) ? $instance['height'] : 250;
    $connections = isset($instance['connections']) ? $instance['connections'] : 8;
    $show_stream = isset($instance['stream']) ? $instance['stream'] : false;
    $show_header = isset($instance['header']) ? $instance['header'] : false;
    $show_logobar = isset($instance['logobar']) ? $instance['logobar'] : false;
    $show_faces = isset($instance['faces']) ? $instance['faces'] : false;
    $identifier = intval($name);
    $follow_slug = isset($instance['follow_slug']) ? $instance['follow_slug'] : 'Visit Me';
    
    
    
    $link = $identifier > 0 ? "http://facebook.com/pages/pages/$identifier" : "http://facebook.com/$name";
    
    if(isset($url) && trim($url) != "") {
      $link = $url;
    }
    ?>
    
    <?php echo $before_widget ?>
      <?php if ( $title ) echo $before_title . do_shortcode($title) . $after_title; ?>
      <?php if(trim($description) != ''): ?>
        <p class="description"><?php echo $description ?></p>
      <?php endif; ?>
      <div class="facebook-drop">
        <<?php echo $version ?>
          href="<?php echo $link ?>" 
          width="<?php echo $width ?>" 
          height="<?php echo $height ?>"

          border_color="<?php echo $border_color ?>" 
          show_faces="<?php echo $show_faces ? 'true' : 'false' ?>" 
          stream="<?php echo $show_stream ? 'true' : 'false' ?>" 
          header="<?php echo $show_header ? 'true' : 'false' ?>" 
          colorscheme="<?php echo $color_scheme ?>" 
          border_color="<?php echo $border_color ?>" 
          class="gunner-technology-facebook-widget"

          <?php if( $identifier > 0 ): ?>
            profile_id="<?php echo $identifier ?>" 
          <?php endif; ?>

          logobar="<?php echo $show_logobar ? 'true' : 'false' ?>"
          connections="<?php echo $connections ?>" 
          css="<?php bloginfo('stylesheet_url'); ?>?1" 
          ></<?php echo $version ?>>
      </div>
      <?php if(trim($follow_slug) != ''): ?>
        <a class="call-to-action" href="<?php echo $link ?>"><?php echo $follow_slug ?></a>
      <?php endif; ?>
    <?php echo $after_widget ?>
    <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    
    $instance = $old_instance;
    $instance = array( 'header' => 0, 'stream' => 0, 'logobar' => 0, 'faces' => 0);
  
    foreach ( $instance as $field => $val ) {
     if ( isset($new_instance[$field]) )
      $instance[$field] = 1;
    }
  
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['username'] = strip_tags( $new_instance['username'] );
    $instance['connections'] = intval($new_instance['connections']);
    $instance['width'] = intval($new_instance['width']);
    $instance['height'] = intval($new_instance['height']);
    $instance['follow_slug'] = $new_instance['follow_slug'];
    $instance['border_color'] = $new_instance['border_color'];
    $instance['url'] = $new_instance['url'];
    $instance['description'] = $new_instance['description'];
    $instance['color_scheme'] = $new_instance['color_scheme'];
    $instance['version'] = $new_instance['version'];

    return $instance;
  }

  /** @see WP_Widget::form */
  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array(
      'description' => "For the latest info, check out my Facebook page!",
      'title' => 'Like Us on Facebook', 
      'username' => 'Facebook Name or Id',
      'url' => '',
      'stream' => false,
      'header' => false,
      'logobar' => false,
      'faces' => true,
      'connections' => 8,
      'width' => 250, 
      'border_color' => "",
      'height' => 250,
      'color_scheme' => 'light',
      'version' => 'fb:like-box',
      'follow_slug' => "Visit Me"
    ));
    ?>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'version' ); ?>">Style:</label><br />
      <select id="<?php echo $this->get_field_id( 'version' ); ?>" name="<?php echo $this->get_field_name( 'version' ); ?>">
        <option value="fb:fan" <?php selected($instance['version'],"fb:fan") ?>>Old (fb:fan)</option>
        <option value="fb:like-box" <?php selected($instance['version'],"fb:like-box") ?>>New (fb:like-box)</option>
      </select><br />
      Unless you want to customize the look of the box using CSS, choose "New (fb:like-box)"
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'color_scheme' ); ?>">Style:</label><br />
      <select id="<?php echo $this->get_field_id( 'color_scheme' ); ?>" name="<?php echo $this->get_field_name( 'color_scheme' ); ?>">
        <option value="light" <?php selected($instance['color_scheme'],"light") ?>>Light</option>
        <option value="dark" <?php selected($instance['color_scheme'],"dark") ?>>Dark</option>
      </select><br />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'username' ); ?>">Facebook Page Name:</label><br />
      <input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'url' ); ?>">URL (this overrides the Page Name Field):</label><br />
      <input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description:</label><br />
      <input id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="<?php echo $instance['description']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'connections' ); ?>">Number of Connections to Show:</label><br />
      <input id="<?php echo $this->get_field_id( 'connections' ); ?>" name="<?php echo $this->get_field_name( 'connections' ); ?>" value="<?php echo $instance['connections']; ?>" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'border_color' ); ?>">Border Color:</label><br />
      <input id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" value="<?php echo $instance['border_color']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'width' ); ?>">Width:</label><br />
      <input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'height' ); ?>">Height:</label><br />
      <input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'follow_slug' ); ?>">Follow Slug:</label><br />
      <input id="<?php echo $this->get_field_id( 'follow_slug' ); ?>" name="<?php echo $this->get_field_name( 'follow_slug' ); ?>" value="<?php echo $instance['follow_slug']; ?>" />
    </p>

    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['header'], true) ?> id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>" />
      <label for="<?php echo $this->get_field_id('header'); ?>"><?php _e('Show Header?'); ?></label><br />
      
      <input class="checkbox" type="checkbox" <?php checked($instance['faces'], true) ?> id="<?php echo $this->get_field_id('faces'); ?>" name="<?php echo $this->get_field_name('faces'); ?>" />
      <label for="<?php echo $this->get_field_id('faces'); ?>"><?php _e('Show Faces?'); ?></label><br />
      
      <input class="checkbox" type="checkbox" <?php checked($instance['stream'], true) ?> id="<?php echo $this->get_field_id('stream'); ?>" name="<?php echo $this->get_field_name('stream'); ?>" />
      <label for="<?php echo $this->get_field_id('stream'); ?>"><?php _e('Show Activity Stream?'); ?></label><br />
      <input class="checkbox" type="checkbox" <?php checked($instance['logobar'], true) ?> id="<?php echo $this->get_field_id('logobar'); ?>" name="<?php echo $this->get_field_name('logobar'); ?>" />
      <label for="<?php echo $this->get_field_id('logobar'); ?>"><?php _e('Show Logo Bar?'); ?></label>
    </p>
    
    <?php 
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtFacebook', 'activate') );
register_activation_hook( __FILE__, array('GtFacebook', 'deactivate') );
register_activation_hook( __FILE__, array('GtFacebook', 'uninstall') );

add_action('plugins_loaded', array('GtFacebook', 'setup') );