<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
#########################################################################################
# Admin AJAX Handlers
#########################################################################################

add_action( 'wp_ajax_cjpopups_prepare_shortcode_tag', 'cjpopups_prepare_shortcode_tag' );
function cjpopups_prepare_shortcode_tag() {
	global $wpdb;
	$postdata = '';
	parse_str($_POST['form_data'], $postdata);

	$exclude_fields = array(
		'form_message',
		'shortcode_content'
	);
	$parse_vars = '';
	foreach ($postdata as $key => $value) {
		if(!in_array($key, $exclude_fields)){
			$pval = (is_array($value)) ? implode('|', $value) : $value;
			$parse_vars .= ' '.$key.'="'.$pval.'" ';
		}
	}
	$shortcode_string = null;
	$shortcode_string = '['.$_POST['shortcode'];
	$shortcode_string .= $parse_vars;

	$shortcode_content = (isset($postdata['shortcode_content'])) ? $postdata['shortcode_content'] : '';

	if($_POST['stype'] == 'closed'){
		$shortcode_string .= ']'.$shortcode_content.'[/'.$_POST['shortcode'].']';
	}else{
		$shortcode_string .= ']';
	}
	echo $shortcode_string;
	die();

}

add_action( 'wp_ajax_cjpopups_fetch_shortcodes', 'cjpopups_fetch_shortcodes' );
function cjpopups_fetch_shortcodes() {
	global $wpdb, $shortcode_tags;

	if(isset($_POST['action']) && $_POST['action'] == 'cjpopups_fetch_shortcodes'){
		$select_shortcodes = null;
		$cjslugs = array_keys(get_option('cjslugs'));
		if(!in_array($_POST['slug'], $cjslugs)){
			echo 'none';
		}else{
			if(!empty($shortcode_tags)){
				$shortcodes_options = '<option value="0">'.__('Select Shortcode', 'cjpopups').'</option>';
				$count = 0;
				foreach ($shortcode_tags as $key => $value) {
					$tag = explode('_', $key);
					if(@in_array($tag[0], $cjslugs) && function_exists($tag[0].'_item_info') && $tag[0] == $_POST['slug']){
						$shortcode_name = cjpopups_prepare_shortcode_name($value);
				  		$select_shortcodes[$value] = unserialize(cjpopups_do_shortcode( '['.$value.' return="options"]' ));
				  		$count++;
				  		$last_class = ($count % 2) ? '' : 'last';
				  		$shortcode_vars = '';
				  		$shortcode_vars = $select_shortcodes[$value];
				  		$shortcodes_options .= '<option value="'.$key.'">'.$shortcode_name.'</option>';
					}
				}
				echo $shortcodes_options;
			}
		}
	}
	die();
}

function cjpopups_prepare_shortcode_name($value){
	$return = explode('_', $value);
	unset($return[0]);
	$return = ucwords(implode(' ', $return));
	return $value;
}

add_action( 'wp_ajax_cjpopups_fetch_shortcode_options', 'cjpopups_fetch_shortcode_options' );
function cjpopups_fetch_shortcode_options() {
	global $wpdb, $shortcode_tags;
	$select_shortcodes = null;

	$shortcode_options = cjpopups_do_shortcode( '['.$_POST['shortcode'].' return="options"]' );
	$shortcode_options = (is_serialized($shortcode_options)) ? unserialize($shortcode_options) : $shortcode_options;

	$shortcode_defaults = cjpopups_do_shortcode( '['.$_POST['shortcode'].' return="defaults"]' );
	$shortcode_defaults = (is_serialized($shortcode_defaults)) ? unserialize($shortcode_defaults) : $shortcode_defaults;


	$form_fields[] = array(
	    'type' => 'heading',
	    'id' => 'shortcode-settings-heading',
	    'label' => '',
	    'info' => '',
	    'suffix' => '',
	    'prefix' => '',
	    'default' => __('Shortcode Settings', 'cjpopups'),
	    'options' => '', // array in case of dropdown, checkbox and radio buttons
	);

	if(!is_null($shortcode_options['description'])){
	  $form_fields[] = array(
	      'type' => 'info-full',
	      'id' => 'shortcode_info',
	      'label' => '',
	      'info' => '',
	      'suffix' => '',
	      'prefix' => '',
	      'default' => $shortcode_options['description'],
	      'options' => '', // array in case of dropdown, checkbox and radio buttons
	  );
	}

	if($shortcode_options['stype'] == 'closed' && isset($shortcode_options['default_content']) && $shortcode_options['default_content'] != ''){
	  $form_fields[] = array(
	      'type' => 'textarea',
	      'id' => 'shortcode_content',
	      'label' => __('Content', 'cjpopups'),
	      'info' => '',
	      'suffix' => '',
	      'prefix' => '',
	      'default' => $shortcode_options['default_content'],
	      'options' => '', // array in case of dropdown, checkbox and radio buttons
	  );
	}

	foreach ($shortcode_options as $key => $options) {
		if(is_array($options)){

			$form_fields[] = array(
			    'type' => $options[1],
			    'id' => $key,
			    'label' => $options[0],
			    'info' => $options[3],
			    'suffix' => '',
			    'prefix' => '',
			    'default' => (isset($shortcode_defaults[$key])) ? $shortcode_defaults[$key] : '',
			    'options' => (is_array($options[2])) ? $options[2] : '', // array in case of dropdown, checkbox and radio buttons
			);
		}
	}

	$form_fields[] = array(
	    'type' => 'submit',
	    'id' => 'cj-insert-shortcode',
	    'label' => __('Insert Shortcode', 'cjpopups'),
	    'info' => '',
	    'suffix' => '',
	    'prefix' => '',
	    'default' => '',
	    'options' => '', // array in case of dropdown, checkbox and radio buttons
	);



	echo '<div class="cj-table">';
	echo '<form action="" method="post" id="cj-shortcode-settings-form" data-shortcode-stype="'.$shortcode_options['stype'].'" data-shortcode-name="'.$_POST['shortcode'].'">';
	echo cjpopups_admin_form_raw($form_fields, 'no-search-box', true);
	echo '</form>';
	echo '</div>';

	die();
}




















