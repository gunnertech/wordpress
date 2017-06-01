<?php
// Fill Address
add_action( 'cmb_render_background_properties', 'cjpopups_cmb_render_background_properties', 10, 5 );
function cjpopups_cmb_render_background_properties( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($_GET['post'], $field_args['id'], true);

    $saved_value = wp_parse_args( $saved_value, array(
        'background-repeat' => '',
        'background-size' => '',
        'background-position' => '',
        'background-attachment' => '',
    ) );

    echo '<div id="'.$field_args['id'].'_background_properties" style="width:97%;">';

    echo '<div class="one_fourth">';
    echo '<label for="'.$field_args['id'].'_background_repeat" style="margin-bottom:5px; display:inline-block;">'.__('Backgound Repeat', 'cjpopups').'</label><br>';
    echo '<select id="'.$field_args['id'].'_background_repeat" name="'.$field_args['id'].'[background-repeat]" style="width:100%;">';
    foreach (cjpopups_background_repeat() as $key => $value) {
        if($key == $saved_value['background-repeat']){
            echo '<option selected value="'.$key.'">'.$value.'</option>';
        }else{
            echo '<option value="'.$key.'">'.$value.'</option>';
        }
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="one_fourth">';
    echo '<label for="'.$field_args['id'].'_background_size" style="margin-bottom:5px; display:inline-block;">'.__('Backgound Size', 'cjpopups').'</label><br>';
    echo '<select id="'.$field_args['id'].'_background_size" name="'.$field_args['id'].'[background-size]" style="width:100%;">';
    foreach (cjpopups_background_size() as $key => $value) {
        if($key == $saved_value['background-size']){
            echo '<option selected value="'.$key.'">'.$value.'</option>';
        }else{
            echo '<option value="'.$key.'">'.$value.'</option>';
        }
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="one_fourth">';
    echo '<label for="'.$field_args['id'].'_background_position" style="margin-bottom:5px; display:inline-block;">'.__('Backgound Position', 'cjpopups').'</label><br>';
    echo '<select id="'.$field_args['id'].'_background_position" name="'.$field_args['id'].'[background-position]" style="width:100%;">';
    foreach (cjpopups_background_position() as $key => $value) {
        if($key == $saved_value['background-position']){
            echo '<option selected value="'.$key.'">'.$value.'</option>';
        }else{
            echo '<option value="'.$key.'">'.$value.'</option>';
        }
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="one_fourth last">';
    echo '<label for="'.$field_args['id'].'_background_attachment" style="margin-bottom:5px; display:inline-block;">'.__('Backgound Attachment', 'cjpopups').'</label><br>';
    echo '<select id="'.$field_args['id'].'_background_attachment" name="'.$field_args['id'].'[background-attachment]" style="width:100%;">';
    foreach (cjpopups_background_attachment() as $key => $value) {
        if($key == $saved_value['background-attachment']){
            echo '<option selected value="'.$key.'">'.$value.'</option>';
        }else{
            echo '<option value="'.$key.'">'.$value.'</option>';
        }
    }
    echo '</select>';
    echo '</div>';

    echo '<div>';
    echo $field_type_object->_desc( true );

}