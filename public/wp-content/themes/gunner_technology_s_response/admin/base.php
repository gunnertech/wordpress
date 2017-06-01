<?php
  
if( !defined('DOING_AJAX') || !DOING_AJAX ) {
  require_once hbgs_theme_path() . 'admin/content_meta_boxes.php';
}

require_once hbgs_theme_path() . 'admin/settings.php';
require_once hbgs_theme_path() . 'admin/sidebars.php';
require_once hbgs_theme_path() . 'admin/redirects.php';
require_once hbgs_theme_path() . 'admin/content_types.php';

add_filter('admin_footer_text','hbgs_admin_footer_text'); //REMOVE DEFAULT WORDPRESS FOOTER AND REPLACE WITH GUNNER TECHNOLOGY

if(!defined('WP_ENV') || WP_ENV != 'development') {
  //RESTRICT ACCESS FOR MEDDLING CLIENTS
  add_action("admin_menu", 'hbgs_remove_menu_items');
  foreach(array("plugin-install","update-core","theme-editor","plugin-editor") as $blocked_page) {
    add_action("load-$blocked_page.php", 'hbgs_restrict_access');
  }
}

add_action('widgets_init', 'hbgs_remove_default_widgets'); //REMOVE DEFAULT WIDGETS. TODO: OPTION PAGE TO SELECT WHICH ONES TO REMOVE
add_action('admin_init', function(){

  //MAKE SURE WE'RE USING THE LOCAL JQUERY. ADMIN HAS PROBLEMS WITH CDN VERSION  
  wp_deregister_script( 'jquery' );
  
  wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', false, HBGS_JQUERY_VERSION);    
  wp_register_script( 'json', get_template_directory_uri().'/admin/js/json.js',array("jquery"),false,true);
  wp_register_script( 'admin', get_template_directory_uri().'/admin/js/base.js',array("json"),false,true);
  
  wp_enqueue_script( "admin" );
 
  //ADD CUSTOM IMAGE SIZES
  $custom_image_sizes = hbgs_as_string(get_option('custom_image_sizes')); 
  $custom_image_json = hbgs_as_json(get_option('custom_image_sizes'));

  if(is_array($custom_image_json)) {
    foreach($custom_image_json as $custom_image) {
      add_image_size( $custom_image->name, $custom_image->width, $custom_image->height, $custom_image->crop );
    }
  }

  register_taxonomy_for_object_type('category','page');
  register_taxonomy_for_object_type('post_tag','page');

  //GET RID OF ANNOYING UPGRADE MESSAGE
  //add_action('admin_notices', 'no_update_notification', 1);
  remove_action('admin_notices', 'update_nag', 3);

  //GIVE PAGES EXCERPTS
  add_post_type_support('page', 'excerpt');
});
add_action('admin_init','hbgs_do_something_to_posts'); //ADMIN PURPOSES: LETS YOU RUN SOMETHING ON ALL POSTS
add_action('admin_head', 'hbgs_admin_register_head'); //REPLACE DEFAULT LOGO WITH THE ONE FROM OUR OPTIONS MENU
add_action('wp_dashboard_setup', 'hbgs_remove_dashboard_widgets' ); //GET RID OF ALL DASHBOARD WIDGETS: TODO: CREATE OPTION PAGE FOR SPECIFYING WHICH ONES CAN STAY
//add_action('updated_option','hbgs_sanitize_option',10,3); //CLEAN UP OPTIONS BEFORE SAVING. BIGGEST PROBLEM IS WITH SITENAME, WHICH CHANGES IN LENGTH, BREAKING DB TRANSFERS
//add_action('added_option','hbgs_sanitize_option',10,2);
add_action("admin_head","hbgs_add_default_taxonomy",9);



/*UTILITIES*/
function hbgs_sanitize_option($option, $oldvalue, $_newvalue=null) {
  $siteurl = str_replace("/",'\/',preg_quote(get_option('siteurl')));
  $test_value = $_newvalue ? $_newvalue : $oldvalue;
  $pattern = '/'.$siteurl.'(.+)/';
  $new_value = $test_value;
  $value_changed = false;
  if(is_array($test_value)) {
    foreach($test_value as $key => $value) {
      if(is_string($value) && preg_match($pattern, $value)) {
        $new_value[$key] = preg_replace($pattern,'\\1',$value);
        $value_changed = true;
      } elseif($value == 'null') {
        $new_value[$key] = null;
        $value_changed = true;
      }
    }
  } else if($test_value && is_string($test_value) && preg_match($pattern, $test_value)) {
    $new_value = preg_replace($pattern,'\\1',$test_value);
    $value_changed = true;
  } elseif($test_value == 'null') {
    $new_value = null;
    $value_changed = true;
  }
  
  if($value_changed) {
    update_option($option,$new_value);
  }
}

function hbgs_user_has_cap($allcaps, $caps, $args) {
  if(($args[0] == 'edit_user' || $args[0] == 'delete_user') && $args[2]) {
    $author_data = new WP_User( $args[2] );
    $allcaps['edit_users'] = !in_array("administrator",$author_data->roles);
    $allcaps['delete_users'] = !in_array("administrator",$author_data->roles);
  }
  
  return $allcaps;
}
