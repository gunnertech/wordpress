<?php
//_log("HIII");
if (!isset($_GET['page']) || $_GET['page'] == hbgs_current_admin_page(__FILE__)) {
  add_action('admin_init', str_replace("-","_",hbgs_current_admin_page(__FILE__).'_init'));
}

add_action('admin_menu', 'hbgs_settings_menu');

function hbgs_settings_menu() {
  if (current_user_can( "delete_published_posts" )) {
    add_menu_page('Theme Settings', 'HBGS Settings', 'administrator', "hbgs-settings", 'hbgs_settings_page',get_template_directory_uri().'/images/hbgs-settings.png');
  }
}

function hbgs_settings_init() {
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');
  wp_enqueue_script( "settings", get_template_directory_uri().'/admin/js/settings.js', array('admin') );
  
  wp_enqueue_style('thickbox');
  
  register_setting( 'hbgs-settings', 'favicon_url' );
  register_setting( 'hbgs-settings', 'admin_logo_large_url' );
  register_setting( 'hbgs-settings', 'admin_logo_large_width' );
  register_setting( 'hbgs-settings', 'admin_logo_large_height' );
  register_setting( 'hbgs-settings', 'admin_logo_small_url' );
  register_setting( 'hbgs-settings', 'custom_fonts' );
  register_setting( 'hbgs-settings', 'footer_javascript' );
  register_setting( 'hbgs-settings', 'header_css' );
  register_setting( 'hbgs-settings', 'meta_tags' );
  register_setting( 'hbgs-settings', 'default_sidebar_categories', function($categories_as_string) {
    $temp_categories = explode(",",$categories_as_string);
    $categories = array();
    
    foreach($temp_categories as $cat) {
      $categories[] = trim($cat);
    }
    
    return $categories;
  });
  register_setting( 'hbgs-settings', 'default_page_header_image_src' );
  register_setting( 'hbgs-settings', 'default_page_header_image_height' );
  register_setting( 'hbgs-settings', 'inquiry_form_code' );
  register_setting( 'hbgs-settings', 'purchase_form_code' );
	register_setting( 'hbgs-settings', 'header_image_height' );
	register_setting( 'hbgs-settings', 'header_image_width' );
	register_setting( 'hbgs-settings', 'grid_size' );
	register_setting( 'hbgs-settings', 'default_content_columns' );
	register_setting( 'hbgs-settings', 'default_content_column_prefix' );
	register_setting( 'hbgs-settings', 'default_content_column_suffix' );
	register_setting( 'hbgs-settings', 'custom_image_sizes' );
	register_setting( 'hbgs-settings', 'hbgs_colors' );
	register_setting( 'hbgs-settings', 'image_size_list_view' );
	register_setting( 'hbgs-settings', 'image_size_detailed_view' );
}


