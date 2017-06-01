<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Comments
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Comments.php 2010-11-18 codyswann
 */



class Hbgs_Widgets_Comments extends Hbgs_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'grided_recent_comments_widget', 'description' => __( 'The most recent comments' ) );

		parent::__construct('grided_recent_comments_widget', __('Comments'), $widget_ops);

		$this->alt_option_name = 'grided_recent_comments_widget';

		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array(&$this, 'recent_comments_style') );

		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
	}

	function recent_comments_style() { ?>
	<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
<?php
	}

	function render( $args, $instance ) {
		global $comments, $comment;

 		extract($args, EXTR_SKIP);
 		$output = '';
 		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Comments') : $instance['title']);

		if ( ! $number = (int) $instance['number'] )
 			$number = 5;
 		else if ( $number < 1 )
 			$number = 1;

		$comments = get_comments( array( 'number' => $number, 'status' => 'approve' ) );
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;
		$output .= '<div class="recent-comments-wrapper"><div class="recent-comments">';

		$output .= '<ul id="recentcomments">';
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
				$output .=  '<li class="recentcomments">';
        $output .=  '<blockquote class="hyphenate">"'.substr(strip_tags($comment->comment_content),0,85).'..."</blockquote>';
				$output .=  '<p><cite> - ' . get_comment_author_link() . '</cite>';
				$output .=  ' on <a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a></p>'; 
				$output .=  '</li>';
			}
 		}
		$output .= '</ul>';
		$output .= '</div></div>';
		$output .= $after_widget;

		echo $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_comments']) )
			delete_option('widget_recent_comments');
        
		return $instance;
	}

	function form( $instance ) {
	  $defaults = array('title' => 'Latest Comments', 'number' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}