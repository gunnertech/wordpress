<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Query
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Query.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Poll extends Hbgs_Widget {
  
  function __construct() {
  	$widget_ops = array( 'classname' => 'poll-widget', 'description' => 'A widget that lets you add a polldaddy poll.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'poll-widget' );
  	
  	parent::__construct( 'poll-widget', 'Poll Widget', $widget_ops, $control_ops );
  }
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['poll_id'] = strip_tags($new_instance['poll_id']);
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	
	function render( $args, $instance ) {
	  $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
	  extract( $args ); ?>
	  <?php echo $before_widget; ?>
	    <?php if ( !empty( $title ) ) { echo $before_title . do_shortcode($title) . $after_title; } ?>
	    <script src="http://static.polldaddy.com/p/<?php echo $instance['poll_id'] ?>.js"></script>
	  <?php echo $after_widget; ?>
	  <?php
  }
  
	
	function form( $instance ) {	  
		$defaults = array('title' => "", "poll_id" => "");
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$title = strip_tags($instance['title']);
		?>
	  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
		<p>
		  <label for="<?php echo $this->get_field_id('poll_id'); ?>"><?php _e('Polldaddy Poll Id:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('poll_id'); ?>" name="<?php echo $this->get_field_name('poll_id'); ?>" type="text" value="<?php echo esc_attr($instance['poll_id']); ?>" />
  	</p>
		<?php
  }
}