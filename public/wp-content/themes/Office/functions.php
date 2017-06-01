<?php
/**
 * @package WordPress
 * @subpackage Office Theme
*/


// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) 
    $content_width = 980;

/*-----------------------------------------------------------------------------------*/
/*	Include functions
/*-----------------------------------------------------------------------------------*/
require('functions/pagination.php');
require('functions/shortcodes.php');
require('mce/shortcode-popup.php');
require('functions/breadcrumbs.php');
require('functions/custom-excerpts.php');
require('functions/meta/meta-box-class.php');
require('functions/meta/meta-box-usage.php');
require('functions/widgets/flickr-widget.php');
require('functions/widgets/testimonials.php');
require('functions/widgets/recent-portfolio.php');
require('functions/custom-editor-columns.php');


/*-----------------------------------------------------------------------------------*/
/*	Images
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'add_theme_support' ) )
	add_theme_support( 'post-thumbnails' );

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'full-size',  9999, 9999, false );
	add_image_size( 'small-thumb',  54, 54, true );
	add_image_size( 'slider',  970, 400, true );
	add_image_size( 'post-image',  660, 220, true );
	add_image_size( 'blog-thumb',  280, 92, true );
	add_image_size( 'grid-thumb',  215, 140, true );
	add_image_size( 'gallery-thumb',  205, 140, true );
	add_image_size( 'staff-thumb',  100, 100, true );
	add_image_size( 'portfolio-single',  500, 9999, false );



/*-----------------------------------------------------------------------------------*/
/*	Javascsript
/*-----------------------------------------------------------------------------------*/

function my_theme_scripts_function() {
	
	global $data; //get theme options
	
	//replace jQuery with Google hosted version
	wp_deregister_script('jquery'); 
		wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false, '1.7.1'); 
	wp_enqueue_script('jquery');	
	
	//replace jQuery UI with Google hosted version
	wp_deregister_script('jquery-ui'); 
		wp_register_script('jquery-ui', ("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"), false, '1.8.16'); 
	wp_enqueue_script('jquery-ui');
	
	// Site wide js
	wp_enqueue_script('hoverIntent', get_template_directory_uri() . '/js/jquery.hoverIntent.minified.js');
	wp_enqueue_script('superfish', get_template_directory_uri() . '/js/jquery.superfish.js');
	wp_enqueue_script('prettyphoto', get_template_directory_uri() . '/js/jquery.prettyPhoto.js');
	wp_enqueue_script('tipsy', get_template_directory_uri() . '/js/jquery.tipsy.js');
	wp_enqueue_script('fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js');
	wp_enqueue_script('flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js');
	
	//responsive
	if($data['disable_responsive'] !='disable') {
		wp_enqueue_script('uniform', get_template_directory_uri() . '/js/jquery.uniform.js');
		wp_enqueue_script('responsify', get_template_directory_uri() . '/js/responsify.init.js');
	}
	
	//home
	if(is_front_page()) {
		wp_enqueue_script('homeinit', get_template_directory_uri() . '/js/jquery.home.init.js');
		wp_enqueue_script('carouFredSel', get_template_directory_uri() . '/js/jquery.carouFredSel-5.5.0-packed.js');
	}
	
	//services
	if(is_page_template('template-services.php') || is_tax('service_cats')) {
		wp_enqueue_script('servicesinit', get_template_directory_uri() . '/js/jquery.services.init.js');
	}
	
	//staff
	if(is_page_template('template-staff.php') || is_tax('staff_departments')) {
		wp_enqueue_script('staffinit', get_template_directory_uri() . '/js/jquery.staff.init.js');
	}

	//portfolio main
	if(is_page_template('template-portfolio-with-filter.php')) {
		wp_enqueue_script('easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js');
		wp_enqueue_script('quicksand', get_template_directory_uri() . '/js/jquery.quicksand.js');
		wp_enqueue_script('quicksandinit', get_template_directory_uri() . '/js/jquery.quicksandinit.js');
	}
	
	//faqs
	if(is_page_template('template-faqs.php')) {
		wp_enqueue_script('faqsinit', get_template_directory_uri() . '/js/jquery.faqs.init.js');
		wp_enqueue_script('easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js');
		wp_enqueue_script('quicksand', get_template_directory_uri() . '/js/jquery.quicksand.js');
		wp_enqueue_script('quicksandinit', get_template_directory_uri() . '/js/jquery.quicksandinit.faqs.js');
	}
	
	//testimonials widget
	if(is_active_widget( '', '', 'office_testimonials' ) ) {
        	wp_enqueue_script('testimonials-widget', get_template_directory_uri() . '/js/jquery.testimonials.widget.js');
    }


	//js init
	wp_enqueue_script('init', get_template_directory_uri() . '/js/jquery.init.js');

}


