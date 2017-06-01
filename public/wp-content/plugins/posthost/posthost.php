<?php
/*
Plugin Name: Posthost
Plugin URI: http://gunnertech.com/posthost
Description: A plugin that allows authors to add their Posthost Feeds via widgets
Version: 1.0.0
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/

class Posthost {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function setup() {
    self::$VERSION = '1.0.0';
    self::$PREFIX = "posthost";
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

    add_shortcode('posthost', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'group_name' => 'TrailLife',
        'photo_count' => '10',
        'category' => ''
      ), $atts));

      return '<div 
        class="posthost-drop"
        data-group_name="'.$group_name.'"
        data-photo_count="'.$photo_count.'"
        data-category="'.$category.'"
      >';
    });
    
    
    add_action('widgets_init',function() {
      return register_widget('Posthost_Widget');
    });
    
    add_action('init',function() {
      if(!is_admin()) {
        wp_enqueue_script( 'lib', Posthost::getConst('URL').'js/lib.js', array('jquery'));
        wp_enqueue_script( Posthost::getConst('PREFIX'), Posthost::getConst('URL').'js/script.js', array('lib'));

        wp_enqueue_style( Posthost::getConst('PREFIX'), Posthost::getConst('URL').'css/style.css' );
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

class Posthost_Widget extends WP_Widget {
  function __construct() {
    $widget_ops = array( 'classname' => 'posthost', 'description' => 'A widget that displays Twitter updates.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'posthost' );
    
    parent::__construct( 
      'posthost', 
      'Posthost Widget', 
      array( 'description' => 'A Posthost Feed Box', 'classname' => 'posthost' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'group_name' => 'TrailLife', 
      'photo_count' => '10',
      'category' => '',
      'title' => 'Posthost Feed')
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
      class="posthost-drop"
      data-group_name="<?php echo $instance['group_name'] ?>"
      data-photo_count="<?php echo $instance['photo_count'] ?>"
      data-category="<?php echo $instance['category'] ?>"
     >
     </div>
   <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['group_name'] = strip_tags( $new_instance['group_name'] );
    $instance['category'] = strip_tags( $new_instance['category'] );
    $instance['photo_count'] = strip_tags( $new_instance['photo_count'] );

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'group_name' => 'TrailLife', 
      'photo_count' => '10',
      'category' => '',
      'title' => 'Posthost Feed')
    );
    ?>

    <p>
      <label>Short Code:</label><br />
      <input disabled="disabled" value="[posthost category='<?php echo $instance['category']; ?>' group_name='<?php echo $instance['group_name']; ?>' photo_count='<?php echo $instance['photo_count']; ?>']" />
      <br />
      <small>You can copy and paste this code into any WordPress page</small>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'group_name' ); ?>">Group Name:</label><br />
      <input id="<?php echo $this->get_field_id( 'group_name' ); ?>" name="<?php echo $this->get_field_name( 'group_name' ); ?>" value="<?php echo $instance['group_name']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'category' ); ?>">Category:</label><br />
      <input id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" value="<?php echo $instance['category']; ?>" />
      <br />
      <small>Leave blank for all; seperate multiple with a comma</small>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'photo_count' ); ?>">Photo Count:</label><br />
      
      <select id="<?php echo $this->get_field_id( 'photo_count' ); ?>" name="<?php echo $this->get_field_name( 'photo_count' ); ?>">
        <?php foreach(range(3, 96) as $number): ?>
          <option <?php selected($number, $instance['photo_count']) ?> value="<?php echo $number ?>"><?php echo $number ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <?php 
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('Posthost', 'activate') );
register_activation_hook( __FILE__, array('Posthost', 'deactivate') );
register_activation_hook( __FILE__, array('Posthost', 'uninstall') );

add_action('plugins_loaded', array('Posthost', 'setup') );