<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Videos
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Videos.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Google_Videos extends Hbgs_Widget {

  function __construct() {
    if(is_admin()) {
      wp_enqueue_script( 'jqueryswfobject', get_bloginfo('template_url').'/js/libs/jquery.swfobject.js', array("jquery"));
      wp_enqueue_script( 'hbgs_youtube', get_bloginfo('template_url').'/js/mylibs/youtube.js', array('jquery','jqueryswfobject'));
    }
    
  	$widget_ops = array( 'classname' => 'videos', 'description' => 'A widget that displays a user\'s YouTube videos.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'videos-widget' );

  	parent::__construct( 'videos-widget', 'Videos Widget', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
		$description = do_shortcode($instance['description']);
		$name = $instance['youtube_username'];
		$max_videos = $instance['max_videos'];
		$video_player_prefix = $instance['video_player_prefix'];
    $video_width = intval($instance['video_width']);
    $video_height = intval($video_width*0.60);
    $play_list_id = $instance['play_list_id'];
    $feed_type = $instance['feed_type'];
    $dom_id = $name . '-' . $this->id;
	
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
    ?>
    <?php if($description): ?>
      <p class="video-description"><?php echo $description ?></p>
    <?php endif; ?>
    <?php if(!$instance['video_player_prefix']): ?>
      <div class="youtube-video" id="<?php echo $dom_id ?>-youtube-video" style="width:<?php echo $video_width ?>px; height:<?php echo $video_height ?>px;"></div>
      <div class="youtube-video-meta" id="<?php echo $dom_id ?>-youtube-video-meta" data-playlistid="<?php echo $play_list_id ?>"></div>
    <?php else: ?>
      <div class="youtube-video-meta" data-playlistid="<?php echo $play_list_id ?>" id="<?php echo $dom_id ?>-youtube-video-meta" style="visibility:hidden;"></div>
    <?php endif; ?>
    <hgroup class="more"><h4><a href="<?php echo $instance['view_more_url'] ?>"><?php echo $instance['view_more_text'] ?></a></h4></hgroup>
    <?php
		echo $after_widget;
	}
	
	function print_scripts($id,$instance,$scripts=null) {
	  $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
		$description = do_shortcode($instance['description']);
		$name = $instance['youtube_username'];
		$max_videos = $instance['max_videos'];
		$video_player_prefix = $instance['video_player_prefix'];
    $video_width = intval($instance['video_width']);
    $video_height = intval($video_width*0.60);
    $play_list_id = $instance['play_list_id'];
    $feed_type = $instance['feed_type'];
    $dom_id = $name . '-' . $id;
    $instance['scripts'] .= '
    $(function(){
      $.hbgs_video_gallery({
        g_youtube_username:"'. $name .'",
        g_max_videos:'. $max_videos .',
        g_video_width:'. $video_width .',
        g_video_height:'. $video_height .',
        g_video_feed_type:"'. $feed_type .'",
        g_widget_id: "'. $dom_id .'",
        g_player_id: "'. $video_player_prefix .'",
        g_video_thumbnail: "'. $instance['thumbnail'] .'",
        g_play_list_id: "'. $play_list_id .'"
      });
    });';
    parent::print_scripts($id,$instance);
  }
  
  function print_default_scripts() {
    wp_enqueue_script( 'jqueryswfobject', get_bloginfo('template_url').'/js/libs/jquery.swfobject.js', array("jquery"));
    wp_enqueue_script( 'hbgs_youtube', get_bloginfo('template_url').'/js/mylibs/youtube.js', array('jquery','jqueryswfobject'));
  }
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = $new_instance['title'];
		$instance['youtube_username'] = strip_tags( $new_instance['youtube_username'] );
		$instance['description'] = $new_instance['description'];
		$instance['max_videos'] = intval( $new_instance['max_videos'] );
		$instance['feed_type'] = strip_tags( $new_instance['feed_type'] );
		$instance['video_player_prefix'] = strip_tags( $new_instance['video_player_prefix'] );
		$instance['video_width'] = intval( $new_instance['video_width'] );
		$instance['play_list_id'] = $new_instance['play_list_id'];
		$instance['view_more_text'] = $new_instance['view_more_text'];
		$instance['view_more_url'] = $new_instance['view_more_url'];
		$instance['thumbnail'] = $new_instance['thumbnail'];

		return $instance;
	}
	
	function form( $instance ) {    
		/* Set up some default widget settings. */
		$defaults = array( 'thumbnail' => 'square', 'view_more_text' => '&#187; View All Videos', 'view_more_url' => '', 'title' => 'Latest Video', 'feed_type' => 'favorites', 'youtube_username' => '', 'max_videos' => '4', 'video_width' => '511', "play_list_id" => null, "video_player_prefix" => null, "description" => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
    <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'max_videos' ); ?>">Max Videos:</label>
			<input id="<?php echo $this->get_field_id( 'max_videos' ); ?>" name="<?php echo $this->get_field_name( 'max_videos' ); ?>" value="<?php echo $instance['max_videos']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'video_width' ); ?>">Player Width:</label>
			<input id="<?php echo $this->get_field_id( 'video_width' ); ?>" name="<?php echo $this->get_field_name( 'video_width' ); ?>" value="<?php echo $instance['video_width']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">Thumbnail:</label>
			<select id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>">
  			<?php foreach(array('square','full') as $thumbnail): ?>
  			  <option value="<?php echo $thumbnail ?>"
  			    <?php if($thumbnail == $instance['thumbnail']){ echo 'selected="selected"';} ?>
  			  ><?php echo $thumbnail ?></option>
  		  <?php endforeach; ?>
			</select>
		</p>
				
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_type' ); ?>">Feed Type:</label>
			<select class="hbgs_video_feed_type" id="<?php echo $this->get_field_id( 'feed_type' ); ?>" name="<?php echo $this->get_field_name( 'feed_type' ); ?>">
  			<?php foreach(array('favorites','uploaded','playlist','playlists') as $feed_type): ?>
  			  <option value="<?php echo $feed_type ?>"
  			    <?php if($feed_type == $instance['feed_type']){ echo 'selected="selected"';} ?>
  			  ><?php echo $feed_type ?></option>
  		  <?php endforeach; ?>
			</select>
		</p>
		
		<p<?php echo $instance['feed_type'] != 'playlist' ? ' style="display:none;"' : '' ?> class="hbgs_video_play_list_id">
			<label for="<?php echo $this->get_field_id( 'play_list_id' ); ?>">Playlist Id:</label>
			<input id="<?php echo $this->get_field_id( 'play_list_id' ); ?>" name="<?php echo $this->get_field_name( 'play_list_id' ); ?>" value="<?php echo $instance['play_list_id']; ?>" />
		</p>
		
	  <p<?php echo $instance['play_list_id'] ? ' style="display:none;"' : '' ?> class="hbgs_video_username">
			<label for="<?php echo $this->get_field_id( 'youtube_username' ); ?>">Username:</label>
			<input id="<?php echo $this->get_field_id( 'youtube_username' ); ?>" name="<?php echo $this->get_field_name( 'youtube_username' ); ?>" value="<?php echo $instance['youtube_username']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'view_more_text' ); ?>">View More Text:</label>
			<input id="<?php echo $this->get_field_id( 'view_more_text' ); ?>" name="<?php echo $this->get_field_name( 'view_more_text' ); ?>" value="<?php echo $instance['view_more_text']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'view_more_url' ); ?>">View More URL:</label>
			<input id="<?php echo $this->get_field_id( 'view_more_url' ); ?>" name="<?php echo $this->get_field_name( 'view_more_url' ); ?>" value="<?php echo $instance['view_more_url']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'video_player_prefix' ); ?>">Player ID (optional):</label>
			<input id="<?php echo $this->get_field_id( 'video_player_prefix' ); ?>" name="<?php echo $this->get_field_name( 'video_player_prefix' ); ?>" value="<?php echo $instance['video_player_prefix']; ?>" />
		</p>
		
		<p>
		  <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label>
		  <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
		</p>
		
		<?php
  }
}