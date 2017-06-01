<?php
/**
 * Supersized
 */
get_header();
?>
<?php
$slideshow_autoplay=of_get_option('slideshow_autoplay');
$slideshow_pause_on_last=of_get_option('slideshow_pause_on_last');
$slideshow_pause_hover=of_get_option('slideshow_pause_hover');
$slideshow_random=of_get_option('slideshow_random');
$slideshow_interval=of_get_option('slideshow_interval');
$slideshow_transition=of_get_option('slideshow_transition');
$slideshow_transition_speed=of_get_option('slideshow_transition_speed');
$slideshow_portrait=of_get_option('slideshow_portrait');
$slideshow_landscape=of_get_option('slideshow_landscape');
$slideshow_fit_always=of_get_option('slideshow_fit_always');
$slideshow_vertical_center=of_get_option('slideshow_vertical_center');
$slideshow_horizontal_center=of_get_option('slideshow_horizontal_center');
$fullscreen_menu_toggle=of_get_option('fullscreen_menu_toggle');
$fullscreen_menu_toggle_nothome=of_get_option('fullscreen_menu_toggle_nothome');
$rootpath= get_stylesheet_directory_uri();
$timthumb_path= $rootpath . '/timthumb.php';

if (! $slideshow_autoplay) $slideshow_autoplay=0;
if (! $slideshow_pause_on_last) $slideshow_pause_on_last=0;
if (! $slideshow_pause_hover) $slideshow_pause_hover=0;
if (! $slideshow_fit_always) $slideshow_fit_always=0;
if (! $slideshow_portrait) $slideshow_portrait=0;
if (! $slideshow_landscape) $slideshow_landscape=0;
if (! $slideshow_vertical_center) $slideshow_vertical_center=0;
if (! $slideshow_horizontal_center) $slideshow_horizontal_center=0;

$slideshow_thumbnails="";

