<?php
global $cjpopups_item_options;

$social_services_array = array(
	'Facebook' => __('Facebook', 'cjpopups'),
	'Twitter' => __('Twitter', 'cjpopups'),
);

$cjpopups_item_options['plugin_addon_options'] = array(
	array(
		'type' => 'textarea',
		'id' => 'cjpopups_social_services',
		'label' => '',
		'info' => '',
		'suffix' => '',
		'prefix' => '',
		'default' => $social_services_array,
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);