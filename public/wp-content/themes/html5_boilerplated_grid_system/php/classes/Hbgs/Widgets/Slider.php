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

class Hbgs_Widgets_Slider extends Hbgs_Widget {
  protected $start_days_ago = 0;
  protected $end_days_ago = 0;
  protected $search_param_name = null;
  
  function template_select($instance) { ?>
    <select id="<?php echo $this->get_field_id('the_template'); ?>" name="<?php echo $this->get_field_name('the_template'); ?>">
  	  <?php if ($handle = opendir(hbgs_theme_path().'/php/templates/widget/')): ?>
        <?php while (false !== ($file = readdir($handle))): ?>
          <?php if ($file != "." && $file != ".."): ?>
            <option value="<?php echo $file ?>" <?php selected($instance['the_template'], $file) ?>><?php echo $file ?></option>
          <?php endif; ?>
        <?php endwhile; ?>
      <?php closedir($handle); endif; ?>
    </select>
    <?php
  }
  
  function filter_where($where = '') {
    if($this->start_days_ago > 0) {
      $where .= " AND post_date >= '" . date('Y-m-d', strtotime('-'.$this->start_days_ago.' days')) . "'";
    }
    if($this->end_days_ago > 0) {
      $where .= " AND post_date <= '" . date('Y-m-d', strtotime('-'.$this->end_days_ago.' days')) . "'";
    }
    $this->end_days_ago = 0;
    $this->start_days_ago = 0;
    
    if(isset($this->search_param_name) && isset($_GET[$this->search_param_name])) {
      $where .= " AND (post_title LIKE '%".$_GET[$this->search_param_name]."%' OR  post_content LIKE '%".$_GET[$this->search_param_name]."%')";
    }
    unset($this->search_param_name);
    
    
    return $where;
  }
  
  function __construct() {
  	$widget_ops = array( 'classname' => 'slider-widget', 'description' => 'A widget that lets you build your own query and display items based on that.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'slider-widget' );
  	parent::__construct( 'slider-widget', 'Slider Widget', $widget_ops, $control_ops );
  }
  
  function parent_update( $new_instance, $old_instance ) {
  	$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['more_text'] = $new_instance['more_text'];
		$instance['more_url'] = strip_tags($new_instance['more_url']);
		$instance['post_status'] = strip_tags($new_instance['post_status']);
		$instance['post_type'] = strip_tags($new_instance['post_type']);
		$instance['orderby'] = strip_tags($new_instance['orderby']);
		$instance['order'] = strip_tags($new_instance['order']);
		$instance['posts_per_page'] = strip_tags($new_instance['posts_per_page']);
		$instance['the_template'] = strip_tags($new_instance['the_template']);
		$instance['extra_parameters'] = strip_tags($new_instance['extra_parameters']);
		$instance['template_parameters'] = strip_tags($new_instance['template_parameters']);
		$instance['image_size'] = strip_tags($new_instance['image_size']);
		$instance['search_param_name'] = strip_tags($new_instance['search_param_name']);
		$instance['category'] = intval($new_instance['category']);
		$instance['start_days_ago'] = intval($new_instance['start_days_ago']);
		$instance['end_days_ago'] = intval($new_instance['end_days_ago']);
		$instance['show_full_content'] = ($new_instance['show_full_content'] ? 1 : 0);
		$instance['inherit_page_number'] = ($new_instance['inherit_page_number'] ? 1 : 0);
		$instance['include_pagination'] = ($new_instance['include_pagination'] ? 1 : 0);

		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);

		return $instance;
  }
	
	function update( $new_instance, $old_instance ) {
		$instance = $this->parent_update( $new_instance, $old_instance );
		
		
		$instance['width'] = intval($new_instance['width']);
		$instance['height'] = intval($new_instance['height']);
		$instance['delay'] = intval($new_instance['delay']);
		$instance['animation_speed'] = intval($new_instance['animation_speed']);
		$instance['include_navigation'] = $new_instance['include_navigation'] ? true : false;
		$instance['include_arrows'] = $new_instance['include_arrows'] ? true : false;
		$instance['transition_type'] = $new_instance['transition_type'];
		
		$instance['the_template'] = 'slider.php';
		if(isset($instance['template_parameters'])) {
      if(!strpos($instance['template_parameters'],'tag=ul')) {
        $instance['template_parameters'] .= "&tag=ul";
      }
    } else {
      $instance['template_parameters'] = 'tag=ul';
    }
		
		return $instance;
	}
	
