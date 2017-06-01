<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Thumbnails
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Thumbnails.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Flickr_Thumbnails extends Hbgs_Widget {
  function __construct() {
  	/* Widget settings. */
  	$widget_ops = array( 'classname' => 'flickr-box', 'description' => 'A widget that displays the most recent photos from a Flickr Stream.' );

  	/* Widget control settings. */
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'latest-photos-widget' );

  	/* Create the widget. */
  	parent::__construct( 'latest-photos-widget', 'Latest Photos Widget', $widget_ops, $control_ops );
  }
  
  function print_scripts($id,$instance,$scripts=null) {
    $instance['scripts'] .= '
    $(function(){
      $("#'.$id.' .widget-content div:not(.widget-content-inner)").addClass("flickr-photos clearfix").flickrStream({
        api_key: "'. $instance['api_key'] .'",
        user_id: "'. $instance['user_id'] .'",
        per_page: "'. $instance['max_photos'] .'",
        photo_height: "'. $instance['photo_height'] .'",
        photo_width: "'. $instance['photo_width'] .'",
        of_user: "'. (isset($instance['of_user']) && $instance['of_user'] ? 1 : 0) .'"
      });
    });';
    parent::print_scripts($id,$instance);
  }
  
  function print_default_styles() { 
    wp_enqueue_style( 'flightbox', get_bloginfo('template_url').'/css/jquery.flightbox.css');
  }
  
  function print_default_scripts() {
    wp_enqueue_script( 'flightbox', get_bloginfo('template_url').'/js/libs/jquery.flightbox.js', array('jquery'));
    wp_enqueue_script( 'hbgs_flickr', get_bloginfo('template_url').'/js/mylibs/flickr.js', array('flightbox'));
  }
  
  function render( $args, $instance ) {
		extract( $args );

		$title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
		$more_url = isset($instance['more_url']) ? $instance['more_url'] : false;

		
    ?>
    <?php echo $before_widget ?>
    <?php if ( $title ) {
			echo $before_title . $title . $after_title;
    } ?>
    <div></div>
    <?php if($more_url): ?>
      <hgroup class="more"><h4><a href="<?php echo $more_url ?>"><?php echo $instance['more_slug'] ?></a></h4></hgroup>
    <?php endif; ?>
    <?php echo $after_widget ?>
  <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
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
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'more_url' => '', 'more_slug' => '&#187; View All', 'title' => 'Latest Photos', 'max_photos' => '12', 'user_id' => '54108942@N02', 'api_key' => '86e47f54f1e07d1dc3e6198852645303', 'photo_height' => 75, 'photo_width' => 75, 'of_user' => false);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
    <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
	
	  <p>
			<label for="<?php echo $this->get_field_id( 'max_photo' ); ?>">Max Photos to Display:</label>
			<input id="<?php echo $this->get_field_id( 'max_photos' ); ?>" name="<?php echo $this->get_field_name( 'max_photos' ); ?>" value="<?php echo $instance['max_photos']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'api_key' ); ?>">Flickr API Key:</label>
			<input id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" value="<?php echo $instance['api_key']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'user_id' ); ?>">Flickr User ID:</label>
			<input id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" value="<?php echo $instance['user_id']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'photo_width' ); ?>">Photo Width:</label>
			<input id="<?php echo $this->get_field_id( 'photo_width' ); ?>" name="<?php echo $this->get_field_name( 'photo_width' ); ?>" value="<?php echo $instance['photo_width']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'photo_height' ); ?>">Photo Height:</label>
			<input id="<?php echo $this->get_field_id( 'photo_height' ); ?>" name="<?php echo $this->get_field_name( 'photo_height' ); ?>" value="<?php echo $instance['photo_height']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'more_url' ); ?>">More URL:</label>
			<input id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo $instance['more_url']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'more_slug' ); ?>">More Slug:</label>
			<input id="<?php echo $this->get_field_id( 'more_slug' ); ?>" name="<?php echo $this->get_field_name( 'more_slug' ); ?>" value="<?php echo $instance['more_slug']; ?>" />
		</p>
		
    <p class="of_user" id="<?php echo $this->get_field_id('of_user'); ?>-wrapper">
      <label for="<?php echo $this->get_field_id('of_user'); ?>"><?php _e('Photos of user?'); ?></label>
      <input type="checkbox" <?php checked($instance['of_user']) ?> id="<?php echo $this->get_field_id('of_user'); ?>" name="<?php echo $this->get_field_name('of_user'); ?>" type="text" />
    </p>
	
		
		<?php
  }
}