<?php
/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {
	
	// Pull all Google Fonts using API into an array
	require ( MTHEME_PARENTDIR . '/framework/options/google-fonts.php');
	//$fontArray = unserialize($fontsSeraliazed);
	$google_font_array = json_decode ($google_api_output,true) ;
	//print_r( json_decode ($google_api_output) );
	
	$items = $google_font_array['items'];
	
	$sidebar_message="Enter a name to active sidebar";
	
	$options_fonts=array();
	array_push($options_fonts, "Default Font");
	$fontID = 0;
	foreach ($items as $item) {
		$fontID++;
		$variants='';
		$variantCount=0;
		foreach ($item['variants'] as $variant) {
			$variantCount++;
			if ($variantCount>1) { $variants .= '|'; }
			$variants .= $variant;
		}
		$variantText = ' (' . $variantCount . ' Varaints' . ')';
		if ($variantCount <= 1) $variantText = '';
		$options_fonts[ $item['family'] . ':' . $variants ] = $item['family']. $variantText;
	}

	
	// Pull all the categories into an array
	$options_categories = array(); 
	array_push($options_categories, "All Categories");
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	if ($options_pages_obj) {
		foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
		}
	}
	
	// Pull all the Featured into an array
	$featured_pages = get_posts('post_type=mtheme_featured&orderby=title&numberposts=-1&order=ASC');
	if ($featured_pages) {
		foreach($featured_pages as $key => $list) {
			$custom = get_post_custom($list->ID);
			if ( isset($custom["fullscreen_type"][0]) ) { 
				$slideshow_type=' ('.$custom["fullscreen_type"][0].')'; 
			} else {
			$slideshow_type="";
			}
			$options_featured[$list->ID] = $list->post_title . $slideshow_type;
		}
	} else {
		$options_featured[0]="Featured pages not found.";
	}
	
	// Pull all the Featured into an array
	$bg_slideshow_pages = get_posts('post_type=mtheme_featured&orderby=title&numberposts=-1&order=ASC');
	if ($bg_slideshow_pages) {
		foreach($bg_slideshow_pages as $key => $list) {
			$custom = get_post_custom($list->ID);
			if ( isset($custom["fullscreen_type"][0]) ) { 
				$slideshow_type=$custom["fullscreen_type"][0]; 
			} else {
			$slideshow_type="";
			}
			if ($slideshow_type<>"Fullscreen-Video") {
				$options_bgslideshow[$list->ID] = $list->post_title;
			}
		}
	} else {
		$options_bgslideshow[0]="Featured pages not found.";
	}
	
	// Pull all the Portfolio into an array
	$portfolio_pages = get_posts('post_type=mtheme_portfolio&orderby=title&numberposts=-1&order=ASC');
	if ($portfolio_pages) {
		foreach($portfolio_pages as $key => $list) {
			$custom = get_post_custom($list->ID);
			$portfolio_list[$list->ID] = $list->post_title;
		}
	}

	// Pull all the Portfolio Categories into an array
	$the_list = get_categories('taxonomy=types&title_li=');
	$portfolio_categories=array();
	foreach($the_list as $key => $list) {
		$portfolio_categories[$list->slug] = $list->name;
	}
	array_unshift($portfolio_categories, "All the items");
		
	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/framework/options/images/';
	$theme_imagepath =  get_template_directory_uri() . '/images/';
		
	$options = array();
		
$options[] = array( "name" => "General",
					"type" => "heading");
						
	$options[] = array( "name" => "Activate Responsive features",
						"desc" => "Activate Responsive features that enable layout switching based on display resolution",
						"id" => "responsive_status",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Activate Menu description text",
						"desc" => "Display menu description text",
						"id" => "menudesc_status",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Fav icon file",
						"desc" => "Customize with your fav icon",
						"id" => "general_fav_icon",
						"type" => "upload");
					
	$options[] = array( "name" => "Theme Style",
						"desc" => "style_dark.css / style.css",
						"id" => "general_theme_style",
						"std" => "light",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array(
							'dark' => "Dark theme",
							'light' => "Light theme")
						);
						
