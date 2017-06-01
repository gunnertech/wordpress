<?php
/*
Plugin Name: Gunner Technology Youtube
Plugin URI: http://gunnertech.com/gunner-technology-youtube
Description: A plugin that allows authors to add their Youtube videos via widgets
Version: 0.0.2
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_YOUTUBE_VERSION', '0.0.2');
define('GT_YOUTUBE_URL', plugin_dir_url( __FILE__ ));

class GtYoutube {
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_youtube_db_version", GT_YOUTUBE_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_youtube_db_version" );
    
    if( $installed_ver != GT_YOUTUBE_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function(){
      return register_widget('GtYoutube_Widget');
    });
    
    add_action("init",function() {
      // if(!is_admin()) {
        wp_enqueue_style( 'gunner_technology_youtube', GT_YOUTUBE_URL.'css/style.css');
        wp_enqueue_style( 'fancybox', GT_YOUTUBE_URL.'css/jquery.fancybox.css');

        wp_enqueue_script( 'jqueryswfobject', GT_YOUTUBE_URL.'js/lib/jquery.swfobject.js', array('jquery'));
        wp_enqueue_script( 'youtube_gallery', GT_YOUTUBE_URL.'js/lib/jquery.youtubeGallery.js', array('swfobject'));
        wp_enqueue_script( 'fancybox', GT_YOUTUBE_URL.'js/lib/fancybox/jquery.fancybox.js', array('jquery'));
        wp_enqueue_script( 'gunner_technology_youtube', GT_YOUTUBE_URL.'js/script.js', array('youtube_gallery','fancybox'));
      // }
    });
  }
  
  public static function setup() {
    self::update_db_check();
    $gunner_technology_youtube = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

class GtYoutube_Widget extends WP_Widget {
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-youtube', 'description' => 'A widget that displays Youtube videos.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-youtube' );
    
    parent::__construct( 
      'gunner_technology_youtube', 
      'Youtube Widget', 
      array( 'description' => 'A Youtube Like Box', 'classname' => 'gunner-technology-youtube' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $description = do_shortcode($instance['description']);
     
     $name = $instance['username'];
     $max_videos = $instance['max_videos'];
     $video_player_prefix = $instance['video_player_prefix'];
     $video_width = intval($instance['video_width']);
     $video_height = intval($video_width*0.60);
     $play_list_id = $instance['play_list_id'];
     $feed_type = $instance['feed_type'];
     $dom_id = $name . '-' . $this->id;


     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     <?php if($description): ?>
       <p class="video-description"><?php echo $description ?></p>
     <?php endif; ?>
     
     <div 
       class="youtube-drop"
       data-username="<?php echo $instance['username'] ?>"
       data-max_videos="<?php echo $instance['max_videos'] ?>"
       data-video_width="<?php echo $instance['video_width'] ?>"
       data-feed_type="<?php echo $instance['feed_type'] ?>"
       data-thumbnail="<?php echo $instance['thumbnail'] ?>"
       data-use_lightbox="<?php echo $instance['use_lightbox'] ?>"
       data-play_list_id="<?php echo $instance['play_list_id'] ?>"
       >
      </div>
     
     <?php if($instance['more_url']): ?>
       <a class="call-to-action" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
     <?php endif; ?>
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['username'] = strip_tags( $new_instance['username'] );
    $instance['description'] = $new_instance['description'];
    $instance['max_videos'] = intval( $new_instance['max_videos'] );
    $instance['feed_type'] = strip_tags( $new_instance['feed_type'] );
    $instance['video_player_prefix'] = strip_tags( $new_instance['video_player_prefix'] );
    $instance['video_width'] = intval( $new_instance['video_width'] );
    $instance['play_list_id'] = $new_instance['play_list_id'];
    $instance['more_slug'] = $new_instance['more_slug'];
    $instance['more_url'] = $new_instance['more_url'];
    $instance['thumbnail'] = $new_instance['thumbnail'];
    $instance['use_lightbox'] = (isset($new_instance['use_lightbox']) && $new_instance['use_lightbox'] ? 1 : 0);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 
      'thumbnail' => 'square', 
      'more_slug' => '&#187; View All Videos', 
      'more_url' => '', 
      'title' => 'Latest Video', 
      'feed_type' => 'favorites', 
      'username' => '', 
      'max_videos' => '4', 
      'video_width' => '511', 
      "play_list_id" => "", 
      "video_player_prefix" => "",
      "use_lightbox" => false,
      "description" => '')
    );
    ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'max_videos' ); ?>">Max Videos:</label><br />
      <input id="<?php echo $this->get_field_id( 'max_videos' ); ?>" name="<?php echo $this->get_field_name( 'max_videos' ); ?>" value="<?php echo $instance['max_videos']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'video_width' ); ?>">Player Width:</label><br />
      <input id="<?php echo $this->get_field_id( 'video_width' ); ?>" name="<?php echo $this->get_field_name( 'video_width' ); ?>" value="<?php echo $instance['video_width']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">Thumbnail:</label><br />
      <select id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>">
        <?php foreach(array('square','full','fit') as $thumbnail): ?>
          <option value="<?php echo $thumbnail ?>"
            <?php if($thumbnail == $instance['thumbnail']){ echo 'selected="selected"';} ?>
          ><?php echo $thumbnail ?></option>
        <?php endforeach; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'feed_type' ); ?>">Feed Type:</label><br />
      <select class="gt_video_feed_type" id="<?php echo $this->get_field_id( 'feed_type' ); ?>" name="<?php echo $this->get_field_name( 'feed_type' ); ?>">
        <?php foreach(array('favorites','uploaded','playlist','playlists') as $feed_type): ?>
          <option value="<?php echo $feed_type ?>"
            <?php if($feed_type == $instance['feed_type']){ echo 'selected="selected"';} ?>
          ><?php echo $feed_type ?></option>
        <?php endforeach; ?>
      </select>
    </p>

    <p<?php echo $instance['feed_type'] != 'playlist' ? ' style="display:none;"' : '' ?> class="gt_video_play_list_id">
      <label for="<?php echo $this->get_field_id( 'play_list_id' ); ?>">Playlist Id:</label><br />
      <input id="<?php echo $this->get_field_id( 'play_list_id' ); ?>" name="<?php echo $this->get_field_name( 'play_list_id' ); ?>" value="<?php echo $instance['play_list_id']; ?>" />
    </p>

    <p<?php echo $instance['play_list_id'] ? ' style="display:none;"' : '' ?> class="gt_video_username">
      <label for="<?php echo $this->get_field_id( 'username' ); ?>">Username:</label><br />
      <input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'more_slug' ); ?>">View More Text:</label><br />
      <input id="<?php echo $this->get_field_id( 'more_slug' ); ?>" name="<?php echo $this->get_field_name( 'more_slug' ); ?>" value="<?php echo $instance['more_slug']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'more_url' ); ?>">View More URL:</label><br />
      <input id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo $instance['more_url']; ?>" />
    </p>

    <!--p>
      <label for="<?php echo $this->get_field_id( 'video_player_prefix' ); ?>">Player ID (optional):</label><br />
      <input id="<?php echo $this->get_field_id( 'video_player_prefix' ); ?>" name="<?php echo $this->get_field_name( 'video_player_prefix' ); ?>" value="<?php echo $instance['video_player_prefix']; ?>" />
    </p-->

    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label><br />
      <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
    </p>
    
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['use_lightbox'], true) ?> id="<?php echo $this->get_field_id('use_lightbox'); ?>" name="<?php echo $this->get_field_name('use_lightbox'); ?>" />
      <label for="<?php echo $this->get_field_id('of_user'); ?>"><?php _e('Open Player in Lightbox?'); ?></label>
    </p>
    
    <?php 
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtYoutube', 'activate') );
register_activation_hook( __FILE__, array('GtYoutube', 'deactivate') );
register_activation_hook( __FILE__, array('GtYoutube', 'uninstall') );

add_action('plugins_loaded', array('GtYoutube', 'setup') );