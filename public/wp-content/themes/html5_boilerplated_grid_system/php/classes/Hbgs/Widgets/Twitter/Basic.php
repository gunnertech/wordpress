<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Basic
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Basic.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Twitter_Basic extends Hbgs_Widget {
  function __construct() {
  	/* Widget settings. */
  	$widget_ops = array( 'classname' => 'hbgs-twitter', 'description' => 'A widget that displays a user\'s Twitter Info.' );

  	/* Widget control settings. */
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'twitter-widget' );

  	/* Create the widget. */
  	parent::__construct( 'twitter-widget', 'Twitter Basic', $widget_ops, $control_ops );
  }
  
  function print_scripts($id,$instance,$scripts=null) {
    $instance['scripts'] .= '
    $(function(){
      $("#'.$id.' #'.$instance['twitter_username'].'-twitter-box").twitterStatus({userName:"'.$instance['twitter_username'].'", formatDate: _date, numResults:'.$instance['num_results'].'});
      $("#'.$id.' #'.$instance['twitter_username'].'-follow").jAnyWhere({key:"'.$instance['api_key'].'"},{username: function(node) { return node.title; }});
    });';
    parent::print_scripts($id,$instance);
  }
  
  function print_default_scripts() {
    wp_enqueue_script( 'hbgs_twitter', get_bloginfo('template_url').'/js/mylibs/twitter.js', array('jquery'));
  }
  
  function render( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['twitter_username'];
		$description = $instance['description'];
		$num_results = intval($instance['num_results']);
		$follow_slug = $instance['follow_slug'];
		$api_key = strip_tags($instance['api_key']);
		
		if($num_results < 1){ $num_results = 1; }
		
		echo $before_widget;

    ?>
    <script>
      var g_twitter_username = '<?php echo $name ?>';
      var g_twitter_num_results = '<?php echo $num_results ?>';
      var g_twitter_api_key = '<?php echo $api_key ?>';
    </script>
    <?php if ( $title ) {
			echo $before_title . $title . $after_title;
    } ?>
    <?php if($description): ?>
      <p><?php echo $description ?></p>
    <?php endif; ?>
    <div id="<?php echo $name ?>-twitter-box"></div>
    <hgroup class="more clearfix"><h4><a id="<?php echo $name ?>-follow" title="<?php echo $name ?>" href="http://twitter.com/<?php echo $name ?>"><?php echo $follow_slug ?></a></h4></hgroup>
    <?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username'] );
		$instance['follow_slug'] = $new_instance['follow_slug'];
		$instance['api_key'] = strip_tags($new_instance['api_key']);
		$instance['num_results'] = intval( $new_instance['num_results'] );

		return $instance;
	}
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'api_key' => '', 'title' => '', 'twitter_username' => 'YourTwitterUsername', 'description' => '', 'num_results' => 1, 'follow_slug' => "Follow Me On Twitter &#187;");
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
    <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>">Twitter Name:</label>
			<input id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" value="<?php echo $instance['twitter_username']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>">Description:</label>
			<input id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="<?php echo $instance['description']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'num_results' ); ?>">Tweets to Show:</label>
			<input id="<?php echo $this->get_field_id( 'num_results' ); ?>" name="<?php echo $this->get_field_name( 'num_results' ); ?>" value="<?php echo $instance['num_results']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'follow_slug' ); ?>">Follow Slug:</label>
			<input id="<?php echo $this->get_field_id( 'follow_slug' ); ?>" name="<?php echo $this->get_field_name( 'follow_slug' ); ?>" value="<?php echo $instance['follow_slug']; ?>" />
		</p>
		
	  <p>
			<label for="<?php echo $this->get_field_id( 'api_key' ); ?>">API Key:</label>
			<input id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" value="<?php echo $instance['api_key']; ?>" />
		</p>
		
		<?php
  }
}