add_action('wp_enqueue_scripts','my_theme_scripts_function');


/*-----------------------------------------------------------------------------------*/
/*Enqueue CSS
/*-----------------------------------------------------------------------------------*/
function office_enqueue_css() {
	
	global $data; //get theme options
	
	//responsive
	if($data['disable_responsive'] !='disable') {
		wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css', 'style');
	}
	
	//prettyPhoto
	wp_enqueue_style('prettyPhoto', get_template_directory_uri() . '/css/prettyPhoto.css', 'style');
	
	//css3 buttons
	wp_enqueue_style('gh-buttons', get_template_directory_uri() . '/css/gh-buttons.css', 'style');
	
}
add_action('wp_enqueue_scripts', 'office_enqueue_css');

/*-----------------------------------------------------------------------------------*/
/*	Output Custom CSS Into Header
/*-----------------------------------------------------------------------------------*/

function office_custom_css() {
	
		global $data;
		
		$custom_css ='';
		
		/**custom css field**/
		if(!empty($data['custom_css'])) {
			$custom_css .= $data['custom_css'];
		}
		
		//background
		if(!empty($data['custom_bg'])) {
			if($data['custom_bg'] !=''.get_template_directory_uri().'/images/bg/bg_20.png') {
				$custom_css .= 'body{background-image: url('.$data['custom_bg'].');}';
			} else {
				$custom_css .= 'body{background-image: none;}';
			}
		}
		if(!empty($data['background_color'])) {
			$custom_css .= 'body{background-color: '.$data['background_color'].';}';
		}
		
		//background pattern
		if($data['disable_background_pattern'] == 'disable') {
			$custom_css .= '#header, .container{background-image: none !important;}';
		}
	
		//highlight color
		if(!empty($data['highlight_color'])) {
			$custom_css .= 'a#top-bar-callout, #navigation .current-menu-item > a, #service-tabs li.active a, .heading a:hover span, .post-date, #carousel-pagination a.selected  { background-color: '.$data['highlight_color'].'; }';
			$custom_css .= '.office-flickr-widget a:hover, .widget-recent-portfolio a:hover, .home-entry a:hover img, .loop-entry-thumbnail a:hover img, ul.filter a.active, .gallery-photo a:hover img{ border-color: '.$data['highlight_color'].' !important;}';
		}	
		
		//homepage tagline link color
		if(!empty($data['home_tagline_link_color'])) {
			$custom_css .= '#home-tagline a{color: '.$data['home_tagline_link_color'].';border-color: '.$data['home_tagline_link_color'].';}';
		}
		
		//navigation color
		if(!empty($data['nav_bg_color'])) {
			$custom_css .= '#navigation, .sf-menu, #navigation a, #navigation .selector option{background-color: '.$data['nav_bg_color'].' !important;}';
		}
		if(!empty($data['nav_hover_color'])) {
			$custom_css .= '#navigation a:hover{background-color: '.$data['nav_hover_color'].' !important;}';
		}
		if(!empty($data['nav_link_color'])) {
			$custom_css .= '#navigation a{color: '.$data['nav_link_color'].' !important;}';
		}
		if(!empty($data['nav_hover_color'])) {
			$custom_css .= '#navigation a:hover{background-color: '.$data['nav_hover_color'].' !important;}';
		}
		if(!empty($data['nav_current_background_color'])) {
			$custom_css .= '#navigation .current-menu-item > a{ background-color: '.$data['nav_current_background_color'].' !important;}';
		}
		if(!empty($data['nav_current_link_color'])) {
			$custom_css .= '#navigation .current-menu-item > a{ color: '.$data['nav_current_link_color'].' !important;}';
		}
		if(!empty($data['nav_light_border_color'])) {
			$custom_css .= '.sf-menu { border-color: '.$data['nav_light_border_color'].' !important;} .sf-menu a { border-left-color: '.$data['nav_light_border_color'].' !important;}.sf-menu ul a{ border-top-color: '.$data['nav_light_border_color'].' !important;}';
		}
		if(!empty($data['nav_dark_border_color'])) {
			$custom_css .= '.sf-menu a { border-right-color: '.$data['nav_dark_border_color'].' !important;}.sf-menu ul a{ border-bottom-color: '.$data['nav_dark_border_color'].' !important;}.sf-menu ul, .sf-menu ul ul{border-top-color: '.$data['nav_dark_border_color'].' !important;}';
		}
		
		
		//header styles
		if($data['header_style'] == 'two') {
			$custom_css .= '#header{margin-bottom: 0px;border-bottom: none;}';
		}
		if($data['header_style'] == 'three') {
			$custom_css .= '#header.header-two{margin-top: 30px;}#header{margin-bottom: 0px;border-bottom: none;}#header.header-one{ margin-top: 74px !important;}';
		}
		if($data['header_style'] == 'four') {
			$custom_css .= '#header.header-two{margin-top: 30px;}#header.header-one{ margin-top: 74px !important;}';
		}
		
		//menu border
		if($data['disable_menu_last_border'] == 'disable') {
			$custom_css .= '.sf-menu li:last-child a, .sf-menu{ border-right: none; }';
		}
		
		//sidebar location
		if($data['sidebar_position'] == 'left') {
			$custom_css .= '#sidebar {float: left;} .post{ float: right;}';
		}
		
		/**echo all css**/
		$css_output = "<!-- Custom CSS -->\n<style type=\"text/css\">\n" . $custom_css . "\n</style>";
		
		if(!empty($custom_css)) {
			echo $css_output;
		}
}

