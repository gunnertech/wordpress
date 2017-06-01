<?php



define('HBGS_FILTER_SCOPE', 77);
define('HBGS_JQUERY_VERSION', "1.7.2");
if(strpos($_SERVER['SERVER_NAME'],'dev') !== FALSE) {
  define('HBGS_ENVIRONMENT', 'development');
} else if(strpos($_SERVER['SERVER_NAME'],'.gunnertechnetwork.net') !== FALSE) {
  define('HBGS_ENVIRONMENT', 'staging');
} else {
  define('HBGS_ENVIRONMENT', 'production');
}




//GLOBALS
$hbgs_inline_scripts = '';
$hbgs_type_of_assets_to_return = null;
$hbgs_inline_count = array('js' => 0, 'css' => 0, 'html' => 0);

require_once(TEMPLATEPATH.'/php/classes/Hbgs/Widget.php');
require_once(TEMPLATEPATH.'/php/classes/Hbgs/Custom_Post_Type.php');
require_once(TEMPLATEPATH.'/php/classes/Hbgs/Walkers/Comment.php');

if ( is_admin() ) {
  require_once hbgs_theme_path() . 'admin/base.php';
}
require_once hbgs_theme_path() . 'php/lib/actions.php';
require_once hbgs_theme_path() . 'php/lib/filters.php';
require_once hbgs_theme_path() . 'php/lib/shortcodes.php';
require_once hbgs_theme_path() . 'php/lib/sidebars_and_widgets.php';

remove_action('wp_head', 'wp_generator');
remove_action("wp_head","wp_print_head_scripts",9);
remove_action('wp_footer', 'wp_print_footer_scripts');

add_action('after_setup_theme', 'hbgs_setup'); //THEME SUPPORT AND ALL THAT GOOD STUFF
add_action('init', 'hbgs_init');
add_action('init', 'hbgs_create_sidebar_taxonomies'); //EXTEND ALL POST TYPES TO ADD SIDEBAR CATEGORIES
add_action('get_header','hbgs_possibly_redirect'); //CHECK TO SEE IF THERE ARE ANY GLOBAL REDIRECTS ON THE URI OR IF THE QUERIED CONTENT HAS A REDIRECT
add_action('widgets_init', 'hbgs_widgets_init'); //REGISTER OUR DYNAMIC SIDEBARS AND OPEN WIDGETS DIRECTORY AND REGISTER EACH WIDGET IN THERE (TODO: CREATE SETTINGS PAGE FOR CHOOSING WHICH WIDGETS TO LOAD)
add_action('parse_request','hbgs_add_custom_post_types_to_category_query'); //CHANGE DEFAULT BEHAVIOR TO LOOK FOR CONTENT OF ANY TYPE WHEN DURING FOR A CATEGORY LISTING PAGE
add_action('wp_head', 'hbgs_configure_content_column',1); //CREATE THE CONTENT COLUMN VARIABLE FOR USE IN TEMPLATES
add_action('wp_head','hbgs_insert_custom_css', 3); //ADD ALL OUR CUSTOM CSS FROM SIDEBARS, WIDGETS, PAGES, ETC INTO THE DOCUMENT
add_action('wp_head','hbgs_insert_custom_script', 3); //ADD ALL OUR CUSTOM JS FROM SIDEBARS, WIDGETS, PAGES, ETC INTO THE DOCUMENT
add_action('wp_head','hbgs_print_sidebar_styles',4);
add_action('wp_head','hbgs_print_sidebar_scripts',4);
add_action("wp_head","hbgs_wp_print_head_scripts",9); //REPLACEMENT FOR WP_PRINT_HEAD_SCRIPTS: EXTRACT EXTERNAL JS AND LOAD VIA HEAD.JS INSTEAD OF NORMAL METHOD
add_action('login_head', 'hbgs_login_logo'); //REPLACE DEFAULT LOGO WITH THE ONE FROM OUR OPTIONS MENU
foreach(hbgs_sidebar_areas() as $area) {
  add_action("hbgs_${area}_widgets",function() use ($area)  {
    hbgs_widgets_for($area);
  });
}