	function print_scripts($id,$instance,$scripts=null) {
    $instance['scripts'] .= '
    $(function(){
      $("#widget-id .query-results").anythingSlider({
        buildArrows: '.(isset($instance['include_arrows']) && $instance['include_arrows'] != '' ? $instance['include_arrows'] : "0").',
        buildNavigation: '.(isset($instance['include_navigation']) && $instance['include_navigation'] != '' ? $instance['include_navigation'] : "0").',
        height: "'.$instance['height'].'px",
        width: "'.$instance['width'].'px",
        transitionType: "'.$instance['transition_type'].'",
        animationTime: '.$instance['animation_speed'].',
        delay: '.$instance['delay'].'
      });
    });';
    parent::print_scripts($id,$instance);
  }
  
  function parent_render( $args, $instance ) {
    global $more, $wp_query, $query_string, $paged;
    
		extract( $args );
				
		$title = apply_filters('widget_title', $instance['title'] );
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		
		if(isset($instance['template_parameters']) && $instance['template_parameters']) {
		  parse_str($instance['template_parameters'],$template_parameters);
		} else {
		  $template_parameters = array();
		}
		if(isset($instance['extra_parameters']) && $instance['extra_parameters']) {
		  parse_str($instance['extra_parameters'],$extra_parameters);
		} else {
		  $extra_parameters = array();
		}
		
		$tag = preg_match('/article/',$instance['the_template']) > 1 ? 'section' : 'ul';
		$tag = isset($template_parameters['tag']) ? $template_parameters['tag'] : $tag;
		
		$query_params = array_merge(
		  array('posts_per_page' => 5, 'nopaging' => 0, 'ignore_sticky_posts' => 1, 'orderby' => 'modified', 'order' => 'DESC'),
		  array(
		    'posts_per_page' => $instance['posts_per_page'],
		    'post_status' => $instance['post_status'],
		    'post_type' => $instance['post_type'],
		    'cat' => $instance['category'],
		    'orderby' => $instance['orderby'],
		    'order' => $instance['order'],
		    'paged' => ($instance['inherit_page_number'] ? $wp_query->query_vars['page'] : 1)
		  ),
		  $extra_parameters
		);
		
		if($instance['inherit_page_number']) {
		  $paged = $wp_query->query_vars['page'];
		}
		
		$this->start_days_ago = intval($instance['start_days_ago']);
		$this->end_days_ago = intval($instance['end_days_ago']);
		$this->search_param_name = $instance['search_param_name'];
		
		add_filter('posts_where', array(&$this,'filter_where'));
		
		$old_wp_query = $wp_query;
		$wp_query = new WP_Query($query_params);
		$count = 0;
		?>
		<?php echo $before_widget; ?>
		  <?php if ($wp_query->have_posts()): ?>
		    <?php if ( $title ) echo $before_title . do_shortcode($title) . $after_title; ?>
  		  <?php echo isset($instance['wrapper_open']) ? $instance['wrapper_open'] : '' ?>
  		  <div class="main-description">
  		    <?php echo $instance['filter'] ? do_shortcode(wpautop($text)) : do_shortcode($text); ?>
  		  </div>
		    <<?php echo $tag ?> class="query-results">
  		    <?php while ($wp_query->have_posts()): $wp_query->the_post(); $more = ($instance['show_full_content'] ? 1 : 0); $count++; ?>
  		      <?php include(hbgs_theme_path().'php/templates/widget/'.$instance['the_template']) ?>
  		    <?php endwhile; ?>
  		  </<?php echo $tag ?>>
  		  <?php echo isset($instance['wrapper_close']) ? $instance['wrapper_close'] : '' ?>
  		  <?php if ( $wp_query->max_num_pages > 1 && isset($instance['include_pagination']) && $instance['include_pagination'] ): ?>
        	<nav id="nav-below" class="next-previous clearfix">
        		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
        		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
        	</nav>
        <?php endif; ?>
		  <?php endif; $wp_query = $old_wp_query; wp_reset_postdata(); ?>
		  <?php if($instance['more_text'] && $instance['more_url']): ?>
		    <hgroup class="more clearfix">
		      <h4><a href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_text'] ?></a></h4>
		    </hgroup>
	    <?php endif; ?>
    <?php echo $after_widget ?>
    <?php
  }
	