add_action('wp_head', 'office_custom_css');


/*-----------------------------------------------------------------------------------*/
/*	Sidebars
/*-----------------------------------------------------------------------------------*/

//Register Sidebars
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Sidebar',
		'id' => 'sidebar',
		'description' => 'Widgets in this area will be shown in the sidebar.',
		'before_widget' => '<div class="sidebar-box %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4><span>',
		'after_title' => '</span></h4>',
));
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Footer Left',
		'id' => 'footer-left',
		'description' => 'Widgets in this area will be shown in the footer left area.',
		'before_widget' => '<div class="footer-widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
));
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Footer Middle',
		'id' => 'footer-middle',
		'description' => 'Widgets in this area will be shown in the footer middle area.',
		'before_widget' => '<div class="footer-widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
));
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Footer Right',
		'id' => 'footer-right',
		'description' => 'Widgets in this area will be shown in the footer right area.',
		'before_widget' => '<div class="footer-widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
));



/*-----------------------------------------------------------------------------------*/
/*	Custom Post Types & Taxonomies
/*-----------------------------------------------------------------------------------*/

add_action( 'init', 'create_post_types' );
function create_post_types() {

//slider post type
	register_post_type( 'Slides',
		array(
		  'labels' => array(
			'name' => __( 'HP Slides', 'office' ),
			'singular_name' => __( 'Slide', 'office' ),		
			'add_new' => _x( 'Add New', 'Slide', 'office' ),
			'add_new_item' => __( 'Add New Slide', 'office' ),
			'edit_item' => __( 'Edit Slide', 'office' ),
			'new_item' => __( 'New Slide', 'office' ),
			'view_item' => __( 'View Slide', 'office' ),
			'search_items' => __( 'Search Slides', 'office' ),
			'not_found' =>  __( 'No Slides found', 'office' ),
			'not_found_in_trash' => __( 'No Slides found in Trash', 'office' ),
			'parent_item_colon' => ''
			
		  ),
		  'public' => true,
		  'supports' => array('title','thumbnail'),
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'slides' ),
		  'show_in_nav_menus' => false,
		  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-slider.png',
		)
	  );
	  
