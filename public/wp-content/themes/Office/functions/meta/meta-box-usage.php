<?php
/**
 * Registering meta boxes
 *
 * In this file, I'll show you how to extend the class to add more field type (in this case, the 'taxonomy' type)
 * All the definitions of meta boxes are listed below with comments, please read them carefully.
 * Note that each validation method of the Validation Class MUST return value instead of boolean as before
 *
 * You also should read the changelog to know what has been changed
 *
 * For more information, please visit: http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
 *
 */

/********************* BEGIN DEFINITION OF META BOXES ***********************/

// prefix of meta keys, optional
// use underscore (_) at the beginning to make keys hidden, for example $prefix = '_rw_';
// you also can make prefix empty to disable it
$prefix = 'office_';

$office_meta_boxes = array();

// meta box ===> Image Slides
$office_meta_boxes[] = array(
	'id' => 'slides_meta',
	'title' => __('Image Slide Options','office'),
	'pages' => array('slides'),

	'fields' => array(
	array(
            'name' => __('Enable/Disable Caption','office'),
            'desc' => __('Select to enable or disable your slide caption.','office'),
            'id' => $prefix . 'enable_caption',
			'type' => 'select',
			'options' => array(
				'disable' => 'disable',
				'enable' => 'enable',
			),
			'multiple' => false,
			'std' => array('default')
        ),
		array(
            'name' => __('Description','office'),
            'desc' => __('Enter a description for your slide.','office'),
            'id' => $prefix . 'slides_description',
            'type' => 'textarea',
            'std' => ''
        ),
		array(
            'name' => __('Slide URL','office'),
            'desc' => __('Enter a URL to link this slide to - perfect for linking slides to pages on your site or other sites. Optional.','office'),
            'id' => $prefix . 'slides_url',
            'type' => 'text',
            'std' => ''
        ),
	)
);

// meta box ===> HP Highlights
$office_meta_boxes[] = array(
	'id' => 'hp_highlights_meta',
	'title' => __('HP Highlights Options','office'),
	'pages' => array('hp_highlights'),

	'fields' => array(
		array(
            'name' => __('URL','office'),
            'desc' => __('Enter a URL to link the title of this highlight to. Optional.','office'),
            'id' => $prefix . 'hp_highlights_url',
            'type' => 'text',
            'std' => ''
        ),
	)
);

// meta box ===> Portfolio Options
$office_meta_boxes[] = array(
	'id' => 'single_portfolio_options',
	'title' => __('Single Portfolio Options','office'),
	'pages' => array('portfolio'),

	'fields' => array(
		array(
			'name' => __('Portfolio Post Style', 'office'),
			'id' => $prefix . 'portfolio_style',
			'type' => 'select',
			'options' => array(
				'default' => 'default',
				'full' => 'full'
			),
			'multiple' => false,
			'std' => array('default'),
			'desc' => __('Select your portfolio post style.', 'office')
		),
		array(
			'name' => __('Enable FullWidth Image Slider', 'office'),
			'id' => $prefix . 'page_slider',
			'type' => 'select',
			'options' => array(
				'disable' => 'disable',
				'enable' => 'enable'
			),
			'multiple' => false,
			'std' => array('disable'),
			'desc' => __('Choose to enable or disable the page slider based on image attachments (shows all images attached to the page). Can also be used to show 1 image banner at the top.', 'office')
		),
		array(
            'name' => __('Video Embed Code','office'),
            'desc' => __('Enter your video embeded code if you want a video instead. Max width of 510px please.','office'),
            'id' => $prefix . 'portfolio_video',
            'type' => 'textarea',
            'std' => ''
        ),
		array(
            'name' => __('Cost','office'),
            'desc' => __('Enter your cost for the project details optional)','office'),
            'id' => $prefix . 'portfolio_cost',
            'type' => 'text',
            'std' => ''
        ),
		array(
            'name' => __('Client','office'),
            'desc' => __('Enter a client name the project details (optional)','office'),
            'id' => $prefix . 'portfolio_client',
            'type' => 'text',
            'std' => ''
        ),
		array(
            'name' => __('Link URL','office'),
            'desc' => __('Enter a URL for the project details (optional)','office'),
            'id' => $prefix . 'portfolio_url',
            'type' => 'text',
            'std' => ''
        ),
	)
);


// meta box ===> Staff
$office_meta_boxes[] = array(
	'id' => 'staff_options',
	'title' => __('Staff Options','office'),
	'pages' => array('staff'),

	'fields' => array(
		array(
            'name' => __('Position','office'),
            'desc' => __('Enter a position for your staff member.','office'),
            'id' => $prefix . 'staff_position',
            'type' => 'text',
            'std' => ''
        ),
	)
);


// meta box ===> Image Slider Options
$office_meta_boxes[] = array(
	'id' => 'page_option',
	'title' => __('Image Slider','office'),
	'pages' => array('page'),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => __('Enable FullWidth Image Slider', 'office'),
			'id' => $prefix . 'page_slider',
			'type' => 'select',
			'options' => array(
				'disable' => 'disable',
				'enable' => 'enable'
			),
			'multiple' => false,
			'std' => array('disable'),
			'desc' => __('Choose to enable or disable the page slider based on image attachments (shows all images attached to the page). Can also be used to show 1 image banner at the top.', 'office')
		),
	)
);

foreach ($office_meta_boxes as $meta_box) {
	new office_meta_box($meta_box);
}
?>