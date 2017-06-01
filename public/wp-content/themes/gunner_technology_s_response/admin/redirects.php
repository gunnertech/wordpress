<?php

if (!isset($_GET['page']) || $_GET['page'] == hbgs_current_admin_page(__FILE__)) {
  add_action('admin_init', str_replace("-","_",hbgs_current_admin_page(__FILE__).'_init'));
}

add_action('admin_menu', 'hbgs_redirects_menu');

function hbgs_redirects_menu() {
  if (current_user_can( "delete_published_posts" )) {
    add_submenu_page('hbgs-settings','Content Redirects', 'Redirects', 'administrator', 'hbgs-redirects', 'hbgs_redirect_page');
  }
}

function hbgs_redirects_init() {
  wp_enqueue_script( "redirects", get_template_directory_uri().'/admin/js/redirects.js', array('admin') );
  
	register_setting( 'hbgs-redirects-group', 'hbgs_redirects', 'hbgs_save_redirects' );
}

function hbgs_save_redirects($redirects) {
  $redirects = is_string($redirects) ? json_decode($redirects) : $redirects;
  
  if(!$redirects) {
    wp_die( "We're sorry. There was a problem with your change. Please go back and try again.", "Error", array(
     "back_link" => true
    ));
  }
  
  return $redirects;
}

function hbgs_redirect_page() {  $hbgs_redirects_string = hbgs_as_string(get_option('hbgs_redirects')); $hbgs_redirects = hbgs_as_json(get_option('hbgs_redirects')); ?>
  <div class="wrap">
    <h2>HTML5 Boilerplated Grid System's Redirects</h2>
    <form method="post" action="options.php" class="hbgs-redirects">
      <fieldset>
        <?php settings_fields( 'hbgs-redirects-group' ); ?>
        <div class="redirect">
          <br />
          Redirect <input class="redirect-from" type="text" size="40" value="<?php echo $key ?>" /> to <input class="redirect-to" type="text" size="40" value="<?php echo $redirect ?>" />
          <input type="submit" value="Add Redirect" /><br /><br />
        </div>
        
        <h3>Existing Redirects</h3>
        <?php if(isset($hbgs_redirects)): foreach($hbgs_redirects as $key => $redirect): ?>
          <div class="redirect">
            Redirect <input size="40" class="redirect-from" type="text" value="<?php echo $key ?>" /> to <input class="redirect-to" type="text" size="40" value="<?php echo $redirect ?>" />
            <input type="button" class="remove-redirect" value="Remove Redirect" />
            <br /><br />
          </div>
        <?php endforeach; endif; ?>
        <input type="hidden" id="hbgs-redirects" name="hbgs_redirects" value="<?php echo esc_js($hbgs_redirects_string) ?>" />
      </fieldset>
    </form>
  </div>
<?php }