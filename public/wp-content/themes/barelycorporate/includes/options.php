<?php
/**
 * Use Options API to add options onto options already
 * present in framework. This is possible in Theme Blvd
 * Framework 2.1.0+.
 *
 * @since 3.1.0
 */
function barelycorporate_options() {

	// Textures
	$texture_options = array( 'none' => __( 'None', 'themeblvd' ) );
	$textures = themeblvd_get_textures();
	foreach( $textures as $id => $atts ) {
		$texture_options[$id] = $atts['name'];
	}

	// Add Styles
	themeblvd_add_option_tab( 'styles', __( 'Styles', 'themeblvd' ), true );

	// Add Styles > Main section
	$main_options = array(
		array(
			'name' 		=> __( 'Layout Shape', 'themeblvd' ),
			'desc'		=> __( 'Select the primary layout shape of the site.', 'themeblvd' ),
			'id'		=> 'layout_shape',
			'std'		=> 'layout_stretch',
			'type' 		=> 'select',
			'options'	=> array(
				'layout_stretch' 	=> __( 'Stretch', 'themeblvd' ),
				'layout_boxed' 		=> __( 'Boxed', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Content Color', 'themeblvd' ),
			'desc'		=> __( 'Select the primary color. This color makes up the background of the site.', 'themeblvd' ),
			'id'		=> 'content_color',
			'std'		=> 'content_light',
			'type' 		=> 'select',
			'options'	=> array(
				'content_dark' 			=> __( 'Dark', 'themeblvd' ),
				'content_light' 		=> __( 'Light', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Primary Color', 'themeblvd' ),
			'desc' 		=> __( 'This color gets applied to the background of the header, as well as being applied to a couple different highlighted areas of the theme.', 'themeblvd' ),
			'id' 		=> 'primary_color',
			'std' 		=> '#8dbacd',
			'type' 		=> 'color'
		),
		array(
			'name' 		=> __( 'Header Texture', 'themeblvd' ),
			'desc'		=> __( 'Select the header you\'d like applied to the header.', 'themeblvd' ),
			'id'		=> 'header_texture',
			'std'		=> 'diagnol_thin',
			'type' 		=> 'select',
			'options'	=> $texture_options
		),
		array(
			'name' 		=> __( 'Socia Media Icon Placement', 'themeblvd' ),
			'desc'		=> __( 'Select if you\'d like the the social icons to be placed on the right or left of the header.', 'themeblvd' ),
			'id'		=> 'social_align',
			'std'		=> 'social_right',
			'type' 		=> 'select',
			'options'	=> array(
				'social_left' 	=> __( 'Left', 'themeblvd' ),
				'social_right' 	=> __( 'Right', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Socia Media Icon Style', 'themeblvd' ),
			'desc'		=> __( 'Select the style for your social media buttons depending on the background color you chose. <em>Light</em> will look good on dark background, <em>Dark</em> will look good on a light background, and <em>Grey</em> will look good on a white background.', 'themeblvd' ),
			'id'		=> 'social_media_style',
			'std'		=> 'light',
			'type' 		=> 'select',
			'options'	=> array(
				'light' 		=> __( 'Light', 'themeblvd' ),
				'dark' 			=> __( 'Dark', 'themeblvd' ),
				'grey'			=> __( 'Grey', 'themeblvd' ),
				'color'			=> __( 'Color', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Header Logo Alignment', 'themeblvd' ),
			'desc'		=> __( 'Select how you\'d like the main logo to be aligned in the header.', 'themeblvd' ),
			'id'		=> 'logo_align',
			'std'		=> 'logo_center',
			'type' 		=> 'select',
			'options'	=> array(
				'logo_center' 	=> __( 'Center', 'themeblvd' ),
				'logo_left' 	=> __( 'Left', 'themeblvd' ),
				'logo_right' 	=> __( 'Right', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Menu Alignment', 'themeblvd' ),
			'desc'		=> __( 'Select how you\'d like the main menu to be aligned in the header.', 'themeblvd' ),
			'id'		=> 'menu_align',
			'std'		=> 'menu_center',
			'type' 		=> 'select',
			'options'	=> array(
				'menu_center' 	=> __( 'Center', 'themeblvd' ),
				'menu_left' 	=> __( 'Left', 'themeblvd' ),
				'menu_right' 	=> __( 'Right', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Menu Button Style', 'themeblvd' ),
			'desc'		=> __( 'Choose the transparent background style for the buttons of the main menu.', 'themeblvd' ),
			'id'		=> 'menu_style',
			'std'		=> 'menu_light',
			'type' 		=> 'select',
			'options'	=> array(
				'menu_dark' 	=> __( 'Dark', 'themeblvd' ),
				'menu_light' 	=> __( 'Light', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Menu Text Color', 'themeblvd' ),
			'desc'		=> __( 'This color will applied to the text of the main menu buttons when they are NOT hovered over.', 'themeblvd' ),
			'id'		=> 'menu_text',
			'std' 		=> '#ffffff',
			'type' 		=> 'color'
		),
		array(
			'name' 		=> __( 'Menu Accent Color', 'themeblvd' ),
			'desc'		=> __( 'This color will run along the bottom of the main menu. It will also serve as the background color for the main buttons when hovered and also the background color for the main menu\'s drop downs.', 'themeblvd' ),
			'id'		=> 'menu_accent',
			'std' 		=> '#ffffff',
			'type' 		=> 'color'
		),
		array(
			'name' 		=> __( 'Menu Accent Text', 'themeblvd' ),
			'desc'		=> __( 'This color will apply to the the text of main menu buttons when they are hovered on. This color will also apply to the text in the main menu\'s drop downs.', 'themeblvd' ),
			'id'		=> 'menu_accent_text',
			'std' 		=> '#858585',
			'type' 		=> 'color'
		)
	);
	themeblvd_add_option_section( 'styles', 'main_styles', 'Main', null, $main_options, false );

	// Add Styles > Links section
	$links_options = array(
		'link_color' => array(
			'name' 		=> __( 'Link Color', 'themeblvd' ),
			'desc' 		=> __( 'Choose the color you\'d like applied to links.', 'themeblvd' ),
			'id' 		=> 'link_color',
			'std' 		=> '#2a9ed4',
			'type' 		=> 'color'
		),
		'link_hover_color' => array(
			'name' 		=> __( 'Link Hover Color', 'themeblvd' ),
			'desc' 		=> __( 'Choose the color you\'d like applied to links when they are hovered over.', 'themeblvd' ),
			'id' 		=> 'link_hover_color',
			'std' 		=> '#1a5a78',
			'type' 		=> 'color'
		)
	);
	themeblvd_add_option_section( 'styles', 'links', __( 'Links', 'themeblvd' ), null, $links_options, false );

	// Add Styles > Typography section
	$typography_options = array(
		array(
			'name' 		=> __( 'Primary Font', 'themeblvd' ),
			'desc' 		=> __( 'This applies to most of the text on your site.', 'themeblvd' ),
			'id' 		=> 'typography_body',
			'std' 		=> array('size' => '12px', 'style' => 'normal', 'face' => 'lucida', 'color' => '', 'google' => ''),
			'atts'		=> array('size', 'style', 'face'),
			'type' 		=> 'typography'
		),
		array(
			'name' 		=> __( 'Header Font', 'themeblvd' ),
			'desc' 		=> __( 'This applies to all of the primary headers throughout your site (h1, h2, h3, h4, h5, h6). This would include header tags used in redundant areas like widgets and the content of posts and pages.', 'themeblvd' ),
			'id' 		=> 'typography_header',
			'std' 		=> array('size' => '', 'style' => 'bold', 'face' => 'helvetica', 'color' => '', 'google' => ''),
			'atts'		=> array('style', 'face'),
			'type' 		=> 'typography'
		),
		array(
			'name' 		=> __( 'Special Font', 'themeblvd' ),
			'desc' 		=> __( 'It can be kind of overkill to select a super fancy font for the previous option, but here is where you can go crazy. There are a few special areas in this theme where this font will get used.', 'themeblvd' ),
			'id' 		=> 'typography_special',
			'std' 		=> array('size' => '', 'style' => 'normal', 'face' => 'google', 'color' => '', 'google' => 'Josefin Sans'),
			'atts'		=> array('style', 'face'),
			'type' 		=> 'typography'
		)
	);
	themeblvd_add_option_section( 'styles', 'typography', __( 'Typography', 'themeblvd' ), null, $typography_options, false );

	// Add Styles > Custom section
	$custom_options = array(
		array(
			'name' 		=> __( 'Custom CSS', 'themeblvd' ),
			'desc' 		=> __( 'If you have some minor CSS changes, you can put them here to override the theme\'s default styles. However, if you plan to make a lot of CSS changes, it would be best to create a child theme.', 'themeblvd' ),
			'id' 		=> 'custom_styles',
			'type'		=> 'textarea'
		)
	);
	themeblvd_add_option_section( 'styles', 'custom', __( 'Custom', 'themeblvd' ), null, $custom_options, false );

	// Add social media option to Layout > Header
	$social_media = array(
		'name' 		=> __( 'Social Media Buttons', 'themeblvd' ),
		'desc' 		=> __( 'Configure the social media buttons you\'d like to show in the header of your site. Check the buttons you\'d like to use and then input the full URL you\'d like the button to link to in the corresponding text field that appears.<br><br>Example: http://twitter.com/jasonbobich<br><br><em>Note: On the "Email" button, if you want it to link to an actual email address, you would input it like this:<br><br><strong>mailto:you@youremail.com</strong></em><br><br><em>Note: If you\'re using the RSS button, your default RSS feed URL is:<br><br><strong>'.get_feed_link().'</strong></em>', 'themeblvd' ),
		'id' 		=> 'social_media',
		'std' 		=> array(
			'includes'	=>  array( 'facebook', 'google', 'twitter', 'rss' ),
			'facebook'	=> 'http://facebook.com/jasonbobich',
			'google'	=> 'https://plus.google.com/116531311472104544767/posts',
			'twitter'	=> 'http://twitter.com/jasonbobich',
			'rss'		=> get_feed_link()
		),
		'type' 		=> 'social_media'
	);
	themeblvd_add_option( 'layout', 'header', 'social_media', $social_media );

	// Add meta option for archive posts
	$archive_meta = array(
		'name' 		=> __( 'Show meta info?', 'themeblvd' ),
		'desc' 		=> __( 'Choose whether you want to show meta information under the title of each post.', 'themeblvd' ),
		'id' 		=> 'archive_meta',
		'std' 		=> 'show',
		'type' 		=> 'radio',
		'options' 	=> array(
			'show'	=> __( 'Show meta info.', 'themeblvd' ),
			'hide' 	=> __( 'Hide meta info.', 'themeblvd' )
		)
	);
	themeblvd_add_option( 'content', 'archives', 'archive_meta', $archive_meta );

	// Add tags option for archive posts
	$archive_meta = array(
		'name' 		=> __( 'Show tags?', 'themeblvd' ),
		'desc' 		=> __( 'Choose whether you want to show tags under at the bottom of each post.', 'themeblvd' ),
		'id' 		=> 'archive_tags',
		'std' 		=> 'show',
		'type' 		=> 'radio',
		'options' 	=> array(
			'show'	=> __( 'Show tags.', 'themeblvd' ),
			'hide' 	=> __( 'Hide tags.', 'themeblvd' )
		)
	);
	themeblvd_add_option( 'content', 'archives', 'archive_tags', $archive_meta );

	// Add post list options
	$post_list_description = __( 'These options apply to posts when they are shown from within any post list throughout your site. This includes the Primary Posts Display described above, as well.<br><br>Note: It may be confusing why these options are not present when editing a specific post list. The reason is because the options when working with a specific post list are incorporated into the actual theme framework, while these settings have been added to this particular theme design for your conveniance.', 'themeblvd' );
	$post_list = array(
		array(
			'name' 		=> __( 'Show meta info?', 'themeblvd' ),
			'desc' 		=> __( 'Choose whether you want to show meta information under the title of each post.', 'themeblvd' ),
			'id' 		=> 'post_list_meta',
			'std' 		=> 'show',
			'type' 		=> 'radio',
			'options' 	=> array(
				'show'	=> __( 'Show meta info.', 'themeblvd' ),
				'hide' 	=> __( 'Hide meta info.', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Show tags?', 'themeblvd' ),
			'desc' 		=> __( 'Choose whether you want to show tags under at the bottom of each post.', 'themeblvd' ),
			'id' 		=> 'post_list_tags',
			'std' 		=> 'show',
			'type' 		=> 'radio',
			'options' 	=> array(
				'show'	=> __( 'Show tags.', 'themeblvd' ),
				'hide' 	=> __( 'Hide tags.', 'themeblvd' )
			)
		)

	);
	themeblvd_add_option_section( 'content', 'post_list', __( 'Post Lists', 'themeblvd' ), $post_list_description, $post_list );

	// Add post grid options
	$post_grid_description = __( 'These options apply to posts when they are shown from within any post grid throughout your site.<br><br>Note: It may be confusing why these options are not present when editing a specific post grid. The reason is because the options when working with a specific post grid are incorporated into the actual theme framework, while these settings have been added to this particular theme design for your conveniance.', 'themeblvd' );
	$post_grid = array(
		array(
			'name' 		=> __( 'Show title?', 'themeblvd' ),
			'desc' 		=> __( 'Choose whether or not you want to show the title below each featured image in post grids.', 'themeblvd' ),
			'id' 		=> 'post_grid_title',
			'std' 		=> 'show',
			'type' 		=> 'radio',
			'options' 	=> array(
				'show'	=> __( 'Show titles.', 'themeblvd' ),
				'hide' 	=> __( 'Hide titles.', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Show excerpts?', 'themeblvd' ),
			'desc' 		=> __( 'Choose whether or not you want to show the excerpt on each post.', 'themeblvd' ),
			'id' 		=> 'post_grid_excerpt',
			'std' 		=> 'hide',
			'type' 		=> 'radio',
			'options' 	=> array(
				'show'	=> __( 'Show excerpts.', 'themeblvd' ),
				'hide' 	=> __( 'Hide excerpts.', 'themeblvd' )
			)
		),
		array(
			'name' 		=> __( 'Show buttons?', 'themeblvd' ),
			'desc' 		=> __( 'Choose whether or not you want to show a button that links to the single post.', 'themeblvd' ),
			'id' 		=> 'post_grid_button',
			'std' 		=> 'hide',
			'type' 		=> 'radio',
			'options' 	=> array(
				'show'	=> __( 'Show buttons.', 'themeblvd' ),
				'hide' 	=> __( 'Hide buttons.', 'themeblvd' )
			)
		)
	);
	themeblvd_add_option_section( 'content', 'post_grid', __( 'Post Grids', 'themeblvd' ), $post_grid_description, $post_grid, false );

	// Add Configuration tab, if it doesn't exist.
	themeblvd_add_option_tab( 'config', __( 'Configuration', 'themeblvd' ) );

	$responsiveness = array(
		'responsive_css' => array(
			'name' 		=> __( 'Tablets and Mobile Devices', 'themeblvd' ),
			'desc' 		=> __( 'This theme comes with a special stylesheet that will target the screen resolution of your website vistors and show them a slightly modified design if their screen resolution matches common sizes for a tablet or a mobile device.', 'themeblvd' ),
			'id' 		=> 'responsive_css',
			'std' 		=> 'true',
			'type' 		=> 'radio',
			'options' 	=> array(
				'true'		=> __( 'Yes, apply special styles to tablets and mobile devices.', 'themeblvd' ),
				'false' 	=> __( 'No, allow website to show normally on tablets and mobile devices.', 'themeblvd' )
			)
		),
		'mobile_nav' => array(
			'name' 		=> __( 'Mobile Navigation', 'themeblvd' ),
			'desc' 		=> __( 'Select how you\'d like the <em>Primary Navigation</em> displayed on mobile devices. While the graphic navigation may be more visually appealing, if your navigation is more complex with many dropdown items, you possibly could be providing a better user experience to mobile users by having the navigation converted into a simple select menu.<br><br>A nice compromise between the two options discussed above is the first default option that will display a button for the user to toggle the main navigation on and off.', 'themeblvd' ),
			'id' 		=> 'mobile_nav',
			'std' 		=> 'mobile_nav_toggle_graphic',
			'type' 		=> 'radio',
			'options' 	=> array(
				'mobile_nav_toggle_graphic'	=> __( 'Display graphic navigation that toggles on/off.', 'themeblvd' ),
				'mobile_nav_graphic'		=> __( 'Display graphic navigation.', 'themeblvd' ),
				'mobile_nav_select' 		=> __( 'Display simple select menu.', 'themeblvd' )
			)
		)
	);
	themeblvd_add_option_section( 'config', 'responsiveness', __( 'Responsiveness', 'themeblvd' ), '', $responsiveness, true );

	// Modify framework options
	themeblvd_edit_option( 'content', 'blog', 'blog_content', 'std', 'excerpt' );
	themeblvd_edit_option( 'layout', 'header', 'logo', 'std', array( 'type' => 'image', 'image' => get_template_directory_uri().'/assets/images/logo.png', 'image_width' => '300', 'image_2x' => get_template_directory_uri().'/assets/images/logo_2x.png' ) );

}
add_action( 'after_setup_theme', 'barelycorporate_options' );

/**
 * Setup theme for customizer.
 *
 * @since 3.1.0
 */
function barelycorporate_customizer(){

	// Textures
	$texture_options = array( 'none' => __( 'None', 'themeblvd' ) );
	$textures = themeblvd_get_textures();
	foreach( $textures as $id => $atts ) {
		$texture_options[$id] = $atts['name'];
	}

	// Setup logo options
	$logo_options = array(
		'logo' => array(
			'name' 		=> __( 'Logo', 'themeblvd' ),
			'id' 		=> 'logo',
			'type' 		=> 'logo',
			'transport'	=> 'postMessage'
		)
	);
	themeblvd_add_customizer_section( 'logo', __( 'Logo', 'themeblvd' ), $logo_options, 1 );

	// Setup main styles options
	$main_options = array(
		'layout_shape' => array(
			'name' 		=> __( 'Layout Shape', 'themeblvd' ),
			'id'		=> 'layout_shape',
			'type' 		=> 'select',
			'options'	=> array(
				'layout_stretch' 	=> __( 'Stretch', 'themeblvd' ),
				'layout_boxed' 		=> __( 'Boxed', 'themeblvd' )
			),
			'transport'	=> 'postMessage',
			'priority'	=> 1
		),
		'content_color' => array(
			'name' 		=> __( 'Content Color', 'themeblvd' ),
			'id'		=> 'content_color',
			'type' 		=> 'select',
			'options'	=> array(
				'content_dark' 		=> __( 'Dark', 'themeblvd' ),
				'content_light' 	=> __( 'Light', 'themeblvd' )
			),
			'transport'	=> 'refresh',
			'priority'	=> 2
		),
		'primary_color' => array(
			'name' 		=> __( 'Primary Color', 'themeblvd' ),
			'id' 		=> 'primary_color',
			'type' 		=> 'color',
			'transport'	=> 'postMessage',
			'priority'	=> 3
		),
		'header_texture' => array(
			'name' 		=> __( 'Header Texture', 'themeblvd' ),
			'id'		=> 'header_texture',
			'type' 		=> 'select',
			'options'	=> $texture_options,
			'transport'	=> 'postMessage',
			'priority'	=> 4
		),
		'social_align' => array(
			'name' 		=> __( 'Header Icons Placement', 'themeblvd' ),
			'id'		=> 'social_align',
			'type' 		=> 'select',
			'options'	=> array(
				'social_left' 	=> __( 'Left', 'themeblvd' ),
				'social_right' 	=> __( 'Right', 'themeblvd' )
			),
			'transport'	=> 'postMessage',
			'priority'	=> 5
		),
		'logo_align' => array(
			'name' 		=> __( 'Header Logo Alignment', 'themeblvd' ),
			'id'		=> 'logo_align',
			'type' 		=> 'select',
			'options'	=> array(
				'logo_center' 	=> __( 'Center', 'themeblvd' ),
				'logo_left' 	=> __( 'Left', 'themeblvd' ),
				'logo_right' 	=> __( 'Right', 'themeblvd' )
			),
			'transport'	=> 'postMessage',
			'priority'	=> 6
		),
		'menu_align' => array(
			'name' 		=> __( 'Menu Alignment', 'themeblvd' ),
			'id'		=> 'menu_align',
			'type' 		=> 'select',
			'options'	=> array(
				'menu_center' 	=> __( 'Center', 'themeblvd' ),
				'menu_left' 	=> __( 'Left', 'themeblvd' ),
				'menu_right' 	=> __( 'Right', 'themeblvd' )
			),
			'transport'	=> 'postMessage',
			'priority'	=> 7
		),
		'menu_style' => array(
			'name' 		=> __( 'Menu Button Style', 'themeblvd' ),
			'id'		=> 'menu_style',
			'type' 		=> 'select',
			'options'	=> array(
				'menu_dark' 	=> __( 'Dark', 'themeblvd' ),
				'menu_light' 	=> __( 'Light', 'themeblvd' )
			),
			'transport'	=> 'postMessage',
			'priority'	=> 8
		),
		'menu_text' => array(
			'name' 		=> __( 'Menu Text Color', 'themeblvd' ),
			'id'		=> 'menu_text',
			'type' 		=> 'color',
			'transport'	=> 'postMessage',
			'priority'	=> 9
		),
		'menu_accent' => array(
			'name' 		=> __( 'Menu Accent Color', 'themeblvd' ),
			'id'		=> 'menu_accent',
			'type' 		=> 'color',
			'transport'	=> 'postMessage',
			'priority'	=> 10
		)
	);
	themeblvd_add_customizer_section( 'main_styles', __( 'Main Styles', 'themeblvd' ), $main_options, 2 );

	// Setup primary font options
	$font_options = array(
		'typography_body' => array(
			'name' 		=> __( 'Primary Font', 'themeblvd' ),
			'id' 		=> 'typography_body',
			'atts'		=> array('size', 'style', 'face'),
			'type' 		=> 'typography',
			'transport'	=> 'postMessage'
		),
		'typography_header' => array(
			'name' 		=> __( 'Header Font', 'themeblvd' ),
			'id' 		=> 'typography_header',
			'atts'		=> array('style', 'face'),
			'type' 		=> 'typography',
			'transport'	=> 'postMessage'
		),
		'typography_special' => array(
			'name' 		=> __( 'Special Font', 'themeblvd' ),
			'id' 		=> 'typography_special',
			'atts'		=> array('style', 'face'),
			'type' 		=> 'typography',
			'transport'	=> 'postMessage'
		)
	);
	themeblvd_add_customizer_section( 'typography', __( 'Typography', 'themeblvd' ), $font_options, 103 );

	$link_options = array(
		'link_color' => array(
			'name' 		=> __( 'Link Color', 'themeblvd' ),
			'id' 		=> 'link_color',
			'type' 		=> 'color'
		),
		'link_hover_color' => array(
			'name' 		=> __( 'Link Hover Color', 'themeblvd' ),
			'id' 		=> 'link_hover_color',
			'type' 		=> 'color'
		)
	);
	themeblvd_add_customizer_section( 'links', __( 'Links', 'themeblvd' ), $link_options, 104 );

	// Setup custom styles option
	$custom_styles_options = array(
		'custom_styles' => array(
			'name' 		=> __( 'Enter styles to preview their results.', 'themeblvd' ),
			'id' 		=> 'custom_styles',
			'type' 		=> 'textarea',
			'transport'	=> 'postMessage'
		)
	);
	themeblvd_add_customizer_section( 'custom_css', __( 'Custom CSS', 'themeblvd' ), $custom_styles_options, 121 );
}
add_action( 'after_setup_theme', 'barelycorporate_customizer' );

/**
 * Add specific theme elements to customizer.
 *
 * @since 3.1.0
 */
function barelycorporate_customizer_init( $wp_customize ){
	// Add real-time option edits
	if( $wp_customize->is_preview() && ! is_admin() ){
		add_action( 'wp_footer', 'barelycorporate_customizer_preview', 21 );
	}
}
add_action( 'customize_register', 'barelycorporate_customizer_init' );

/**
 * Add real-time option edits for this theme in customizer.
 *
 * @since 3.1.0
 */
function barelycorporate_customizer_preview(){

	// Global option name
	$option_name = themeblvd_get_option_name();

	// Begin output
	?>
	<script type="text/javascript">
	window.onload = function(){ // window.onload for silly IE9 bug fix
		(function($){

			// Variables
			var template_url = "<?php echo get_template_directory_uri(); ?>";

			// ---------------------------------------------------------
			// Logo
			// ---------------------------------------------------------

			<?php themeblvd_customizer_preview_logo(); ?>

			// ---------------------------------------------------------
			// Main Styles
			// ---------------------------------------------------------

			var special_font_selectors = '#featured .media-full .slide-title, #content .media-full .slide-title, #featured_below .media-full .slide-title, .element-slogan .slogan .slogan-text, .element-tweet, .special-font ';

			/* Layout Shape */
			wp.customize('<?php echo $option_name; ?>[layout_shape]',function( value ) {
				value.bind(function(value) {
					$('body').removeClass('layout_boxed layout_stretch');
					$('body').addClass(value);
				});
			});

			/* Primary Color */
			wp.customize('<?php echo $option_name; ?>[primary_color]',function( value ) {
				value.bind(function(value) {
					$('#top, .tb-btn-gradient .btn-default,.tb-btn-gradient input[type="submit"], .tb-btn-gradient input[type="reset"], .tb-btn-gradient input[type="button"], .tb-btn-gradient button').css('background-color', value);
					$('.tb-btn-gradient .btn-default,.tb-btn-gradient input[type="submit"], .tb-btn-gradient input[type="reset"], .tb-btn-gradient input[type="button"], .tb-btn-gradient button').css('border-color', value);
				});
			});

			/* Header Texture */
			wp.customize('<?php echo $option_name; ?>[header_texture]',function( value ) {
				value.bind(function(value) {
					$('#top').css('background-image', 'url('+template_url+'/framework/assets/images/textures/'+value+'.png)' );
				});
			});

			/* Social Alignment */
			wp.customize('<?php echo $option_name; ?>[social_align]',function( value ) {
				value.bind(function(value) {
					$('body').removeClass('social_left social_right');
					$('body').addClass(value);
				});
			});

			/* Logo Alignment */
			wp.customize('<?php echo $option_name; ?>[logo_align]',function( value ) {
				value.bind(function(value) {
					$('body').removeClass('logo_center logo_left logo_right');
					$('body').addClass(value);
				});
			});

			/* Menu Alignment */
			wp.customize('<?php echo $option_name; ?>[menu_align]',function( value ) {
				value.bind(function(value) {
					$('body').removeClass('menu_center menu_left menu_right');
					$('body').addClass(value);
				});
			});

			/* Menu Style */
			wp.customize('<?php echo $option_name; ?>[menu_style]',function( value ) {
				value.bind(function(value) {
					$('body').removeClass('menu_light menu_dark');
					$('body').addClass(value);
				});
			});

			/* Menu Text */
			wp.customize('<?php echo $option_name; ?>[menu_text]',function( value ) {
				value.bind(function(value) {
					var sub_menu_color = $('#access li li a').css('color');
					$('#access li a').css('color', value);
					$('#access li li a').css('color', sub_menu_color);
				});
			});

			/* Menu Accent */
			wp.customize('<?php echo $option_name; ?>[menu_accent]',function( value ) {
				value.bind(function(value) {
					$('.menu_accent_preview').remove();
					$('head').append('<style class="menu_accent_preview">#top #branding {border-color: '+value+'} #access li a:hover, #access ul ul {background-color: '+value+'}</style>');
				});
			});

			/* Menu Accent Text */
			wp.customize('<?php echo $option_name; ?>[menu_accent_text]',function( value ) {
				value.bind(function(value) {
					$('.menu_accent_text_preview').remove();
					$('head').append('<style class="menu_accent_text_preview">#access li a:hover, #access li li a {color: '+value+'}</style>');
				});
			});

			// ---------------------------------------------------------
			// Typography
			// ---------------------------------------------------------

			<?php themeblvd_customizer_preview_font_prep(); ?>
			<?php themeblvd_customizer_preview_primary_font(); ?>
			<?php themeblvd_customizer_preview_header_font(); ?>

			// ---------------------------------------------------------
			// Special Typography
			// ---------------------------------------------------------

			var special_font_selectors = '.slide-title, .tb-slogan .slogan-text, .element-tweet, .special-font';

			/* Special Typography - Style */
			wp.customize('<?php echo $option_name; ?>[typography_special][style]',function( value ) {
				value.bind(function(style) {
					// Possible choices: normal, bold, italic, bold-italic
					if( style == 'normal' ) {
						$(special_font_selectors).css('font-weight', 'normal');
						$(special_font_selectors).css('font-style', 'normal');
					} else if( style == 'bold' ) {
						$(special_font_selectors).css('font-weight', 'bold');
						$(special_font_selectors).css('font-style', 'normal');
					} else if( style == 'italic' ) {
						$(special_font_selectors).css('font-weight', 'normal');
						$(special_font_selectors).css('font-style', 'italic');
					} else if( style == 'bold-italic' ) {
						$(special_font_selectors).css('font-weight', 'bold');
						$(special_font_selectors).css('font-style', 'italic');
					}
				});
			});

			/* Special Typography - Face */
			wp.customize('<?php echo $option_name; ?>[typography_special][face]',function( value ) {
				value.bind(function(face) {
					if( face == 'google' ){
						googleFonts.specialToggle = true;
						var google_font = googleFonts.specialName.split(":"),
							google_font = google_font[0];
						$(special_font_selectors).css('font-family', google_font);
					}
					else
					{
						googleFonts.specialToggle = false;
						$(special_font_selectors).css('font-family', fontStacks[face]);
					}
				});
			});

			/* Special Typography - Google */
			wp.customize('<?php echo $option_name; ?>[typography_special][google]',function( value ) {
				value.bind(function(google_font) {
					// Only proceed if user has actually selected for
					// a google font to show in previous option.
					if(googleFonts.specialToggle)
					{
						// Set global google font for reference in
						// other options.
						googleFonts.specialName = google_font;

						// Remove previous google font to avoid clutter.
						$('.preview_google_special_font').remove();

						// Format font name for inclusion
						var include_google_font = google_font.replace(/ /g,'+');

						// Include font
						$('head').append('<link href="http://fonts.googleapis.com/css?family='+include_google_font+'" rel="stylesheet" type="text/css" class="preview_google_special_font" />');

						// Format for CSS
						google_font = google_font.split(":");
						google_font = google_font[0];

						// Apply font in CSS
						$(special_font_selectors).css('font-family', google_font);
					}
				});
			});

			// ---------------------------------------------------------
			// Custom CSS
			// ---------------------------------------------------------

			<?php themeblvd_customizer_preview_styles(); ?>

		})(jQuery);
	} // End window.onload for silly IE9 bug
	</script>
	<?php
}