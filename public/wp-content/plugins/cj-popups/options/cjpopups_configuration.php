<?php
global $cjpopups_item_options;

$enable_disable_array = array(
	'enable' => __('Enable', 'cjpopups'),
	'disable' => __('Disable', 'cjpopups'),
);
$yes_no_array = array(
	'yes' => __('Yes', 'cjpopups'),
	'no' => __('No', 'cjpopups'),
);

$cjpopups_item_options['cjpopups_configuration'] = array(
	array(
		'type' => 'heading',
		'id' => 'cjpopups_configuration_heading',
		'label' => '',
		'info' => '',
		'suffix' => '',
		'prefix' => '',
		'default' => __('Plugin Configuration', 'cjpopups'),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'dropdown',
		'id' => 'show_on_backend',
		'label' => __('PopUps in Admin area', 'cjpopups'),
		'info' => __('You can choose to enable or disable popups in WordPress Admin Dashboard as well.', 'cjpopups'),
		'suffix' => '',
		'prefix' => '',
		'default' => 'disable',
		'options' => $enable_disable_array, // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'dropdown',
		'id' => 'disable_animate_css',
		'label' => __('Disable Plugin\'s Animate.css', 'cjpopups'),
		'info' => __('If your theme already call animate.css, you can choose Yes to disable plugin\'s call and use your theme\'s animate.css instead.', 'cjpopups'),
		'suffix' => '',
		'prefix' => '',
		'default' => 'no',
		'options' => $yes_no_array, // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'submit',
		'id' => '',
		'label' => __('Save Settings', 'cjpopups'),
		'info' => '',
		'suffix' => '',
		'prefix' => '',
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);