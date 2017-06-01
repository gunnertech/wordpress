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

class Hbgs_Widgets_Search extends Hbgs_Widget {
  
  function __construct() {
  	$widget_ops = array('classname' => 'widget_search', 'description' => __( "A search form for your site") );
		parent::__construct('search', __('Search'), $widget_ops);
  }
	
	function render( $args, $instance ) {
	  extract($args);
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		// Use current theme search form if it exists
		get_search_form();

		echo $after_widget;
  }
  
	
		function form( $instance ) {
  		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
  		$title = $instance['title'];
  ?>
  		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
  <?php
  	}

  	function update( $new_instance, $old_instance ) {
  		$instance = $old_instance;
  		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
  		$instance['title'] = strip_tags($new_instance['title']);
  		return $instance;
  	}
  
}