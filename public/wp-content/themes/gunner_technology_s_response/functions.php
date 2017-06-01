<?php

//require_once dirname( __FILE__ ) . '/php/classes/Tgm_Plugin_Activation.php';

/***** CONSTANTS ****/

define('HBGS_IS_HTTPS_REQUEST', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
define('HBGS_FILTER_SCOPE', 77);
define('HBGS_JQUERY_VERSION', "1.6.1");

/***** /CONSTANTS ****/


/***** REGISTERED SCRIPTS FOR CHILD THEMES ****/

wp_register_script( 'twitter-bootstrap', get_bloginfo('template_url').'/js/libs/bootstrap.min.js?v=44', array('jquery') );

/***** /REGISTERED SCRIPTS FOR CHILD THEMES ****/


/**** REMOVE ACTIONS *****/

remove_action('wp_head', 'wp_generator');

/***** /REMOVE ACTIONS *****/


/**** ADD ACTIONS *****/

add_action('after_setup_theme', function() {
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'automatic-feed-links' );
});

add_action('init', function() {
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'plugins', get_bloginfo('template_url').'/js/plugins.js', array('jquery'));
  wp_enqueue_script( 'script', get_bloginfo('template_url').'/js/script.js', array('plugins'));
  wp_enqueue_script( 'hyphenator', get_bloginfo('template_url').'/js/libs/hyphenator.js', array('script'));
});

add_action( 'tgmpa_register', function() {
  $plugins = array(

    // This is an example of how to include a plugin pre-packaged with a theme
    array(
      'name'             => 'TGM Example Plugin', // The plugin name
      'slug'             => 'tgm-example-plugin', // The plugin slug (typically the folder name)
      'source'           => get_stylesheet_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source
      'required'         => true, // If false, the plugin is only 'recommended' instead of required
      'version'         => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
      'force_activation'     => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
      'force_deactivation'   => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
      'external_url'       => '', // If set, overrides default API URL and points to an external URL
    ),

    // This is an example of how to include a plugin from the WordPress Plugin Repository
    array(
      'name'     => 'BuddyPress',
      'slug'     => 'buddypress',
      'required'   => false,
    ),

  );
  
  $theme_text_domain = 'gtsr';

  /**
   * Array of configuration settings. Amend each line as needed.
   * If you want the default strings to be available under your own theme domain,
   * leave the strings uncommented.
   * Some of the strings are added into a sprintf, so see the comments at the
   * end of each line for what each argument will be.
   */
  $config = array(
    'domain'           => $theme_text_domain,           // Text domain - likely want to be the same as your theme.
    'default_path'     => '',                           // Default absolute path to pre-packaged plugins
    'parent_menu_slug'   => 'themes.php',         // Default parent menu slug
    'parent_url_slug'   => 'themes.php',         // Default parent URL slug
    'menu'             => 'install-required-plugins',   // Menu slug
    'has_notices'        => true,                         // Show admin notices or not
    'is_automatic'      => false,               // Automatically activate plugins after installation or not
    'message'       => '',              // Message to output right before the plugins table
    'strings'          => array(
      'page_title'                             => __( 'Install Required Plugins', $theme_text_domain ),
      'menu_title'                             => __( 'Install Plugins', $theme_text_domain ),
      'installing'                             => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
      'oops'                                   => __( 'Something went wrong with the plugin API.', $theme_text_domain ),
      'notice_can_install_required'           => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_install_recommended'      => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_install'            => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
      'notice_can_activate_required'          => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_activate_recommended'      => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_activate'           => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
      'notice_ask_to_update'             => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_update'             => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
      'return'                                 => __( 'Return to Required Plugins Installer', $theme_text_domain ),
      'plugin_activated'                       => __( 'Plugin activated successfully.', $theme_text_domain ),
      'complete'                   => __( 'All plugins installed and activated successfully. %s', $theme_text_domain ) // %1$s = dashboard link
    )
  );
  
  // UNCOMMENT THIS WHEN READY FOR PRIME TIME
  // tgmpa( $plugins, $config );
});

/**** /ADD ACTIONS *****/


/**** ADD FILTERS *****/

add_filter('widget_text', 'do_shortcode');

/**** /ADD FILTERS *****/

if(strpos($_SERVER['SERVER_NAME'],'dev') !== FALSE) {
  define('HBGS_ENVIRONMENT', 'development');
} else if(strpos($_SERVER['SERVER_NAME'],'.gunnertechnetwork.net') !== FALSE) {
  define('HBGS_ENVIRONMENT', 'staging');
} else {
  define('HBGS_ENVIRONMENT', 'production');
}

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


function hbgs_theme_path() {
  return TEMPLATEPATH.'/';
}

function hbgs_body_classes($addition=null) {
  global $wp_query;
  
  $cats = (array) get_the_category();
  
  
  foreach($cats as $cat) {
    $classes[] = $cat->slug;
  }
  
  $classes[] = "clearfix";
  
  if (!is_front_page() ) {
    $classes[] = "not-home";
  }
  
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


function hbgs_get_the_slug() {
  global $post;
  
  if ( is_single() || is_page() ) {
    return $post->post_name;
  }
  
  return "";
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
  $category = is_array($category) && count($category) > 0 ? $category[0] : $category;
  
  if($category) {
    echo '<a href="'.get_category_link($category->cat_ID).'">'.$category->cat_name.'</a>';
  } else {
    $post_type_obj = get_post_type_object(get_post_type());
    echo '<a href="/'.$post_type_obj->name.'/">'.$post_type_obj->labels->name.'</a>';
  }
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
