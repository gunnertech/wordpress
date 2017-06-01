<?php
/*
* @ Header
*/
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>
	<?php
	if (is_category()) {
		echo 'Category: '; wp_title(''); echo ' - ';

	} elseif (function_exists('is_tag') && is_tag()) {
		single_tag_title('Tag Archive for &quot;'); echo '&quot; - ';

	} elseif (is_archive()) {
		wp_title(''); echo ' Archive - ';

	} elseif (is_page()) {
		echo wp_title(''); echo ' - ';

	} elseif (is_search()) {
		echo 'Search for &quot;'.wp_specialchars($s).'&quot; - ';

	} elseif (!(is_404()) && (is_single()) || (is_page())) {
		wp_title(''); echo ' - ';

	} elseif (is_404()) {
		echo 'Not Found - ';

	} bloginfo('name');
	?>
</title>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( of_get_option('general_fav_icon') ) { ?>
<link rel="shortcut icon" href="<?php echo of_get_option('general_fav_icon'); ?>" />
<?php } ?>
<!-- Feeds -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
<?php
if ( DEMO_STATUS ) { 
	require (MTHEME_PARENTDIR . "/framework/demopanel/demo_loader.php");
}
require (MTHEME_PARENTDIR . "/functions/conditional_scripts.php");
function uri_path_script() { 
?>
<script type="text/javascript">
var mtheme_uri="<?php echo get_template_directory_uri(); ?>";
</script>
<?php
}
add_action('wp_head', 'uri_path_script');
wp_head();
?>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/dynamic_css.php"/>
</head>
<body <?php body_class(); ?>>
<?php
if ( DEMO_STATUS ) { 
	require ( MTHEME_PARENTDIR . '/framework/demopanel/demo-panel.php');
}
?>
<div class="background-fill"></div>
<?php
// get fullscreen status
global $fullscreen_status;
if ( $fullscreen_status<>"enable" ) {
	require (MTHEME_PARENTDIR . "/includes/background/background_display.php");
}
?>
<?php
//Get the sidebar choice
global $sidebar_choice;
if (isset($post->ID)) $sidebar_choice= get_post_meta($post->ID, MTHEME . '_sidebar_choice', true);
?>
<?php
get_template_part('header','navigation');
?>
<?php
if ( $fullscreen_status<>"enable" ) {
	echo '<div class="container clearfix">';
	if ( is_home() ) {
		echo '<div class="main-contents">';
	} else {
		echo '<div class="page-contents">';
	}
}
?>