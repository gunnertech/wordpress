<?php
// Multiselect
add_action( 'cmb_render_multiselect', 'cjpopups_cmb_render_multiselect', 10, 5 );
function cjpopups_cmb_render_multiselect( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$saved_value = get_post_meta($_GET['post'], $field_args['id'], true);
	echo '<p>';
    echo '<select id="'.$field_args['id'].'" multiple name="'.$field_args['id'].'[]" class="cmb_select chzn-select-no-results">';
    foreach ($field_args['options'] as $key => $opt) {
    	if(in_array($opt['value'], $saved_value)){
    		echo '<option selected value="'.$opt['value'].'">'.$opt['name'].'</option>';
    	}else{
    		echo '<option value="'.$opt['value'].'">'.$opt['name'].'</option>';
    	}
    }
    echo '</select>';
    echo '</p>';
    echo $field_type_object->_desc( true );
}