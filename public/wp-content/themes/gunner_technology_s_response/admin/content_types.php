<?php

if (!isset($_GET['page']) || $_GET['page'] == hbgs_current_admin_page(__FILE__)) {  
  add_action('admin_init', str_replace("-","_",hbgs_current_admin_page(__FILE__).'_init'));
}

add_action('admin_menu', 'hbgs_content_types_menu');

function hbgs_content_types_menu() {
  if (current_user_can( "delete_published_posts" )) {
    add_submenu_page('hbgs-settings','Content Types', 'Content Types', 'administrator', 'hbgs-content-types', 'hbgs_content_types_page');
  }
}

function hbgs_content_types_init() {  
  wp_enqueue_script( "content_types", get_template_directory_uri().'/admin/js/content_types.js', array('admin') );
  
	register_setting( 'hbgs-content-types-group', 'hbgs_content_types', 'hbgs_save_content_types' );
}

function hbgs_save_content_types($content_types) {
  $content_types = is_string($content_types) ? json_decode($content_types) : $content_types;
  
  if($content_types === null) {
    wp_die( "We're sorry. There was a problem with your change. Please go back and try again.", "Error", array(
     "back_link" => true
    ));
  }
  
  return $content_types;
}

function hbgs_content_types_page() {  
  $hbgs_content_types_string = hbgs_as_string(get_option('hbgs_content_types')); 
  $hbgs_content_types = hbgs_as_json(get_option('hbgs_content_types'));
  
  $hbgs_content_types = $hbgs_content_types ? $hbgs_content_types : array();
  
  $custom_post_types = hbgs_directory_to_array(hbgs_theme_path().'php/classes/Hbgs/Custom_Post_Types',true);
  ?>
  <div class="wrap">
    <h2>HTML5 Boilerplated Grid System's Content Types</h2>
    <form method="post" action="options.php" class="hbgs-content-types">
      <fieldset>
        <?php settings_fields( 'hbgs-content-types-group' ); ?>
        <p>Below are the available Custom Content Types for this site. Once that are checked are activated. <a href="">Learn more about the Custom Content Types</a>.</p>
        <?php foreach($custom_post_types as $custom_post_type): $value = strtolower(str_replace(array(".php"),array(""),basename($custom_post_type))); ?>
          <p><input type="checkbox" value="<?php echo $value ?>" <?php checked(in_array($value, $hbgs_content_types)) ?> /> <?php echo str_replace(array(".php","_"),array(""," "),basename($custom_post_type)) ?></p>
        <?php endforeach; ?>
        <input type="hidden" id="hbgs-content-types" name="hbgs_content_types" value="<?php echo esc_js($hbgs_content_types_string) ?>" />
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </fieldset>
    </form>
  </div>
<?php }