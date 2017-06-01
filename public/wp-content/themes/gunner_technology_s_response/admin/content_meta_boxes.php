<?php 

add_action('admin_init','hbgs_page_meta_init');
add_action('admin_init','hbgs_css_init');
add_action('admin_init','hbgs_javascript_init');

function hbgs_css_init() {
  if (current_user_can('delete_published_posts')) {
    $custom_types = get_post_types();
  	foreach ($custom_types as $type) {
  	  add_meta_box('custom_css', 'Custom CSS', 'hbgs_css_input', $type, 'advanced', 'high');
	  }
  }
}

function hbgs_css_input() {
	global $post;
	echo '<input type="hidden" name="custom_css_noncename" id="custom_css_noncename" value="'.wp_create_nonce('custom-css').'" />';
	echo '<textarea name="custom_css" id="custom_css" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,'_custom_css',true).'</textarea>';
	echo '<p>This area can be used to add CSS to just this page, allowing producers to add styles and override existing defaults. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about content CSS.</a></p>';
}

function hbgs_css_save($post_id) {
  if (!array_key_exists('custom_css_noncename',$_POST)) return $post_id;
	if (!wp_verify_nonce($_POST['custom_css_noncename'], 'custom-css')) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	$custom_css = $_POST['custom_css'];
	update_post_meta($post_id, '_custom_css', $custom_css);
}

function hbgs_javascript_init() {
  if (current_user_can('delete_published_posts')) {
    $custom_types = get_post_types();
  	foreach ($custom_types as $type) {
  	  add_meta_box('custom_javascript', 'Custom Javascript', 'hbgs_javascript_input', $type, 'advanced', 'high');
	  }
  }
}

function hbgs_javascript_input() {
	global $post;
	echo '<input type="hidden" name="custom_script_noncename" id="custom_script_noncename" value="'.wp_create_nonce('custom-script').'" />';
	echo '<textarea name="custom_script" id="custom_script" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,'_custom_script',true).'</textarea>';
	echo '<p>This area can be used to add Javascript to just this page, allowing producers to simple, dynamic behavior. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about content Javascript.</a></p>';
}

function hbgs_javascript_save($post_id) {
  if (!array_key_exists('custom_script_noncename',$_POST)) return $post_id;
	if (!wp_verify_nonce($_POST['custom_script_noncename'], 'custom-script')) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	$custom_javascript = $_POST['custom_script'];
	update_post_meta($post_id, '_custom_script', $custom_javascript);
}


