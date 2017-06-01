<?php
/*
Plugin Name: Gunner Technology Widget Classes
Plugin URI: http://gunnertech.com/gunner-technology-widget-classes
Description: A plugin that allows for adding css classes to widgets
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/

class GtWidgetClasses {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function getConst($const) {
    return self::$$const;
  }
  
  public static function activate() {
    global $wpdb;
  
    update_option(self::$PREFIX."_db_version", self::$VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( self::$PREFIX."_db_version" );
    
    if( $installed_ver != self::$VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    $_this = $this;
    $version = GtWidgetClasses::$PREFIX;
    
    add_action('init',function() {
      wp_enqueue_style( GtWidgetClasses::getConst('PREFIX'), GtWidgetClasses::getConst('URL').'css/style.css' );
    });
    
    add_action('admin_init',function() {
      wp_enqueue_script( GtWidgetClasses::getConst('PREFIX'), GtWidgetClasses::getConst('URL').'js/admin.js', array('jquery') );
    });
    
    add_filter( 'widget_update_callback', function($instance, $new_instance) {
      
      $instance['classes'] = $new_instance['classes'];
      $instance['hidden_desktop'] = isset($new_instance['hidden_desktop']) ? $new_instance['hidden_desktop'] : false ;
      $instance['hidden_phone'] = isset($new_instance['hidden_phone']) ? $new_instance['hidden_phone'] : false;
      $instance['row-style'] = $new_instance['row-style'];
      $instance['columns'] = $new_instance['columns'];
      $instance['offset'] = $new_instance['offset'];
      
      $instance['background-image'] = $new_instance['background-image'];
      $instance['background-image-position'] = $new_instance['background-image-position'];
      $instance['background-image-repeat'] = $new_instance['background-image-repeat'];
      $instance['background-color'] = $new_instance['background-color'];
      
      $instance['header-background-image'] = $new_instance['header-background-image'];
      $instance['header-background-image-position'] = $new_instance['header-background-image-position'];
      $instance['header-background-image-repeat'] = $new_instance['header-background-image-repeat'];
      $instance['header-background-image-height'] = $new_instance['header-background-image-height'];
      $instance['header-background-image-width'] = $new_instance['header-background-image-width'];
      $instance['header-image-replace'] = isset($new_instance['header-image-replace']) ? $new_instance['header-image-replace'] : false;
      $instance['header-background-color'] = $new_instance['header-background-color'];
      
      return $instance;

    }, 10, 2);
    
    add_action('admin_init', function() {
      wp_enqueue_script( GtWidgetClasses::getConst('PREFIX'), GtWidgetClasses::getConst('URL').'/js/script.js', array('jquery'));
    });
    
    add_action('in_widget_form', function($widget,$return,$instance) use ($_this) {
      if ( !isset($instance['classes']) ) {
        $instance['classes'] = null;
      }
      
      if ( !isset($instance['hidden_desktop']) ) {
        $instance['hidden_desktop'] = null;
      }
      
      if ( !isset($instance['hidden_tablet']) ) {
        $instance['hidden_tablet'] = null;
      }
      
      if ( !isset($instance['hidden_phone']) ) {
        $instance['hidden_phone'] = null;
      }
      
      if ( !isset($instance['row-style']) ) {
        $instance['row-style'] = 'row-default';
      }
      
      if ( !isset($instance['columns']) ) {
        $instance['columns'] = 0;
      }
      
      if ( !isset($instance['header-image-replace']) ) {
        $instance['header-image-replace'] = 0;
      }
      
      if ( !isset($instance['background-image-position']) ) {
        $instance['background-image-position'] = 'center center';
      }
      
      if ( !isset($instance['background-color']) ) {
        $instance['background-color'] = '';
      }
      
      if ( !isset($instance['header-background-color']) ) {
        $instance['header-background-color'] = '';
      }
      
      if ( !isset($instance['background-image-repeat']) ) {
        $instance['background-image-repeat'] = 'no-repeat';
      }
      
      if ( !isset($instance['header-background-image-position']) ) {
        $instance['header-background-image-position'] = 'center center';
      }
      
      if ( !isset($instance['header-background-image-repeat']) ) {
        $instance['header-background-image-repeat'] = 'no-repeat';
      }
      
      $row = "";
      
      $row .= "<h3><a class='widget-options-toggle' href='#'>Display Options</a></h3>\n";
      $row .= "<div class='widget-options-container' style='display:none;'>\n";
      
      $row .= "\t<h4>Grid Options</h4>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-classes'>Additional Classes <small>(separate with spaces)</small></label>\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' class='widefat' value='{$instance['classes']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-row-style'>Row Style</label>\n";
      $row .= "\t<select class='widefat' name='widget-{$widget->id_base}[{$widget->number}][row-style]' id='widget-{$widget->id_base}-{$widget->number}-row-style'>\n";
      foreach(array('row-default' => 'Default', 'row' => 'Static', 'row-fluid' => 'Fluid') as $key => $value) {
        $row .= "<option ".selected($instance['row-style'],$key,false)." value='{$key}'>{$value}</option>";
      }
      $row .= "</select>";
      $row .= "</p>\n";
            
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-columns'>Columns</label>\n";
      $row .= "\t<select class='widefat' name='widget-{$widget->id_base}[{$widget->number}][columns]' id='widget-{$widget->id_base}-{$widget->number}-columns'>\n";
      foreach(range(0, 12) as $columns) {
        $row .= "<option ".selected($instance['columns'],"span{$columns}",false)." value='span{$columns}'>{$columns}</option>";
      }
      $row .= "</select>";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-offset'>Offset</label>\n";
      $row .= "\t<select class='widefat' name='widget-{$widget->id_base}[{$widget->number}][offset]' id='widget-{$widget->id_base}-{$widget->number}-offset'>\n";
      $instance['offset'] = isset($instance['offset']) ? $instance['offset'] : 0;
      foreach(range(0, 12) as $offset) {
        $row .= "<option ".selected($instance['offset'],"offset{$offset}",false)." value='offset{$offset}'>{$offset}</option>";
      }
      $row .= "</select>";
      $row .= "</p>\n";
      
      $row .= "\t<h4>Background Options</h4>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-background-image'>Image</label><br />\n";
      $instance['background-image'] = isset($instance['background-image']) ? $instance['background-image'] : "";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][background-image]' id='widget-{$widget->id_base}-{$widget->number}-background-image' class='upload_image_value' value='{$instance['background-image']}'/>\n";
      $row .= "\t<input class='upload_image_button' type='button' value='Upload' />\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-background-image-repeat'>Repeat</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][background-image-repeat]' id='widget-{$widget->id_base}-{$widget->number}-background-image-repeat' value='{$instance['background-image-repeat']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-background-image-position'>Position</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][background-image-position]' id='widget-{$widget->id_base}-{$widget->number}-background-image-position' value='{$instance['background-image-position']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-background-color'>Color</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][background-color]' id='widget-{$widget->id_base}-{$widget->number}-background-color' value='{$instance['background-color']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "\t<h4>Header Background Options</h4>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-image'>Image</label><br />\n";
      $instance['header-background-image'] = isset($instance['header-background-image']) ? $instance['header-background-image'] : "";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-image]' id='widget-{$widget->id_base}-{$widget->number}-header-background-image' class='upload_image_value' value='{$instance['header-background-image']}'/>\n";
      $row .= "\t<input class='upload_image_button' type='button' value='Upload' />\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-image-repeat'>Repeat</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-image-repeat]' id='widget-{$widget->id_base}-{$widget->number}-header-background-image-repeat' value='{$instance['header-background-image-repeat']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-image-position'>Position</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-image-position]' id='widget-{$widget->id_base}-{$widget->number}-header-background-image-position' value='{$instance['header-background-image-position']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-image-height'>Height</label><br />\n";
      $instance['header-background-image-height'] = isset($instance['header-background-image-height']) ? $instance['header-background-image-height'] : "";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-image-height]' id='widget-{$widget->id_base}-{$widget->number}-header-background-image-height' value='{$instance['header-background-image-height']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-image-width'>Width</label><br />\n";
      $instance['header-background-image-width'] = isset($instance['header-background-image-width']) ? $instance['header-background-image-width'] : "";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-image-width]' id='widget-{$widget->id_base}-{$widget->number}-header-background-image-width' value='{$instance['header-background-image-width']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-background-color'>Color</label><br />\n";
      $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][header-background-color]' id='widget-{$widget->id_base}-{$widget->number}-header-background-color' value='{$instance['header-background-color']}'/>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<input type='checkbox' ".checked($instance['header-image-replace'],1,false)." name='widget-{$widget->id_base}[{$widget->number}][header-image-replace]' value='1' id='widget-{$widget->id_base}-{$widget->number}-header-image-replace'/>";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-header-image-replace'>Replace Title with Image?</label>\n";
      $row .= "</p>\n";
      
      $row .= "\t<h4>Responsive Options</h4>\n";
      
      $row .= "<p>\n";
      $row .= "\t<input type='checkbox' name='widget-{$widget->id_base}[{$widget->number}][hidden_desktop]' id='widget-{$widget->id_base}-{$widget->number}-hidden_desktop' ".checked($instance['hidden_desktop'],'1',false)." value='1'/>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-hidden_desktop'>Hide on Desktops</label>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<input type='checkbox' name='widget-{$widget->id_base}[{$widget->number}][hidden_phone]' id='widget-{$widget->id_base}-{$widget->number}-hidden_phone' ".checked($instance['hidden_phone'],'1',false)." value='1'/>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-hidden_phone'>Hide on Phones</label>\n";
      $row .= "</p>\n";
      
      $row .= "<p>\n";
      $row .= "\t<input type='checkbox' name='widget-{$widget->id_base}[{$widget->number}][hidden_tablet]' id='widget-{$widget->id_base}-{$widget->number}-hidden_tablet' ".checked($instance['hidden_tablet'],'1',false)." value='1'/>\n";
      $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-hidden_tablet'>Hide on Tablets</label>\n";
      $row .= "</p>\n";
      $row .= "</div>\n";

      echo $row;
      
    }, 10, 3);
    
    add_filter('dynamic_sidebar_params', function($params) {
      return GtWidgetClasses::modify_params($params);
    }); 
  }
  
  public static function modify_params($params, $p_key=0, $obj=false, $number=false) {
    global $wp_registered_widgets;
    
    $styles = '';
    $header_styles = '';
    if($obj) {
      $widget_opt = get_option($obj->option_name);
      $widget_num = $number;
    } else {
      $widget_id = $params[$p_key]['widget_id'];
      $widget_obj = $wp_registered_widgets[$widget_id];
      $widget_opt = get_option($widget_obj['callback'][0]->option_name);
      $widget_num = $widget_obj['params'][0]['number'];
    }
    
    $moreclasses = (!isset($widget_opt[$widget_num]['classes']) || empty($widget_opt[$widget_num]['classes'])) ? "" : $widget_opt[$widget_num]['classes'];
    
    if(isset($widget_opt[$widget_num]['hidden_tablet']) && $widget_opt[$widget_num]['hidden_tablet'] == '1') {
      $moreclasses .= " hidden-tablet";
    }
    
    if(isset($widget_opt[$widget_num]['hidden_desktop']) && $widget_opt[$widget_num]['hidden_desktop'] == '1') {
      $moreclasses .= " hidden-desktop";
    }
    
    if(isset($widget_opt[$widget_num]['hidden_phone']) && $widget_opt[$widget_num]['hidden_phone'] == '1') {
      $moreclasses .= " hidden-phone";
    }
    
    if(isset($widget_opt[$widget_num]['columns']) && $widget_opt[$widget_num]['columns'] != 'span0') {
      $moreclasses .= " {$widget_opt[$widget_num]['columns']}";
    }
    
    if(isset($widget_opt[$widget_num]['offset']) && $widget_opt[$widget_num]['offset'] != 'offset0') {
      $moreclasses .= " {$widget_opt[$widget_num]['offset']}";
    }
    
    if(isset($widget_opt[$widget_num]['row-style'])) {
      $moreclasses .= " {$widget_opt[$widget_num]['row-style']}";
    }
    
    if(isset($widget_opt[$widget_num]['background-image']) && $widget_opt[$widget_num]['background-image'] != '') {
      $styles .= "background:url('{$widget_opt[$widget_num]['background-image']}') {$widget_opt[$widget_num]['background-image-repeat']} {$widget_opt[$widget_num]['background-image-position']} scroll {$widget_opt[$widget_num]['background-color']};";
    } else if(isset($widget_opt[$widget_num]['background-color']) && $widget_opt[$widget_num]['background-color'] != '') {
      $styles .= "background-color: {$widget_opt[$widget_num]['background-color']};";
    }
    
    if(isset($widget_opt[$widget_num]['header-background-image']) && $widget_opt[$widget_num]['header-background-image'] != '') {
      $height = isset($widget_opt[$widget_num]['header-background-image-height']) && $widget_opt[$widget_num]['header-background-image-height'] != '' ? "height:{$widget_opt[$widget_num]['header-background-image-height']};" : "";
      
      $width = isset($widget_opt[$widget_num]['header-background-image-width']) && $widget_opt[$widget_num]['header-background-image-width'] != '' ? "width:{$widget_opt[$widget_num]['header-background-image-width']};" : "";
      
      $image_replace = $widget_opt[$widget_num]['header-image-replace'] == 1 ? 'text-indent:-9999px;' : '';
      
      $header_styles = "style='background:url({$widget_opt[$widget_num]['header-background-image']}) {$widget_opt[$widget_num]['header-background-image-repeat']} {$widget_opt[$widget_num]['header-background-image-position']} scroll {$widget_opt[$widget_num]['header-background-color']}; {$height} {$width} {$image_replace}'";
    } else if(isset($widget_opt[$widget_num]['header-background-color']) && $widget_opt[$widget_num]['header-background-color'] != '') {
      $header_styles .= "style='background-color: {$widget_opt[$widget_num]['header-background-color']};'";
    }
    
    if ( $moreclasses != "" ) {
      $params[$p_key]['before_widget'] = preg_replace( '/class="/', "class=\"{$moreclasses} ", $params[$p_key]['before_widget'], 1 );
    }
    
    if($styles != '') {
      $styles = 'style="'.$styles.';" ';
      $params[$p_key]['before_widget'] = preg_replace( '/class=/', "{$styles} class=", $params[$p_key]['before_widget'], 1 );
    }
    
    if($header_styles != '') {
      if (isset($params[$p_key]['before_title'])) {
        $params[$p_key]['before_title'] = preg_replace( '/>/', "{$header_styles}>", $params[$p_key]['before_title'], 1 );
      } else {
        $params[$p_key]['before_title'] = "<div {$header_styles}>";
        $params[$p_key]['after_title'] = "</div>";
      }
    }
    
    return $params;
  }
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_widget_classes";
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

register_activation_hook( __FILE__, array('GtWidgetClasses', 'activate') );
register_activation_hook( __FILE__, array('GtWidgetClasses', 'deactivate') );
register_activation_hook( __FILE__, array('GtWidgetClasses', 'uninstall') );

add_action('plugins_loaded', array('GtWidgetClasses', 'setup') );