//hp highlights
	register_post_type( 'hp_highlights',
		array(
		  'labels' => array(
			'name' => __( 'HP Highlights', 'office' ),
			'singular_name' => __( 'Highlight', 'office' ),		
			'add_new' => _x( 'Add New', 'Highlight', 'office' ),
			'add_new_item' => __( 'Add New Highlight', 'office' ),
			'edit_item' => __( 'Edit Highlight', 'office' ),
			'new_item' => __( 'New Highlight', 'office' ),
			'view_item' => __( 'View Highlight', 'office' ),
			'search_items' => __( 'Search Highlights', 'office' ),
			'not_found' =>  __( 'No Highlights found', 'office' ),
			'not_found_in_trash' => __( 'No Highlights found in Trash', 'office' ),
			'parent_item_colon' => ''
			
		  ),
		  'public' => true,
		  'supports' => array('title','editor','thumbnail', 'revisions'),
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'hp-highlights' ),
		  'show_in_nav_menus' => false,
		  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-highlights.png',
		)
	  );

//portfolio post type
	register_post_type( 'Portfolio',
		array(
		  'labels' => array(
			'name' => __( 'Portfolio', 'office' ),
			'singular_name' => __( 'Portfolio', 'office' ),		
			'add_new' => _x( 'Add New', 'Portfolio Project', 'office' ),
			'add_new_item' => __( 'Add New Portfolio Project', 'office' ),
			'edit_item' => __( 'Edit Portfolio Project', 'office' ),
			'new_item' => __( 'New Portfolio Project', 'office' ),
			'view_item' => __( 'View Portfolio Project', 'office' ),
			'search_items' => __( 'Search Portfolio Projects', 'office' ),
			'not_found' =>  __( 'No Portfolio Projects found', 'office' ),
			'not_found_in_trash' => __( 'No Portfolio Projects found in Trash', 'office' ),
			'parent_item_colon' => ''
			
		  ),
		  'public' => true,
		  'supports' => array('title','editor','thumbnail', 'revisions'),
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'portfolio' ),
		  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-portfolio.png',
		)
	  );
  
// Staff Post Type
register_post_type( 'staff',
    array(
      'labels' => array(
        'name' => __( 'Staff','office' ),
        'singular_name' => __( 'Staff','office' ),		
		'add_new' => _x( 'Add New', 'Staff Member','office' ),
		'add_new_item' => __( 'Add New Staff Member','office' ),
		'edit_item' => __( 'Edit Staff Member','office' ),
		'new_item' => __( 'New Staff Member','office' ),
		'view_item' => __( 'View Staff Member','office' ),
		'search_items' => __( 'Search Staff Members','office' ),
		'not_found' =>  __( 'No Staff Members found','office' ),
		'not_found_in_trash' => __( 'No Staff Members found in Trash','office' ),
		'parent_item_colon' => ''
		
      ),
      'public' => true,
	  'supports' => array('title','thumbnail','editor', 'revisions'),
	  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-staff.png',
	  'query_var' => true,
	  'rewrite' => array( 'slug' => 'staff' ),
    )
);
  


// Testimonials
register_post_type( 'testimonials',
    array(
      'labels' => array(
        'name' => __( 'Testimonials','office' ),
        'singular_name' => __( 'Testimonial','office' ),		
		'add_new' => _x( 'Add New', 'Testimonial','office' ),
		'add_new_item' => __( 'Add New Testimonial','office' ),
		'edit_item' => __( 'Edit Testimonial','office' ),
		'new_item' => __( 'New Testimonial','office' ),
		'view_item' => __( 'View Testimonial','office' ),
		'search_items' => __( 'Search Testimonials','office' ),
		'not_found' =>  __( 'No Testimonials found','office' ),
		'not_found_in_trash' => __( 'No Testimonials found in Trash','office' ),
		'parent_item_colon' => ''
		
      ),
      'public' => true,
    'menu_position' => 5,
	  'supports' => array('title','editor', 'revisions'),
	  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-testimonials.png',
	  'query_var' => true,
	  'rewrite' => array( 'slug' => 'testimonials' ),
    )
  );