function hbgs_page_meta_init() {
  wp_deregister_script( 'jquery' );
  wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', false, '1.4.3');
  wp_enqueue_script( 'jquery-ui-draggable' );
  wp_enqueue_script( 'jquery-ui-droppable' );
  wp_enqueue_script( "content_meta_boxes", get_template_directory_uri().'/admin/js/content_meta_boxes.js', array('suggest') );
 
 	add_action('save_post','hbgs_meta_save');
 	add_action('save_post','hbgs_css_save');
 	add_action('save_post','hbgs_javascript_save');
 	
 	if (current_user_can('delete_published_posts')) {
 	  $hbgs_meta_boxes_has_been_nonced = false;
    $custom_types = get_post_types();
    $metas = hbgs_get_additional_meta_boxes();
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
}


//This is where you will define extra meta information for content (pages,posts,etc)
function hbgs_get_additional_meta_boxes() {
  $hbgs_meta = hbgs_get_meta();
  return array(
    array(
      "slug" => "featured_images",
      "title" => "Featured Image Sizes",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { 
        $hbgs_meta = array_merge(
          array(
            "image_size_detailed_view" => get_option("image_size_detailed_view",0),
            "image_size_list_view" => get_option("image_size_list_view",0)
          ),
          hbgs_get_meta()
        );
        ?><div class="ajaxtag notypeahead">
          <p>
        	  <span style="display:inline-block; width: 124px;">Detailed View:</span>
        	  <select name="_hbgs_meta[image_size_detailed_view]">
          	  <option value="0">None</option>
              <?php $image_sizes = hbgs_image_sizes(); foreach($image_sizes as $image_size): ?>
                <option value="<?php echo $image_size ?>" <?php selected($hbgs_meta["image_size_detailed_view"],$image_size) ?>><?php echo $image_size ?></option>
              <?php endforeach; ?>
            </select>
        	</p>
        	<p>
        	  <span style="display:inline-block; width: 124px;">List View:</span>
        	  <select name="_hbgs_meta[image_size_list_view]">
          	  <option value="0">None</option>
              <?php $image_sizes = hbgs_image_sizes(); foreach($image_sizes as $image_size): ?>
                <option value="<?php echo $image_size ?>" <?php selected($hbgs_meta["image_size_list_view"],$image_size) ?>><?php echo $image_size ?></option>
              <?php endforeach; ?>
            </select>
        	</p>
          <p>This panel will allow you to set how featured images appear in detailed views and list views. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more featured images.</a></p>
        </div> 
      <?php }
    ),
    array(
      "slug" => "subtitle",
      "title" => "Sub Title",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        echo '<div class="ajaxtag notypeahead">
          <div>
            '. (isset($hbgs_meta["subtitle"]) ? '' : '<div class="taghint" style="">Type Sub Title Here</div>') .'
            <p><input style="width:100%; " type="text" class="newtag form-input-tip" name="_hbgs_meta[subtitle]" value="'. (array_key_exists('subtitle',$hbgs_meta) ? $hbgs_meta["subtitle"] : '') .'" /></p>
          </div>
          <p>Your site may use subtitles to add more information to content in list or detail view. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about subtitles.</a></p>
        </div>'; 
      }
    ),
    array(
      "slug" => "calltoaction",
      "title" => "Call to Action",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        echo '<div class="ajaxtag notypeahead">
          <div>
            '. (isset($hbgs_meta["call_to_action"]) ? '' : '<div class="taghint" style="">Type Call To Action Text Here</div>') .'
            <p><input style="width:100%;" class="newtag form-input-tip" type="text" name="_hbgs_meta[call_to_action]" value="'. (array_key_exists('call_to_action',$hbgs_meta) ? $hbgs_meta["call_to_action"] : '') .'" /></p>
          </div>
          <div>
            '. (isset($hbgs_meta["call_to_action_url"]) ? '' : '<div class="taghint" style="">Type Call To Action URL Here</div>') .'
            <p><input style="width:100%;" class="newtag form-input-tip" type="text" name="_hbgs_meta[call_to_action_url]" value="'. (array_key_exists('call_to_action_url',$hbgs_meta) ? $hbgs_meta["call_to_action_url"] : '') .'" /></p>
          </div>
          <p>Occasionally, you may want to have content in list-view show a link to read more or a link to a call to action. If your site supports it, this is where you set that information <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about calls to action.</a></p>
        </div>'; 
      }
    ),
    array(
      "slug" => "icon",
      "title" => "Icon",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        echo '<div class="ajaxtag notypeahead">
          '. (isset($hbgs_meta["icon_url"]) ? '' : '<div class="taghint" style="">Enter an URL or Click "Upload Image"</div>') .'
          <p><input class="newtag form-input-tip upload_image_value the-value" type="text" size="36" name="_hbgs_meta[icon_url]" value="'. (array_key_exists('icon_url',$hbgs_meta) ? $hbgs_meta["icon_url"] : '') .'" /> 
          <input style="" class="upload_image_button" type="button" value="Upload Image" /></p>
          <p>If your site supports icons, you may upload an icon that will represent the content in list-views. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about icons.</a></p>
        </div>'; 
      }
    ),
    array(
      "slug" => "headergraphic",
      "title" => "Header Graphic",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        echo '<div class="ajaxtag notypeahead">
          <div>
            '. (isset($hbgs_meta["header_url"]) ? '' : '<div class="taghint" style="">Enter an URL or Click "Upload Image"</div>') .'
            <p><input style="" class="newtag form-input-tip upload_image_value the-value" type="text" size="36" name="_hbgs_meta[header_url]" value="'. (array_key_exists('header_url',$hbgs_meta) ? $hbgs_meta["header_url"] : '') .'" /> 
            <input class="upload_image_button" type="button" value="Upload Image" /></p>
          </div>
          <div>
            '. (isset($hbgs_meta["header_height"]) ? '' : '<div class="taghint" style="">Height in px</div>') .'
            <p><input style="" class="newtag form-input-tip" type="text" size="10" name="_hbgs_meta[header_height]" value="'. (array_key_exists('header_height',$hbgs_meta) ? $hbgs_meta["header_height"] : '') .'" /></p>
          </div>
          <div>
            <p><input type="checkbox" '. checked(isset($hbgs_meta["show_header_text"]) && $hbgs_meta["show_header_text"] == 'on') . ' name="_hbgs_meta[show_header_text]" /> Show Header Text?</p>
          </div>
          <p>Header graphics appear behind the headers of content in detail-view. If your site supports them, you may upload one here. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about header graphics.</a></p>
        </div>'; 
      }
    ),
    array(
      "slug" => "redirect",
      "title" => "Redirect",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        echo '<div class="ajaxtag notypeahead">
          '. (isset($hbgs_meta["redirect"]) ? '' : '<div class="taghint">Enter a URL Here</div>') .'
          <p><input style="width:100%;" class="newtag form-input-tip" type="text" name="_hbgs_meta[redirect]" value="'. (array_key_exists('redirect',$hbgs_meta) ? $hbgs_meta["redirect"] : '') .'" /></p>
          <p>If you enter a URL here, visitors will be taken to that URL instead of this content when clicking on it from a list-view. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about header graphics.</a></p>
        </div>'; 
      }
    ),
    array(
      "slug" => "contentcolumns",
      "title" => "Content Columns",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        $content_columns = (isset($hbgs_meta['content_columns']) ? $hbgs_meta['content_columns'] : get_option("default_content_columns",24));
        $content_column_prefix = (isset($hbgs_meta['content_column_prefix']) ? $hbgs_meta['content_column_prefix'] : get_option("default_content_column_prefix",0));
        $content_column_suffix = (isset($hbgs_meta['content_column_suffix']) ? $hbgs_meta['content_column_suffix'] : get_option("default_content_column_suffix",0));
        
        ?><div class="ajaxtag notypeahead">
          <p>
        	  <span style="display:inline-block; width: 124px;">Number of Columns:</span>
        	  <select name="_hbgs_meta[content_columns]">
          	  <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $content_columns ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
        	</p>
        	<p>
        	  <span style="display:inline-block; width: 124px;">Empty Left Columns:</span>
        	  <select name="_hbgs_meta[content_column_prefix]">
          	  <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $content_column_prefix ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
        	</p>
        	<p>
        	  <span style="display:inline-block; width: 124px;">Empty Right Columns:</span>
        	  <select name="_hbgs_meta[content_column_suffix]">
          	  <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>" <?php echo $i == $content_column_suffix ? 'selected="selected"' : '' ?>><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
        	</p>
          <p>This panel will allow you to override the default grid settings for this piece of content. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more the grid system.</a></p>
        </div> 
      <?php }
    ),
    array(
      "slug" => "template",
      "title" => "Template",
      "placement" => "side",
      "priority" => "low",
      "callback" => function() { $hbgs_meta = hbgs_get_meta();
        if(!isset($hbgs_meta['post_template'])){ 
          if(get_post_type() == 'image_album') {
            $hbgs_meta['post_template'] = 'image_album.php'; 
          } else if(get_post_type() == 'image_album') {
            $hbgs_meta['post_template'] = 'product.php'; 
          } else {
            $hbgs_meta['post_template'] = 'default.php'; 
          }
          
          if($hbgs_meta['post_template'] == 'default.php') { //LEGACY TO REMOVE DEFAULT TEMPLATE
            unset($hbgs_meta['post_template']);
          }
        }
        ?><div class="ajaxtag notypeahead">
          <p>
            <select name="_hbgs_meta[post_template]">
              <option value=""></option>
          	  <?php if ($handle = opendir(hbgs_theme_path().'/php/templates/content/custom')): ?>
                <?php while (false !== ($file = readdir($handle))): ?>
                  <?php if ($file != "." && $file != ".."): ?>
                    <option value="<?php echo $file ?>" <?php selected(isset($hbgs_meta['post_template']) && $hbgs_meta['post_template'], $file) ?>><?php echo $file ?></option>
                  <?php endif; ?>
                <?php endwhile; ?>
              <?php closedir($handle); endif; ?>
            </select>
          </p>
          <p>Most of the time, the default template will work just fine, but some sites need custom templates for some content. You can choose a custom template here. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about custom templates.</a></p>
        </div> 
      <?php }
    )
  );
}



