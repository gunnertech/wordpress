<?php
/*
Plugin Name: Gunner Technology Dynamic Sidebars
Plugin URI: http://gunnertech.com/gunner-technology-dynamic-sidebars
Description: A plugin that allows admins to add as many sidebars as necessary and control what content they appear on
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_DYNAMIC_SIDEBARS_VERSION', '0.0.1');
define('GT_DYNAMIC_SIDEBARS_URL', plugin_dir_url( __FILE__ ));
define('GT_DYNAMIC_SIDEBARS_PATH', plugin_dir_path( __FILE__ ));

if ( !function_exists( 'of_get_option' ) ) {
  function of_get_option($name, $default = false) {
    $optionsframework_settings = get_option('optionsframework');
    // Gets the unique option id
    $option_name = $optionsframework_settings['id'];
    if ( get_option($option_name) ) {
      $options = get_option($option_name);
    }
  
    if ( isset($options[$name]) ) {
      return $options[$name];
    } else {
      return $default;
    }
  }
}

class GtDynamicSidebars {
  private static $instance;
  private $sidebars;
  private $sidebarString;
  private $areas;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_dynamic_sidebars_db_version", GT_DYNAMIC_SIDEBARS_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_dynamic_sidebars_db_version" );
    
    if( $installed_ver != GT_DYNAMIC_SIDEBARS_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    $_this = $this;
    
    add_shortcode('sidebar', function($atts, $content=null, $code="") use ($_this) {
      extract(shortcode_atts(array(
        'slug' => false
      ), $atts));
      
      // $slug = $area.'_'.strtolower(preg_replace('/\W/',"_",$sidebar->name));
      $sidebar = $_this->find_sidebar($slug);
      $html = '';
      ob_start();
      
      if ( is_active_sidebar( $slug ) ) { 
        $_this->render_sidebar($sidebar,$slug,false);
      }
      $html = ob_get_contents();
      
      ob_end_clean();
      
      return $html;
    });
    
    $this->areas = array("pre_header","pre_content","post_content","content_left","content_right","content_above","header","footer","post_content_body","reserve");
    $_this = $this;
    
    if(is_admin()) {
      add_action('admin_menu', function() use ($_this) {
        if (current_user_can( "delete_published_posts" )) {
          add_menu_page('Sidebars', 'Sidebars', 'administrator', "gunner-technology-sidebars", array($_this,'admin_markup'),GT_DYNAMIC_SIDEBARS_URL.'images/menu-icon.png');
        }
      });
      
      add_action('admin_init', function() use ($_this) {
        wp_enqueue_style( 'gunner-technology-sidebars', GT_DYNAMIC_SIDEBARS_URL.'css/styles.css');
        wp_enqueue_script( 'json', GT_DYNAMIC_SIDEBARS_URL.'js/json.js', array('jquery'));
        wp_enqueue_script( 'gunner-technology-sidebars', GT_DYNAMIC_SIDEBARS_URL.'js/script.js', array('json'));
        
        // register_setting( 'hbgs-settings', 'default_sidebar_categories', function($categories_as_string) {
        //   if(!isset($categories_as_string)) {
        //     $categories_as_string = "";
        //   }
        //   $temp_categories = explode(",",$categories_as_string);
        //   $categories = array();
        // 
        //   foreach($temp_categories as $cat) {
        //     $categories[] = trim($cat);
        //   }
        // 
        //   return $categories;
        // });

        register_setting( 'gunner-technology-sidebars-group', 'hbgs_sidebars', function($sidebars) {
          $sidebars = is_object($sidebars) ? $sidebars : json_decode($sidebars);


          if(!$sidebars) {
            wp_die( "We're sorry. There was a problem with your change. Please go back and try again.", "Error", array(
             "back_link" => true
            ));
          }
          
          return $sidebars;
        } );
        
        // register_setting( 'gunner-technology-sidebar-categories', 'default_sidebar_categories', function($categories_as_string) {
        //   if(is_array($categories_as_string)) {
        //     return $categories_as_string;
        //   }
        //   
        //   
        //   $temp_categories = explode(",",$categories_as_string);
        //   $categories = array();
        // 
        //   foreach($temp_categories as $cat) {
        //     $categories[] = trim($cat);
        //   }
        // 
        //   return $categories;
        // });
      });
    }
    
    add_action( 'widgets_init', array( $this, 'widgets_init' ) );
    
    
    
    
    // add_action( 'init', function() {
    // 
    //   $valid_post_types = array_filter(get_post_types(),function($post_type) {
    //     return !in_array($post_type,array('mediapage','attachement','revision','nav_menu_item'));
    //   });
    // 
    //   $labels = array(
    //   'name' => _x( 'Sidebar Categories', 'taxonomy general name' ),
    //   'singular_name' => _x( 'Sidebar Category', 'taxonomy singular name' ),
    //   'search_items' =>  __( 'Search Sidebar Categories' ),
    //   'all_items' => __( 'All Sidebar Categories' ),
    //   'parent_item' => __( 'Parent Sidebar Category' ),
    //   'parent_item_colon' => __( 'Parent Sidebar Category:' ),
    //   'edit_item' => __( 'Edit Sidebar Category' ),
    //   'update_item' => __( 'Update Sidebar Category' ),
    //   'add_new_item' => __( 'Add New Sidebar Category' ),
    //   'new_item_name' => __( 'New Sidebar Category Name' ),
    //   'separate_items_with_commas' => __( 'Separate categories with commas' ),
    //       'add_or_remove_items' => __( 'Add or remove sidebar categories' ),
    //       'choose_from_most_used' => __( 'Choose from the most sidebar categories'),
    //       'menu_name' => false
    //   );
    // 
    //   register_taxonomy( 'sidebar_category', $valid_post_types, array(
    //   'hierarchical' => false,
    //   'labels' => $labels,
    //   'show_ui' => true,
    //   'show_in_nav_menus' => false,
    //   'query_var' => false,
    //   'rewrite' => false
    //   ));
    // 
    //   $labels = array(
    //   'name' => _x( 'Sidebar Exclusions', 'taxonomy general name' ),
    //   'singular_name' => _x( 'Sidebar Exclusion', 'taxonomy singular name' ),
    //   'search_items' =>  __( 'Search Sidebar Exclusions' ),
    //   'all_items' => __( 'All Sidebar Exlcusions' ),
    //   'parent_item' => __( 'Parent Sidebar Exclusion' ),
    //   'parent_item_colon' => __( 'Parent Sidebar Exclusion:' ),
    //   'edit_item' => __( 'Edit Sidebar Exclusion' ),
    //   'update_item' => __( 'Update Sidebar Exclusion' ),
    //   'add_new_item' => __( 'Add New Sidebar Exclusion' ),
    //   'new_item_name' => __( 'New Sidebar Exclusion Name' ),
    //   'separate_items_with_commas' => __( 'Separate exclusions with commas' ),
    //       'add_or_remove_items' => __( 'Add or remove sidebar exclusions' ),
    //       'choose_from_most_used' => __( 'Choose from the most sidebar exclusions'),
    //       'menu_name' => false
    //   );
    // 
    // 
    //   register_taxonomy( 'sidebar_exclusion', $valid_post_types, array(
    //   'hierarchical' => false,
    //   'labels' => $labels,
    //   'show_ui' => true,
    //   'show_in_nav_menus' => false,
    //   'query_var' => false,
    //   'rewrite' => false
    //   ));
    // 
    //   $scs = get_option('default_sidebar_categories',array("Default"));
    // 
    //   foreach($scs as $sc) {
    //     wp_insert_term( $sc, 'sidebar_category');
    //   }
    // });
    
    foreach($this->areas as $area) {
      $_this = $this;
      add_action("hbgs_${area}_widgets", function() use ($_this, $area)  {
        $_this->widgets_for($area);
      });
      
      add_action("gt_${area}_widgets", function() use ($_this, $area)  {
        $_this->widgets_for($area);
      });
    }
  }
  
  public static function setup() {
    self::update_db_check();
    $gt_dynamic_sidebars = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }
  
  public function admin_markup() {
    $sidebars = $this->sidebars_as_json();
    $sidebarString = json_encode($sidebars);
    
    include GT_DYNAMIC_SIDEBARS_PATH.'templates/admin.php';
  }
  
  private function add_default_taxonomy($post_id=null,$is_singular=true) {
    global $post;

    $id = !!$post_id ? $post_id : (isset($post) ? $post->ID : null);

    if(!!$id) {
      
      if(count(wp_get_object_terms($id,'sidebar_category')) === 0 || !$is_singular) {
        $scs = get_option('default_sidebar_categories',array("Default"));

        $scs_slugs = array();
        foreach($scs as $sc) {
          $scs_slugs[] = strtolower(preg_replace('/\W/',"-",$sc));
        }

        wp_set_object_terms( $id, $scs_slugs, 'sidebar_category', false );
      }
    }

  }
  
  public function widgets_for($area,$cb=false) {
    global $wp_query, $hbgs_meta, $post;
    
    if(!isset($this->sidebars->$area)) {
      return true;
    }
    
    $item_id = is_singular() ? $post->ID : isset($wp_query->queried_object_id) ? $wp_query->queried_object_id : false;
    
    // if($item_id) {
    //   $this->add_default_taxonomy($item_id,is_singular());
    // }
    

    $area_sidebars = isset($this->sidebars->$area) ? GtDynamicSidebars::as_json($this->sidebars->$area) : false;

    if($area_sidebars && is_array($area_sidebars)) {
      
      usort($area_sidebars, function($a,$b) {
        if($a->position == $b->position){
          return 0;
        }
        return ($a->position < $b->position) ? -1 : 1;
      });
       
      // $categories = wp_get_object_terms($item_id,'sidebar_category');
      // $exclusions = wp_get_object_terms($item_id,'sidebar_exclusion');
      // 
      // if(empty($categories)) {
      //   $categories = array();
      //   $default_category_names = get_option('default_sidebar_categories');
      //   
      //   $default_category_names = is_array($default_category_names) ? $default_category_names : array();
      //   
      //   foreach($default_category_names as $dc) {
      //     $categories[] = get_term_by( 'name', preg_replace('/\W/','-',strtolower($dc)), 'sidebar_category');
      //   }
      // }

      foreach($area_sidebars as $sidebar) {
        // if(!$this->has_category_match($categories,$exclusions,$sidebar->categories)) {
        //   continue;
        // }

        $slug = $area.'_'.strtolower(preg_replace('/\W/',"_",$sidebar->name));
        if ( is_active_sidebar( $slug ) ) { 
          if($cb) {
            $cb($slug,$sidebar);
          } else {
            $this->render_sidebar($sidebar,$slug,$area);
          }
        }
      }
    }
  }
  
  public function find_sidebar($slug) {
    
    $sidebar = false;
    foreach($this->sidebars as $key => $area) {
      if(is_array($area)) {
        foreach($area as $area_sidebar) {
          if($slug == $key.'_'.strtolower(preg_replace('/\W/',"_",$area_sidebar->name))) {
            $sidebar = $area_sidebar;
            break;
          }
          if($sidebar) {
            break;
          }
        }
      }
    }
    
    return $sidebar;
  }
  
  private function background_styles($key) {
    $background = of_get_option($key);
    
    if ($background['image']) {
      echo "background: url('{$background['image']}') {$background['repeat']} {$background['position']} {$background['attachment']} {$background['color']};";
    } else if($background['color']) {
      echo "background-color: {$background['color']};";
    }
  }
  
  private function css_classes($key) {
    $classes = 'sidebar ';
    $columns = of_get_option("{$key}_columns",0);
    $offset = of_get_option("{$key}_offset",0);
    $row_type = of_get_option("{$key}_row_type","None");
    
    $classes .= intval($columns > 0) ? " span{$columns}" : "";
    $classes .= intval($offset > 0) ? " offset{$offset}" : "";
    
    if ($row_type == 'Fluid') {
      $classes .= ' row-fluid';
    } else if($row_type == 'Static') {
      $classes .= ' row';
    }
    
    echo $classes;
  }
  
  public function render_sidebar($sidebar, $slug, $area) { 
    if(!$slug) {
      $slug = "{$area}_{$sidebar->name}";
    }
    
    $header_height_style = "";
    $header_width_style = "";
    if($header_height = of_get_option("{$slug}_header_height",0)) {
      $header_height_style = "height: {$header_height};";
    }
    
    if($header_width = of_get_option("{$slug}_header_width",0)) {
      $header_width_style = "width: {$header_width};";
    }
    
    $widget_string = "";
    
    ob_start();
    
    dynamic_sidebar($slug);
    
    $widget_string = trim(ob_get_contents());
    
    ob_end_clean();
    
    if($widget_string != '') { $tag_name = of_get_option("{$slug}_tag_name","aside"); ?>
    <<?php echo empty($tag_name) ? "aside" : $tag_name ?> 
      id="<?php echo $slug ?>" 
      style="<?php $this->background_styles("{$slug}_background") ?>" 
      class="<?php $this->css_classes("{$slug}") ?>">
        <?php if($title = of_get_option("{$slug}_title",false)): ?>
          <header
            class="<?php echo of_get_option("{$slug}_hide_header_text",0) ? 'ir' : '' ?>"
            style="<?php $this->background_styles("{$slug}_header_background") ?> <?php echo $header_height_style ?> <?php echo $header_width_style ?>"
          ><h3><?php echo $title ?></h3></header>
        <?php endif; ?>
        <?php echo $widget_string; ?>
    </<?php echo of_get_option("{$slug}_tag_name","aside") ?>>
  <?php }
  }
  
  private function has_category_match($sidebar_categories_on_post,$sidebar_exclusions_on_post,$sidebar_categories_on_sidebar) {
    $sidebar_exclusions_on_post = is_array($sidebar_exclusions_on_post) ? $sidebar_exclusions_on_post : array();
    if(!is_array($sidebar_categories_on_sidebar)) {
      return true;
    }
    
    if(!is_array($sidebar_categories_on_post)) {
      return false;
    }

    $sidebar_exclusions_on_post_ids = array();

    foreach($sidebar_exclusions_on_post as $ex) {
      $sidebar_exclusions_on_post_ids[] = $ex->term_id;
    }

    foreach($sidebar_categories_on_post as $cat) {
      if(isset($cat) && $cat && in_array($cat->term_id,$sidebar_categories_on_sidebar)) {
        foreach($sidebar_exclusions_on_post_ids as $id) {
          if(in_array($id,$sidebar_categories_on_sidebar)) {
            return false;
          }
        }
        return true;
      }
    }

    return false;
  }
  
  
  public function widgets_init() {
    $this->sidebars = $this->sidebars_as_json();
    $this->sidebarString = json_encode($this->sidebars);
    
    if(!isset($this->sidebars)) {
      $this->sidebars = array();
    }
    
    foreach($this->sidebars as $key => $value) {
      $area_name = $name = ucwords(str_replace("_"," ",$key));
      $value = GtDynamicSidebars::as_json($value);

      if(!$value) continue;

      usort($value, function($a,$b) {
        if($a->position == $b->position){
          return 0;
        }
        return ($a->position < $b->position) ? -1 : 1;
      }); 

      foreach($value as $sidebar) {
        $slug = $key.'_'.strtolower(preg_replace('/\W/',"_",$sidebar->name));
        register_sidebar( array(
         'name' => __( "${area_name} ". $sidebar->name, 'hbgs' ),
         'id' => $slug,
         'before_widget' => '<div id="%1$s" class="moreclasses widget-container %2$s"><div class="widget-content">',
         'after_widget' => ('</div></div>'),
         'before_title' => '<hgroup class="title-wrapper"><h3 class="widget-title">',
         'after_title' => ('</h3></hgroup>')
        ) );
      }
    }
  }
  
  private function sidebars_as_json() {
    return self::as_json(get_option('hbgs_sidebars',""));
  }
  
  public static function get_sidebars_as_json() {
    return self::as_json(get_option('hbgs_sidebars',""));
  }
  
  private static function as_json($value) {
    if(is_string($value)) {
      return GtDynamicSidebars::as_json(json_decode($value));
    }

    return $value;
  }
  
}

register_activation_hook( __FILE__, array('GtDynamicSidebars', 'activate') );
register_activation_hook( __FILE__, array('GtDynamicSidebars', 'deactivate') );
register_activation_hook( __FILE__, array('GtDynamicSidebars', 'uninstall') );

add_action('plugins_loaded', array('GtDynamicSidebars', 'setup') );

