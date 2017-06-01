<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
$display[] = '';
if(isset($options) && is_array($options)){

	foreach ($options as $key => $option):
	if(isset($option) && is_array($option) && isset($option['type'])):

		$params = '';
		if(isset($option['params']) && is_array($option['params'])){
			foreach ($option['params'] as $key => $value) {
				$params .= ' '.$key.'="'.$value.'" ';
			}
		}
		$field_required = '';
		if(isset($option['required']) && $option['required'] == true){
			$field_required = ' required ';
		}
		$container_class = (isset($option['class'])) ? $option['class'] : '';

		$field_readonly = '';
		if(isset($option['readonly']) && $option['readonly'] == true){
			$field_readonly = ' readonly ';
		}

		if($option['type'] == 'heading'){
			$display[] = '<div class="control-group '.$option['id'].'">';
			$display[] = '<h2 id="'.$option['id'].'" class="cjpopups-heading" '.$params.'>'.$option['default'].'</h2>';
			$display[] = '</div>';
		}

		if($option['type'] == 'paragraph'){
			$display[] = '<div class="control-group '.$option['id'].'">';
			$display[] = '<p id="'.$option['id'].'" class="cjpopups-paragraph" '.$params.'>'.$option['default'].'</p>';
			$display[] = '</div>';
		}

		if($option['type'] == 'custom_html'){
			$display[] = '<div class="control-group '.$option['id'].'">';
			$display[] = '<div class="cjpopups-custom-html" '.$params.'>'.$option['default'].'</div>';
			$display[] = '</div>';
		}

		if($option['type'] == 'html_tag'){
			$display[] = $option['default'];
		}

		if($option['type'] == 'sub-heading'){
			$display[] = '<div class="control-group '.$option['id'].'">';
			$display[] = '<h2 id="'.$option['id'].'" class="sub-heading" '.$params.'>'.$option['default'].'</h2>';
			$display[] = '</div>';
		}

		if($option['type'] == 'info'){
			$display[] = '<div class="control-group '.$option['id'].'">';
			$display[] = '<p id="'.$option['id'].'" class="info" '.$params.'>'.$option['default'].'</p>';
			$display[] = '</div>';
		}

		if($option['type'] == 'hidden'){
			$display[] = '<input type="hidden" name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>';
		}


		if($option['type'] == 'text'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' textbox">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="text" name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'date'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' cjpopups-date">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="text" name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'email'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' email">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="email" name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'text-readonly'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' textbox">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="text" readonly name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'upload'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' file">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="file" name="'.$option['id'].'" id="'.$option['id'].'" value="" '.$params.$field_required.$field_readonly.'>';
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = $option['suffix'];
			$display[] = '</div>';
		}

		if($option['type'] == 'file'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' file">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="file" name="'.$option['id'].'" id="'.$option['id'].'" value="" '.$params.$field_required.$field_readonly.'>';
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = $option['suffix'];
			$display[] = '</div>';
		}

		if($option['type'] == 'uploads'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' files">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = '<input type="file" name="'.$option['id'].'[]" id="'.$option['id'].'" value="" '.$params.$field_required.$field_readonly.' multiple>';
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = $option['suffix'];
			$display[] = '</div>';
		}

		if($option['type'] == 'files'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' files">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = '<input type="file" name="'.$option['id'].'[]" id="'.$option['id'].'" value="" '.$params.$field_required.$field_readonly.' multiple>';
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = $option['suffix'];
			$display[] = '</div>';
		}

		if($option['type'] == 'password'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  password">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<input type="password" name="'.$option['id'].'" id="'.$option['id'].'" value="'.$option['default'].'" '.$params.$field_required.$field_readonly.'>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}


		if($option['type'] == 'textarea'){
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  textarea">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'].'<textarea name="'.$option['id'].'" id="'.$option['id'].'" rows="5" cols="40" '.$params.$field_required.$field_readonly.'>'.$option['default'].'</textarea>'.$option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
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
			wp_editor($option['default'], $editor_id, $editor_settings);
			$editor_panel = ob_get_clean();

			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  wysiwyg">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = '<div class="cj-panel-editor">'.$editor_panel.'</div>';
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'dropdown' || $option['type'] == 'select'){
			$opts = '';
			if(is_array($option['options'])){
				foreach ($option['options'] as $key => $opt) {
					if($option['default'] == strip_tags($key)){
						$opts .= '<option selected value="'.strip_tags($key).'">'.$opt.'</option>';
					}else{
						$opts .= '<option value="'.strip_tags($key).'">'.$opt.'</option>';
					}
				}
			}
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  select">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'];
			$display[] = '<select name="'.$option['id'].'" id="'.$option['id'].'" '.$params.$field_required.$field_readonly.'>';
			$display[] = $opts;
			$display[] = '</select>';
			$display[] = $option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'multidropdown' || $option['type'] == 'multiselect'){
			$opts = '';
			if(is_array($option['options'])){
				foreach ($option['options'] as $key => $opt) {
					$format_option_defaults = (is_serialized($option['default'])) ? unserialize($option['default']) : $option['default'];
					if(@in_array(strip_tags($key), $format_option_defaults )){
						$opts .= '<option selected value="'.strip_tags($key).'">'.$opt.'</option>';
					}else{
						$opts .= '<option value="'.strip_tags($key).'">'.$opt.'</option>';
					}
				}
			}
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  multiselect">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $option['prefix'];
			$display[] = '<select multiple name="'.$option['id'].'[]" id="'.$option['id'].'" '.$params.$field_required.$field_readonly.'>';
			$display[] = $opts;
			$display[] = '</select>';
			$display[] = $option['suffix'];
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'radio' || $option['type'] == 'radio-inline'){
			$opts = '';
			$inline = ($option['type'] == 'radio-inline') ? 'inline' : '';
			if(is_array($option['options'])){
				foreach ($option['options'] as $key => $opt) {
					if($option['default'] == strip_tags($key)){
						$opts .= '<label class="radio-label '.$inline.'"> <input checked type="radio" id="'.$option['id'].'" name="'.$option['id'].'" value="'.strip_tags($key).'"> '.stripcslashes($opt).'</label>';
					}else{
						$opts .= '<label class="radio-label '.$inline.'"> <input type="radio" id="'.$option['id'].'" name="'.$option['id'].'" value="'.strip_tags($key).'"> '.stripcslashes($opt).'</label>';
					}
				}
			}
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.' radio radio-buttons">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $opts;
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'checkbox' || $option['type'] == 'checkbox-inline'){
			$opts = '';
			$inline = ($option['type'] == 'checkbox-inline') ? 'inline' : '';
			if(is_array($option['options'])){
				foreach ($option['options'] as $key => $opt) {
					$format_option_defaults = (is_serialized($option['default'])) ? unserialize($option['default']) : $option['default'];
					if(@in_array(strip_tags($key), $format_option_defaults )){
						$opts .= '<label class="checkbox-label '.$inline.'"> <input type="checkbox" id="'.$option['id'].'" name="'.$option['id'].'[]" value="'.strip_tags($key).'" checked>'.stripcslashes($opt).'</label>';
					}else{
						$opts .= '<label class="checkbox-label '.$inline.'"> <input type="checkbox" id="'.$option['id'].'" name="'.$option['id'].'[]" value="'.strip_tags($key).'">'.stripcslashes($opt).'</label>';
					}
				}
			}
			$display[] = '<div id="container-'.$option['id'].'" class="control-group '.$container_class.'  checkbox">';
			$display[] = (!is_null($option['label'])) ? '<label class="control-label" for="'.$option['id'].'"><span class="label-'.$option['id'].'">'.$option['label'].'</span></label>' : '';
			$display[] = '<span class="cjpopups-relative">';
			$display[] = $opts;
			$display[] = '</span>';
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

		if($option['type'] == 'submit'){
			$display[] = '<div class="control-group submit-button">';
			$display[] = $option['prefix'].'<button type="submit" name="'.$option['id'].'" id="'.$option['id'].'" class="submit '.$container_class.'" '.$params.$field_required.$field_readonly.'>'.$option['label'].'</button>'.$option['suffix'];
			$display[] = $option['info'];
			$display[] = ($option['info'] != '') ? '<div class="'.$option['id'].'-info info help-block">'.$option['info'].'</div>' : '';
			$display[] = '</div>';
		}

	endif;
	endforeach;
}