$options[] = array( "name" => "Custom WordPress Login Page Logo",
					"desc" => "Upload logo for WordPress Login Page",
					"id" => "wplogin_logo",
					"type" => "upload");		
						
	$options[] = array( "name" => "Fullscreen Toggle Message",
						"desc" => "This text appears when you hover over the fullscreen toggle button",
						"id" => "fullscreen_menu_default_text",
						"std" => "Toggle Fullscreen",
						"class" => "mini",
						"type" => "text");
						
	$options[] = array( "name" => "Disable Right Click",
						"desc" => "Disable right clicking",
						"id" => "rightclick_disable",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Right Click Message",
						"desc" => "This text appears in the popup when right click is disabled",
						"id" => "rightclick_disabletext",
						"std" => "You can enable/disable right clicking from Theme Options and customize this message too.",
						"type" => "textarea");
						
$options[] = array( "name" => "Logo",
					"type" => "heading");
					
	$options[] = array( "name" => "Site Logo",
						"desc" => "Upload logo for website",
						"id" => "main_logo",
						"type" => "upload");
						
	$options[] = array( "name" => "Top Margin Space",
						"desc" => "Top margin spacing for logo",
						"id" => "logo_topmargin",
						"min" => "0",
						"max" => "200",
						"step" => "0",
						"unit" => 'pixels',
						"std" => "0",
						"type" => "text");
						
	$options[] = array( "name" => "Left Margin Space",
						"desc" => "Left margin spacing for logo",
						"id" => "logo_leftmargin",
						"min" => "0",
						"max" => "200",
						"step" => "0",
						"unit" => 'pixels',
						"std" => "0",
						"type" => "text");
						
$options[] = array( "name" => "Background",
					"type" => "heading");			
						
	$options[] = array( "name" => "Background color",
						"desc" => "No color selected by default.",
						"id" => "general_background_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Background Fullscreen Slideshow Page",
						"desc" => "Featured page to display as mainpage.",
						"id" => "general_bgslideshow",
						"std" => "",
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_bgslideshow);
						
	$options[] = array( "name" => "Background image ( required for archive pages )",
						"desc" => "Upload background image",
						"id" => "general_background_image",
						"type" => "upload");
						
	$options[] = array( "name" => "Background overlay pattern",
						"desc" => "Background overlay patterns.",
						"id" => "general_background_overlay",
						"std" => "0",
						"type" => "images",
						"options" => array(
							'0' => $theme_imagepath . 'overlays/options/sample-none.png',
							'01' => $theme_imagepath . 'overlays/options/sample-01.png',
							'02' => $theme_imagepath . 'overlays/options/sample-02.png',
							'03' => $theme_imagepath . 'overlays/options/sample-03.png',
							'04' => $theme_imagepath . 'overlays/options/sample-04.png',
							'05' => $theme_imagepath . 'overlays/options/sample-05.png',
							'06' => $theme_imagepath . 'overlays/options/sample-06.png',
							'07' => $theme_imagepath . 'overlays/options/sample-07.png',
							'08' => $theme_imagepath . 'overlays/options/sample-08.png',
							'09' => $theme_imagepath . 'overlays/options/sample-09.png',
							'10' => $theme_imagepath . 'overlays/options/sample-10.png',
							'11' => $theme_imagepath . 'overlays/options/sample-11.png',
							'12' => $theme_imagepath . 'overlays/options/sample-12.png',
							'13' => $theme_imagepath . 'overlays/options/sample-13.png',
							'14' => $theme_imagepath . 'overlays/options/sample-14.png',
							'15' => $theme_imagepath . 'overlays/options/sample-15.png')
						);
						
$options[] = array( "name" => "Main Page",
					"type" => "heading");
					
	$options[] = array( "name" => "Fullscreen Page",
						"type" => "info");
						
	$options[] = array( "name" => "Select main fullscreen page",
						"desc" => "Featured page to display as mainpage.",
						"id" => "options_featured_page",
						"std" => "",
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_featured);
											
$options[] = array( "name" => "Home Service Boxes",
					"type" => "heading");

$options[] = array( "name" => "Section status",
					"desc" => "Toggle services on and off",
					"id" => "three_step_status",
					"std" => "1",
					"type" => "checkbox");