$featured_linked=false;
$attatchmentURL="";
$postLink="";
$thelimit=-1;
$count=0;

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
<style type='text/css'>
.background-fill {z-index:-990;}
</style>
<script type="text/javascript">
jQuery(function($){	
	jQuery.supersized({
	
		// Functionality
		slideshow               :   1,			// Slideshow on/off
		autoplay				:	<?php echo $slideshow_autoplay; ?>,			// Slideshow starts playing automatically
		start_slide             :   1,			// Start slide (0 is random)
		image_path				:	'<?php echo get_template_directory_uri(); ?>/images/supersized/',
		stop_loop				:	<?php echo $slideshow_pause_on_last; ?>,			// Pauses slideshow on last slide
		random					: 	0,			// Randomize slide order (Ignores start slide)
		slide_interval          :   <?php echo $slideshow_interval; ?>,		// Length between transitions
		transition              :   <?php echo $slideshow_transition; ?>, 			// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
		transition_speed		:	<?php echo $slideshow_transition_speed; ?>,		// Speed of transition
		new_window				:	0,			// Image links open in new window/tab
		pause_hover             :   <?php echo $slideshow_pause_hover; ?>,			// Pause slideshow on hover
		keyboard_nav            :   1,			// Keyboard navigation on/off
		performance				:	2,			// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
		image_protect			:	1,			// Disables image dragging and right click with Javascript
												   
		// Size & Position						   
		min_width		        :   0,			// Min width allowed (in pixels)
		min_height		        :   0,			// Min height allowed (in pixels)
		vertical_center         :   <?php echo $slideshow_vertical_center; ?>,			// Vertically center background
		horizontal_center       :   <?php echo $slideshow_horizontal_center; ?>,			// Horizontally center background
		fit_always				:	<?php echo $slideshow_fit_always; ?>,			// Image will never exceed browser width or height (Ignores min. dimensions)
		fit_portrait         	:   <?php echo $slideshow_portrait; ?>,			// Portrait images will not exceed browser height
		fit_landscape			:   <?php echo $slideshow_landscape; ?>,			// Landscape images will not exceed browser width
												   
		// Components							
		slide_links				:	'blank',	// Individual links for each slide (Options: false, 'number', 'name', 'blank')
		thumb_links				:	1,			// Individual thumb links for each slide
		thumbnail_navigation    :   0,			// Thumbnail navigation
		slides 					:  	[			// Slideshow Images


<?php
if (defined('ICL_LANGUAGE_CODE')) { // this is to not break code in case WPML is turned off, etc.
    $_type  = get_post_type($featured_page);
    $featured_page = icl_object_id($featured_page, $_type, true, ICL_LANGUAGE_CODE);
}
// Don't Populate list if no Featured page is set
if ( $featured_page <>"" ) { 

	// Grab all image attachements from the featured page
	$images =& get_children( array( 
								'post_parent' => $featured_page,
								'post_status' => 'inherit',
								'post_type' => 'attachment',
								'post_mime_type' => 'image',
								'order' => 'ASC',
								'numberposts' => $thelimit,
								'orderby' => 'menu_order' )
								);
								
	// Loop through the images
	foreach ( $images as $id => $image ) {
		$attatchmentID = $image->ID;
		
		$imagearray = wp_get_attachment_image_src( $attatchmentID , 'fullsize', false);
		$imageURI = $imagearray[0];
		$imageID = get_post($attatchmentID);
		
		$thumb_imagearray = wp_get_attachment_image_src( $attatchmentID , 'fullscreen-thumbnails', false);
		$thumb_imageURI = $thumb_imagearray[0];
		
		$imageTitle = $image->post_title;
		$imageDesc = $image->post_content;
		$postlink = get_permalink($image->post_parent);
		// If linking is On
		if ($featured_linked == 1 || $featured_linked == true) {
			$attatchmentURL = get_attachment_link($image->ID);
		}
		// Count
		$count++;
		if ($count>1) { echo ","; }

		$slideshow_title="";
		$slideshow_caption="";
		
		//Find and replace all new lines to BR tags
		$find   = array("\r\n", "\n", "\r");
		$replace = '<br />';
		$imageDesc = str_replace($find, $replace , $imageDesc);
		
		if ($imageTitle) $slideshow_title='<div class="slideshow_title">'. esc_attr($imageTitle) .'</div>';
		if ($imageDesc) $slideshow_caption='<div class="slideshow_caption">'. $imageDesc .'</div>';
		
		$caption_text= '<div class="slideshow_captionwrap">' . $slideshow_title . $slideshow_caption . '</div>';
		
		echo "{image : '".$imageURI."', title : '". $caption_text . "', thumb : '".$thumb_imageURI."', url : ''}";
	}

// If Ends here for the Featured Page
}
?>
		],
		progress_bar			:	1,			// Timer for each slide							
		mouse_scrub				:	1
	});
});
</script>
	<?php if ($count>1) { ?>
	<!--Arrow Navigation-->
	<div class="super-navigation">
	<a id="prevslide" class="load-item"></a>
	<a id="nextslide" class="load-item"></a>
	</div>
	<?php } ?>
	
	<?php
	$custom = get_post_custom($featured_page);
	
	if (isset($custom["slideshow_thumbnails"][0])) $slideshow_thumbnails=$custom["slideshow_thumbnails"][0];
	
	if ($slideshow_thumbnails=="Enable") { 
	?>
	
	<div id="thumb-tray" class="load-item">
		<div id="thumb-back"></div>
		<div id="thumb-forward"></div>
	</div>
	
	<?php } ?>
		
	<?php if ($fullscreen_type=="Slideshow-plus-captions") { ?>
		<div id="slidecaption"></div>
	<?php } ?>
		<!--Control Bar-->
	<!--Time Bar-->
	<div id="progress-back" class="load-item">
		<?php if ($count>1) { ?>
		<div id="progress-bar"></div>
		<?php } ?>
	</div>
	

	
		<div id="controls-wrapper" class="load-item">
			<div id="controls">
				<!--Navigation-->
				<?php if ($slideshow_thumbnails=="Enable") { ?>
				<a id="tray-button"><img id="tray-arrow" src="<?php echo get_template_directory_uri(); ?>/images/supersized/button-tray-up.png"/></a>
				<?php } ?>
				<?php if ($count>1) { ?>
				<a id="play-button"><img id="pauseplay" src="<?php echo get_template_directory_uri(); ?>/images/supersized/pause.png"/></a>
				<a id="tray-panelprev">prev</a><a id="tray-panelnext">next</a>
				<ul id="slide-list"></ul>
				<?php } ?>
			</div>
		</div>
		
	<?php if ($count>1) { ?>
	<span class="control-underlay"></span>
	<?php } ?>

