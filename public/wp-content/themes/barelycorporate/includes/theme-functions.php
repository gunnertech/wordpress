<?php
/*-----------------------------------------------------------------------------------*/
/* Theme Functions
/*
/* In premium Theme Blvd themes, this file will contains everything that's needed
/* to modify the framework's default setting to construct the current theme.
/*-----------------------------------------------------------------------------------*/

// Define theme constants
define( 'TB_THEME_ID', 'barelycorporate' );
define( 'TB_THEME_NAME', 'BarelyCorporate' );

// Modify framework's theme options
require_once( get_template_directory() . '/includes/options.php' );

// Automatic updates
include_once( get_template_directory() . '/includes/updates.php' );

/**
 * Barely Corporate Setup
 *
 * @since 3.0.0
 */
function barelycorporate_setup() {

	// Custom background support
	add_theme_support( 'custom-background' ); // Only supported with WP v3.4+, and only works with boxed layout

}
add_action( 'after_setup_theme', 'barelycorporate_setup' );

/**
 * Barely Corporate CSS Files
 *
 * @since 3.0.0
 */
function barelycorporate_css() {

	// For plugins not inserting their scripts/stylesheets
	// correctly in the admin.
	if ( is_admin() ) {
		return;
	}

	// Get theme version for stylesheets
	$theme_data = wp_get_theme( get_template() );
	$theme_version = $theme_data->get('Version');

	// Get stylesheet API
	$api = Theme_Blvd_Stylesheets_API::get_instance();

	wp_register_style( 'themeblvd_barelycorporate', get_template_directory_uri() . '/assets/css/theme.min.css', $api->get_framework_deps(), $theme_version );
	wp_register_style( 'themeblvd_dark', get_template_directory_uri() . '/assets/css/dark.min.css', array('themeblvd_barelycorporate'), $theme_version );
	wp_register_style( 'themeblvd_responsive', get_template_directory_uri() . '/assets/css/responsive.min.css', array('themeblvd_barelycorporate'), $theme_version );
	wp_register_style( 'themeblvd_ie', get_template_directory_uri() . '/assets/css/ie.css', array('themeblvd_barelycorporate'), $theme_version );
	wp_register_style( 'themeblvd_theme', get_stylesheet_uri(), array('themeblvd_barelycorporate'), $theme_version );

	// Enqueue CSS files as needed
	wp_enqueue_style( 'themeblvd_barelycorporate' );

	if ( themeblvd_get_option( 'content_color' ) == 'content_dark' ) {
		wp_enqueue_style( 'themeblvd_dark' );
	}

	if ( themeblvd_supports( 'display', 'responsive' ) ) {
		wp_enqueue_style( 'themeblvd_responsive' );
	}

	// IE Styles
	// $GLOBALS['wp_styles']->add_data( 'themeblvd_ie', 'conditional', 'lt IE 9' ); // Add IE conditional
	wp_enqueue_style( 'themeblvd_ie' );

	// Inline styles from theme options --
	// Note: Using themeblvd_ie as $handle because it's the only
	// constant stylesheet just before style.css
	wp_add_inline_style( 'themeblvd_ie', barelycorporate_styles() );

	// style.css -- This is mainly for WP continuity and Child theme modifications.
	wp_enqueue_style( 'themeblvd_theme' );

	// Level 3 client API-added styles
	$api->print_styles(3);

}
add_action( 'wp_enqueue_scripts', 'barelycorporate_css', 20 );

if ( !function_exists( 'barelycorporate_styles' ) ) :
/**
 * Barely Corporate Styles
 *
 * @since 3.0.0
 *
 * @return string $styles Inline styles for wp_add_inline_style()
 */