add_filter('get_bookmarks', 'hbgs_nofollow_bookmarks'); //MAKES ALL BOOKMARKS NOFOLLOW TODO: MAKE THIS OPTIONAL?
add_filter('the_permalink', 'hbgs_filter_permalink'); //OVERRIDES THE DEFAULT PERMALINK FUNCTION TO USE META BOX IF SET
add_filter('widget_text', "hbgs_parse_smart_tube"); //ADDS SMART YOUTUBE SHORTCODE SUPPORT FOR TEXT WIDGET
add_filter('widget_text', 'do_shortcode'); //TURN ON SHORT CODES FOR WIDGETS
add_filter('the_excerpt', 'do_shortcode'); //TURN ON SHORT CODES FOR EXCERPTS
add_filter('the_title', 'do_shortcode'); //TURN ON SHORT CODES FOR TITLES
add_filter('single_cat_title', 'do_shortcode'); //TURN ON SHORT CODES FOR TITLES
add_filter("attachment_fields_to_edit", "hbgs_image_attachment_fields_to_edit", null, 2); //ADD MORE FIELDS TO THE DEFAULT MEDIA TYPE
add_filter('attachment_fields_to_save', 'hbgs_image_attachment_fields_to_save', 10, 2); //ADD MORE FIELDS TO THE DEFAULT MEDIA TYPE
add_filter('widget_display_callback', 'hbgs_widget_display_callback',10,3);
add_filter('excerpt_more', 'hbgs_excerpt_more');
add_filter('login_headerurl','hbgs_login_headerurl'); //REMOVE DEFAULT WORDPRESS FOOTER AND REPLACE WITH GUNNER TECHNOLOGY
add_filter('login_headertitle','hbgs_login_headertitle'); //REMOVE DEFAULT WORDPRESS FOOTER AND REPLACE WITH GUNNER TECHNOLOGY
add_filter('post_link', 'hbgs_link_filter', 10, 2); //FOR TUMBLR STYLE LINKS
add_filter('the_excerpt', 'hbgs_link_excerpt'); //FOR TUMBLR STYLE LINKS

/*UTILITIES*/
if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

if(!function_exists('getallheaders')) {
  function getallheaders() {
    foreach($_SERVER as $h=>$v) {
      if(ereg('HTTP_(.+)',$h,$hp)) {
        $headers[$hp[1]]=$v;
      }
    }
    return $headers;
  }
}

function gt_is_ssl() {
  if(is_ssl()) {
    return true;
  }
  
  if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    return true;
  }
  
  return false;
}

function hbgs_content_column_classes() {
  global $content_column;
  
  if(is_singular()) {
    post_class("content-column clearfix grid_".$content_column['count']." suffix_".$content_column['suffix']." prefix_".$content_column['prefix']." pull_".$content_column['pull']." push_".$content_column['push']." ".$content_column['extra']);
  } else {
    echo "class=\"content-column clearfix grid_".$content_column['count']." suffix_".$content_column['suffix']." prefix_".$content_column['prefix']." pull_".$content_column['pull']." push_".$content_column['push']." ".$content_column['extra']."\"";
  }  
}

function hbgs_content_title() {
  
}

function hbgs_favicon_url() {
  $url = get_option("favicon_url");
  if($url == 'null') {
    return get_stylesheet_directory_uri().'/images/favicon.ico';
  }
  
  return $url;
}


function hbgs_configure_custom_post_types() {
  $files = hbgs_directory_to_array(hbgs_theme_path().'php/classes/Hbgs/Custom_Post_Types',true);
  $hbgs_content_types = hbgs_as_json(get_option('hbgs_content_types'));
  $hbgs_content_types = $hbgs_content_types ? $hbgs_content_types : array();

  foreach($files as $file) {
    $value = strtolower(str_replace(array(".php"),array(""),basename($file)));
    if(in_array($value, $hbgs_content_types)) {
      require_once $file;
      $class_name = str_replace(".php","",str_replace("/","_",str_replace(hbgs_theme_path().'php/classes/',"",$file)));
      $type_obj = new $class_name;
      $type_obj->register();
    }
  }
}

