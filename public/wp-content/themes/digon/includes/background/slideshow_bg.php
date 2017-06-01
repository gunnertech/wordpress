<?php
/**
 * Supersized
 */
?>
<?php
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

if (! $slideshow_pause_on_last) $slideshow_pause_on_last=0;
if (! $slideshow_pause_hover) $slideshow_pause_hover=0;
if (! $slideshow_fit_always) $slideshow_fit_always=0;
if (! $slideshow_portrait) $slideshow_portrait=0;
if (! $slideshow_landscape) $slideshow_landscape=0;
if (! $slideshow_vertical_center) $slideshow_vertical_center=0;
if (! $slideshow_horizontal_center) $slideshow_horizontal_center=0;

$featured_linked=false;
$attatchmentURL="";
$postLink="";
$thelimit=-1;
$count=0;

?>
<style type='text/css'>
.background-fill {z-index:-990;}
</style>
<script type="text/javascript">
jQuery(function($){
	jQuery.supersized({
	
		// Functionality
		slideshow               :   1,			// Slideshow on/off
		autoplay				:	1,			// Slideshow starts playing automatically
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
// Don't Populate list if no Featured page is set
$bg_slideshow = of_get_option ('general_bgslideshow');

if ($bg_choice=="Image Attachments Slideshow") { $bg_slideshow = $post->ID; }
if ($bg_choice=="Fullscreen Post Slideshow" ) { $bg_slideshow = $fullscreen_slideshowpost; }

if ( $bg_slideshow <>"" ) {

	// Grab all image attachements from the featured page
	$images =& get_children( array( 
								'post_parent' => $bg_slideshow,
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
		$imagearray = wp_get_attachment_image_src( $attatchmentID , 'full', false);
		$imageURI = $imagearray[0];
		$imageID = get_post($attatchmentID);
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
		
		//if ($imageTitle) $slideshow_title='<div class="slideshow_title">'.$imageTitle.'</div>';
		//if ($imageDesc) $slideshow_caption='<div class="slideshow_caption">'.$imageDesc.'</div>';
		
		echo "{image : '".$imageURI."', title : '". $slideshow_title . $slideshow_caption . "', url : ''}";
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
<style type='text/css'>
.control-underlay { width: 155px; }
#tray-panelnext { right:115px; }
#tray-panelprev { right:144px; }
#play-button { right:190px; }
ul#slide-list { right:256px; }
</style>
	
	<!--Arrow Navigation-->
	<div class="super-navigation">
	<a id="prevslide" class="load-item"></a>
	<a id="nextslide" class="load-item"></a>
	</div>		
	
	<!--Control Bar-->
	<!--Time Bar-->
	<div id="progress-back" class="load-item">
		<div id="progress-bar"></div>
	</div>
		<div id="controls-wrapper" class="load-item">
			<div id="controls">		
				<!--Navigation-->
				<a id="play-button"><img id="pauseplay" src="<?php echo get_template_directory_uri(); ?>/images/supersized/pause.png"/></a>
				<a id="tray-panelprev">prev</a><a id="tray-panelnext">next</a>
				<ul id="slide-list"></ul>		
			</div>
		</div>
