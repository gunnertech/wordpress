<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Facebook
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Like_Box.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Facebook_Like_Box extends Hbgs_Widget {
  function __construct() {
  	$widget_ops = array( 'classname' => 'hbgs-facebook-like', 'description' => 'A widget that displays a Facebook like box.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'hbgs-facebook-like' );
  	
  	parent::__construct( 'hbgs-facebook-like', 'Facebook Like Widget', $widget_ops, $control_ops );
  }
  
  function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['username'];
		$width = isset($instance['width']) ? $instance['width'] : 250;
		$height = isset($instance['height']) ? $instance['height'] : 250;
		$connections = isset($instance['connections']) ? $instance['connections'] : 8;
		$show_stream = isset($instance['stream']) ? $instance['stream'] : false;
		$show_header = isset($instance['header']) ? $instance['header'] : false;
		$show_logobar = isset($instance['logobar']) ? $instance['logobar'] : false;
    $identifier = intval($name);
    $follow_slug = isset($instance['follow_slug']) ? $instance['follow_slug'] : 'Visit Me';
    $link = $identifier > 0 ? "http://facebook.com/pages/pages/$identifier" : "http://facebook.com/$name";
    ?>
		<?php echo $before_widget ?>
      <?php if ( $title ) echo $before_title . do_shortcode($title) . $after_title; ?>
      <?php if($identifier > 0): ?>
        <fb:fan css="<?php echo get_stylesheet_directory_uri().'/css/facebook.css' ?>?25" connections="<?php echo $connections ?>" logobar="<?php echo $show_logobar ? 'true' : 'false' ?>" height="<?php echo $height ?>" width="<?php echo $width ?>" profile_id="<?php echo $identifier ?>" stream="<?php echo $show_stream ? 'true' : 'false' ?>" header="<?php echo $show_header ? 'true' : 'false' ?>"></fb:fan>
      <?php else: ?>
        <fb:fan css="<?php echo get_stylesheet_directory_uri().'/css/facebook.css' ?>?25" connections="<?php echo $connections ?>" logobar="<?php echo $show_logobar ? 'true' : 'false' ?>" height="<?php echo $height ?>" width="<?php echo $width ?>" name="<?php echo $name ?>" href="http://www.facebook.com/<?php echo $name ?>" stream="<?php echo $show_stream ? 'true' : 'false' ?>" header="<?php echo $show_header ? 'true' : 'false' ?>"></fb:fan>
      <?php endif; ?>
      <hgroup class="more clearfix"><h4><a href="<?php echo $link ?>"><?php echo $follow_slug ?></a></h4></hgroup>
    <?php echo $after_widget ?>
    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
    $instance = array( 'header' => 0, 'stream' => 0, 'logobar' => 0);
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

		return $instance;
	}
	
	function form( $instance ) {

		$defaults = array( 'title' => '', 'username' => 'YourFacebookPageName', 'stream' => false, 'header' => false, 'logobar' => false, 'connections' => 8, 'width' => 250, 'height' => 250, 'follow_slug' => "Visit Me");		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
    <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>">Facebook Page Name:</label>
			<input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'connections' ); ?>">Connections:</label>
			<input id="<?php echo $this->get_field_id( 'connections' ); ?>" name="<?php echo $this->get_field_name( 'connections' ); ?>" value="<?php echo $instance['connections']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>">Width:</label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>">Height:</label>
			<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'follow_slug' ); ?>">Follow Slug:</label>
			<input id="<?php echo $this->get_field_id( 'follow_slug' ); ?>" name="<?php echo $this->get_field_name( 'follow_slug' ); ?>" value="<?php echo $instance['follow_slug']; ?>" />
		</p>
		
		<p>
      <input class="checkbox" type="checkbox" <?php checked($instance['header'], true) ?> id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>" />
      <label for="<?php echo $this->get_field_id('header'); ?>"><?php _e('Show Header?'); ?></label><br />
      <input class="checkbox" type="checkbox" <?php checked($instance['stream'], true) ?> id="<?php echo $this->get_field_id('stream'); ?>" name="<?php echo $this->get_field_name('stream'); ?>" />
      <label for="<?php echo $this->get_field_id('stream'); ?>"><?php _e('Show Stream?'); ?></label><br />
      <input class="checkbox" type="checkbox" <?php checked($instance['logobar'], true) ?> id="<?php echo $this->get_field_id('logobar'); ?>" name="<?php echo $this->get_field_name('logobar'); ?>" />
      <label for="<?php echo $this->get_field_id('logobar'); ?>"><?php _e('Show Logo Bar?'); ?></label>
    </p>
    
		<?php
  }
}