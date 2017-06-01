<?php
/*
Plugin Name: Gunner Technology Twitter
Plugin URI: http://gunnertech.com/gunner-technology-twitter
Description: A plugin that allows authors to add their Twitter Feeds via widgets
Version: 0.1.0
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/

class GtTwitter {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function setup() {
    self::$VERSION = '0.1.0';
    self::$PREFIX = "gt_twitter";
    self::$URL = plugin_dir_url( __FILE__ );
    self::$PATH = plugin_dir_path( __FILE__ );
    
    self::update_db_check();
    self::singleton();
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
    
    
    add_action('widgets_init',function() {
      return register_widget('GtTwitter_Widget');
    });
    
    add_action('init',function() {
      if(!is_admin()) {
        wp_enqueue_script( 'twitter', GtTwitter::getConst('URL').'js/lib/jquery.twitterStream.js', array('jquery'));
        wp_enqueue_script( GtTwitter::getConst('PREFIX'), GtTwitter::getConst('URL').'js/script.js', array('twitter'));

        wp_enqueue_style( GtTwitter::getConst('PREFIX'), GtTwitter::getConst('URL').'css/style.css' );
      }
      
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

class GtTwitter_Widget extends WP_Widget {
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-twitter', 'description' => 'A widget that displays Twitter updates.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-twitter' );
    
    parent::__construct( 
      'gunner_technology_twitter', 
      'Twitter Widget', 
      array( 'description' => 'A Twitter Feed Box', 'classname' => 'gunner-technology-twitter' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'api_key' => '', 
      'follow_slug' => '&#187; Follow Me', 
      'title' => 'Twitter', 
      'num_results' => 3, 
      'user_name' => 'gunnertech',
      'search_term' => 'gunnertech',
      'search_type' => 'user', 
      'result_type' => 'mixed', 
      'image' => '', 
      'description' => '')
    );
    
    extract( $args );

    $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));

    ?>
    <?php echo $before_widget ?>
      <?php if ( $title ) {
        echo $before_title . $title . $after_title;
      } ?>
      <?php if($instance['description']): ?>
        <p><?php echo $instance['description'] ?></p>
      <?php endif; ?>
     <div 
      class="twitter-drop"
      data-api_key="<?php echo $instance['api_key'] ?>"
      data-user_name="<?php echo $instance['user_name'] ?>"
      data-search_term="<?php echo $instance['search_term'] ?>"
      data-search_type="<?php echo $instance['search_type'] ?>"
      data-result_type="<?php echo $instance['result_type'] ?>"
      data-num_results="<?php echo $instance['num_results'] ?>"
      data-image="<?php echo $instance['image'] ?>"
     >
     </div>
     <a class="call-to-action" id="<?php echo $instance['user_name'] ?>-follow" title="<?php echo $instance['user_name'] ?>" href="http://twitter.com/<?php echo $instance['user_name'] ?>"><?php echo $instance['follow_slug'] ?></a>
   <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['api_key'] = strip_tags( $new_instance['api_key'] );
    $instance['user_name'] = strip_tags( $new_instance['user_name'] );
    $instance['follow_slug'] = $new_instance['follow_slug'];
    $instance['description'] = $new_instance['description'];
    $instance['search_type'] = $new_instance['search_type'];
    $instance['result_type'] = $new_instance['result_type'];
    $instance['search_term'] = $new_instance['search_term'];
    $instance['image'] = $new_instance['image'];
    $instance['num_results'] = intval($new_instance['num_results']);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'api_key' => '', 
      'follow_slug' => '&#187; Follow Me', 
      'title' => 'Twitter', 
      'num_results' => 3, 
      'result_type' => 'mixed',
      'user_name' => 'gunnertech',
      'search_term' => 'gunnertech',
      'search_type' => 'user', 
      'image' => '',
      'description' => '')
    );
    ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'search_term' ); ?>">Search Term:</label><br />
      <input id="<?php echo $this->get_field_id( 'search_term' ); ?>" name="<?php echo $this->get_field_name( 'search_term' ); ?>" value="<?php echo $instance['search_term']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'search_type' ); ?>">Search Type:</label><br />
      
      <select id="<?php echo $this->get_field_id( 'search_type' ); ?>" name="<?php echo $this->get_field_name( 'search_type' ); ?>">
        <?php foreach(array('user','list','text') as $search_type): ?>
          <option <?php selected($search_type,$instance['search_type']) ?> value="<?php echo $search_type ?>"><?php echo $search_type ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'result_type' ); ?>">Result Type:</label><br />
      
      <select id="<?php echo $this->get_field_id( 'result_type' ); ?>" name="<?php echo $this->get_field_name( 'result_type' ); ?>">
        <?php foreach(array('mixed','recent','popular') as $result_type): ?>
          <option <?php selected($result_type,$instance['result_type']) ?> value="<?php echo $result_type ?>"><?php echo $result_type ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'user_name' ); ?>">User Name:</label><br />
      <input id="<?php echo $this->get_field_id( 'user_name' ); ?>" name="<?php echo $this->get_field_name( 'user_name' ); ?>" value="<?php echo $instance['user_name']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'num_results' ); ?>">Number of Tweets to Show:</label><br />
      <input id="<?php echo $this->get_field_id( 'num_results' ); ?>" name="<?php echo $this->get_field_name( 'num_results' ); ?>" value="<?php echo $instance['num_results']; ?>" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'api_key' ); ?>">API Key:</label><br />
      <input id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" value="<?php echo $instance['api_key']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description:</label><br />
      <input id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="<?php echo $instance['description']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'follow_slug' ); ?>">Follow Slug:</label><br />
      <input id="<?php echo $this->get_field_id( 'follow_slug' ); ?>" name="<?php echo $this->get_field_name( 'follow_slug' ); ?>" value="<?php echo $instance['follow_slug']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'image' ); ?>">Replacement Image:</label><br />
      <input style="width:80%;" type="text" placeholder="Enter a URL or Click 'Pick Image from Computer'" class="newtag upload_image_value form-input-tip" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo $instance['image']; ?>" />
      <input class="upload_image_button" type="button" value="Pick Image from Computer" />
    </p>
    
    <?php 
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtTwitter', 'activate') );
register_activation_hook( __FILE__, array('GtTwitter', 'deactivate') );
register_activation_hook( __FILE__, array('GtTwitter', 'uninstall') );

add_action('plugins_loaded', array('GtTwitter', 'setup') );