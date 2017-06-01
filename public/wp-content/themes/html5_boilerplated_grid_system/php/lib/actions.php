<?php

function hbgs_add_default_taxonomy($post_id=null) {
  global $post;
  
  $id = !!$post_id ? $post_id : (isset($post) ? $post->ID : null);
  
  $sidebars = hbgs_sidebar_as_json_object();
  
  if(!!$id) {
    if(count(wp_get_object_terms($id,'sidebar_category')) === 0) {
      $scs = get_option('default_sidebar_categories',array("Default"));
      
      $scs_slugs = array();
      foreach($scs as $sc) {
        $scs_slugs[] = strtolower(preg_replace('/\W/',"-",$sc));
      }
      
      wp_set_object_terms( $id, $scs_slugs, 'sidebar_category', false );
    }
  }
  
}

function hbgs_do_something_to_posts() {
  return false;
  query_posts('posts_per_page=-1&post_type=any');
  
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();
      $my_post = array();
      $my_post['ID'] = get_the_ID();
      $my_post['post_author'] = 5;
      // $my_post['post_status'] = 'draft';
      
      
      // $hbgs_meta = get_post_meta(get_the_ID(),'_hbgs_meta',TRUE);
      // if(isset($hbgs_meta['content_columns']) && $hbgs_meta['content_columns'] == '17') {
      //   $hbgs_meta['content_columns'] = '16';
      //   update_post_meta(get_the_ID(), '_hbgs_meta', $hbgs_meta);
      // }
      
      wp_update_post( $my_post );
    }
  }
  
  wp_reset_query();
}


function hbgs_create_sidebar_taxonomies() {
  
  $valid_post_types = array_filter(get_post_types(),function($post_type) {
	  return !in_array($post_type,array('mediapage','attachement','revision','nav_menu_item'));
	});

	$labels = array(
		'name' => _x( 'Sidebar Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Sidebar Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Sidebar Categories' ),
		'all_items' => __( 'All Sidebar Categories' ),
		'parent_item' => __( 'Parent Sidebar Category' ),
		'parent_item_colon' => __( 'Parent Sidebar Category:' ),
		'edit_item' => __( 'Edit Sidebar Category' ),
		'update_item' => __( 'Update Sidebar Category' ),
		'add_new_item' => __( 'Add New Sidebar Category' ),
		'new_item_name' => __( 'New Sidebar Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
    'add_or_remove_items' => __( 'Add or remove sidebar categories' ),
    'choose_from_most_used' => __( 'Choose from the most sidebar categories'),
    'menu_name' => false
	);

	register_taxonomy( 'sidebar_category', $valid_post_types, array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'query_var' => false,
		'rewrite' => false
	));
	
	$labels = array(
		'name' => _x( 'Sidebar Exclusions', 'taxonomy general name' ),
		'singular_name' => _x( 'Sidebar Exclusion', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Sidebar Exclusions' ),
		'all_items' => __( 'All Sidebar Exlcusions' ),
		'parent_item' => __( 'Parent Sidebar Exclusion' ),
		'parent_item_colon' => __( 'Parent Sidebar Exclusion:' ),
		'edit_item' => __( 'Edit Sidebar Exclusion' ),
		'update_item' => __( 'Update Sidebar Exclusion' ),
		'add_new_item' => __( 'Add New Sidebar Exclusion' ),
		'new_item_name' => __( 'New Sidebar Exclusion Name' ),
		'separate_items_with_commas' => __( 'Separate exclusions with commas' ),
    'add_or_remove_items' => __( 'Add or remove sidebar exclusions' ),
    'choose_from_most_used' => __( 'Choose from the most sidebar exclusions'),
    'menu_name' => false
	);
	

	register_taxonomy( 'sidebar_exclusion', $valid_post_types, array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'query_var' => false,
		'rewrite' => false
	));
	
	$scs = get_option('default_sidebar_categories',array("Default"));
	
	foreach($scs as $sc) {
	  wp_insert_term( $sc, 'sidebar_category');
	}
}

