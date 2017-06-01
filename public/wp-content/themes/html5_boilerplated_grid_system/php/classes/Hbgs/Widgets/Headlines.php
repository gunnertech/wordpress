<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Headlines
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Headlines.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/php/classes/Hbgs/Widget.php';

function hbgs_list_bookmarks($args = '') {
	$defaults = array(
	  'override_title' => false,
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '', 'exclude_category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'echo' => 1,
		'categorize' => 1, 'title_li' => __('Bookmarks'),
		'title_before' => '<h2>', 'title_after' => '</h2>',
		'category_orderby' => 'name', 'category_order' => 'ASC',
		'class' => 'linkcat', 'category_before' => '<li id="%id" class="%class">',
		'category_after' => '</li>'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';

	if ( $categorize ) {
		//Split the bookmarks into ul's for each category
		$cats = get_terms('link_category', array('name__like' => $category_name, 'include' => $category, 'exclude' => $exclude_category, 'orderby' => $category_orderby, 'order' => $category_order, 'hierarchical' => 0));

		foreach ( (array) $cats as $cat ) {
			$params = array_merge($r, array('category'=>$cat->term_id));
			$bookmarks = get_bookmarks($params);
			if ( empty($bookmarks) )
				continue;
			$output .= str_replace(array('%id', '%class'), array("linkcat-$cat->term_id", $class), $category_before);
			$catname = apply_filters( "link_category", ($override_title ? $override_title : $cat->name) );
			$output .= "$title_before$catname$title_after\n\t<ul class='xoxo blogroll'>\n";
			$output .= _walk_bookmarks($bookmarks, $r);
			$output .= "\n\t</ul>\n$category_after\n";
		}
	} else {
		//output one single list using title_li for the title
		$bookmarks = get_bookmarks($r);

		if ( !empty($bookmarks) ) {
			if ( !empty( $title_li ) ){
				$output .= str_replace(array('%id', '%class'), array("linkcat-$category", $class), $category_before);
				$output .= "$title_before$title_li$title_after\n\t<ul class='xoxo blogroll'>\n";
				$output .= _walk_bookmarks($bookmarks, $r);
				$output .= "\n\t</ul>\n$category_after\n";
			} else {
				$output .= _walk_bookmarks($bookmarks, $r);
			}
		}
	}

	$output = apply_filters( 'wp_list_bookmarks', $output );

	if ( !$echo )
		return $output;
	echo $output;
}

class Hbgs_Widgets_Headlines extends Hbgs_Widget {

	function __construct() {
		$widget_ops = array('description' => __( "Your headlines" ) );
		parent::__construct('headlines', __('Headlines'), $widget_ops);
	}

	function render( $args, $instance ) {
		extract($args, EXTR_SKIP);
    
    $title = isset($instance['title']) ? do_shortcode($instance['title']) : false;
		$show_description = isset($instance['description']) ? $instance['description'] : false;
		$show_name = isset($instance['name']) ? $instance['name'] : false;
		$show_rating = isset($instance['rating']) ? $instance['rating'] : false;
		$show_images = isset($instance['images']) ? $instance['images'] : false;
		$category = isset($instance['category']) ? $instance['category'] : false;
		$slug = isset($instance['slug']) ? $instance['slug'] : 'View All Headlines &#187;';
		$more_url = isset($instance['more_url']) ? $instance['more_url'] : false;
		$limit = isset($instance['limit']) ? $instance['limit'] : 11;
    
    $more_area = $more_url ? '<hgroup class="more"><h4><a href="'.$more_url.'">'.$slug.'</a></h4></hgroup>' : '';
    
    $after_widget = $more_area . $after_widget . '<div class="clear"></div></div>';
		if ( is_admin() && !$category ) {
			// Display All Links widget as such in the widgets screen
			echo $before_widget . $before_title. __('All Links') . $after_title . $after_widget;
			return;
		}
		//$before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
		$before_title = '<div class="headlines">'.$before_title;
		hbgs_list_bookmarks(apply_filters('widget_links_args', array(
		  'override_title' => $title,
		  'orderby' => 'id', 'limit' => $limit, 'order' => 'DESC',
			'title_before' => $before_title, 'title_after' => $after_title,
			'category_before' => $before_widget, 'category_after' => $after_widget,
			'show_images' => $show_images, 'show_description' => $show_description,
			'show_name' => $show_name, 'show_rating' => $show_rating,
			'category' => $category, 'class' => 'linkcat widget'
		)));
	}

	function update( $new_instance, $old_instance ) {
		$new_instance = (array) $new_instance;
		$instance = array( 'images' => 0, 'name' => 0, 'description' => 0, 'rating' => 0);
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}
		$instance['more_url'] = strip_tags( $new_instance['more_url'] );
		$instance['slug'] = $new_instance['slug'];
		$instance['title'] = $new_instance['title'];
		$instance['category'] = intval($new_instance['category']);
		$instance['limit'] = intval($new_instance['limit']);
		
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => false, 'slug' => 'View All Headlines &#187;', 'images' => false, 'name' => true, 'description' => true, 'rating' => false, 'category' => false, 'more_url' => '', 'limit' => 11);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
		$link_cats = get_terms( 'link_category');
?>
    <p><strong>This widget is deprecated. Please don't use it if you aren't already</strong></p>
		<p>
		<label for="<?php echo $this->get_field_id('category'); ?>" class="screen-reader-text"><?php _e('Select Link Category'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
		<option value=""><?php _e('All Links'); ?></option>
		<?php
		foreach ( $link_cats as $link_cat ) {
			echo '<option value="' . intval($link_cat->term_id) . '"'
				. ( $link_cat->term_id == $instance['category'] ? ' selected="selected"' : '' )
				. '>' . $link_cat->name . "</option>\n";
		}
		?>
		</select></p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['images'], true) ?> id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>" />
		<label for="<?php echo $this->get_field_id('images'); ?>"><?php _e('Show Link Image'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['name'], true) ?> id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" />
		<label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Show Link Name'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['description'], true) ?> id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" />
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Show Link Description'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['rating'], true) ?> id="<?php echo $this->get_field_id('rating'); ?>" name="<?php echo $this->get_field_name('rating'); ?>" />
		<label for="<?php echo $this->get_field_id('rating'); ?>"><?php _e('Show Link Rating'); ?></label>
		</p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'more_url' ); ?>">More URL:</label>
			<input id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo $instance['more_url']; ?>" />
		</p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">Max Headlines:</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" />
		</p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'slug' ); ?>">Slug:</label>
			<input id="<?php echo $this->get_field_id( 'slug' ); ?>" name="<?php echo $this->get_field_name( 'slug' ); ?>" value="<?php echo $instance['slug']; ?>" />
		</p>
<?php
	}
}