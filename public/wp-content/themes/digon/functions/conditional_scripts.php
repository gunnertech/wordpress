<?php
function load_ie78_styles() {
?>
<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/css/ie7.css" media="screen" />
<![endif]-->
<!--[if lt IE 9]>
        <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/css/ie8.css" media="screen" />
<![endif]-->
<?php
}
add_action('wp_head','load_ie78_styles',12);
?>
<?php
function disable_rightclick() {
if ( of_get_option('rightclick_disable') ) {
?> 
<script language=JavaScript>var message="<?php echo of_get_option('rightclick_disabletext') ?>"; function clickIE4(){ if (event.button==2){ alert(message); return false; } } function clickNS4(e){ if (document.layers||document.getElementById&&!document.all){ if (e.which==2||e.which==3){ alert(message); return false; } } } if (document.layers){ document.captureEvents(Event.MOUSEDOWN); document.onmousedown=clickNS4; } else if (document.all&&!document.getElementById){ document.onmousedown=clickIE4; } document.oncontextmenu=new Function("alert(message);return false") </script>
<?php
}
}
add_action('wp_footer','disable_rightclick');
?>
<?php
if (isset($post->ID)) { 
	$bg_choice= get_post_meta($post->ID, MTHEME . '_meta_background_choice', true);
	$portfolio_bg_choice= get_post_meta($post->ID, 'portfolio_background_choice', true);
	$fullscreen_slideshowpost= get_post_meta($post->ID, MTHEME . '_slideshow_bgfeaturedpost', true);
	
	if ($portfolio_bg_choice<>"") { $bg_choice=$portfolio_bg_choice; }
	if ($fullscreen_slideshowpost != "none" && $fullscreen_slideshowpost<>"" && isSet($fullscreen_slideshowpost) ) {
		$bg_choice="Fullscreen Post Slideshow";
	}
}

if ( empty($bg_choice) ) $bg_choice="Theme options set image";
if ( is_archive() ) $bg_choice="Theme options set image";
// Load scripts based on Background Image / Slideshow Choice
switch ($bg_choice) { 
	case "Use featured image" :
	case "Use custom background" :
	case "Theme options set image" :
	backstretchScript();
	break;
	case "Background Slideshow" :
	case "Image Attachments Slideshow" :
	case "Fullscreen Post Slideshow" :
	//Defined in Theme framework Functions
	LoadSuperSizedScripts();
	break;
}
// Shortcodes checker //
//Maps
if(theme_got_shortcode('map')) {  
	GoogleMapsLoader();
}

if( theme_got_shortcode('flexislideshow')) {
	FlexiSlideScripts();
}
if( theme_got_shortcode('audioplayer')) {
	JPlayerScripts();
}

//Tabs
if(theme_got_shortcode('tabs') || theme_got_shortcode('accordion')) {
	JqueryUIScript();
}
?>
<?php
if ( is_archive() || is_single() || is_search() || is_page_template('template-bloglist.php') || is_page_template('template-bloglist_fullwidth.php') || is_page_template('template-gallery-posts.php') ) {
	FlexiSlideScripts();
}
if ( is_home() || is_front_page() || is_archive() || is_single() || is_search() || is_page_template('template-bloglist.php') || is_page_template('template-bloglist_fullwidth.php') || is_page_template('template-video-posts.php') || is_page_template('template-audio-posts.php') ) {
	JPlayerScripts();
}
if ( is_page_template('template-contact.php') ) {
	contactFormScript();
}
//Theme
if ( !DEMO_STATUS ) {
	if (of_get_option('general_theme_style')=="dark" ) {
		DarkTheme();
	}
}

if (DEMO_STATUS) {
	if ( isset( $_GET['demo_theme_style'] ) ) $_SESSION['demo_theme_style']=$_GET['demo_theme_style'];
	if ( isset($_SESSION['demo_theme_style'] )) $demo_theme_style = $_SESSION['demo_theme_style'];
	if (!isSet($_SESSION['demo_theme_style']) || $_SESSION['demo_theme_style'] == "dark" ) {
		DarkTheme();
	}
}
//Responsive Load
if ( of_get_option('responsive_status') ) {
	ResponsiveStyle();
}
?>