// Services Post Type
register_post_type( 'services',
    array(
      'labels' => array(
        'name' => __( 'Services','office' ),
        'singular_name' => __( 'Service','office' ),		
		'add_new' => _x( 'Add New', 'Service','office' ),
		'add_new_item' => __( 'Add New Service','office' ),
		'edit_item' => __( 'Edit Service','office' ),
		'new_item' => __( 'New Service','office' ),
		'view_item' => __( 'View Service','office' ),
		'search_items' => __( 'Search Services','office' ),
		'not_found' =>  __( 'No Services found','office' ),
		'not_found_in_trash' => __( 'No Services found in Trash','office' ),
		'parent_item_colon' => ''
		
      ),
      'public' => true,
      'show_in_menu' => true,
      'show_ui' => true,
      'menu_position' => 5,
	  'supports' => array('title','editor','revisions'),
	  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-services.png',
	  'query_var' => true,
	  'rewrite' => array( 'slug' => 'services' ),
    )
);

  
// Faqs
register_post_type( 'faqs',
    array(
      'labels' => array(
        'name' => __( 'FAQs','office' ),
        'singular_name' => __( 'FAQ','office' ),		
		'add_new' => _x( 'Add New', 'FAQ','office' ),
		'add_new_item' => __( 'Add New FAQ','office' ),
		'edit_item' => __( 'Edit FAQ','office' ),
		'new_item' => __( 'New FAQ','office' ),
		'view_item' => __( 'View FAQ','office' ),
		'search_items' => __( 'Search FAQs','office' ),
		'not_found' =>  __( 'No FAQs found','office' ),
		'not_found_in_trash' => __( 'No FAQs found in Trash','office' ),
		'parent_item_colon' => ''
		
      ),
      'public' => true,
	  'supports' => array('title','editor', 'revisions'),
	  'menu_icon' => get_template_directory_uri() . '/images/admin/icon-faqs.png',
	  'query_var' => true,
	  'rewrite' => array( 'slug' => 'faqs' ),
    )
  );

}


// Add taxonomies
add_action( 'init', 'create_taxonomies' );

