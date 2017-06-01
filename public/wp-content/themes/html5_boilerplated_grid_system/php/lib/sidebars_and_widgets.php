<?php

/*UTILITIES*/

function hbgs_render_styles($slug,$sidebar,$styles=null) {
  if($sidebar) {
    $styles = preg_replace(array('/#sidebar-id/'),array('#'.$slug), $sidebar->styles);
    $replacements = hbgs_color_replacements();
    $styles = str_replace($replacements['patterns'],$replacements['replacements'],$styles);
    $widget_ids = hbgs_widget_ids($slug);
  
    echo "\n/****$slug****/\n";
    echo $styles;
    hbgs_print_widget_styles($widget_ids);
    echo "\n/**** end $slug****/\n";
  } else {
    echo "\n/****$slug****/\n";
    hbgs_print_widget_styles(array($slug),$styles);
    echo "\n/**** end $slug****/\n";
  }
}

function hbgs_render_scripts($slug,$sidebar,$scripts=null,$overrides=false) {
  if($sidebar) {
    //GET ANY SCRIPTS THAT HAVE BEEN ADDED TO THE SIDEBAR
    $scripts = isset($sidebar->scripts) ? preg_replace(array('/#sidebar-id/'),array('#'.$slug), $sidebar->scripts) : '';
    
    $widget_ids = hbgs_widget_ids($slug);    

    echo $scripts;
    hbgs_print_widget_scripts($widget_ids);
  } else {
    hbgs_print_widget_scripts(array($slug),$scripts,$overrides);
  }
}

function hbgs_sidebar_areas() {
  return array("pre_header","pre_content","post_content","content_left","content_right","content_above","header","footer","post_content_body","reserve");
}

function hbgs_sidebar_as_json_object() {
  return hbgs_as_json(get_option('hbgs_sidebars'));
}

function hbgs_get_css_for_widget($id,$count) {
  $args = hbgs_get_widget_instance($id);
  ob_start();
	
	$args['obj']->print_default_styles();
	$args['obj']->print_styles($id,$args['instance']);
	
  $css = ob_get_contents();
  ob_end_clean();
  
  if(!$css){ $css = ''; }
  
  return preg_replace(array('/#widget-id/','/#'.$id.'/'),array('#'.$id.'-'.$count,'#'.$id.'-'.$count), $css);
}

function hbgs_get_scripts_for_widget($id) {
  $args = hbgs_get_widget_instance($id);
  ob_start();
	
	$args['obj']->print_default_scripts();
	$args['obj']->print_scripts($id,$args['instance']);
	
  $out = ob_get_contents();
  ob_end_clean();
  
  if(!$out){ $out = ''; }
  
  return preg_replace(array('/#widget-id/'),array('#'.$id), $out);
}


