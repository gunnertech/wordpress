<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Connection
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Text.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Connections extends Hbgs_Widget {
  
  protected $networks = array('Foursquare','Google','LinkedIn','Facebook','Twitter','YouTube');

	function __construct() {
		$widget_ops = array('classname' => 'hbgs-widgets-connections', 'description' => __('List of social networks to connect with'));
		$control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'hbgs-widgets-connections');

		parent::__construct('hbgs-widgets-connections', __('Connection Widgets'), $widget_ops, $control_ops);
	}

	function render( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		?>
		
		<?php echo $before_widget ?>
      <?php if ( !empty( $title ) ) { echo $before_title . do_shortcode($title) . $after_title; } ?>
      <div class="connection-body">
        <?php foreach($this->networks as $network): ?>
          <?php if(!empty($instance[strtolower($network).'_url'])): ?>
            <a class="<?php echo strtolower($network) ?>" href="<?php echo $instance[strtolower($network).'_url'] ?>"><?php echo $network ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
		<?php echo $after_widget ?>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
	  foreach($this->networks as $network) {
	    $instance[strtolower($network).'_url'] = $new_instance[strtolower($network).'_url'];
	  }
		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
		<?php foreach($this->networks as $network): $key = strtolower($network).'_url'; ?>
		  <p><label for="<?php echo $this->get_field_id($key); ?>"><?php echo $network ?> URL:</label>
  		<input class="widefat" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>" type="text" value="<?php echo (array_key_exists($key,$instance) ? esc_attr($instance[$key]) : "") ?>" /></p>
    <?php endforeach; ?>
<?php
	}
}