//create taxonomies
function create_taxonomies() {
	
	// portfolio taxonomies
	$portfolio_cat_labels = array(
		'name' => __( 'Portfolio Categories', 'office' ),
		'singular_name' => __( 'Portfolio Category', 'office' ),
		'search_items' =>  __( 'Search Portfolio Categories', 'office' ),
		'all_items' => __( 'All Portfolio Categories', 'office' ),
		'parent_item' => __( 'Parent Portfolio Category', 'office' ),
		'parent_item_colon' => __( 'Parent Portfolio Category:', 'office' ),
		'edit_item' => __( 'Edit Portfolio Category', 'office' ),
		'update_item' => __( 'Update Portfolio Category', 'office' ),
		'add_new_item' => __( 'Add New Portfolio Category', 'office' ),
		'new_item_name' => __( 'New Portfolio Category Name', 'office' ),
		'choose_from_most_used'	=> __( 'Choose from the most used portfolio categories', 'office' )
	); 	

	register_taxonomy('portfolio_cats','portfolio',array(
		'hierarchical' => false,
		'labels' => $portfolio_cat_labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'portfolio-category' ),
	));
	
	
	// staff taxonomies
	$staff_cat_labels = array(
		'name' => __( 'Staff Departments', 'office' ),
		'singular_name' => __( 'Staff Department', 'office' ),
		'search_items' =>  __( 'Search Staff Departments', 'office' ),
		'all_items' => __( 'All Staff Departments', 'office' ),
		'parent_item' => __( 'Parent Staff Department', 'office' ),
		'parent_item_colon' => __( 'Parent Staff Department:', 'office' ),
		'edit_item' => __( 'Edit Staff Department', 'office' ),
		'update_item' => __( 'Update Staff Department', 'office' ),
		'add_new_item' => __( 'Add New Staff Department', 'office' ),
		'new_item_name' => __( 'New Staff Department Name', 'office' ),
		'choose_from_most_used'	=> __( 'Choose from the most used staff departments', 'office' )
	); 	

	register_taxonomy('staff_departments','staff',array(
		'hierarchical' => false,
		'labels' => $staff_cat_labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'department' ),
	));
	
	
	// FAQ taxonomies
	$faqs_cat_labels = array(
		'name' => __( 'FAQ Category', 'office' ),
		'singular_name' => __( 'FAQ Category', 'office' ),
		'search_items' =>  __( 'Search FAQ Categories', 'office' ),
		'all_items' => __( 'All FAQ Categories', 'office' ),
		'parent_item' => __( 'Parent FAQ Category', 'office' ),
		'parent_item_colon' => __( 'Parent FAQ Category:', 'office' ),
		'edit_item' => __( 'Edit FAQ Category', 'office' ),
		'update_item' => __( 'Update FAQ Category', 'office' ),
		'add_new_item' => __( 'Add New FAQ Category', 'office' ),
		'new_item_name' => __( 'New FAQ Category', 'office' ),
		'choose_from_most_used'	=> __( 'Choose from the most used FAQ categories', 'office' )
	); 	

	register_taxonomy('faqs_cats','faqs',array(
		'hierarchical' => false,
		'labels' => $faqs_cat_labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'faqs-category' ),
	));
	
	// Service taxonomies
	$service_cat_labels = array(
		'name' => __( 'Service Category', 'office' ),
		'singular_name' => __( 'Service Category', 'office' ),
		'search_items' =>  __( 'Search Service Categories', 'office' ),
		'all_items' => __( 'All Service Categories', 'office' ),
		'parent_item' => __( 'Parent Service Category', 'office' ),
		'parent_item_colon' => __( 'Parent Service Category:', 'office' ),
		'edit_item' => __( 'Edit Service Category', 'office' ),
		'update_item' => __( 'Update Service Category', 'office' ),
		'add_new_item' => __( 'Add New Service Category', 'office' ),
		'new_item_name' => __( 'New Service Category', 'office' ),
		'choose_from_most_used'	=> __( 'Choose from the most used Service categories', 'office' )
	); 	

	register_taxonomy('service_cats','services',array(
		'hierarchical' => false,
		'labels' => $service_cat_labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'service-category' ),
	));
	
}


/*-----------------------------------------------------------------------------------*/
/*	Post Type Pagination
/*-----------------------------------------------------------------------------------*/

// Set number of posts per page for taxonomy pages
$option_posts_per_page = get_option( 'posts_per_page' );
add_action( 'init', 'my_modify_posts_per_page', 0);
function my_modify_posts_per_page() {
    add_filter( 'option_posts_per_page', 'my_option_posts_per_page' );
}
function my_option_posts_per_page( $value ) {
	global $data;
	global $option_posts_per_page;
	
	// Get theme panel admin
	if($data['portfolio_cat_pagination']) {
		$portfolio_posts_per_page = $data['portfolio_cat_pagination'];
		} else {
			$portfolio_posts_per_page = '-1';
			}
	
    if (is_tax( 'portfolio_cats') ) {
        return $portfolio_posts_per_page;
    }
	if (is_tax( 'staff_departments')) {
		return -1;
	}
	else {
        return $option_posts_per_page;
    }
}


/*-----------------------------------------------------------------------------------*/
/*	Custom Login Logo
/*-----------------------------------------------------------------------------------*/
function office_custom_login_logo() {
	
	global $data;
	
	if($data['custom_login_logo'] !='') {
		$custom_login_logo_css .= '<style type="text/css">';
		$custom_login_logo_css .= 'h1 a {';
		$custom_login_logo_css .= 'background-image:url('. $data['custom_login_logo'] .') !important;';
		if($data['custom_login_logo_height']) {
			$custom_login_logo_css .= 'height: '.$data['custom_login_logo_height'].'px !important';
		}
		$custom_login_logo_css .= '}</style>';
		
		echo $custom_login_logo_css;
	}
}

