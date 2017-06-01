<?php
//Defined in Theme Framework functions
$featured_page= get_the_ID();
$custom = get_post_custom($featured_page);

$fullscreen_type = $custom["fullscreen_type"][0];

global $fullscreen_status;
$fullscreen_status="enable";

switch ($fullscreen_type) {	
	
	case "Slideshow" :
	case "Slideshow-plus-captions" :
		LoadSuperSizedScripts();
		require_once (MTHEME_INCLUDES . 'featured/supersized.php');
	break;
	
	case "Fullscreen-Video" :
		require_once (MTHEME_INCLUDES . 'featured/fullscreenvideo.php');
	break;
	default:
		LoadSuperSizedScripts();
		$fullscreen_type="Slideshow";
		require_once (MTHEME_INCLUDES . 'featured/supersized.php');
	break;
}
?>