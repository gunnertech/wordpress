<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
global $wpdb;
$options_table = cjpopups_item_info('options_table');

$cj_message = null;

$unique_key = sha1(serialize($options));

$form_submit = 'form_submit_'.$unique_key;
if ( empty($_POST) || !wp_verify_nonce('form_submit') ){
	if(isset($_POST[$form_submit])){
		foreach ($_POST as $key => $value) {
			if($key != '_wpnonce' && $key != '_wp_http_referer'){
				if (is_array($value)) {
					$update_value = serialize($value);
				} else {
					$update_value = $value;
				}
				//$wpdb->query("UPDATE {$options_table} SET option_value = '{$update_value}' WHERE option_name = '{$key}'");
				$wpdb->update( $options_table, array("option_value" => $update_value), array("option_name" => $key), array("%s"), array("%s") );
			}
		}
		$location = cjpopups_string(cjpopups_current_url()).'cjpopups_options=saved';
		wp_safe_redirect($location);
	}
}else{
	print __('Sorry, your nonce did not verify.', 'cjpopups');
   	exit;
}

if(isset($_GET['cjpopups_options']) && $_GET['cjpopups_options'] == 'saved'){
	$cj_message = cjpopups_message('success', __('Settings saved successfully.', 'cjpopups'));
	$display[] = $cj_message;
}

// Reset section settings
if(isset($_GET['cjpopups_reset']) && $_GET['cjpopups_reset'] == $_GET['callback']){
	foreach ($options as $key => $value) {
		$wpdb->query("DELETE FROM $options_table WHERE option_name = '{$value['id']}'");
	}
	$location = cjpopups_callback_url($_GET['callback']);
	wp_safe_redirect( $location );
	exit;
}

// Reset section all
if(isset($_GET['cjpopups_reset']) && $_GET['cjpopups_reset'] == 'all'){
	foreach ($options as $key => $value) {
		$wpdb->query("TRUNCATE TABLE $options_table");
	}
	$location = cjpopups_callback_url($_GET['callback']);
	wp_safe_redirect( $location );
	exit;
}


$display[] = wp_nonce_field('form_submit');

