<?php
global $cjpopups_sample_widget_args;
$cjpopups_sample_widget_args = array(
	'title' => __('Social Media (cjpopups)', 'cjpopups'),
	'description' => __('Display social media links with or without icons', 'cjpopups'),
	'classname' => 'social-media',
	'width' => '250',
	'height' => '350',
	'form_options' => array(
		array(
			'type' => 'text',
			'id' => 'title',
			'label' => __('Title', 'cjpopups'),
			'info' => __('Specify widget title here.', 'cjpopups'),
			'suffix' => '',
			'prefix' => '',
			'default' => '',
			'options' => '', // array in case of dropdown, checkbox and radio buttons
		),
		array(
			'type' => 'textgroup',
			'id' => 'facebook',
			'label' => __('facebook', 'cjpopups'),
			'info' => __('Specify widget facebook here.', 'cjpopups'),
			'suffix' => '',
			'prefix' => '',
			'default' => '',
			'options' => array(
				'link_text', 'link_url', 'link_image'
			), // array in case of dropdown, checkbox and radio buttons
		),
		array(
			'type' => 'text',
			'id' => 'sub_title',
			'label' => __('sub_title', 'cjpopups'),
			'info' => __('Specify widget title here.', 'cjpopups'),
			'suffix' => '',
			'prefix' => '',
			'default' => '',
			'options' => '', // array in case of dropdown, checkbox and radio buttons
		),
	) // form array
);

class cjpopups_sample_widget_widget extends WP_Widget {

    /** constructor */
    function cjpopups_sample_widget_widget() {
    	global $cjpopups_sample_widget_args;
		$widget_ops = array('classname' => $cjpopups_sample_widget_args['classname'], 'description' => $cjpopups_sample_widget_args['description']);
		$control_ops = array('width' => $cjpopups_sample_widget_args['width'], 'height' => $cjpopups_sample_widget_args['height']);
		$this->WP_Widget($cjpopups_sample_widget_args['classname'], $cjpopups_sample_widget_args['title'] , $widget_ops, $control_ops);
    }

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cjpopups_sample_widget_args;
		extract( $args );

		foreach ($cjpopups_sample_widget_args['form_options'] as $key => $option) {
			$vars[$option['id']] = apply_filters('title', $instance[$option['id']]);
		}

		$display[] = $before_widget;
		$display[] = ($vars['title'] != '') ? $before_title.$vars['title'].$after_title : '';
		$display[] = cjpopups_sample_widget_display($vars);
		$display[] = $after_widget;

		echo implode("\n", $display);
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		global $cjpopups_sample_widget_args;
		$instance = $old_instance;
		foreach ($cjpopups_sample_widget_args['form_options'] as $key => $option) {
			$id = $option['id'];
			if(is_array($new_instance[$id])){
				$instance[$id] = implode('~~~~~', $new_instance[$id]);
			}else{
				$instance[$id] = strip_tags($new_instance[$id]);
			}
		}
	    return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		global $cjpopups_sample_widget_args;
		foreach ($cjpopups_sample_widget_args['form_options'] as $key => $value) {
			$id = $value['id'];
			$form_instance[$id]['id'] = $this->get_field_id($id);
			$form_instance[$id]['name'] = $this->get_field_name($id);
			$form_instance[$id]['value'] = @esc_attr( $instance[$id] );
		}

	    echo cjpopups_widget_form($cjpopups_sample_widget_args['form_options'], $form_instance);
	}

}
add_action('widgets_init', create_function('', 'return register_widget("cjpopups_sample_widget_widget");'));


function cjpopups_sample_widget_display($vars){
	if(is_array($vars)){
		foreach ($vars as $key => $var) {
			if(strpos($var, '~~~~~') > 0){
				$$key = explode('~~~~~', $var);
			}else{
				$$key = $var;
			}
		}
	}

	// Start display function here...
	$display[] = '';


	return  implode("\n", $display);
}
