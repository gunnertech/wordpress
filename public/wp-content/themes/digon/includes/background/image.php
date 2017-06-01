<?php
$image_link=featured_image_link($post->ID);
$bg_choice= get_post_meta($post->ID, MTHEME . '_meta_background_choice', true);
$custom_bg_image_url= get_post_meta($post->ID, MTHEME . '_meta_background_url', true);
$default_bg= of_get_option('general_background_image');

	// Check custom posts
	$custom_post = get_post_custom(get_the_ID());
	$custom_post_bg_choice=$custom_post["portfolio_background_choice"][0];
	if ( $custom_post_bg_choice <> "" ) $bg_choice = $custom_post_bg_choice;

if ( is_archive() ) $bg_choice="default";

if ($bg_choice<>"none") {
function backstretch_script() {
?>
<script type="text/javascript">
jQuery(document).ready(function(){
<?php
switch ($bg_choice) {
	case "Use featured image" :
	echo '
		jQuery.backstretch("'.$image_link.'", {
			speed: 1000
		});
		';
	break;
	case "Use custom background" :
	echo '
		jQuery.backstretch("'.$custom_bg_image_url.'", {
			speed: 1000
		});
		';
	break;
	case "Theme options set image" :
	echo '
		jQuery.backstretch("'.$default_bg.'", {
			speed: 1000
		});
		';
	break;
	default :
		if ($default_bg) {
			echo '
				jQuery.backstretch("'.$default_bg.'", {
					speed: 1000
				});
				';
		}
	break;
}
?>
});
</script>
<?php
}
add_action('wp_footer','backstretch_script',19);
}
?>