$display[] = '<table class="enable-search" cellspacing="0" cellpadding="0" width="100%">';
foreach ($options as $key => $option) {

	$params = '';

	if(isset($option['params'])){
		$params_object = $option['params'];
		if(is_array($params_object)){
			foreach ($params_object as $pk => $pv) {
				$params .= ' '.$pk.'="'.$pv.'" ';
			}
		}
	}

	if($option['type'] == 'heading'){

		$settings_search_box = '
			<span class="settings-search-box">

				<a href="'.cjpopups_string(cjpopups_callback_url($_GET['callback'])).'cjpopups_reset='.$_GET['callback'].'" class="button-secondary cj-confirm margin-5-right margin-6-top inline-block" data-confirm="'.__("Are you sure?\nThis canont be undone.", 'cjpopups').'">'.__('Reset Section', 'cjpopups').'</a>
				<a href="'.cjpopups_string(cjpopups_callback_url($_GET['callback'])).'cjpopups_reset=all" class="button-secondary cj-confirm margin-5-right margin-6-top inline-block" data-confirm="'.__("Are you sure?\nThis canont be undone.", 'cjpopups').'">'.__('Reset All', 'cjpopups').'</a>

				<input id="settings-search-box" name="settings-search-box" type="text" placeholder="search" />
				<i class="cj-icon fa fa-search"></i>
			</span>
		';

		$display[] = '<tr>';
		$display[] = '<th colspan="2">';
		$display[] = '<h2 class="main-heading">'.__($option['default'], 'cjpopups').$settings_search_box.'</h2>';
		$display[] = '</th>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'sub-heading'){
		$display[] = '<tr>';
		$display[] = '<th colspan="2">';
		$display[] = '<h2 id="'.$option['id'].'" class="sub-heading">'.__($option['default'], 'cjpopups').'</h2>';
		$display[] = '</th>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'section-heading'){
		$display[] = '<tr class="searchable">';
		$display[] = '<th colspan="2">';
		$display[] = '<h2 class="section-heading">'.__($option['default'], 'cjpopups').'</h2>';
		$display[] = '</th>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'hidden'){
		$display[] = '<tr class="cj-hidden hidden"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="hidden" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.$option['default'].'" />';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'info-full'){
		$display[] = '<tr class="searchable info-full">';
		$display[] = '<td colspan="2" class="cj-panel">';
		$display[] = __($option['default'], 'cjpopups');
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'info-highlight'){
		$display[] = '<tr class="searchable info-highlight">';
		$display[] = '<td colspan="2" class="cj-panel">';
		$display[] = __($option['default'], 'cjpopups');
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'info'){
		$display[] = '<tr class="searchable info"><td class="cj-label">'.__($option['label'], 'cjpopups').'</td>';
		$display[] = '<td class="cj-panel">';
		$display[] = __($option['default'], 'cjpopups');
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'text' || $option['type'] == 'textbox'){
		if($option['suffix'] != ''){
			$suffix = $option['suffix'];
		}else{
			$suffix = '';
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="text" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.esc_textarea(cjpopups_get_option($option['id'])).'" /> <span class="cj-suffix">'.$suffix.'</span>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'multitextboxes'){
		$option_value = cjpopups_get_option($option['id']);
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		foreach ($option['options'] as $key => $value) {
			$display[] = '<div><label><span class="bold block margin-3-bottom">'.$value.'</span><input type="text" name="'.$option['id'].'['.$key.']" id="'.sanitize_title( $option['id'] ).'_width" value="'.$option_value[$key].'" /></label></div>';
		}
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'padding'){
		$option_value = cjpopups_get_option($option['id']);
		if(is_array($option_value)){
			$top = $option_value[0];
			$bottom = $option_value[1];
			$left = $option_value[2];
			$right = $option_value[3];
		}else{
			$top = 0;
			$bottom = 0;
			$left = 0;
			$right = 0;
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel padding-10-top">';
		$display[] = '<div class="clearfix">';
		$display[] = '<div class="alignleft margin-10-right padding-5-top"><label class="block textcenter padding-3-left">'.__('Top', 'cjpopups').'</label><input style="width:100px; text-align:center;" placeholder="'.__('Top', 'cjpopups').'" type="text" name="'.$option['id'].'[]" value="'.$top.'" /></div>';
		$display[] = '<div class="alignleft margin-10-right padding-5-top"><label class="block textcenter padding-3-left">'.__('Bottom', 'cjpopups').'</label><input style="width:100px; text-align:center;" placeholder="'.__('Bottom', 'cjpopups').'" type="text" name="'.$option['id'].'[]" value="'.$bottom.'" /></div>';
		$display[] = '<div class="alignleft margin-10-right padding-5-top"><label class="block textcenter padding-3-left">'.__('Left', 'cjpopups').'</label><input style="width:100px; text-align:center;" placeholder="'.__('Left', 'cjpopups').'" type="text" name="'.$option['id'].'[]" value="'.$left.'" /></div>';
		$display[] = '<div class="alignleft margin-10-right padding-5-top"><label class="block textcenter padding-3-left">'.__('Right', 'cjpopups').'</label><input style="width:100px; text-align:center;" placeholder="'.__('Right', 'cjpopups').'" type="text" name="'.$option['id'].'[]" value="'.$right.'" /></div>';
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'pass' || $option['type'] == 'password'){
		if($option['suffix'] != ''){
			$suffix = $option['suffix'];
		}else{
			$suffix = '';
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="password" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.cjpopups_get_option($option['id']).'" /> <span class="cj-suffix">'.$suffix.'</span>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'number'){
		if($option['suffix'] != ''){
			$suffix = $option['suffix'];
		}else{
			$suffix = '';
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="number" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.cjpopups_get_option($option['id']).'" /> <span class="cj-suffix">'.$suffix.'</span>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'textarea'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<textarea '.$params.' rows="5" cols="40" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">'.cjpopups_get_option($option['id']).'</textarea>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'textarea-readonly'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<textarea '.$params.' rows="5" cols="40" name="'.$option['id'].'" readonly id="'.sanitize_title( $option['id'] ).'">'.cjpopups_get_option($option['id']).'</textarea>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'code-css'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<textarea '.$params.' class="cj-code-css" rows="5" cols="40" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">'.cjpopups_get_option($option['id']).'</textarea>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'code-js'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<textarea '.$params.' class="cj-code-js" rows="5" cols="40" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">'.cjpopups_get_option($option['id']).'</textarea>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'dropdown' || $option['type'] == 'select'){
		$opts = null;
		foreach ($option['options'] as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = $option['prefix'];
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select> ';
		$display[] = $option['suffix'];
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'multidropdown' || $option['type'] == 'multiselect'){
		$opts = null;
		$saved_opts = cjpopups_get_option($option['id']);
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($option['options'] as $okey => $ovalue) {
			if(@in_array($okey, cjpopups_get_option($option['id']))){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'radio-inline'){
		$opts = null;
		foreach ($option['options'] as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $okey){
				$opts[] = '<label for="'.$option['id'].'_'.$okey.'"><input checked type="radio" name="'.$option['id'].'" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}else{
				$opts[] = '<label for="'.$option['id'].'_'.$okey.'"><input type="radio" name="'.$option['id'].'" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel cj-checkbox">';
		$display[] = implode('', $opts);
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'radio'){
		$opts = null;
		foreach ($option['options'] as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $okey){
				$opts[] = '<label class="cj-block" for="'.$option['id'].'_'.$okey.'"><input checked type="radio" name="'.$option['id'].'" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}else{
				$opts[] = '<label class="cj-block" for="'.$option['id'].'_'.$okey.'"><input type="radio" name="'.$option['id'].'" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel cj-checkbox">';
		$display[] = implode('', $opts);
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'checkbox-inline'){
		$opts = null;
		foreach ($option['options'] as $okey => $ovalue) {
			if(@in_array($okey, cjpopups_get_option($option['id']))){
				$opts[] = '<label for="'.$option['id'].'_'.$okey.'"><input checked type="checkbox" name="'.$option['id'].'[]" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}else{
				$opts[] = '<label for="'.$option['id'].'_'.$okey.'"><input type="checkbox" name="'.$option['id'].'[]" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel cj-checkbox">';
		$display[] = implode('', $opts);
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'checkbox'){
		$opts = null;
		foreach ($option['options'] as $okey => $ovalue) {
			if(@in_array($okey, cjpopups_get_option($option['id']))){
				$opts[] = '<label class="cj-block" for="'.$option['id'].'_'.$okey.'"><input checked type="checkbox" name="'.$option['id'].'[]" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}else{
				$opts[] = '<label class="cj-block" for="'.$option['id'].'_'.$okey.'"><input type="checkbox" name="'.$option['id'].'[]" id="'.$option['id'].'_'.$okey.'" value="'.$okey.'" /> <span class="checkbox-span-fix">'.__($ovalue, 'cjpopups').'</span> </label>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel cj-checkbox">';
		$display[] = implode('', $opts);
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'page'){
		$opts = null;
		$paggs = get_pages(array('sort_column' => 'post_title', 'sort_order' => 'ASC','post_type' => 'page','post_status' => 'publish', 'posts_per_page' => '10000'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->ID){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'pages'){
		$opts = null;
		$paggs = get_pages(array('sort_column' => 'post_title', 'sort_order' => 'ASC','post_type' => 'page','post_status' => 'publish', 'posts_per_page' => '10000'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(@in_array($ovalue->ID, cjpopups_get_option($option['id']))){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'post'){
		$opts = null;
		$paggs = get_posts(array('sort_column' => 'post_title', 'sort_order' => 'ASC', 'post_type' => 'post','post_status' => 'publish', 'posts_per_page' => '10000'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->ID){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'post_type'){
		$opts = null;
		$paggs = get_post_types();
		unset($paggs['revision']);
		unset($paggs['nav_menu_item']);
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if($okey == cjpopups_get_option($option['id'])){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<tr id="'.$option['id'].'" class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'post_types'){
		$opts = null;
		$paggs = get_post_types();
		unset($paggs['revision']);
		unset($paggs['nav_menu_item']);
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		if(!empty($paggs)){
			foreach ($paggs as $okey => $ovalue) {
				if(@in_array($okey, cjpopups_get_option($option['id']))){
					$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
				}else{
					$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
				}
			}
		}else{
			$opts[] = '<option value="">'.__('No custom post types found', 'cjpopups').'</option>';
		}
		$display[] = '<tr id="'.$option['id'].'" class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'posts'){
		$opts = null;
		$paggs = get_posts(array('sort_column' => 'post_title', 'sort_order' => 'ASC','post_type' => 'post','post_status' => 'publish', 'posts_per_page' => '10000'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(@in_array($ovalue->ID, cjpopups_get_option($option['id']))){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->post_title.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'category'){
		$opts = null;
		$paggs = get_categories( array('type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category', 'number' => 10000) );
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->term_id){
				$opts[] = '<option selected value="'.$ovalue->term_id.'">'.$ovalue->cat_name.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->term_id.'">'.$ovalue->cat_name.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'categories'){
		$opts = null;
		$paggs = get_categories( array('type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category', 'number' => 10000) );
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(@in_array($ovalue->term_id, cjpopups_get_option($option['id']))){
				$opts[] = '<option selected value="'.$ovalue->term_id.'">'.$ovalue->cat_name.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->term_id.'">'.$ovalue->cat_name.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}


	if($option['type'] == 'taxonomy'){
		$opts = null;
		$taxonomies = get_taxonomies();
		$exclude_taxonomies = array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format');
		$terms = '';
		foreach ($taxonomies as $key => $taxonomy) {
			if(!in_array($key, $exclude_taxonomies)){
				$taxonomy_array[] = $key;
			}
		}
		$paggs = get_terms($taxonomy_array, array('orderby' => 'name'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->slug.'~~~~'.$ovalue->taxonomy){
				$opts[] = '<option selected value="'.$ovalue->slug.'~~~~'.$ovalue->taxonomy.'">'.$ovalue->name.' ('.$ovalue->taxonomy.')</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->slug.'~~~~'.$ovalue->taxonomy.'">'.$ovalue->name.' ('.$ovalue->taxonomy.')</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}



	if($option['type'] == 'taxonomies'){
		$opts = null;
		$taxonomies = get_taxonomies();
		$exclude_taxonomies = array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format');
		$terms = '';
		$taxonomy_array = null;
		foreach ($taxonomies as $key => $taxonomy) {
			if(!in_array($key, $exclude_taxonomies)){
				if(!isset($option['specific'])){
					$taxonomy_array[] = $key;
				}else{
					if($key == $option['specific']){
						$taxonomy_array[] = $key;
					}
				}
			}
		}
		$paggs = get_terms($taxonomy_array, array('orderby' => 'name'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(is_array(cjpopups_get_option($option['id']))){
				if(@in_array($ovalue->slug.'~~~~'.$ovalue->taxonomy, cjpopups_get_option($option['id']))){
					$opts[] = '<option selected value="'.$ovalue->slug.'~~~~'.$ovalue->taxonomy.'">'.$ovalue->name.' ('.$ovalue->taxonomy.')</option>';
				}else{
					$opts[] = '<option value="'.$ovalue->slug.'~~~~'.$ovalue->taxonomy.'">'.$ovalue->name.' ('.$ovalue->taxonomy.')</option>';
				}
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}


	if($option['type'] == 'tag'){
		$opts = null;
		$paggs = get_tags();
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->slug){
				$opts[] = '<option selected value="'.$ovalue->slug.'">'.$ovalue->name.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->slug.'">'.$ovalue->name.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'tags'){
		$opts = null;
		$paggs = get_tags();
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(@in_array($ovalue->slug, cjpopups_get_option($option['id']))){
				$opts[] = '<option selected value="'.$ovalue->slug.'">'.$ovalue->name.'</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->slug.'">'.$ovalue->name.'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'role'){
		$opts = null;
		global $wp_roles;
		$paggs = $wp_roles->role_names;
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'roles'){
		$opts = null;
		$saved_opts = cjpopups_get_option($option['id']);
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		global $wp_roles;
		$paggs = $wp_roles->role_names;
		foreach ($paggs as $okey => $ovalue) {
			if(is_array($saved_opts) && in_array($okey, $saved_opts)){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'user'){
		$opts = null;
		global $wp_roles;
		$paggs = get_users(array('orderby' => 'display_name'));
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		foreach ($paggs as $okey => $ovalue) {
			if(cjpopups_get_option($option['id']) == $ovalue->ID){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->display_name.' ('.$ovalue->user_email.')</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->display_name.' ('.$ovalue->user_email.')</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'users'){
		$opts = null;
		$saved_opts = cjpopups_get_option($option['id']);
		$opts[] = '<option value="0">'.__('None', 'cjpopups').'</option>';
		$paggs = get_users(array('orderby' => 'display_name'));
		foreach ($paggs as $okey => $ovalue) {
			if(is_array($saved_opts) && @in_array($ovalue->ID, $saved_opts)){
				$opts[] = '<option selected value="'.$ovalue->ID.'">'.$ovalue->display_name.' ('.$ovalue->user_email.')</option>';
			}else{
				$opts[] = '<option value="'.$ovalue->ID.'">'.$ovalue->display_name.' ('.$ovalue->user_email.')</option>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<select '.$params.' class="chzn-select-no-results" multiple name="'.$option['id'].'[]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'date'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="text" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.cjpopups_get_option($option['id']).'" class="date" />';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'color'){
		$saved_hex = cjpopups_get_option($option['id']);
		$saved_color_hex = ($saved_hex == '') ? 'transparent' : $saved_hex;
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="text" name="'.$option['id'].'" id="'.sanitize_title( $option['id'] ).'" value="'.cjpopups_get_option($option['id']).'" data-color="'.cjpopups_get_option($option['id']).'" class="color-picker" /> <span class="color-hex"><code>'.$saved_color_hex.'</code></span>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'slider'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '
						<input name="'.$option['id'].'" id="cj-slider-'.$option['id'].'" class="cj-slider-input" type="text" value="'.cjpopups_get_option( $option['id'] ).'" />
						<div class="cj-slider" id="'.sanitize_title( $option['id'] ).'"></div>

						<script>
						$(function() {
						  $( "#'.$option['id'].'" ).slider({
						    range: "max",
						    min: '.$option['min'].',
						    max: '.$option['max'].',
						    value: '.cjpopups_get_option( $option['id'] ).',
						    slide: function( event, ui ) {
						      $( "#cj-slider-'.$option['id'].'" ).val( ui.value );
						    }
						  });
						  //$( "#cj-slider-'.$option['id'].'" ).val( $( "#cj-slider" ).slider( "value" ) );
						});
						</script>
					 ';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'file'){
		$opts = '';
		$saved_files = cjpopups_get_option($option['id']);
		if(is_array($saved_files)){
			foreach ($saved_files as $key => $saved_file) {
				if($saved_file != ''){
					$opts .= '<div class="file-list">';
					$opts .= '<div class="file-info">';
					$opts .= '<a href="'.$saved_file.'" target="_blank">'.basename($saved_file).'</a>';
					$opts .= '<input type="hidden" name="'.$option['id'].'[]" value="'.$saved_file.'" />';
					$opts .= '<a href="#" class="cj-remove-file"><i class="fa fa-times"></i></a>';
					$opts .= '</div>';
					$opts .= '<div class="padding-10">';
					$opts .= '<img src="'.$saved_file.'">';
					$opts .= '</div>';
					$opts .= '</div>';
				}
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-file-upload">';
		$display[] = '<a data-form-id="'.$form_submit.'" href="#" title="" class="cj-upload-file button-secondary">'.__('Upload File', 'cjpopups').'</a>';
		$display[] = '<input '.$params.' type="hidden" name="'.$option['id'].'" value="" />';
		$display[] = '<div data-id="'.$option['id'].'" class="uploaded-file sortables clearfix">'.$opts.'</div>';
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'files'){

		$opts = '';
		$saved_files = cjpopups_get_option($option['id']);
		if(is_array($saved_files)){
			foreach ($saved_files as $key => $saved_file) {
				if($saved_file != ''){
					$opts .= '<div class="file-list clearfix">';
					$opts .= '<div class="file-info">';
					$opts .= '<a href="'.$saved_file.'" target="_blank">'.basename($saved_file).'</a>';
					$opts .= '<input type="hidden" name="'.$option['id'].'[]" value="'.$saved_file.'" />';
					$opts .= '<a href="#" class="cj-remove-file"><i class="fa fa-times"></i></a>';
					$opts .= '</div>';
					$opts .= '<div class="padding-10">';
					$opts .= '<img src="'.$saved_file.'">';
					$opts .= '</div>';
					$opts .= '</div>';
				}
			}
		}

		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-file-upload">';
		$display[] = '<a data-form-id="'.$form_submit.'" href="#" title="" class="cj-upload-files button-secondary">'.__('Upload Files', 'cjpopups').'</a>';
		$display[] = '<input '.$params.' type="hidden" name="'.$option['id'].'" value="" />';
		$display[] = '<div data-id="'.$option['id'].'" class="uploaded-files sortables clearfix">'.$opts.'</div>';
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'wysiwyg'){

		$editor_id = str_replace('-', '', str_replace('_', '', strtolower($option['id'])));
		$editor_settings = array(
			'wpautop' => false,
			'media_buttons' => true,
			'textarea_name' => $option['id'],
			'textarea_rows' => 12,
			'teeny' => false,
		);
		ob_start();
		wp_editor(cjpopups_get_option( $option['id'] ), $editor_id, $editor_settings);
		$editor_panel = ob_get_clean();

		$display[] = '<tr class="searchable" class="cj-wysiwyg"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-panel-editor">'.$editor_panel.'</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'screenshots'){
		$opts = '';
		foreach ($option['options'] as $key => $value) {
			if($key == cjpopups_get_option($option['id'])){
				$opts .= '<div class="cj-screenshot checked"><label><input checked type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label></div>';
			}else{
				$opts .= '<div class="cj-screenshot"><label><input type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label></div>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-screenshots clearfix">';
		$display[] = $opts;
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'screenshots-small'){
		$opts = '';
		foreach ($option['options'] as $key => $value) {
			if($key == cjpopups_get_option($option['id'])){
				$opts .= '<div class="cj-screenshot small checked"><label><input checked type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label><span class="checked-img"></span><br /><p class="screenshot-name">'.str_replace('_', ' ', $key).'</p><span class="check-icon"><i class="icon-ok white"></i></span></div>';
			}else{
				$opts .= '<div class="cj-screenshot small"><label><input type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label><span class="checked-img"></span><br /><p class="screenshot-name">'.str_replace('_', ' ', $key).'</p><span class="check-icon"><i class="icon-white cj-icon-checkmark-5"></i></span></div>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-screenshots clearfix">';
		$display[] = $opts;
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'screenshots-block'){
		$opts = '';
		foreach ($option['options'] as $key => $value) {
			if($key == cjpopups_get_option($option['id'])){
				$opts .= '<div class="cj-screenshot-block clearfix checked"><label><input checked type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label><span class="checked-img"></span><br /><p class="screenshot-name">'.str_replace('_', ' ', $key).'</p><span class="check-icon"><i class="icon-ok white"></i></span></div>';
			}else{
				$opts .= '<div class="cj-screenshot-block clearfix"><label><input type="radio" name="'.$option['id'].'" class="cj-hidden" value="'.$key.'" /> <img src="'.$value.'" alt="" /></label><span class="checked-img"></span><br /><p class="screenshot-name">'.str_replace('_', ' ', $key).'</p><span class="check-icon"><i class="icon-white cj-icon-checkmark-5"></i></span></div>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-screenshots clearfix">';
		$display[] = $opts;
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'textures'){
		$opts = '';
		foreach ($option['options'] as $key => $value) {
			if($value == cjpopups_get_option( $option['id'] )){
				$opts .= '<label class="texture checked" for="label-'.$key.'" class="texture" style="background:#ccc url('.$value.') repeat;"><input class="hidden"  checked id="label-'.$key.'" name="'.$option['id'].'" value="'.$value.'" type="radio" /></label>';
			}else{
				$opts .= '<label class="texture" for="label-'.$key.'" class="texture" style="background:#ccc url('.$value.') repeat;"><input class="hidden" id="label-'.$key.'" name="'.$option['id'].'" value="'.$value.'" type="radio" /></label>';
			}
		}
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<div class="cj-textures">';
		$display[] = $opts;
		$display[] = '</div>';
		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}


	if($option['type'] == 'font'){

		$google_fonts_array = cjpopups_get_option( 'google_fonts' );

		$google_fonts['inherit'] = 'Inherit';
		$google_fonts['Arial'] = 'Arial';
		$google_fonts['Arial Black'] = 'Arial Black';
		$google_fonts['Helvetica'] = 'Helvetica';
		$google_fonts['Georgia'] = 'Georgia';
		$google_fonts['Trebuchet MS'] = 'Trebuchet MS';
		$google_fonts['Verdana'] = 'Verdana';
		$google_fonts['Palatino Linotype'] = 'Palatino Linotype';
		$google_fonts['Book Antiqua'] = 'Book Antiqua';
		$google_fonts['Palatino'] = 'Palatino';
		$google_fonts['Times New Roman'] = 'Times New Roman';
		$google_fonts['Times'] = 'Times';
		$google_fonts['Gadget'] = 'Gadget';
		$google_fonts['Comic Sans MS'] = 'Comic Sans MS';
		$google_fonts['Impact'] = 'Impact';
		$google_fonts['Charcoal'] = 'Charcoal';
		$google_fonts['Lucida Sans Unicode'] = 'Lucida Sans Unicode';
		$google_fonts['Lucida Grande'] = 'Lucida Grande';
		$google_fonts['Geneva'] = 'Geneva';
		$google_fonts['Courier New'] = 'Courier New';
		$google_fonts['Courier'] = 'Courier';
		$google_fonts['Lucida Console'] = 'Lucida Console';
		$google_fonts['Monaco'] = 'Monaco';
		$google_fonts['monospace'] = 'monospace';

		if(is_array($google_fonts_array)){
			foreach ($google_fonts_array as $key => $value) {
				$google_fonts[$key] = $value;
			}
		}

		$saved_font = cjpopups_get_option($option['id']);

		$saved_font['family'] = (isset($saved_font['family'])) ? $saved_font['family'] : 'Open+Sans';
		$saved_font['size'] = (isset($saved_font['size'])) ? $saved_font['size'] : '12px';
		$saved_font['weight'] = (isset($saved_font['weight'])) ? $saved_font['weight'] : 'normal';
		$saved_font['style'] = (isset($saved_font['style'])) ? $saved_font['style'] : 'inherit';
		$saved_font['line-height'] = (isset($saved_font['line-height'])) ? $saved_font['line-height'] : '20';

		$font_weight_array = array(
			'inherit' => __('Inherit', 'cjpopups'),
			'bold' => __('Bold', 'cjpopups'),
			'bolder' => __('Bolder', 'cjpopups'),
			'normal' => __('Normal', 'cjpopups'),
			'lighter' => __('Lighter', 'cjpopups'),
			'100' => __('100', 'cjpopups'),
			'200' => __('200', 'cjpopups'),
			'300' => __('300', 'cjpopups'),
			'400' => __('400', 'cjpopups'),
			'500' => __('500', 'cjpopups'),
			'600' => __('600', 'cjpopups'),
			'700' => __('700', 'cjpopups'),
			'800' => __('800', 'cjpopups'),
			'900' => __('900', 'cjpopups'),
		);

		$font_style_array = array(
			'inherit' => __('Inherit', 'cjpopups'),
			'italic' => __('Italic', 'cjpopups'),
			'oblique' => __('Oblique', 'cjpopups'),
			'normal' => __('Normal', 'cjpopups'),

		);

		$font_family = '';
		foreach ($google_fonts as $key => $value) {
			if($saved_font['family'] == $key){
				$font_family .= '<option selected value="'.$key.'">'.$value.'</option>';
			}else{
				$font_family .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}

		$font_weight = '';
		foreach ($font_weight_array as $key => $value) {
			if(isset($saved_font['weight']) && $saved_font['weight'] == $key){
				$font_weight .= '<option selected value="'.$key.'">'.$key.'</option>';
			}else{
				$font_weight .= '<option value="'.$key.'">'.$key.'</option>';
			}
		}

		$font_size = '';
		$font_size .= '<option selected value="inherit">'.__('Inherit', 'cjpopups').'</option>';
		for ($i=8; $i < 37; $i++) {
			if(isset($saved_font['size']) && $saved_font['size'] == $i.'px'){
				$font_size .= '<option selected value="'.$i.'px">'.$i.'px</option>';
			}else{
				$font_size .= '<option value="'.$i.'px">'.$i.'px</option>';
			}
		}

		$font_style = '';
		foreach ($font_style_array as $key => $value) {
			if(isset($saved_font['style']) && $saved_font['style'] == $key){
				$font_style .= '<option selected value="'.$key.'">'.$key.'</option>';
			}else{
				$font_style .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}

		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';

		$display[] = '<div class="clearfix">';

		$display[] = '<div class="margin-8-bottom one_third"><label style="display:block;" for="'.$option['id'].'-family">'.__('Family:', 'cjpopups').'</label>';
		$display[] = '<select '.$params.' name="'.$option['id'].'[family]" id="'.$option['id'].'-family" class="chzn-select-no-results" style="margin-right:10px;">';
		$display[] = $font_family;
		$display[] = '</select></div>';

		$display[] = '<div class="margin-8-bottom one_third last"><label style="display:block;" for="'.$option['id'].'-size">'.__('Size:', 'cjpopups').'</label>';
		$display[] = '<select '.$params.' name="'.$option['id'].'[size]" id="'.$option['id'].'-size" class="chzn-select-no-results" style="margin-right:10px;">';
		$display[] = $font_size;
		$display[] = '</select></div>';

		$display[] = '<br class="clear" />';

		$display[] = '<div class="margin-8-bottom one_third"><label style="display:block;" for="'.$option['id'].'-weight">'.__('Weight:', 'cjpopups').'</label>';
		$display[] = '<select '.$params.' name="'.$option['id'].'[weight]" id="'.$option['id'].'-weight" class="chzn-select-no-results" style="margin-right:10px;">';
		$display[] = $font_weight;
		$display[] = '</select></div>';

		$display[] = '<div class="margin-8-bottom one_third"><label style="display:block;" for="'.$option['id'].'-style">'.__('Style:', 'cjpopups').'</label>';
		$display[] = '<select '.$params.' name="'.$option['id'].'[style]" id="'.$option['id'].'-style" class="chzn-select-no-results" style="margin-right:10px;">';
		$display[] = $font_style;
		$display[] = '</select></div>';

		$display[] = '<div class="margin-8-bottom one_third"><label style="display:block;" for="'.$option['id'].'-style">'.__('Line Height:', 'cjpopups').'</label>';
		$display[] = '<input name="'.$option['id'].'[line-height]" id="'.$option['id'].'-line-height" style="width: 50px; border-radius:5px; border:1px solid #ddd; text-align:center; margin-right:5px;" value="'.$saved_font['line-height'].'">px';
		$display[] = '</div>';

		$display[] = '</div>';

		$display[] = '<div class="cj-info margin-5-top">'.__($option['info'], 'cjpopups').'</div>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}


	if($option['type'] == 'sortable'){
		$display[] = '<tr class="searchable"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<ul id="'.$option['id'].'">';
		$opts = null;
		if(cjpopups_get_option($option['id']) != ''){
			$opts = cjpopups_sortArrayByArray($option['options'], cjpopups_get_option($option['id']));
		}else{
			$opts = $option['options'];
		}
		foreach ($opts as $key => $value) {
			$display[] = '<li class="ui-state-default padding-10" style="background:#f7f7f7;">'.$value.' <input type="hidden" name="'.$option['id'].'[]" value="'.$key.'" /> </li>';
		}
		$display[] = '</ul>
		<script>
		  jQuery(function() {
		    jQuery( "#'.$option['id'].'" ).sortable();
		    jQuery( "#'.$option['id'].'" ).disableSelection();
		  });
		  </script>';
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'background'){
		$display[] = '<tr class="searchable" class="cj-background"><td class="cj-label"><label for="'.sanitize_title( $option['id'] ).'">'.__($option['label'], 'cjpopups').'</label></td>';
		$display[] = '<td class="cj-panel">';

		$opt_value['color'] = 'Transparent';
		$opt_value['image'] = null;
		$opt_value['bg_repeat'] = null;
		$opt_value['bg_size'] = null;
		$opt_value['bg_attachment'] = null;
		$opt_value['bg_position'] = null;

		$opt_value = cjpopups_get_option($option['id']);

		// Color
		$saved_hex = (isset($opt_value['color']) && $opt_value['color'] != '') ? $opt_value['color'] : '';
		$saved_color_hex = ($saved_hex == '') ? __('Transparent', 'cjpopups') : $saved_hex;
		$display[] = '<p><input type="text" name="'.$option['id'].'[color]" id="'.sanitize_title( $option['id'].'_color' ).'" value="'.$saved_color_hex.'" data-color="'.$saved_color_hex.'" class="color-picker" /> <span class="color-hex"><code>'.$saved_color_hex.'</code></span></p>';


		// Image
		$opts = null;
		$saved_file = (isset($opt_value['image'])) ? $opt_value['image'] : '';
		if($saved_file != ''){
			$opts .= '<div class="file-list">';
			$opts .= '<div class="file-info">';
			$opts .= '<a href="'.$saved_file.'" target="_blank">'.basename($saved_file).'</a>';
			$opts .= '<input type="hidden" name="'.$option['id'].'[image]" value="'.$saved_file.'" />';
			$opts .= '<a href="#" class="cj-remove-background-file"><i class="fa fa-times"></i></a>';
			$opts .= '</div>';
			$opts .= '<div class="padding-10">';
			$opts .= '<img src="'.$saved_file.'">';
			$opts .= '</div>';
			$opts .= '</div>';
		}
		$display[] = '<div class="cj-file-upload margin-20-top">';
		$display[] = '<p><strong>'.__('Upload background image', 'cjpopups').'</strong></p>';
		$display[] = '<a data-form-id="'.$option['id'].'" href="#" title="" class="cj-background-image button-secondary">'.__('Upload File', 'cjpopups').'</a>';
		$display[] = '<input '.$params.' type="hidden" name="'.$option['id'].'[image]" value="" />';
		$display[] = '<div data-id="'.$option['id'].'" class="uploaded-file sortables clearfix">'.$opts.'</div>';
		$display[] = '<div class="cj-bg-info padding-5-top padding-5-bottom"></div>';
		$display[] = '</div>';

		$display[] = '<div class="clearfix">';

		// Background Repeat
		$display[] = '<div class="alignleft padding-5-top padding-5-bottom">';
		$bg_repeat_array = array(
			'inherit' => _x('Inherit', 'cjpopups'),
			'repeat' => _x('Repeat All', 'cjpopups'),
			'repeat-x' => _x('Repeat Horizontal', 'cjpopups'),
			'repeat-y' => _x('Repeat Vertical', 'cjpopups'),
			'no-repeat' => _x('Do not repeat', 'cjpopups'),
		);
		$opts = null;
		foreach ($bg_repeat_array as $okey => $ovalue) {
			if(isset($opt_value['bg_repeat']) && $opt_value['bg_repeat'] == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<label>'.__('Background Repeat', 'cjpopups').'<br>';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'[bg_repeat]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '</label>';
		$display[] = '</div>';


		// Background size
		$display[] = '<div class="alignleft padding-5-top padding-5-bottom">';
		$bg_size_array = array(
			'inherit' => _x('Inherit', 'cjpopups'),
			'cover' => _x('Cover', 'cjpopups'),
			'contain' => _x('Contain', 'cjpopups'),
		);
		$opts = null;
		foreach ($bg_size_array as $okey => $ovalue) {
			if(isset($opt_value['bg_size']) && $opt_value['bg_size'] == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<label>'.__('Background Size', 'cjpopups').'<br>';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'[bg_size]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '</label>';
		$display[] = '</div>';

		$display[] = '<br class="clear" />';




		// Background attachment
		$display[] = '<div class="alignleft padding-5-top padding-5-bottom">';
		$bg_attachment_array = array(
			'inherit' => _x('Inherit', 'cjpopups'),
			'fixed' => _x('Fixed', 'cjpopups'),
			'scroll' => _x('Scroll', 'cjpopups'),
		);
		$opts = null;
		foreach ($bg_attachment_array as $okey => $ovalue) {
			if(isset($opt_value['bg_attachment']) && $opt_value['bg_attachment'] == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.__($ovalue, 'cjpopups').'</option>';
			}
		}
		$display[] = '<label>'.__('Background Attachment', 'cjpopups').'<br>';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'[bg_attachment]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '</label>';
		$display[] = '</div>';


		// Background position
		$display[] = '<div class="alignleft padding-5-top padding-5-bottom">';
		$bg_position_array = array(
			'inherit' => _x('Inherit', 'cjpopups'),
			'top left' => _x('top left', 'cjpopups'),
			'top center' => _x('top center', 'cjpopups'),
			'top right' => _x('top right', 'cjpopups'),
			'bottom left' => _x('bottom left', 'cjpopups'),
			'bottom center' => _x('bottom center', 'cjpopups'),
			'bottom right' => _x('bottom right', 'cjpopups'),
			'center left' => _x('center left', 'cjpopups'),
			'center center' => _x('center center', 'cjpopups'),
			'center right' => _x('center right', 'cjpopups'),
		);
		$opts = null;
		foreach ($bg_position_array as $okey => $ovalue) {
			if(isset($opt_value['bg_position']) && $opt_value['bg_position'] == $okey){
				$opts[] = '<option selected value="'.$okey.'">'.ucwords($ovalue).'</option>';
			}else{
				$opts[] = '<option value="'.$okey.'">'.ucwords($ovalue).'</option>';
			}
		}
		$display[] = '<label>'.__('Background Position', 'cjpopups').'<br>';
		$display[] = '<select '.$params.' class="chzn-select-no-results" name="'.$option['id'].'[bg_position]" id="'.sanitize_title( $option['id'] ).'">';
		$display[] = implode('', $opts);
		$display[] = '</select>';
		$display[] = '</label>';
		$display[] = '</div>';

		$display[] = '</div><!-- /clearfix -->';


		$display[] = '<div class="cj-info">'.__($option['info'], 'cjpopups').'</div>';

		$display[] = '</td>';
		$display[] = '</tr>';
	}


	if($option['type'] == 'submit'){
		$display[] = '<tr class="cj-submit"><td class="cj-label">&nbsp;</td>';
		$display[] = '<td class="cj-panel">';
		$display[] = '<input '.$params.' type="hidden" name="form_message" value="'.$option['default'].'" />';
		$display[] = '<input '.$params.' type="submit" id="'.$form_submit.'" name="'.$form_submit.'" value="'.__($option['label'], 'cjpopups').'" class="button-primary" />'.$option['suffix'];
		$display[] = '</td>';
		$display[] = '</tr>';
	}

	if($option['type'] == 'submit-full'){
		$display[] = '<tr class="cj-submit">';
		$display[] = '<td colspan="2" class="cj-panel">';
		$display[] = '<input '.$params.' type="hidden" name="form_message" value="'.$option['default'].'" />';
		$display[] = '<input '.$params.' type="submit" id="'.$form_submit.'" name="'.$form_submit.'" value="'.__($option['label'], 'cjpopups').'" class="button-primary" />'.$option['suffix'];
		$display[] = '</td>';
		$display[] = '</tr>';
	}

}


$display[] = '</table>';


echo '<form action="'.admin_url('admin.php?page='.cjpopups_item_info('page_slug').'&callback='.@$_GET['callback'].'').'" method="post" enctype="multipart/form-data">';
echo implode('', $display);
echo '</form>';