$options[] = array( "name" => "Step 1",
					"type" => "info");
					
$options[] = array( "name" => "Hover image (278px X 118px)",
				"desc" => "Upload 278px X 118px image for hover image",
				"id" => "step_1_image",
				"type" => "upload");

$options[] = array( "name" => "Icon",
				"desc" => "Upload icon for service",
				"id" => "step_1_icon",
				"type" => "upload");
				
$options[] = array( "name" => "Icon background color",
					"desc" => "Icon background color",
					"id" => "stepicon_1_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Step background color",
					"desc" => "Background color",
					"id" => "step_1_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Title",
					"desc" => "Title for service 1",
					"id" => "step_1_title",
					"std" => "Travel Photography",
					"class" => "tiny",
					"type" => "text");

$options[] = array( "name" => "Description",
					"desc" => "Enter step description",
					"id" => "step_1_desc",
					"std" => "Vestibulum ante ipsum primis in faucibus orci luctus et ultrices.",
					"type" => "textarea");

$options[] = array( "name" => "Link URL",
					"desc" => "Step link",
					"id" => "step_1_link",
					"std" => "#",
					"class" => "small",
					"type" => "text");

					

$options[] = array( "name" => "Step 2",
					"type" => "info");
					
$options[] = array( "name" => "Hover image (278px X 118px)",
				"desc" => "Upload 278px X 118px image for hover image",
				"id" => "step_2_image",
				"type" => "upload");

$options[] = array( "name" => "Icon",
				"desc" => "Upload icon for service",
				"id" => "step_2_icon",
				"type" => "upload");
				
$options[] = array( "name" => "Icon background color",
					"desc" => "Icon background color",
					"id" => "stepicon_2_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Step background color",
					"desc" => "Background color",
					"id" => "step_2_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Title",
					"desc" => "Title for service 2",
					"id" => "step_2_title",
					"std" => "Beauty & Fashion",
					"class" => "tiny",
					"type" => "text");

$options[] = array( "name" => "Description",
					"desc" => "Enter step description",
					"id" => "step_2_desc",
					"std" => "Vestibulum ante ipsum primis in faucibus orci luctus et ultrices.",
					"type" => "textarea");

$options[] = array( "name" => "Link URL",
					"desc" => "Step link",
					"id" => "step_2_link",
					"std" => "#",
					"class" => "small",
					"type" => "text");

					

$options[] = array( "name" => "Step 3",
					"type" => "info");
					
$options[] = array( "name" => "Hover image (278px X 118px)",
				"desc" => "Upload 278px X 118px image for hover image",
				"id" => "step_3_image",
				"type" => "upload");

$options[] = array( "name" => "Icon",
				"desc" => "Upload icon for step",
				"id" => "step_3_icon",
				"type" => "upload");
				
$options[] = array( "name" => "Icon background color",
					"desc" => "Icon background color",
					"id" => "stepicon_3_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Step background color",
					"desc" => "Background color",
					"id" => "step_3_bgcolor",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Title",
					"desc" => "Title for step",
					"id" => "step_3_title",
					"std" => "Wedding Photography",
					"class" => "tiny",
					"type" => "text");

$options[] = array( "name" => "Description",
					"desc" => "Enter step description",
					"id" => "step_3_desc",
					"std" => "Vestibulum ante ipsum primis in faucibus orci luctus et ultrices.",
					"type" => "textarea");

$options[] = array( "name" => "Link URL",
					"desc" => "Step link",
					"id" => "step_3_link",
					"std" => "#",
					"class" => "small",
					"type" => "text");

						