function barelycorporate_styles() {
	$primary_color = themeblvd_get_option('primary_color');
	$textures = themeblvd_get_textures();
	$header_texture = themeblvd_get_option('header_texture');
	$menu_accent = themeblvd_get_option('menu_accent');
	$menu_accent_text = themeblvd_get_option('menu_accent_text');
	$menu_accent_home = themeblvd_text_color( $menu_accent );
	$custom_styles = themeblvd_get_option( 'custom_styles' );
	$body_font = themeblvd_get_option( 'typography_body' );
	$header_font = themeblvd_get_option( 'typography_header' );
	$special_font = themeblvd_get_option( 'typography_special' );
	themeblvd_include_google_fonts( $body_font, $header_font, $special_font );
	ob_start();
	?>
	/* Fonts */
	html,
	body {
		font-family: <?php echo themeblvd_get_font_face( $body_font ); ?>;
		font-size: <?php echo themeblvd_get_font_size( $body_font ); ?>;
		font-style: <?php echo themeblvd_get_font_style( $body_font ); ?>;
		font-weight: <?php echo themeblvd_get_font_weight( $body_font ); ?>;
	}
	h1, h2, h3, h4, h5, h6, .slide-title {
		font-family: <?php echo themeblvd_get_font_face( $header_font ); ?>;
		font-style: <?php echo themeblvd_get_font_style( $header_font ); ?>;
		font-weight: <?php echo themeblvd_get_font_weight( $header_font ); ?>;
	}
	.slide-title,
	.tb-slogan .slogan-text,
	.element-tweet,
	.special-font {
		font-family: <?php echo themeblvd_get_font_face( $special_font ); ?>;
		font-style: <?php echo themeblvd_get_font_style( $special_font ); ?>;
		font-weight: <?php echo themeblvd_get_font_weight( $special_font ); ?>;
	}
	/* Primary Color */
	#top,
	.tb-btn-gradient .btn-default,
	.tb-btn-gradient input[type="submit"],
	.tb-btn-gradient input[type="reset"],
	.tb-btn-gradient input[type="button"],
	button {
		background-color: <?php echo $primary_color; ?>;
		background-image: none;
		border-color: <?php echo themeblvd_adjust_color( $primary_color, 40 ); ?>; /* Will get overrident for #top below */
		color: #ffffff;
		text-shadow: none;
	}
	.tb-btn-gradient .btn-default:hover,
	.tb-btn-gradient .btn-default:active,
	.tb-btn-gradient .btn-default:focus,
	.tb-btn-gradient input[type="submit"]:hover,
	.tb-btn-gradient input[type="submit"]:active,
	.tb-btn-gradient input[type="submit"]:focus,
	.tb-btn-gradient input[type="reset"]:hover,
	.tb-btn-gradient input[type="reset"]:active,
	.tb-btn-gradient input[type="reset"]:focus,
	.tb-btn-gradient input[type="button"]:hover,
	.tb-btn-gradient input[type="button"]:active,
	.tb-btn-gradient input[type="button"]:focus,
	.tb-btn-gradient button:hover,
	.tb-btn-gradient button:active,
	.tb-btn-gradient button:focus {
		background-color: <?php echo themeblvd_adjust_color( $primary_color, 20 ); ?>;
		background-image: none;
		border-color: <?php echo themeblvd_adjust_color( $primary_color, 40 ); ?>;
	}
	/* Header Texture */
	#top {
		<?php if ( $header_texture == 'none' ) : ?>
		background-image: none;
		<?php else : ?>
		background-image: url(<?php echo $textures[$header_texture]['url']; ?>);
		background-position: <?php echo $textures[$header_texture]['position']; ?>;
		background-repeat: <?php echo $textures[$header_texture]['repeat']; ?>;
		<?php endif; ?>
	}
	/* Menu */
	#top,
	#access ul ul {
		border-color: <?php echo themeblvd_adjust_color( $menu_accent, 25 ); ?>;
	}
	#top #branding {
		border-color: <?php echo $menu_accent; ?>;
	}
	#access li a {
		color: <?php echo themeblvd_get_option('menu_text'); ?>;
	}
	#access li a:hover,
	#access ul ul {
		background-color: <?php echo $menu_accent; ?>;
		color: <?php echo $menu_accent_text; ?>;
	}
	<?php if ( $menu_accent_home == '#ffffff' ) : ?>
		#access li.home a:hover {
			background-position: 0 -120px;
		}
		<?php if ( themeblvd_supports( 'display', 'responsive') ) : ?>
			@media (max-width: 992px) {
				#access li.home a:hover {
					background-position: 0 -90px;
				}
			}
		<?php endif; ?>
	<?php else : ?>
		#access li.home a:hover {
			background-position: 0 -80px;
		}
		<?php if ( themeblvd_supports( 'display', 'responsive') ) : ?>
			@media (max-width: 992px) {
				#access li.home a:hover {
					background-position: 0 -60px;
				}
			}
		<?php endif; ?>
	<?php endif; ?>
	#access li li a {
		color: <?php echo $menu_accent_text; ?>;
	}
	#access li li a:hover {
		background-color: <?php echo themeblvd_adjust_color( $menu_accent, 10 ) ?>;
	}
	/* Link Colors */
	a {
		color: <?php echo themeblvd_get_option('link_color'); ?>;
	}
	a:hover {
		color: <?php echo themeblvd_get_option('link_hover_color'); ?>;
	}
	#branding .header_logo .tb-text-logo:hover,
	.entry-title a:hover,
	.widget ul li a:hover,
	#breadcrumbs a:hover,
	.tags a:hover,
	.entry-meta a:hover {
		color: <?php echo themeblvd_get_option('link_hover_color'); ?> !important;
	}
	<?php
	// Compress inline styles
	$styles = themeblvd_compress( ob_get_clean() );

	// Add in user's custom CSS
	if ( $custom_styles ) {
		$styles .= "\n/* User Custom CSS */\n";
		$styles .= $custom_styles;
	}

	return $styles;
}
endif;