function office_wp_login_title() {
	echo get_option('blogname');
}
function office_wp_login_url() {
	echo home_url();
}

add_action('login_head', 'office_custom_login_logo');
add_filter('login_headerurl', 'office_wp_login_url');
add_filter('login_headertitle', 'office_wp_login_title');


/*-----------------------------------------------------------------------------------*/
/*	Add taxonomy filter
/*-----------------------------------------------------------------------------------*/
function pippin_add_taxonomy_filters() {
	global $typenow;

	if( $typenow == 'services' || $typenow == 'portfolio' || $typenow == 'staff' || $typenow == 'faqs' ){
		if( $typenow == 'portfolio') { $taxonomies = array('portfolio_cats'); }
		if( $typenow == 'services') { $taxonomies = array('service_cats'); }
		if( $typenow == 'staff') { $taxonomies = array('staff_departments'); }
		if( $typenow == 'faqs') { $taxonomies = array('faqs_cats'); }
		
 
		foreach ($taxonomies as $tax_slug) {
			$tax_obj = get_taxonomy($tax_slug);
			$tax_name = $tax_obj->labels->name;
			$terms = get_terms($tax_slug);
			if(count($terms) > 0) {
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>All Categories</option>";
				foreach ($terms as $term) { 
					echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>'; 
				}
				echo "</select>";
			}
		}
	}
}
add_action( 'restrict_manage_posts', 'pippin_add_taxonomy_filters' );



/*-----------------------------------------------------------------------------------*/
/*	Other functions
/*-----------------------------------------------------------------------------------*/

//enable more post editor buttons
function enable_more_buttons($buttons) {
  $buttons[] = 'fontselect';
  $buttons[] = 'fontsizeselect';
  return $buttons;
}
add_filter("mce_buttons_3", "enable_more_buttons");

// Limit Post Word Count
function new_excerpt_length($length) {
	
	global $data;
	return $data['blog_excerpt'];
}
add_filter('excerpt_length', 'new_excerpt_length');

//Replace Excerpt Link
function new_excerpt_more($more) {
	global $post;
	return '...<a href="'. get_permalink($post->ID) . '">'.__('read more','office').' &rarr;</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');
}

// Enable Custom Background
add_custom_background();

// register navigation menus
register_nav_menus(
	array(
	'top_menu' => __('Top','office'),
	'main_menu' => __('Main','office'),
	'footer_menu' => __('Footer','office')
	)
);

/// add home link to menu
function home_page_menu_args( $args ) {
$args['show_home'] = true;
return $args;
}
add_filter( 'wp_page_menu_args', 'home_page_menu_args' );

// menu fallback
function default_menu() {
	require_once (TEMPLATEPATH . '/includes/default-menu.php');
}


// Localization Support
load_theme_textdomain( 'office', TEMPLATEPATH.'/lang' );

// functions run on activation --> important flush to clear rewrites
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
	$wp_rewrite->flush_rules();
}


/*-----------------------------------------------------------------------------------*/
// Options Framework
/*-----------------------------------------------------------------------------------*/

// Paths to admin functions
define('ADMIN_PATH', STYLESHEETPATH . '/admin/');
define('ADMIN_DIR', get_template_directory_uri() . '/admin/');
define('LAYOUT_PATH', ADMIN_PATH . '/layouts/');


// You can mess with these 2 if you wish.
$themedata = get_theme_data(STYLESHEETPATH . '/style.css');
define('THEMENAME', $themedata['Name']);
define('OPTIONS', 'of_options'); // Name of the database row where your options are stored
define('BACKUPS', 'of_options'); // Name of the database row where your backup options are stored


// Build Options
require_once ('admin/admin-interface.php'); // Admin Interfaces 
require_once ('admin/theme-options.php'); // Options panel settings and custom settings
require_once ('admin/admin-functions.php'); // Theme actions based on options settings
require_once ('admin/medialibrary-uploader.php'); // Media Library Uploader
?>