<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Login_Bars
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Login_Bars.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Login_Bars extends Hbgs_Widget {
  function __construct() {
  	/* Widget settings. */
  	$widget_ops = array( 'classname' => 'login_bar', 'description' => 'A widget that displays a login bar.' );

  	/* Widget control settings. */
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'login-bar-widget' );

  	/* Create the widget. */
  	parent::__construct( 'login-bar-widget', 'Login Bar Widget', $widget_ops, $control_ops );
  }
  
  function render( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$slug = $instance['slug'];
		$background_path = $instance['background_path'];
		$action_url = $instance['action_url'];
		$register_url = $instance['register_url'];
		$forgot_url = $instance['forgot_url'];
		$button_path = $instance['button_path'];

		/* Before widget (defined by themes). */
		echo $before_widget;
		?>
    <div class="grid_24 alpha omega" 
      <?php if($background_path): ?>
        style="background-image:url(<?php echo $background_path ?>);"
      <?php endif; ?>
    >
      <div class="alpha grid_12">
        <hgroup
          <?php if($background_path): ?>
            class="ir"
          <?php endif; ?>
        >
          <?php echo $before_title . $title . $after_title ?>
        </hgroup>
      </div>
      <div class="omega grid_12">
        <div class="alpha grid_4 slug"><?php echo $slug ?></div>
        <form class="omega grid_8" action="<?php echo $action_url ?>">
          <div>
            <input type="text" name="username" />
            <input type="password" name="password" />
            <input type="image" src="<?php echo $button_path ?>" />
          </div>
          <div>
            <a href="<?php echo $forgot_url ?>">Register Now &#187;</a> 
            <a href="<?php echo $register_url ?>">Forgot Your Password? &#187;</a>
          </div>
        </form>
      </div>
    </div>
    <?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['slug'] = strip_tags( $new_instance['slug'] );
		$instance['background_path'] = strip_tags( $new_instance['background_path'] );
		$instance['action_url'] = strip_tags( $new_instance['action_url'] );
		$instance['register_url'] = strip_tags( $new_instance['register_url'] );
		$instance['forgot_url'] = strip_tags( $new_instance['forgot_url'] );
		$instance['button_path'] = strip_tags( $new_instance['button_path'] );

		return $instance;
	}
	
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Login', 'slug' => "", "button_path" => "", "background_path" => "", "action_url" => "", "forgot_url" => "", "register_url" => "");
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'slug' ); ?>">Slug:</label>
			<input id="<?php echo $this->get_field_id( 'slug' ); ?>" name="<?php echo $this->get_field_name( 'slug' ); ?>" value="<?php echo $instance['slug']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'background_path' ); ?>">Background Path:</label>
			<input id="<?php echo $this->get_field_id( 'background_path' ); ?>" name="<?php echo $this->get_field_name( 'background_path' ); ?>" value="<?php echo $instance['background_path']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'button_path' ); ?>">Button Path:</label>
			<input id="<?php echo $this->get_field_id( 'button_path' ); ?>" name="<?php echo $this->get_field_name( 'button_path' ); ?>" value="<?php echo $instance['button_path']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'action_url' ); ?>">Action URL:</label>
			<input id="<?php echo $this->get_field_id( 'action_url' ); ?>" name="<?php echo $this->get_field_name( 'action_url' ); ?>" value="<?php echo $instance['action_url']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'forgot_url' ); ?>">Forgot URL:</label>
			<input id="<?php echo $this->get_field_id( 'forgot_url' ); ?>" name="<?php echo $this->get_field_name( 'forgot_url' ); ?>" value="<?php echo $instance['forgot_url']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'register_url' ); ?>">Register URL:</label>
			<input id="<?php echo $this->get_field_id( 'register_url' ); ?>" name="<?php echo $this->get_field_name( 'register_url' ); ?>" value="<?php echo $instance['register_url']; ?>" />
		</p>
  	
		
		<?php
  }
}