<?php
$sound_found=false;
$mp3_ext='';
$mp3_sep='';
$m4a_ext='';
$m4a_sep='';
$oga_ext='';
if (isset($custom["fullscreen_slideshow_audio_mp3"][0])) $mp3_file=$custom["fullscreen_slideshow_audio_mp3"][0];
if (isset($custom["fullscreen_slideshow_audio_m4a"][0])) $m4a_file=$custom["fullscreen_slideshow_audio_m4a"][0];
if (isset($custom["fullscreen_slideshow_audio_oga"][0])) $oga_file=$custom["fullscreen_slideshow_audio_oga"][0];

if ( isset($mp3_file) && $mp3_file<>'' ) { $sound_found=true; $mp3_ext ="mp3"; if ($m4a_file || $oga_file){ $mp3_sep=",";} }
if ( isset($m4a_file)  && $m4a_file<>'' ) { $sound_found=true; $m4a_ext ="m4a"; if ($oga_file){ $m4a_sep=",";} }
if ( isset($oga_file)  && $oga_file<>'' ) { $sound_found=true; $oga_ext ="oga";  }

if ($sound_found) {
	$files_used=$mp3_ext.$mp3_sep.$m4a_ext.$m4a_sep.$oga_ext;
}

if ( $sound_found ) {
?>
<style type='text/css'>
div.jp-audio div.jp-type-single div.jp-volume-bar { left:100%; top:33px; }
div.jp-audio {position:absolute; bottom:32px; right:195px;z-index:10; }
div.jp-audio div.jp-type-single a.jp-mute, div.jp-audio div.jp-type-single a.jp-unmute { left:-21px; top:28px; }
div.jp-interface { background:none; filter:none; }
div.jp-audio div.jp-type-single div.jp-interface {height:30px;}
div.jp-volume-bar { width:65px; }
</style>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){
	jQuery("#jquery_jplayer_<?php the_ID(); ?>").jPlayer({
		ready: function () {
			jQuery(this).jPlayer("setMedia", {
				<?php if ($mp3_file) echo 'mp3: "'.$mp3_file.'",'; ?>
				<?php if ($m4a_file) echo 'm4a: "'.$m4a_file.'",'; ?>
				<?php if ($oga_file) echo 'oga: "'.$oga_file.'",'; ?>
				end: ""
			}).jPlayer("play").jPlayer("volume", <?php echo of_get_option('audio_volume')/100; ?>);
		},
		<?php
		if ( of_get_option('audio_loop') ) {
		?>
		ended: function() {
		jQuery(this).jPlayer("play");
		},
		<?php
		}
		?>
		swfPath: "<?php echo get_stylesheet_directory_uri(); ?>/js/html5player/",
		supplied: "<?php echo $files_used; ?>",
		cssSelectorAncestor: "#jp_interface_<?php the_ID(); ?>"
	});
});
//]]>
</script>

<div class="fullscreenslideshow-audio">
<div id="jquery_jplayer_<?php the_ID(); ?>" class="jp-jplayer"></div>
<div class="jp-audio">
	<div class="jp-type-single">
		<div id="jp_interface_<?php the_ID(); ?>" class="jp-interface">
			<ul class="jp-controls">
				<li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
				<li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
			</ul>
			<div class="jp-volume-bar">
				<div class="jp-volume-bar-value"></div>
			</div>
		</div>
	</div>
</div>
</div>
<?php
} else {
?>
<style type='text/css'>
.control-underlay { width: 155px; }
#tray-panelnext { right:115px; }
#tray-panelprev { right:144px; }
#play-button { right:190px; }
<?php
if ($slideshow_thumbnails=="Enable") {
echo '#tray-button { right: 248px; }';
echo 'ul#slide-list { right:295px; }';
} else {
echo 'ul#slide-list { right:256px; }';
}
?>

</style>
<?php
}
?>
<div class="slidshowbackground-panel"></div>
<?php
//Include the featured blocks
if ( of_get_option('three_step_status') && is_front_page() ) {
	require (MTHEME_INCLUDES . "services-block.php");
}
?>
<?php
//End password check wrap
}
?>
<?php get_footer(); ?>