function hbgs_directory_to_array($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, hbgs_directory_to_array($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

function hbgs_current_admin_page($full_path) {
  return 'hbgs-'.str_replace(".php","",str_replace("_","-",basename($full_path)));
}

function hbgs_ensure_json_array($value) {
  if(!is_array($value)) { 
    $value = json_decode($value);
  } 
  
  if(!is_array($value)) { 
    $value = array(); 
  }
  
  return $value;
}

function hbgs_as_json($value) {
  if(is_string($value)) {
    return hbgs_as_json(json_decode($value));
  }
  
  return $value;
}

function hbgs_as_string($value) {
  if(is_string($value)) {
    return $value;
  }
  
  return json_encode($value);
}

function hbgs_theme_path() {
  //return get_template_directory().'/';
  return TEMPLATEPATH.'/';
}

function hbgs_v($value) {
  if(!isset($value)) {
    return null;
  }
  
  return $value;
}

function hbgs_get_colors($as_json=false) {
  $default_colors = array(
    array(
      'name' => 'color-palatte-primary',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-primary_var_1',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-primary_var_2',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-secondary',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-secondary_var_1',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-secondary_var_2',
      'value' => '#607890'
    ),
    array(
      'name' => 'color-palatte-transparent',
      'value' => 'transparent'
    ),
    array(
      'name' => 'color-palatte-inherit',
      'value' => 'inherit'
    )
  );

  $colors = get_option('hbgs_colors',json_encode($default_colors));
  
  $colors = hbgs_as_json(get_option('hbgs_colors'));
  
  if(!$colors) {
    $colors = json_decode(json_encode($default_colors));
  }
  
  return $as_json ? $colors : json_encode($colors);
}

function hbgs_get_the_slug() {
  global $post;
  
  if ( is_single() || is_page() ) {
    return $post->post_name;
  }
  
  return "";

}

function hbgs_color_replacements() {
  global $_hbgs_colors, $_hbgs_color_replacements;
  
  if(isset($_hbgs_color_replacements)) {
    return $_hbgs_color_replacements;
  }
  
  if(!isset($_hbgs_colors)) {
    $_hbgs_colors = hbgs_get_colors(true);
  }
  
  $patterns = array();
  $replacements = array();
  
  foreach($_hbgs_colors as $c){
    $patterns[] = '&:'.str_replace("color-palatte-","",$c->name).";";
    $replacements[] = $c->value.";";
  }
  
  $_hbgs_color_replacements = array("patterns" => $patterns, "replacements" => $replacements);
  
  return $_hbgs_color_replacements;
}

function hbgs_shortened_excerpt($excerpt,$excerpt_length) {
  $link = '';
  $pattern = '/<a .+>.+<\/a>/';
  
  if(preg_match($pattern,$excerpt,$matches) > 0) {
    $content = preg_replace($pattern,"",$excerpt);
    $link = ' ' . $matches[0];
  }
  return substr(strip_tags($excerpt), 0, $excerpt_length) . (strlen($excerpt) > $excerpt_length ? '...' : '') . $link;
}

function hbgs_body_classes($addition=null) {
  $cats = (array) get_the_category();
  
  
  foreach($cats as $cat) {
    $classes[] = $cat->slug;
  }
  
  //$classes[] = "clearfix";
  
  if($addition) {
    $classes[] = $addition;
  }
  
  $classes[] = hbgs_get_the_slug();
  
  return $classes;
}

function hbgs_get_the_category_name() {
  global $wp_query;
  if(isset($wp_query->query_vars["wpsc_product_category"])) {
    if(isset($wp_query->query_vars["wpsc_product_category"])) {
      return ucwords(preg_replace('/\-/',' ', $wp_query->query_vars["wpsc_product_category"]));
    } else {
      return null;
    }
  } else {
    $category = get_the_category();
    if($category && is_array($category)) {
      return $category[0]->cat_name;
    }
  }
  
  return null;
}


function hbgs_image_sizes() {
  $image_sizes = array('thumbnail', 'medium', 'large', 'full'); // Standard sizes
	$custom_image_json = hbgs_as_json(get_option('custom_image_sizes'));
	
	if(is_array($custom_image_json)) {
	  foreach($custom_image_json as $custom_image) {
	    $image_sizes[] = $custom_image->name;
	  }
	}
		
	return $image_sizes;
}

function hbgs_set_all_posts_to_pending() {
  query_posts('posts_per_page=-1&post_status=publish');
  
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();
      $my_post = array();
      $my_post['ID'] = get_the_ID();
      $my_post['post_status'] = 'draft';

      wp_update_post( $my_post );
    }
  }
  wp_reset_query();
}