/**
 * Barely Corporate Scripts
 *
 * @since 3.0.0
 */
function barelycorporate_scripts() {

	global $themeblvd_framework_scripts;

	// Theme-specific script
	wp_enqueue_script( 'themeblvd_theme', get_template_directory_uri() . '/assets/js/barelycorporate.js', $themeblvd_framework_scripts, '4.0.0', true );

}
add_action( 'wp_enqueue_scripts', 'barelycorporate_scripts' );

/**
 * Barely Corporate Google Fonts
 *
 * If any fonts need to be included from Google based
 * on the theme options, here's where we do it.
 *
 * @since 3.0.0
 */
function barelycorporate_include_fonts() {
	themeblvd_include_google_fonts(
		themeblvd_get_option('typography_body'),
		themeblvd_get_option('typography_header'),
		themeblvd_get_option('typography_special')
	);
}
add_action( 'wp_head', 'barelycorporate_include_fonts', 5 );

/**
 * Barely Corporate Body Classes
 *
 * Here we filter WordPress's default body_class()
 * function to include necessary classes for Main
 * Styles selected in Theme Options panel.
 *
 * @since 3.0.0
 */
function barelycorporate_body_class( $classes ) {
	$classes[] = themeblvd_get_option( 'layout_shape' );
	$classes[] = themeblvd_get_option( 'content_color' );
	$classes[] = themeblvd_get_option( 'social_align' );
	$classes[] = themeblvd_get_option( 'logo_align' );
	$classes[] = themeblvd_get_option( 'menu_align' );
	$classes[] = themeblvd_get_option( 'menu_style' );
	$classes[] = themeblvd_get_option( 'mobile_nav' );
	return $classes;
}
add_filter( 'body_class', 'barelycorporate_body_class' );

/*-----------------------------------------------------------------------------------*/
/* Add Sample Layout
/*
/* Here we add a sample layout to the layout builder's sample layouts.
/*-----------------------------------------------------------------------------------*/

/**
 * Add sample layouts to Layout Builder plugin.
 *
 * @since 4.0.0
 */
