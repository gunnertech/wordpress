<?php
// Longitude Latitude
add_action( 'cmb_render_latlng', 'cjpopups_cmb_render_latlng', 10, 5 );
function cjpopups_cmb_render_latlng( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$saved_value = get_post_meta($_GET['post'], $field_args['id'], true);

    if($saved_value == ''){
        $saved_value['lng'] = '';
        $saved_value['lat'] = '';
    }

    echo '<div class="clearfix">';
    echo '<div class="one_third">';
    echo '<p><label for="'.$field_args['id'].'_lng">'.__('Latitude', 'cjpopups').'</label><input name="'.$field_args['id'].'[lat]" id="'.$field_args['id'].'_lng" type="text" value="'.$saved_value['lat'].'"></p>';
    echo '<p><label for="'.$field_args['id'].'_lat">'.__('Longitude', 'cjpopups').'</label><input name="'.$field_args['id'].'[lng]" id="'.$field_args['id'].'_lat" type="text" value="'.$saved_value['lng'].'"></p>';
    echo '</select>';
    echo '</div>';
    echo '</div>';
    echo $field_type_object->_desc( true );
}