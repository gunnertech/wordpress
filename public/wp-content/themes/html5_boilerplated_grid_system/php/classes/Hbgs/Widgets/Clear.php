<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Clear
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Clear.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Clear extends Hbgs_Widget {
  function __construct() {
  	$widget_ops = array( 'classname' => 'clear-widget', 'description' => 'A widget that will clear a grid layout.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'clear-widget' );

  	parent::__construct( 'clear-widget', 'Grid Clear', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) { extract( $args ); ?>
      <?php echo $before_widget ?>
        <div class="clear"></div>
      <?php echo $after_widget ?>
  <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;		
		
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array();
  }
}