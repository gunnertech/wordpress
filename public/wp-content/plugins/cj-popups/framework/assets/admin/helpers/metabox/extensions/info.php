<?php
// Border
add_action( 'cmb_render_info', 'cjpopups_cmb_render_info', 10, 5 );
function cjpopups_cmb_render_info( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    echo '<div id="'.$field_args['id'].'_info" style="width:97%;">';
    echo $field_args['desc'];
    echo '<div>';


}