function hbgs_insert_custom_script() {
  global $hbgs_scripts, $hbgs_inline_scripts, $hbgs_type_of_assets_to_return;
	if (is_page() || is_single()) {
		if (have_posts()) : while (have_posts()) : the_post();
		  $widget_matches = array();
		  $script = '';
		  
		  preg_match_all('|\[widget id=["\']([^ "\']+)["\'].*\]|',get_the_content(),$widget_matches);
      $hbgs_type_of_assets_to_return = 'js';
      foreach($widget_matches[0] as $key => $value) {
        $s = do_shortcode($value);
        if($s == '' || strpos($hbgs_inline_scripts, $s) === false) {
          $script .= $s;
        }
      }
      $hbgs_type_of_assets_to_return = null;
            
		  $script .= get_post_meta(get_the_ID(), '_custom_script', true);
		  $matches = array();
		  preg_match_all('|<script.+src=["\'](.+)["\'].+<\/script>|',$script,$matches);

      $head_scripts = implode("','",$matches[1]);
      foreach($matches[0] as $match) {
        $scripts = str_replace($match,"",$scripts);
      }
      foreach($matches[1] as $match) {
        wp_enqueue_script(esc_attr($match),$match);
      }
      $allowed_tags = '<b><i><sup><sub><em><strong><u><br><p><div><section><aside><article><h1><h2><h3><h4><h5><h6>';
      $hbgs_inline_scripts .= strip_tags($script,$allowed_tags);
		endwhile; endif;
		rewind_posts();
	}
}

function hbgs_insert_custom_css() {
  global $hbgs_type_of_assets_to_return;
  
	if (is_page() || is_single()) {
	  $replacements = hbgs_color_replacements();
		if (have_posts()) : while (have_posts()) : the_post();
		  $widget_matches = array();
		  $css = '';
		  
		  preg_match_all('|\[widget id=["\']([^ "\']+)["\'].*\]|',get_the_content(),$widget_matches);
		  $hbgs_type_of_assets_to_return = 'css';
      foreach($widget_matches[0] as $key => $value) {
        $css .= do_shortcode($value);
      }
      $hbgs_type_of_assets_to_return = null;		  
      
		  $css .= get_post_meta(get_the_ID(), '_custom_css', true);
		  $matches = array();
		  preg_match_all('|<link.+href=["\']([^ "\']+)["\'].+\/ *>|',$css,$matches);
		  foreach($matches[0] as $match) {
        $css = str_replace($match,"",$css);
      }
      foreach($matches[1] as $match) {
        wp_enqueue_style(esc_attr($match),$match);
      }
			echo '<style>'.strip_tags(str_replace($replacements['patterns'],$replacements['replacements'],$css)).'</style>';
		endwhile; endif;
		rewind_posts();
	}
}

function hbgs_add_custom_post_types_to_category_query($args) {
  if(isset($args->query_vars['category_name'])) {
    $args->set_query_var('post_type','any');
  }
}