$options[] = array( "name" => "Fullscreen Media",
					"type" => "heading");
					
	$options[] = array( "name" => "Audio Settings",
						"type" => "info");
					
	$options[] = array( "name" => "Loop Audio Clip",
						"desc" => "Loop the audio clip for fullscreen slideshows",
						"id" => "audio_loop",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "On-start volume",
						"desc" => "Volume to start with",
						"id" => "audio_volume",
						"min" => "1",
						"max" => "100",
						"step" => "0",
						"unit" => '%',
						"std" => "75",
						"type" => "text");
						
	$options[] = array( "name" => "Video Settings",
						"type" => "info");
						
	$options[] = array( "name" => "Video Control bar",
						"desc" => "Show or hide video control bar. ( Doesn't support fullscreen Vimeo video )",
						"id" => "video_control_bar",
						"std" => "none",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array(
							'none' => "None",
							'over' => "Over",
							'bottom' => "Bottom")
						);
						
	$options[] = array( "name" => "Slideshow Settings",
						"type" => "info");
						
	// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left					
	$options[] = array( "name" => "Transition",
						"desc" => "Transition type",
						"id" => "slideshow_transition",
						"std" => "1",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array(
							'1' => "Fade",
							'2' => "Slide Top",
							'3' => "Slide Right",
							'4' => "Slide Bottom",
							'5' => "Slide Left",
							'6' => "Carousel Right",
							'7' => "Carousel Left",
							'0' => "None")
						);
						
	$options[] = array( "name" => "Auto Play Slideshow",
						"desc" => "Auto start slideshow on load",
						"id" => "slideshow_autoplay",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Pause on last slide",
						"desc" => "Pause on end of slideshow",
						"id" => "slideshow_pause_on_last",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Pause on hover",
						"desc" => "Pause slideshow on hover",
						"id" => "slideshow_pause_hover",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Vertical center",
						"desc" => "Vertical center images",
						"id" => "slideshow_vertical_center",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Horizontal center",
						"desc" => "Horizontal center images",
						"id" => "slideshow_horizontal_center",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Fit portrait",
						"desc" => "Portrait images will not exceed browser height",
						"id" => "slideshow_portrait",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Fit Landscape",
						"desc" => "Landscape images will not exceed browser width",
						"id" => "slideshow_landscape",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Fit Always",
						"desc" => "Image will never exceed browser width or height.",
						"id" => "slideshow_fit_always",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Slide Interval",
						"desc" => "Length between transitions",
						"id" => "slideshow_interval",
						"min" => "500",
						"max" => "20000",
						"step" => "0",
						"unit" => 'px',
						"std" => "8000",
						"type" => "text");
						
	$options[] = array( "name" => "Transition speed",
						"desc" => "Speed of transition",
						"id" => "slideshow_transition_speed",
						"std" => "1000",
						"min" => "500",
						"max" => "20000",
						"step" => "0",
						"unit" => 'px',
						"type" => "text");
						
$options[] = array( "name" => "Fonts",
					"type" => "heading");
					
$options[] = array( "name" => "Enable Default Google Web Fonts",
					"desc" => "Enable default Google Web fonts",
					"id" => "default_googlewebfonts",
					"std" => "1",
					"type" => "checkbox");
						
	$options[] = array(	"name" =>"Menu Font",
						"desc" => "Select menu font",
						"id" => "menu_font",
						"std" => 'PT Sans',
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_fonts);
						
	$options[] = array(	"name" =>"Heading Font (applies to all headings)",
						"desc" => "Select heading font",
						"id" => "heading_font",
						"std" => 'PT Sans',
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_fonts);	
						
	$options[] = array(	"name" =>"Contents post/page heading (overide)",
						"desc" => "Select font for headings inside posts and pages",
						"id" => "page_headings",
						"std" => 'PT Sans',
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_fonts);
						
	$options[] = array(	"name" =>"Slideshow Title font",
						"desc" => "Select font for slideshow title",
						"id" => "super_title",
						"std" => 'PT Sans',
						"type" => "select",
						"class" => "small", //mini, tiny, small
						"options" => $options_fonts);
						
$options[] = array( "name" => "Blog",
					"type" => "heading");
					
	$options[] = array( "name" => "Display Postformat icons",
						"desc" => "Display postformat icons",
						"id" => "postformat_icons",
						"std" => "1",
						"type" => "checkbox");
					
	$options[] = array( "name" => "Display Post info after Post",
						"desc" => "Display post info after each post",
						"id" => "blog_postinfo",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Hide allowed HTML tags info",
						"desc" => "Hide allowed HTML tags info after comments box",
						"id" => "blog_allowedtags",
						"std" => "1",
						"type" => "checkbox");
					
	$options[] = array( "name" => "Time format for blog posts",
						"desc" => "Switch from traditional or modern time",
						"id" => "mtheme_datetime",
						"std" => "timeago",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array(
							'modern' => "Modern time",
							'traditional' => "Traditional")
						);
						
	$options[] = array( "name" => "Read more text",
						"desc" => "Enter text for Read more",
						"id" => "read_more",
						"std" => "Continue reading",
						"class" => "small",
						"type" => "text");
						
