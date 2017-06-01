<?php

function hbgs_link_filter($link, $post) {
  if (has_post_format('link', $post) && get_post_meta($post->ID, 'LinkFormatURL', true)) {
    $link = get_post_meta($post->ID, 'LinkFormatURL', true);
  }
  return $link;
}

function hbgs_link_excerpt($excerpt) {
  global $post;
  
  if (has_post_format('link', $post) && get_post_meta($post->ID, 'LinkFormatSource', true)) {
    $excerpt .= '<cite class="source">'.get_post_meta($post->ID, 'LinkFormatSource', true).'</cite>';
  }
  return $excerpt;
}

function hbgs_do_shortcode($widget_text) {
  global $hbgs_type_of_assets_to_return;
  
  $hbgs_type_of_assets_to_return = 'js';
  do_shortcode($widget_text);
  $hbgs_type_of_assets_to_return = 'css';
  do_shortcode($widget_text);
  $hbgs_type_of_assets_to_return = 'html';
  $widget_text = do_shortcode($widget_text);
  
  return $widget_text;
}

function hbgs_widget_display_callback($instance, $obj, $args) {  
  return $instance;
}

function hbgs_excerpt_more($more) {
  global $post;
	
	return '<a class="more" href="'. get_permalink($post->ID) . '">Read More</a>';
}

function hbgs_nofollow_bookmarks( $links ) {
  foreach($links as $link) {
    if($link->link_target == '_blank') {
      $link->link_target = '';
      $link->link_rel .= ' external';
    }
    $link->link_rel .= ' nofollow';
    $link->link_rel = trim($link->link_rel);
  }
  return $links;
}

function hbgs_filter_permalink($url) {
  if(get_the_ID()) {
    $hbgs_meta = hbgs_get_meta();
    return is_array($hbgs_meta) && array_key_exists('permalink',$hbgs_meta) ? $hbgs_meta['permalink'] : $url;
  }
  
  return $url;
}

function hbgs_parse_smart_tube($text) {
  if(isset($smart_youtube)) {
    return $smart_youtube->check($text, 1);
  } else if(class_exists("SmartYouTube")) {
    $smart_youtube = new SmartYouTube();
    return $smart_youtube->check($text, 1);
  } else {
    return $text;
  }
}

/**
 * Adding our custom fields to the $form_fields array
 *
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function hbgs_image_attachment_fields_to_edit($form_fields, $post) {
  if( substr($post->post_mime_type, 0, 5) == 'image' ) {
  	$form_fields["mid_sized_image_url"] = array(
  		"label" => __("Mid-sized Image URL"),
  		"input" => "text",
  		"value" => get_post_meta($post->ID, "_mid_sized_image_url", true)
  	);
  	
  	$form_fields["mid_sized_caption"] = array(
    	"label" => __("Mid-sized Caption"),
    	"input" => "textarea",
    	"value" => get_post_meta($post->ID, "_mid_sized_caption", true)
    );
    
    $form_fields["large_sized_image_url"] = array(
    	"label" => __("Large-sized Image URL"),
    	"input" => "text",
    	"value" => get_post_meta($post->ID, "_large_sized_image_url", true)
    );

    $form_fields["large_sized_caption"] = array(
    	"label" => __("Large-sized Caption"),
    	"input" => "textarea",
    	"value" => get_post_meta($post->ID, "_large_sized_caption", true)
    );
    
  }
	return $form_fields;
}

/**
 * @param array $post
 * @param array $attachment
 * @return array
 */
function hbgs_image_attachment_fields_to_save($post, $attachment) {
    
	if( isset($attachment['large_sized_caption']) ){
		update_post_meta($post['ID'], '_large_sized_caption', $attachment['large_sized_caption']);
	}
	
	if( isset($attachment['large_sized_image_url']) ){
		update_post_meta($post['ID'], '_large_sized_image_url', $attachment['large_sized_image_url']);
	}
	
	if( isset($attachment['mid_sized_caption']) ){
		update_post_meta($post['ID'], '_mid_sized_caption', $attachment['mid_sized_caption']);
	}
	
	if( isset($attachment['mid_sized_image_url']) ){
		update_post_meta($post['ID'], '_mid_sized_image_url', $attachment['mid_sized_image_url']);
	}
  	
	return $post;
}

function hbgs_login_headerurl($arg) {
  return "http://gunnertech.com/";
}

function hbgs_login_headertitle($arg) {
  return "Powered by Gunner Technology";
}


/*ADMIN FILTERS*/

function hbgs_admin_footer_text($arg) {
  return '<span id="footer-thankyou">' . __('Thank you for using <a href="http://gunnertech.com/">Gunner Technology</a>.').'</span> '.__('<a href="http://codex.wordpress.org/">Documentation</a>');
}

function hbgs_image_album_the_content($content) { 
  $images = hbgs_get_images_for_item();
  
  $thumbnail_size = hbgs_get_thumbnail_image_size_for_option("_image_album_thumbnail_size");
  
  if(!preg_match("/image-detail/i",$content)) {
    $content = "<div id=\"image-detail\">$content</div>";
  }
  ob_start();
  ?>
  <?php if($images and is_array($images)): ?>
    <ul class="image-gallery">
      <?php foreach($images as $image): ?>
        <?php if($image->menu_order > 0): $image_data = wp_get_attachment_image_src( $image->ID, $thumbnail_size ); ?>
          <li>
            <figure class="small" id="image-small-<?php echo $image->ID ?>" style="width:<?php echo $image_data[1] ?>px">
              <?php if(!$url = get_post_meta($image->ID, "_mid_sized_image_url",true)) { $url = $image->guid; } ?>
              <a data-detailedarea="image-detail" class="image-small-link gallery-link" href="<?php echo $url ?>">
                <?php echo wp_get_attachment_image($image->ID,$thumbnail_size,false,array()) ?>
              </a>
              <figcaption>
                <?php echo $image->post_title ?>
              </figcaption>
            </figure>
            
            <figure class="mid" style="display:none;" id="image-mid-<?php echo $image->ID ?>">
              <?php if(!$url = get_post_meta($image->ID, "_large_sized_image_url",true)) { $url = $image->guid; } ?>
              <a data-detailedarea="image-detail" class="image-mid-link gallery-link" href="<?php echo $url ?>">
                <img src="<?php echo get_post_meta($image->ID, "_mid_sized_image_url", true) ?>" alt="" />
              </a>
              <figcaption>
                <?php echo wpautop(get_post_meta($image->ID, "_mid_sized_caption", true)); ?>
                <h4><a data-detailedarea="image-detail" class="back-to-start-link" href="<?php echo $url ?>">Back</a></h4>
              </figcaption>
            </figure>
            
            <figure class="large" style="display:none;" id="image-large-<?php echo $image->ID ?>">
              <img src="<?php echo get_post_meta($image->ID, "_large_sized_image_url", true) ?>" alt="" />
              <figcaption>
                <?php echo wpautop(get_post_meta($image->ID, "_large_sized_caption", true)); ?>
                <h4><a data-detailedarea="image-detail" class="image-small-link" href="<?php echo $url ?>">Back</a></h4>
              </figcaption>
            </figure>
            
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <?php  
  $content .= ob_get_contents(); 
  ob_end_clean();
  return $content;
}