// Given the sidebar object, its area ($key) and identifier ($slug), returns true or false for if the sidebar is being used by the current piece of content 
function hbgs_is_sidebar_in_use($sidebar,$key,$slug) {
  $hbgs_meta = hbgs_get_meta();
  
  if(!$hbgs_meta || !$hbgs_meta[$key] || !isset($hbgs_meta[$key][$slug])) {
    if($sidebar->default == 'on') {
      return (intval($sidebar->position) > 0);
    } else {
      return false;
    }
  } else {
    return (intval($hbgs_meta[$key][$slug]) > 0);
  }
}


function hbgs_meta_save($post_id) {
  if (!array_key_exists('hbgs_meta_noncename',$_POST)) return $post_id;
	if (!wp_verify_nonce($_POST['hbgs_meta_noncename'],__FILE__)) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	
	$current_data = get_post_meta($post_id, '_hbgs_meta', TRUE);	
  
	$new_data = $_POST['_hbgs_meta'];

	hbgs_meta_clean($new_data);
 
	if ($current_data) {
		if (is_null($new_data)) delete_post_meta($post_id,'_hbgs_meta');
		else update_post_meta($post_id,'_hbgs_meta',$new_data);
	} elseif (!is_null($new_data)) {
		add_post_meta($post_id,'_hbgs_meta',$new_data,TRUE);
	}
 
	return $post_id;
}

function hbgs_meta_clean(&$arr) {
	if (is_array($arr)) {
		foreach ($arr as $i => $v) {
			if (is_array($arr[$i])) {
				hbgs_meta_clean($arr[$i]);
 
				if (!count($arr[$i])) {
					unset($arr[$i]);
				}
			} else {
				if (trim($arr[$i]) == '') {
					unset($arr[$i]);
				} else {
				  $arr[$i] = hbgs_sanitize_data($arr[$i]);
				}
			}
		}
 
		if (!count($arr)) {
			$arr = NULL;
		}
	}
}

function hbgs_sanitize_data($test_value) {
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
