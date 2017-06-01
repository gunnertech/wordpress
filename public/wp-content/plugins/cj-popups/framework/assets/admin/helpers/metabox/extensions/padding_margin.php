<?php
// Padding & Margin
add_action( 'cmb_render_padding', 'cjpopups_cmb_render_padding', 10, 5 );
function cjpopups_cmb_render_padding( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($_GET['post'], $field_args['id'], true);

    $saved_value = wp_parse_args( $saved_value, array(
        'padding-top' => (isset($defaults['padding-top'])) ? $defaults['padding-top'] : '',
        'padding-right' => (isset($defaults['padding-right'])) ? $defaults['padding-right'] : '',
        'padding-bottom' => (isset($defaults['padding-bottom'])) ? $defaults['padding-bottom'] : '',
        'padding-left' => (isset($defaults['padding-left'])) ? $defaults['padding-left'] : '',
    ) );

    echo '<div id="'.$field_args['id'].'_padding" style="width:97%;">';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_padding_top" style="margin-bottom:5px; display:inline-block;">'.__('Padding Top (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'padding_top" name="'.$field_args['id'].'[padding-top]" style="width:100%;" value="'.$saved_value['padding-top'].'" />';
    echo '</div>';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_padding_right" style="margin-bottom:5px; display:inline-block;">'.__('Padding Right (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'padding_right" name="'.$field_args['id'].'[padding-right]" style="width:100%;" value="'.$saved_value['padding-right'].'" />';
    echo '</div>';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_padding_bottom" style="margin-bottom:5px; display:inline-block;">'.__('Padding Bottom (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'padding_bottom" name="'.$field_args['id'].'[padding-bottom]" style="width:100%;" value="'.$saved_value['padding-bottom'].'" />';
    echo '</div>';

    echo '<div class="one_sixth last">';
    echo '<label for="'.$field_args['id'].'_padding_left" style="margin-bottom:5px; display:inline-block;">'.__('Padding Left (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'padding_left" name="'.$field_args['id'].'[padding-left]" style="width:100%;" value="'.$saved_value['padding-left'].'" />';
    echo '</div>';

    echo '<div>';
    echo $field_type_object->_desc( true );

}


add_action( 'cmb_render_margin', 'cjpopups_cmb_render_margin', 10, 5 );
function cjpopups_cmb_render_margin( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($_GET['post'], $field_args['id'], true);
    $defaults = $field_args['std'];

    $saved_value = wp_parse_args( $saved_value, array(
        'margin-top' => (isset($defaults['margin-top'])) ? $defaults['margin-top'] : '',
        'margin-right' => (isset($defaults['margin-right'])) ? $defaults['margin-right'] : '',
        'margin-bottom' => (isset($defaults['margin-bottom'])) ? $defaults['margin-bottom'] : '',
        'margin-left' => (isset($defaults['margin-left'])) ? $defaults['margin-left'] : '',
    ) );

    echo '<div id="'.$field_args['id'].'_margin" style="width:97%;">';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_margin_top" style="margin-bottom:5px; display:inline-block;">'.__('Margin Top (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'margin_top" name="'.$field_args['id'].'[margin-top]" style="width:100%;" value="'.$saved_value['margin-top'].'" />';
    echo '</div>';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_margin_right" style="margin-bottom:5px; display:inline-block;">'.__('Margin Right (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'margin_right" name="'.$field_args['id'].'[margin-right]" style="width:100%;" value="'.$saved_value['margin-right'].'" />';
    echo '</div>';

    echo '<div class="one_sixth">';
    echo '<label for="'.$field_args['id'].'_margin_bottom" style="margin-bottom:5px; display:inline-block;">'.__('Margin Bottom (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'margin_bottom" name="'.$field_args['id'].'[margin-bottom]" style="width:100%;" value="'.$saved_value['margin-bottom'].'" />';
    echo '</div>';

    echo '<div class="one_sixth last">';
    echo '<label for="'.$field_args['id'].'_margin_left" style="margin-bottom:5px; display:inline-block;">'.__('Margin Left (px)', 'cjpopups').'</label><br>';
    echo '<input type="text" id="'.$field_args['id'].'margin_left" name="'.$field_args['id'].'[margin-left]" style="width:100%;" value="'.$saved_value['margin-left'].'" />';
    echo '</div>';

    echo '<div>';
    echo $field_type_object->_desc( true );

}