$hbgs_scripts = false;
function hbgs_wp_print_head_scripts() {
	if ( ! did_action('wp_print_scripts') )
		do_action('wp_print_scripts');

	global $wp_scripts, $hbgs_scripts, $hbgs_inline_scripts;

	if ( !is_a($wp_scripts, 'WP_Scripts') )
		return array(); // no need to run if nothing is queued
	ob_start();
	print_head_scripts();
  echo get_option("footer_javascript");
	print_footer_scripts();
	
	
  $output = ob_get_contents();
  ob_end_clean();
    
  $matches = array();
  preg_match_all('|<script.+src=["\'](.+)["\'].+<\/script>|',$output,$matches);
  $output = preg_replace('|<script.+src=["\'](.+)["\'].+<\/script>|',"",$output);
  
  $hbgs_scripts = implode("','",$matches[1]);
  
  if(GT_CDN) {
    $site_domain = str_replace("https://","",str_replace("http://", "", get_option('siteurl')));
    $hbgs_scripts = preg_replace('|'.$site_domain.'|',GT_CDN,$hbgs_scripts);
    $hbgs_scripts = preg_replace('/'.GT_CDN.'\/index.php/',$site_domain.'/index.php',$hbgs_scripts);
  }
  
  if(defined('GT_ASSET_VERSION')) {
    $hbgs_scripts = preg_replace('|\.js?|',".js?v=".GT_ASSET_VERSION.'&',$hbgs_scripts);
  }
  
  if(GT_CDN) {
    $hbgs_scripts = preg_replace('|\.js\?|',".js.gzip?",$hbgs_scripts);
  }
  
  if(gt_is_ssl()) {
    $hbgs_scripts = preg_replace('/http:\/\//',"https://",$hbgs_scripts);
    $hbgs_scripts = str_replace("https://".GT_CDN."/index.php?wpsc_user_dynamic_js","https://{$site_domain}/index.php?wpsc_user_dynamic_js",$hbgs_scripts);
    $hbgs_scripts = str_replace("https://".GT_CDN.":443/index.php?wpsc_user_dynamic_js","/index.php?wpsc_user_dynamic_js",$hbgs_scripts);
    $hbgs_scripts = str_replace("http://{$site_domain}/index.php?wpsc_user_dynamic_js","https://{$site_domain}/index.php?wpsc_user_dynamic_js",$hbgs_scripts);
    $hbgs_scripts = preg_replace('/http:\/\//',"https://",$hbgs_scripts);
  }
  
  
  
  if($hbgs_scripts) {
    $allowed_tags = '<b><i><sup><sub><em><strong><u><br><p><div><section><aside><article><h1><h2><h3><h4><h5><h6>';
    $hbgs_inline_scripts = strip_tags($hbgs_inline_scripts,$allowed_tags);
    $output = preg_replace('/<script type=\'text\/javascript\'>/',"",$output);
    $output = preg_replace('/<\/script>/',"",$output);
    $hbgs_scripts = preg_replace('/&#038;/','&amp;',$hbgs_scripts);
    
    $localized_scripts = array();
    preg_match_all( '/var .+ = \{.+\};/' , $output, $localized_scripts);
    
    $pre_load_scripts = isset($localized_scripts[0]) && is_array($localized_scripts[0]) ? implode("\n",$localized_scripts[0]) : "";
    
    $output = preg_replace('/var (.+) = {/',"window.$1 ={",$output);

    $new_output = "<script>".$pre_load_scripts."Modernizr.load({test: Modernizr.hbgs_loaded, nope:['$hbgs_scripts'], complete:function(){ Modernizr.hbgs_loaded = true; (function($){ ".$hbgs_inline_scripts.trim($output)."}(jQuery)) }});</script>";

  } else {
    $new_output = "";
  }
  
  echo $new_output;
  
	return $wp_scripts->done;
}

function hbgs_possibly_redirect() {
  $hbgs_meta = hbgs_get_meta();
  
  if(array_key_exists('redirect',$hbgs_meta)){ 
    wp_redirect(clean_url($hbgs_meta['redirect']), 301); 
  } else {
    $hbgs_redirects = hbgs_as_json(get_option('hbgs_redirects'));
    $path = $_SERVER["REQUEST_URI"];
    if(isset($hbgs_redirects) && is_object($hbgs_redirects) && property_exists ( $hbgs_redirects, $path )) {
      wp_redirect(clean_url($hbgs_redirects->$path), 301); 
    }
  }
}

