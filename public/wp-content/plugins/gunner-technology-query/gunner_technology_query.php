<?php
/*
Plugin Name: Gunner Technology Query
Plugin URI: http://gunnertech.com/gunner-technology-query
Description: A plugin that allows authors to add content via custom queries
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


class GtQuery {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function activate() {
    global $wpdb;
  
    update_option(self::$PREFIX."_db_version", self::$VERSION);
  }
  
  public static function getConst($const) {
    return self::$$const;
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( self::$PREFIX."_db_version" );
    
    if( $installed_ver != self::$VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function() {
      return register_widget('GtQuery_Widget');
    });
    
    add_action('init',function() {
      wp_enqueue_style( GtQuery::getConst('PREFIX'), GtQuery::getConst('URL').'css/style.css');
      // wp_enqueue_script( "swfobject", GtQuery::getConst('URL').'js/lib/swfobject.js');
      wp_enqueue_script( GtQuery::getConst('PREFIX'), GtQuery::getConst('URL').'js/script.js');
    });
  }
  
  public static function categories_string($id) {
    $cats = (array) get_the_category($id);


    foreach($cats as $cat) {
      $classes[] = $cat->slug;
    }
    
    return join(" ", $classes);
  }
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_query";
    self::$URL = plugin_dir_url( __FILE__ );
    self::$PATH = plugin_dir_path( __FILE__ );
    
    self::update_db_check();
    self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }  
}

class GtQuery_Widget extends WP_Widget {
  public $start_days_ago = 0;
  public $end_days_ago = 0;
  public $search_param_name = null;
  
  private $options = array( 
    "title" => "",    
    "category" => "",
    "end_days_ago" => "",
    "start_days_ago" => "",
    "search_param_name" => "",
    "post_type" => "all",
    "post_format" => "all",
    "post_status" => "publish",
    "template_parameters" => "",
    "extra_parameters" => "",
    'posts_per_page' => 5, 
    'nopaging' => 0, 
    'ignore_sticky_posts' => 1, 
    'orderby' => 'modified', 
    'order' => 'DESC',
    'template' => 'standard.php',
    'inherit_page_number' => false,
    
    'show_full_content' => false,
    'display_type' => 'list',
    'headline_placement' => 'high',
    'image_size' => 'full',
    'more_url' => '',
    'more_slug' => '&#187; Read More',
    'feature_image_only' => 0
  );
    
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-query', 'description' => 'A widget that displays custom queries.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-query' );
    
    parent::__construct( 
      'gunner_technology_query', 
      'Query Widget', 
      array( 'description' => 'A Query Widget', 'classname' => 'gunner-technology-query' ),
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
    
    $post_types = $instance['post_type'] != 'all' ? $instance['post_type'] : 'any';
    
    $query_params = array_merge(
      array('posts_per_page' => 5, 'nopaging' => 0, 'ignore_sticky_posts' => 1, 'orderby' => 'modified', 'order' => 'DESC'),
      array(
        'posts_per_page' => $instance['posts_per_page'],
        'post_status' => $instance['post_status'],
        'post_type' => $post_types,
        'cat' => $instance['category'],
        'orderby' => $instance['orderby'],
        'order' => $instance['order'],
        'paged' => ($instance['inherit_page_number'] ? $wp_query->query_vars['paged'] : 1),
        'feature_image_only' => 0
      ),
      $extra_parameters
    );
    
    if(intval($instance['feature_image_only']) != 0) {
      $query_params['meta_key'] = '_thumbnail_id';
    }
    
    if($instance['post_format'] != 'all') {
      $query_params['tax_query'] = isset($query_params['tax_query']) && is_array($query_params['tax_query']) ? $query_params['tax_query'] : array();
      $query_params['tax_query'] = array_merge($query_params['tax_query'], array(
        'taxonomy' => 'post_format',
        'field' => 'slug',
        'terms' => 'post-format-'.$instance['post_format']
      ));
    }
    
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
    
    $instance = array_merge(
      $instance,
      $template_parameters
    );
    
    ?>
    <?php echo $before_widget ?>
      <?php include GtQuery::getConst('PATH').'/templates/'.$instance['template']; ?> 
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
    $post_types = get_post_types();
    $post_formats = array('all','aside','gallery','link','image','quote','status','video','audio','chat');
    $instance = wp_parse_args( (array) $instance, $this->options);
    $categories = get_categories(array('hide_empty' => false));
  ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <h4>Query Options</h4>
        
    <p>
      <label for="<?php echo $this->get_field_id('post_status'); ?>"><?php _e('Post Status:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>">
        <option value="" <?php selected($instance['post_status'], 'any') ?>>any</option>
        <?php foreach($wp_post_statuses as $status): ?>
          <option value="<?php echo $status->name ?>" <?php selected($instance['post_status'], $status->name) ?>><?php echo $status->name ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
        <?php foreach(array_merge(array('all'),$post_types) as $type): ?>
          <option value="<?php echo $type ?>" <?php selected($instance['post_type'], $type) ?>><?php echo $type ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('post_format'); ?>"><?php _e('Post Format:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('post_format'); ?>" name="<?php echo $this->get_field_name('post_format'); ?>">
        <?php foreach($post_formats as $format): ?>
          <option value="<?php echo $format ?>" <?php selected($instance['post_format'], $format) ?>><?php echo $format ?></option>
        <?php endforeach; ?>
      </select>
    </p>
      
    <p>
      <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
        <option value=""></option>
        <?php foreach($categories as $category): ?>
          <option value="<?php echo $category->cat_ID ?>" <?php selected($instance['category'], $category->cat_ID) ?>><?php echo $category->slug ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <input class="checkbox" type="checkbox" value="1" <?php checked($instance['feature_image_only'], 1) ?> id="<?php echo $this->get_field_id('feature_image_only'); ?>" name="<?php echo $this->get_field_name('feature_image_only'); ?>" />
      <label for="<?php echo $this->get_field_id('feature_image_only'); ?>"><?php _e('Limit to Content with featured images?'); ?></label>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?php _e('Posts Per Page:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>">
        <option value="-1">All</option>
        <?php for($i=1; $i<=25; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['posts_per_page'], $i) ?>><?php echo $i ?></option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('start_days_ago'); ?>"><?php _e('Start Search:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('start_days_ago'); ?>" name="<?php echo $this->get_field_name('start_days_ago'); ?>">
        <option value="-1">At the Beginning of Time</option>
        <?php for($i=1; $i<=90; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['start_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('end_days_ago'); ?>"><?php _e('End Search:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('end_days_ago'); ?>" name="<?php echo $this->get_field_name('end_days_ago'); ?>">
        <option value="-1">Today</option>
        <?php for($i=1; $i<=90; $i++): ?>
          <option value="<?php echo $i ?>" <?php selected($instance['end_days_ago'], $i) ?>><?php echo $i ?> Days Ago</option>
        <?php endfor; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('search_param_name'); ?>"><?php _e('Name of Search Parameter:'); ?></label><br />
      <input class="widefat" value="<?php echo $instance['search_param_name'] ?>" type="text" id="<?php echo $this->get_field_id('search_param_name'); ?>" name="<?php echo $this->get_field_name('search_param_name'); ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
        <?php foreach(array("date","modified","author","title","menu_order","parent","ID","rand","none","comment_count") as $sort): ?>
          <option value="<?php echo $sort ?>" <?php selected($instance['orderby'], $sort) ?>><?php echo $sort ?></option>
        <?php endforeach; ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
        <?php foreach(array("DESC","ASC") as $sort): ?>
          <option value="<?php echo $sort ?>" <?php selected($instance['order'], $sort) ?>><?php echo $sort ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('extra_parameters'); ?>"><?php _e('Extra Query Parameters:'); ?></label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('extra_parameters'); ?>" name="<?php echo $this->get_field_name('extra_parameters'); ?>" type="text" value="<?php echo $instance['extra_parameters']; ?>" />
    </p>
    
    <h4>Display Options</h4>
    <?php if($instance['template'] == 'standard.php'): ?>
      <p>
        <label for="<?php echo $this->get_field_id( 'display_type' ); ?>">Display Type:</label><br />

        <select class="widefat" id="<?php echo $this->get_field_id( 'display_type' ); ?>" name="<?php echo $this->get_field_name( 'display_type' ); ?>">
          <?php foreach(array("list","articles") as $option): ?>
            <option value="<?php echo $option ?>" <?php selected($option,$instance['display_type']) ?>><?php echo $option ?></option>
          <?php endforeach; ?>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id( 'headline_placement' ); ?>">Headline Placement:</label><br />

        <select class="widefat" id="<?php echo $this->get_field_id( 'headline_placement' ); ?>" name="<?php echo $this->get_field_name( 'headline_placement' ); ?>">
          <?php foreach(array("high","low") as $option): ?>
            <option value="<?php echo $option ?>" <?php selected($option,$instance['headline_placement']) ?>><?php echo $option ?></option>
          <?php endforeach; ?>
        </select>
      </p>
    <?php endif; ?>
    
    <p>
      <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>">
        <?php if ($handle = opendir(GtQuery::getConst('PATH').'/templates')) { while (false !== ($entry = readdir($handle))) { if ($entry != "." && $entry != "..") { ?>
          <option value="<?php echo $entry ?>" <?php selected($instance['template'], $entry) ?>><?php echo str_replace('.php', '', $entry) ?></option>
        <?php }}} ?>
      </select>
    </p>
      
    <p>
      <label for="<?php echo $this->get_field_id('template_parameters'); ?>"><?php _e('Template Parameters:'); ?></label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('template_parameters'); ?>" name="<?php echo $this->get_field_name('template_parameters'); ?>" type="text" value="<?php echo $instance['template_parameters']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
        <option value="0">None</option>
        <?php $image_sizes = array_merge(get_intermediate_image_sizes(),array('full')); foreach($image_sizes as $image_size): ?>
          <option value="<?php echo $image_size ?>" <?php selected($instance['image_size'],$image_size) ?>><?php echo $image_size ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('more_url'); ?>"><?php _e('More URL:'); ?></label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('more_url'); ?>" name="<?php echo $this->get_field_name('more_url'); ?>" type="text" value="<?php echo $instance['more_url']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('more_slug'); ?>"><?php _e('More Slug:'); ?></label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('more_slug'); ?>" name="<?php echo $this->get_field_name('more_slug'); ?>" type="text" value="<?php echo $instance['more_slug']; ?>" />
    </p>
    
    <?php
  }

}

register_activation_hook( __FILE__, array('GtQuery', 'activate') );
register_activation_hook( __FILE__, array('GtQuery', 'deactivate') );
register_activation_hook( __FILE__, array('GtQuery', 'uninstall') );

add_action('plugins_loaded', array('GtQuery', 'setup') );