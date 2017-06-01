<?php
/*
Plugin Name: Gunner Technology Shortcodes
Plugin URI: http://gunnertech.com/2012/02/shortcodes-wordpress-plugin/
Description: A plugin that adds a plethora of shortcodes we use
Version: 0.0.2
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_SHORTCODES_VERSION', '0.0.2');
define('GT_SHORTCODES_URL', plugin_dir_url( __FILE__ ));

class GtShortcodes {
  private static $instance;
  public static $is_https_request;
  
  public static function activate() {
    update_option("gt_shortcodes_db_version", GT_SHORTCODES_VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_shortcodes_db_version" );
    
    if( $installed_ver != GT_SHORTCODES_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    $_this = $this;
    
    add_action('in_widget_form', function($obj,$return,$instance) use ($_this) {
      $settings_string = '';
      foreach($instance as $key => $value) {
        $settings_string .= ' ' . $key . '=\'' . esc_attr($value) . '\'';
      }
      echo '<p>
        <label>Shortcodes:</label><br />
        <input style="width:100%;" type="text" value="'.esc_attr('[widget id=\'' . $obj->id . '\']').'">
        <br />
        <input style="width:100%;" type="text" value="'.esc_attr('[widget widget_name=\'' . get_class($obj) . '\''.$settings_string.']').'">
      </p>';
    }, 10, 3);
    
    add_shortcode('date', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'format' => 'Y'
      ), $atts));
      
      return date($format);
    });
    
    add_shortcode('updated_time', function($atts, $content=null, $code="") {
      global $post;
      
      extract(shortcode_atts(array(
      ), $atts));
      
      return sprintf( '<span class="entry-date">%1$s</span> <span class="entry-time">%2$s</span></a>',
        get_the_date(),
        get_the_time()
      );
    });
    
    add_shortcode('more_link', function($atts, $content=null, $code="") {
      global $post;
      extract(shortcode_atts(array(
      ), $atts));
      
      return '<a class="call-to-action" href="'.get_permalink($post->ID).'">'.do_shortcode($content).'</a>';
    });
    
    add_shortcode('column', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'size' => 1,
        'class' => '',
        'style' => false,
        'id' => false
      ), $atts));
      
      return '<div '.(!!$id ? 'id="'.$id.'"' : '').' '.(!!$style ? 'style="'.$style.'"' : '').' class="span'.$size.' '.$class. '">'.do_shortcode($content).'</div>';
    });
    
    add_shortcode('row', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'class' => '',
        'fluid' => false,
        'style' => false,
        'id' => false
      ), $atts));
      
      $base_class = $fluid ? 'row-fluid' : 'row';
      
      return '<div '.(!!$id ? 'id="'.$id.'"' : '').' '.(!!$style ? 'style="'.$style.'"' : '').' class="'.$base_class.' '.$class. '">'.do_shortcode($content).'</div>';
    });
    
    add_shortcode('tab', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'active' => 0,
        'title' => ""
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '
        <li class="'.(!!$active ? 'active' : '').'"><a data-toggle="tab" href="#'.GtShortcodes::slugify($title).'">'.$title.'</a></li>
        <div class="tab-pane'.(!!$active ? ' active' : '').'" id="'.GtShortcodes::slugify($title).'">'.do_shortcode($content).'</div>
      ';
      
      return $html;
    });
    
    add_shortcode('tabs', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'class' => '',
        'tab_style' => 'tabs'
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '
        <div class="tabbable '.$class.'">
          <ul class="nav nav-'.$tab_style.'"></ul>
          <div class="tab-content">'.do_shortcode($content).'</div>
        </div>
        
        <script>
          window.GT_tab_timer = setInterval(function() {
            if(window.jQuery) {
              clearInterval(window.GT_tab_timer);
              jQuery(".tab-content").each(function(){
                var $nav_items = jQuery(this).find("li");
                jQuery(this).prev(".nav").append($nav_items);
              });
            }
          },1000);
        </script>
      ';
      
      return $html;
    });
    
    add_shortcode('thumbnails', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '<ul class="thumbnails">'.do_shortcode($content).'</ul>';
      
      return $html;
    });
    
    add_shortcode('thumbnail', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'src' => 0,
        'size' => "6",
        'link' => '#',
        'title' => false,
        'caption' => false
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '
        <li class="span'.$size.'">
          <a href="'.$link.'" class="thumbnail">
            <img src="'.$src.'" alt="'.$title.'" />
            '.(!!$title ? '<h5>'.$title.'</h5>' : '' ).'
            '.(!!$caption ? '<p>'.$caption.'</p>' : '' ).'
          </a>
        </li>
      ';
      
      return $html;
    });
    
    add_shortcode('well', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '<div class="well">'.do_shortcode($content).'</div>';
      
      return $html;
    });
    
    add_shortcode('strip_markup', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
      ), $atts));
      
      return do_shortcode(strip_tags($content));
    });
    
    
    add_shortcode('accordion', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'id' => 'accordion-1'
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '<div id="'.$id.'" class="accordion">'.do_shortcode($content).'</div>';
      
      return $html;
    });
    
    add_shortcode('clear', function($atts, $content=null, $code="") { 
      extract(shortcode_atts(array(), $atts));
      
      return '<div style="clear:both;"></div>';
    });
    
    add_shortcode('donate', function($atts, $content=null, $code="") { 
      extract(shortcode_atts(array(
        'text' => 'Make a donation',
        'account' => 'paypal@gunnertech.com',
        'for' => '',
      ), $atts));

      global $post;

      if (!$for) $for = str_replace(" ","+",$post->post_title);

      return '<a class="donateLink" href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='.$account.'&item_name=Donation+for+'.$for.'">'.$text.'</a>';
    });
    
    add_shortcode('snap', function($atts, $content=null, $code="") { 
      extract(shortcode_atts(array(
        "snap" => 'http://s.wordpress.com/mshots/v1/',
        "url" => 'http://www.catswhocode.com',
        "alt" => 'A Website',
        "w" => '400', // width
        "h" => '300' // height
      ), $atts));

      return '<img src="' . $snap . '' . urlencode($url) . '?w=' . $w . '&h=' . $h . '" alt="' . $alt . '"/>';
    });
    
    add_shortcode('chart', function($atts, $content=null, $code="") { 
      extract(shortcode_atts(array(
          'data' => '',
          'colors' => '',
          'size' => '400x200',
          'bg' => 'ffffff',
          'title' => '',
          'labels' => '',
          'advanced' => '',
          'type' => 'pie'
      ), $atts));

      switch ($type) {
        case 'line' :
          $charttype = 'lc'; break;
        case 'xyline' :
          $charttype = 'lxy'; break;
        case 'sparkline' :
          $charttype = 'ls'; break;
        case 'meter' :
          $charttype = 'gom'; break;
        case 'scatter' :
          $charttype = 's'; break;
        case 'venn' :
          $charttype = 'v'; break;
        case 'pie' :
          $charttype = 'p3'; break;
        case 'pie2d' :
          $charttype = 'p'; break;
        default :
          $charttype = $type;
        break;
      }
      
      $string = '';
      
      if ($title) $string .= '&chtt='.$title.'';
      if ($labels) $string .= '&chl='.$labels.'';
      if ($colors) $string .= '&chco='.$colors.'';
      $string .= '&chs='.$size.'';
      $string .= '&chd=t:'.$data.'';
      $string .= '&chf='.$bg.'';

      return '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$charttype.''.$string.$advanced.'" alt="'.$title.'" />';
    });
    
    add_shortcode('accordion_item', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'parent' => 'accordion-1',
        'title' => 'title',
        'active' => false
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '
        <div class="accordion-group">
          <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$parent.'" href="#'.GtShortcodes::slugify($title).'">
              '.$title.'
            </a>
          </div>
          <div id="'.GtShortcodes::slugify($title).'" class="accordion-body '.(!!$active ? 'in' : 'collapse').'">
            <div class="accordion-inner">'.do_shortcode($content).'</div>
          </div>
        </div>
      ';
      
      return $html;
    });
    
    add_shortcode('bootstrap_carousel', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'id' => 'bootstrap_carousel-1',
        'interval' => 2
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '
        <div id="'.$id.'" class="carousel">
          <div class="carousel-inner">
            '.do_shortcode($content).'
          </div>
          <a class="carousel-control left" href="#'.$id.'" data-slide="prev">&lsaquo;</a>
          <a class="carousel-control right" href="#'.$id.'" data-slide="next">&rsaquo;</a>
        </div>
      
        <script>
          window.GT_carousel_timer = setInterval(function() {
            if(window.jQuery && window.jQuery.fn.carousel) {
              clearInterval(GT_carousel_timer);
              jQuery(".carousel").carousel({
                interval: '.(intval($interval)*1000).'
              });
            }
          },1000);
        </script>
      ';
      
      return $html;
    });
    
    add_shortcode('bootstrap_carousel_item', function($atts, $content=null, $code="") {
      extract(shortcode_atts(array(
        'title' => false,
        'caption' => false,
        'active' => false,
        'src' => 'http://twitter.github.com/bootstrap/assets/img/bootstrap-mdo-sfmoma-01.jpg'
      ), $atts));
      
      $content = str_replace("<br />","",$content);
      $content = str_replace("\r\n\r\n","\r\n",$content);
      $content = str_replace("\n\n","\n",$content);
      
      $html = '<div class="item '.($active ? 'active' : '').'">';
      $html .= '<img src="'.$src.'" alt="'.$title.'" />';
      
      if($title || $caption) {
        $html .= '<div class="carousel-caption">';
      }
      
      if($title) {
        $html .= '<h4>'.$title.'</h4>';
      }
      
      if($caption) {
        $html .= '<p>'.$caption.'</p>';
      }
      
      if($title || $caption) {
        $html .= '</div>';
      }
      
      $html .= '</div>';
      
      return $html;
    });
    
    
    add_shortcode('widget', function($atts, $content=null, $code="") use ($_this) {
      global $hbgs_inline_count, $hbgs_scripts, $hbgs_type_of_assets_to_return, $wp_widget_factory;
      
      if(!isset($hbgs_inline_count)) {
        $hbgs_inline_count = array();
      }
      
      extract(shortcode_atts(array(
        "id" => false
      ), $atts));
      
      if(!$id) {

        extract(shortcode_atts(array(
          'widget_name' => FALSE,
          'w_id' => FALSE
        ), $atts));

        $widget_name = wp_specialchars($widget_name);

        if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
            $wp_class = 'WP_Widget_'.ucwords(strtolower($class));

            if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
                return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
            else:
                $class = $wp_class;
            endif;
        endif;

        ob_start();
        echo '<div class="widget-container" '.($w_id ? 'id="'.$w_id.'"' : '').'>';
        the_widget($widget_name, $atts, array(
            'widget_id'=>'arbitrary-instance-'.$w_id,
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
            'before_widget' => '<div class="widget-content">',
            'after_widget' => '</div>'
        ));
        echo '</div>';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
      }
      
      $echo = false;
      
      $count_key = isset($hbgs_type_of_assets_to_return) ? $hbgs_type_of_assets_to_return : 'html';
      $args = $_this->get_widget_instance($id);
      $obj = $args['obj'];

      if(!$args['obj']) {
        return false;
      }
      
      $out = '';
      $atts = (array)$atts;
      $unique_id = $id;

      if(isset($atts['id'])) {
        $hbgs_inline_count[$count_key] = isset($hbgs_inline_count[$count_key]) ? $hbgs_inline_count[$count_key] : 0;
        $hbgs_inline_count[$count_key]++;
        $atts['nocache'] = 1;
        $atts['filter_key'] = $hbgs_inline_count[$count_key];
        
        
        $unique_id = ($id.'-'.$hbgs_inline_count[$count_key]);

        unset($atts['id']);

        $args['instance']['styles'] = isset($args['instance']['styles']) ? preg_replace(array('/#widget-id/'),array('#'.$unique_id), $args['instance']['styles']) : '';
        
        $args['instance']['scripts'] = isset($args['instance']['scripts']) ? preg_replace(array('/#widget-id/'),array('#'.$unique_id), $args['instance']['scripts']) : '';
        
        
        $args['args'] = isset($args['args']) ? $args['args'] : array(
           'before_widget' => '<div id="%1$s" class="moreclasses widget-container %2$s"><div class="widget-content">',
           'after_widget' => ('</div></div>'),
           'before_title' => '<hgroup class="title-wrapper"><h3 class="widget-title">',
           'after_title' => ('</h3></hgroup>')
        );
        
        if(class_exists('GtWidgetClasses')) {
          $conversion = GtWidgetClasses::modify_params($args,'args',$args['obj'],$args['number']);
          $args['args'] = $conversion['args'];
        }
        
        $args['args']['filter_key'] = $hbgs_inline_count[$count_key];
        $args['args']['before_widget'] = preg_replace("/id=\"".$id."\"/", 'id="'.$unique_id.'"', $args['args']['before_widget']);
        
        

        // $filter = function($instance,$obj,$args) use ($atts) {
        //   if($instance['filter_key'] == $atts['filter_key']) {
        //     $instance = array_merge($instance,$atts);
        //   }
        // 
        //   return $instance;
        // };
        // 
        // add_filter('widget_display_callback',$filter,77,3); 
      }

      if($echo) {
        $obj->print_styles($id,$args['instance']);
        //$obj->display_callback($args['args'], array('number' => $args['number'] ) );
        $obj->widget($args['args'], array_merge($args['instance'],$atts,array('number' => $args['number']) ) );
      } else {
        ob_start();
        if($hbgs_type_of_assets_to_return == 'css') {
          //hbgs_render_styles($unique_id,null,$args['instance']['styles']);
        } else if($hbgs_type_of_assets_to_return == 'js') {
          //hbgs_render_scripts($unique_id,null,$args['instance']['scripts'],$atts);
        }
        $out = ob_get_contents();
        ob_end_clean();

        ob_start();
                
        $obj->widget($args['args'], array_merge($args['instance'],$atts,array('number' => $args['number']) ) );
        $html = ob_get_contents();
        ob_end_clean();

        if($hbgs_type_of_assets_to_return != 'css' && $hbgs_type_of_assets_to_return != 'js') {
          $out .= $html;
        }

        return $out;
      }
    });
    
  }
  
  
  public static function setup() {
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
  
  static public function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    if (function_exists('iconv')) {
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }
  
  public function get_widget_instance($id) {
    global $wp_registered_widgets;

    preg_match('/-(\d+$)/',$id,$matches);
    $number = $matches[1];

    foreach($wp_registered_widgets as $key => $wid) {
      if($key == $id) {
        $widget_object = $wid['callback'][0];
        $instances = $widget_object->get_settings();
        $before_widget = '<div id="%1$s" class="moreclasses widget-container %2$s %3$s"><div class="widget-content">';

        $before_widget = sprintf($before_widget, $id, $widget_object->widget_options['classname'], $id);

        $args = array(
          'name' => $widget_object->name,
          'id' => $widget_object->id_base, 
          'description' => $widget_object->widget_options['description'],
          'before_widget' => $before_widget,
          'after_widget' => '</div></div>',
          'before_title' => '<hgroup class="title-wrapper"><h3 class="widget-title">',
          'after_title' => '</h3></hgroup>',
          'widget_id' => $widget_object->id, 
          'widget_name' => $widget_object->name
        );
        return array('obj' => $widget_object, 'instance' => $instances[$number], 'number' => $number, 'args' => $args);
      }
    }

    return null;
  }
  
}

register_activation_hook( __FILE__, array('GtShortcodes', 'activate') );
register_activation_hook( __FILE__, array('GtShortcodes', 'deactivate') );
register_activation_hook( __FILE__, array('GtShortcodes', 'uninstall') );

add_action('plugins_loaded', array('GtShortcodes', 'setup') );