function hbgs_widgets_init() {
  $sidebars = hbgs_sidebar_as_json_object();
  $sidebarString = json_encode($sidebars);
  $areas = hbgs_sidebar_areas();
    
  if(!!$sidebars) {
    foreach($sidebars as $key => $value) {
      $area_name = $name = ucwords(str_replace("_"," ",$key));
      
      if(!is_array($value)) {
        $value = json_decode($value);
      }
      
      if(!$value) continue;
      
      usort($value, "hbgs_sidebar_cmp"); 
      
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
  
  $files = hbgs_directory_to_array(hbgs_theme_path().'php/classes/Hbgs/Widgets',true);

  foreach($files as $file) {
    require_once $file;
    $class_name = str_replace(".php","",str_replace("/","_",str_replace(hbgs_theme_path().'php/classes/',"",$file)));
    
    register_widget( $class_name );
  }
}

function hbgs_header_style() {
  $src = get_header_image();
  if(GT_CDN) {
    $src = str_replace(get_option('siteurl'),"http://".GT_CDN,$src);
  }
  
?>
<style>
  header>hgroup {
    background-image: url(<?php echo $src; ?>);
  }
  
  header>hgroup,
  header>hgroup a {
    height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
  }
</style>
<?php }


function hbgs_background_cb() {
	$background = get_background_image();
	if(GT_CDN) {
    $background = str_replace(get_option('siteurl'),"http://".GT_CDN,$background);
  }
	$color = get_background_color();
	if ( ! $background && ! $color )
		return;

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = " background-image: url('$background');";

		$repeat = get_theme_mod( 'background_repeat', 'repeat' );
		if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
			$repeat = 'repeat';
		$repeat = " background-repeat: $repeat;";

		$position = get_theme_mod( 'background_position_x', 'left' );
		if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
			$position = 'left';
		$position = " background-position: top $position;";

		$attachment = get_theme_mod( 'background_attachment', 'scroll' );
		if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
			$attachment = 'scroll';
		$attachment = " background-attachment: $attachment;";

		$style .= $image . $repeat . $position . $attachment;
	}
?>
<style>
html { <?php echo trim( $style ); ?> }
</style>
<?php
}

function hbgs_setup() {
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'automatic-feed-links' );
  
  add_custom_background('hbgs_background_cb');
  
  define('GUNNER_TECHNOLOGY', 'yup');
  define('HEADER_TEXTCOLOR', 'ffffff');
  define('HEADER_IMAGE', get_bloginfo('stylesheet_directory') . '/images/header.png');
  define('HEADER_IMAGE_WIDTH', apply_filters('hbgs_header_image_width', get_option('header_image_width',950)));
	define('HEADER_IMAGE_HEIGHT', apply_filters('hbgs_header_image_height', get_option('header_image_height',200)));
	define('BACKGROUND_IMAGE', get_bloginfo('stylesheet_directory') . '/images/bg.jpg');
  define('BACKGROUND_COLOR', get_option('html_background_color',"082135"));
  
  add_custom_image_header('hbgs_header_style', 'hbgs_admin_header_style');
  
  $repeat = get_theme_mod( 'background_repeat', 'no-repeat' );
	$position = get_theme_mod( 'background_position_x', 'center' );
  
  set_theme_mod( 'background_position_x', $position );
  set_theme_mod( 'background_repeat', $repeat );
  
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'hbgs' ),
		'footer' => __( 'Footer Navigation', 'hbgs' )
	) );
	
	$dynamic_nav_menus = hbgs_as_json(get_option('dynamic_nav_menus'));
	
	if(is_array($dynamic_nav_menus)) {
	  foreach($dynamic_nav_menus as $nav) {
  	  register_nav_menu($nav[0],$nav[1]);
  	}
	}
	
}

