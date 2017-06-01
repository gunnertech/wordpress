<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Widget.php 2010-11-18 codyswann
 */

 
class Hbgs_Widget extends WP_Widget {
  public static $inited = false;  
	
	function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {
    //    add_action( 'save_post', array(&$this, 'flush_widget_cache') );
    // add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
    // add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
		
	  if(!self::$inited) {
      add_filter('widget_update_callback', array(&$this,'hbgs_widget_update_callback'), 10,4);
      add_filter('widget_form_callback', array(&$this,'hbgs_widget_form_callback'), 10,2);
      add_filter('widget_display_callback', array(&$this,'hbgs_widget_display_callback'), 10,3);
      add_action('in_widget_form', array(&$this,'hbgs_in_widget_form'), 10, 3);
    }
    
    self::$inited = true;
	  
	  parent::__construct( $id_base, $name, $widget_options, $control_options );
	}
	
	function flush_widget_cache($instance=null, $new_instance=null, $old_instance=null, $obj=null) {
	  if(!$obj) {
	    $obj = $this;
	  }
		wp_cache_delete($obj->id, 'widget');
	}
	
	function do_pre_cache() {
	  
	}
	
	function widget($args, $instance) {
	  if(isset($instance['nocache'])) {
	    $this->render($args,$instance);
	    return;
	  } 
	  
	  $this->do_pre_cache();
    // $cache = wp_cache_get($this->id, 'widget');
    // 
    // if ( !is_array($cache) )
    //  $cache = array();
    // 
    // if ( isset($cache[$args['widget_id']]) ) {
    //  echo $cache[$args['widget_id']];
    //  return;
    // }
    // 
    // ob_start();
		$this->render($args,$instance);
    // 
    // $cache[$args['widget_id']] = ob_get_flush();
    // wp_cache_set($this->id, $cache, 'widget', $this->cache_expire());
	}
	
	function cache_expire() {
	  return 0;
	}
  
	
	function hbgs_widget_update_callback($instance, $new_instance, $old_instance, $obj) {
	  $instance['css_classes'] = isset($new_instance['css_classes']) ? $new_instance['css_classes'] : '';
    $instance['styles'] = $new_instance['styles'];
    $instance['scripts'] = $new_instance['scripts'];
    
    $siteurl = str_replace("/",'\/',preg_quote(get_option('siteurl')));
    $pattern = '/'.$siteurl.'(.+)/';
    
    foreach($instance as $key => $value) {
      if(is_string($value) && preg_match($pattern, $value)) {
        $instance[$key] = preg_replace($pattern,'\\1',$value);
      }
    }
    
    if(isset($obj->properties) && is_array($obj->properties)) {
      foreach($obj->properties as $key => $prop) {
        if(is_array($prop)) {
          if($prop['strip_tags']) {
            $instance[$key] = strip_tags($new_instance[$key]);
          } else {
            $instance[$key] = $new_instance[$key];
          }
        }
      }
    }
    $this->set_grid_preferences($instance,$new_instance);
    $this->flush_widget_cache($instance, $new_instance, $old_instance, $obj);
    
    return $instance;
  }

  function hbgs_widget_display_callback($instance, $obj, $args) {
    if(isset($obj->properties) && is_array($obj->properties)) {
      foreach($obj->properties as $key => $prop) {
        if(is_array($prop)) {
          if($prop['strip_tags']) {
            $instance[$key] = strip_tags($instance[$key]);
          }
        }
      }
    }
    return $instance;
  }

  function hbgs_widget_form_callback($instance,$obj) {
    $instance = wp_parse_args( (array) $instance, $this->get_grid_defaults() );
    return $instance;
  }

  function hbgs_in_widget_form($obj,$return,$instance) {
    $this->print_css_classes($obj,$instance);
    $this->print_styles_textarea($obj,$instance);
    $this->print_scripts_textarea($obj,$instance);
    $this->print_grid_forms($instance,$obj);
    $this->print_shortcode($instance, $obj);
    $this->print_help_link($instance, $obj);
  }
  
  function print_shortcode($instance,$obj) {
    $settings_string = '';
    foreach($instance as $key => $value) {
      $settings_string .= ' ' . $key . '=\'' . esc_attr($value) . '\'';
    }
    echo '<p>
      <label>Shortcodes:</label><br />
      <input style="width:100%;" type="text" value="'.esc_attr('[widget id=\'' . $obj->id . '\''.$settings_string.']').'">
      <br />
      <input style="width:100%;" type="text" value="'.esc_attr('[widget widget_name=\'' . get_class($obj) . '\''.$settings_string.']').'">
    </p>';
  }
  
  function print_help_link($instance,$object) { ?>
    <p>
			<a href="">ADD HELP LINK</a>
		</p>
  <?php }
  