$options[] = array( "name" => "Contact Template",
					"type" => "heading");
					
	$options[] = array( "name" => "Section title",
						"desc" => "Title for this section",
						"id" => "ctemplate_title",
						"std" => "Use form below to contact us.",
						"class" => "tiny",
						"type" => "textarea");
						
	$options[] = array( "name" => "Email address",
						"desc" => "Email address to recieve mail",
						"id" => "ctemplate_email",
						"std" => "email@address.com",
						"class" => "tiny",
						"type" => "text");
						
	$options[] = array( "name" => "Label- Name Field",
						"desc" => "Label for name field",
						"id" => "ctemplate_lname",
						"std" => "Name",
						"class" => "tiny",
						"type" => "text");
						
	$options[] = array( "name" => "Label- Email Field",
						"desc" => "Label for email field",
						"id" => "ctemplate_lemail",
						"std" => "E-mail",
						"class" => "tiny",
						"type" => "text");
						
	$options[] = array( "name" => "Label- Subject Field",
						"desc" => "Label for subject field",
						"id" => "ctemplate_lsubject",
						"std" => "Subject",
						"class" => "tiny",
						"type" => "text");
						
	$options[] = array( "name" => "Label- Message Field",
						"desc" => "Label for message field",
						"id" => "ctemplate_lmessage",
						"std" => "Message",
						"class" => "tiny",
						"type" => "text");
						
	$options[] = array( "name" => "Error Notice - For no name input",
						"desc" => "Error Notice - For no name input",
						"id" => "ctemplate_errorname",
						"std" => "Please enter name",
						"class" => "small",
						"type" => "text");
						
	$options[] = array( "name" => "Error Notice - For no email input",
						"desc" => "Error Notice - For no email input",
						"id" => "ctemplate_erroremail",
						"std" => "Please enter email",
						"class" => "small",
						"type" => "text");
						
	$options[] = array( "name" => "Error Notice - For invalid email input",
						"desc" => "Error Notice - For invalid email input",
						"id" => "ctemplate_invalidemail",
						"std" => "Please provide a valid email",
						"class" => "small",
						"type" => "text");
						
	$options[] = array( "name" => "Error Notice - For no message input",
						"desc" => "Error Notice - For no message input",
						"id" => "ctemplate_errormsg",
						"std" => "Please enter message",
						"class" => "small",
						"type" => "text");
						
	$options[] = array( "name" => "Thank you message",
						"desc" => "Thank you message",
						"id" => "ctemplate_thankyou",
						"std" => "<h2>Thank you!</h2>Your message was sent! This message along with the contact form labels are editable from theme options.",
						"class" => "tiny",
						"type" => "textarea");
						
	$options[] = array( "name" => "Button text",
						"desc" => "Button text for form",
						"id" => "ctemplate_button",
						"std" => "Send",
						"class" => "tiny",
						"type" => "text");

$options[] = array( "name" => "Sidebars",
					"type" => "heading");
						
	$options[] = array( "name" => "Activate Sidebars by filling the text box with a custom name",
						"type" => "info");

				
	for ($sidebar_count=1; $sidebar_count <=MAX_SIDEBARS; $sidebar_count++ ) {
						
		$options[] = array( "name" => "Sidebar " . $sidebar_count,
						"desc" => $sidebar_message,
						"id" => "theme_sidebar".$sidebar_count,
						"std" => "",
						"class" => "small",
						"type" => "text");
	}
	

						
$options[] = array( "name" => "Colors",
					"type" => "heading");
					
$options[] = array( "name" => "General",
						"type" => "info");
						