function hbgs_configure_content_column() {
  global $content_column;
  
  $content_column = is_array($content_column) ? $content_column : array();

  $hbgs_meta = hbgs_get_meta(true);
  if(!isset($content_column['count'])) {
    $content_column['count'] = (isset($hbgs_meta['content_columns']) ? $hbgs_meta['content_columns'] : get_option("default_content_columns",24));
  }
  $content_column['prefix'] = (isset($hbgs_meta['content_column_prefix']) ? $hbgs_meta['content_column_prefix'] : get_option("default_content_column_prefix",0));
  $content_column['suffix'] = (isset($hbgs_meta['content_column_suffix']) ? $hbgs_meta['content_column_suffix'] : get_option("default_content_column_suffix",0));
  $content_column['pull'] = (isset($hbgs_meta['content_column_pull']) ? $hbgs_meta['content_column_pull'] : get_option("default_content_column_pull",0));
  $content_column['push'] = (isset($hbgs_meta['content_column_push']) ? $hbgs_meta['content_column_push'] : get_option("default_content_column_push",0));
  $not_last = $content_column['count'] < 24;
  $not_first = (isset($hbgs_meta['content_column_not_first']) ? $hbgs_meta['content_column_not_first'] : get_option("default_content_column_not_first",0));
  $content_column['extra'] = "";
  $content_column['extra'] .= $not_first == 'on' && intval($content_column['count']) != 24 ? '' : ' alpha ' ;
  $content_column['extra'] .= $not_last ? ' alpha ' : '';
}


function hbgs_init() {
  // if(!WP_DEBUG && !is_admin()) { //IF WE'RE NOT DEBUGGING AND IT'S NOT THE ADMIN PANEL, LET'S GRAB FROM THE CDN
  //   wp_deregister_script( 'jquery' );
  //   wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/'.HBGS_JQUERY_VERSION.'/jquery.min.js',array(),false,true);
  // }
  
  hbgs_configure_custom_post_types();
}

function hbgs_login_logo() {
  echo '<style type="text/css">
    body h1 a { 
      background-image:url('.get_option("admin_logo_large_url","/wp-content/themes/html5_boilerplated_grid_system/images/login-logo.png").') !important; 
      height: '.get_option("admin_logo_large_height",128).'px !important; 
      width: '.get_option("admin_logo_large_width",326).'px !important; 
    }
  </style>';
}


/* ADMIN ACTIONS */

function hbgs_admin_register_head() {
  $siteurl = get_option('siteurl');
  // $url = $siteurl . '/wp-admin/css/widgets.css';
  // echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
  
  echo '<style type="text/css">
    body img#header-logo { background-image:url('.get_option('admin_logo_small_url',"/wp-content/themes/html5_boilerplated_grid_system/images/admin-header-logo.png").') !important; }
    #mapp_metabox > div > div:first-child {
      display: none;
    }
  </style>';
}

function hbgs_remove_default_widgets() {
  unregister_widget( 'WP_Widget_Pages' );
	unregister_widget( 'WP_Widget_Calendar' );
  // unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Links' );
	unregister_widget( 'WP_Widget_Categories' );
	unregister_widget( 'WP_Widget_Recent_Posts' );
  unregister_widget( 'WP_Widget_Search' );
  // unregister_widget( 'WP_Widget_Tag_Cloud' );
	unregister_widget( 'WP_Widget_Meta' );
  unregister_widget( 'WP_Widget_Recent_Comments' );
  unregister_widget( 'WP_Widget_RSS' );
  unregister_widget( 'WP_Widget_Text' );
}

function hbgs_remove_dashboard_widgets() {
	global $wp_meta_boxes;
 
	// Main Modules
	// Remove the right now widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	// Remove the recent comments widget
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	// Remove the incoming links widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	// Remove the plugins widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
 
	// Secondary Modules
	// Remove the quickpress widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	// Remove the recent drafts widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	// Remove the primary feed widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	// Remove the secondary feed widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}

function hbgs_restrict_access() {
  wp_die( __( 'You do not have sufficient permissions to update this page.' ),'Access Denied',array('response' => 500,'back_link' => true) );
}

function hbgs_remove_menu_items() {
  global $menu, $submenu;
  
  unset($submenu['plugins.php'][10]);
  unset($submenu['plugins.php'][15]);
}


function hbgs_admin_header_style() { ?>
  <style>
    #headimg {
      width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
      height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
    }
  </style>
<?php }