<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Legal
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Legal.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Legal extends Hbgs_Widget {
  function __construct() {
  	$widget_ops = array( 'classname' => 'legal-box', 'description' => 'A widget that displays the sites legal information.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'legal' );

  	parent::__construct( 'legal', 'Legal Info', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) {
		extract( $args );
		?>
		
		<?php echo $before_widget ?>
      <small>
        <strong>&copy;<?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></strong> Copyright - All Rights Reserved.
      </small>
    <?php echo $after_widget ?>
    
    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array();		
		$instance = wp_parse_args( (array) $instance, $defaults );
  }
}