$options[] = array( "name" => "Highlight Links / Accents color",
					"desc" => "Highlight Links / Accents color",
					"id" => "accent_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Highlight Links / Accents hover color",
					"desc" => "Highlight Links / Accents hover color",
					"id" => "accent_color_hovers",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Menu",
						"type" => "info");
						
	$options[] = array( "name" => "Menu item description color",
						"desc" => "Menu item description color",
						"id" => "photomenu_desc_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Menu link color",
						"desc" => "Menu link color",
						"id" => "photomenu_link_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Menu link hover color",
						"desc" => "Menu link hover color",
						"id" => "photomenu_linkhover_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Menu item hover background color",
						"desc" => "Menu item hover background color",
						"id" => "photomenu_hover_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Menu subcategory background color",
						"desc" => "Menu subcategory background color",
						"id" => "photomenusubcat_color",
						"std" => "",
						"type" => "color");
						
$options[] = array( "name" => "Slideshow",
						"type" => "info");
						
	$options[] = array( "name" => "Slideshow Control panel background color",
						"desc" => "Slideshow Control panel background color",
						"id" => "slideshow_controlbg",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Slideshow Caption background color",
						"desc" => "Slideshow Caption background color",
						"id" => "slideshow_captionbg",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Slideshow Title color",
						"desc" => "Slideshow Title color",
						"id" => "slideshow_titletext",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Slideshow Description color",
						"desc" => "Slideshow Description color",
						"id" => "slideshow_desctext",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Slideshow progress bar color",
						"desc" => "Slideshow progress bar color",
						"id" => "slideshow_transbar",
						"std" => "",
						"type" => "color");
						
						
$options[] = array( "name" => "Page",
						"type" => "info");
						
	$options[] = array( "name" => "Page Top bar color",
						"desc" => "Page Top bar color",
						"id" => "pagetop_bar",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Fullscreen Toggle button color",
						"desc" => "Fullscreen Toggle button color",
						"id" => "fullscreen_toggle_color",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Page background",
						"desc" => "Page background",
						"id" => "content_pagebg",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Page title color",
						"desc" => "Page title color",
						"id" => "content_title",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Page title background",
						"desc" => "Page title background",
						"id" => "content_titlebg",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Sidebar title",
						"desc" => "Sidebar title",
						"id" => "sidebar_title",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Sidebar background",
						"desc" => "Sidebar background",
						"id" => "sidebar_background",
						"std" => "",
						"type" => "color");
						
$options[] = array( "name" => "Contents",
						"type" => "info");
						
	$options[] = array( "name" => "Content titles",
						"desc" => "Content titles",
						"id" => "content_titles",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Content text",
						"desc" => "Content text",
						"id" => "content_text",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Content title link color",
						"desc" => "Content title link color",
						"id" => "content_titlelinks",
						"std" => "",
						"type" => "color");
						
	$options[] = array( "name" => "Content title link hover color",
						"desc" => "Content title link hover color",
						"id" => "content_titlehover",
						"std" => "",
						"type" => "color");
						
$options[] = array( "name" => "Footer",
					"type" => "heading");
					
	$options[] = array( "name" => "Copyright text",
						"desc" => "Enter your copyright and other texts to display in footer",
						"id" => "footer_copyright",
						"std" => "Copyright 2013",
						"type" => "textarea");
						
	$options[] = array( "name" => "Footer Scripts",
						"desc" => "Enter footer scripts. eg. Google Analytics. ",
						"id" => "footer_scripts",
						"std" => "",
						"type" => "textarea");

$options[] = array( "name" => "Export",
					"type" => "heading");

	$options[] = array( "name" => "Export Options ( Copy this ) Readonly.",
						"desc" => "Select All, copy and store your theme options backup. You can use these value to import theme options settings.",
						"id" => "exportpack",
						"std" => '',
						"class" => "big",
						"type" => "textarea");

$options[] = array( "name" => "Import Options",
					"type" => "heading",
					"subheading" => 'exportpack');

	$options[] = array( "name" => "Import Options ( Paste and Save )",
						"desc" => "CAUTION: Copy and Paste the Export Options settings into the window and Save to apply theme options settings.",
						"id" => "importpack",
						"std" => '',
						"class" => "big",
						"type" => "textarea");	
	return $options;
}

?>