function hbgs_get_post_thumbnail($content_id,$view) {
  $hbgs_meta = get_post_meta($content_id,'_hbgs_meta',TRUE);
  
  $hbgs_meta['image_size_'.$view.'_view'] = isset($hbgs_meta['image_size_'.$view.'_view']) ? $hbgs_meta['image_size_'.$view.'_view'] : get_option('image_size_'.$view.'_view');
  if( $hbgs_meta['image_size_'.$view.'_view'] !== '0' ) {
    return get_the_post_thumbnail($content_id,$hbgs_meta['image_size_'.$view.'_view']);
  } else {
    return false;
  }
}

function hbgs_get_post_thumbnail_meta($content_id,$view) {
  $hbgs_meta = get_post_meta($content_id,'_hbgs_meta',TRUE);
  
  $hbgs_meta['image_size_'.$view.'_view'] = isset($hbgs_meta['image_size_'.$view.'_view']) ? $hbgs_meta['image_size_'.$view.'_view'] : get_option('image_size_'.$view.'_view');
  
  if( $hbgs_meta['image_size_'.$view.'_view'] !== '0' ) {
    return wp_get_attachment_image_src(get_post_thumbnail_id($content_id),$hbgs_meta['image_size_'.$view.'_view']);
  } else {
    return false;
  }
}


function hbgs_get_meta($reload=false) {
  global $wp_query, $post, $hbgs_meta;
	
	$meta = $hbgs_meta;
	if($reload) {
	  if(is_search() || is_category()) {
	    $meta = get_post_meta(get_option('page_for_posts'),'_hbgs_meta',TRUE);
	  } elseif(isset($wp_query->queried_object)) {
	    $meta = get_post_meta($wp_query->queried_object->ID,'_hbgs_meta',TRUE);
	  } elseif(isset($post)) {
	    $meta = get_post_meta($post->ID,'_hbgs_meta',TRUE);
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

function hbgs_category_title_with_link($category=false) {
  $category = $category ? $category : get_the_category();
  $category = is_array($category) ? $category[0] : $category;
  
  if($category) {
    echo '<a href="'.get_category_link($category->cat_ID).'">'.$category->cat_name.'</a>';
  } else {
    $post_type_obj = get_post_type_object(get_post_type());
    echo '<a href="/'.$post_type_obj->name.'/">'.$post_type_obj->labels->name.'</a>';
  }
}



function hbgs_post_meta() {
	printf( __( '<span class="author-info"><span class="meta-sep">By</span> %3$s </span><span class="separator">|</span> %2$s <span class="separator">|</span> ', 'hbgs' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span> <span class="entry-time">%4$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date(),
			get_the_time()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}

function hbgs_get_the_category_list( $separator = '', $parents='', $post_id = false ) {
	global $wp_rewrite;
	
	$categories = get_the_category( $post_id );
	if ( !is_object_in_taxonomy( get_post_type( $post_id ), 'category' ) )
		return apply_filters( 'the_category', '', $separator, $parents );

	if ( empty( $categories ) )
		return apply_filters( 'the_category', __( 'Uncategorized' ), $separator, $parents );

	$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, true, $separator );
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, false, $separator );
					$thelist .= $category->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i )
				$thelist .= $separator;
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, true, $separator );
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
					break;
				case 'single':
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, false, $separator );
					$thelist .= "$category->name</a>";
					break;
				case '':
				default:
					$thelist .= '<a class="category-link" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
			}
			++$i;
		}
	}
	return apply_filters( 'the_category', $thelist, $separator, $parents );
}

function hbgs_get_images_for_item($options=null) {
  $options = $options ? $options : array();
  
  return get_children(
    array(
  	  'post_parent' => get_the_ID(),
    	'post_status' => 'inherit',
    	'post_type' => 'attachment',
    	'post_mime_type' => 'image',
    	'order' => 'ASC',
    	'orderby' => 'menu_order'
  	)
  );
}

function hbgs_get_thumbnail_image_size_for_option($name) {
  if(!$thumbnail_size = get_post_meta(get_the_ID(), $name, true)) {
    $thumbnail_size = 'thumbnail';
  }
  
  return $thumbnail_size;
}