function hbgs_settings_page() { ?>
  <div class="wrap">
    <h2>HTML5 Boilerplated Grid System's Options</h2>
    <form method="post" action="options.php" class="hbgs-options">
      <?php settings_fields( 'hbgs-settings' ); ?>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Custom Image Sizes</a> <small><a target="_blank" href="http://www.youtube.com/watch?v=FBjQc6Cnr5Y">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">  
        <caption>Below, you can manage the size of the images the CMS will create when you upload a picture. These sizes can then be used in widgets.</caption>
        <tr valign="top">
          <td>
            <?php $custom_image_json = hbgs_as_json(get_option('custom_image_sizes')); ?>
            Name: <input type="text" id="custom-image-name" />&nbsp;&nbsp;
            Width: <input size="4" type="text" id="custom-image-width" />&nbsp;&nbsp;
            Height: <input size="4" type="text" id="custom-image-height" />&nbsp;&nbsp;
            Force Crop? <input type="checkbox" id="custom-image-crop" />&nbsp;&nbsp;
            <a id="add-custom-image" href="#">Add Image Size</a>
            <br /><br />
            <input type="hidden" name="custom_image_sizes" id="custom-image-sizes" value="<?php echo esc_js(json_encode($custom_image_json)) ?>" /> 
            <div id="existing-image-sizes">Existing Image Sizes:</div>
            <?php if(is_array($custom_image_json)): foreach($custom_image_json as $custom_image): ?>
              <div class="image-row">
                <strong class="custom-image-name"><?php echo $custom_image->name ?></strong>:&nbsp;&nbsp;
                Width: <?php echo $custom_image->width ?>&nbsp;&nbsp;Height: <?php echo $custom_image->height ?>&nbsp;&nbsp;
                Crop? <?php echo $custom_image->crop ? 'yes' : 'no' ?>&nbsp;&nbsp;
                <a class="remove-custom-image" href="#">Delete Image Size</a>
              </div>
            <?php endforeach; endif; ?>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Color Palatte <small><a target="_blank" href="http://www.youtube.com/watch?v=XPufOLvbYnU">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <tr valign="top">
          <td>
            <?php 
              $colors_json = hbgs_get_colors(true);
              $colors = json_encode($colors_json);
            ?>
            Name: <input type="text" id="hbgs-color-name" />&nbsp;&nbsp;
            Value: <input size="10" type="text" id="hbgs-color-value" />&nbsp;&nbsp;
            <a id="add-hbgs-color" href="#">Add Color</a>
            <br /><br />
            <input type="hidden" name="hbgs_colors" id="hbgs-colors" value="<?php echo esc_js($colors) ?>" /> 
            <?php if(is_array($colors_json)): foreach($colors_json as $color): ?>
              <div id="<?php echo $color->name ?>" class="color-row">
                <strong class="color-name"><?php echo str_replace("color-palatte-","",$color->name) ?></strong>:&nbsp;&nbsp;
                Value: <input class="color-value" value="<?php echo esc_js($color->value) ?>" />&nbsp;&nbsp;
                <div class="color-sample" style="display:inline-block; width:25px; height:15px; background-color: <?php echo esc_js($color->value) ?>"></div>
                <a class="update-color" id="<?php echo $color->name ?>-update" href="#">Update Color</a>&nbsp;&nbsp;
                <a class="remove-color" href="#">Delete Color</a>
                <br /><br />
              </div>
            <?php endforeach; endif; ?>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Favicon</a> <small><a target="_blank" href="http://www.youtube.com/watch?v=rQJAUSwTTe0">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <tr valign="top">
          <td>
            <input class="upload_image_value the-value" type="text" size="36" name="favicon_url" value="<?php echo get_option('favicon_url'); ?>" />
            <input class="upload_image_button" type="button" value="Upload Image" />
            <br />Enter a URL or Click "Upload an Image"
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Admin Skin Options</a></h3>
      <table class="form-table" style="display:none;">
        <caption>Use these options to control the look and feel of your Admin Dashboard</caption>
        <tr valign="top">
          <th scope="row">Admin Logo Large URL</th>
          <td>
            <input class="upload_image_value the-value" type="text" size="36" name="admin_logo_large_url" value="<?php echo get_option('admin_logo_large_url',get_template_directory_uri().'/images/login-logo.png'); ?>" />
            <input class="upload_image_button" type="button" value="Upload Image" />
            <br />Enter a URL or Click "Upload an Image"
            <input type="hidden" class="default-value" value="<?php echo get_template_directory_uri().'/images/login-logo.png' ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Admin Logo Large Width</th>
          <td>
            <input type="text" class="the-value" name="admin_logo_large_width" value="<?php echo get_option('admin_logo_large_width',326); ?>" />
            <input type="hidden" class="default-value" value="<?php echo 326 ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Admin Logo Large Height</th>
          <td>
            <input type="text" class="the-value" name="admin_logo_large_height" value="<?php echo get_option('admin_logo_large_height',128); ?>" />
            <input type="hidden" class="default-value" value="<?php echo 128 ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>      
        <tr valign="top">
          <th scope="row">Admin Logo Small URL</th>
          <td>
            <input class="upload_image_value the-value" type="text" size="36" name="admin_logo_small_url" value="<?php echo get_option('admin_logo_small_url',get_template_directory_uri().'/images/admin-header-logo.png'); ?>" />
            <input class="upload_image_button" type="button" value="Upload Image" />
            <br />Enter a URL or Click "Upload an Image"
            <input type="hidden" class="default-value" value="<?php echo get_template_directory_uri().'/images/admin-header-logo.png' ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Header Options</a> <small><a target="_blank" href="http://www.youtube.com/watch?v=HWYYpzKFdJ4">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>These options allow you to change the size of the header image that appears on the site and which you managed <a target="_blank" href="/wp-admin/themes.php?page=custom-header">from here</a>.</caption>
        <tr valign="top">
          <th scope="row">Header Image Width</th>
          <td>
            <input type="text" class="the-value" name="header_image_width" value="<?php echo get_option('header_image_width',964); ?>" />
            <input type="hidden" class="default-value" value="<?php echo '950' ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Header Image Height</th>
          <td>
            <input type="text" class="the-value" name="header_image_height" value="<?php echo get_option('header_image_height',176); ?>" />
            <input type="hidden" class="default-value" value="<?php echo '200' ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Main Header Image</th>
          <td>
            A huge, custom header image with your logo will give your site a unique brand and feel. <a target="_blank" href="http://www.youtube.com/watch?v=HWYYpzKFdJ4">Check out how it's done in this video</a>, and then <a target="_blank" href="/wp-admin/themes.php?page=custom-header">head over to the uploader</a>  and add a new header image for your site!
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>
      
      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Sidebar Categories <small><a target="_blank" href="http://www.youtube.com/watch?v=HNAC-TxlqdI">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>These options allow you to set default sidebar categories for all new conent.</caption>
        <tr valign="top">
          <th scope="row">Default Sidebar Categories (comma separated)</th>
          <td><textarea rows="10" cols="58" name="default_sidebar_categories"><?php echo implode(",",get_option('default_sidebar_categories',array("Default"))) ?></textarea></td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Grid Options <small><a target="_blank" href="http://www.youtube.com/watch?v=HNAC-TxlqdI">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>These options allow you to control the grid you use and the default column layout for your site.</caption>
        <?php $grid_size = get_option('grid_size',"984_24_10_10.css"); ?>
        <tr valign="top">
          <th scope="row">Grid Size</th>
          <td>
            <select name="grid_size">
              <?php if ($handle = opendir(get_template_directory().'/css/grids')): ?>
                <?php while (false !== ($file = readdir($handle))): ?>
                  <?php if ($file != "." && $file != ".."): ?>
                    <option value="<?php echo $file ?>" <?php selected($grid_size, $file) ?>><?php echo $file ?></option>
                  <?php endif; ?>
                <?php endwhile; ?>
              <?php closedir($handle); endif; ?>
            </select>
          </td>
        </tr>
        <?php $default_content_columns = get_option('default_content_columns',24); ?>
        <tr valign="top">
          <th scope="row">Default Content Column Size</th>
          <td>
            <select name="default_content_columns">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $default_content_columns ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </td>
        </tr>
        <?php $default_content_left_gutter = get_option('default_content_column_prefix',0); ?>
        <tr valign="top">
          <th scope="row">Default Content Left Gutter Size</th>
          <td>
            <select name="default_content_column_prefix">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $default_content_left_gutter ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </td>
        </tr>
        <?php $default_content_right_gutter = get_option('default_content_column_suffix',0); ?>
        <tr valign="top">
          <th scope="row">Default Content Right Gutter Size</th>
          <td>
            <select name="default_content_column_suffix">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $default_content_right_gutter ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>
      
      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Featured Image Options <small><a target="_blank" href="http://www.youtube.com/watch?v=HNAC-TxlqdI">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>These options allow you to control the default settins for how featured images appear on your site.</caption>
        <tr valign="top">
          <th scope="row">Detailed View</th>
          <td>
            <select name="image_size_detailed_view">
          	  <option value="0">None</option>
              <?php $image_sizes = hbgs_image_sizes(); foreach($image_sizes as $image_size): ?>
                <option value="<?php echo $image_size ?>" <?php selected(get_option("image_size_detailed_view"),$image_size) ?>><?php echo $image_size ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">List View</th>
          <td>
            <select name="image_size_list_view">
          	  <option value="0">None</option>
              <?php $image_sizes = hbgs_image_sizes(); foreach($image_sizes as $image_size): ?>
                <option value="<?php echo $image_size ?>" <?php selected(get_option("image_size_list_view"),$image_size) ?>><?php echo $image_size ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Site Wide Options</a> <small><a target="_blank" href="http://www.youtube.com/watch?v=Xmw0SzsykAI">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>These options allow you to include extra fonts, Meta Tags, Javascript and CSS on every page on your site.</caption>
        <tr valign="top">
          <th scope="row">Meta Tags</th>
          <td><textarea rows="10" cols="58" name="meta_tags"><?php echo get_option('meta_tags',"") ?></textarea></td>
        </tr>
        <tr valign="top">
          <th scope="row">Custom Fonts</th>
          <td><textarea rows="10" cols="58" name="custom_fonts"><?php echo get_option('custom_fonts',"") ?></textarea></td>
        </tr>
        <tr valign="top">
          <th scope="row">Javascript</th>
          <td><textarea rows="10" cols="58" name="footer_javascript"><?php echo get_option('footer_javascript',"") ?></textarea></td>
        </tr>
        <tr valign="top">
          <th scope="row">CSS</th>
          <td><textarea rows="10" cols="58" name="header_css"><?php echo get_option('header_css',"") ?></textarea></td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Default Page Header Options <small><a target="_blank" href="">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <caption>By default, you can include a banner image across the top of the content on every page.</caption>
        <tr valign="top">
          <th scope="row">Default Page Header Image Source</th>
          <td>
            <input class="upload_image_value the-value" type="text" size="36" name="default_page_header_image_src" value="<?php echo str_replace("null","",get_option("default_page_header_image_src")); ?>" />
            <input class="upload_image_button" type="button" value="Upload Image" />
            <br />Enter a URL or Click "Upload an Image"
            <input type="hidden" class="default-value" value="" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Default Page Header Image Height</th>
          <td>
            <input type="text" class="the-value" name="default_page_header_image_height" value="<?php echo get_option('default_page_header_image_height',HEADER_IMAGE_HEIGHT); ?>" />
            <input type="hidden" class="default-value" value="<?php echo HEADER_IMAGE_HEIGHT ?>" /> 
            <a class="restore-default" href="#">Restore Default</a>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Main Body Background</a> <small><a target="_blank" href="http://www.youtube.com/watch?v=fCyGSKBsinI">Watch Video</a></small></h3>
      <table class="form-table" style="display:none;">
        <tr valign="top">
          <th scope="row">Main Background Image</th>
          <td>
            A huge, custom background image will give your site a unique brand and feel. <a target="_blank" href="http://www.youtube.com/watch?v=fCyGSKBsinI">Check out how it's done in this video</a>, and then <a target="_blank" href="/wp-admin/themes.php?page=custom-background">head over to the uploader</a>  and add a new background for your site!
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Products</a></h3>
      <table class="form-table" style="display:none;">
        <caption>
          <p>The Gunner Technology Framework uses Contact 7 Forms for inquires and purchases.</p>
          <p>If you haven't done so, please <a href="/wp-admin/admin.php?page=wpcf7" target="_blank">create the forms</a> and then paste the codes in the relevant field.</p>
        </caption>
        <tr valign="top">
          <th scope="row">Inquiry Form</th>
          <td>
            <input type="text" class="the-value" name="inquiry_form_code" value="<?php echo get_option('inquiry_form_code'); ?>" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Purcahse Form</th>
          <td>
            <input type="text" class="the-value" name="purchase_form_code" value="<?php echo get_option('purchase_form_code'); ?>" />
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
          </td>
        </tr>
      </table>

      <h3 class="sub-option-menu"><a href="#" style="text-decoration:none;">Shortcodes</a></h3>
      <table class="form-table" style="display:none;">
        <caption>The theme comes with several shortcodes to make development and management more easy. This section is under construction, so check back for more ino.</caption>
        <tr valign="top">
          <th scope="row">Widget Shortcodes</th>
          <td>
            <p>As widgets are an essential component to our theme, we have made them easy to insert just about anywhere, including in your Visual Editor, via shortcodes.</p>
            <p>Please <a href="http://www.youtube.com/watch?v=VpP0F3l2YlU">watch this video</a> to see how they work.</p>
          </td>
        </tr>
      </table>
    </form>
  </div>
<?php }
