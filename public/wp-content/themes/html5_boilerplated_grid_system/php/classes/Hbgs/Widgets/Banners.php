<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Banners
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Banners.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Banners extends Hbgs_Widget {
  function __construct() {
  	$widget_ops = array( 'classname' => 'banners', 'description' => 'A widget that displays a banner image with a link.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'banners-widget' );

  	parent::__construct( 'banners-widget', 'Banners Widget', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) {
		extract( $args );
		
		$image_url = strip_tags($instance['image_url']);
		$link_url = strip_tags($instance['link_url']);
		$image_alt = strip_tags($instance['image_alt']);

    ?>
    <?php echo $before_widget ?>
      <?php if ( isset($title) ){echo $before_title . $title . $after_title;} ?>
      <figure class="banner"><?php if($link_url): ?><a href="<?php echo $link_url ?>"><?php endif; ?><img alt="<?php echo $image_alt ?>" src="<?php echo $image_url ?>" /><?php if($link_url): ?></a><?php endif; ?></figure>
    <?php echo $after_widget ?>
    <?php 
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['image_url'] = strip_tags( $new_instance['image_url'] );
		$instance['image_alt'] = strip_tags( $new_instance['image_alt'] );
		$instance['link_url'] = strip_tags( $new_instance['link_url'] );

		return $instance;
	}
	
	function form( $instance ) {

		$defaults = array( 'image_alt' => 'banner', 'image_url' => '', 'link_url' => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
    <p>
			<label for="<?php echo $this->get_field_id( 'image_url' ); ?>">Image URL:</label>
			<input id="<?php echo $this->get_field_id( 'image_url' ); ?>" name="<?php echo $this->get_field_name( 'image_url' ); ?>" value="<?php echo $instance['image_url']; ?>" />
		</p>
	
	  <p>
			<label for="<?php echo $this->get_field_id( 'image_alt' ); ?>">Image ALT Text:</label>
			<input id="<?php echo $this->get_field_id( 'image_alt' ); ?>" name="<?php echo $this->get_field_name( 'image_alt' ); ?>" value="<?php echo $instance['image_alt']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'link_url' ); ?>">Link URL:</label>
			<input id="<?php echo $this->get_field_id( 'link_url' ); ?>" name="<?php echo $this->get_field_name( 'link_url' ); ?>" value="<?php echo $instance['link_url']; ?>" />
		</p>

		<?php
  }
}
