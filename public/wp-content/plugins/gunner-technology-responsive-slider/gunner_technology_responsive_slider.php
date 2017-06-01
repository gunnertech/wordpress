<?php
/*
Plugin Name: Gunner Technology Responsive Slider
Plugin URI: http://gunnertech.com/gunner-technology-responsive-slider
Description: A plugin that allows authors to add their Responsive Slider photos via widgets
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_RESPONSIVE_SLIDER_VERSION', '0.0.1');
define('GT_RESPONSIVE_SLIDER_URL', plugin_dir_url( __FILE__ ));

class GtResponsiveSlider {
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_responsive_slider_db_version", GT_RESPONSIVE_SLIDER_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_responsive_slider_db_version" );
    
    if( $installed_ver != GT_RESPONSIVE_SLIDER_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function() {
      return register_widget('GtResponsiveSlider_Widget');
    });
    
    add_action('init',function() {
      wp_enqueue_style(   'flexslider', GT_RESPONSIVE_SLIDER_URL.'css/flexslider.css');

      wp_enqueue_script( 'flexslider', GT_RESPONSIVE_SLIDER_URL.'js/lib/jquery.flexslider-min.js', array('jquery'));
      wp_enqueue_script( 'responsive-slider-script', GT_RESPONSIVE_SLIDER_URL.'js/script.js', array('flexslider'));
    });
  }
  
  public static function setup() {
    self::update_db_check();
    $gunner_technology_responsive_slider = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

class GtResponsiveSlider_Widget extends WP_Widget {
  public $start_days_ago = 0;
  public $end_days_ago = 0;
  public $search_param_name = null;
  
  private $options = array( 
    "title" => "",
    "animation" => "fade",              //String: Select your animation type, "fade" or "slide"
    "slideDirection" => "horizontal",   //String: Select the sliding direction, "horizontal" or "vertical"
    "prevText" => "Previous",           //String: Set the text for the "previous" directionNav item
    "nextText" => "Next",               //String: Set the text for the "next" directionNav item
    "pauseText" => 'Pause',             //String: Set the text for the "pause" pausePlay item
    "playText" => 'Play',               //String: Set the text for the "play" pausePlay item
    
    "slideshowSpeed" => 7000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
    "animationDuration" => 600,         //Integer: Set the speed of animations, in milliseconds
    "slideToStart" => 0,                //Integer: The slide that the slider should start on. Array notation (0 = first slide)
    
    "slideshow" => true,                //Boolean: Animate slider automatically
    "directionNav" => true,             //Boolean: Create navigation for previous/next navigation? (true/false)
    "controlNav" => true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
    "keyboardNav" => true,              //Boolean: Allow slider navigating via keyboard left/right keys
    "mousewheel" => false,              //Boolean: Allow slider navigating via mousewheel
    "pausePlay" => false,               //Boolean: Create pause/play dynamic element
    "randomize" => false,               //Boolean: Randomize slide order
    "animationLoop" => true,            //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
    "pauseOnAction" => true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
    "pauseOnHover" => false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
    
    "controlsContainer" => "",          //Selector: Declare which container the navigation elements should be appended too. Default container is the flexSlider element. Example use would be ".flexslider-container", "#container", etc. If the given element is not found, the default action will be taken.
    "manualControls" => "",             //Selector: Declare custom control navigation. Example would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
    
    "category" => "",
    "end_days_ago" => "",
    "start_days_ago" => "",
    "search_param_name" => "",
    "post_type" => "all",
    "post_status" => "any",
    "template_parameters" => "",
    "extra_parameters" => "",
    'posts_per_page' => 5, 
    'nopaging' => 0, 
    'ignore_sticky_posts' => 1, 
    'orderby' => 'modified', 
    'order' => 'DESC',
    'inherit_page_number' => false,
    
    'show_full_content' => false,
    'image_size' => 'full'
  );
    
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-responsive-slider', 'description' => 'A widget that displays Responsive Slider photos.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-responsive-slider' );
    
    parent::__construct( 
      'gunner_technology_responsive_slider', 
      'Responsive Slider Widget', 
      array( 'description' => 'A Responsive Slider Like Box', 'classname' => 'gunner-technology-responsive-slider' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    global $wp_query;
    extract( $args );
    
    if(isset($instance['template_parameters']) && $instance['template_parameters']) {
      parse_str($instance['template_parameters'],$template_parameters);
    } else {
      $template_parameters = array();
    }
    
    if(isset($instance['extra_parameters'])) {
      parse_str($instance['extra_parameters'],$extra_parameters);
    } else {
      $extra_parameters = array();
    }
    
    $query_params = array_merge(
      array('posts_per_page' => 5, 'nopaging' => 0, 'ignore_sticky_posts' => 1, 'orderby' => 'modified', 'order' => 'DESC', 'meta_key' => '_thumbnail_id'),
      array(
        'posts_per_page' => $instance['posts_per_page'],
        'post_status' => $instance['post_status'],
        'post_type' => $instance['post_type'],
        'cat' => $instance['category'],
        'orderby' => $instance['orderby'],
        'order' => $instance['order'],
        'paged' => ($instance['inherit_page_number'] ? $wp_query->query_vars['paged'] : 1)
      ),
      $extra_parameters
    );
    
    if($instance['inherit_page_number']) {
      $paged = $wp_query->query_vars['paged'];
    }
    
    $this->start_days_ago = intval($instance['start_days_ago']);
    $this->end_days_ago = intval($instance['end_days_ago']);
    $this->search_param_name = $instance['search_param_name'];
    
    $_this = $this;
    
    add_filter('posts_where', function($where = '') use ($_this) {
      global $wp_query;
      
      if($_this->start_days_ago > 0) {
        $where .= " AND post_date >= '" . date('Y-m-d', strtotime('-'.$_this->start_days_ago.' days')) . "'";
      }
      if($_this->end_days_ago > 0) {
        $where .= " AND post_date <= '" . date('Y-m-d', strtotime('-'.$_this->end_days_ago.' days')) . "'";
      }
      $_this->end_days_ago = 0;
      $_this->start_days_ago = 0;

      if(isset($_this->search_param_name) && isset($_GET[$_this->search_param_name])) {
        $where .= " AND (post_title LIKE '%".$_GET[$_this->search_param_name]."%' OR  post_content LIKE '%".$_GET[$_this->search_param_name]."%')";
      }

      unset($_this->search_param_name);


      return $where;
    });
    
    $old_wp_query = $wp_query;
    $query = new WP_Query($query_params);
    $count = 0;
    
    ?>
    <?php echo $before_widget ?>
      <?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
      <?php if ($query->have_posts()): ?>
        <div class="flexslider"
          <?php foreach($this->options as $key => $value): ?>
            data-<?php echo $key ?>="<?php echo esc_attr($instance[$key]) ?>"
          <?php endforeach; ?>
        >
          <ul class="slides">
            <?php while ($query->have_posts()): $query->the_post(); $more = ($instance['show_full_content'] ? 1 : 0); $count++; ?>
              <li>
                <a title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>"> 
                  <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
                </a>
                <p class="flex-caption">
                  <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
                  <?php echo get_the_excerpt() ?>
                </p>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      <?php endif; ?>
    <?php echo $after_widget ?>
    <?php
    wp_reset_query();
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    foreach($this->options as $key => $value) {
      $instance[$key] = $new_instance[$key];
    }
    
    return $instance;
  }

  /** @see WP_Widget::form */
  function form( $instance ) {
    global $wp_post_statuses;
    $post_types = array_merge(array("any"),get_post_types());
    $instance = wp_parse_args( (array) $instance, $this->options);
    $categories = get_categories(array('hide_empty' => false));
  ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'animation' ); ?>">Animation:</label><br />
      
      <select id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>">
        <?php foreach(array("fade","slide") as $option): ?>
          <option value="<?php echo $option ?>" <?php selected($option,$instance['animation']) ?>><?php echo $option ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'slideDirection' ); ?>">Slide Direction:</label><br />
      
      <select id="<?php echo $this->get_field_id( 'slideDirection' ); ?>" name="<?php echo $this->get_field_name( 'slideDirection' ); ?>">
        <?php foreach(array("horizontal","vertical") as $option): ?>
          <option value="<?php echo  $option ?>" <?php selected($option,$instance['slideDirection']) ?>><?php echo  $option ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'prevText' ); ?>">Previous Text:</label><br />
      <input id="<?php echo $this->get_field_id( 'prevText' ); ?>" name="<?php echo $this->get_field_name( 'prevText' ); ?>" value="<?php echo $instance['prevText']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'nextText' ); ?>">Next Text:</label><br />
      <input id="<?php echo $this->get_field_id( 'nextText' ); ?>" name="<?php echo $this->get_field_name( 'nextText' ); ?>" value="<?php echo $instance['nextText']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'pauseText' ); ?>">Pause Text:</label><br />
      <input id="<?php echo $this->get_field_id( 'pauseText' ); ?>" name="<?php echo $this->get_field_name( 'pauseText' ); ?>" value="<?php echo $instance['pauseText']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'playText' ); ?>">Play Text:</label><br />
      <input id="<?php echo $this->get_field_id( 'playText' ); ?>" name="<?php echo $this->get_field_name( 'playText' ); ?>" value="<?php echo $instance['playText']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'slideshowSpeed' ); ?>">Slideshow Speed:</label><br />
      <input type="number" id="<?php echo $this->get_field_id( 'slideshowSpeed' ); ?>" name="<?php echo $this->get_field_name( 'slideshowSpeed' ); ?>" value="<?php echo $instance['slideshowSpeed']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'animationDuration' ); ?>">Animation Duration:</label><br />
      <input type="number" id="<?php echo $this->get_field_id( 'animationDuration' ); ?>" name="<?php echo $this->get_field_name( 'animationDuration' ); ?>" value="<?php echo $instance['animationDuration']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'slideToStart' ); ?>">Starting Slide:</label><br />
      <input type="number" id="<?php echo $this->get_field_id( 'slideToStart' ); ?>" name="<?php echo $this->get_field_name( 'slideToStart' ); ?>" value="<?php echo $instance['slideToStart']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'slideshow' ); ?>">Start Automatically?</label>
      <input type="checkbox" value="1" <?php checked($instance['slideshow']) ?> id="<?php echo $this->get_field_id('slideshow'); ?>" name="<?php echo $this->get_field_name('slideshow'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'directionNav' ); ?>">Direction Nav?</label>
      <input type="checkbox" value="1" <?php checked($instance['directionNav']) ?> id="<?php echo $this->get_field_id('directionNav'); ?>" name="<?php echo $this->get_field_name('directionNav'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'controlNav' ); ?>">Control Nav?</label>
      <input type="checkbox" value="1" <?php checked($instance['controlNav']) ?> id="<?php echo $this->get_field_id('controlNav'); ?>" name="<?php echo $this->get_field_name('controlNav'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'keyboardNav' ); ?>">Keyboard Nav?</label>
      <input type="checkbox" value="1" <?php checked($instance['keyboardNav']) ?> id="<?php echo $this->get_field_id('keyboardNav'); ?>" name="<?php echo $this->get_field_name('keyboardNav'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'mousewheel' ); ?>">Mousewheel Nav?</label>
      <input type="checkbox" value="1" <?php checked($instance['mousewheel']) ?> id="<?php echo $this->get_field_id('mousewheel'); ?>" name="<?php echo $this->get_field_name('mousewheel'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'pausePlay' ); ?>">Pause/play Dynamic Element?</label>
      <input type="checkbox" value="1" <?php checked($instance['pausePlay']) ?> id="<?php echo $this->get_field_id('pausePlay'); ?>" name="<?php echo $this->get_field_name('pausePlay'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'randomize' ); ?>">Randomize?</label>
      <input type="checkbox" value="1" <?php checked($instance['randomize']) ?> id="<?php echo $this->get_field_id('randomize'); ?>" name="<?php echo $this->get_field_name('randomize'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'animationLoop' ); ?>">Loop?</label>
      <input type="checkbox" value="1" <?php checked($instance['animationLoop']) ?> id="<?php echo $this->get_field_id('animationLoop'); ?>" name="<?php echo $this->get_field_name('animationLoop'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'pauseOnHover' ); ?>">Pause on Hover?</label>
      <input type="checkbox" value="1" <?php checked($instance['pauseOnHover']) ?> id="<?php echo $this->get_field_id('pauseOnHover'); ?>" name="<?php echo $this->get_field_name('pauseOnHover'); ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('post_status'); ?>"><?php _e('Post Status:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>">
        <option value=""></option>
        <?php foreach($wp_post_statuses as $status): ?>
          <option value="<?php echo $status->name ?>" <?php selected($instance['post_status'], $status->name) ?>><?php echo $status->name ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
        <?php foreach($post_types as $type): ?>
          <option value="<?php echo $type ?>" <?php selected($instance['post_type'], $type) ?>><?php echo $type ?></option>
        <?php endforeach; ?>
      </select>
    </p>
      
    <p>
      <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
        <option value=""></option>
        <?php foreach($categories as $category): ?>
          <option value="<?php echo $category->cat_ID ?>" <?php selected($instance['category'], $category->cat_ID) ?>><?php echo $category->slug ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?php _e('Posts Per Page:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>">
        <option value="-1">All</option>
        <?php for($i=1; $i<=25; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['posts_per_page'], $i) ?>><?php echo $i ?></option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('start_days_ago'); ?>"><?php _e('Start Search:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('start_days_ago'); ?>" name="<?php echo $this->get_field_name('start_days_ago'); ?>">
        <option value="-1">At the Beginning of Time</option>
        <?php for($i=1; $i<=90; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['start_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('end_days_ago'); ?>"><?php _e('End Search:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('end_days_ago'); ?>" name="<?php echo $this->get_field_name('end_days_ago'); ?>">
        <option value="-1">Today</option>
        <?php for($i=1; $i<=90; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['end_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('search_param_name'); ?>"><?php _e('Name of Search Parameter:'); ?></label><br />
      <input value="<?php echo $instance['search_param_name'] ?>" type="text" id="<?php echo $this->get_field_id('search_param_name'); ?>" name="<?php echo $this->get_field_name('search_param_name'); ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
        <?php foreach(array("date","modified","author","title","menu_order","parent","ID","rand","none","comment_count") as $sort): ?>
          <option value="<?php echo $sort ?>" <?php selected($instance['orderby'], $sort) ?>><?php echo $sort ?></option>
        <?php endforeach; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
        <?php foreach(array("DESC","ASC") as $sort): ?>
          <option value="<?php echo $sort ?>" <?php selected($instance['order'], $sort) ?>><?php echo $sort ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('extra_parameters'); ?>"><?php _e('Extra Query Parameters:'); ?></label><br />
      <input id="<?php echo $this->get_field_id('extra_parameters'); ?>" name="<?php echo $this->get_field_name('extra_parameters'); ?>" type="text" value="<?php echo $instance['extra_parameters']; ?>" />
    </p>
      
    <p>
      <label for="<?php echo $this->get_field_id('template_parameters'); ?>"><?php _e('Template Parameters:'); ?></label><br />
      <input id="<?php echo $this->get_field_id('template_parameters'); ?>" name="<?php echo $this->get_field_name('template_parameters'); ?>" type="text" value="<?php echo $instance['template_parameters']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size:'); ?></label><br />
      <select id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
        <option value="0">None</option>
        <?php $image_sizes = array('thumbnail', 'medium', 'large', 'full'); foreach($image_sizes as $image_size): ?>
          <option value="<?php echo $image_size ?>" <?php selected($instance['image_size'],$image_size) ?>><?php echo $image_size ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <?php
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtResponsiveSlider', 'activate') );
register_activation_hook( __FILE__, array('GtResponsiveSlider', 'deactivate') );
register_activation_hook( __FILE__, array('GtResponsiveSlider', 'uninstall') );

add_action('plugins_loaded', array('GtResponsiveSlider', 'setup') );