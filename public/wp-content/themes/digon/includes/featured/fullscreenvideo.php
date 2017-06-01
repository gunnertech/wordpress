<?php
/**
 * Fullscreen Video
 */
JWPlayerScripts();
global $fullscreen_status;
$fullscreen_status="enable";
get_header();
$swfplayer= MTHEME_PATH . '/js/jwplayer/jwplayer.flash.swf';
$jwskins= MTHEME_PATH . '/images/jwskin/newtubedark.zip';

$custom = get_post_custom($featured_page);
$videofile=$custom["fullscreen_video_id"][0];
$vimeoID=$custom["fullscreen_vimeo_id"][0];
$html_video=$custom["fullscreen_html_video"][0];
if (isset($custom["fullscreen_html_poster"][0])) $html_video_poster=$custom["fullscreen_html_poster"][0];

$video_control_bar=of_get_option('video_control_bar');
$fullscreen_menu_toggle=of_get_option('fullscreen_menu_toggle');
$fullscreen_menu_toggle_nothome=of_get_option('fullscreen_menu_toggle_nothome');

if ( post_password_required() ) {
// Grab default background set from theme options	
$default_bg= of_get_option('general_background_image');
?>
<script type="text/javascript">
jQuery(document).ready(function(){
<?php
	echo '
		jQuery.backstretch("'.$default_bg.'", {
			speed: 1000
		});
		';
?>
});
</script>
	<div class="container clearfix">
		<div class="fullpage-contents-wrap">
		<div class="page-container">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
					<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php
				echo '<div id="password-protected">';
				if (DEMO_STATUS) { echo '<p><h2>DEMO Password is 1234</h2></p>'; }
				echo get_the_password_form();
				echo '</div>';
				?>
				
			</div>
		</div>
		</div>
	</div>
<?php	
	
	} else {
?>
<?php
// Activate Vimeo iframe for fullscreen playback
if ($vimeoID) {
	$videofile=false;
	?>
<div id="fullscreenvimeo">
<iframe frameborder="0" allowfullscreen="" webkitallowfullscreen="" src="http://player.vimeo.com/video/<?php echo $vimeoID; ?>?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;loop=0"></iframe>
</div>
<?php
}
?>


<?php
// Play Youtube and Other Video files
if ($videofile) {
?>
<div id="backgroundvideo">
<div id="videocontainer"></div>	
<script type='text/javascript'>
jQuery(document).ready(function($) {
	jwplayer('videocontainer').setup({
		'flashplayer': '<?php echo $swfplayer; ?>',
		'id': 'playerID',
		'width': '100%',
		'height': '100%',
		'stretching':'fill',
		'allowscriptaccess':'always',
		'allowfullscreen': 'true',
		'autostart':'true',
		'controlbar':'<?php echo $video_control_bar; ?>',
		'file': '<?php echo $videofile; ?>'
	});
});
</script>

</div>
<?php
}
?>


<?php
// HTML5 Video
if ($html_video) {
?>
<div id="backgroundvideo">
<div id="videocontainer"></div>	
<script type='text/javascript'>
jQuery(document).ready(function($) {
		var w_height = $(window).height();
		w_height = w_height - 30; 
		var w_width = $(window).width();
	jwplayer('videocontainer').setup({
		'id': 'playerID',
		'width': '100%',
		'height': '100%',
		'stretching':'fill',
		<?php if ($html_video_poster<>"") { echo "'image':'".$html_video_poster."',"; } ?>
		'allowscriptaccess':'always',
		'allowfullscreen': 'true',
		'autostart':'true',
		'controlbar':'<?php echo $video_control_bar; ?>',
		'file': '<?php echo $html_video; ?>',
		'modes': [
			{type: 'html5'},
			{
			type: 'flash',
			src: '<?php echo $swfplayer; ?>',
			'width': '100%',
			'height': '100%',
			'stretching':'fill',
			'allowscriptaccess':'always',
			'allowfullscreen': 'true',
			'autostart':'true',
			'controlbar':'<?php echo $video_control_bar; ?>'
			}
		]
	});
});
</script>

</div>
<?php
}
//End password check wrap
}
?>
<?php get_footer(); ?>