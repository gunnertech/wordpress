<?php
//Common Scripts
function LoadCommonScripts() {
		global $is_IE;
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'superfish', MTHEME_JS . '/menu/superfish.js?v=1.0', array( 'jquery' ),null, true );
		wp_enqueue_script( 'hoverintent', MTHEME_JS . '/menu/hoverIntent.js?v=1.0', array( 'jquery' ),null, true );
		wp_enqueue_script( 'qtips', MTHEME_JS . '/jquery.tipsy.js?v=1.0', array( 'jquery' ),null, true );
		wp_enqueue_script( 'prettyPhoto', MTHEME_JS . '/jquery.prettyPhoto.js?v=1.0', array( 'jquery' ),null, true );
		wp_enqueue_script( 'twitter', MTHEME_JS . '/jquery.tweet.js?v=1', array( 'jquery' ),null, true );
		wp_enqueue_script( 'EasingScript', MTHEME_JS . '/jquery.easing.min.js', array( 'jquery' ),null, true );
		wp_enqueue_script( 'portfolioloader', MTHEME_JS . '/page-elements.js', array( 'jquery' ), null,false );
		wp_enqueue_script( 'fitVidsJS', MTHEME_JS . '/jquery.fitvids.js', array( 'jquery' ), null,false );
		if($is_IE) {
			if ( of_get_option(responsive_status) ) {
				wp_enqueue_script( 'ResponsiveJQIE', MTHEME_JS . '/css3-mediaqueries.js', array('jquery'),null, true );
			}
		}
		wp_enqueue_script( 'custom', MTHEME_JS . '/common.js?v=1.0', array( 'jquery' ),null, true );
}
//Common Styles
function LoadCommonStyles() {
		wp_enqueue_style( 'MainStyle', MTHEME_STYLESHEET . '/style.css',false, 'screen' );
		if ( ! MTHEME_BUILDMODE ) {
			if ( of_get_option('default_googlewebfonts') ) {
				wp_enqueue_style( 'Lato', 'http://fonts.googleapis.com/css?family=Lato:400,700,900,300,400italic,700italic,300italic' );
			}
		}
		wp_enqueue_style( 'PrettyPhoto', MTHEME_CSS . '/prettyPhoto.css', array( 'MainStyle' ), false, 'screen' );
		wp_enqueue_style( 'navMenuCSS', MTHEME_CSS . '/menu/superfish.css', array( 'MainStyle' ), false, 'screen' );	
}
//HTML5 Video player
function JPlayerScripts() {
		wp_enqueue_script( 'jPlayerJS', MTHEME_JS . '/html5player/jquery.jplayer.min.js', array( 'jquery' ),null, true );
		wp_enqueue_style( 'css_jplayer', MTHEME_ROOT . '/css/html5player/jplayer.dark.css', array( 'MainStyle' ), false, 'screen' );
}
//Dark Theme
function DarkTheme() {
	wp_enqueue_style( 'DarkStyle', MTHEME_STYLESHEET . '/style_dark.css', array( 'MainStyle' ), false, 'screen' );
}
//JwScripts
function JWPlayerScripts() {
wp_enqueue_script( 'jwplayer', MTHEME_JS . '/jwplayer/jwplayer.js', array( 'jquery' ),null, true );
}
//Supersized
function LoadSuperSizedScripts () {
	wp_enqueue_script( 'supersized', MTHEME_JS . '/supersized/supersized.3.2.7.min.js', array( 'jquery' ), '' );
	wp_enqueue_script( 'supersizedShutter', MTHEME_JS . '/supersized/supersized.shutter.js', array( 'jquery' ), '' );
	wp_enqueue_script( 'jQEasing', MTHEME_JS . '/jquery.easing.min.js', array( 'jquery' ), '' );
	wp_enqueue_style( 'SupersizedCSS', MTHEME_CSS . '/supersized/supersized.css' );
	wp_enqueue_style( 'SupersizedShutterCSS', MTHEME_CSS . '/supersized/supersized.shutter.css' );
	}
//FeaturedFlexisliderscripts
function FeaturedFlexiSlideScripts () {
wp_enqueue_script( 'flexislider', MTHEME_JS . '/flexislider/jquery.flexslider.js', array('jquery') , '',true );
wp_enqueue_style( 'featuredflexislider_css', MTHEME_ROOT . '/css/flexislider/flexslider_featured.css', false, 'screen' );
}
//Flexisliderscripts
function FlexiSlideScripts () {
wp_enqueue_script( 'flexislider', MTHEME_JS . '/flexislider/jquery.flexslider.js', array('jquery') , '',true );
wp_enqueue_style( 'flexislider_css', MTHEME_ROOT . '/css/flexislider/flexslider-page.css', false, 'screen' );
}
// jQuery UI
function JqueryUIScript() {
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-accordion');
}
//Contact Form
function contactFormScript() {
wp_enqueue_script( 'contactform', MTHEME_JS . '/contact.js', array( 'jquery' ),null, false );
}
//BackStretch Imager
function backstretchScript() {
wp_enqueue_script( 'backstretch', MTHEME_JS . '/jquery.backstretch.min.js', array('jquery'), '' );
}
//Responsive
function ResponsiveStyle() {
wp_enqueue_style( 'Responsive', MTHEME_CSS . '/responsive.css',array( 'MainStyle' ),false, 'screen' );
}
//Custom CSS
function CustomStyle() {
wp_enqueue_style( 'CustomStyle', MTHEME_STYLESHEET . '/custom.css',array( 'MainStyle' ),false, 'screen' );
}
//Google Maps Loader
function GoogleMapsLoader() {
wp_enqueue_script( 'GoogleMaps', 'http://maps.google.com/maps/api/js?sensor=false', array( 'jquery' ),null, false ); 	
}
?>