function hbgs_get_widget_instance($id) {
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

function hbgs_the_widget_instance_with_no_id($atts) {
  global $wp_widget_factory;

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
  the_widget($widget_name, $atts, array('widget_id'=>'arbitrary-instance-'.$w_id,
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

function hbgs_the_widget_instance($id,$echo=true,$overrides=null) {
  global $hbgs_inline_count, $hbgs_scripts, $hbgs_type_of_assets_to_return;
  
  $count_key = isset($hbgs_type_of_assets_to_return) ? $hbgs_type_of_assets_to_return : 'html';
  $args = hbgs_get_widget_instance($id);
  $obj = $args['obj'];
  if(!$args['obj']) {
    return false;
  }
  $out = '';
  $overrides = (array)$overrides;
  $unique_id = $id;
  
  if(isset($overrides['id'])) {
    $hbgs_inline_count[$count_key]++;
    $overrides['nocache'] = 1;
    $unique_id = ($id.'-'.$hbgs_inline_count[$count_key]);
    
    unset($overrides['id']);
    
    $args['instance']['styles'] = isset($args['instance']['styles']) ? preg_replace(array('/#widget-id/'),array('#'.$unique_id), $args['instance']['styles']) : '';
    $args['instance']['scripts'] = isset($args['instance']['scripts']) ? preg_replace(array('/#widget-id/'),array('#'.$unique_id), $args['instance']['scripts']) : '';
    
    $args['args'] = isset($args['args']) ? $args['args'] : array("before_widget" => "");
    $args['args']['before_widget'] = preg_replace("/id=\"".$id."\"/", 'id="'.$unique_id.'"', $args['args']['before_widget']);
    
    $filter = function($instance,$obj,$args) use ($overrides) {
      $instance = array_merge($instance,$overrides);
      remove_all_filters('widget_display_callback',HBGS_FILTER_SCOPE);
      
      return $instance;
    };
    
    add_filter('widget_display_callback',$filter,HBGS_FILTER_SCOPE,3); 
  }
  
  if($echo) {
    $obj->print_styles($id,$args['instance']);
    $obj->display_callback($args['args'], array('number' => $args['number'] ) );
  } else {
    ob_start();
    if($hbgs_type_of_assets_to_return == 'css') {
      hbgs_render_styles($unique_id,null,$args['instance']['styles']);
    } else if($hbgs_type_of_assets_to_return == 'js') {
      hbgs_render_scripts($unique_id,null,$args['instance']['scripts'],$overrides);
    }
    $out = ob_get_contents();
    ob_end_clean();
    
    ob_start();
    $obj->display_callback($args['args'], array('number' => $args['number'] ) );
    $html = ob_get_contents();
    ob_end_clean();
    
    if($hbgs_type_of_assets_to_return != 'css' && $hbgs_type_of_assets_to_return != 'js') {
      $out .= $html;
    }
        
    return $out;
  }
}

function hbgs_widget_ids($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];
	
	
  
  return $sidebars_widgets[$index];
}

function hbgs_print_sidebar($sidebar, $slug, $is_first, $is_last) { ?>
  <aside id="<?php echo $slug ?>" class="pull_<?php echo $sidebar->pull ?> push_<?php echo $sidebar->push ?> suffix_<?php echo $sidebar->suffix ?> prefix_<?php echo $sidebar->prefix ?>  clearfix grid_<?php echo $sidebar->columns ?>
    <?php if($is_first): ?>
      alpha
    <?php endif; if($is_last): ?>
      omega
    <?php endif; ?>
  ">
    <?php dynamic_sidebar($slug) ?>
  </aside>
<?php 
}

function hbgs_print_widget_styles($ids,$style=null) {
  global $wp_registered_sidebars, $wp_registered_widgets, $hbgs_type_of_assets_to_return;
  
  foreach ( (array) $ids as $id ) {
	  preg_match('/-(\d+$)/',$id,$matches);
	  
	  $number = $matches[1];
	  $real_id = $id;
	  
	  //IF IT DOESN'T EXIST, THAT MEANS IT'S AN OVERRIDDEN WIDGET. LET'S FIND THE REAL INSTANCE
	  if ( !isset($wp_registered_widgets[$id]) ) {
	    $parts = split( '-' , $id);
  	  $parts_count = count($parts);
  	  $number = $parts[$parts_count-2];
  	  
  	  preg_match('/(.+)-'.$number.'/',$id,$name);
      $real_id = $name[1].'-'.$number;
      
      if(!isset($wp_registered_widgets[$real_id])) {
        $real_id = preg_replace('/-\d$/',"",$real_id);
      }
	  }
    
		if ( !isset($wp_registered_widgets[$id]) && !isset($wp_registered_widgets[$real_id]) ) continue;
		
		$widget_object = isset($wp_registered_widgets[$id]) ? $wp_registered_widgets[$id]['callback'][0] : $wp_registered_widgets[$real_id]['callback'][0];
		
		if(is_string($widget_object)){
		  continue;
	  }
	  $args = hbgs_get_widget_instance($id);
    if(isset($args['obj']->id_base) && $args['obj']->id_base == 'grided-text') {
      $css = '';
      
      preg_match_all('|\[widget id=["\']([^ "\']+)["\'].*\]|',$args['instance']['text'],$widget_matches);
		  $hbgs_type_of_assets_to_return = 'css';
      foreach($widget_matches[0] as $key => $value) {
        $css .= do_shortcode($value);
      }
      $hbgs_type_of_assets_to_return = null;
      $replacements = hbgs_color_replacements();
      
      echo strip_tags(str_replace($replacements['patterns'],$replacements['replacements'],$css));
      
    }
	  
		$settings = $widget_object->get_settings();
    
    $instance = $settings[$number];
    
    if(method_exists($widget_object,'print_default_styles')) {
      $widget_object->print_default_styles();
    }
    if(method_exists($widget_object,'print_styles')) {
      $widget_object->print_styles($id,$instance,$style);
    }
	}
}

function hbgs_print_widget_scripts($ids,$script=null,$overrides=false) {
  global $wp_registered_sidebars, $wp_registered_widgets, $hbgs_inline_scripts, $hbgs_type_of_assets_to_return;
  
  foreach ( (array) $ids as $id ) {
	  preg_match('/-(\d+$)/',$id,$matches);
	  
	  $number = $matches[1];
	  $real_id = $id;
	  
	  //IF IT DOESN'T EXIST, THAT MEANS IT'S AN OVERRIDDEN WIDGET. LET'S FIND THE REAL INSTANCE
	  if ( !isset($wp_registered_widgets[$id]) ) {
	    $parts = split( '-' , $id);
  	  $parts_count = count($parts);
  	  $number = $parts[$parts_count-2];
  	  
  	  preg_match('/(.+)-'.$number.'/',$id,$name);
      $real_id = $name[1].'-'.$number;
      
      if(!isset($wp_registered_widgets[$real_id])) {
        $real_id = preg_replace('/-\d$/',"",$real_id);
      }
	  }
    
		if ( !isset($wp_registered_widgets[$id]) && !isset($wp_registered_widgets[$real_id]) ) continue;
		
		$widget_object = isset($wp_registered_widgets[$id]) ? $wp_registered_widgets[$id]['callback'][0] : $wp_registered_widgets[$real_id]['callback'][0];
		
		if(is_string($widget_object)){
		  continue;
	  }
    $args = hbgs_get_widget_instance($id);
    if(isset($args['obj']->id_base) && $args['obj']->id_base == 'grided-text') {
      
      preg_match_all('|\[widget id=["\']([^ "\']+)["\'].*\]|',$args['instance']['text'],$widget_matches);
      $hbgs_type_of_assets_to_return = 'js';
      foreach($widget_matches[0] as $key => $value) {
        $scripts = do_shortcode($value);
        if($scripts == '' || strpos($hbgs_inline_scripts, $scripts) === false) {
          $hbgs_inline_scripts .= $scripts;
        }
      }
      $hbgs_type_of_assets_to_return = null;
    }
	  
		$settings = $widget_object->get_settings();
    $instance = $settings[$number];
    
    if($overrides) {
      $instance = array_merge($instance,$overrides);
    }
    
    if(method_exists($widget_object,'print_default_scripts')) {
      $widget_object->print_default_scripts();
    }
    if(method_exists($widget_object,'print_scripts')) {
      $widget_object->print_scripts($id,$instance,$script);
    }
    
    
    
	}
}

function hbgs_find_sidebar($sidebars,$name) {
  $test = strtolower(preg_replace('/\W/',"_",$name));
  if(!is_array($sidebars)) {
    $sidebars = json_decode($sidebars);
  }
  foreach($sidebars as $sidebar) {
    if(strtolower(preg_replace('/\W/',"_",$sidebar->name)) == $test) {
      return $sidebar;
    }
  }
  return null;
}

function hbgs_matches_category($sidebar_categories_on_post,$sidebar_exclusions_on_post,$sidebar_categories_on_sidebar) {
  $sidebar_exclusions_on_post = is_array($sidebar_exclusions_on_post) ? $sidebar_exclusions_on_post : array();
  
  if(!is_array($sidebar_categories_on_post) || !is_array($sidebar_categories_on_sidebar)) {
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


function hbgs_widgets_for($area,$cb=false) {
  global $wp_query, $hbgs_meta, $post;
  
  $item_id = is_singular() ? $post->ID : isset($wp_query->queried_object_id) ? $wp_query->queried_object_id : false;
  
  if($item_id) {
    hbgs_add_default_taxonomy($item_id);
  }
  
  $sidebars = hbgs_sidebar_as_json_object();
  
  //BAIL: Theme hasn't been set up yet
  if(!$sidebars) { return; }
  
  $hbgs_meta = hbgs_get_meta(true);
  
  if(!isset($sidebars->$area)) {
    return true;
  }
  
  $area_sidebars = $sidebars->$area;
  $area_sidebars = !!$area_sidebars && !is_array($area_sidebars) ? json_decode($area_sidebars) : $area_sidebars;
  
  if(is_array($area_sidebars)) {
    usort($area_sidebars, "hbgs_sidebar_cmp"); 
    $categories = wp_get_object_terms($item_id,'sidebar_category');
    $exclusions = wp_get_object_terms($item_id,'sidebar_exclusion');
    
    if(empty($categories)) {
      $categories = array();
      $default_category_names = get_option('default_sidebar_categories');
       foreach($default_category_names as $dc) {
         $categories[] = get_term_by( 'name', preg_replace('/\W/','-',strtolower($dc)), 'sidebar_category');
       }
    }
    
    //_log($categories);
    
    foreach($area_sidebars as $sidebar) {
      if(!hbgs_matches_category($categories,$exclusions,$sidebar->categories)) {
        continue;
      }

      $slug = $area.'_'.strtolower(preg_replace('/\W/',"_",$sidebar->name));
      if ( is_active_sidebar( $slug ) ) { 
        if($cb) {
          $cb($slug,$sidebar);
        } else {
          hbgs_print_sidebar($sidebar,$slug,($sidebar->alpha=='on'),($sidebar->omega=='on'));
        }
      }
    }
  }
}

function hbgs_sidebar_cmp($a,$b) {
  if($a->position == $b->position){
    return 0;
  }
  return ($a->position < $b->position) ? -1 : 1;
}

/*** ACTIONS ****/

function hbgs_print_sidebar_scripts() { 
  global $hbgs_scripts, $hbgs_inline_scripts;
  
  $areas = hbgs_sidebar_areas(); 
  $footer_javascript = str_replace("null","",get_option("footer_javascript")); 
  $inline_js = preg_replace('|<script.+src=["\'](.+)["\'].+<\/script>|',"",$footer_javascript);
  
  ob_start();
	foreach($areas as $area) {
	  hbgs_widgets_for($area,"hbgs_render_scripts");
	}
  $inline_js = ob_get_contents();
  ob_end_clean();
  
  $hbgs_inline_scripts .= $inline_js;
}

function hbgs_print_sidebar_styles() { 
  $areas = hbgs_sidebar_areas(); 
  $replacements = hbgs_color_replacements();
  $css = get_option("header_css"); 
  $matches = array();
  
  preg_match_all('|<link.+href=["\']([^ "\']+)["\'].+\/ *>|',$css,$matches); //FIND ALL THE EXTERNAL CSS FILES
  foreach($matches[0] as $match) {
    $css = str_replace($match,"",$css);
  }
  
  foreach($matches[1] as $match) {
    wp_enqueue_style(esc_attr($match),$match); //ADD ALL THE EXTERNAL CSS FILES ONTO THE QUEUE
  }
?>
<style>
  <?php echo str_replace("null","",strip_tags(str_replace($replacements['patterns'],$replacements['replacements'],$css))) ?>
  <?php foreach($areas as $area): ?>
    <?php hbgs_widgets_for($area,"hbgs_render_styles") ?>
  <?php endforeach; ?>
</style>
<?php }