  function print_grid_forms($instance,$object) { ?>
    <p><a href="#grid-fields" class="field-toggle">Show Grid Fields</a></p>
    <fieldset class="grid-fields" style="display:none;">
      <p>
      	<label for="<?php echo $object->get_field_id( 'grid_columns' ); ?>">Grid Columns:</label>
      	<select id="<?php echo $object->get_field_id( 'grid_columns' ); ?>" name="<?php echo $object->get_field_name( 'grid_columns' ); ?>">
      	<?php for($i=0; $i<=24; $i++): ?>
      	  <option <?php echo  $i == intval($instance['grid_columns']) ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
      	<?php endfor; ?>
      	</select>
      </p>
      <p>
      	<label for="<?php echo $object->get_field_id( 'push_columns' ); ?>">Push Columns:</label>
      	<select id="<?php echo $object->get_field_id( 'push_columns' ); ?>" name="<?php echo $object->get_field_name( 'push_columns' ); ?>">
      	<?php for($i=0; $i<=24; $i++): ?>
      	  <option <?php echo  $i == $instance['push_columns'] ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
      	<?php endfor; ?>
      	</select>
      </p>
      <p>
      	<label for="<?php echo $object->get_field_id( 'pull_columns' ); ?>">Pull Columns:</label>
      	<select id="<?php echo $object->get_field_id( 'pull_columns' ); ?>" name="<?php echo $object->get_field_name( 'pull_columns' ); ?>">
      	<?php for($i=0; $i<=24; $i++): ?>
      	  <option <?php echo  $i == $instance['pull_columns'] ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
      	<?php endfor; ?>
      	</select>
      </p>
      <p>
      	<label for="<?php echo $object->get_field_id( 'suffix_columns' ); ?>">Suffix Columns:</label>
      	<select id="<?php echo $object->get_field_id( 'suffix_columns' ); ?>" name="<?php echo $object->get_field_name( 'suffix_columns' ); ?>">
      	<?php for($i=0; $i<=24; $i++): ?>
      	  <option <?php echo  $i == $instance['suffix_columns'] ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
      	<?php endfor; ?>
      	</select>
      </p>
      <p>
      	<label for="<?php echo $object->get_field_id( 'prefix_columns' ); ?>">Prefix Columns:</label>
      	<select id="<?php echo $object->get_field_id( 'prefix_columns' ); ?>" name="<?php echo $object->get_field_name( 'prefix_columns' ); ?>">
      	<?php for($i=0; $i<=24; $i++): ?>
      	  <option <?php echo  $i == $instance['prefix_columns'] ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
      	<?php endfor; ?>
      	</select>
      </p>
      <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['is_first_column'] == 'on', true) ?> id="<?php echo $object->get_field_id('is_first_column'); ?>" name="<?php echo $object->get_field_name('is_first_column'); ?>" />
        <label for="<?php echo $object->get_field_id('is_first_column'); ?>"><?php _e('First Column?'); ?></label><br />
        <input class="checkbox" type="checkbox" <?php checked($instance['is_last_column'] == 'on', true) ?> id="<?php echo $object->get_field_id('is_last_column'); ?>" name="<?php echo $object->get_field_name('is_last_column'); ?>" />
        <label for="<?php echo $object->get_field_id('is_last_column'); ?>"><?php _e('Last Column?'); ?></label><br />
      </p>
    </fieldset>
    <?php
  }
  
  function print_css_classes($obj,$instance) { $css_classes = isset($instance['css_classes']) ? $instance['css_classes'] : ""; ?>
    <p>
      <label for="<?php echo $obj->get_field_id('css_classes'); ?>"><?php _e('CSS Classes:'); ?></label>
      <input id="<?php echo $obj->get_field_id('css_classes'); ?>" name="<?php echo $obj->get_field_name('css_classes'); ?>" value="<?php echo $css_classes ?>" />
    </p>
  <?php }
  
  function print_styles_textarea($obj,$instance) { $styles = isset($instance['styles']) ? format_to_edit($instance['styles']) : ""; ?>
    <p><a href="#styles-fields" class="field-toggle">Show Styles Field</a></p>
    <fieldset class="styles-fields" style="display:none;">
      <p>
    	  <label for="<?php echo $obj->get_field_id('styles'); ?>"><?php _e('Styles:'); ?></label>
    	  <textarea class="widefat" rows="16" cols="20" id="<?php echo $obj->get_field_id('styles'); ?>" name="<?php echo $obj->get_field_name('styles'); ?>"><?php echo $styles; ?></textarea>
    	</p>
    </fieldset>
  <?php }

  function print_scripts_textarea($obj,$instance) { $scripts = isset($instance['scripts']) ? format_to_edit($instance['scripts']) : ""; ?>
    <p><a href="#script-fields" class="field-toggle">Show Script Field</a></p>
    <fieldset class="script-fields" style="display:none;">
      <p>
    	  <label for="<?php echo $obj->get_field_id('scripts'); ?>"><?php _e('Scripts:'); ?></label>
    	  <textarea class="widefat" rows="16" cols="20" id="<?php echo $obj->get_field_id('scripts'); ?>" name="<?php echo $obj->get_field_name('scripts'); ?>"><?php echo $scripts; ?></textarea>
    	</p>
    </fieldset>
  <?php }
    
  function print_scripts($id,$instance,$scripts=null) {
    global $hbgs_inline_scripts;
    
    $instance['scripts'] = isset($instance['scripts']) ? $instance['scripts'] : '';
    $scripts = $scripts ? $scripts : $instance['scripts'];
    $matches = array();
    preg_match_all('|<script.+src=["\'](.+)["\'].+<\/script>|',$scripts,$matches);
    
    $head_scripts = implode("','",$matches[1]);
    foreach($matches[0] as $match) {
      $scripts = str_replace($match,"",$scripts);
    }
    foreach($matches[1] as $match) {
      wp_enqueue_script(esc_attr($match),$match);
    }
    
    $scripts = preg_replace(array('/#widget-id/'),array('#'.$id), $scripts);
    if(!isset($hbgs_inline_scripts) || $scripts == '' || strpos($hbgs_inline_scripts, $scripts) === false) {
      echo $scripts;
    } else {
      echo '';
    }
  }
  
  function print_styles($id,$instance,$styles=null) { 
    $instance['styles'] = isset($instance['styles']) ? $instance['styles'] : '';
    $styles = $styles ? $styles : $instance['styles'];
    $matches = array();
    preg_match_all('|<link.+href=["\']([^ "\']+)["\'].+\/ *>|',$styles,$matches);
    
    foreach($matches[0] as $match) {
      $styles = str_replace($match,"",$styles);
    }
    foreach($matches[1] as $match) {
      wp_enqueue_style(esc_attr($match),$match);
    }
    $replacements = hbgs_color_replacements();
    $styles = str_replace($replacements['patterns'],$replacements['replacements'],$styles);
    
    echo preg_replace(array('/#widget-id/'),array('#'.$id), $styles);
  }
  
  function set_grid_preferences(&$instance,$new_instance) {
    $instance['grid_columns'] = intval($new_instance['grid_columns']);
  	$instance['prefix_columns'] = intval($new_instance['prefix_columns']);
  	$instance['suffix_columns'] = intval($new_instance['suffix_columns']);
  	$instance['pull_columns'] = intval($new_instance['pull_columns']);
  	$instance['push_columns'] = intval($new_instance['push_columns']);
    $instance['is_first_column'] = isset($new_instance['is_first_column']) ? $new_instance['is_first_column'] : false;
    $instance['is_last_column'] = isset($new_instance['is_last_column']) ? $new_instance['is_last_column'] : false;
  }
  
  function get_grid_defaults() {
    return array('grid_columns' => 0, 'is_first_column' => "on", 'is_last_column' => "on", 'pull_columns' => 0, 'push_columns' => 0, 'suffix_columns' => 0, 'prefix_columns' => 0);
  }
    
	function display_callback( $args, $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$this->_set( $widget_args['number'] );
		$instance = $this->get_settings();

		if ( array_key_exists( $this->number, $instance ) ) {
			$instance = $instance[$this->number];
			// filters the widget's settings, return false to stop displaying the widget
			
			$instance = apply_filters('widget_display_callback', $instance, $this, $args);
      $args['before_widget'] = $this->add_grid_classes($instance,$args['before_widget']);
      
			if ( false !== $instance )
				$this->widget($args, $instance);
		}
	}
	
	function add_grid_classes($instance,$before_widget,$extra_classes="") {
    $classes = ' ' . $this->get_grid_classes($instance) . ' ' . $extra_classes . ' ' . $this->id . ' ' . (isset($instance['css_classes']) ? $instance['css_classes'] : '');
    return preg_replace('/moreclasses/', $classes, $before_widget);
  }
  
  function get_grid_classes($instance) { 
    $instance = array_merge(array(
      'is_first_column' => '',
      'is_last_column' => '',
      'grid_columns' => '',
      'push_columns' => '',
      'pull_columns' => '',
      'prefix_columns' => '',
      'suffix_columns' => ''
    ),$instance);
    
    $is_first = $instance['is_first_column'] == 'on'; 
    $is_last = $instance['is_last_column'] == 'on';
    $output = '';

    $output .= ' clearfix ';
    $output .= ' grid_'.intval($instance['grid_columns']).' ';
    $output .= ' push_'.intval($instance['push_columns']).' ';
    $output .= ' pull_'.intval($instance['pull_columns']).' ';
    $output .= ' prefix_'.intval($instance['prefix_columns']).' ';
    $output .= ' suffix_'.intval($instance['suffix_columns']).' ';
    if($is_first) {
      $output .= ' alpha ';
    } 
    if($is_last) {
      $output .= ' omega ';
    }

    return $output;
  }
  
  function print_default_styles() {
    
  }
  
  function print_default_scripts() {
    
  }
}