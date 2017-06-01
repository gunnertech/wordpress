<?php
/*
Plugin Name: Gunner Technology Custom Fields
Plugin URI: http://gunnertech.com/gunner-technology-custom-fields
Description: A plugin that allows authors to add custom fields to their posts
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


class GtCustomFields {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
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
  
  private function __construct(){
    add_action( 'gt_featured_image', function() {
      $html = '';
      $meta = GtCustomFields::meta();
      $thumbnail = GtCustomFields::post_thumbnail(get_the_ID(),(is_single() ? 'detailed' : 'list')); 
      
      if($thumbnail) {
        $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large');
        
        $html .= '<figure class="featured-image">';
        $html .= '<a href="' . get_permalink(get_the_ID()) . '" title="' . esc_attr(get_the_title() ? get_the_title() : get_the_ID()) . '">';
        $html .= $thumbnail;
        if($attachment && isset($attachment->post_title)) {
          $html .= '<figcaption>';
          $html .= $attachment->post_title;
          $html .= '</figcaption>';
        }
        
        $html .= '</a></figure>';
      }
      
      echo $html;
      
    });
    
    add_action('init',function() {
    });
    
    add_action('admin_init',function() {
      add_action('save_post', function($post_id) {
        if (!array_key_exists('hbgs_meta_noncename',$_POST)) return $post_id;
        if (!wp_verify_nonce($_POST['hbgs_meta_noncename'],__FILE__)) return $post_id;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

        $current_data = get_post_meta($post_id, '_hbgs_meta', TRUE);  

        $new_data = $_POST['_hbgs_meta'];

        GtCustomFields::clean($new_data);

        if ($current_data) {
          if (is_null($new_data)) delete_post_meta($post_id,'_hbgs_meta');
          else update_post_meta($post_id,'_hbgs_meta',$new_data);
        } elseif (!is_null($new_data)) {
          add_post_meta($post_id,'_hbgs_meta',$new_data,TRUE);
        }

        return $post_id;
      });
      
      if (current_user_can('delete_published_posts')) {
        $hbgs_meta_boxes_has_been_nonced = false;
        $custom_types = get_post_types();
        $metas = GtCustomFields::meta_boxes();
        
        foreach ($metas as $meta) {
          foreach ($custom_types as $type) {
            $callback = function() use ($meta, &$hbgs_meta_boxes_has_been_nonced){
              if(!$hbgs_meta_boxes_has_been_nonced) {
                echo '<input type="hidden" name="hbgs_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
              }
              $hbgs_meta_boxes_has_been_nonced = true;
              $meta['callback']();
            };
            add_meta_box('hbgs_'.$meta['slug'], $meta['title'], $callback, $type, $meta['placement'], $meta['priority']);
          }
        }
      }
    });
  }
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_custom_fields";
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
  
  public static function meta_boxes() {
    $hbgs_meta = GtCustomFields::meta();
    return array(
      array(
        "slug" => "featured_images",
        "title" => "Featured Image Sizes",
        "placement" => "side",
        "priority" => "low",
        "callback" => function() { 
          $hbgs_meta = array_merge(
            array(
              "image_size_detailed_view" => get_option("image_size_detailed_view",'large'),
              "image_size_list_view" => get_option("image_size_list_view",'large')
            ),
            GtCustomFields::meta()
          );
          ?><div class="ajaxtag notypeahead">
            <p>
              <span style="display:inline-block; width: 124px;">Detailed View:</span>
              <select name="_hbgs_meta[image_size_detailed_view]">
                <option value="0">None</option>
                <?php $image_sizes = GtCustomFields::image_sizes(); foreach($image_sizes as $image_size): ?>
                  <option value="<?php echo $image_size ?>" <?php selected($hbgs_meta["image_size_detailed_view"],$image_size) ?>><?php echo $image_size ?></option>
                <?php endforeach; ?>
              </select>
            </p>
            <p>
              <span style="display:inline-block; width: 124px;">List View:</span>
              <select name="_hbgs_meta[image_size_list_view]">
                <option value="0">None</option>
                <?php $image_sizes = GtCustomFields::image_sizes(); foreach($image_sizes as $image_size): ?>
                  <option value="<?php echo $image_size ?>" <?php selected($hbgs_meta["image_size_list_view"],$image_size) ?>><?php echo $image_size ?></option>
                <?php endforeach; ?>
              </select>
            </p>
          </div> 
        <?php }
      ),
      array(
        "slug" => "custom_templates",
        "title" => "Custom Templates",
        "placement" => "side",
        "priority" => "low",
        "callback" => function() { 
          $hbgs_meta = array_merge(
            array(
              "custom_template" => get_option("custom_template","0")
            ),
            GtCustomFields::meta()
          );
          ?><div class="ajaxtag notypeahead">
            <p>
              <span style="display:inline-block; width: 124px;">Custom Template:</span>
              <select name="_hbgs_meta[custom_template]">
                <option value="0">None</option>
                <?php
                if (file_exists(TEMPLATEPATH."/php/templates/custom") && $handle = opendir(TEMPLATEPATH."/php/templates/custom")) {
                  while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                      echo "<option ".selected($hbgs_meta["custom_template"],$entry,false)." value='$entry'>".str_replace(".php","",$entry)."</option>";
                    }
                  }
                  closedir($handle);
                }
                if (file_exists(STYLESHEETPATH."/php/templates/custom") && $handle = opendir(STYLESHEETPATH."/php/templates/custom")) {
                  while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                      echo "<option ".selected($hbgs_meta["custom_template"],$entry,false)." value='$entry'>".str_replace(".php","",$entry)."</option>";
                    }
                  }
                  closedir($handle);
                }
                ?>
              </select>
            </p>
          </div> 
        <?php }
      ),
      array(
        "slug" => "column_span_box",
        "title" => "Column Span",
        "placement" => "side",
        "priority" => "low",
        "callback" => function() { 
          $hbgs_meta = array_merge(
            array(
              "column_span" => get_option("column_span")
            ),
            GtCustomFields::meta()
          );
          ?><div class="ajaxtag notypeahead">
            <p>
              <select name="_hbgs_meta[column_span]">
                <option></option>
                <?php foreach(range(1,12) as $column_span): ?>
                  <option value="span<?php echo $column_span ?>" <?php selected($hbgs_meta["column_span"],"span{$column_span}") ?>><?php echo $column_span ?></option>
                <?php endforeach; ?>
              </select>
            </p>
          </div> 
        <?php }
      )
    );
  }
  
  public static function image_sizes() {
    $image_sizes = array('thumbnail', 'medium', 'large', 'full'); // Standard sizes
    $custom_image_json = array();

    if(is_array($custom_image_json)) {
      foreach($custom_image_json as $custom_image) {
        $image_sizes[] = $custom_image->name;
      }
    }

    return $image_sizes;
  }
  
  public static function meta($reload=true) {
    global $wp_query, $post, $hbgs_meta;

    $meta = $hbgs_meta;
    if($reload) {
      if(isset($post)) {
        $meta = get_post_meta($post->ID,'_hbgs_meta',TRUE);
      } elseif(isset($wp_query->queried_object)) {
        $meta = get_post_meta($wp_query->queried_object->ID,'_hbgs_meta',TRUE);
      }
    } else {
      if(isset($hbgs_meta)) {
        $meta = $hbgs_meta;
      } else {
        if(is_search() || is_category()) {
          $meta = get_post_meta(get_option('page_for_posts'),'_hbgs_meta',TRUE);
        } elseif(isset($wp_query->queried_object) && isset($wp_query->queried_object->ID)) {
          $meta =  get_post_meta($wp_query->queried_object->ID,'_hbgs_meta',TRUE);
        } elseif(isset($post)) {
          $meta = get_post_meta($post->ID,'_hbgs_meta',TRUE);
        }
      }
    }

    if(!is_array($meta)) {
      $meta = array();
    }

    return $meta;
  }
  
  public static function post_thumbnail($content_id,$view) {
    $hbgs_meta = GtCustomFields::meta();

    $hbgs_meta['image_size_'.$view.'_view'] = isset($hbgs_meta['image_size_'.$view.'_view']) ? $hbgs_meta['image_size_'.$view.'_view'] : get_option('image_size_'.$view.'_view');
    if( $hbgs_meta['image_size_'.$view.'_view'] !== '0' ) {
      return get_the_post_thumbnail($content_id,$hbgs_meta['image_size_'.$view.'_view']);
    } else {
      return false;
    }
  }
  
  
  public static function clean(&$arr) {
    if (is_array($arr)) {
      foreach ($arr as $i => $v) {
        if (is_array($arr[$i])) {
          self::clean($arr[$i]);

          if (!count($arr[$i])) {
            unset($arr[$i]);
          }
        } else {
          if (trim($arr[$i]) == '') {
            unset($arr[$i]);
          } else {
            $arr[$i] = self::sanitize($arr[$i]);
          }
        }
      }

      if (!count($arr)) {
        $arr = NULL;
      }
    }
  }
  
  public static function sanitize($test_value) {
    $siteurl = str_replace("/",'\/',preg_quote(get_option('siteurl')));
    $pattern = '/'.$siteurl.'(.+)/';
    $new_value = $test_value;
    if(is_array($test_value)) {
      foreach($test_value as $key => $value) {
        if(is_string($value) && preg_match($pattern, $value)) {
          $new_value[$key] = preg_replace($pattern,'\\1',$value);
        }
      }
    } else if($test_value && is_string($test_value) && preg_match($pattern, $test_value)) {
      $new_value = preg_replace($pattern,'\\1',$test_value);
    }

    if($new_value == 'null') {
      $new_value = null;
    }

    return $new_value;
  }
}

register_activation_hook( __FILE__, array('GtCustomFields', 'activate') );
register_activation_hook( __FILE__, array('GtCustomFields', 'deactivate') );
register_activation_hook( __FILE__, array('GtCustomFields', 'uninstall') );

add_action('plugins_loaded', array('GtCustomFields', 'setup') );