function barelycorporate_sample_layouts() {
	$elements = array(
		array(
			'type' => 'slider',
			'location' => 'featured'
		),
		array(
			'type' => 'slogan',
			'location' => 'featured',
			'defaults' => array(
				'slogan' => 'The all-new Barely Corporate 3 has arrived and it\'s time to check your feet because we may have just rocked your socks off.',
	            'button' => 1,
	            'button_text' => 'Grab it Today!',
	            'button_color' => 'default',
	            'button_url' => 'http://www.google.com',
	            'button_target' => '_blank'
			)
		),
		array(
			'type' => 'columns',
			'location' => 'featured',
			'defaults' => array(
	            'setup' => array(
					'num' => '3',
					'width' => array(
						'2' => 'grid_6-grid_6',
						'3' => 'grid_4-grid_4-grid_4',
						'4' => 'grid_3-grid_3-grid_3-grid_3',
						'5' => 'grid_fifth_1-grid_fifth_1-grid_fifth_1-grid_fifth_1-grid_fifth_1'
					)
				),
	            'col_1' => array(
					'type' => 'raw',
					'page' => null,
					'raw' => "<h3>A WordPress Experience</h3>\n<p><img src=\"http://themeblvd.com/demo/assets/barely-corporate/barelycorporate_layout_1.png\" class=\"pretty\" /></p>\n<p>Utilizing the Theme Blvd Framework, Barely Corporate provides a WordPress experience like you've never experienced with things like the Layout Builder.</p>\n[button link=\"http://google.com\" color=\"default\"]Learn More[/button]",
					'raw_format' => 0
				),
	            'col_2' => array(
					'type' => 'raw',
					'page' => null,
					'raw' => "<h3>Responsive Design</h3>\n<p><img src=\"http://themeblvd.com/demo/assets/barely-corporate/barelycorporate_layout_2.png\" class=\"pretty\" /></p>\n<p>The entire Theme Blvd framework was built from the ground up with the intention of making sure all of its themes display gracefully no matter where you view them.</p>\n[button link=\"http://google.com\" color=\"default\"]Learn More[/button]",
					'raw_format' => 0
				),
	            'col_3' => array(
					'type' => 'raw',
					'page' => null,
					'raw' => "<h3>HTML5 and CSS3</h3>\n<p><img src=\"http://themeblvd.com/demo/assets/barely-corporate/barelycorporate_layout_3.png\" class=\"pretty\" /></p>\n<p>Many themes around the community are marketing themselves with the HTML5 emblem, but Breakout is truly built to give you the most modern web experience possible.</p>\n[button link=\"http://google.com\" color=\"default\"]Learn More[/button]",
					'raw_format' => 0
				)
	        )
		),
		array(
			'type' => 'content',
			'location' => 'primary',
			'defaults' => array(
	            'source' => 'raw',
				'page_id' => null,
				'raw_content' => "<h2>Be careful or we just might rock your socks off.</h2>\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n\n[one_half]\n<h4>Lorem ipsum dolor sit</h4>\n<p>[icon image=\"clock\" align=\"left\"]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n[/one_half]\n\n[one_half last]\n<h4>Lorem ipsum dolor sit</h4>\n<p>[icon image=\"pie_chart\" align=\"left\"]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n[/one_half]\n[clear]\n\n[one_half]\n<h4>Lorem ipsum dolor sit</h4>\n<p>[icon image=\"analytics\" align=\"left\"]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n[/one_half]\n\n[one_half last]\n<h4>Lorem ipsum dolor sit</h4>\n<p>[icon image=\"support\" align=\"left\"]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n[/one_half]\n[clear]",
				'raw_format' => 0

	        )
		)
	);
	themeblvd_add_sample_layout( 'barelycorporate', 'Barely Corporate Homepage', get_template_directory_uri().'/assets/images/sample-barelycorporate.png', 'sidebar_right', $elements );

}
add_action( 'after_setup_theme', 'barelycorporate_sample_layouts' );

/*-----------------------------------------------------------------------------------*/
/* Theme Blvd Filters
/*
/* Here we can take advantage of modifying anything in the framework that is
/* filterable.
/*-----------------------------------------------------------------------------------*/

/**
 * Theme Blvd Setup
 *
 * @since 3.0.0
 */
function barelycorporate_global_config( $config ) {

	// If user turned off responsive CSS, then
	// filter the global config that applies
	// this throughout the framework.
	if ( themeblvd_get_option( 'responsive_css' ) == 'false' ) {
		$config['display']['responsive'] = false;
	}

	return $config;
}
add_filter( 'themeblvd_global_config', 'barelycorporate_global_config' );

/**
 * Image Sizes
 *
 * @since 3.0.0
 */
function barelycorporate_image_sizes( $sizes ) {
	$sizes['slider-large']['width'] = 922;
	$sizes['slider-large']['height'] = 326;
	$sizes['slider-staged']['width'] = 553;
	$sizes['slider-staged']['height'] = 326;
	return $sizes;
}
add_filter( 'themeblvd_image_sizes', 'barelycorporate_image_sizes' );

/**
 * Default dark text color
 *
 * @since 3.0.0
 */
function barelycorporate_dark_font( $color ) {
	$color = '#666666';
	return $color;
}
add_filter( 'themeblvd_dark_font', 'barelycorporate_dark_font' );

/**
 * De-register footer navigation for this theme
 *
 * @since 3.0.0
 */