	function render( $args, $instance ) {
	  $this->parent_render($args, $instance);
  }
  
  function print_default_styles() {
    wp_enqueue_style('slider',get_bloginfo('template_url').'/css/anythingSlider.css');
  }
  
  function print_default_scripts() { 
    wp_enqueue_script( 'slider', get_bloginfo('template_url').'/js/libs/jquery.anythingslider.js', array('jquery'));
  }
  
  function exclude_fields() {
	  return array(
		  'show_full_content',
		  'include_pagination',
		  'inherit_page_number',
	    'the_template'
	  );
	}
	
	function parent_form( $instance ) { 
	  global $wp_post_statuses;
	  
	  $exclude_fields = $this->exclude_fields();
	  
		$defaults = array( 
		  'title' => '',
		  'post_status' => 'publish',
		  'post_type' => 'any',
		  'posts_per_page' => 1,
		  'show_full_content' => false,
		  'more_text' => 'Read More &#187;',
		  'extra_parameters' => '',
		  'template_parameters' => '',
		  'include_pagination' => false,
		  'inherit_page_number' => false,
		  'category' => '',
		  'start_days_ago' => '',
		  'end_days_ago' => '',
		  'image_size' => 'None',
		  'orderby' => 'date',
		  'search_param_name' => '',
	    'more_url' => '',
	    'the_template' => 'article.php',
	    'filter' => false,
	    'text' => false,
		  'order' => 'DESC'
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$post_types = array_merge(array("any"),get_post_types());
		$categories = get_categories(array('hide_empty' => false));
		$text = format_to_edit($instance['text']);
		?>
		<p>
		  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('post_status'); ?>"><?php _e('Post Status:'); ?></label>
		  <select id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>">
		    <option value=""></option>
		    <?php foreach($wp_post_statuses as $status): ?>
		      <option value="<?php echo $status->name ?>" <?php selected($instance['post_status'], $status->name) ?>><?php echo $status->name ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?></label>
		  <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
		    <?php foreach($post_types as $type): ?>
		      <option value="<?php echo $type ?>" <?php selected($instance['post_type'], $type) ?>><?php echo $type ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:'); ?></label>
		  <select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
		    <option value=""></option>
		    <?php foreach($categories as $category): ?>
		      <option value="<?php echo $category->cat_ID ?>" <?php selected($instance['category'], $category->cat_ID) ?>><?php echo $category->slug ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?php _e('Posts Per Page:'); ?></label>
		  <select id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>">
		    <option value="-1">All</option>
		    <?php for($i=1; $i<=25; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['posts_per_page'], $i) ?>><?php echo $i ?></option>
		    <?php endfor; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('start_days_ago'); ?>"><?php _e('Start Search:'); ?></label>
		  <select id="<?php echo $this->get_field_id('start_days_ago'); ?>" name="<?php echo $this->get_field_name('start_days_ago'); ?>">
		    <option value="-1">At the Beginning of Time</option>
		    <?php for($i=1; $i<=90; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['start_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
		    <?php endfor; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('end_days_ago'); ?>"><?php _e('End Search:'); ?></label>
		  <select id="<?php echo $this->get_field_id('end_days_ago'); ?>" name="<?php echo $this->get_field_name('end_days_ago'); ?>">
		    <option value="-1">Today</option>
		    <?php for($i=1; $i<=90; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['end_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
		    <?php endfor; ?>
		  </select>
  	</p>
  	<p class="search_param_name" id="<?php echo $this->get_field_id('search_param_name'); ?>-wrapper">
		  <label for="<?php echo $this->get_field_id('search_param_name'); ?>"><?php _e('Name of Search Parameter:'); ?></label>
		  <input value="<?php echo $instance['search_param_name'] ?>" type="text" id="<?php echo $this->get_field_id('search_param_name'); ?>" name="<?php echo $this->get_field_name('search_param_name'); ?>" />
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By:'); ?></label>
		  <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
		    <?php foreach(array("date","modified","author","title","menu_order","parent","ID","rand","none","comment_count") as $sort): ?>
		      <option value="<?php echo $sort ?>" <?php selected($instance['orderby'], $sort) ?>><?php echo $sort ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
  	<p>
      <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label>
      <select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
        <?php foreach(array("DESC","ASC") as $sort): ?>
          <option value="<?php echo $sort ?>" <?php selected($instance['order'], $sort) ?>><?php echo $sort ?></option>
        <?php endforeach; ?>
      </select>
    </p>
	  <p>
		  <label for="<?php echo $this->get_field_id('extra_parameters'); ?>"><?php _e('Extra Query Parameters:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('extra_parameters'); ?>" name="<?php echo $this->get_field_name('extra_parameters'); ?>" type="text" value="<?php echo $instance['extra_parameters']; ?>" />
  	</p>
  	<?php if(!in_array('the_template',$exclude_fields)): ?>
  	  <p class="the_template" id="<?php echo $this->get_field_id('the_template'); ?>-wrapper">
  		  <label for="<?php echo $this->get_field_id('the_template'); ?>"><?php _e('Template:'); ?></label>
  		  <?php $this->template_select($instance) ?>
    	</p>
    <?php endif; ?>
	  <p>
		  <label for="<?php echo $this->get_field_id('template_parameters'); ?>"><?php _e('Template Parameters:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('template_parameters'); ?>" name="<?php echo $this->get_field_name('template_parameters'); ?>" type="text" value="<?php echo $instance['template_parameters']; ?>" />
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size:'); ?></label>
		  <select id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
		    <option value="0">None</option>
		    <?php $image_sizes = hbgs_image_sizes(); foreach($image_sizes as $image_size): ?>
		      <option value="<?php echo $image_size ?>" <?php selected($instance['image_size'],$image_size) ?>><?php echo $image_size ?></option>
		    <?php endforeach; ?>
		  </select>
		  <a href="/wp-admin/admin.php?page=hbgs-settings" target="_blank">&#187; Add an Image Size</a>
  	</p>
	  <p class="show_full_content" id="<?php echo $this->get_field_id('show_full_content'); ?>-wrapper">
		  <label for="<?php echo $this->get_field_id('show_full_content'); ?>"><?php _e('Show Full Content?'); ?></label>
  		<input type="checkbox" <?php checked($instance['show_full_content']) ?> id="<?php echo $this->get_field_id('show_full_content'); ?>" name="<?php echo $this->get_field_name('show_full_content'); ?>" type="text" />
  	</p>
  	<?php if(!in_array('include_pagination',$exclude_fields)): ?>
  	  <p class="include_pagination" id="<?php echo $this->get_field_id('include_pagination'); ?>-wrapper">
  		  <label for="<?php echo $this->get_field_id('include_pagination'); ?>"><?php _e('Include Pagination?'); ?></label>
    		<input type="checkbox" <?php checked($instance['include_pagination']) ?> id="<?php echo $this->get_field_id('include_pagination'); ?>" name="<?php echo $this->get_field_name('include_pagination'); ?>" type="text" />
    	</p>
    <?php endif; ?>
    <?php if(!in_array('inherit_page_number',$exclude_fields)): ?>
  	  <p class="inherit_page_number" id="<?php echo $this->get_field_id('inherit_page_number'); ?>-wrapper">
  		  <label for="<?php echo $this->get_field_id('inherit_page_number'); ?>"><?php _e('Inherit Page Number?'); ?></label>
    		<input type="checkbox" <?php checked($instance['inherit_page_number']) ?> id="<?php echo $this->get_field_id('inherit_page_number'); ?>" name="<?php echo $this->get_field_name('inherit_page_number'); ?>" type="text" />
    	</p>
    <?php endif; ?>
	  <p class="more_text" id="<?php echo $this->get_field_id('more_text'); ?>-wrapper">
		  <label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('More Text:'); ?></label>
  		<input type="text" value="<?php echo esc_attr($instance['more_text']) ?>" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" />
  	</p>
	  <p class="more_url" id="<?php echo $this->get_field_id('more_url'); ?>-wrapper">
		  <label for="<?php echo $this->get_field_id('more_url'); ?>"><?php _e('More URL:'); ?></label>
  		<input type="text" value="<?php echo esc_attr($instance['more_url']) ?>" id="<?php echo $this->get_field_id('more_url'); ?>" name="<?php echo $this->get_field_name('more_url'); ?>" type="text" />
  	</p>
  	<p>
  	  <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Description:'); ?></label>
  	  <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
  		<br /><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label>
  	</p>
		<?php
  }
	
	function form( $instance ) { 
	  $defaults = array(
	   'height' => 270,
	   'width' => 964,
	   'include_arrows' => true,
	   'include_navigation' => true,
	   'delay' => 4000,
	   'animation_speed' => 1000,
	   'transition_type' => 'slider'
	  );
		
		$instance = wp_parse_args( (array) $instance, $defaults );
	  ?>
		<?php $this->parent_form($instance) ?>
		<p>
		  <label for="<?php echo $this->get_field_id('transition_type'); ?>"><?php _e('Transition Type:'); ?></label>
  		<select id="<?php echo $this->get_field_id('transition_type'); ?>" name="<?php echo $this->get_field_name('transition_type'); ?>">
		    <?php foreach(array("slider","fader") as $transition_type): ?>
		      <option value="<?php echo $transition_type ?>" <?php selected($instance['transition_type'], $transition_type) ?>><?php echo $transition_type ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('animation_speed'); ?>"><?php _e('Animation Speed (in milliseconds):'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('animation_speed'); ?>" name="<?php echo $this->get_field_name('animation_speed'); ?>" type="text" value="<?php echo esc_attr($instance['animation_speed']); ?>" />
  	</p>
	  <p>
      <label for="<?php echo $this->get_field_id('delay'); ?>"><?php _e('Delay Speed (in milliseconds):'); ?></label>
    	<input class="widefat" id="<?php echo $this->get_field_id('delay'); ?>" name="<?php echo $this->get_field_name('delay'); ?>" type="text" value="<?php echo esc_attr($instance['delay']); ?>" />
    </p>
	  <p>
		  <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height (in pixels):'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($instance['height']); ?>" />
  	</p>
	  <p>
      <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width (in pixels):'); ?></label>
    	<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($instance['width']); ?>" />
    </p>
    <p id="<?php echo $this->get_field_id('include_arrows'); ?>-wrapper">
		  <label for="<?php echo $this->get_field_id('include_arrows'); ?>"><?php _e('Includ Arrow Buttons?'); ?></label>
  		<input type="checkbox" <?php checked($instance['include_arrows']) ?> id="<?php echo $this->get_field_id('include_arrows'); ?>" name="<?php echo $this->get_field_name('include_arrows'); ?>" type="text" />
  	</p>
	  <p id="<?php echo $this->get_field_id('include_navigation'); ?>-wrapper">
      <label for="<?php echo $this->get_field_id('include_navigation'); ?>"><?php _e('Includ Page Navigation?'); ?></label>
    	<input type="checkbox" <?php checked($instance['include_navigation']) ?> id="<?php echo $this->get_field_id('include_navigation'); ?>" name="<?php echo $this->get_field_name('include_navigation'); ?>" type="text" />
    </p>
  <?php }
}