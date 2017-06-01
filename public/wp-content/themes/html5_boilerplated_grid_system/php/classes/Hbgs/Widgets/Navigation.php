<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Navigation
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Navigation.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Navigation extends Hbgs_Widget {

  function __construct() {
  	/* Widget settings. */
  	$widget_ops = array( 'classname' => 'hbgs-primary-nav', 'description' => 'A widget that displays the primary navigation menu for this site.' );

  	/* Widget control settings. */
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'hbgs-primary-nav-widget' );

  	/* Create the widget. */
  	parent::__construct( 'hbgs-primary-nav-widget', 'Navigation', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) {
		extract( $args );

		$nav_type = $instance['nav_type'];
		$title = $instance['title'];
    $nav_location = isset($instance['nav_location']) ? $instance['nav_location'] : 'primary';
    
    ?>
		<?php echo $before_widget ?>
      <div class="nav">
        <?php if ( $title ){ echo $before_title . $title . $after_title; } ?>
        <nav class="primary-nav <?php echo $nav_type ?>">
          <?php echo do_shortcode(wp_nav_menu( array( 'echo' => false, 'before' => '<span class="separator">|</span>', 'container' => 'ul', 'menu_class' => 'menu clearfix', 'theme_location' => $nav_location ) )); ?>
        </nav>
      </div>
    <?php echo $after_widget ?>
    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
    $instance['title'] = $new_instance['title'];
		$instance['nav_type'] = $new_instance['nav_type'];
		$instance['nav_location'] = $new_instance['nav_location'];
		
    if(isset($new_instance['new_nav_location']) && isset($new_instance['new_nav_description'])) {
      $dynamic_nav_menus_string = get_option('dynamic_nav_menus');
      $dynamic_nav_menus = !!$dynamic_nav_menus_string ? json_decode($dynamic_nav_menus_string) : array();
      $dynamic_nav_menus[] = array(
        strtolower(preg_replace('/\W/',"-",$new_instance['new_nav_location'])),
        $new_instance['new_nav_description']
      );
      register_nav_menu(strtolower(preg_replace('/\W/',"-",$new_instance['new_nav_location'])), $new_instance['new_nav_description'] );
      update_option('dynamic_nav_menus' , json_encode($dynamic_nav_menus) );
    }
				
		return $instance;
	}
	
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'nav_type' => 'horizontal', 'nav_location' => 'primary');
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$nav_types = array('vertical','horizontal');
		$navs = get_registered_nav_menus();
		
		?>
	  <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
    <p>
			<label for="<?php echo $this->get_field_id( 'nav_type' ); ?>">Display Type:</label>
			<select id="<?php echo $this->get_field_id( 'nav_type' ); ?>" name="<?php echo $this->get_field_name( 'nav_type' ); ?>">
			<?php foreach($nav_types as $nav_type): ?>
			  <option <?php echo  $nav_type == $instance['nav_type'] ? 'selected="selected"' : '' ?> value="<?php echo $nav_type ?>"><?php echo $nav_type ?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'nav_location' ); ?>">Nav Location:</label>
			<select id="<?php echo $this->get_field_id( 'nav_location' ); ?>" name="<?php echo $this->get_field_name( 'nav_location' ); ?>">
			<?php foreach($navs as $key => $nav): ?>
			  <option <?php selected($instance['nav_location'],$key) ?> value="<?php echo $key ?>"><?php echo $key ?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>Need to add a Nav Location? Fill in the fields below and click "Save"</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'new_nav_location' ); ?>">New Nav Location:</label>
			<input id="<?php echo $this->get_field_id( 'new_nav_location' ); ?>" name="<?php echo $this->get_field_name( 'new_nav_location' ); ?>" value="" />
		</p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'new_nav_description' ); ?>">New Nav Description:</label>
			<input id="<?php echo $this->get_field_id( 'new_nav_description' ); ?>" name="<?php echo $this->get_field_name( 'new_nav_description' ); ?>" value="" />
		</p>
		<?php
  }
}