function barelycorporate_nav_menus( $menus ) {
	unset( $menus['footer'] );
	return $menus;
}
add_filter( 'themeblvd_nav_menus', 'barelycorporate_nav_menus' );

/**
 * Theme Blvd WPML Bridge support
 *
 * @since 3.0.0
 */
function barelycorporate_wpml_theme_locations( $current_locations ) {
	$new_locations = array();
	$new_locations['social_media_addon'] = array(
		'name' 		=> __( 'Social Media Addon', 'themeblvd' ),
		'desc' 		=> __( 'This will display your language flags just below your social icons in the header of your website.', 'themeblvd' ),
		'action' 	=> 'barelycorporate_header_wpml'
	);
	unset($current_locations['menu_addon']);
	$new_locations = array_merge( $new_locations, $current_locations );
	return $new_locations;
}
add_filter( 'tb_wpml_theme_locations', 'barelycorporate_wpml_theme_locations' );

/**
 * Modify recommended plugins.
 *
 * @since 4.0.0
 */
function barelycorporate_plugins( $plugins ){

	// Add Twitter
	$plugins['tweeple'] = array(
		'name'		=> 'Tweeple',
		'slug'		=> 'tweeple',
		'required'	=> false
	);

	// Add News scroller used in theme demo's homepage
	$plugins['news_scroller'] = array(
		'name'		=> 'Theme Blvd News Scroller',
		'slug'		=> 'theme-blvd-news-scroller',
		'required'	=> false
	);

	return $plugins;
}
add_filter( 'themeblvd_plugins', 'barelycorporate_plugins' );

/**
 * Apply gradient buttons, which were default
 * before Bootstrap 3.
 *
 * @since 4.1.0
 */
function barelycorporate_btn_gradient( $class ) {
	$class[] = 'tb-btn-gradient';
	return $class;
}
add_filter( 'body_class', 'barelycorporate_btn_gradient' );

/*-----------------------------------------------------------------------------------*/
/* Theme Blvd Hooked Functions
/*
/* The following functions either add elements to unsed hooks in the framework,
/* or replace default functions. These functions can be overridden from a child
/* theme.
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'barelycorporate_social_media' ) ) :
/**
 * Header Addon
 *
 * @since 3.0.0
 */
function barelycorporate_social_media() {
	?>
	<div class="social-media<?php if (has_action('barelycorporate_header_wpml')) echo ' social-media-with-wpml'; ?>">
		<?php echo themeblvd_contact_bar(); ?>
		<?php do_action('barelycorporate_header_wpml'); ?>
	</div><!-- .social-media (end) -->
	<?php
}
endif;

if ( !function_exists( 'barelycorporate_header_menu' ) ) :
/**
 * Main Menu
 *
 * @since 3.0.0
 */
function barelycorporate_header_menu() {

	$responsive = themeblvd_supports('display', 'responsive');
	$menu_style = themeblvd_get_option('menu_style');
	$mobile_nav = themeblvd_get_option('mobile_nav');

	do_action( 'themeblvd_header_menu_before' );

	// Show responsive select nav
	if ( $responsive && $mobile_nav == 'mobile_nav_select' ) {
		echo themeblvd_nav_menu_select( apply_filters( 'themeblvd_responsive_menu_location', 'primary' ) );
	}
	?>
	<div class="menu-wrapper menu_style <?php echo $menu_style; ?>">
		<?php if ( $responsive && $mobile_nav == 'mobile_nav_toggle_graphic' ) : ?>
			<a href="#access" class="btn-navbar">
				<?php echo apply_filters( 'themeblvd_btn_navbar_text', '<i class="fa fa-bars"></i>' ); ?>
			</a>
		<?php endif; ?>
		<nav id="access" role="navigation">
			<div class="access-inner">
				<div class="access-content clearfix">
					<?php wp_nav_menu( apply_filters( 'themeblvd_primary_menu_args', array( 'menu_id' => 'primary-menu', 'menu_class' => 'sf-menu','container' => '', 'theme_location' => 'primary', 'fallback_cb' => 'themeblvd_primary_menu_fallback' ) ) ); ?>
					<?php themeblvd_header_menu_addon(); ?>
				</div><!-- .access-content (end) -->
			</div><!-- .access-inner (end) -->
		</nav><!-- #access (end) -->
	</div><!-- .menu_style (end) -->
	<?php
	do_action( 'themeblvd_header_menu_after' );
}
endif;

