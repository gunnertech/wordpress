<?php
function cjpopups_slides_metabox( $meta_boxes ) {
	$prefix = '_cmb_'; // Prefix for all fields

	/*
	$show_on = '';
	if(isset($_GET['post']) && @$_GET['action'] == 'edit'){
		$page_template = get_post_meta($_GET['post'], '_wp_page_template', true);
		if(is_admin() && $page_template == 'page-templates/page-nav-menu.php'){
			$show_on = array('page');
		}
	}
	*/

	$enable_disable_array = array(
		array('value' => 'enable', 'name' => __('Enable', 'cjpopups')),
		array('value' => 'disable', 'name' => __('Disable', 'cjpopups')),
	);
	$yes_no_array = array(
		array('value' => 'yes', 'name' => __('Yes', 'cjpopups')),
		array('value' => 'no', 'name' => __('No', 'cjpopups')),
	);

	$meta_boxes[] = array(
		'id' => 'test_metabox',
		'title' => __('Slide Options', 'cjpopups'),
		'pages' => array('slides'), // post type
		'context' => 'advanced', //  'normal', 'advanced', or 'side'
		'priority' => 'high', //  'high', 'core', 'default' or 'low'
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => __('Slide type', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'slide_type',
				'type' => 'select',
				'options' => array(
					array('name' => __('Image - (Must Set Featured Image)', 'cjpopups'), 'value' => 'image'),
					array('name' => __('Video - (Must Specify Video URL)', 'cjpopups'), 'value' => 'video'),
					array('name' => __('Contact Map - (Must Specify Map URL)', 'cjpopups'), 'value' => 'contact-map'),
				)
			),
			array(
				'name' => __('Main Heading', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'main_heading',
				'type' => 'text'
			),
			array(
				'name' => __('Sub Heading', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'sub_heading',
				'type' => 'text'
			),
			array(
				'name' => __('Content', 'cjpopups'),
				'desc' => __('Feel free to use any shortcodes', 'cjpopups'),
				'id' => $prefix . 'content',
				'type' => 'wysiwyg'
			),
			array(
				'name' => __('Video URL', 'cjpopups'),
				'desc' => __('Require for video slides', 'cjpopups'),
				'id' => $prefix . 'video_url',
				'type' => 'text'
			),
			array(
				'name' => __('Map URL', 'cjpopups'),
				'desc' => __('Require for contact map slides', 'cjpopups'),
				'id' => $prefix . 'map_url',
				'type' => 'text'
			),
			array(
				'name' => __('Animation', 'cjpopups'),
				'desc' => __('Animation will be applied to all <u>valid</u> html elements, based on slide layout.', 'cjpopups'),
				'id' => $prefix . 'animation',
				'type' => 'select',
				'options' => array(
						array('name' => 'Option One', 'value' => 'standard'),
						array('name' => 'Option Two', 'value' => 'custom'),
						array('name' => 'Option Three', 'value' => 'none')
					)
			),
			array(
				'name' => __('Animation Delay', 'cjpopups'),
				'desc' => __('Specify delay in miliseconds. <code>e.g. 250</code> (1000ms = 1sec)', 'cjpopups'),
				'id' => $prefix . 'animation_delay',
				'type' => 'text'
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'cjpopups_slides_metabox' );