if ( !function_exists( 'barelycorporate_footer_sub_content' ) ) :
/**
 * Copyright
 *
 * @since 3.0.0
 */
function barelycorporate_footer_sub_content() {
	?>
	<div id="footer_sub_content">
		<div class="footer_sub_content-inner">
			<div class="footer_sub_content-content">
				<div class="copyright">
					<p>
						<span><?php echo apply_filters( 'themeblvd_footer_copyright', themeblvd_get_option( 'footer_copyright' ) ); ?></span>
					</p>
				</div><!-- .copyright (end) -->
				<div class="clear"></div>
			</div><!-- .footer_sub_content-content (end) -->
		</div><!-- .footer_sub_content-inner (end) -->
	</div><!-- .footer_sub_content (end) -->
	<?php
}
endif;

if ( !function_exists( 'barelycorporate_blog_meta' ) ) :
/**
 * Blog Meta
 *
 * @since 3.0.0
 */
function barelycorporate_blog_meta() {
	?>
	<div class="entry-meta">
		<span class="sep"><?php echo themeblvd_get_local( 'posted_on' ); ?></span>
		<time class="entry-date updated" datetime="<?php the_time('c'); ?>"><?php the_time( get_option('date_format') ); ?></time>
		<span class="sep"> <?php echo themeblvd_get_local( 'by' ); ?> </span>
		<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php echo sprintf( esc_attr__( 'View all posts by %s', 'themeblvd' ), get_the_author() ); ?>" rel="author"><?php the_author(); ?></a></span>
		<span class="sep"> <?php echo themeblvd_get_local( 'in' ); ?> </span>
		<?php if ( 'portfolio_item' == get_post_type() ) : ?>
			<span class="category"><?php echo get_the_term_list( get_the_id(), 'portfolio', '', ', ' ); ?></span>
		<?php else : ?>
			<span class="category"><?php the_category(', '); ?></span>
		<?php endif; ?>
		<?php if ( comments_open() ) : ?>
			<span class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">'.themeblvd_get_local( 'no_comments' ).'</span>', '1 '.themeblvd_get_local( 'comment' ), '% '.themeblvd_get_local( 'comments' ) ); ?>
			</span>
		<?php endif; ?>
	</div><!-- .entry-meta -->
	<?php
}
endif;

if ( !function_exists( 'barelycorporate_custom_bg_admin_notice' ) ) :
/**
 * Admin notice to tell user under Appearance > Background
 * that the page only applies if they've selected the boxed layout.
 *
 * @since 3.0.0
 */
function barelycorporate_custom_bg_admin_notice() {
	global $current_screen;
	global $current_user;
	// DEBUG: delete_user_meta( $current_user->ID, 'swagger_custom_bg_notice' );
    if ( $current_screen->base == 'appearance_page_custom-background' && !get_user_meta( $current_user->ID, 'barelycorporate_custom_bg_notice' ) ) {
        echo '<div class="updated">';
        echo '<p>'.__( 'With the Barely Corporate theme, this page\'s functionality will only work properly if you\'ve selected the "Boxed" layout shape from your Theme Options.', 'themeblvd' ).'</p>';
        echo '<p><a href="?page=custom-background&tb_nag_ignore=barelycorporate_custom_bg_notice">'.__('Dismiss this notice', 'themeblvd').'</a></p>';
        echo '</div>';
    }
}
endif;

/*-----------------------------------------------------------------------------------*/
/* Hook Adjustments on framework
/*-----------------------------------------------------------------------------------*/

// Remove hooks
remove_action( 'themeblvd_header_menu', 'themeblvd_header_menu_default' );
remove_action( 'themeblvd_footer_sub_content', 'themeblvd_footer_sub_content_default' );
remove_action( 'themeblvd_blog_meta', 'themeblvd_blog_meta_default' );

// Add hooks
add_action( 'themeblvd_header_addon', 'barelycorporate_social_media' );
add_action( 'themeblvd_header_menu', 'barelycorporate_header_menu' );
add_action( 'themeblvd_footer_sub_content', 'barelycorporate_footer_sub_content' );
add_action( 'themeblvd_blog_meta', 'barelycorporate_blog_meta' );
add_action( 'admin_notices', 'barelycorporate_custom_bg_admin_notice' );